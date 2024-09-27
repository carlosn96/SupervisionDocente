<?php

class SupervisionDAO extends DAO {

    private const TABLA_RUBRO = "supervision_rubro";
    private const TABLA_CRITERIO = "supervision_criterio";
    private const RECUPERAR_CRITERIOS_POR_RUBRO = "SELECT * FROM listar_criterios_supervision_por_rubroTIPO ORDER BY id_rubro ASC";
    private const ACTUALIZAR_DESCRIPCION_RUBRO = "UPDATE `supervision_rubro` SET `descripcion` = ? WHERE (`id_rubro` = ?)";
    private const ACTUALIZAR_DESCRIPCION_CRITERIO = "UPDATE `supervision_criterio` SET `descripcion` = ? WHERE (`id_criterio` = ?)";
    private const TIPO_RUBROS = ["contable" => "", "no_contable" => "_no_contable"];
    private const GUARDAR_SUPERVISION = "CALL insertar_supervision(?, ?, ?, ?, ?, ?, ?)";
    private const RECUPERAR_SUPERVISION_REALIZADA_CRITERIOS = "SELECT * FROM consultar_detalles_supervision_realizada WHERE id_agenda = ?";
    private const RECUPERAR_SUPERVISION = "SELECT * FROM consultar_supervision WHERE id_agenda = ?";
    private const RECUPERAR_AGENDA_GENERAL_POR_COORDINADOR = "SELECT * FROM listar_agenda_supervision WHERE id_coordinador = ? [WHERE] ORDER BY fecha, hora_inicio";

    public function guardar_rubro_criterios(array $rubros) {
        $cuenta_ejecuciones = 0;
        $cuenta_criterios = 0;
        foreach ($rubros as $rubro) {
            $descripcion = $rubro->get_descripcion();
            $cuenta_ejecuciones += $this->ejecutar_instruccion("INSERT INTO supervision_rubro (descripcion) VALUES ('$descripcion')");
            $id_rubro = $this->obtener_id_autogenerado();
            foreach ($rubro->get_criterios() as $criterio) {
                $cuenta_criterios++;
                $descripcion = $criterio->get_descripcion();
                $cuenta_ejecuciones += $this->ejecutar_instruccion("INSERT INTO supervision_criterio (id_rubro, descripcion) VALUES ($id_rubro, '$descripcion')");
            }
        }
        return $cuenta_ejecuciones === count($rubros) + $cuenta_criterios;
    }

    public function recuperar_criterios_por_rubro() {
        $rubros = [];
        foreach (self::TIPO_RUBROS as $key => $tipo_rubro) {
            $rubros[$key] = $this->recuperar_criterios($tipo_rubro);
        }
        return $rubros;
    }

    public function recuperar_criterios($tipo_rubro) {
        $result = $this->ejecutar_instruccion(str_replace("TIPO", $tipo_rubro, self::RECUPERAR_CRITERIOS_POR_RUBRO));
        if ($result->num_rows > 0) {
            $rubros = array();
            while ($row = $result->fetch_assoc()) {
                $rubro_id = $row['id_rubro'];
                if (!isset($rubros[$rubro_id])) {
                    $rubros[$rubro_id] = array(
                        'id' => $row['id_rubro'],
                        'descripcion' => $row['rubro_descripcion'],
                        'criterios' => array()
                    );
                }
                if (!empty($row['id_criterio'])) {
                    $rubros[$rubro_id]['criterios'][] = array(
                        'id' => $row['id_criterio'],
                        'descripcion' => $row['descripcion_criterio']
                    );
                }
            }
            return $rubros;
        } else {
            return array();
        }
    }

    public function actualizar_descripcion_rubro($descripcion, $id) {
        return $this->actualizar_supervision_rubro_criterio(self::ACTUALIZAR_DESCRIPCION_RUBRO, $id, $descripcion);
    }

    public function actualizar_descripcion_criterio($descripcion, $id) {
        return $this->actualizar_supervision_rubro_criterio(self::ACTUALIZAR_DESCRIPCION_CRITERIO, $id, $descripcion);
    }

    private function actualizar_supervision_rubro_criterio($instruccion, $id, $descripcion) {
        $arg = new PreparedStatmentArgs;
        $arg->add("s", $descripcion);
        $arg->add("i", $id);
        return $this->ejecutar_instruccion_preparada($instruccion, $arg);
    }

    public function eliminar_rubro($id) {
        return $this->eliminar_por_id(self::TABLA_RUBRO, "id_rubro", $id);
    }

