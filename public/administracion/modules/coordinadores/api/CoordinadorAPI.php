<?php

include_once '../../../../../loader.php';

class CoordinadorAPI extends API {

    function recuperar_campos_formulario() {
        $this->enviar_respuesta([
            "grupoCarreras" => (new AdminCarrera())->recuperar_listado_sin_coordinador(),
            "grupoTodasCarreras" => (new AdminCarrera())->recuperar_listado(),
            "avatares" => $this->leer_lista_avatares()
        ]);
    }

    function guardar() {
        $foto = $_FILES["imagen"]["tmp_name"];
        $avatar = "data:image/jpeg;base64," . base64_encode(file_get_contents(empty($foto) ? ROOT_APP . "public/assets/images/profile/" . $this->data["avatar"] : $foto));
        $this->enviar_resultado_operacion(
                (new AdminCoordinador())->guardar(
                        $this->data["nombre"], $this->data["apellidos"],
                        $this->data["genero"], $this->data["fecha_nacimiento"],
                        $this->data["telefono"], $this->data["correo"] . "@" . $this->data["dominio"],
                        $this->data["carreras"], $avatar
        ));
    }

    function eliminar() {
        $this->enviar_resultado_operacion((new AdminCoordinador())->eliminar($this->data["id"]));
    }

    function listar_coordinadores() {
        $this->enviar_respuesta((new AdminCoordinador)->listar());
    }

    private function leer_lista_avatares() {
        $directory = '../../../../assets/images/profile';
        if (is_dir($directory)) {
            return array_diff(scandir($directory), array('.', '..'));
        } else {
            return [];
        }
    }

    public function buscar_carreras_propias() {
        $this->enviar_respuesta((new AdminCarrera())
                        ->recuperar_listado_detallado_por_id_coordinador(
                                $this->data["id_coordinador"]
                        )
        );
    }

}

Util::iniciar_api("CoordinadorAPI");
