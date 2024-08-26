<?php

include_once '../../../../../loader.php';

class IndexAPI extends API {

    public function consultar_supervision() {
        
        
        $id_agenda = (new AdminSupervision())->recuperar_id_agenda_por_id_supervision($this->data["expediente"]);
       // var_dump($id_agenda);
        
        $adminSupervision = new AdminSupervision();
        $expediente = [
            "info_agenda" => $this->resumir_info_agenda((new AdminDocente())->obtener_info_agenda($id_agenda)),
            "supervision" => $adminSupervision->recuperar_supervision($id_agenda)
        ];
        Sesion::setInfoTemporal("supervision", $expediente);
        $this->enviar_respuesta($expediente);
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

Util::iniciar_api("IndexAPI");
