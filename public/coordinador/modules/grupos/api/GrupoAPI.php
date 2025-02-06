<?php

include_once '../../../../../loader.php';

class GrupoAPI extends API {
    
    public function recuperar_turnos_grupos() {
        $this->enviar_respuesta([
            "turnos" => ($admin = new AdminGrupo())->recuperar_turnos(),
            "grados" => $admin->recuperar_grados()
        ]);
    }
    
    public function crear_grupo() {
        $this->enviar_resultado_operacion((new AdminGrupo)->crear_grupo($this->data));
    }
    
    public function actualizar_grupo() {
        $this->enviar_resultado_operacion((new AdminGrupo)->actualizar_grupo($this->data));
    }
    
    public function recuperar_grupos() {
        $carrera = $this->data["carrera"];
        $plantel = $this->data["plantel"];
        $this->enviar_respuesta((new AdminGrupo)->listar_grupos($carrera, $plantel));
    }
    
    public function eliminar() {
        $this->enviar_resultado_operacion((new AdminGrupo())->eliminar($this->data["id"]));
    }
    
    public function recuperar_grados() {
        $this->enviar_respuesta((new AdminGrupo())->recuperar_grados());
    }
}

Util::iniciar_api("GrupoAPI");
