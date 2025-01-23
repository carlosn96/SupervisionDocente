$(document).ready(function () {
    revisarSesion();
    $("#iniciar_sesion_form").submit(function (e) {
        const $spinner = $("#spinner");
        const $subBtn = $("#btnIniciarSesion");
        e.preventDefault();
        $spinner.removeAttr("hidden");
        $subBtn.prop("disabled", true);
        crearPeticion("api/IndexAPI.php", {case: "iniciar_sesion", data: $(this).serialize()}, function (res) {
            if (res.es_valor_error) {
                $spinner.attr("hidden", true);
                $subBtn.prop("disabled", false);
                mostrarMensajeError(res.mensaje);
            } else {
                refresh();
            }
        }, "json");
    });
});


function revisarSesion() {
    let url = getRootUrl() + "controller/RevisorSesion.php";
    crearPeticion(url, {
        case: "verificar_sesion"
    }, function (res) {
        let rs = JSON.parse(res);
        if (rs.sesion_activa) {
            redireccionar(rs.url);
        }
    });
}