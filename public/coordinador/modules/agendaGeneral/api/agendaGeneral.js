let urlAPI = "api/AgendaAPI.php";


function ready() {
    crearPeticion(urlAPI, {
        case: "recuperar_agenda"
    }, function (rs) {
        //print(rs);
        construirSelectorCiclos(rs.ciclos, rs.ciclo_actual);
        let calendario = $('#calendarContent');
        if (rs.agenda.length !== 0) {
            renderizarCalendario(rs.agenda);
        } else {
            let msg = "<p>Sin información para mostrar. </p>";
            insertarAlerta(calendario, msg, "warning");
        }
    }, "json");
}

function construirSelectorCiclos(lista, actual) {
    const $selector = $("#cicloEscolar");
    lista.forEach((ciclo) => {
        crearOpcionSelector($selector, ciclo.id_ciclo_escolar, ciclo.ciclo_escolar);
    });
    $selector.val(actual);
    $selector.on("change", function () {
        crearPeticion(urlAPI, {case: "actualizar_ciclo_escolar", data: "ciclo=" + $(this).val()}, (rs) => {
            if (!rs.es_valor_error) {
                refresh();
            }
        }, "json");
    });
}

function renderizarCalendario(agenda) {
    const calendar = new FullCalendar.Calendar(document.getElementById('calendarContent'), {
        themeSystem: 'bootstrap5',
        headerToolbar: {
            left: 'prev,next,today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,list,multiMonthYear'
        },
        locale: 'es',
        timeZone: 'UTC',
        initialView: 'dayGridMonth',
        editable: false,
        selectable: true,
        events: construirAgenda(agenda),
        eventClick: function (info) {
            mostrarDetallesEvento(info.event);
        }
    });
    calendar.render();
}

function construirAgenda(agenda) {
    var listaEventos = [];
    agenda.forEach(function (a) {
        const fechaAgenda = new Date(a.fecha);
        const supervisionHecha = a.status === "Realizada";
        const color = supervisionHecha ? '#28a745' : '#dc3545';
        const borderColor = supervisionHecha ? '#155724' : '#721c24';
        const textColor = 'white';
        const startDate = `${fechaAgenda.toISOString().split('T')[0]}T${a.hora_inicio}`;
        const endDate = `${fechaAgenda.toISOString().split('T')[0]}T${a.hora_fin}`;
        listaEventos.push({
            title: a.nombre_docente,
            start: startDate,
            end: endDate,
            backgroundColor: color,
            borderColor: borderColor,
            textColor: textColor,
            extendedProps: {
                carrera: a.carrera,
                nombre_materia: a.nombre_materia,
                plantel: a.plantel,
                status: a.status
            }
        });
    });
    return listaEventos;
}


function mostrarDetallesEvento(evento) {
    const horaInicio = new Date(evento.start);
    const horaFin = new Date(evento.end);

    const horaInicioFormateada = `${horaInicio.getUTCHours().toString().padStart(2, '0')}:${horaInicio.getUTCMinutes().toString().padStart(2, '0')}`;
    const horaFinFormateada = `${horaFin.getUTCHours().toString().padStart(2, '0')}:${horaFin.getUTCMinutes().toString().padStart(2, '0')}`;
    const detalles = `
    <div class="mb-3">
        <h6><i class="ti ti-user"></i> <strong>Docente:</strong> ${evento.title}</h6>
        <h6><i class="ti ti-book"></i> <strong>Materia:</strong> ${evento.extendedProps.nombre_materia}</h6>
        <h6><i class="ti ti-clock"></i> <strong>Hora:</strong> ${horaInicioFormateada} - ${horaFinFormateada}</h6>
        <h6><i class="ti ti-school"></i> <strong>Carrera:</strong> ${evento.extendedProps.carrera}</h6>
        <h6><i class="ti ti-location"></i> <strong>Plantel:</strong> ${evento.extendedProps.plantel}</h6>
    </div>
`;
    const modalHTML = `
        <div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="eventoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventoModalLabel">Supervisión ${evento.extendedProps.status}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ${detalles}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('body').append(modalHTML);
    
    const modal = new bootstrap.Modal($('#eventoModal')[0]);
    modal.show();

    // Eliminar el modal después de cerrarlo para evitar duplicados
    $('#eventoModal').on('hidden.bs.modal', function () {
        $(this).remove();
    });
}
