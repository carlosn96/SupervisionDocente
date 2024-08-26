<?php

class UsuarioDAO extends DAO {

    private const NOMBRE_TABLA = "usuario";
    private const INSERTA_NUEVO = "INSERT INTO " . self::NOMBRE_TABLA . " (tipo_usuario, nombre, apellidos, correo_electronico, contrasenia, avatar) values (?, ?, ?, ?, ?, ?)";
    private const BUSCAR_POR_CORREO = "SELECT * FROM " . self::NOMBRE_TABLA . " WHERE correo_electronico = ?";
    private const ACTUALIZAR_CORREO = "UPDATE " . self::NOMBRE_TABLA . " SET correo_electronico = ? WHERE id_usuario = ?";
    private const ACTUALIZAR_CONTRASENIA = "UPDATE " . self::NOMBRE_TABLA . " SET contrasenia = ? WHERE id_usuario = ?";
    private const ACTUALIZAR_FOTO_PERFIL = "UPDATE " . self::NOMBRE_TABLA . " SET avatar = ? WHERE id_usuario = ?";

    public function insertar_nuevo(Usuario &$usuario) {
        $args = new PreparedStatmentArgs();
        $args->add("s", $usuario->get_nombre());
        $args->add("s", $usuario->get_apellidos());
        $args->add("s", $usuario->get_contrasenia());
        $args->add("s", $usuario->get_correo_electronico());
        $args->add("s", $usuario->get_tipo_usuario());
        $args->add("s", $usuario->get_avatar());
        if ($this->ejecutar_instruccion_preparada(self::INSERTA_NUEVO, $args)) {
            $usuario->set_id_usuario($this->obtener_id_autogenerado());
            return true;
        }
        return false;
    }

    public function recuperar_por_correo(string $correo) {
        $args = new PreparedStatmentArgs();
        $args->add("s", $correo);
        $res = $this->ejecutar_instruccion_prep_result(self::BUSCAR_POR_CORREO, $args);
        return count($res) ? $res[0] : null;
    }

    public function actualizar_correo(Usuario $usuario, $correo) {
        $args = new PreparedStatmentArgs();
        $args->add("s", $correo);
        $args->add("i", $usuario->get_id_usuario());
        return $this->ejecutar_instruccion_preparada(self::ACTUALIZAR_CORREO, $args);
    }

    public function actualizar_contrasenia(Usuario $usuario, $contrasenia) {
        $args = new PreparedStatmentArgs();
        $args->add("s", $contrasenia);
        $args->add("i", $usuario->get_id_usuario());
        return $this->ejecutar_instruccion_preparada(self::ACTUALIZAR_CONTRASENIA, $args);
    }

    public function actualizar_foto_perfil(Usuario $usuario, $foto) {
        $args = new PreparedStatmentArgs();
        $args->add("s", $foto);
        $args->add("i", $usuario->get_id_usuario());
        return $this->ejecutar_instruccion_preparada(self::ACTUALIZAR_FOTO_PERFIL, $args);
    }

    public function actualizar_info_personal($data, $id_usuario) {
        $sql = "UPDATE " . self::NOMBRE_TABLA . " SET ";
        $sets = [];
        foreach ($data as $key => $val) {
            $sets[] = "$key = '$val'";
        }
        $sql .= implode(", ", $sets);
        $sql .= " WHERE id_usuario = $id_usuario";
        return $this->ejecutar_instruccion($sql);
    }

}
