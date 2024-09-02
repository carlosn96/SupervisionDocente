const urlAPI = "api/VerSupervisionAPI.php";

let chart;
let rubroContablesContainer;
let rubroNoContablesContainer;


function ready() {
    $(document).ready(function () {
        let idAgenda = $("#id_agenda").val();
        const redirigir = function () {
            redireccionar("../agenda");
        };
        if (idAgenda) {
            crearPeticion(urlAPI, {case: "recuperar_supervision", data: "id_agenda=" + idAgenda}, function (res) {
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
                    construirSeccionCompatirResultados(supervision);
                    $("#id_supervision").val(supervision.id_supervision);
                }
            }, "json");
        } else {
            redirigir();
        }
        $('#editModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget); // Botón que abrió el modal
            const id = button.data('id'); // ID del elemento a editar
            const type = button.data('type'); // Tipo de contenido a editar
            const column = button.data('column'); // Nombre de la columna de la base de datos

            const element = $('#' + id);
            const currentText = element.text().trim(); // Obtener el texto actual

            const modal = $(this);
            modal.find('.modal-title').text(`Editar ${type === 'tema' ? 'Tema' : 'Conclusiones'}`);
            modal.find('#editInput').val(currentText);

            modal.data('id', id);
            modal.data('column', column); // Guardar el nombre de la columna en los datos del modal
        });

        $('#saveChanges').on('click', function () {
            const modal = $('#editModal');
            const id = modal.data('id');
            const column = modal.data('column'); // Obtener el nombre de la columna del modal
            const newText = $('#editInput').val();
            $('#' + id).text(newText);
            modal.modal('hide');

            crearPeticion(urlAPI, {case: "actualizar_supervision", data: `columna=${column}&valor=${newText}&id_agenda=${$("#id_agenda").val()}`}, function (res) {
                print(res);
            });
        });
    });
}

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
                        <p> <strong> <a href="https://mail.google.com/mail/?view=cm&fs=1&to=${profesor.correo_electronico}" target="_blank">${profesor.correo_electronico}</a> </strong> </p>
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
    let enviarMail = "https://mail.google.com/mail/?view=cm&fs=1&to=" + profesor.correo_electronico
            + "&su=" + encodeURIComponent("Retroalimentación de Supervisión Docente " + profesor.fecha_agenda)
            + "&body=" + encodeURIComponent("Estimado " + nombreProfesor + "\n ...");
    $("#urlResultadosGmail").attr("href", enviarMail);
}


function construirTablaValoracionGlobal(criteriosContables) {
    let rubros = {};
    criteriosContables.forEach(rubro => {
        const totalCriterios = rubro.criterios.length;
        const criteriosCumplidos = rubro.criterios.filter(criterio => criterio.cumplido).length;
        const valoracion = (criteriosCumplidos / totalCriterios * 100).toFixed(2);
        const descripcion = rubro.descripcion;
        $('#rubro-table tbody').append(`
                <tr>
                    <td>${descripcion}</td>
                    <td>${valoracion} %</td>
                </tr>
            `);
        rubros[descripcion] = valoracion;
    });
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
                            <input onclick="actualizarCumplimientoCriterioContable(this)" class="form-check-input" type="checkbox" ${criterio.cumplido ? 'checked' : ''} data-id-criterio="${criterio.id_criterio}">
                        </div>
                    </td>
                    <td>${criterio.descripcion}</td>
                    <td data-id-criterio=${criterio.id_criterio} contenteditable="true" onblur="actualizarComentarioCriterioContable(this)">${criterio.comentario}</td>
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

function actualizarCumplimientoCriterioContable(check) {
    let data = {
        id_criterio: check.getAttribute('data-id-criterio'),
        criterio_cumplido: check.checked,
        id_supervision: $("#id_supervision").val()
    };
    crearPeticion(urlAPI, {case: "actualizar_cumplimiento_criterio_contable", data: $.param(data)}, print);
}

function actualizarComentarioCriterioContable(celda) {
    let data = {
        id_criterio: celda.getAttribute('data-id-criterio'),
        comentario: celda.textContent,
        id_supervision: $("#id_supervision").val()
    };
    crearPeticion(urlAPI, {case:"actualizar_comentario_criterio_contable", data:$.param(data)}, print);
}

function calcularValoracion(selector) {
    const checkboxes = document.querySelectorAll(selector);
    const total = checkboxes.length;
    const checked = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
    return Math.round((checked / total) * 100);
}

function construirSeccionCompatirResultados(supervision) {
    let url = `${window.location.protocol}//${window.location.hostname}${window.location.hostname === "localhost" ? "/supervision_docente" : ""}/public/docente/modules/get/?exp=${supervision.id_agenda}`;
    $("#id-expediente").text(supervision.id_supervision);
    var qr = new QRCode(document.getElementById("qrContainer"), {
        text: url,
        width: 200,
        height: 200
    });
    setTimeout(function () {
        var canvas = $('#qrContainer canvas')[0];
        if (canvas) {
            var img = canvas.toDataURL("image/png");
            $('#qr').attr('src', img);
        }
    }, 500);
    $("#contrasenia").text(supervision.contrasenia);
}


function descargarQR() {
    var canvas = $('#qrContainer canvas')[0];
    if (canvas) {
        var img = canvas.toDataURL("image/png");

        // Crea un enlace de descarga
        var link = document.createElement('a');
        link.href = img;
        link.download = 'SupervisionDocenteQR.png';

        // Simula un clic en el enlace para iniciar la descarga
        link.click();
    }
}

function copiarURL() {
    let texto = $("#url-supervision").prop("href");
    navigator.clipboard.writeText(texto)
            .then(() => {
                alert('Contenido copiado al portapapeles');
            })
            .catch(err => {
                console.error('Error al copiar: ', err);
                alert('Error al copiar el contenido.');
            });
}

