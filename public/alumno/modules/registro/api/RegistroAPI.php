<?php

include_once '../../../../../loader.php';

class RegistroAPI extends API {

    private const DICCIONARIO_CODIGO_VALIDACION = "0123456789";
    private const LEN_CODIGO_VALIDACION = 6;

    function pre_registro() {
        //$correo = $this->data["correo"];
        $correo = "carlithos906@gmail.com";
        $mail = AdminMailer::enviarCorreoVerificacionCuenta($correo, ($codigo = $this->get_codigo_validacion_cuenta()));
        if ($mail["status"]) {
            Sesion::setInfoTemporal("pre_registro", [
                "data" => $this->data,
                "codigo" => $codigo
            ]);
        }
        $this->enviarRespuesta($mail);
    }

    function verificar_matricula_existe() {
        $matricula = $this->get_data("matricula");
        $existe_matricula = (new AdminUsuario())->existe_matricula($matricula);
        $mensaje = $existe_matricula ? "La matricula \"$matricula\" ya cuenta con un registro en el sistema" : "Matricula disponible";
        $this->enviar_respuesta(Util::enum($mensaje, $existe_matricula));
    }

    private function get_codigo_validacion_cuenta() {
        return substr(str_shuffle(self::DICCIONARIO_CODIGO_VALIDACION), 0, self::LEN_CODIGO_VALIDACION);
    }
}

Util::iniciar_api(RegistroAPI::class);
