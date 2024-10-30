<?php

class DocenteDAO extends DAO {

    private const ACTUALIZAR_DOCENTE = "CALL actualizar_docente(?, ?, ?, ?, ?)";

    public function guardar_docente_materias(Docente $docente, $id_carrera, $id_plantel) {
        $materias_json = json_encode($docente->array()["materias"]);
        $nombre = $docente->get_nombre();
        $apellidos = $docente->get_apellidos();
        $perfil_profesional = $docente->get_perfil_profesional();
        $coordinador = $docente->get_id_coordinador();
        $correo = $docente->get_correo_electronico();
        $stmt = $this->preparar_instruccion("CALL insertar_docente_materias_horarios(?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiiis",
                $nombre, $apellidos,
                $correo, $perfil_profesional,
                $coordinador, $id_carrera, $id_plantel, $materias_json);
        return $stmt->execute();
    }

    public function obtener_horario($tipo, $id, $carrera, $plantel) {
        $campo = ($tipo === "Grupo") ? 'grupo' : 'id_docente';
        $instruccion = "SELECT * FROM consultar_horario WHERE $campo = ? AND id_carrera = ? AND id_plantel = ?";
        $args = new PreparedStatmentArgs;
        $args->add('s', $id);
        $args->add('i', $carrera);
        $args->add('i', $plantel);
        return $this->ejecutar_instruccion_prep_result($instruccion, $args);
    }

    public function consultar_disponibilidad($dia, $hora, $carrera, $plantel) {
        $where_hora = empty($hora) ? "" : "AND hora_inicio = '$hora'";
        $instruccion = "SELECT * FROM consultar_horario WHERE dia_semana = '$dia' "
                . " $where_hora AND id_carrera = $carrera AND id_plantel = $plantel";
        
        return ($rs = $this->ejecutar_instruccion($instruccion)->fetch_all(MYSQLI_ASSOC)) ? $rs : [];
    }

    public function obtener_docentes_materias($id_carrera, $id_plantel) {
        return $this->listar_docente_materias_horarios(" id_carrera = $id_carrera AND id_plantel = $id_plantel");
    }

    public function obtener_materias($id_docente) {
        return $this->listar_docente_materias_horarios(" id_docente = $id_docente");
    }

    public function obtener_info_agenda($id_agenda) {
        return $this->listar_docente_materias_horarios(" id_agenda = $id_agenda");
    }

    private function listar_docente_materias_horarios($where) {
        $rs = $this->ejecutar_instruccion("SELECT * FROM listar_docente_materias_horarios WHERE $where");
        $docentes = array();
        foreach ($rs->fetch_all(MYSQLI_ASSOC) as $row) {
            $docenteKey = $row['nombre_docente'] . ' ' . $row['apellido_docente'];
            $materiaKey = $row['nombre_materia'];
            if (!isset($docentes[$docenteKey])) {
                $docentes[$docenteKey] = [
                    'id_docente' => $row['id_docente'],
                    'nombre' => $row['nombre_docente'],
                    'apellidos' => $row['apellido_docente'],
                    'correo_electronico' => $row['correo_electronico'],
                    'perfil_profesional' => $row['perfil_profesional'],
                    'materias' => [],
                    'es_profesor_agendado' => $row['es_profesor_agendado'] == 1 ? true : false,
                    'id_agenda' => $row['id_agenda'],
                    'fecha_agenda' => $row['fecha'],
                    'supervision_hecha' => boolval($row['supervision_hecha']),
                ];
            } else {
                if ($row['es_profesor_agendado'] == 1) {
                    $docentes[$docenteKey]['es_profesor_agendado'] = true;
                }
            }

            if (!isset($docentes[$docenteKey]['materias'][$materiaKey])) {
                $docentes[$docenteKey]['materias'][$materiaKey] = [
                    'horarios' => []
                ];
            }
            $docentes[$docenteKey]['materias'][$materiaKey]["plantel"] = $row["nombre_plantel"];
            $docentes[$docenteKey]['materias'][$materiaKey]["grupo"] = $row["grupo_materia"];
            $docentes[$docenteKey]['materias'][$materiaKey]["id"] = $row["id_materia"];
            $docentes[$docenteKey]['materias'][$materiaKey]["total_horas"] = $row["total_horas"];

            // Agregar el horario al array de horarios de la materia
            $horario = [
                'es_horario_agendado' => $row['es_horario_agendado'] == 1 ? true : false,
                'id_horario' => $row['id_horario'],
                'dia_semana' => $row['dia_semana'],
                'hora_inicio' => date('H:i', strtotime($row['hora_inicio'])),
                'hora_fin' => date('H:i', strtotime($row['hora_fin']))
            ];

            $docentes[$docenteKey]['materias'][$materiaKey]['horarios'][] = $horario;

            // Si el horario es agendado, actualizar la fecha_agenda si es_profesor_agendado es true
            if ($row['es_profesor_agendado'] == 1 && $row['es_horario_agendado'] == 1) {
                $fechaAgenda = new DateTime($row['fecha']);
                $docentes[$docenteKey]['fecha_agenda'] = $fechaAgenda->format('Y-m-d');
            }
        }

        return $docentes;
    }

    public function eliminar($id) {
        return $this->eliminar_por_id("docente", "id_docente", $id);
    }

    public function actualizar(Docente $docente) {
        $prep = new PreparedStatmentArgs();
        $prep->add("i", $docente->get_id_docente());
        $prep->add("s", $docente->get_nombre());
        $prep->add("s", $docente->get_apellidos());
        $prep->add("s", $docente->get_correo_electronico());
        $prep->add("s", $docente->get_perfil_profesional());
        return $this->ejecutar_instruccion_preparada(self::ACTUALIZAR_DOCENTE, $prep);
    }
}
