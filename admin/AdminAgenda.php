<?php

class AdminAgenda {

    private $dao;

    public function __construct() {
        $this->dao = new AgendaDAO();
    }

    private function construir_listado($rows) {
        $lista = [];
        foreach ($rows as $value) {
            $lista[] = $this->construir_evento($value)->to_array();
        }
        return $lista;
    }

    public function listar_eventos($id_coordinador) {
        return $this->construir_listado($this->dao->listar_eventos_por_coordinador($id_coordinador));
    }

    public function guardar($form) {
        return $this->dao->guardar($this->construir_evento($form));
    }
    
    public function actualizar($id_evento, $campo, $valor) {
        return $this->dao->actualizar($id_evento, $campo, $valor);
    }
    
    public function eliminar($id) {
        return $this->dao->eliminar($id);
    }

    private function construir_evento($form): Evento {
        $id_evento = $form["id"] ?? 0;
        $nombre = $form["nombreEvento"] ?? "";
        $fecha_hora_inicio = $form["fechaHoraInicioEvento"] ?? "";
        $fecha_hora_fin = $form["fechaHoraFinEvento"] ?? null;
        $lugar = $form["lugar"] ?? "";
        $detalles = $form["detalles"] ?? "";
        $id_coordinador = $form["id_coordinador"] ?? 0;
        return new Evento($id_evento, $id_coordinador, $nombre, $fecha_hora_inicio,
                $fecha_hora_fin, $lugar, $detalles);
    }
}
