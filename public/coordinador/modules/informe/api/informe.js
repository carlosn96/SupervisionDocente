var urlAPI = "api/InformeAPI.php";

function ready() {
    recuperarCarreras(function () {
        let data = {
            carrera: $("#selectorCarrera").find('option:selected').val(),
            plantel: $("#selectorPlantel").find('option:selected').val()
        };
        crearPeticion(urlAPI, {case: "recuperar_supervisiones", data: $.param(data)}, fillTable, "json");
    });
}


function fillTable(data) {
    const tbody = $("#supervisionTable tbody");
    tbody.empty();
    data.forEach(item => {
        tbody.append(`
                        <tr>
                            <td>${item.docente}</td>
                            <td>${item.conclusion_general.replace(/\n/g, '<br>')}</td>
                            <td>${new Date(item.fecha_supervision).toLocaleString()}</td>
                            <td>${item.promedio_cumplimiento}%</td>
                        </tr>
                    `);
    });
}