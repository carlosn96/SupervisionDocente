<?php

include_once '../../../../../loader.php';

class AgendaAPI extends API {

    public function agendar_supervision() {
        return $this->enviar_respuesta((new AdminSupervision())->agendar_supervision(
                                $this->data["idHorario"],
                                $this->data["fechaSupervision"]));
    }

    public function recuperar_docentes() {
        return (new AdminDocente())->obtener_docentes_materias($this->data["id_carrera"], $this->data["id_plantel"]);
    }

    public function recuperar_agenda() {
        $this->enviar_respuesta([
            "docentes" => $this->recuperar_docentes()
        ]);
    }

    public function recuperar_agenda_por_fecha() {
        $fecha = $this->data["fecha"];
        $plantel = $this->data["plantel"];
        $carrera = $this->data["carrera"];
        $agenda = (new AdminSupervision)->recuperar_agenda_por_fecha(
                $fecha,
                Sesion::info()["usuario"]->get_id_coordinador(),
                $plantel["id"], $carrera["id"],
        );
        Sesion::setInfoTemporal("agenda", [
            "listado" => $agenda,
            "fecha" => $fecha,
            "plantel" => $plantel["val"],
            "carrera" => $carrera["val"],
            "mes" => $this->data["mes"],
            "año" => $this->data["año"],
        ]);
        $this->enviar_respuesta(["agendaVacia" => empty($agenda)]);
    }

    public function eliminar() {
        $this->enviar_resultado_operacion((new AdminSupervision)->eliminar_horario_agendado($this->data["id_horario"]));
    }
}

Util::iniciar_api("AgendaAPI");
