var urlAPI = "api/DocenteAPI.php";

function ready() {
    $(document).ready(function () {
        recuperarCarreras(construirListaProfesores);
        ajustarEventos();
    });
}


function ajustarEventos() {
    const $searchInput = $('#searchInput');
    const $profesorList = $('#profesor-list'); //contenedor de tarjetas
    $searchInput.on('input', function () {
        const searchTerm = $searchInput.val().toLowerCase().trim();
        $profesorList.find('.profesor-tarjeta').each(function () {
            const $tarjeta = $(this);
            const nombre = $tarjeta.find('.card-title').text().toLowerCase().trim(); // Get the professor name
            const perfil = $tarjeta.find('.card-subtitle').text().toLowerCase().trim(); // Get the professor profile
            const materias = $tarjeta.find('.materia').map(function () {
                return $(this).text().toLowerCase().trim();
            }).get(); // Get all subject names

            const matches = materias.some(materia => materia.includes(searchTerm)) ||
                    nombre.includes(searchTerm) ||
                    perfil.includes(searchTerm);

            if (matches) {
                $tarjeta.show();
            } else {
                $tarjeta.hide();
            }
        });
    });
    enviarFormulario("updateDocenteForm", urlAPI, "actualizar_docente");
}

function construirListaProfesores() {
    $('#searchInput').val("");
    let carrera = $("#selectorCarrera").find('option:selected');
    let plantel = $("#selectorPlantel").find('option:selected');
    $("#carreraPlantel").text(`${carrera.text()} en el Plantel ${plantel.text()}`);
    crearPeticion(urlAPI, {
        case: "recuperar_docentes",
        data: `id_carrera=${carrera.val()}&id_plantel=${plantel.val()}`
    }, function (res) {
        let profesorContent = $("#profesor-list");
        let listaDocentes = JSON.parse(res);
        profesorContent.empty();
        if (Object.keys(listaDocentes).length > 0) {
            profesorContent.addClass("row row-cols-1 row-cols-md-3 g-4");
            $.each(listaDocentes, function (docente, val) {
                const materias = Object.entries(val.materias).map(([materia, info]) => {
                    const horarios = info.horarios.map(horario => {
                        return `
                            <li>
                                ${horario.dia_semana}: ${horario.hora_inicio} - ${horario.hora_fin}
                                ${horario.es_horario_agendado ? "(Agendado)" : ""}
                            </li>
                        `;
                    }).join("");
                    return `
                        <div>
                            <h6 class="materia">${materia}</h6>
                            <ul>${horarios}</ul>
                        </div>
                    `;
                }).join("");
                //print(val);
                const nuevaTarjeta = `
                    <div class="col profesor-tarjeta" data-nombre="${docente}">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">${docente}</h3>
                                <h6 class="card-subtitle mb-2 text-body-secondary">${val.perfil_profesional}</h6>
                                <a href="https://mail.google.com/mail/?view=cm&fs=1&to=${val.correo_electronico}" target="_blank">${val.correo_electronico}</a>
                                <div class="mt-3">${materias}</div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actualizar
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:mostrarModalActualizarDocente(${JSON.stringify(val).replace(/"/g, '&quot;')})">Información personal</a></li>
                                    <li><a class="dropdown-item" href="javascript:editarMaterias(${JSON.stringify(val.id_docente).replace(/"/g, '&quot;')})">Materias</a></li>
                                </ul>
                                </div>
                                <button class="btn btn-danger btn-sm" onclick="eliminarDocente(${val.id_docente})">Eliminar</button>
    </div>
</div>

                </div>
                        </div>
                    </div>`;
                profesorContent.append(nuevaTarjeta);
            });
        } else {
            profesorContent.removeClass("row row-cols-1 row-cols-md-3 g-4");
            profesorContent.append(`<div class="alert alert-info text-center" role="alert">
                No se han agregado docentes para la carrera ${carrera.text()} en plantel ${plantel.text()}
            </div>`);
        }
    });
}

function eliminarDocente(id) {
    alertaEliminar({
        mensajeAlerta: "Se perderá toda la información con referencia al docente",
        url: urlAPI,
        data: {"case": "eliminar", "data": "id=" + id}
    });
}

function mostrarModalActualizarDocente(docente) {
    print(docente);
    $("#nombre").val(docente.nombre);
    $("#apellidos").val(docente.apellidos);
    $("#correo_electronico").val(docente.correo_electronico);
    $("#perfil_profesional").val(docente.perfil_profesional);
    $("#id_docente").val(docente.id_docente);

    // Mostrar el modal
    $("#updateDocenteModal").modal("show");
}


function editarMaterias(idDocente) {
    redireccionar("editarMaterias.php?docente="+idDocente);
}