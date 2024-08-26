<?php

class Materia {

    use Entidad;

    private $id_materia;
    private $nombre;
    private array $horario;
    private $carrera;
    private $grupo;

    public function __construct($nombre, $carrera, $grupo, $horario = array(), $id_materia = "") {
        $this->id_materia = $id_materia;
        $this->nombre = $nombre;
        $this->horario = $horario;
        $this->carrera = $carrera;
        $this->grupo = $grupo;
    }

    public function get_grupo() {
        return $this->grupo;
    }

    public function set_grupo($grupo): void {
        $this->grupo = $grupo;
    }

    public function get_id_materia() {
        return $this->id_materia;
    }

    public function get_nombre() {
        return $this->nombre;
    }

    public function get_horario() {
        return $this->horario;
    }

    public function get_carrera() {
        return $this->carrera;
    }

    public function set_id_materia($id_materia): void {
        $this->id_materia = $id_materia;
    }

    public function set_nombre($nombre): void {
        $this->nombre = $nombre;
    }

    public function agregar_dia_horas($dia, $hora_inicio, $hora_fin): void {
        $this->horario[] = [
            "dia_semana" => $dia,
            'hora_inicio' => $hora_inicio,
            'hora_fin' => $hora_fin
        ];
    }

    public function agregar_horario(array $horario) {
        array_push($this->horario, $horario);
    }

    public function set_carrera($carrera): void {
        $this->carrera = $carrera;
    }
}
