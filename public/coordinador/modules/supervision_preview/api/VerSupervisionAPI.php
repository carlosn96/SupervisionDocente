<?php

include_once '../../../../../loader.php';

class VerSupervisionAPI extends API {

    public function recuperar_supervision() {
        $id_agenda = $this->data["id_agenda"];
        $adminSupervision = new AdminSupervision();
        $this->enviar_respuesta([
            "info_agenda" => $this->resumir_info_agenda((new AdminDocente())->obtener_info_agenda($id_agenda)),
            "supervision" => $adminSupervision->recuperar_supervision($id_agenda)
        ]);
    }

    public function actualizar_supervision() {
        $campo = $this->data["columna"];
        $valor = $this->data["valor"];
        $id_agenda = $this->data["id_agenda"];
        $this->enviar_resultado_operacion((new AdminSupervision)->actualizar_supervision($campo, $valor, $id_agenda));
    }

    private function resumir_info_agenda($data) {
        foreach ($data as $key => $profesor) {
            foreach ($profesor["materias"] as $materia => $detalles) {
                $agendado = false;
                foreach ($detalles["horarios"] as $horario) {
                    if ($horario["es_horario_agendado"]) {
                        $agendado = true;
                        break;
                    }
                }
                if (!$agendado) {
                    unset($data[$key]["materias"][$materia]);
                }
            }
        }
        return $data;
    }

    public function actualizar_cumplimiento_criterio_contable() {
        $id_supervision = $this->data["id_supervision"];
        $id_criterio = $this->data["id_criterio"];
        $es_criterio_cumplido = intval(filter_var($this->data["criterio_cumplido"], FILTER_VALIDATE_BOOLEAN));
        $this->enviar_resultado_operacion((new AdminSupervision)->actualizar_cumplimiento_criterio_contable($id_supervision,
                        $id_criterio, $es_criterio_cumplido));
    }

    public function actualizar_comentario_criterio_contable() {
        $id_supervision = $this->data["id_supervision"];
        $id_criterio = $this->data["id_criterio"];
        $comentario = $this->data["comentario"];
        $this->enviar_resultado_operacion((new AdminSupervision)->actualizar_comentario_criterio_contable($id_supervision,
                        $id_criterio, $comentario));
    }
}

Util::iniciar_api("VerSupervisionAPI");
