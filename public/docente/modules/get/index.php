<?php

include_once '../../../../loader.php';

$id_agenda = $_GET["exp"] ?? null;

if ($id_agenda) {
    $adminSupervision = new AdminSupervision();
    $expediente = [
        "info_agenda" => resumir_info_agenda((new AdminDocente())->obtener_info_agenda($id_agenda)),
        "supervision" => $adminSupervision->recuperar_supervision($id_agenda)
    ];
    Sesion::setInfoTemporal("supervision", $expediente);
    $url="../supervision";
} else {
    $url = "../";
}
header("Location: $url");

function resumir_info_agenda($data) {
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
