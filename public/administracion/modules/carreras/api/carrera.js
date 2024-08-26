let apiURL = "api/CarreraAPI.php";

$(document).ready(function () {
    recuperarCampos();
    $("#carreraForm").submit(function (e) {
        e.preventDefault();
        if ($('#grupoPlanteles input[name="planteles[]"]:checked').length > 0) {
            crearPeticion(apiURL, {case: "guardar", data: $(this).serialize()});
        } else {
            mostrarMensajeAdvertencia("Elige al menos un plantel ...", false);
        }
    });

    $("#nombre").change(function () {
        crearPeticion(apiURL, {case: "existe_carrera", data: "nombre_carrera=" + $(this).val()}, function (res) {
            if (JSON.parse(res).es_valor_error) {
                $("#nombre").addClass("is-invalid");
            } else {
                $("#nombre").removeClass("is-invalid");
            }
        });
    });
});

function recuperarCampos() {
    crearPeticion(apiURL, {case: "recuperar_campos_formulario"}, function (res) {
        let rs = JSON.parse(res);
        if (rs.grupoPlanteles.length > 0) {
            crearCheckboxes("grupoPlanteles", rs.grupoPlanteles, "planteles", "int");
            rs.grupoTipos.forEach(function (idx) {
                crearOpcionSelector($("#grupoTipos"), idx, idx);
            });
        } else {
            $("#cardCarrera").empty();
            insertarAlerta("#cardCarrera", "Primero debes agregar planteles");
        }
    });
}


