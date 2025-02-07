<?php

include_once '../../../../../loader.php';

class HorarioAPI extends API {

    private const GRUPO = "Grupo";
    private const DOCENTE = "Docente";
    private const MATERIA = "Materia";
    private const DURACION_BLOQUE_VESPERTINO = 40 * 60; //40 minutos
    private const DURACION_BLOQUE_MATUTINO = 60 * 60; //60 minutos, 1 h
    private const DURACION_DESCANSO_BLOQUE_MATUTINO = 30 * 60; //30 minutos
    const TURNOS_CONFIG = [
        "Matutino" => [
            'inicio' => '07:00',
            'fin' => '14:30',
            'duracion_bloque' => self::DURACION_BLOQUE_MATUTINO,
            'descanso_inicio' => '10:00',
            'descanso_duracion' => self::DURACION_DESCANSO_BLOQUE_MATUTINO
        ],
        "Vespertino" => [
            'inicio' => '17:55',
            'fin' => '21:55',
            'duracion_bloque' => self::DURACION_BLOQUE_VESPERTINO
        ]
    ];

    function obtener_lista_elementos() {
        $tipo = $this->data["tipoHorario"];
        $carrera = $this->data["carrera"];
        $plantel = $this->data["plantel"];
        $cicloEscolar = $this->data["cicloEscolar"];
        $this->enviar_respuesta([
            "tabla_horario" => $this->recuperar_listado_grupo_docente($tipo, $carrera, $plantel, $cicloEscolar),
            "docentes" => (new AdminDocente)->obtener_docentes_materias($carrera, $plantel, $cicloEscolar)
        ]);
    }

    private function recuperar_listado_grupo_docente($tipo, $carrera, $plantel, $ciclo) {
        switch ($tipo) {
            case "grupo":
                $lista = [
                    self::GRUPO => array_map(function ($grupo) {
                        return ["text" => $grupo['grupo'], "id" => $grupo['id_grupo']];
                    }, (new AdminMateria())->listar_grupos($carrera, $plantel, $ciclo))
                ];
                break;
            case "profesor":
                $lista = [
                    self::DOCENTE => array_map(function ($docente) {
                        return [
                    "text" => $docente["nombre"] . " " . $docente["apellidos"],
                    "id" => $docente['id_docente']
                        ];
                    }, array_values((new AdminDocente())->obtener_docentes_materias($carrera, $plantel, $ciclo)))
                ];
                break;
            case "materia":
                $lista = [
                    self::MATERIA => array_map(function ($materia) {
                        return [
                    "text" => $materia["nombre"],
                    "id" => $materia['id_materia']
                        ];
                    }, [
                        ["nombre" => "Materia", "id_materia" => 5]
                    ])
                ];
                break;
        }
        return $lista;
    }

    function recuperar_horario() {
        try {
            $carrera = $this->data["carrera"];
            $plantel = $this->data["plantel"];
            $ciclo = $this->data["ciclo"];
            $id = $this->data["id"];
            $tipo = $this->data["tipo"];
            $rs = (new AdminDocente())->obtener_horario($tipo, $id, $carrera, $plantel, $ciclo);
            $horario = [
                "bloques" => $tipo === self::GRUPO ? $this->get_bloques_grupo($id) : $this->get_bloques_docente(),
                "tipo" => $tipo,
                "id" => $id,
                "horario" => $rs,
                $tipo => $rs[0][strtolower($tipo)]
            ];
            Sesion::setInfoTemporal("horario", $horario);
            Sesion::setInfoTemporal("plantel", (new AdminPlantel())->recuperar_plantel_id($plantel));
            $this->enviar_respuesta(OPERACION_COMPLETA);
        } catch (Exception $e) {
            $this->enviar_respuesta(Util::enum($e->getMessage(), true));
        }
    }

    function consultar_disponibilidad() {
        $dia = $this->data["diaSemana"];
        $hora = $this->data["hora"];
        $carrera = $this->data["carrera"];
        $plantel = $this->data["plantel"];
        $ciclo = $this->data["ciclo"];
        $this->enviar_respuesta(
                (new AdminDocente())->consultar_disponibilidad($dia, $hora, $carrera, $plantel, $ciclo)
        );
    }

    private function get_bloques_grupo($id) {
        $grupo = (new AdminGrupo())->recuperar_grupo_id($id);
        return (new AdminPlantel())->consultar_turno_plantel($grupo->getPlantel(), $grupo->getTurno());
        /* if (isset(self::TURNOS_CONFIG[$turno])) {
          $config = self::TURNOS_CONFIG[$turno];
          return $this->generar_bloques(
          $config['inicio'],
          $config['fin'],
          $config['duracion_bloque'],
          $config['descanso_inicio'] ?? null,
          $config['descanso_duracion'] ?? 0
          );
          } */
    }

    function generar_bloques($inicio, $fin, $duracion, $descanso_inicio = null, $descanso_duracion = 0) {
        $bloques = [];
        $hora_inicio = strtotime($inicio);
        $hora_fin = strtotime($fin);
        while ($hora_inicio + $duracion <= $hora_fin) {
            if ($descanso_inicio && $hora_inicio == strtotime($descanso_inicio)) {
                $hora_inicio = strtotime($descanso_inicio) + $descanso_duracion;
                continue;
            }
            $hora_final = $hora_inicio + $duracion;
            $bloques[] = date('H:i', $hora_inicio) . ' - ' . date('H:i', $hora_final);
            $hora_inicio = $hora_final;
        }
        return $bloques;
    }
}

Util::iniciar_api("HorarioAPI");
