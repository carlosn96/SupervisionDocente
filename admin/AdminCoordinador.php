<?php

class AdminCoordinador {

    private const DEFAULT_PASS = "coordinador2024";

    private $dao;

    public function __construct() {
        $this->dao = new CoordinadorDAO();
    }

    public function guardar($nombre, $apellidos, $genero, $fecha_nacimiento, $telefono, $correo, $carreras_coordina, $avatar) {
        $contrasenia = Util::encriptar_contrasenia(self::DEFAULT_PASS);
        return $this->dao->guardar($this->construir_coordinador($nombre,
                                $apellidos, $genero, $fecha_nacimiento, $telefono,
                                $correo, $contrasenia, $carreras_coordina, $avatar));
    }

    public function listar() {
        return $this->dao->listar();
    }

    public function eliminar($id) {
        return $this->dao->eliminar($id);
    }

    private function construir_coordinador($nombre, $apellidos, $genero, $fecha_nacimiento, $telefono,
            $correo_electronico, $contrasenia, $carreras_coordina, $avatar, $id_coordinador = "") {
        return new Coordinador($nombre, $apellidos, $genero, $fecha_nacimiento, $telefono, $correo_electronico,
                $contrasenia, $carreras_coordina, $avatar, $id_coordinador);
    }

    public function recuperar_por_id(int $id): Coordinador {
        $rs = $this->dao->recuperar_por_id($id)->fetch_assoc();
        $nombre = $rs["nombre"];
        $apellidos = $rs["apellidos"];
        $correo_electronico = $rs["correo_electronico"];
        $carreras_coordina = $rs["carreras_coordina"];
        $avatar = $rs["avatar"];
        $fecha_nacimiento = $rs["fecha_nacimiento"];
        $genero = $rs["genero"];
        $telefono = $rs["telefono"];
        return $this->construir_coordinador($nombre, $apellidos, $genero, $fecha_nacimiento, $telefono, $correo_electronico, "", $carreras_coordina, $avatar, $id);
    }

}
