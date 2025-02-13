<?php

class AdminHorario {

    private const TURNOS = [ //Lo sustituye la información almacenada en la base de datos por plantel
        "Matutino" => ["07:00", "15:00", "10:00", 60, 30],
        "Vespertino" => ["17:00", "22:00", "20:00", 60, 30],
        "Sabatino" => ["08:00", "14:00", "10:10", 60, 30],
        "Mixto" => ["08:00", "21:00", "13:00", 60, 30]
    ];

    public function get_bloques_horarios($plantel, $turno) {
       return $this->generar_bloques((new AdminPlantel())->consultar_turno_plantel($plantel, $turno));
        
    }

    private function generar_bloques($datos) {
        $bloques = [];
        $hora_inicio = strtotime($datos['inicio']);
        $hora_fin = strtotime($datos['fin']);
        $duracion_bloque = $datos['duracion_bloque'] * 60;
        $descanso_inicio = strtotime($datos['descanso_inicio']);
        $descanso_duracion = $datos['descanso_duracion'] * 60;
        $hora_actual = $hora_inicio;
        while ($hora_actual < $hora_fin) {
            if ($hora_actual >= $descanso_inicio && $hora_actual < ($descanso_inicio + $descanso_duracion)) {
                $hora_actual = $descanso_inicio + $descanso_duracion;
            } else {
                $bloques[] = [
                    'inicio' => date("H:i", $hora_actual),
                    'fin' => date("H:i", $hora_actual + $duracion_bloque)
                ];
                $hora_actual += $duracion_bloque;
            }
        }
        return $bloques;
    }
}
