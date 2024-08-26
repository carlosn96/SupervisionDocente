<?php

class RubroSupervision {

    use Entidad;

    private $id_rubro;
    private $descripcion;
    private array $criterios;

    public function __construct($descripcion, array $criterios, $id_rubro = "") {
        $this->id_rubro = $id_rubro;
        $this->descripcion = $descripcion;
        $this->criterios = $criterios;
    }

    public function get_descripcion() {
        return $this->descripcion;
    }

    public function set_descripcion($descripcion): void {
        $this->descripcion = $descripcion;
    }

    public function get_id_rubro() {
        return $this->id_rubro;
    }

    public function get_criterios() {
        return $this->criterios;
    }

    public function set_id_rubro($id_rubro): void {
        $this->id_rubro = $id_rubro;
    }

    public function set_criterios($criterios): void {
        $this->criterios = $criterios;
    }
}
