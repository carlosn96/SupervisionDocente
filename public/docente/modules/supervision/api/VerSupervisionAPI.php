<?php

include_once '../../../../../loader.php';

class VerSupervisionAPI extends API {

    public function recuperar_supervision() {
        $this->enviar_respuesta(Sesion::getInfoTemporal("supervision"));
    }
    
    public function salir() {
        Sesion::deleteInfoTemporal("supervision");
    }
}

Util::iniciar_api("VerSupervisionAPI");
