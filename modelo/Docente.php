
<?php

class Docente {

    use Entidad;

    private $id_docente;
    private $nombre;
    private $apellidos;
    private $correo_electronico;
    private $perfil_profesional;
    private $materias;
    private $id_coordinador;

    public function __construct($nombre, $apellidos, $correo_electronico,
            $perfil_profesional, $materias, $id_coordinador, $id_docente = "") {
        $this->id_docente = $id_docente;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->materias = $materias;
        $this->correo_electronico = $correo_electronico;
        $this->perfil_profesional = $perfil_profesional;
        $this->id_coordinador = $id_coordinador;
    }

    public function get_id_docente() {
        return $this->id_docente;
    }

    public function get_nombre() {
        return $this->nombre;
    }

    public function get_apellidos() {
        return $this->apellidos;
    }

    public function get_correo_electronico() {
        return $this->correo_electronico;
    }

    public function get_perfil_profesional() {
        return $this->perfil_profesional;
    }

    public function get_id_coordinador() {
        return $this->id_coordinador;
    }

    public function set_id_docente($id_docente): void {
        $this->id_docente = $id_docente;
    }

    public function set_nombre($nombre): void {
        $this->nombre = $nombre;
    }

    public function set_apellidos($apellidos): void {
        $this->apellidos = $apellidos;
    }

    public function set_correo_electronico($correo_electronico): void {
        $this->correo_electronico = $correo_electronico;
    }

    public function set_perfil_profesional($perfil_profesional): void {
        $this->perfil_profesional = $perfil_profesional;
    }

    public function set_id_coordinador($id_coordinador): void {
        $this->id_coordinador = $id_coordinador;
    }

    public function array() {
        $materias = [];
        foreach ($this->materias as $m) {
            $materias[] = $m->to_array();
        }
        $array = $this->to_array();
        $array["materias"] = $materias;
        return $array;
    }
}
