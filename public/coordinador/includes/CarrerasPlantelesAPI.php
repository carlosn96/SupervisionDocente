<?php

include_once '../../../loader.php';

class CarrerasPlantelesAPI extends API {

    function recuperar_carreras_coordinador() {
        $this->enviar_respuesta(
                ["carreras" => (new AdminCarrera())->recuperar_listado_detallado_por_id($this->obtener_id_coordinador_actual()),
                    "carrera_plantel_actual" => $this->obtener_configuracion_plantel_actual()]
        );
    }

    function recuperar_listado_planteles_por_carrera() {
        $this->enviar_respuesta((new AdminPlantel())->recuperar_listado_por_carrera($this->data["id_carrera"]));
    }

    function guardar_configuracion_plantel() {
        $id_coordinador = $this->obtener_id_coordinador_actual();
        $id_plantel = $this->data["id_plantel"];
        $id_carrera = $this->data["id_carrera"];
        $this->enviar_resultado_operacion((new AdminPlantel())->guardar_configuracion_carrera_plantel($id_coordinador, $id_carrera, $id_plantel));
    }

    private function obtener_configuracion_plantel_actual() {
        $id = $this->obtener_id_coordinador_actual();
        return ((new AdminPlantel())->recuperar_configuracion_carrera_plantel($id));
    }

    private function obtener_id_coordinador_actual() {
        return Sesion::info()["usuario"]->get_id_coordinador();
    }
}

Util::iniciar_api("CarrerasPlantelesAPI");
