<?php

class Coordinador extends Usuario {

    private $id_coordinador;
    private $carreras_coordina;

    public function __construct($nombre, $apellidos, $genero, $fecha_nacimiento, $telefono,
            $correo_electronico, $contrasenia, $carreras_coordina, $avatar, $id_coordinador = "", $id_usuario = "") {
        parent::__construct($nombre, $apellidos, $genero, $fecha_nacimiento, $telefono, $correo_electronico, $contrasenia, $avatar, TipoUsuario::COORDINADOR, $id_usuario);
        $this->id_coordinador = $id_coordinador;
        $this->carreras_coordina = $carreras_coordina;
        $this->avatar = $avatar;
    }

    public function get_id_coordinador() {
        return $this->id_coordinador;
    }

    public function get_carrereas_coordina() {
        return $this->carreras_coordina;
    }

    public function set_id_coordinador($id_coordinador): void {
        $this->id_coordinador = $id_coordinador;
    }

    public function set_carrereas_coordina($carrereas_coordina): void {
        $this->carrereas_coordina = $carrereas_coordina;
    }

    public function get_carreras_coordina() {
        return $this->carreras_coordina;
    }

    public function get_avatar() {
        return $this->avatar;
    }

    public function set_carreras_coordina($carreras_coordina): void {
        $this->carreras_coordina = $carreras_coordina;
    }

    public function set_avatar($avatar): void {
        $this->avatar = $avatar;
    }

}
