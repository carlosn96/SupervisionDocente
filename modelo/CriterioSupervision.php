<?php

class CriterioSupervision {

    use Entidad;

    private $id_criterio;
    private $descripcion;
    private bool $cumplido;
    private $comentario;

    public function __construct($descripcion, $id_criterio = "", $cumplido = false, $comentario = "") {
        $this->id_criterio = $id_criterio;
        $this->descripcion = $descripcion;
        $this->cumplido = $cumplido;
        $this->comentario = $comentario;
    }

    public function get_id_criterio() {
        return $this->id_criterio;
    }

    public function get_descripcion() {
        return $this->descripcion;
    }

    public function get_cumplido(): bool {
        return $this->cumplido;
    }

    public function get_comentario() {
        return $this->comentario;
    }

    public function set_id_criterio($id_criterio): void {
        $this->id_criterio = $id_criterio;
    }

    public function set_descripcion($descripcion): void {
        $this->descripcion = $descripcion;
    }

    public function set_cumplido(bool $cumplido): void {
        $this->cumplido = $cumplido;
    }

    public function set_comentario($comentario): void {
        $this->comentario = $comentario;
    }
}
