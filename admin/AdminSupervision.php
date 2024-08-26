<?php

class AdminSupervision {

    private const RUBRO_CONTABLE = "contables";
    private const RUBRO_NO_CONTABLE = "no_contables";

    private $dao;

    public function __construct() {
        $this->dao = new SupervisionDAO();
    }

    public function guardar_rubro_criterios(array $formulario) {
        $rubros = array();
        foreach ($formulario['rubro'] as $rubro_data) {
            $criterios = [];
            foreach ($rubro_data['criteria'] as $criterio_data) {
                $criterios[] = new CriterioSupervision($criterio_data['description']);
            }
            $rubros[] = new RubroSupervision($rubro_data['name'], $criterios);
        }
        return $this->dao->guardar_rubro_criterios($rubros);
    }

    public function recuperar_criterios_por_rubro() {
        $rubros = [];
        foreach ($this->dao->recuperar_criterios_por_rubro() as $tipo => $value) {
            foreach ($value as $r) {
                $rubros[$tipo][] = (new RubroSupervision($r["descripcion"], $this->generar_criterios($r["criterios"]), $r["id"]))->to_array();
            }
        }
        return $rubros;
    }

    public function generar_criterios($criterios) {
        $lista = [];
        foreach ($criterios as $criterio) {
            $lista[] = (new CriterioSupervision($criterio["descripcion"], $criterio["id"]))->to_array();
        }
        return $lista;
    }

    public function actualizar_descripcion_rubro($descripcion, $id) {
        return $this->dao->actualizar_descripcion_rubro($descripcion, $id);
    }

    public function actualizar_descripcion_criterio($descripcion, $id) {
        return $this->dao->actualizar_descripcion_criterio($descripcion, $id);
    }

    public function eliminar_rubro($id) {
        return $this->dao->eliminar_rubro($id);
    }

    public function eliminar_criterio($id) {
        return $this->dao->eliminar_criterio($id);
    }

    public function agendar_supervision($id_horario, $fecha) {
        return $this->dao->agendar_supervision($id_horario, $fecha);
    }
    
    public function obtener_agenda_general($id_coordinador, $fecha) {
        return $this->dao->obtener_agenda_general($id_coordinador, $fecha);
    }
    
    public function recuperar_agenda_por_fecha($fecha, $id_coordinador, $id_plantel, $id_carrera) {
        return $this->dao->recuperar_agenda_por_fecha($fecha, $id_coordinador, $id_plantel, $id_carrera);
    }

    public function eliminar_horario_agendado($id_horario) {
        return $this->dao->eliminar_horario_agendado($id_horario);
    }

    public function guardar_supervision($data) {
        $supervision = new Supervision($data['fecha'], $data['id_agenda'],
                $data["tema"], $data["conclusion_general"],
                $this->construir_criterios($data["rubros"]["contable"]),
                $this->construir_criterios($data["rubros"]["no_contable"]));
        return $this->dao->guardar_supervision($supervision);
    }

    private function construir_criterios($data) {
        $rubros = [];
        foreach ($data as $value) {
            $criterios = [];
            foreach ($value["criterios"] as $criterio) {
                $criterios[] = (new CriterioSupervision($criterio["label"],
                                $criterio["id"], filter_var($criterio["cumplido"], FILTER_VALIDATE_BOOLEAN),
                                $criterio["comentario"]))->to_array();
            }
            $rubros[] = (new RubroSupervision($value["rubro"], $criterios, $value['id_rubro']))->to_array();
        }
        return $rubros;
    }
    
    public function recuperar_supervision($id_agenda) {
        return [
            "detalles_criterios" => $this->obtener_detalles_supervision($id_agenda),
            "info_supervision"=> $this->dao->recuperar_supervision($id_agenda)
        ];
    }

    private function obtener_detalles_supervision($id_agenda) {
        $data = $this->dao->obtener_detalles_supervision($id_agenda);
        $rubros = [];

        // Agrupamos los criterios por id_rubro y tipo_criterio
        foreach ($data as $value) {
            $id_rubro = $value['id_rubro'];
            $rubro_descripcion = $value['rubro_descripcion'];
            $tipo_criterio = $value['tipo_criterio'];
            $criterio = [
                'label' => $value['criterio_descripcion'],
                'id' => $value['id_criterio'],
                'cumplido' => filter_var($value['criterio_cumplido'], FILTER_VALIDATE_BOOLEAN),
                'comentario' => $value['comentario']
            ];

            // Inicializamos el rubro si no existe
            if (!isset($rubros[$tipo_criterio][$id_rubro])) {
                $rubros[$tipo_criterio][$id_rubro] = [
                    'rubro' => $rubro_descripcion,
                    'id_rubro' => $id_rubro,
                    'criterios' => []
                ];
            }

            // Añadimos el criterio al rubro correspondiente
            $rubros[$tipo_criterio][$id_rubro]['criterios'][] = $criterio;
        }

        // Convertimos a los arrays que espera construir_criterios
        $rubros_contables = $rubros['contable'] ?? [];
        $rubros_no_contables = $rubros['no_contable'] ?? [];

        return [
            'contables' => $this->construir_criterios($rubros_contables),
            'no_contables' => $this->construir_criterios($rubros_no_contables)
        ];
    }
    
    function recuperar_id_agenda_por_id_supervision($id_supervision) {
        return $this->dao->recuperar_id_agenda_por_id_supervision($id_supervision);
    }
}
