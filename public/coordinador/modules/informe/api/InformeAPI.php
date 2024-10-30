<?php

include_once '../../../../../loader.php';

class InformeAPI extends API {

    function recuperar_supervisiones() {
        $plantel = $this->data["plantel"];
        $carrera = $this->data["carrera"];
        $this->enviar_respuesta((new AdminSupervision)->recuperar_supervisiones($plantel, $carrera));
    }
}

Util::iniciar_api("InformeAPI");
