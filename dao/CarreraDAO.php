<?php

class CarreraDAO extends DAO {

    private const NOMBRE_TABLA = "carrera";
    private const LISTAR = "SELECT id_carrera, CONCAT(tipo, ' ', nombre) nombre,  tipo FROM " . self::NOMBRE_TABLA;
    private const BUSCAR_NOMBRE = "SELECT * FROM " . self::NOMBRE_TABLA . " WHERE nombre = ?";
    private const LISTAR_DETALLES = "SELECT * FROM listar_carrera_detalles";
    private const LISTAR_SIN_COORDINADOR = "SELECT * FROM lista_carreras_sin_coordinador";
    private const INSERTAR = "INSERT INTO " . self::NOMBRE_TABLA . " (nombre, tipo) VALUES(?, ?)";
    private const OBTENER_TIPOS_CARRERA = "SHOW COLUMNS FROM " . self::NOMBRE_TABLA . " LIKE 'tipo'";
    private const ELIMINAR_POR_ID = "DELETE FROM " . self::NOMBRE_TABLA . " WHERE id_carrera = ?";

    public function recuperar_listado($where = "") {
        return $this->ejecutar_instruccion(self::LISTAR . $where)->fetch_all(MYSQLI_ASSOC);
    }

    public function recuperar_listado_detallado($where = "") {
        return $this->ejecutar_instruccion(self::LISTAR_DETALLES . " " . $where)->fetch_all(MYSQLI_ASSOC);
    }

    public function recuperar_listado_sin_coordinador() {
        return $this->ejecutar_instruccion(self::LISTAR_SIN_COORDINADOR)->fetch_all(MYSQLI_ASSOC);
    }

    public function recuperar_tipos_carrera() {
        return $this->ejecutar_instruccion(self::OBTENER_TIPOS_CARRERA)->fetch_assoc();
    }

    public function guardar(Carrera &$carrera) {
        $prep = new PreparedStatmentArgs();
        $prep->add("s", $carrera->get_nombre());
        $prep->add("s", $carrera->get_tipo());
        if ($this->ejecutar_instruccion_preparada(self::INSERTAR, $prep)) {
            $carrera->set_id_carrera($this->obtener_id_autogenerado());
            return (new AdminPlantel())->agregar_carrera_en_plantel($carrera);
        }
        return false;
    }

    public function eliminar($id) {
        $prep = new PreparedStatmentArgs();
        $prep->add("i", $id);
        return $this->ejecutar_instruccion_preparada(self::ELIMINAR_POR_ID, $prep);
    }

    public function actualizar(Carrera $carrera) {
        $args = new PreparedStatmentArgs();
        $args->add("s", $carrera->get_nombre());
        $args->add("s", $carrera->get_tipo());
        $args->add("i", $carrera->get_id_carrera());
        return $this->actualizar_multiple(self::NOMBRE_TABLA, "nombre=?, tipo=?", "id_carrera", $args) &&
                $this->actualizar_coordinador_carrera($carrera) && $this->actualizar_planteles_carrera($carrera);
    }

    private function actualizar_coordinador_carrera(Carrera $carrera) {
        $prep = new PreparedStatmentArgs();
        $prep->add("i", $carrera->get_coordinador());
        $prep->add("i", $carrera->get_id_carrera());
        return $this->ejecutar_instruccion_preparada("CALL actualizar_coordinador_carrera(?, ?)", $prep); 
    }

    private function actualizar_planteles_carrera(Carrera $carrera) {
        $admin_plantel = new AdminPlantel;
        return $admin_plantel->eliminar_carrera_de_plantel($carrera) && $admin_plantel->agregar_carrera_en_plantel($carrera);
    }

    public function recuperar_nombre($nombre) {
        $arg = new PreparedStatmentArgs();
        $arg->add("s", $nombre);
        return $this->ejecutar_instruccion_prep_result(self::BUSCAR_NOMBRE, $arg);
    }

    public function recuperar_id($id) {
        return $this->recuperar_listado_detallado(" WHERE id_carrera = $id");
    }
}
