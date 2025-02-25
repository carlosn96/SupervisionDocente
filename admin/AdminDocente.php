<?php

class AdminDocente {

    private $dao;

    public function __construct() {
        $this->dao = new DocenteDAO;
    }

    public function existe_docente($nombre, $apellidos, $correo): bool {
        return $this->dao->existe_docente($nombre, $apellidos, $correo);
    }

    public function listar_todos_docentes() {
        return $this->dao->listar_todos_docentes();
    }

    public function guardar_docente($form) {
        $nombre = $form["nombre"];
        $apellidos = $form["apellidos"];
        $correo_electronico = $form["correo_electronico"];
        $perfil_profesional = $form["perfil_profesional"];
        return $this->dao->guardar_docente(new Docente($nombre, $apellidos, $correo_electronico, $perfil_profesional));
    }

    public function guardar_docente_materias($formulario) {
        return $this->dao->guardar_docente_materias($this->construir_docente($formulario),
                        $formulario["carrera"],
                        $formulario["plantel"]);
    }

    public function obtener_agenda_general($id_coordinador) {
        return $this->dao->obtener_agenda_general($id_coordinador);
    }

    public function obtener_docentes_materias($id_carrera, $id_plantel, $ciclo_escolar) {
        return $this->dao->obtener_docentes_materias($id_carrera, $id_plantel, $ciclo_escolar);
    }

    private function construir_docente($formulario): Docente {
        $nombre = $formulario["nombre"];
        $apellidos = $formulario["apellidos"];
        $correo_electronico = $formulario["correo_electronico"];
        $perfil_profesional = $formulario["perfil_profesional"];
        //$id_coordinador = $formulario["id_coordinador"] ?? "";
        $materias = $this->construir_materias($formulario["materias"] ?? array(), $formulario["id_carrera"] ?? "");
        $id_docente = $formulario["id_docente"] ?? "";
        return new Docente($nombre, $apellidos, $correo_electronico,
                $perfil_profesional, $materias, $id_docente);
    }

    public function recuperar_docente($id) {
        return $this->dao->recuperar_docente($id);
    }

    private function construir_materias($materias, $carrera) {
        $lista = [];
        if (isset($materias)) {
            foreach ($materias as $m) {
                $materia = new Materia($m["nombre"], $carrera, $m["grupo"]);
                $this->parse_horario($materia, $m["horario"]);
                array_push($lista, $materia);
            }
        }
        return $lista;
    }

    private function parse_horario(Materia &$materia, $horariostr) {
        $horariosArr = explode(", ", $horariostr);
        foreach ($horariosArr as $horario) {
            list($dia, $horas) = explode(": ", $horario);
            list($hora_inicio, $hora_fin) = explode(" - ", $horas);
            $materia->agregar_dia_horas($dia, $hora_inicio, $hora_fin);
        }
    }

    public function obtener_info_agenda($id_agenda) {
        return $this->dao->obtener_info_agenda($id_agenda);
    }

    public function eliminar($id_docente) {
        return $this->dao->eliminar($id_docente);
    }

    public function actualizar($formulario) {
        return $this->dao->actualizar($this->construir_docente($formulario));
    }

    public function agregar_materia($docente, $materia) {
        return $this->dao->agregar_materia($docente, $materia);
    }

    public function recuperar_materias($docente, $carrera, $plantel, $ciclo) {
        return $this->dao->obtener_materias($docente, $carrera, $plantel, $ciclo);
    }

    public function obtener_horario($tipo, $id, $carrera, $plantel, $ciclo) {
        return $this->dao->obtener_horario($tipo, $id, $carrera, $plantel, $ciclo);
    }

    public function consultar_disponibilidad($dia, $hora, $carrera, $plantel, $ciclo) {
        return $this->dao->consultar_disponibilidad($dia, $hora, $carrera, $plantel, $ciclo);
    }

    public function buscar_horario_agendado($docente, $carrera, $plantel, $ciclo) {
        $materiasDocente = $this->recuperar_materias($docente, $carrera, $plantel, $ciclo);
        foreach ($materiasDocente as $detalles) {
            foreach ($detalles['materias'] as $materiaDetalles) {
                $horarioAgendado = array_filter($materiaDetalles['horarios'], function ($horario) {
                    return $horario['es_horario_agendado'] == 1;
                });
                if (!empty($horarioAgendado)) {
                    return reset($horarioAgendado)['id_horario'];
                }
            }
        }
        return -1;
    }
    
    public function get_turno_docente($plantel, $ciclo, $id_docente) {
        return array_column($this->dao->get_turno_docente($plantel, $ciclo, $id_docente), 'turno');
    }
}
