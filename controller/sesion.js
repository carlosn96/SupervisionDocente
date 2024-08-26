var usuario;
$(document).ready(function () {
    let root = getRootUrl();
    crearPeticion(root + "/controller/RevisorSesion.php", {
        case: "verificar_sesion"
    }, function (res) {
        //print(res);
        let rs = JSON.parse(res);
        if (!rs.sesion_activa) {
            redireccionar(root);
        } else {
            usuario = rs.usuario;
            $("#idUsuarioActual").val(usuario.tipo_usuario === "Coordinador" ? usuario.id_coordinador : usuario.id_usuario);
            $("#avatar").prop("src", usuario.avatar);
            ready();
        }
    });
});

function cerrarSesion() {
    crearPeticion("../../../../controller/RevisorSesion.php", {case: "cerrar_sesion"}, function () {
        location.reload();
    });
}
