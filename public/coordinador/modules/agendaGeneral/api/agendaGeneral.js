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
    // Mostrar un modal o popup con detalles del evento
    const detalles = `
        <strong>Carrera:</strong> ${evento.extendedProps.carrera} <br>
        <strong>Materia:</strong> ${evento.extendedProps.nombre_materia} <br>
        <strong>Docente:</strong> ${evento.title} <br>
        <strong>Plantel:</strong> ${evento.extendedProps.plantel} <br>
        <strong>Status:</strong> ${evento.extendedProps.status} <br>
        <strong>Hora:</strong> ${evento.start.toLocaleTimeString()} - ${evento.end.toLocaleTimeString()} <br>
    `;

    alert(detalles); // Este es un ejemplo simple con alert, pero se puede reemplazar con un modal bonito.
}

