
<?php

class Carrera {

    use Entidad;

    private $id_carrera;
    private $nombre;
    private $tipo;
    private $coordinador;
    private $planteles;

    public function __construct($nombre, $tipo, $planteles, $id_carrera = "", $coordinador = "") {
        $this->id_carrera = $id_carrera;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->planteles = $planteles;
        $this->coordinador = $coordinador;
    }

    public function get_tipo() {
        return $this->tipo;
    }

    public function set_tipo($tipo): void {
        $this->tipo = $tipo;
    }

    public function get_id_carrera() {
        return $this->id_carrera;
    }

    public function get_nombre() {
        return $this->nombre;
    }

    public function get_nombre_completo() {
        return $this->nombre;
    }

    public function get_coordinador() {
        return $this->coordinador;
    }

    public function get_planteles() {
        return $this->planteles;
    }

    public function set_id_carrera($id_carrera): void {
        $this->id_carrera = $id_carrera;
    }

    public function set_nombre($nombre): void {
        $this->nombre = $nombre;
    }

    public function set_coordinador($id_coordinador): void {
        $this->coordinador = $id_coordinador;
    }

    public function set_planteles($planteles): void {
        $this->planteles = $planteles;
    }
}
