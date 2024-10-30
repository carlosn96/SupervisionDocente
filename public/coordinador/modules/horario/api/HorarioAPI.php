<?php

include_once '../../../../../loader.php';

class HorarioAPI extends API {

    private const GRUPO = "Grupo";
    private const DOCENTE = "Docente";

    function obtener_lista_elementos() {
        $tipo = $this->data["tipoHorario"];
        $carrera = $this->data["carrera"];
        $plantel = $this->data["plantel"];
        $this->enviar_respuesta([
            "tabla_horario" => $this->recuperar_listado_grupo_docente($tipo, $carrera, $plantel),
            "docentes" => (new AdminDocente)->obtener_docentes_materias($carrera, $plantel)
        ]);
    }

    private function recuperar_listado_grupo_docente($tipo, $carrera, $plantel) {
        switch ($tipo) {
            case "grupo":
                $lista = [
                    self::GRUPO => array_map(function ($grupo) {
                        return ["text" => $grupo['grupo'], "id" => $grupo['grupo']];
                    }, (new AdminMateria())->listar_grupos($carrera, $plantel))
                ];
                break;
            case "profesor":
                $lista = [
                    self::DOCENTE => array_map(function ($docente) {
                        return [
                    "text" => $docente["nombre"] . " " . $docente["apellidos"],
                    "id" => $docente['id_docente']
                        ];
                    }, array_values((new AdminDocente())->obtener_docentes_materias($carrera, $plantel)))
                ];
                break;
        }
        return $lista;
    }

    function recuperar_horario() {
        $carrera = $this->data["carrera"];
        $plantel = $this->data["plantel"];
        $id = $this->data["id"];
        $tipo = $this->data["tipo"];
        $rs = (new AdminDocente())->obtener_horario($tipo, $id, $carrera, $plantel);
        $horario = [
            "tipo" => $tipo,
            "id" => $id,
            "horario" => $rs
        ];
        $horario["docente"] = $tipo === self::DOCENTE ? $rs[0]["docente"] : null;
        Sesion::setInfoTemporal("horario", $horario);
    }

    function consultar_disponibilidad() {
        $dia = $this->data["diaSemana"];
        $hora = $this->data["hora"];
        $carrera = $this->data["carrera"];
        $plantel = $this->data["plantel"];
        $this->enviar_respuesta(
                (new AdminDocente())->consultar_disponibilidad($dia, $hora, $carrera, $plantel)
        );
    }
}

Util::iniciar_api("HorarioAPI");
