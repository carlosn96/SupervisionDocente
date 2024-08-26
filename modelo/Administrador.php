<?php

class Administrador extends Usuario {

    private $id_administrador;

    public function __construct($nombre, $apellidos, $genero, $fecha_nacimiento, $telefono,$correo_electronico, $contrasenia, $avatar, $id_usuario) {
        parent::__construct($nombre, $apellidos, $genero, $fecha_nacimiento, $telefono, 
                $correo_electronico, $contrasenia, $avatar, TipoUsuario::ADMINISTRADOR, $id_usuario);
    }

    public function get_id_administrador() {
        return $this->id_administrador;
    }

    public function set_id_administrador($id_administrador): void {
        $this->id_administrador = $id_administrador;
    }
}
