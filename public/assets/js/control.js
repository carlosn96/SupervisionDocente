
$(document).ready(function () {
    verificar_sesion();
});


function cerrarSesion() {
    crearPeticion("../../controller/RevisorSesion.php", {"case": "cerrar_sesion"}, function (res) {
        if (JSON.parse(res).sesion_cerrada) {
            refresh();
        }
    });
}

function verificar_sesion() {
    crearPeticion("../../controller/RevisorSesion.php", {"case": "verificar_sesion"}, function (rs) {
        //print(rs);
        let res = JSON.parse(rs);
        if (res.sesion_activa && window.location.pathname.endsWith("public/index/")) {
            redireccionar("../" + res.url);
        } else if (!res.sesion_activa && !window.location.pathname.endsWith("public/index/")) {
            redireccionar("../index");
        }
    });
}

