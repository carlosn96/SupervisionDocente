const urlAPI = "api/RegistroAPI.php";

$(document).ready(function () {

    $("#matricula").change(function () {
        const value = $(this).val();
        const $btnEnviar = $("#btnSubmit");

        // Validar si la matrícula tiene espacios
        if (/\s/.test(value)) {
            $("#correo").val("");
            $btnEnviar.prop("disabled", true);
            return; // Salir de la función si hay espacios
        }

        // Si la matrícula está vacía, limpiamos el correo y deshabilitamos el botón
        if (value.length === 0) {
            $("#correo").val("");
            $btnEnviar.prop("disabled", true);
            return;
        }

        // Si la matrícula tiene un valor válido (sin espacios), realizar la llamada a la API
        crearPeticion(urlAPI, {case: "verificar_matricula_existe", data: "matricula=" + value}, (res) => {
            print(res); // Asegúrate de que 'print' sea una función válida para mostrar resultados

            // Si hay un error con la matrícula (ya existe o cualquier otro error)
            if (res.es_valor_error) {
                mostrarMensajeAdvertencia(res.mensaje, false);
                $("#correo").val("");
                $btnEnviar.prop("disabled", true); // Deshabilitar botón de enviar
            } else {
                // Si la matrícula es válida, asignamos el correo
                $("#correo").val(value + "@universidad-une.com");
                $btnEnviar.prop("disabled", false); // Habilitar botón de enviar
            }
        }, "JSON");
    });

    $.validator.addMethod("noSpaces", function (value, element) {
        return this.optional(element) || !/\s/.test(value); // Verifica que no haya espacios
    }, "No se permiten espacios.");

    $("#registroForm").validate({
        rules: {
            contrasenia: {
                minlength: 6 // Asegura que la contraseña tenga al menos 6 caracteres
            },
            contraseniaConfirma: {
                minlength: 6, // La confirmación también debe tener al menos 6 caracteres
                equalTo: "#contrasenia" // Verifica que la confirmación coincida con la contraseña
            },
            matricula: {
                noSpaces: true   // Usa la regla personalizada 'noSpaces'
            }
        },
        messages: {
            contrasenia: {
                required: "Por favor ingresa una contraseña",
                minlength: "La contraseña debe tener al menos 6 caracteres"
            },
            contraseniaConfirma: {
                required: "Por favor confirma tu contraseña",
                minlength: "La confirmación de la contraseña debe tener al menos 6 caracteres",
                equalTo: "Las contraseñas no coinciden"
            },
            matricula: {
                required: "Por favor ingresa tu matrícula",
                noSpaces: "La matrícula no puede contener espacios"
            }
        }
    });

    $("#registroForm").submit(function (e) {
        e.preventDefault();
        const $spinner = $("#spinner");
        const $btnSubmit = $("#btnSubmit");
        const $form = $(this);
        e.preventDefault();
        $spinner.removeAttr("hidden");
        $btnSubmit.prop("disabled", true);
        if ($form.valid()) {
            crearPeticion(urlAPI, {case: "pre_registro", data: $form.serialize()}, function (res) {
                $spinner.attr("hidden", true);
                $btnSubmit.prop("disabled", false);
                if (res.status === true) {
                    redireccionar("../registroVerificarCorreo");
                } else {
                    mostrarMensajeError(res.info, false);
                }
            }, "json");
        }
    });
});

