<?php

class Supervision {

    use Entidad;

    private $id_supervision;
    private $fecha;
    private $tema;
    private $conclusion_general;
    private $id_agenda;
    private $criterios_contables;
    private $criterios_no_contables;
    private $contrasenia;

    public function __construct($fecha, $id_agenda, $tema, $conclusion_general,
            array $criterios_contables, array $criterios_no_contables,
            $contrasenia, $id_supervision = "") {
        $this->id_supervision = $id_supervision;
        $this->fecha = $fecha;
        $this->conclusion_general = $conclusion_general;
        $this->tema = $tema;
        $this->id_agenda = $id_agenda;
        $this->criterios_contables = $criterios_contables;
        $this->criterios_no_contables = $criterios_no_contables;
        $this->contrasenia = $contrasenia;
    }

    public function get_contrasenia() {
        return $this->contrasenia;
    }

    public function get_id_supervision() {
        return $this->id_supervision;
    }

    public function get_fecha() {
        return $this->fecha;
    }

    public function get_tema() {
        return $this->tema;
    }

    public function get_conclusion_general() {
        return $this->conclusion_general;
    }

    public function get_id_agenda() {
        return $this->id_agenda;
    }

    public function get_criterios_contables() {
        return $this->criterios_contables;
    }

    public function get_criterios_no_contables() {
        return $this->criterios_no_contables;
    }

    public function set_id_supervision($id_supervision): void {
        $this->id_supervision = $id_supervision;
    }

    public function set_fecha($fecha): void {
        $this->fecha = $fecha;
    }

    public function set_tema($tema): void {
        $this->tema = $tema;
    }

    public function set_conclusion_general($conclusion_general): void {
        $this->conclusion_general = $conclusion_general;
    }

    public function set_id_agenda($id_agenda): void {
        $this->id_agenda = $id_agenda;
    }

    public function set_criterios_contables($criterios_contables): void {
        $this->criterios_contables = $criterios_contables;
    }

    public function set_criterios_no_contables($criterios_no_contables): void {
        $this->criterios_no_contables = $criterios_no_contables;
    }
}
