<?php

class Plantel {

    use Entidad;

    private $id_plantel;
    private $nombre;
    private $director;

    public function __construct($nombre, $director, $id_plantel = "") {
        $this->id_plantel = $id_plantel;
        $this->director = $director;
        $this->setNombre($nombre);
    }

    public function getIdPlantel() {
        return $this->id_plantel;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getDirector() {
        return $this->director;
    }

    public function setNombre($nombre) {
        $this->nombre = strtoupper($nombre);
    }
}
