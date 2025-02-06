let urlAPI = "api/GrupoAPI.php";

function ready() {

    $('#grupoForm').submit(function (event) {
        event.preventDefault();
        crearPeticion(urlAPI, {
            case: "crear_grupo",
            data: $(this).serialize() + "&" + recuperarParametrosCarreraCiclo()
        });
    });

    $('#editarGrupoForm').submit(function (event) {
        event.preventDefault();
        crearPeticion(urlAPI, {
            case: "actualizar_grupo",
            data: $(this).serialize() + "&" + recuperarParametrosCarreraCiclo()
        });
    });

    recuperarCarreras(function () {
        const data = {
            case: "recuperar_grupos",
            data: recuperarParametrosCarreraCiclo()
        };
        crearPeticion(urlAPI, data, function (res) {
            //print(res);
            $("#tabContent").empty();
            const tabla = $("<table>", {class: "table text-nowrap mb-0 align-middle", id: "tablaGrupos"})
                    .append($("<thead>", {class: "text-dark fs-4"})
                            .append($("<tr>")
                                    .append($("<th>", {class: "text-center"}).append($("<h6>", {class: "fs-4 fw-semibold mb-0", text: "Clave"})))
                                    .append($("<th>", {class: "text-center"}).append($("<h6>", {class: "fs-4 fw-semibold mb-0", text: "Seudónimo"})))
                                    .append($("<th>", {class: "text-center"}).append($("<h6>", {class: "fs-4 fw-semibold mb-0", text: "Turno"})))
                                    .append($("<th>", {class: "text-center"}).append($("<h6>", {class: "fs-4 fw-semibold mb-0"}).append(
                                            $("<i>", {class: "ti ti-dots-vertical fs-7 d-block"})
                                            )))
                                    )
                            )
                    .append($("<tbody>"));
            let html = "";
            JSON.parse(res).forEach(function (grupo) {
                const g = JSON.stringify(grupo);
                const btnsMenu = [
                    {"url": "javascript:editarGrupo(" + g + ")", "titulo": "<i class='ti ti-edit'></i> Editar"},
                    {"url": "javascript:eliminar(" + g + ")", "titulo": "<i class='ti ti-trash'></i> Eliminar"},
                    {"url": "javascript:verHorario(" + grupo.id_grupo + ")", "titulo": "<i class='ti ti-clock'></i> Ver horario"}
                ];
                html += "<tr>";
                html += crearColumnaTabla(grupo.clave);
                html += crearColumnaTabla(grupo.seudonimo);
                html += crearColumnaTabla(grupo.turno);
                html += crearColumnaTablaCentrada(crearBotonMenuDesplegable('<i class="ti ti-dots-vertical fs-7 d-block"></i>', btnsMenu, "", "rounded-circle btn-transparent btn-sm px-1 btn shadow-none"));
                html += "</tr>";
            });
            tabla.find("tbody").append(html);
            $("#tabContent").append(tabla);
            crearDataTable($("#tablaGrupos"));
        });
    });

    crearPeticion(urlAPI, {case: "recuperar_turnos_grupos"}, (rs) => {
        rs.turnos.forEach((turno) => {
            crearOpcionSelector($("#turno"), turno, turno);
            crearOpcionSelector($("#turno-modal"), turno, turno);
        });
        //print(rs.grados);
        rs.grados.forEach((grado) => {
            crearOpcionSelector($("#grado_actual"), grado.id_grado, grado.grado);
            //crearOpcionSelector($("#turno-modal"), turno, turno);
        });
    }, "json");
}

function recuperarParametrosCarreraCiclo() {
    const carrera = $("#selectorCarrera").find('option:selected').val();
    const plantel = $("#selectorPlantel").find('option:selected').val();
    const ciclo = $("#selectorCicloEscolar").find('option:selected').val();
    return $.param({carrera: carrera, plantel: plantel, ciclo: ciclo});
}

function eliminar(grupo) {
    alertaEliminar({
        mensajeAlerta: "Se elimininará " + grupo.clave,
        url: urlAPI,
        data: {"case": "eliminar", "data": "id=" + grupo.id_grupo}
    });
}

function editarGrupo(grupo) {
    $("#idGrupo-modal").val(grupo.id_grupo);
    $("#clave-modal").val(grupo.clave);
    $("#seudonimo-modal").val(grupo.seudonimo);
    $("#turno-modal").val(grupo.turno);
    $("#editar-grupo-modal").modal("show");
}

function verHorario(id) {
    let data = $.param({
        tipo: "Grupo",
        id: id
    }) + "&" +recuperarParametrosCarreraCiclo();
    print(data);
    crearPeticion("../horario/api/HorarioAPI.php", {case: "recuperar_horario", data: data}, function (res) {
        redireccionar("../verHorario");
    });
}