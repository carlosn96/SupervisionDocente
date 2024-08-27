<?php

class Conexion {
    private const SERVIDOR = "154.56.47.204";
    private const USUARIO = "u487588057_supervision";
    private const CONTRASENIA = "Ch@rly1996";
    private const BD = "u487588057_sup_docente";

    private $conexion;
    private $conexion_abierta = false;
    
    public function __construct() {
        $this->crear_conexion();
    }

    public function crear_conexion($servidor = self::SERVIDOR, $usuario = self::USUARIO, $contrasenia = self::CONTRASENIA, $bd = self::BD) {
        if ($this->conexion_abierta) {
            $this->cerrar_conexion();
        }
        $this->conexion = new mysqli($servidor, $usuario, $contrasenia, $bd);
        if ($this->conexion->connect_errno) {
            die("Connection failed: " . $this->conexion->connect_error);
            exit();
        } else {
            $this->conexion->set_charset("utf8");
            $this->conexion_abierta = true;
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
