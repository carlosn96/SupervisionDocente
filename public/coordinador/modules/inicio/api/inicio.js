const urlAPI = "api/InicioAPI.php";

function ready() {
    $(document).ready(function () {
        crearPeticion(urlAPI, {"case": "recuperar_agenda"}, function (res) {
            let today = getFechaActual();
            $("#fechaTimeLine").val(today);
            construirGraficaAvance(res);
            res.forEach(function (docente) {
                let $tr = $("<tr>");
                let statusClass = docente.status === "Realizada" ? "primary" : "danger";
                $("<td>").html(
                        `<div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bolder">${docente.nombre_docente}</h6>
                                <p class="fs-3 mb-0">Plantel ${docente.plantel}</p>
                            </div>
                        </div>`
                        ).appendTo($tr);
                $("<td>").text(`${docente.carrera}`).appendTo($tr);
                $("<td>").text(`${docente.dia_semana}, ${docente.fecha}, ${docente.hora_inicio.slice(0, 5)} - ${docente.hora_fin.slice(0, 5)}`).appendTo($tr);
                $("<td>").html(
                        `<span class="badge bg-light-${statusClass} rounded-pill text-${statusClass} px-3 py-2 fs-3">
                                ${docente.status}
                                </span>`
                        ).appendTo($tr);
                $("#bodyAgendaSupervision").append($tr);
            });
            crearDataTable("#tablaAgenda");
            crearTimeLineSupervisiones(res.filter(docente => docente.fecha === today));
        }, "json");

        $("#fechaTimeLine").change(function () {
            let fecha = $(this).val();
            //print(fecha);
            if (fecha.length > 0) {
                crearPeticion(urlAPI,
                        {case: "recuperar_agenda_fecha", data: "fecha=" + $(this).val()},
                        crearTimeLineSupervisiones,
                        "json");
            }
        });
    });
}

function updateDate(days) {
    const $inputFecha = $("#fechaTimeLine");
    let currentDate = new Date($inputFecha.val());
    currentDate.setDate(currentDate.getDate() + days);
    $inputFecha.val(currentDate.toISOString().split('T')[0]);
    $inputFecha.change();
}

function crearTimeLineSupervisiones(listaSupervisiones) {
    let timeLine = $("#timeLineSupervision");
    timeLine.empty();
    timeLine.removeClass("alert alert-danger");
    timeLine.removeAttr("role");
    if (listaSupervisiones.length > 0) {
        listaSupervisiones.forEach(function (docente) {
            let statusClass = docente.status === "Realizada" ? "success" : "danger";
            let urlSupervision = {
                href: "../supervision/?id_agenda=" + docente.id_agenda,
                text: docente.status === "Realizada" ? "Ver informe" : "Supervisar"
            };
            let $li = $("<li>", {class: "timeline-item d-flex position-relative overflow-hidden"});
            $("<div>", {class: "timeline-time text-dark flex-shrink-0 text-end"})
                    .text(docente.hora_inicio.slice(0, 5))
                    .appendTo($li);
            let $badgeWrap = $("<div>", {class: "timeline-badge-wrap d-flex flex-column align-items-center"}).appendTo($li);
            $("<span>", {class: `timeline-badge border-2 border border-${getRandomColor()} flex-shrink-0 my-2`}).appendTo($badgeWrap);
            $("<span>", {class: "timeline-badge-border d-block flex-shrink-0"}).appendTo($badgeWrap);
            $("<div>", {class: "timeline-desc fs-3 text-dark mt-n1 fw-semibold"})
                    .html(`${docente.nombre_materia} en ${docente.carrera} <a href="${urlSupervision.href}" class="text-${statusClass} d-block fw-normal">${urlSupervision.text}</a>`)
                    .appendTo($li);
            timeLine.append($li);
        });
    } else {
        insertarAlerta($("#timeLineSupervision"), "No hay supervisiones programadas para la fecha mostrada");
    }
}

function construirGraficaAvance(listaSupervisiones) {
    if (Object.values(listaSupervisiones).length > 0) {
        let supervisadas = listaSupervisiones.filter(supervision => supervision.status === "Realizada").length;
        let noSupervisadas = listaSupervisiones.filter(supervision => supervision.status === "Sin realizarse").length;
        var grade = {
            series: [supervisadas, noSupervisadas],
            labels: ["Supervisiones realizadas", "Supervisiones sin realizarse"],
            chart: {
                type: "donut"
            },
            colors: ["#0f1f6d", "#f80f0a"],
            dataLabels: {
                enabled: true
            },
            legend: {
                show: false
            },

            plotOptions: {
                pie: {
                    donut: {
                        size: '0%'
                    }
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#grade"), grade);
        chart.render();
    } else {
        $("#graficaAvanceSupervisiones").prop("hidden", true);
    }

}
