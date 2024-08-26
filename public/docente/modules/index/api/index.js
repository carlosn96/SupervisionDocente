$(document).ready(function () {
    enviarFormulario("#revisar_supervision_form", "api/IndexAPI.php", "consultar_supervision", function (res) {
        print(res);
        if ((Array.isArray(res) && res.length === 0) || res.info_agenda.length === 0) {
            mostrarMensajeInfo("No hay existe información relacionada con el número de expediente proporcionado");
        } else {
            redireccionar("../supervision");
        }
    });
});
