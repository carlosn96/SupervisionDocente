let chart;
let rubroContablesContainer;
let rubroNoContablesContainer;

const urlAPI = "api/VerSupervisionAPI.php";


$(document).ready(function () {
    const redirigir = function () {
        redireccionar("../");
    };
    crearPeticion(urlAPI, {case: "recuperar_supervision"}, function (res) {
        //print(res);
        if ((Array.isArray(res) && res.length === 0) || res.info_agenda.length === 0) {
            redirigir();
        } else {
            print(res);
            const supervision = res.supervision.info_supervision;
            const criteriosSupervision = res.supervision.detalles_criterios;
            const criteriosContables = criteriosSupervision.contables;
            const criteriosNoContables = criteriosSupervision.no_contables;

            construirCardInfoSupervision(supervision, res.info_agenda);
            construirTablaValoracionGlobal(criteriosContables);
            construirSeccionCriterios(criteriosContables, $('#tarjetas-rubros-contables'), "contable");
            construirSeccionCriterios(criteriosNoContables, $('#tarjetas-rubros-no-contables'), "no_contable");
        }
    }, "json");

});

function construirCardInfoSupervision(supervision, agenda) {
    const nombreProfesor = Object.keys(agenda)[0];
    const profesor = agenda[nombreProfesor];
    const nombreMateria = Object.keys(profesor.materias)[0];
    const materia = profesor.materias[nombreMateria];
    const horario = materia.horarios[0];
    const cardContent = `
                <div class="mb-3 p-3">
                    <h6 class="mt-3">Docente:</h6>
                    <div class="text-white p-2 rounded">
                        <h5 class="mb-0">${nombreProfesor}</h5>
                    </div>
                    <h6 class="mt-3">Materia:</h6>
        <ul class="list-group mb-3">
            <li class="list-group-item"><strong>${nombreMateria}</strong></li>
            <li class="list-group-item">
                <div class="d-flex justify-content-between">
                    <span class="badge bg-info">${horario.dia_semana}</span>
                    <span>${horario.hora_inicio} - ${horario.hora_fin}</span>
                </div>
            </li>
        </ul>
                `;
    $("#infoDocente").html(cardContent);
    $("#fechaHoraSupervision").val(supervision.fecha_supervision);
    $("#temaSupervision").append(supervision.tema);
    $("#conclusionGeneral").html(supervision.conclusion_general);
}


function construirTablaValoracionGlobal(criteriosContables) {
    let rubros = {};
    let totalValoracion = 0;
    let count = 0;

    criteriosContables.forEach(rubro => {
        const totalCriterios = rubro.criterios.length;
        const criteriosCumplidos = rubro.criterios.filter(criterio => criterio.cumplido).length;
        const valoracion = (criteriosCumplidos / totalCriterios * 100).toFixed(1);
        const descripcion = rubro.descripcion;
        rubros[descripcion] = valoracion;
        $('#rubro-table tbody').append(`
        <tr>
            <td>${descripcion}</td>
            <td>${valoracion} %</td>
        </tr>
    `);
        totalValoracion += parseFloat(valoracion);
        count++;
    });

// Agrega el footer con el promedio
    $('#rubro-table tbody').append(`
    <tr class="table-footer">
        <td class='text-right'><strong>Puntuación promedio</strong></td>
        <td><strong>${(totalValoracion / count).toFixed(1)} %</strong></td>
    </tr>
`);

    const ctx = document.getElementById('valoracionChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(rubros),
            datasets: [{
                    label: 'Valoración',
                    data: Object.values(rubros),
                    backgroundColor: ['rgba(54, 162, 235, 0.2)'],
                    borderColor: ['rgba(54, 162, 235, 1)'],
                    borderWidth: 1
                }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

function construirSeccionCriterios(rubros, contenedor, tipo) {
    rubros.forEach(rubro => {
        const totalCriterios = rubro.criterios.length;
        const criteriosCumplidos = rubro.criterios.filter(criterio => criterio.cumplido).length;
        const porcentajeCumplimiento = (criteriosCumplidos / totalCriterios * 100).toFixed(2);

        let criteriosHTML = '';
        rubro.criterios.forEach(criterio => {
            criteriosHTML += `
                <tr>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" ${criterio.cumplido ? 'checked' : ''} disabled>
                        </div>
                    </td>
                    <td>${criterio.descripcion}</td>
                    <td>${criterio.comentario}</td>
                </tr>
            `;
        });

        const tarjetaHTML = `
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="card-title">${rubro.descripcion}</h2>
                    <table class="table table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th>${tipo === "contable" ? `${porcentajeCumplimiento}%` : ""}</th>
                                <th>Criterio a evaluar</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${criteriosHTML}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        contenedor.append(tarjetaHTML);
    });
}

function calcularValoracion(selector) {
    const checkboxes = document.querySelectorAll(selector);
    const total = checkboxes.length;
    const checked = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
    return Math.round((checked / total) * 100);
}

function salir() {
    crearPeticion(urlAPI, {case: "salir"}, function () {
        refresh();
    });
}