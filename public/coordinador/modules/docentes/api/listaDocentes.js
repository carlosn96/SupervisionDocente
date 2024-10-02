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
            $.each(listaDocentes, function (docente, val) {
                const materias = Object.entries(val.materias).map(([materia, info]) => {
                    const horarios = info.horarios.map(horario => `
                <li class="list-group-item">
                    ${horario.dia_semana}: ${horario.hora_inicio} - ${horario.hora_fin}
                    ${horario.es_horario_agendado ? "<span class='badge bg-success ms-2'>(Agendado)</span>" : ""}
                </li>
            `).join("");

                    return `
                <div class="mb-2">
                    <h6 class="materia">${materia}</h6>
                    <ul class="list-group">${horarios}</ul>
                </div>
            `;
                }).join("");

                const nuevaTarjeta = `
            <div class="col-12 col-md-6 col-lg-4 profesor-tarjeta mb-3" data-nombre="${docente}">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">${docente}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">${val.perfil_profesional}</h6>
                        <a href="mailto:${val.correo_electronico}" class="card-link text-primary">${val.correo_electronico}</a>
                        <button class="btn btn-link mt-3 toggle-materias" type="button">Mostrar Materias</button>
                        <div class="materias-content mt-2" style="display: none;">
                            ${materias}
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
            </div>`;
                $("#profesor-list").append(nuevaTarjeta);
            });

            // Event listener to toggle materias display
            $("#profesor-list").on('click', '.toggle-materias', function () {
                const materiasContent = $(this).closest('.card-body').find('.materias-content');
                materiasContent.toggle();
                $(this).text(materiasContent.is(':visible') ? 'Ocultar Materias' : 'Mostrar Materias');
            });
        } else {
            $("#profesor-list").append(`
        <div class="alert alert-info text-center" role="alert">
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
    redireccionar("editarMaterias.php?docente=" + idDocente);
}