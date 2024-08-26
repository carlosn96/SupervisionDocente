<?php

class Usuario {

    use Entidad;

    private $nombre;
    private $apellidos;
    private $contrasenia;
    private $correo_electronico;
    private $avatar;
    private $tipo_usuario;
    private $id_usuario;
    private $genero;
    private $fecha_nacimiento;
    private $telefono;

    public function __construct($nombre, $apellidos, $genero, $fecha_nacimiento, $telefono, $correo_electronico, $contrasenia, $avatar, $tipo_usuario, $id_usuario = "") {
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->contrasenia = $contrasenia;
        $this->correo_electronico = $correo_electronico;
        $this->tipo_usuario = $tipo_usuario;
        $this->id_usuario = $id_usuario;
        $this->avatar = $avatar;
        $this->genero = $genero;
        $this->fecha_nacimiento = $fecha_nacimiento;
        $this->telefono = $telefono;
    }

    public function get_apellidos() {
        return $this->apellidos;
    }

    public function set_apellidos($apellidos): void {
        $this->apellidos = $apellidos;
    }

    public function get_nombre() {
        return $this->nombre;
    }

    public function get_contrasenia() {
        return $this->contrasenia;
    }

    public function get_correo_electronico() {
        return $this->correo_electronico;
    }

    public function get_tipo_usuario() {
        return $this->tipo_usuario;
    }

    public function get_id_usuario() {
        return $this->id_usuario;
    }

    public function set_nombre($nombre): void {
        $this->nombre = $nombre;
    }

    public function set_contrasenia($contrasenia): void {
        $this->contrasenia = $contrasenia;
    }

    public function set_correo_electronico($correo_electronico): void {
        $this->correo_electronico = $correo_electronico;
    }

    public function set_tipo_usuario($tipo_usuario): void {
        $this->tipo_usuario = $tipo_usuario;
    }

    public function set_id_usuario($id_usuario): void {
        $this->id_usuario = $id_usuario;
    }

    public function get_avatar() {
        return $this->avatar;
    }

    public function set_avatar($avatar): void {
        $this->avatar = $avatar;
    }

    function get_genero() {
        return $this->genero;
    }

    function get_fecha_nacimiento() {
        return $this->fecha_nacimiento;
    }

    function get_telefono() {
        return $this->telefono;
    }

    function set_genero($genero): void {
        $this->genero = $genero;
    }

    function set_fecha_nacimiento($fecha_nacimiento): void {
        $this->fecha_nacimiento = $fecha_nacimiento;
    }

    function set_telefono($telefono): void {
        $this->telefono = $telefono;
    }

}
