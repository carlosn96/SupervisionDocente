<?php

class Sesion {

    private const ADMINISTRACION = "../administracion";
    private const COORDINACION = "../coordinador";
    public const INDEX = ROOT_APP;

    public static function establecer_usuario_actual(Usuario $usuario): void {
        $_SESSION["usuario"] = serialize($usuario);
    }

    public static function obtener_usuario_actual(): ?Usuario {
        $usuario = isset($_SESSION["usuario"]) ?
                unserialize($_SESSION["usuario"]) : null;
        return $usuario;
    }

    public static function cerrar() {
        unset($_SESSION);
        session_destroy();
        return is_null(Sesion::obtener_usuario_actual());
    }

    public function actualizar($usuario) {
        self::establecer_usuario($usuario);
    }

    private static function verificar_url_sesion() {
        $usuario = self::obtener_usuario_actual();
        switch (is_null($usuario) ? "" : $usuario->get_tipo_usuario()) {
            case TipoUsuario::COORDINADOR:
                $url = self::COORDINACION;
                break;
            case TipoUsuario::ADMINISTRADOR:
                $url = self::ADMINISTRACION;
                break;
            default:
                $url = self::INDEX;
        }
        return $url;
    }

    public static function info(): array {
        $info["usuario"] = self::obtener_usuario_actual();
        $info["url"] = self::verificar_url_sesion();
        //$info["root_app"] = self::INDEX;
        return $info;
    }

    public static function setInfoTemporal($key, $val) {
        $_SESSION[$key] = $val;
    }

    public static function getInfoTemporal($key) {
        return $_SESSION[$key] ?? [];
    }
    
    public static function deleteInfoTemporal($key) {
        self::setInfoTemporal($key, null);
    }
}
