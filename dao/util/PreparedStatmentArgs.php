<?php

class PreparedStatmentArgs {

    private const ALLOWED_TYPES = ["s", "i", "d", "b"];
    private const TYPE_INDEX = 0;
    private const DATA_INDEX = 1;

    private $data;
    private $blob;

    function __construct() {
        $this->clear();
    }

    function clear() {
        $this->data = array();
        $this->blob = array();
    }

    function add($type, $val) {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw new Exception("Tipo de dato no permitido");
        }
        array_push($this->data, [$type, $val]);
    }

    public function update($index, $value) {
        if (isset($this->data[$index])) {
            $this->data[$index][self::DATA_INDEX] = $value;
        }
    }

    function add_blob($data) {
        $this->blob[] = $data; // Añadir el blob directamente al array $blob
        $this->add("b", NULL); // Añadir un marcador para el blob al array $data
    }

    function get(): array {
        return $this->data;
    }

    public function compile(mysqli_stmt &$stmt) {
        $params = "";
        $data = [];
        foreach ($this->data as $key => $value) {
            $params .= $value[self::TYPE_INDEX];
            $data[] = $this->data[$key][self::DATA_INDEX];
        }
        $stmt->bind_param($params, ...$data);

        foreach ($this->blob as $key => $value) {
            $stmt->send_long_data($key, $value);
        }
    }

}
