<?php

include_once '../../../../../loader.php';

class CarreraAPI extends API {

    private function get_campos_comunes_carrera($campos_adicionales = []) {
        $this->enviar_respuesta(array_merge([
            "grupoPlanteles" => (new AdminPlantel())->recuperar_listado(),
            "grupoTipos" => (new AdminCarrera())->recuperar_tipos_carrera(),
                        ], $campos_adicionales));
    }

    function recuperar_campos_formulario_nueva_carrera() {
        $this->get_campos_comunes_carrera();
    }

    function recuperar_campos_formulario_listado() {
        $this->get_campos_comunes_carrera([
            "grupoCoordinadoresCarrera" => $this->listar_coordinadores(),
            "listado_detallado" => $this->listar_carreras()
        ]);
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

    private function listar_coordinadores() {
        $coordinadores = [];
        foreach ((new AdminCoordinador())->listar() as $coordinador) {
            $coordinadores[] = [
                "id" => $coordinador["id_coordinador"],
                "nombre" => $coordinador["nombre"] . " " . $coordinador["apellidos"]
            ];
        }
        return $coordinadores;
    }

    private function listar_carreras() {
        $carreras = (new AdminCarrera())->recuperar_listado_detallado();
        foreach ($carreras as &$carrera) {
            if ($carrera["coordinador"] !== "No asignado") {
                unset($carrera["coordinador"]["avatar"]);
            }
        }
        return $carreras;
    }
}

Util::iniciar_api("CarreraAPI");
