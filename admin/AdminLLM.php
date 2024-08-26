<?php

class AdminLLM {

    private $dao;

    public function __construct() {
        $this->dao = new LLMDao();
    }

    public function generar_comentarios_supervision($id_usuario, $model, $criterios) {
        $prompt = "Genera una retroalimentación a un docente por su desempeño.";
        $APIKey = $this->dao->recuperar_api_key($id_usuario, $model);
        if (is_null($APIKey)) {
            return NO_API_KEY;
        } else {
            return $model === "gpt" ? Util::callOpenAI($prompt, $APIKey) : Util::callHuggingFace($prompt, $APIKey);
        }
    }
}
