let urlAPI = "api/PlantelAPI.php";

function ready() {
    $(document).ready(function () {
        listarPlanteles();
        $("#agregarPlantelForm").submit(function (e) {
            e.preventDefault();
            crearPeticion(urlAPI, {"case": "agregar", "data": $(this).serialize()});
        });
    });
}

function listarPlanteles() {
    crearPeticion(urlAPI, {case: "listar"}, function (res) {
        print(res);
        let html = "";
        JSON.parse(res).forEach(function (plantel) {
            const p = JSON.stringify(plantel);
            const btnsMenuPlantel = [
                {"url": "javascript:editarPlantel(" + p + ")", "titulo": "<i class='ti ti-edit'></i> Editar"},
                {"url": "javascript:eliminar(" + p + ")", "titulo": "<i class='ti ti-trash'></i>Eliminar"}
            ];

            html += "<tr>";
            html += crearColumnaTabla(plantel.nombre);
            html += crearColumnaTabla(plantel.director);
            html += crearColumnaTablaCentrada(crearBotonMenuDesplegable("Acciones", btnsMenuPlantel, "primary"));
            html += "</tr>";
        });
        $("#tbodyPlanteles").html(html);
        //crearDataTable($("#tablaPlanteles"));
    });
}


function editarPlantel(plantel) {
    $('#modalEditarPlantelLabel').text('Editar "' + plantel.nombre + '"');
    $('#nombrePlantel').val(plantel.nombre);
    $('#idPlantel').val(plantel.id_plantel);
    $('#directorPlantel').val(plantel.director);
    $('#modalEditarPlantel').modal('show');
    print(plantel);
    enviarFormulario('#formEditarPlantel', urlAPI, "editar_plantel");
}

function eliminar(plantel) {
    alertaEliminar({
        mensajeAlerta: "Se elimininará " + plantel.nombre,
        url: urlAPI,
        data: {"case": "eliminar", "data": "id=" + plantel.id_plantel}
    });
}