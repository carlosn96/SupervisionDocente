<?php

class AdminPlantel {

    private $dao;

    public function __construct() {
        $this->dao = new PlantelDAO();
    }

    private function construir_listado(array $tuplas) {
        $lista = array();
        foreach ($tuplas as $tupla) {
            array_push($lista, $this->construir_plantel($tupla)->to_array());
        }
        return $lista;
    }

    public function recuperar_listado() {
        return $this->construir_listado($this->dao->recuperar_listado(""));
    }
    
    public function recuperar_plantel_id($id) {
        return $this->dao->recuperar_listado("WHERE id_plantel=$id")[0];
    }

    public function recuperar_listado_por_carrera($id_carrera) {
        return $this->construir_listado($this->dao->recuperar_listado_por_carrera($id_carrera));
    }

    function agregar($nombre): bool {
        try {
            return $this->dao->agregar(new Plantel($nombre));
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    function agregar_carrera_en_plantel(Carrera &$carrera) {
        return $this->dao->agregar_carrera_en_plantel($carrera);
    }

    function eliminar_carrera_de_plantel(Carrera &$carrera) {
        return $this->dao->eliminar_carrera_de_plantel($carrera);
    }

    private function construir_plantel($tupla): Plantel {
        return new Plantel($tupla["nombre"], $tupla["id_plantel"]);
    }

    public function guardar_configuracion_carrera_plantel($id_coord, $id_carrera, $id_plantel) {
        return $this->dao->guardar_configuracion_carrera_plantel($id_coord, $id_carrera, $id_plantel);
    }

    public function recuperar_configuracion_carrera_plantel($id_coord) {
        return $this->dao->recuperar_configuracion_carrera_plantel($id_coord);
    }

    public function editar(Plantel $plantel) {
        return $this->dao->editar($plantel);
    }

    public function eliminar($plantel) {
        return $this->dao->eliminar($plantel);
    }

}
