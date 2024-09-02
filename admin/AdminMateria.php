<?php

class AdminMateria {

    private $dao;

    public function __construct() {
        $this->dao = new MateriaDAO();
    }

    public function recuperar_info_materia($id) {
        return $this->construir_materia($this->dao->recuperar_info_materia($id))->to_array();
    }

    private function construir_materia($tupla): Materia {
        // Inicializar las variables
        $id_materia = $tupla[0]['id_materia'] ?? "";
        $nombre = $tupla[0]['nombre_materia'] ?? "";
        $carrera = $tupla[0]['perfil_profesional'] ?? "";
        $horarios = [];

        // Recolectar los horarios de la tupla
        foreach ($tupla as $fila) {
            $horarios[] = [
                'dia_semana' => $fila['dia_semana'],
                'hora_inicio' => $fila['hora_inicio'],
                'hora_fin' => $fila['hora_fin']
            ];
        }

        // Crear y retornar el objeto Materia
        return new Materia($nombre, $carrera, $horarios, $id_materia);
    }

    function actualizar_nombre_materia($id, $nombre) {
        return $this->dao->actualizar_nombre_materia($id, $nombre);
    }

    function actualizar_grupo_materia($id, $grupo) {
        return $this->dao->actualizar_grupo_materia($id, $grupo);
    }

    function actualizar_horario($form) {
        return $this->dao->actualizar_horario($form);
    }

    function agregar_horario($form) {
        return $this->dao->agregar_horario($form);
    }

    function eliminar_horario($id) {
        return $this->dao->eliminar_horario($id);
    }

    function listar_grupos($carrera, $plantel) {
        return $this->dao->listar_grupos($carrera, $plantel);
    }

}
