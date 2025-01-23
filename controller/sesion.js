var usuario;
$(document).ready(function () {
    let root = getRootUrl();
    let url = root + "controller/RevisorSesion.php";
    crearPeticion(url, {
        case: "verificar_sesion"
    }, function (res) {
        //print(res);
        let rs = JSON.parse(res);
        if (rs.sesion_activa) {
            usuario = rs.usuario;
            $("#idUsuarioActual").val(usuario.tipo_usuario === "Coordinador" ? usuario.id_coordinador : usuario.id_usuario);
            $("#avatar").prop("src", usuario.avatar);
            ready();
        } else {
            redireccionar(root);
        }
    });
});

function cerrarSesion() {
    crearPeticion("../../../../controller/RevisorSesion.php", {case: "cerrar_sesion"}, function () {
        location.reload();
    });
}
