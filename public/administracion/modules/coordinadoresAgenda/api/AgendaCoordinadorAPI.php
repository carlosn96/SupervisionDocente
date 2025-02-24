<?php

include_once '../../../../../loader.php';

class AgendaCoordinadorAPI extends API {

    function listar_coordinadores() {
        $this->enviar_respuesta([
            "coordinadores" =>(new AdminCoordinador)->listar(),
            "ciclos" => (new AdminCicloEscolar)->recuperar_listado()
        ]);
    }

    public function buscar_carreras_propias() {
        $this->enviar_respuesta((new AdminCarrera())
                        ->recuperar_listado_detallado_por_id_coordinador(
                                $this->data["id_coordinador"]
                        )
        );
    }

    function recuperar_listado_planteles_por_carrera() {
        $this->enviar_respuesta((new AdminPlantel())->recuperar_listado_por_carrera($this->data["carrera"]));
    }

    function consultar_agenda() {
        Sesion::setInfoTemporal("consultar_agenda_coordinador", $this->data);
    }
}

Util::iniciar_api("AgendaCoordinadorAPI");
