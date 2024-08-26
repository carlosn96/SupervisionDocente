
function ready() {
    ajustarCampos();
    ajustarImagenPerfil();
    enviarFormulario("#configuracionForm", "api/ConfigurarPerfilAPI.php", "configurar_perfil");

}

function ajustarCampos() {
    let coordinador = usuario;
    $('#profileName').text(`${coordinador.nombre} ${coordinador.apellidos}`);
    $('#profileEmail').text(`Correo electrónico actual: ${coordinador.correo_electronico}`);
    $('#telefono').val(coordinador.telefono);
    $('#btnActualizarTelefono').click(actualizarCampo);
    $('#nombre').val(coordinador.nombre);
    $('#apellidos').val(coordinador.apellidos);
    $('#btnActualizarNombre').click(actualizarCampo);
    $('#fecha_nacimiento').val(coordinador.fecha_nacimiento);
    $('#btnActualizarFechaNacimiento').click(actualizarCampo);
    $('#genero').val(coordinador.genero);
    $('#btnActualizarGenero').click(actualizarCampo);
    $('#actualizarCorreo').change(function () {
        $('#correo').prop('disabled', !this.checked);
    });
    $('#correo').change(function () {
        var correo = $(this).val();
        var formatoIncorrecto = correo.includes(' ');
        if (formatoIncorrecto) {
            $(this).addClass('is-invalid');
            $('#correoFeedback').text('El correo no contienen el formato correcto.'); // Mostrar mensaje de error
        } else {
            $(this).removeClass('is-invalid').addClass('is-valid'); // Quitar clase de error si es válida
            $('#correoFeedback').text(''); // Limpiar mensaje de error
        }
        $('#btnGuardar').prop('disabled', formatoIncorrecto);
    });

    $('#actualizarContrasena').change(function () {
        $('#newPassword, #confirmPassword').prop('disabled', !this.checked);
    });
    $('#newPassword, #confirmPassword').change(function () {
        var newPassword = $('#newPassword').val();
        var confirmPassword = $('#confirmPassword').val();
        var passwordMismatch = newPassword !== confirmPassword;
        if (passwordMismatch) {
            $('#confirmPassword').addClass('is-invalid');
            $('#confirmPasswordFeedback').text('Las contraseñas no coinciden.');
        } else {
            $('#confirmPassword').removeClass('is-invalid').addClass('is-valid');
            $('#confirmPasswordFeedback').text('');
        }
        $('#btnGuardar').prop('disabled', passwordMismatch);
    });
}

function ajustarImagenPerfil() {
    let $img = $("#imgPerfil");
    let $uploadImage = $('#uploadImage');
    $img.prop("src", $img.prop("src") + usuario.avatar);
    $img.on("click", function () {
        $uploadImage.click();
    });
    $uploadImage.on('change', function (event) {
        if (event.target.files && event.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var imgData = e.target.result;
                $img.attr('src', imgData);
                crearPeticion('api/ConfigurarPerfilAPI.php', {
                    case: "actualizar_imagen",
                    data: "img=" + imgData
                }, mostrarMensajeResultado, "json");
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    });
}


function actualizarCampo() {
    let data = "";
    $.each($(this).attr("aria-controls").split(","), function (i, e) {
        data += (e + "=" + $("#" + e).val()) + "&";
    });
    crearPeticion("api/ConfigurarPerfilAPI.php", {case: "actualizar_campo", data: data});
}