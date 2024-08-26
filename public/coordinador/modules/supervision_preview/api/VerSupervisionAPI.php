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
}

Util::iniciar_api("VerSupervisionAPI");
