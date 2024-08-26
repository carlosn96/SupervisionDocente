
$(document).ready(function () {
    $("#registrar_sudo_form").submit(function (e) {
        e.preventDefault();
        crearPeticion("api/SudoAPI.php", {"case": "guardar_administrador", "data": $(this).serialize()});
    });
});
