<?php

class AdminCarrera {

    private $dao;

    public function __construct() {
        $this->dao = new CarreraDAO();
    }

    private function construir_listado_carreras($tuplas, $to_array = true) {
        $lista = array();
        foreach ($tuplas as $tupla) {
            $carrera = $this->construir_carrera($tupla);
            array_push($lista, $to_array ? $carrera->to_array() : $carrera);
        }
        return $lista;
    }

    public function recuperar_listado() {
        return $this->construir_listado_carreras($this->dao->recuperar_listado());
    }

    public function recuperar_listado_detallado() {
        return $this->listar_detallado();
    }

    public function recuperar_listado_detallado_por_coordinador($nombre) {
        return $this->listar_detallado(" WHERE coordinador_nombre='" . $nombre . "'");
    }
    
    public function recuperar_listado_detallado_por_id_coordinador($id) {
        return $this->listar_detallado(" WHERE id_usuario_coordinador=$id");
    }


    public function recuperar_listado_detallado_por_id($id) {
        return $this->listar_detallado(" WHERE id_coordinador=" . $id);
    }

    private function listar_detallado($where = "") {
        $admin_coordinador = new AdminCoordinador();
        $lista = $this->construir_listado_carreras($this->dao->recuperar_listado_detallado($where), false);
        foreach ($lista as $idx => &$carrera) {
            if ($carrera->get_coordinador() !== "No asignado") {
                $coord = $admin_coordinador->recuperar_por_id($carrera->get_coordinador());
                $carrera->set_coordinador($coord->to_array());
            }
            $lista[$idx] = $carrera->to_array();
        }
        return $lista;
    }

    public function recuperar_listado_sin_coordinador() {
        return $this->construir_listado_carreras($this->dao->recuperar_listado_sin_coordinador());
    }

    public function recuperar_tipos_carrera() {
        $tupla = $this->dao->recuperar_tipos_carrera();
        preg_match("/^enum\((.*)\)$/", $tupla['Type'], $matches);
        return str_getcsv($matches[1], ',', "'");
    }

    public function guardar(string $nombre, $tipo, array $planteles) {
        $carrera = new Carrera($nombre, $tipo, $planteles);
        return $this->dao->guardar($carrera);
    }

    public function eliminar($id) {
        return $this->dao->eliminar($id);
    }

    public function actualizar($formulario) {
        return $this->dao->actualizar($this->construir_carrera($formulario));
    }

    private function construir_carrera($tupla): Carrera {
        $planteles = $tupla["planteles"] ?? [];
        $id_carrera = $tupla["id_carrera"] ?? "";
        $coordinador = isset($tupla["id_coordinador"]) ? $tupla["id_coordinador"] : "";
        return new Carrera($tupla["nombre"], $tupla["tipo"], $planteles, $id_carrera, $coordinador);
    }

    public function existe_carrera($nombre) {
        return count($this->dao->recuperar_nombre($nombre)) > 0;
    }

    public function recuperar_id($id) {
        return $this->construir_carrera($this->dao->recuperar_id($id)[0]);
    }
}