    public function eliminar_criterio($id) {
        return $this->eliminar_por_id(self::TABLA_CRITERIO, "id_criterio", $id);
    }

    public function agendar_supervision($id_horario, $fecha) {
        $arg = new PreparedStatmentArgs();
        $arg->add("i", $id_horario);
        $arg->add("s", $fecha);
        $esOperacionCompleta = $this->ejecutar_instruccion_preparada("CALL agendar_supervision(?, ?)", $arg);
        return Util::enum($esOperacionCompleta ? "Docente agendado" : "Se encontró un conflicto de horario con otro docente agendado", !$esOperacionCompleta);
    }

    public function recuperar_agenda_por_fecha($fecha, $id_coordinador, $id_plantel, $id_carrera) {
        $arg = new PreparedStatmentArgs();
        $arg->add("s", $fecha);
        $arg->add("i", $id_coordinador);
        $arg->add("i", $id_carrera);
        $arg->add("i", $id_plantel);
        return $this->ejecutar_instruccion_prep_result("CALL recuperar_agenda_por_fecha(?, ?, ?, ?)", $arg);
    }

    public function eliminar_horario_agendado($id_horario) {
        return $this->eliminar_por_id("supervision_agenda", "id_horario", $id_horario);
    }

    public function guardar_supervision(Supervision $supervision) {
        $prep = new PreparedStatmentArgs();
        $prep->add("i", $supervision->get_id_agenda());
        $prep->add("s", $supervision->get_fecha());
        $prep->add("s", $supervision->get_tema());
        $prep->add("s", $supervision->get_conclusion_general() ?? "");
        $prep->add("s", json_encode($supervision->get_criterios_contables()));
        $prep->add("s", json_encode($supervision->get_criterios_no_contables()));
        $prep->add("s", $supervision->get_contrasenia());
        return $this->ejecutar_instruccion_preparada(self::GUARDAR_SUPERVISION, $prep);
    }

    public function recuperar_id_agenda_por_id_supervision($id_supervision) {
        $prep = new PreparedStatmentArgs();
        $prep->add("i", $id_supervision);
        $rs = $this->ejecutar_instruccion_prep_result("SELECT id_agenda FROM consultar_supervision WHERE id_supervision = ?", $prep);
        return $rs[0]["id_agenda"] ?? -1;
    }

    public function obtener_detalles_supervision($id_agenda) {
        $prep = new PreparedStatmentArgs();
        $prep->add("i", $id_agenda);
        return $this->ejecutar_instruccion_prep_result(self::RECUPERAR_SUPERVISION_REALIZADA_CRITERIOS, $prep);
    }

    public function recuperar_supervision($id_agenda) {
        $prep = new PreparedStatmentArgs();
        $prep->add("i", $id_agenda);
        $result = $this->ejecutar_instruccion_prep_result(self::RECUPERAR_SUPERVISION, $prep);
        return $result[0] ?? [];
    }

    public function obtener_agenda_general($id_coordinador, $fecha) {
        $prep = new PreparedStatmentArgs();
        $prep->add("i", $id_coordinador);
        if (empty($fecha)) {
            $fechaInstruccion = "";
        } else {
            $fechaInstruccion = " AND fecha = ?";
            $prep->add("s", $fecha);
        }
        return $this->ejecutar_instruccion_prep_result(str_replace("[WHERE]", $fechaInstruccion, self::RECUPERAR_AGENDA_GENERAL_POR_COORDINADOR), $prep);
    }

    public function actualizar_supervision($campo, $valor, $id_agenda, $tabla = "supervision_realizada") {
        return $this->ejecutar_instruccion("UPDATE $tabla SET $campo = '$valor' WHERE id_agenda = $id_agenda");
    }

    public function actualizar_cumplimiento_criterio_contable($id_supervision, $id_criterio, $es_criterio_cumplido) {
        $instruccion = "UPDATE supervision_realizada_contable_detalles SET criterio_cumplido = $es_criterio_cumplido WHERE id_supervision = $id_supervision AND id_criterio = $id_criterio";
        return $this->ejecutar_instruccion($instruccion);
    }
    public function actualizar_comentario_criterio_contable($id_supervision, $id_criterio, $comentario) {
        $instruccion = "UPDATE supervision_realizada_contable_detalles SET comentario = '$comentario' WHERE id_supervision = $id_supervision AND id_criterio = $id_criterio";
        return $this->ejecutar_instruccion($instruccion);
    }
}
