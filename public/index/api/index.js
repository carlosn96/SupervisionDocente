$(document).ready(function () {
    revisarSesion();
    enviarFormulario("#iniciar_sesion_form", "api/IndexAPI.php", "iniciar_sesion", function(res){
        if(res.es_valor_error) {
            mostrarMensajeError(res.mensaje);
        } else {
            refresh();
        }
    });
});


function revisarSesion() {
    crearPeticion(getRootUrl() + "/controller/RevisorSesion.php", {
        case: "verificar_sesion"
    }, function (res) {
        let rs = JSON.parse(res);
        if (rs.sesion_activa) {
            redireccionar(rs.url);
        }
    });
}