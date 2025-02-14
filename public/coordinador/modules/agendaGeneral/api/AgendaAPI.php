<?php

include_once '../../../../../loader.php';

class AgendaAPI extends API {

    public function recuperar_agenda() {
        $ciclos = ($adminCiclos = new AdminCicloEscolar)->recuperar_listado();
        $cicloActual = ($cicloActual = $adminCiclos->recuperar_ciclo_actual($id = $this->get_coordinador())) ? $cicloActual : $ciclos[0];
        $this->enviar_respuesta([
            "agenda" => (new AdminAgenda)->recuperar_agenda_por_coordinador($id),
            "ciclos" => $ciclos,
            "ciclo_actual" => $cicloActual
        ]);
    }

    public function actualizar_ciclo_escolar() {
        $id_coordinador = $this->get_coordinador();
        $ciclo = $this->get_data("ciclo");
        $this->enviar_resultado_operacion((new AdminCicloEscolar)->actualizar_ciclo_actual($id_coordinador, $ciclo));
    }

    private function get_coordinador() {
        return Sesion::obtener_usuario_actual()->get_id_coordinador();
    }
}

Util::iniciar_api(AgendaAPI::class);
