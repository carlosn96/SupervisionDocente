<?php

include_once '../../../../loader.php';

$coordinador = $_GET["coordinador"] ?? null;
$carrera = $_GET["carrera"] ?? null;
$plantel = $_GET["plantel"] ?? null;
$ciclo = $_GET["ciclo_escolar"] ?? null;

if ($coordinador && $carrera && $plantel && $ciclo) {
    $coordObj = (new AdminCoordinador)->recuperar_por_id($coordinador);
    Sesion::setInfoTemporal("agenda", [
        "docentes" => (new AdminDocente())->obtener_docentes_materias($carrera, $plantel, $ciclo),
        "carrera" => [
            "carrera" => (new AdminCarrera)->recuperar_id($carrera)->to_array(),
            "plantel" => (new AdminPlantel())->recuperar_plantel_id($plantel),
            "coordinador" => $coordinador,
            "nombreCoordinador" => $coordObj->get_nombre() . " " . $coordObj->get_apellidos()
        ]
    ]);
    $url = "../coordinadorAgenda";
} else {
    $url = "../../../../";
}

header("Location: $url");
