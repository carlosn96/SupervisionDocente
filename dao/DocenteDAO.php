<?php

class DocenteDAO extends DAO {

    private const ACTUALIZAR_DOCENTE = "CALL actualizar_docente(?, ?, ?, ?, ?)";
    private const INSERTAR_NUEVO = "INSERT INTO docente (nombre, apellidos, correo_electronico, perfil_profesional) VALUES (?, ?, ?, ?)";

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

    public function obtener_horario($tipo, $id, $carrera, $plantel, $ciclo) {
        $campo = ($tipo === "Grupo") ? 'id_grupo' : 'id_docente';
        $instruccion = "SELECT * FROM consultar_horario WHERE $campo = ? AND id_carrera = ? AND id_plantel = ? AND id_ciclo_escolar = ?";
        $args = new PreparedStatmentArgs;
        $args->add('s', $id);
        $args->add('i', $carrera);
        $args->add('i', $plantel);
        $args->add('i', $ciclo);
        return $this->ejecutar_instruccion_prep_result($instruccion, $args);
    }

    public function consultar_disponibilidad($dia, $hora, $carrera, $plantel, $ciclo) {
        $where_hora = empty($hora) ? "" : "AND hora_inicio >= '$hora'";
        $instruccion = "SELECT * FROM consultar_horario WHERE dia_semana = '$dia' "
                . " $where_hora AND id_carrera = $carrera AND id_plantel = $plantel"
                . " AND id_ciclo_escolar = $ciclo";
        return ($rs = $this->ejecutar_instruccion($instruccion)->fetch_all(MYSQLI_ASSOC)) ? $rs : [];
    }

    public function obtener_docentes_materias($id_carrera, $id_plantel, $ciclo_escolar) {
        return $this->listar_docente_materias_horarios(" id_carrera = $id_carrera AND id_plantel = $id_plantel AND id_ciclo_escolar = $ciclo_escolar");
    }

    public function obtener_materias($docente, $carrera, $plantel, $ciclo) {
        return $this->listar_docente_materias_horarios(" id_carrera = $carrera AND id_plantel = $plantel AND id_ciclo_escolar = $ciclo AND id_docente = $docente");
    }

    public function agregar_materia($docente, Materia $materia) {
        $prep = new PreparedStatmentArgs;
        $prep->add("i", $docente);
        $prep->add("s", json_encode($materia->to_array()));
        $instruccion = "CALL guardar_materia(?, ?)";
        return $this->ejecutar_instruccion_preparada($instruccion, $prep);
    }

    public function obtener_info_agenda($id_agenda) {
        return $this->listar_docente_materias_horarios(" id_agenda = $id_agenda AND es_horario_agendado = 1");
    }

    private function listar_docente_materias_horarios($where) {
        $rs = $this->ejecutar_instruccion("SELECT * FROM listar_docente_materias_horarios WHERE $where");
        $docentes = array();

        foreach ($rs->fetch_all(MYSQLI_ASSOC) as $row) {
            $docenteKey = $row['nombre_docente'] . ' ' . $row['apellido_docente'];
            $materiaKey = $row['nombre_materia'];

            // Si el docente no está en el array, inicializamos su estructura
            if (!isset($docentes[$docenteKey])) {
                $docentes[$docenteKey] = [
                    'id_docente' => $row['id_docente'],
                    'nombre' => $row['nombre_docente'],
                    'apellidos' => $row['apellido_docente'],
                    'correo_electronico' => $row['correo_electronico'],
                    'perfil_profesional' => $row['perfil_profesional'],
                    'materias' => [], // Inicializamos materias como un array vacío
                    'es_profesor_agendado' => boolval($row['es_profesor_agendado']),
                    'id_agenda' => $row['id_agenda'],
                    'fecha_agenda' => $row['fecha'],
                    'supervision_hecha' => boolval($row['supervision_hecha']),
                ];
            } else {
                // Si ya existe el docente, actualizamos el estado de es_profesor_agendado
                if ($row['es_profesor_agendado'] == 1) {
                    $docentes[$docenteKey]['es_profesor_agendado'] = true;
                }
            }

            // Si el docente tiene materias, agregamos la información de la materia
            if (!empty($row['nombre_materia'])) {
                if (!isset($docentes[$docenteKey]['materias'][$materiaKey])) {
                    $docentes[$docenteKey]['materias'][$materiaKey] = [
                        'horarios' => []
                    ];
                }

                // Rellenamos los detalles de la materia
                $docentes[$docenteKey]['materias'][$materiaKey]["ciclo_escolar"] = $row["ciclo_escolar"];
                $docentes[$docenteKey]['materias'][$materiaKey]["carrera"] = $row["carrera"];
                $docentes[$docenteKey]['materias'][$materiaKey]["plantel"] = $row["nombre_plantel"];
                $docentes[$docenteKey]['materias'][$materiaKey]["grupo"] = $row["grupo_materia"];
                $docentes[$docenteKey]['materias'][$materiaKey]["id"] = $row["id_materia"];
                $docentes[$docenteKey]['materias'][$materiaKey]["total_horas"] = $row["total_horas"];

                // Agregar el horario al array de horarios de la materia
                $horario = [
                    'es_horario_agendado' => $row['es_horario_agendado'] == 1,
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
        }

        // Si el docente no tiene materias, dejamos el array de materias vacío
        foreach ($docentes as &$docente) {
            if (empty($docente['materias'])) {
                $docente['materias'] = []; // Aseguramos que 'materias' sea un array vacío si no tiene materias
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

    public function guardar_docente(Docente $docente) {
        $prep = new PreparedStatmentArgs();
        $prep->add("s", $docente->get_nombre());
        $prep->add("s", $docente->get_apellidos());
        $prep->add("s", $docente->get_correo_electronico());
        $prep->add("s", $docente->get_perfil_profesional());
        return $this->ejecutar_instruccion_preparada(self::INSERTAR_NUEVO, $prep);
    }

    public function existe_docente($nombre, $apellido, $correo): bool {
        $sql = "SELECT 1 FROM docente 
            WHERE LOWER(correo_electronico) = LOWER(?) 
            OR (LOWER(nombre) LIKE LOWER(?) AND LOWER(apellidos) LIKE LOWER(?))";
        $prep = new PreparedStatmentArgs;
        $prep->add("s", $correo);
        $prep->add("s", $nombre);
        $prep->add("s", $apellido);
        $result = $this->ejecutar_instruccion_preparada($sql, $prep, true);
        return $result->get_result()->num_rows > 0;
    }

    public function listar_todos_docentes($where = "") {
        $instruccion = "SELECT * FROM docente $where";
        return $this->ejecutar_instruccion($instruccion)->fetch_all(MYSQLI_ASSOC);
    }

    public function recuperar_docente($id) {
        return $this->listar_docente_materias_horarios(" id_docente = $id");
    }
}
