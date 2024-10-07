<?php

class Util {

    public static function generarCadenaAleatoria($len = 5) {
        return substr(bin2hex(random_bytes($len)), 0, $len);
    }

    public static function callOpenAI($prompt, $APIKey) {
        $data = [
            'model' => 'gpt-3.5-turbo',
            'prompt' => $prompt,
        ];
        $ch = curl_init('https://api.openai.com/v1/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $APIKey,
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return Util::enum('Error en la solicitud: ' . curl_error($ch), true);
        }
        curl_close($ch);
        $responseData = json_decode($response, true);
        if (isset($responseData['error'])) {
            return Util::enum('Error en la respuesta de OpenAI: ' . $responseData['error']['message'], true);
        }
        return $responseData;
    }

    public static function callHuggingFace($prompt, $APIKey) {
        $repo_id = "bigscience/bloom";
        $data = array(
            'inputs' => $prompt,
                //"task"=>"text-generation"
        );
        $dataString = json_encode($data);
        $ch = curl_init("https://api-inference.huggingface.co/models/" . $repo_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $APIKey,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($dataString)
        ));
        $response = curl_exec($ch);
        if ($response === FALSE) {
            return Util::enum('Error en la solicitud: ' . curl_error($ch), true);
        }
        curl_close($ch);
        return json_decode($response, true);
    }

    public static function redirigir($url, $permanent = false) {
        echo $url;
        if (headers_sent()) {
            // echo "<script>window.location = '$url';</script>";
            //echo "<script type='text/javascript'>window.location.replace('$url');</script>";
        } else {
            //header('Location: ' . $url, true, $permanent ? 301 : 302);
        }
    }

    public static function separarCamposFormulario($data) {
        $campos = array();
        parse_str($data, $campos);
        return $campos;
    }

    public static function print($val) {
        echo orint_r($val);
    }

    public static function enum($mensaje, $esError): array {
        return [
            "mensaje" => $mensaje,
            "es_valor_error" => $esError,
        ];
    }

    public static function encriptar_contrasenia($contrasenia) {
        return password_hash($contrasenia, PASSWORD_DEFAULT);
    }

    public static function verificar_contrasenia($contrasenia_ingresada, $hash) {
        return password_verify($contrasenia_ingresada, $hash);
    }

    public static function iniciar_api($nombre) {
        (new $nombre($_POST["case"], isset($_POST["data"]) ? $_POST["data"] : ""));
    }

    public static function convertToMySQLDateTime($dateString) {
        return empty($dateString) ? null : (new DateTime($dateString))->format('Y-m-d H:i:s');
    }
}

define("NO_API_KEY", Util::enum("No se ha establecido el API KEY", true));

define("ROOT_APP", __DIR__ . DIRECTORY_SEPARATOR);
define("INDEX", "FundacionCardenalGaribiRivera2024" . DIRECTORY_SEPARATOR);

define("ERROR_INSERTAR", Util::enum('Ha ocurrido un error al intentar almacenar la información proporcionada. Verifique los datos', true));
define("REGISTRO_COMPLETO", Util::enum('Registro completo', false));
define("OPERACION_COMPLETA", Util::enum("Operacion completa", false));
define("ACTUALIZACION_COMPLETA", Util::enum("La actualización de la información se ha realizado correctamente", false));
define("OPERACION_INCOMPLETA", Util::enum("La operación no se pudo completar con éxito", true));
define("USUARIO_YA_EXISTE", Util::enum("El número móvil ingresado ya se encuentra registrado", true));
define("DATO_YA_EXISTE", Util::enum("La información ingresada ya existe", true));

define("ACCESO_DENEGADO", Util::enum('No tienes permiso de acceso', true));
define("ERROR_CLAVE", Util::enum('Verifique la clave ingresada.', true));
define("ERROR_ACCESO_USUARIO", Util::enum('Error de acceso: verifica el usuario ingresado.', true));
define("ERROR_ACCESO_PASSWORD", Util::enum('Error de acceso: contraseña incorrecta.', true));
define("ERROR_CONTRASENIA_ACTUAL", Util::enum('Contraseña actual incorrecta', true));
define("ERROR_CONTRASENIA_NUEVA", Util::enum('Las contraseñas no coinciden: verifica la contraseña nueva', true));
define("ERROR_DESCONOCIDO", Util::enum('Error desconocido', true));
define("ERROR_SEGURIDAD", Util::enum('La respuesta de seguridad no es correcta', true));
define("ERROR_FORMATO", Util::enum('Ingresa usuario válido', true));
define("UNDEFINED", Util::enum('Sin información', true));
define("NO_ERROR", Util::enum('', false));

define("NO_DATA_ERROR", Util::enum("No existe información disponible", true));
define("NO_COMPLETE_DATA_ERROR", Util::enum("La información solicitada no está completa", true));

spl_autoload_register(function ($clase) {
    $directorios = ["admin", "dao", "dao/util", "modelo", "controller"];
    foreach ($directorios as $directorio) {
        if (buscar(ROOT_APP . $directorio, $clase)) {
            return;
        }
    }
});

function buscar($directorio, $clase) {
    $archivo = $directorio . DIRECTORY_SEPARATOR . $clase . '.php';
    if (file_exists($archivo)) {
        require_once($archivo);
        return true;
    }
    return false;
}
