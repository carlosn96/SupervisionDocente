<?php

include_once '../../../loader.php';

class IndexAPI extends API {

    private $admin_usuario;

    #[\Override]
    function __construct($case, $data) {
        $this->admin_usuario = new AdminUsuario();
        parent::__construct($case, $data);
    }

    public function iniciar_sesion() {
        $usuario = $this->admin_usuario->buscar_por_correo($this->data["correo_inicio_sesion"]);
        if (is_null($usuario)) {
            $rsp = ERROR_ACCESO_USUARIO;
        } else if (Util::verificar_contrasenia($this->data["contrasenia_inicio_sesion"], $usuario->get_contrasenia())) {
            Sesion::establecer_usuario_actual($usuario);
            $rsp = NO_ERROR;
        } else {
            $rsp = ERROR_CLAVE;
        }
        $this->enviar_respuesta($rsp);
    }

    public function comprobar_existe_telefono() {
        $this->enviar_respuesta_existe_telefono($this->admin_usuario->existe_correo($this->data["telefono"]));
    }

    private function enviar_respuesta_existe_telefono(bool $existe_telefono) {
        $this->enviar_respuesta($existe_telefono ? USUARIO_YA_EXISTE : NO_ERROR);
    }

}

Util::iniciar_api("IndexAPI");