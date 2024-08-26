<?php

include_once '../../../../../loader.php';

class DocenteAPI extends API {

    public function recuperar_docentes() {
        $this->enviar_respuesta((new AdminDocente())->obtener_docentes_materias(
                        $this->data["id_carrera"],
                        $this->data["id_plantel"]));
    }

    public function guardar_docente_materias() {
        $this->data["correo_electronico"] = $this->data["correo"] . "@" . $this->data["dominio"];
        $this->data["id_coordinador"] = Sesion::info()["usuario"]->get_id_coordinador();
        $this->enviar_resultado_operacion((new AdminDocente)->guardar_docente_materias($this->data));
    }

    function eliminar() {
        $this->enviar_resultado_operacion((new AdminDocente())->eliminar($this->data["id"]));
    }

    function eliminar_horario() {
        $this->enviar_resultado_operacion((new AdminMateria())->eliminar_horario($this->data["id"]));
    }

    function actualizar_docente() {
        $this->enviar_resultado_operacion((new AdminDocente())->actualizar($this->data));
    }

    function recuperar_materias() {
        $this->enviar_respuesta((new AdminDocente())->recuperar_materias($this->data["idDocente"]));
    }

    function actualizar_nombre_materia() {
        $this->enviar_resultado_operacion((new AdminMateria())->actualizar_nombre_materia($this->data["id"], $this->data["val"]));
    }

    function actualizar_grupo_materia() {
        $this->enviar_resultado_operacion((new AdminMateria())->actualizar_grupo_materia($this->data["id"], $this->data["val"]));
    }

    function actualizar_horario() {
        $this->enviar_resultado_operacion((new AdminMateria())->actualizar_horario($this->data));
    }
    
    function agregar_horario() {
        $this->enviar_resultado_operacion((new AdminMateria())->agregar_horario($this->data));
    }
}

Util::iniciar_api("DocenteAPI");
