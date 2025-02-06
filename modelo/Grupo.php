<?php

class Grupo {

    use Entidad;

    private $clave;
    private $carrera;
    private $plantel;
    private $seudonimo;
    private $turno;
    private $id;

    public function __construct($clave, $carrera, $plantel, $seudonimo, $turno, $id = "") {
        $this->clave = $clave;
        $this->carrera = $carrera;
        $this->plantel = $plantel;
        $this->seudonimo = $seudonimo;
        $this->turno = $turno;
        $this->id = $id;
    }

    public function getPlantel() {
        return $this->plantel;
    }

    public function setPlantel($plantel): void {
        $this->plantel = $plantel;
    }
    
    public function getTurno() {
        return $this->turno;
    }

    public function setTurno($turno): void {
        $this->turno = $turno;
    }

    public function getClave() {
        return $this->clave;
    }

    public function getCarrera() {
        return $this->carrera;
    }

    public function getSeudonimo() {
        return $this->seudonimo;
    }

    public function setClave($clave): void {
        $this->clave = $clave;
    }

    public function setCarrera($carrera): void {
        $this->carrera = $carrera;
    }

    public function setSeudonimo($seudonimo): void {
        $this->seudonimo = $seudonimo;
    }
}
