<?php

abstract class API {

    private $case;
    protected $data;

    function __construct($case, $data) {
        $this->set_case($case);
        $this->set_data(Util::separarCamposFormulario($data));
        $this->resolver_peticion();
    }

    protected function resolver_peticion() {
        $case = $this->case;
        if (method_exists($this, $case)) {
            $this->$case();
        } else {
            $this->enviar_respuesta_str("sin respuesta");
        }
    }

    private function codificar_respuesta($respuesta) {
        echo json_encode($respuesta);
    }

    protected function enviar_respuesta(array $respuesta) {
        $this->codificar_respuesta($respuesta);
    }

    protected function enviar_respuesta_str(string $respuesta) {
        $this->codificar_respuesta(["response" => $respuesta]);
    }

    protected function enviar_resultado_operacion($es_esultado_correcto) {
        return $this->enviar_respuesta($es_esultado_correcto ? OPERACION_COMPLETA : OPERACION_INCOMPLETA);
    }

    function get_case() {
        return $this->case;
    }

    function set_case($case): void {
        $this->case = $case;
    }

    function set_data($data): void {
        $this->data = $data;
    }

    function get_data($key) {
        return $this->data[$key] ?? "";
    }
}
