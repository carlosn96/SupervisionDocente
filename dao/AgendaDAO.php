<?php

class AgendaDAO extends DAO {

    private const NOMBRE_TABLA = "evento_agenda";
    private const INSERTAR = "CALL insertar_evento(?, ?, ?, ?, ?, ?)";
    private const LISTAR = "SELECT * FROM listar_eventos";

    public function recuperar_listado($where = "") {
        return $this->ejecutar_instruccion(self::LISTAR . " " . $where)->fetch_all(MYSQLI_ASSOC);
    }

    public function listar_eventos_por_coordinador($id_coordinador) {
        return $this->recuperar_listado("WHERE id_coordinador = $id_coordinador");
    }

    public function guardar(Evento $evento) {
        $prep = new PreparedStatmentArgs();
        $prep->add("i", $evento->get_id_coordinador());
        $prep->add("s", $evento->get_nombre());
        $prep->add("s", $evento->get_fecha_hora_inicio());
        $prep->add("s", $evento->get_fecha_hora_fin());
        $prep->add("s", $evento->get_lugar());
        $prep->add("s", $evento->get_detalles());
        return $this->ejecutar_instruccion_preparada(self::INSERTAR, $prep);
    }

    public function eliminar($id) {
        return $this->eliminar_por_id(self::NOMBRE_TABLA, "id_evento", $id);
    }

    public function actualizar($id_evento, $campo, $valor) {
        $args = new PreparedStatmentArgs();
        $args->add("s", $valor);
        $args->add("i", $id_evento);
        return $this->actualizar_por_id(self::NOMBRE_TABLA, $campo, "id_evento", $args);
    }
}
