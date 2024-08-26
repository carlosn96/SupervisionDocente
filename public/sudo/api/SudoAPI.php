<?php

include_once '../../../loader.php';

class SudoAPI extends API {

    private $admin_usuario;

    function __construct($case, $data) {
        $this->admin_usuario = new AdminUsuario();
        parent::__construct($case, $data);
    }

    public function guardar_administrador() {
        $this->enviar_respuesta(
                $this->admin_usuario->guardar_administrador(
                        $this->data["nombre_registrar"],
                        $this->data["correo_registrar"],
                        $this->data["contrasenia_registrar"]
                ) ? OPERACION_COMPLETA : OPERACION_INCOMPLETA
        );
    }
}

Util::iniciar_api("SudoAPI");
