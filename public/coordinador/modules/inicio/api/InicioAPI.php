<?php

include_once '../../../../../loader.php';

class InicioAPI extends API {

    private function obtener_id_coordinador() {
        $usuario = Sesion::info()["usuario"];
        return $usuario ? $usuario->get_id_coordinador() : 0;
    }

    private function obtener_ciclo_actual($id_coordinador) {
        $adminCiclos = new AdminCicloEscolar();
        $ciclos = $adminCiclos->recuperar_listado();
        return $adminCiclos->recuperar_ciclo_actual($id_coordinador) ?: $ciclos[0];
    }

    function recuperar_agenda() {
        $id_coordinador = $this->obtener_id_coordinador();
        if ($id_coordinador) {
            $cicloActual = $this->obtener_ciclo_actual($id_coordinador);
            $this->enviar_respuesta([
                "agenda" => $this->obtener_agenda_general($id_coordinador, "", $cicloActual),
                "ciclos" => (new AdminCicloEscolar())->recuperar_listado(),
                "ciclo_actual" => $cicloActual
            ]);
        } else {
            $this->enviar_respuesta_str("sin respuesta");
        }
    }

    function recuperar_agenda_ciclo_escolar() {
        $id_coordinador = $this->obtener_id_coordinador();
        (new AdminCicloEscolar())->actualizar_ciclo_actual($id_coordinador, $this->data["ciclo"]);
        $this->enviar_respuesta(OPERACION_COMPLETA);
    }

    function recuperar_agenda_fecha() {
        $id_coordinador = $this->obtener_id_coordinador();
        $cicloActual = $this->obtener_ciclo_actual($id_coordinador);
        $this->enviar_respuesta($this->obtener_agenda_general($id_coordinador, $this->data["fecha"], $cicloActual));
    }

    private function obtener_agenda_general($id_coordinador, $fecha, $ciclo_actual) {
        return (new AdminSupervision())->obtener_agenda_general($id_coordinador, $fecha, $ciclo_actual);
    }
}

Util::iniciar_api("InicioAPI");
