<?php

class AdminGrupo {

    private $dao;

    public function __construct() {
        $this->dao = new GrupoDAO();
    }

    public function crear_grupo($form) {
        return $this->dao->agregar($this->construir_grupo($form));
    }

    public function actualizar_grupo($form) {
        return $this->dao->actualizar($this->construir_grupo($form));
    }

    public function construir_grupo($form) {
        $clave = $form["clave"];
        $seudonimo = $form["seudonimo"] ?? "";
        $grado = $form["grado_actual"] ?? "";
        $turno = $form["turno"];
        $carrera = $form["carrera"] ?? $form["id_carrera"];
        $plantel = $form["plantel"] ?? $form["id_plantel"];
        $id = $form["id"] ?? $form["id_grupo"] ?? 0;
        return new Grupo($clave, $carrera, $plantel, $seudonimo, $turno, $grado, $id);
    }

    function listar_grupos($carrera, $plantel) {
        return $this->dao->listar_grupos($carrera, $plantel);
    }

    function eliminar($id) {
        return $this->dao->eliminar($id);
    }

    function recuperar_turnos() {
        return $this->dao->recuperar_turnos();
    }

    function recuperar_grupo_id($id) {
        return $this->construir_grupo($this->dao->recuperar_grupo($id));
    }

    function recuperar_turno_grupo($id) {
        return $this->recuperar_grupo_id($id)->getTurno();
    }

    public function recuperar_grados() {
        return $this->dao->recuperar_grados();
    }
}
