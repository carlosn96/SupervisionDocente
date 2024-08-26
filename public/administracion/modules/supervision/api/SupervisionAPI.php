<?php

include_once '../../../../../loader.php';

class SupervisionAPI extends API {

    public function guardar_criterios() {
        $this->enviar_resultado_operacion((new AdminSupervision)->guardar_rubro_criterios($this->data));
    }

    public function recuperar_criterios_por_rubro() {
        $this->enviar_respuesta((new AdminSupervision)->recuperar_criterios_por_rubro());
    }

    public function actualizar_rubro() {
        $this->enviar_resultado_operacion((new AdminSupervision())->actualizar_descripcion_rubro(
                        $this->data["descripcion"], $this->data["id"])
        );
    }

    public function actualizar_criterio() {
        $this->enviar_resultado_operacion((new AdminSupervision())->actualizar_descripcion_criterio(
                        $this->data["descripcion"], $this->data["id"]
        ));
    }

    function eliminar_rubro() {
        $this->enviar_resultado_operacion((new AdminSupervision())->eliminar_rubro($this->data["id"]));
    }

    function eliminar_criterio() {
        $this->enviar_resultado_operacion((new AdminSupervision())->eliminar_criterio($this->data["id"]));
    }

    function guardar_nuevos_criterios() {
        var_dump($this->data);
    }
}

Util::iniciar_api("SupervisionAPI");
