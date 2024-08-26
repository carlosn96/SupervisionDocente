<?php

include_once '../loader.php';

class RevisorSesion extends API {

    public function cerrar_sesion() {
        $this->enviar_respuesta(["sesion_cerrada" => Sesion::cerrar()]);
    }

    public function verificar_sesion() {
        $info_sesion = Sesion::info();
        $this->enviar_respuesta([
            "sesion_activa" => isset($info_sesion["usuario"]),
            "url" => $info_sesion["url"],
            "usuario" => isset($info_sesion["usuario"]) ? $info_sesion["usuario"]->to_array() : ""
        ]);
    }
}

Util::iniciar_api("RevisorSesion");
