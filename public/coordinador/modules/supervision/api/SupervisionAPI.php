<?php

include_once '../../../../../loader.php';

class SupervisionAPI extends API {

    public function obtener_info_agenda() {
        $this->enviar_respuesta((new AdminDocente())->obtener_info_agenda($this->data["id_agenda"]));
    }

    public function recuperar_criterios_por_rubro() {
        $this->enviar_respuesta((new AdminSupervision)->recuperar_criterios_por_rubro());
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
}

Util::iniciar_api("SupervisionAPI");
