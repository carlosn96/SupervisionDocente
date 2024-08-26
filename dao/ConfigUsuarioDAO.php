<?php

class ConfigUsuarioDAO extends DAO {

    const ACTUALIZAR_CONFIG_USUARIO = "CALL actualizar_config_usuario (?, ?, ?, ?, ?)";

    private function actualizar_config_usuario($id_usuario, $key_gpt = null, $key_huggingface = null, $id_plantel_actual = null, $id_carrera_actual = null) {
        $prep = new PreparedStatmentArgs();
        $prep->add("i", $id_usuario);
        $prep->add("s", $key_gpt);
        $prep->add("s", $key_huggingface);
        $prep->add("i", $id_plantel_actual);
        $prep->add("i", $id_carrera_actual);
        return $this->ejecutar_instruccion_preparada(self::ACTUALIZAR_CONFIG_USUARIO, $prep);
    }

    public function recuperar_api_key($id_usuario, $model) {
        $rs = $this->ejecutar_instruccion("SELECT api_key_$model FROM config_usuario WHERE id_usuario = $id_usuario");
        return $rs ? $rs->fetch_assoc()["api_key_$model"] : null;
    }

    public function guardar_configuracion_carrera_plantel($id_coord, $id_carrera, $id_plantel) {
        $id_usuario = $this->extraer_id_tupla("id_usuario", "id_coordinador", $id_coord, "coordinador");
        return $this->actualizar_config_usuario($id_usuario, id_carrera_actual: $id_carrera, id_plantel_actual: $id_plantel);
    }

    public function recuperar_configuracion_carrera_plantel($id_coord) {
        $id_usuario = $this->extraer_id_tupla("id_usuario", "id_coordinador", $id_coord, "coordinador");
        $prep = new PreparedStatmentArgs();
        $prep->add("i", $id_usuario);
        if (($rs = $this->ejecutar_instruccion_prep_result("SELECT id_carrera_actual, id_plantel_actual FROM config_usuario WHERE id_usuario = ?", $prep))) {
            return $rs[0];
        } else {
            return ["id_plantel_actual" => null, "id_carrera_actual" => null];
        }
    }
}
