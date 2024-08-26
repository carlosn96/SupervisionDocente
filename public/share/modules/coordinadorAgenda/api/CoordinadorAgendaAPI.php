<?php

include_once '../../../../../loader.php';

class CoordinadorAgendaAPI extends API {

    private function getAgendaSesion() {
        return Sesion::getInfoTemporal("agenda");
    }

    function consultar_agenda() {
        $this->enviar_respuesta($this->getAgendaSesion());
    }

    public function recuperar_agenda_por_fecha() {
        $agendaSesion = $this->getAgendaSesion()["carrera"];
        $fecha = $this->data["fecha"];
        $plantel = $agendaSesion["plantel"];
        $carrera = $agendaSesion["carrera"];
        $coordinador = $agendaSesion["coordinador"];
        $agenda = (new AdminSupervision)->recuperar_agenda_por_fecha(
                $fecha,
                $coordinador,
                $plantel["id_plantel"], $carrera["id_carrera"],
        );
        Sesion::setInfoTemporal("imprimirAgenda", [
            "listado" => $agenda,
            "fecha" => $fecha,
            "plantel" => $plantel["nombre"],
            "carrera" => $carrera["tipo"]." ".$carrera["nombre"],
            "mes" => $this->data["mes"],
            "año" => $this->data["año"],
        ]);
        $this->enviar_respuesta(["agendaVacia" => empty($agenda)]);
    }
    
    public function salir() {
        Sesion::deleteInfoTemporal("agenda");
    }
}

Util::iniciar_api("CoordinadorAgendaAPI");
