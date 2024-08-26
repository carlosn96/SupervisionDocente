<?php

class Plantel {

    use Entidad;

    private $id_plantel;
    private $nombre;

    public function __construct($nombre, $id_plantel = "") {
        $this->id_plantel = $id_plantel;
        $this->setNombre($nombre);
    }

    public function getIdPlantel() {
        return $this->id_plantel;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = strtoupper($nombre);
    }
}
