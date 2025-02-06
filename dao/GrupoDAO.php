<?php

class GrupoDAO extends DAO {

    private const NOMBRE_TABLA = "grupo";
    private const INSERTAR = "INSERT INTO " . self::NOMBRE_TABLA . " (clave, id_carrera, id_plantel, grado_actual) VALUES (?, ?, ?, ?)";
    private const LISTAR = "SELECT * FROM " . self::NOMBRE_TABLA;
    private const ACTUALIZAR = "CALL actualizar_grupo(?) ";
    private const RECUPERAR_GRADOS = "SELECT * FROM grupo_grado";

    public function agregar(Grupo $grupo) {
        $pre = new PreparedStatmentArgs();
        $pre->add("s", $grupo->getClave());
        $pre->add("s", $grupo->getGrado());
        $pre->add("i", $grupo->getCarrera());
        $pre->add("i", $grupo->getPlantel());
        return $this->ejecutar_instruccion_preparada(self::INSERTAR, $pre);
    }

    public function actualizar(Grupo $grupo) {
        $pre = new PreparedStatmentArgs();
        $pre->add("s", json_encode($grupo->to_array()));
        return $this->ejecutar_instruccion_preparada(self::ACTUALIZAR, $pre);
    }

    private function buscar_grupo($where) {
        return $this->ejecutar_instruccion(self::LISTAR . "  " . $where)->fetch_all(MYSQLI_ASSOC);
    }

    public function listar_grupos($carrera, $plantel) {
        $where = " WHERE id_carrera = $carrera AND id_plantel = $plantel";
        return $this->buscar_grupo($where);
    }

    public function recuperar_grupo($id) {
        return $this->buscar_grupo(" WHERE id_grupo = $id")[0];
    }

    public function eliminar($id) {
        return $this->eliminar_por_id(self::NOMBRE_TABLA, "id_grupo", $id);
    }

    public function recuperar_turnos() {
        return $this->get_anum_values(self::NOMBRE_TABLA, "turno");
    }

    public function recuperar_grados() {
        $grados = $this->ejecutar_instruccion(self::RECUPERAR_GRADOS);
        return $grados ? $grados->fetch_all(MYSQLI_ASSOC) : [];
    }
}
