<?php

class AdminHorario {

    private const TURNOS = [
        "Matutino" => ["07:00", "15:00", "10:00", 60, 30],
        "Vespertino" => ["17:00", "22:00", "20:00", 60, 30],
        "Sabatino" => ["08:00", "14:00", "10:10", 60, 30],
        "Mixto" => ["08:00", "21:00", "13:00", 60, 30]
    ];

    public function get_bloques_horarios() {
        $turnos = [];
        foreach (self::TURNOS as $nombre => $config) {
            list($entrada, $salida, $descansoHora, $duracionBloque, $duracionDescanso) = $config;
            $turnos[$nombre] = [
                "entrada" => $entrada,
                "salida" => $salida,
                "duracionBloque" => $duracionBloque,
                "descansoHora" => $descansoHora,
                "duracionDescanso" => $duracionDescanso
            ];
        }
        return $turnos;
    }
}
