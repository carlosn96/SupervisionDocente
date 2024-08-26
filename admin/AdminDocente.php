<?php

class AdminDocente {

    private $dao;

    public function __construct() {
        $this->dao = new DocenteDAO;
    }

    public function guardar_docente_materias($formulario) {
        return $this->dao->guardar_docente_materias($this->construir_docente($formulario), 
                $formulario["id_carrera"],
                $formulario["id_plantel"]);
    }
    
    public function obtener_agenda_general($id_coordinador) {
        return $this->dao->obtener_agenda_general($id_coordinador);
    }

    public function obtener_docentes_materias($id_carrera, $id_plantel) {
        return $this->dao->obtener_docentes_materias($id_carrera, $id_plantel);
    }

    private function construir_docente($formulario): Docente {
        $nombre = $formulario["nombre"];
        $apellidos = $formulario["apellidos"];
        $correo_electronico = $formulario["correo_electronico"];
        $perfil_profesional = $formulario["perfil_profesional"];
        $id_coordinador = $formulario["id_coordinador"]??"";
        $materias = $this->construir_materias($formulario["materias"] ?? array(), $formulario["id_carrera"]??"");
        $id_docente = $formulario["id_docente"] ?? "";
        return new Docente($nombre, $apellidos, $correo_electronico,
                $perfil_profesional, $materias, $id_coordinador, $id_docente);
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
    
    public function recuperar_materias($id_docente) {
        return $this->dao->obtener_materias($id_docente);
    }
}
