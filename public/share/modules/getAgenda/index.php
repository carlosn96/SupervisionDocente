<?php

include_once '../../../../loader.php';

$coordinador = $_GET["coordinador"] ?? null;
$carrera = $_GET["carrera"] ?? null;
$plantel = $_GET["plantel"] ?? null;

if ($coordinador && $carrera && $plantel) {
    Sesion::setInfoTemporal("agenda", [
        "docentes" => (new AdminDocente())->obtener_docentes_materias($carrera, $plantel),
        "carrera" => [
            "carrera" => (new AdminCarrera)->recuperar_id($carrera)->to_array(),
            "plantel" => (new AdminPlantel())->recuperar_plantel_id($plantel),
            "coordinador"=> $coordinador
        ]
    ]);
    $url = "../coordinadorAgenda";
} else {
    $url = "../../../../";
}

header("Location: $url");
