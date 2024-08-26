<?php

include_once '../../../../../loader.php';

class ConfigurarPerfilAPI extends API {

    function configurar_perfil() {
        $correo = $this->data["correo"] ?? null;
        $contrasenia = $this->data["newPassword"] ?? null;
        $usuario = $this->get_usuario();
        if (isset($correo)) {
            $correoCompleto = $correo . "@" . $this->data["dominio"];
            $respuesta = (new AdminUsuario())->actualizar_correo($usuario, $correoCompleto);
            if ($respuesta) {
                $usuario->set_correo_electronico($correoCompleto);
                Sesion::establecer_usuario_actual($usuario);
            }
        } elseif (isset($contrasenia)) {
            $respuesta = (new AdminUsuario())->actualizar_contrasenia($usuario, Util::encriptar_contrasenia($contrasenia));
        } else {
            $respuesta = [];
        }
        $this->enviar_resultado_operacion($respuesta);
    }

    function actualizar_imagen() {
        $imgData = $this->data["img"];
        $imgData = str_replace('data:image/png;base64,', '', $imgData);
        $imgData = str_replace(' ', '+', $imgData);
        $usuario = $this->get_usuario();
        if (($resultado = (new AdminUsuario)->actualizar_foto_perfil($usuario, $imgData))) {
            $usuario->set_avatar($imgData);
            Sesion::establecer_usuario_actual($usuario);
        }
        $this->enviar_resultado_operacion($resultado);
    }

    function actualizar_campo() {
        $usuario = $this->get_usuario();
        if (($es_resultado_correcto = (new AdminUsuario)->actualizar_info_personal($this->data, $usuario->get_id_usuario()))) {
            foreach ($this->data as $key => $value) {
                $fn = "set_" . $key;
                $usuario->$fn($value);
            }
            Sesion::establecer_usuario_actual($usuario);
        }
        return $this->enviar_resultado_operacion($es_resultado_correcto);
    }

    private function get_usuario() {
        return $usuario = Sesion::info()["usuario"];
    }

}

Util::iniciar_api(ConfigurarPerfilAPI::class);
