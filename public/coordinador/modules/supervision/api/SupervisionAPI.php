<?php

include_once '../../../../../loader.php';

class SupervisionAPI extends API {

    public function obtener_info_agenda() {
        $id_agenda = $this->data["id_agenda"];
        $this->enviar_respuesta([
            "agenda" => (new AdminDocente())->obtener_info_agenda($id_agenda),
            "criterios" => (new AdminSupervision)->recuperar_criterios_por_rubro(),
            "info_agenda_temp" => Sesion::getInfoTemporal("supervisionTemp")[$id_agenda] ?? []
        ]);
    }

    public function generar_comentarios_supervision() {
        $id_usuario = Sesion::info()["usuario"]->get_id_usuario();
        $criterios = [];
        $response["retroalimentacion"] = (new AdminLLM())->generar_comentarios_supervision($id_usuario, $this->data["model"], $criterios);
        $this->enviar_respuesta($response);
    }

    public function guardar_supervision() {
        $this->enviar_resultado_operacion((new AdminSupervision())->guardar_supervision($this->data));
    }

    public function guardar_info_temp() {
        $keyInfoSupTemp = "supervisionTemp";
        $supervisionTemp = Sesion::getInfoTemporal($keyInfoSupTemp);
        $supervisionTemp[$this->data["id_agenda"]][$this->data["input_id"]] = $this->data;
        Sesion::setInfoTemporal($keyInfoSupTemp, $supervisionTemp);
        //Sesion::deleteInfoTemporal($keyInfoSupTemp);
        $this->enviar_respuesta($supervisionTemp);
    }
    
}

Util::iniciar_api("SupervisionAPI");
