<?php

include_once '../../../../../loader.php';

class PlantelAPI extends API {

    private function get_admin(): AdminPlantel {
        return new AdminPlantel;
    }

    function agregar() {
        $this->enviar_resultado_operacion($this->get_admin()->agregar($this->data["nombre"]));
    }

    function listar() {
        $this->enviar_respuesta($this->get_admin()->recuperar_listado());
    }

    function editar_plantel() {
        $plantel = new Plantel($this->data["nombrePlantel"], $this->data["idPlantel"]);
        $this->enviar_resultado_operacion($this->get_admin()->editar($plantel));
    }

    function eliminar() {
        $this->enviar_resultado_operacion($this->get_admin()->eliminar($this->data["id"]));
    }

}

Util::iniciar_api("PlantelAPI");
