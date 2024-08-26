<?php

class MateriaDAO extends DAO {

    private const TABLA = "materia";
    private const ACTUALIZAR_HORARIO = "UPDATE `materia_horarios` SET `dia_semana` = ?, `hora_inicio` = ?, `hora_fin` = ? WHERE id_horario = ?";
    private const AGREGAR_HORARIO = "INSERT INTO `materia_horarios` (`id_materia`, `dia_semana`, `hora_inicio`, `hora_fin`) VALUES (?, ?, ?, ?)";

    private function actualizar_campo($campo, $id, $val) {
        $args = new PreparedStatmentArgs();
        $args->add("s", $val);
        $args->add("i", $id);
        return $this->actualizar_por_id(self::TABLA, $campo, "id_materia", $args);
    }

    public function actualizar_nombre_materia($id, $nombre) {
        return $this->actualizar_campo("nombre", $id, $nombre);
    }

    public function actualizar_grupo_materia($id, $grupo) {
        return $this->actualizar_campo("grupo", $id, $grupo);
    }

    public function eliminar_horario($id) {
        return $this->eliminar_por_id("materia_horarios", "id_horario", $id);
    }

    public function actualizar_horario($form) {
        $args = new PreparedStatmentArgs();
        $args->add("s", $form["dia_semana"]);
        $args->add("s", $form["hora_inicio"]);
        $args->add("s", $form["hora_fin"]);
        $args->add("i", $form["id_horario"]);
        return $this->ejecutar_instruccion_preparada(self::ACTUALIZAR_HORARIO, $args);
    }
    
    public function agregar_horario($form) {
        $args = new PreparedStatmentArgs();
        $args->add("i", $form["id_materia"]);
        $args->add("s", $form["dia_semana"]);
        $args->add("s", $form["hora_inicio"]);
        $args->add("s", $form["hora_fin"]);
        return $this->ejecutar_instruccion_preparada(self::AGREGAR_HORARIO, $args);
    }
}
