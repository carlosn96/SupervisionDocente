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
            "supervisiones" => $this->recuperar_docentes(),
            "eventos" => (new AdminAgenda)->listar_eventos(Sesion::obtener_usuario_actual()->get_id_coordinador())
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

    public function actualizar_dia() {
        $id_agenda = $this->data["horario"]["detalles"]["id_agenda"];
        $fecha = $this->data["fecha_actualizar"];
        $this->enviar_resultado_operacion((new AdminSupervision())->actualizar_fecha_agenda($fecha, $id_agenda));
    }

    public function guardar_evento() {
        $this->data["id_coordinador"] = Sesion::obtener_usuario_actual()->get_id_coordinador();
        $this->enviar_resultado_operacion((new AdminAgenda())->guardar($this->data));
    }

    public function eliminar_evento() {
        $this->enviar_resultado_operacion((new AdminAgenda)->eliminar($this->data["id_evento"]));
    }

    public function actualizar_evento() {
        $id = $this->data["id_evento"];
        $campo = $this->data["campo"];
        $valor = ($campo === "fecha_hora_inicio" || $campo === "fecha_hora_fin") ?
                Util::convertToMySQLDateTime($this->data["val"]) :
                $this->data["val"];
        $this->enviar_resultado_operacion((new AdminAgenda)->actualizar($id, $campo, $valor));
    }
}

Util::iniciar_api("AgendaAPI");
