<?php

class CoordinadorDAO extends DAO {

    private const NOMBRE_TABLA = "coordinador";
    private const LISTAR = "SELECT * FROM " . self::NOMBRE_TABLA;
    private const INSERTAR_NUEVO = "CALL insertar_coordinador( ?, ?, ?, ? , ?, ?, ?)";
    private const OBTENER_TIPOS_CARRERA = "SHOW COLUMNS FROM " . self::NOMBRE_TABLA . " LIKE 'tipo'";
    private const LISTAR_USUARIO_COORDINADOR = "SELECT * FROM coordinador_usuario";

    public function guardar(Coordinador $coordinador) {
        $args = new PreparedStatmentArgs();
        $args->add("s", $coordinador->get_tipo_usuario());
        $args->add("s", $coordinador->get_nombre());
        $args->add("s", $coordinador->get_apellidos());        
        $args->add("s", $coordinador->get_correo_electronico());
        $args->add("s", $coordinador->get_contrasenia());
        $args->add("s", $coordinador->get_avatar());
        $args->add("s", json_encode($coordinador->get_carreras_coordina()));
        return $this->ejecutar_instruccion_preparada(self::INSERTAR_NUEVO, $args);
    }

    public function recuperar_por_id($id) {
        return $this->ejecutar_instruccion(self::LISTAR . "_usuario WHERE id_coordinador = " . $id);
    }

    public function listar() {
        return $this->ejecutar_instruccion(self::LISTAR_USUARIO_COORDINADOR)->fetch_all(MYSQLI_ASSOC);
    }

    public function eliminar($id) {
        return $this->eliminar_por_id("usuario", "id_usuario", $id);
    }
}
