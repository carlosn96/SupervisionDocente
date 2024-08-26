<?php

include_once '../../../../../loader.php';

class CarreraAPI extends API {

    function recuperar_campos_formulario() {
        $this->enviar_respuesta([
            "grupoPlanteles" => (new AdminPlantel())->recuperar_listado(),
            "grupoTipos" => (new AdminCarrera())->recuperar_tipos_carrera(),
            "grupoCoordinadoresCarrera" => (new AdminCoordinador())->listar()
        ]);
    }

    function recuperar_listado() {
        $this->enviar_respuesta((new AdminCarrera())->recuperar_listado_detallado());
    }

    function guardar() {
        $this->enviar_resultado_operacion((new AdminCarrera())->guardar(
                        $this->data["nombre"], $this->data["tipo"],
                        $this->data["planteles"]
        ));
    }

    function eliminar() {
        $this->enviar_resultado_operacion((new AdminCarrera())->eliminar($this->data["id"]) ? OPERACION_COMPLETA : OPERACION_INCOMPLETA);
    }

    function existe_carrera() {
        $this->enviar_respuesta((new AdminCarrera)->existe_carrera($this->data["nombre_carrera"]) ? DATO_YA_EXISTE : NO_ERROR);
    }

    function recuperar_info_carrera() {
        return $this->enviar_respuesta((new AdminCarrera())->recuperar_id($this->data["id"])->to_array());
    }

    function actualizar_carrera() {
        return $this->enviar_resultado_operacion((new AdminCarrera)->actualizar($this->data));
    }

}

Util::iniciar_api("CarreraAPI");
