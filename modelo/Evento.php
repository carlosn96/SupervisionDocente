<?php

class Evento {

    use Entidad;

    private $id;
    private $id_coordinador;
    private $nombre;
    private $fecha_hora_inicio;
    private $fecha_hora_fin;
    private $lugar;
    private $detalles;

    public function __construct($id, $id_coordinador, $nombre, $fecha_hora_inicio, $fecha_hora_fin = null, $lugar = '', $detalles = '') {
        $this->id = $id;
        $this->id_coordinador = $id_coordinador;
        $this->nombre = $nombre;
        $this->fecha_hora_inicio = $fecha_hora_inicio;
        $this->fecha_hora_fin = $fecha_hora_fin;
        $this->lugar = $lugar;
        $this->detalles = $detalles;
    }

    public function get_id_coordinador() {
        return $this->id_coordinador;
    }

    public function set_id_coordinador($id_coordinador): void {
        $this->id_coordinador = $id_coordinador;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_nombre() {
        return $this->nombre;
    }

    public function get_fecha_hora_inicio() {
        return $this->fecha_hora_inicio;
    }

    public function get_fecha_hora_fin() {
        return $this->fecha_hora_fin;
    }

    public function get_lugar() {
        return $this->lugar;
    }

    public function get_detalles() {
        return $this->detalles;
    }

    public function set_id($id): void {
        $this->id = $id;
    }

    public function set_nombre($nombre): void {
        $this->nombre = $nombre;
    }

    public function set_fecha_hora_inicio($fecha_hora_inicio): void {
        $this->fecha_hora_inicio = $fecha_hora_inicio;
    }

    public function set_fecha_hora_fin($fecha_hora_fin): void {
        $this->fecha_hora_fin = $fecha_hora_fin;
    }

    public function set_lugar($lugar): void {
        $this->lugar = $lugar;
    }

    public function set_detalles($detalles): void {
        $this->detalles = $detalles;
    }
}
