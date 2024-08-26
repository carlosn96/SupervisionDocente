<?php

class Conexion {

    private const SERVIDOR = "localhost:3306";
    private const USUARIO = "root";
    private const CONTRASENIA = "";
    private const BD = "supervision_docente";

    private $conexion;
    
    public function __construct() {
        $this->crear_conexion();
    }

    public function crear_conexion($servidor = self::SERVIDOR, $usuario = self::USUARIO, $contrasenia = self::CONTRASENIA, $bd = self::BD) {
        $this->cerrar_conexion();
        $conexion = new mysqli($servidor, $usuario, $contrasenia, $bd);
        if ($conexion->connect_errno) {
            die("Connection failed: " . $conexion->connect_error);
            exit();
        } else {
            $conexion->set_charset("utf8");
        }
        $this->conexion = $conexion;
    }

    public function cerrar_conexion() {
        if ($this->conexion !== null) {
            $this->conexion->close();
            $this->conexion = null;
        }
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
