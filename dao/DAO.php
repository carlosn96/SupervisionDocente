<?php

class DAO {

    //public const CODIGO_ERROR_CAMPO_DUPLICADO = 1062; 

    private $conexion;

    function __construct() {
        $this->conexion = new Conexion();
    }

    function get_conexion(): Conexion {
        return $this->conexion;
    }

    private function abrir_conexion() {
        $this->conexion->crear_conexion();
    }

    protected function cerrar_conexion() {
        $this->conexion->cerrar_conexion();
    }

    protected function ejecutar_instruccion($instruccion) {
        $this->abrir_conexion();
        $resultado = $this->conexion->ejecutar_instruccion($instruccion);
        return $resultado;
    }

    /**
     * Regresa una tupla buscando por ID
     *
     * @param string $instruccion Sentencia SQL a ejecutar para extraer la tupla
     * @param int $id_registro El id (primary key) de la tupla a extraer
     * @return El resultado de fetch_assoc
     * @see fetch_assoc()
     */
    protected function select_por_id($instruccion, $id_registro) {
        $stat = $this->preparar_instruccion($instruccion);
        $stat->bind_param("i", $id_registro);
        $stat->execute();
        return $stat->get_result()->fetch_assoc();
    }

    /**
     * Regresa un conjunto de tuplas buscando por ID o por una columna de tipo INT
     *
     * @param string $instruccion Sentencia SQL a ejecutar para extraer las tuplas
     * @param int $id_registro La columna de tipo INT de la tupla a extraer
     * @return El resultado de fetch_all(MYSQLI_ASSOC)
     * @see fetch_all()
     */
    protected function select_all_por_id($instruccion, $id_registro) {
        $stat = $this->preparar_instruccion($instruccion);
        $stat->bind_param("i", $id_registro);
        $stat->execute();
        return $stat->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function select_por_campos_especificos($seleccion, $tabla, $where, $fetchassoc = false) {
        $result = $this->ejecutar_instruccion("SELECT " . $seleccion . " FROM " . $tabla . " " . $where);
        if ($result) {
            return $fetchassoc ? $result->fetch_all(MYSQLI_ASSOC) : $result->fetch_all();
        } else {
            return array();
        }
    }

    /**
     * 
     * @param string $tabla nombre de la tabla a buscar
     * @param string $campoBusqueda de la forma: nombre_campo = cadena_busqueda
     */
    protected function verificar_campo_existente($tabla, $campoBusqueda) {
        return $this->recuperar_count_all($tabla, $campoBusqueda);
    }

    protected function eliminar_por_id($tabla, $columna, $id) {
        $instruccionDelete = "DELETE FROM " . $tabla . " WHERE " . $columna . " = ?";
        $pre = $this->preparar_instruccion($instruccionDelete);
        $pre->bind_param("i", $id);
        return $pre->execute();
    }

    protected function actualizar_por_id($tabla, $columna, $idUpdate, $args, $show_info = false) {
        $instruccion = "UPDATE " . $tabla . " SET " . $columna . " = ? WHERE $idUpdate = ?";
        return $this->ejecutar_instruccion_preparada($instruccion, $args, $show_info);
        // return $$this->ejecutarInstruccion($instruccion);
    }

    protected function actualizar_multiple($tabla, $columnas, $idUpdate, $args) {
        $instruccion = "UPDATE " . $tabla . " SET " . $columnas . " WHERE $idUpdate = ?";
        //var_dump($instruccion);
        return $this->ejecutar_instruccion_preparada($instruccion, $args);
        // return $$this->ejecutarInstruccion($instruccion);
    }

    protected function preparar_instruccion($instruccion) {
        $this->abrir_conexion();
        $stmt = $this->conexion->preparar_instruccion();
        $stmt->prepare($instruccion);
        return $stmt;
    }

    protected function ejecutar_instruccion_preparada($instruccion, PreparedStatmentArgs $args, $show_info = false) {
        $stat = $this->preparar_instruccion($instruccion);
        $args->compile($stat);
        $result = $stat->execute();
        return $show_info ? $stat : $result;
    }

    protected function ejecutar_instruccion_prep_result($instruccion, PreparedStatmentArgs $args) {
        $stat = $this->preparar_instruccion($instruccion);
        $args->compile($stat);
        $stat->execute();
        return $stat->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    protected function obtener_id_autogenerado() {
        return $this->conexion->obtener_id_autogenerado();
    }

    protected function obtener_ultimo_insertado($id, $tabla) {
        return $this->ejecutar_instruccion("SELECT MAX($id) FROM $tabla")->fetch_row()[0];
    }

    protected function extraer_id_tupla($nombreID, $campoBusqueda, $valorCampoBusqueda, $tabla) {
        return $this->ejecutar_instruccion("SELECT $nombreID FROM $tabla WHERE $campoBusqueda = $valorCampoBusqueda")
                        ->fetch_row()[0];
    }

    protected function recuperar_count_all($tabla, $where = null) {
        $sql = "SELECT COUNT(*) FROM $tabla " . ( is_empty($where) ? "" : " WHERE $where");
        $res = $this->ejecutar_instruccion($sql);
        return $res->fetch_array()[0];
    }

    protected function get_error() {
        return $this->conexion->error_info();
    }

    protected function get_affected_rows() {
        return $this->conexion->affected_rows();
    }

    /* protected function actualizarListadoElementos($consultaIdTabla, $idBusqueda, $idFiltro, $tablaLista, $listado) {
      $stat = $this->prepararInstruccion($consultaIdTabla);
      $cuentaEjecuciones = 0;
      $stat->bind_param("i", $idBusqueda);
      if ($stat->execute()) {
      $idTablaEnlace = $stat->get_result()->fetch_array()[0];
      $this->ejecutarInstruccion("DELETE FROM $tablaLista WHERE $idFiltro = $idTablaEnlace");
      $stat = $this->prepararInstruccion("INSERT INTO $tablaLista VALUES(?, ?)");
      foreach ($listado as $idRegistro) {
      $stat->bind_param("ii", $idTablaEnlace, $idRegistro);
      $cuentaEjecuciones += $stat->execute();
      }
      }
      return count($listado) == $cuentaEjecuciones;
      } */
}
