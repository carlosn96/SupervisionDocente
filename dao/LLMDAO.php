<?php

class LLMDAO extends DAO {

    public function recuperar_api_key($id_usuario, $model) {
        return (new ConfigUsuarioDAO())->recuperar_api_key($id_usuario, $model);
    }

}
