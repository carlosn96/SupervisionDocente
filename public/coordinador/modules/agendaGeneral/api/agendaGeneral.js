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
    const listaEventos = [];

    agenda.forEach(function (a) {
        // Separar componentes de la fecha
        const partesFecha = a.fecha.split('-'); // formato: "YYYY-MM-DD"
        const anio = parseInt(partesFecha[0], 10);
        const mes = parseInt(partesFecha[1], 10) - 1; // meses: 0 a 11
        const dia = parseInt(partesFecha[2], 10);

        // Crear string de fecha local para combinar con hora
        const fechaStr = `${anio}-${(mes + 1).toString().padStart(2, '0')}-${dia.toString().padStart(2, '0')}`;
        const startDate = `${fechaStr}T${a.hora_inicio}`; // ejemplo: "2025-06-20T08:00:00"
        const endDate = `${fechaStr}T${a.hora_fin}`;

        const supervisionHecha = a.status === "Realizada";
        const color = supervisionHecha ? '#28a745' : '#dc3545';
        const borderColor = supervisionHecha ? '#155724' : '#721c24';
        const textColor = '#ffffff';

        listaEventos.push({
            title: a.nombre_docente,
            start: startDate,
            end: endDate,
            backgroundColor: color,
            borderColor: borderColor,
            textColor: textColor,
            extendedProps: {
                grupo: a.grupo,
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
    const ahora = new Date();
    const horaInicio = new Date(evento.start);
    const horaFin = new Date(evento.end);

    const diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'];
    const diaNombre = diasSemana[horaInicio.getDay()];
    const fechaEvento = `${diaNombre}, ${horaInicio.getDate().toString().padStart(2, '0')}-${(horaInicio.getMonth() + 1).toString().padStart(2, '0')}-${horaInicio.getFullYear()}`;

    const horaInicioLocal = horaInicio.toLocaleTimeString('es-MX', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });

    const horaFinLocal = horaFin.toLocaleTimeString('es-MX', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });

    function calcularProximidad() {
        const ahora = new Date();
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        const inicio = new Date(horaInicio);
        const inicioSinHora = new Date(inicio);
        inicioSinHora.setHours(0, 0, 0, 0);

        const diffMs = inicio - ahora;
        const diffDias = Math.floor((inicioSinHora - hoy) / (1000 * 60 * 60 * 24));
        const horasFaltantes = Math.floor(diffMs / (1000 * 60 * 60));
        const minutosFaltantes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

        if (diffMs < 0) {
            return `<span class="badge bg-danger"><i class="ti ti-alert-circle me-1"></i> Supervisión pasada</span>`;
        }

        if (diffDias === 0) {
            return `<span class="badge bg-info text-dark"><i class="ti ti-calendar-event me-1"></i> Hoy - en ${horasFaltantes}h ${minutosFaltantes}m</span>`;
        }

        if (diffDias === 1) {
            return `<span class="badge bg-warning text-dark"><i class="ti ti-alarm me-1"></i> Mañana - en ${horasFaltantes}h ${minutosFaltantes}m</span>`;
        }

        return `<span class="badge bg-primary"><i class="ti ti-clock me-1"></i> En ${diffDias} días, ${horasFaltantes % 24}h ${minutosFaltantes}m</span>`;
    }


    const estadoClase = evento.extendedProps.status === 'Realizada'
            ? 'bg-success-subtle text-success'
            : 'bg-warning-subtle text-warning';

    const detalles = `
        <div class="mb-4">
            <h5 class="fw-semibold text-primary mb-2"><i class="ti ti-calendar"></i> ${fechaEvento}</h5>
            ${calcularProximidad()}
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="p-3 border rounded">
                    <div class="fw-semibold text-dark text-uppercase small mb-1">
                        <i class="ti ti-user me-1"></i> Docente
                    </div>
                    <div class="fs-6 text-body">${evento.title}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 border rounded">
                    <div class="fw-semibold text-dark text-uppercase small mb-1">
                        <i class="ti ti-book me-1"></i> Materia
                    </div>
                    <div class="fs-6 text-body">${evento.extendedProps.nombre_materia}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 border rounded">
                    <div class="fw-semibold text-dark text-uppercase small mb-1">
                        <i class="ti ti-clock me-1"></i> Hora
                    </div>
                    <div class="fs-6 text-body">${horaInicioLocal} - ${horaFinLocal}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 border rounded">
                    <div class="fw-semibold text-dark text-uppercase small mb-1">
                        <i class="ti ti-users me-1"></i> Grupo
                    </div>
                    <div><span class="badge bg-secondary-subtle text-dark">${evento.extendedProps.grupo}</span></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 border rounded">
                    <div class="fw-semibold text-dark text-uppercase small mb-1">
                        <i class="ti ti-school me-1"></i> Carrera
                    </div>
                    <div class="fs-6 text-body">${evento.extendedProps.carrera}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 border rounded">
                    <div class="fw-semibold text-dark text-uppercase small mb-1">
                        <i class="ti ti-location me-1"></i> Plantel
                    </div>
                    <div class="fs-6 text-body">${evento.extendedProps.plantel}</div>
                </div>
            </div>
        </div>
    `;

    const modalHTML = `
        <div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="eventoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-sm">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-semibold" id="eventoModalLabel">
                            Supervisión <span class="badge ms-2 ${estadoClase}">${evento.extendedProps.status}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        ${detalles}
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i> Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('body').append(modalHTML);
    const modal = new bootstrap.Modal($('#eventoModal')[0]);
    modal.show();

    $('#eventoModal').on('hidden.bs.modal', function () {
        $(this).remove();
    });
}
