<?php

class AdminUsuario {

    private $dao;

    public function __construct() {
        $this->dao = new UsuarioDAO();
    }

    public function existe_correo($correo) {
        return $this->buscar_por_correo($correo) !== null;
    }

    public function buscar_por_correo($correo): ?Usuario {
        $usuario = $this->dao->recuperar_por_correo($correo);
        return $usuario !== null ? $this->construir_usuario($usuario) : null;
    }

    private function construir_administrador(array $tupla) {
        $nombre = $tupla["nombre"];
        $apellidos = $tupla["apellidos"];
        $correo_electronico = $tupla["correo_electronico"];
        $contrasenia = $tupla["contrasenia"];
        $avatar = $tupla["avatar"];
        $id_usuario = $tupla["id_usuario"];
        $fecha_nacimiento = $tupla["fecha_nacimiento"];
        $genero = $tupla["genero"];
        $telefono = $tupla["telefono"];
        return new Administrador($nombre, $apellidos, $genero, $fecha_nacimiento,
                $telefono, $correo_electronico, $contrasenia, $avatar, $id_usuario);
    }

    private function construir_coordinador(array $tupla) {
        $nombre = $tupla["nombre"];
        $apellidos = $tupla["apellidos"];
        $correo_electronico = $tupla["correo_electronico"];
        $contrasenia = $tupla["contrasenia"];
        $avatar = $tupla["avatar"];
        $id_usuario = $tupla["id_usuario"];
        $coordinador = (new AdminCarrera())->recuperar_listado_detallado_por_coordinador($nombre)[0]["coordinador"];
        $fecha_nacimiento = $tupla["fecha_nacimiento"];
        $genero = $tupla["genero"];
        $telefono = $tupla["telefono"];
        return new Coordinador($nombre, $apellidos, $genero, $fecha_nacimiento, $telefono,
                $correo_electronico,
                $contrasenia, $coordinador["carreras_coordina"],
                $avatar, $coordinador ["id_coordinador"], $id_usuario);
    }

    private function construir_usuario($tupla) {
        switch ($tupla["tipo_usuario"]) {
            case TipoUsuario::COORDINADOR:
                return $this->construir_coordinador($tupla);
            case TipoUsuario::ADMINISTRADOR:
                return $this->construir_administrador($tupla);
            default :
                return null;
        }
    }

    public function guardar_administrador($nombre, $correo_electronico, $contrasenia) {
        $usuario = new Usuario(
                $nombre,
                $correo_electronico,
                Util::encriptar_contrasenia($contrasenia),
                TipoUsuario::ADMINISTRADOR);
        return $this->dao->insertar_nuevo($usuario);
    }

    public function guardar_coordinador(Coordinador &$coordinador) {
        return $this->dao->insertar_nuevo($coordinador);
    }

    public function actualizar_correo(Usuario $usuario, $correo) {
        return $this->dao->actualizar_correo($usuario, $correo);
    }

    public function actualizar_contrasenia(Usuario $usuario, $correo) {
        return $this->dao->actualizar_contrasenia($usuario, $correo);
    }

    public function actualizar_foto_perfil(Usuario $usuario, $correo) {
        try {
            return $this->dao->actualizar_foto_perfil($usuario, $correo);
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    public function actualizar_info_personal($data, $id_usuario) {
        return $this->dao->actualizar_info_personal($data, $id_usuario);
    }

}
