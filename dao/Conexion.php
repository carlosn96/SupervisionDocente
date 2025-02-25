<?php

class Conexion {

//    private const SERVIDOR = "154.56.47.204";
//    private const USUARIO = "u487588057_developer";
//    private const CONTRASENIA = "D3v3lop3r1996";
//    private const BD = "u487588057_supervision_dp";
//    private const SERVIDOR = "193.203.166.238";
    private const SERVIDOR = "localhost";
    private const USUARIO = "u487588057_supervision";
    private const CONTRASENIA = "Ch@rly1996";
    private const BD = "u487588057_sup_docente";

    private $conexion;
    private $conexion_abierta = false;

    public function __construct() {
        $this->crear_conexion();
    }

    public function crear_conexion($servidor = self::SERVIDOR,
            $usuario = self::USUARIO, $contrasenia = self::CONTRASENIA, $bd = self::BD) {
        if ($this->conexion_abierta) {
            $this->cerrar_conexion();
        }
        try {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $this->conexion = new mysqli($servidor, $usuario, $contrasenia, $bd);
            $this->conexion->set_charset("utf8");
            $this->conexion_abierta = true;
        } catch (mysqli_sql_exception $e) {
            echo json_encode(Util::enum("Error: " . $e->getMessage(), true));
            exit();
        }
    }

    public function cerrar_conexion() {
        if ($this->conexion_abierta) {
            $this->conexion->close();
            $this->conexion = null;
            $this->conexion_abierta = false;
        }
    }

    public function es_conexion_nueva() {
        return !$this->conexion_abierta;
    }

    public function is_connected() {
        return $this->conexion !== null && $this->conexion->ping();
    }

    public function ejecutar_instruccion($instruccion) {
        return $this->conexion->query($instruccion);
    }

    public function preparar_instruccion() {
        $stat = $this->conexion->stmt_init();
        return $stat;
    }

    public function error_info() {
        return $this->conexion->error;
    }

    public function affected_rows() {
        return $this->conexion->affected_rows;
    }

    public function obtener_id_autogenerado() {
        return $this->conexion->insert_id;
    }
}
