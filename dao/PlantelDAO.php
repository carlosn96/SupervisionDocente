<?php

class PlantelDAO extends DAO {

    private const NOMBRE_TABLA = "plantel";
    private const LISTAR = "SELECT * FROM " . self::NOMBRE_TABLA;
    private const INSERTAR = "INSERT INTO " . self::NOMBRE_TABLA . " (nombre) VALUES (?)";
    private const INSERTAR_CARRERA = "INSERT INTO " . "carrera_" . self::NOMBRE_TABLA . " VALUES (?, ?)";
    private const EDITAR_PLANTEL = "UPDATE " . self::NOMBRE_TABLA . " SET `nombre` = ?,  `director` = ? WHERE (`id_plantel` = ?)";
    private const CONSULTAR_HORARIO_TURNO = "SELECT hora_entrada inicio, hora_salida fin, duracion_bloque_minutos duracion_bloque, hora_descanso descanso_inicio, duracion_descanso_minutos descanso_duracion FROM plantel_turno turno WHERE id_plantel = ? AND turno = ?";

    public function recuperar_listado($where) {
        return $this->ejecutar_instruccion(self::LISTAR . "  " . $where)->fetch_all(MYSQLI_ASSOC);
    }

    public function recuperar_listado_por_carrera($id_carrera) {
        return $this->ejecutar_instruccion("SELECT plantel.director, plantel.id_plantel, plantel.nombre
FROM plantel
JOIN carrera_plantel ON plantel.id_plantel = carrera_plantel.id_plantel
WHERE carrera_plantel.id_carrera = $id_carrera")->fetch_all(MYSQLI_ASSOC);
    }

    public function agregar(Plantel $plantel) {
        $pre = new PreparedStatmentArgs();
        $pre->add("s", $plantel->getNombre());
        return $this->ejecutar_instruccion_preparada(self::INSERTAR, $pre);
    }

    public function agregar_carrera_en_plantel(Carrera $carrera) {
        $prep = new PreparedStatmentArgs();
        $prep->add("i", $carrera->get_id_carrera());
        $prep->add("i", 0);
        foreach ($carrera->get_planteles() as $id_plantel) {
            $prep->update(1, $id_plantel);
            $this->ejecutar_instruccion_preparada(self::INSERTAR_CARRERA, $prep);
        }
        return true;
    }

    public function eliminar_carrera_de_plantel(Carrera $carrera) {
        return $this->eliminar_por_id("carrera_" . self::NOMBRE_TABLA, "id_carrera", $carrera->get_id_carrera());
    }

    public function guardar_configuracion_carrera_plantel($id_coord, $id_carrera, $id_plantel, $ciclo) {
        return (new ConfigUsuarioDAO)->guardar_configuracion_carrera_plantel($id_coord, $id_carrera, $id_plantel, $ciclo);
    }

    public function recuperar_configuracion_carrera_plantel($id_coord) {
        return (new ConfigUsuarioDAO)->recuperar_configuracion_carrera_plantel($id_coord);
    }

    public function editar(Plantel $plantel) {
        $prep = new PreparedStatmentArgs();
        $prep->add("s", $plantel->getNombre());
        $prep->add("s", $plantel->getDirector());
        $prep->add("i", $plantel->getIdPlantel());
        return $this->ejecutar_instruccion_preparada(self::EDITAR_PLANTEL, $prep);
    }

    public function eliminar($id) {
        return $this->eliminar_por_id(self::NOMBRE_TABLA, "id_plantel", $id);
    }

    public function consultar_detalle_turno_plantel($id_plantel, $turno) {
        $args = new PreparedStatmentArgs;
        $args->add("i", $id_plantel);
        $args->add("s", $turno);
        return ($rs = $this->ejecutar_instruccion_prep_result(self::CONSULTAR_HORARIO_TURNO, $args)) ? $rs[0] : [];
    }
}
