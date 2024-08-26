<?php

include_once '../../../../../loader.php';

class InicioAPI extends API {

    private function consultar_agenda($fecha = "") {
        $usuario = Sesion::info()["usuario"];
        $id_coordinador = $usuario ? $usuario->get_id_coordinador() : null;
        if ($id_coordinador) {
            $this->enviar_respuesta((new AdminSupervision())->obtener_agenda_general($id_coordinador, $fecha));
        } else {
            $this->enviar_respuesta_str("sin respuesta");
        }
    }

    function recuperar_agenda() {
        return $this->consultar_agenda();
    }

    function recuperar_agenda_fecha() {
        return $this->consultar_agenda($this->data["fecha"]);
    }
}

Util::iniciar_api("InicioAPI");
