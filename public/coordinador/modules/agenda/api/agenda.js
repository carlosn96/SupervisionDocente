let urlAPI = "api/AgendaAPI.php";
var calendar;

const tiposEventos = {
    EVENTO: "evento",
    SUPERVISION: "supervision"
};

function ready() {
    $(document).ready(function () {
        recuperarCarreras(function () {
            var carrera = $("#selectorCarrera").find('option:selected');
            var plantel = $("#selectorPlantel").find('option:selected');
            $("#nombreCarrera").html(carrera.text());
            const data = {
                case: "recuperar_agenda",
                data: "id_carrera=" + carrera.val() + "&id_plantel=" + plantel.val()
            };
            crearPeticion(urlAPI, data, function (rs) {
                print(rs);
                let calendario = $('#calendar');
                calendario.empty();
                calendario.removeClass();
                limipiarContenedoresDocentes();
                if (Object.values(rs.supervisiones).length > 0) {
                    iniciarCalendario(crearListaProfesores(rs.supervisiones), rs.eventos);
                } else {
                    let carreraPlantel = carrera.text() + " del Plantel " + plantel.text();
                    let url = "<a href = '../docentes/agregarDocente.php'> esta ventana </a>";
                    let msg = "<p>No existen profesores en <strong>" + carreraPlantel + "</strong>. Dirigirse a " + url + " para agregar docente</p>";
                    insertarAlerta(calendario, msg, "warning");
                }
            }, "json");
        });
        $("#agendaSupervisionForm").submit(agendarSupervision);
        $("#agregarEventoForm").submit(guardarEvento);
        $('#eventoUnDia').change(function () {
            $('#fechaHoraFinEvento').prop('disabled', $(this).is(':checked'));
        });
        $('#eventoUnDiaEdit').change(function () {
            const isChecked = $(this).is(':checked');
            $('#fechaHoraFinEdit').prop('disabled', isChecked);
            if (isChecked) {
                enviarPeticionActualizarEvento("fecha_hora_fin", null);
            }
        });
        $("#btnEliminarEvento").click(eliminarEvento);
    });
}


function verCronograma(url) {
    let carrera = $("#selectorCarrera").find('option:selected');
    let plantel = $("#selectorPlantel").find('option:selected');
    const palabras = calendar.getCurrentData().viewTitle.split(' ');
    const mes = palabras[0];
    const año = parseInt(palabras[2]);
    crearPeticion(urlAPI, {
        case: "recuperar_agenda_por_fecha",
        data: "fecha=" + formatDate(new Date(mes + ' 1, ' + año)) + "&id_carrera=" + carrera.val() + "&id_plantel=" + plantel.val()
    }, function (res) {
        let arr = JSON.parse(res);
        if (arr.length > 0) {
            redireccionar("api/descargar.php?agenda=" + encodeURIComponent(JSON.stringify(arr)) + "&mes=" + mes + "&anio=" + año);
        } else {
            mostrarMensajeAdvertencia("No hay supervisiones para este mes");
        }
    });
}

function iniciarCalendario(supervisiones, eventos) {
    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        themeSystem: 'bootstrap5',
        headerToolbar: {
            left: 'prev,next,today',
            center: 'title',
            right: 'vistaCronograma,agregarEvento'
        },
        customButtons: {
            exportarCalendar: {
                text: "Exportar G-Calendar"
            },
            botonDescargar: {
                text: 'Descargar',
                click: function () {
                    const palabras = calendar.getCurrentData().viewTitle.split(' ');
                    const mes = palabras[0];
                    const año = parseInt(palabras[2]);
                    let carrera = $("#selectorCarrera").find('option:selected');
                    let plantel = $("#selectorPlantel").find('option:selected');
                    crearPeticion(urlAPI, {
                        case: "recuperar_agenda_por_fecha",
                        data: "fecha=" + formatDate(new Date(mes + ' 1, ' + año)) + "&id_carrera=" + carrera.val() + "&id_plantel=" + plantel.val()
                    }, function (res) {
                        //print(res);
                        let arr = JSON.parse(res);
                        if (arr.length > 0) {
                            redireccionar("api/descargar.php?agenda=" +
                                    encodeURIComponent(JSON.stringify(arr)) +
                                    "&mes=" + mes + "&anio=" + año +
                                    "&plantel=" + $("#selectorPlantel").find('option:selected').text() +
                                    "&carrera=" + $("#selectorCarrera").find('option:selected').text());
                        } else {
                            mostrarMensajeAdvertencia("No hay supervisiones para este mes");
                        }
                    });
                }
            },
            vistaCronograma: {
                text: "Ver cronograma",
                click: function () {
                    const [mes, , año] = calendar.getCurrentData().viewTitle.split(' ');
                    let carrera = $("#selectorCarrera").find('option:selected');
                    let plantel = $("#selectorPlantel").find('option:selected');
                    let data = {
                        "fecha": formatDate(new Date(parseInt(año), numeroMes(mes))),
                        "carrera": {id: carrera.val(), val: carrera.text()},
                        "plantel": {id: plantel.val(), val: plantel.text()},
                        "mes": mes,
                        "año": año
                    };
                    crearPeticion(urlAPI, {case: "recuperar_agenda_por_fecha", data: $.param(data)}, function (res) {
                        if (res.agendaVacia) {
                            mostrarMensajeAdvertencia("No hay supervisiones para este mes");
                        } else {
                            redireccionar("api/view.php");
                        }
                    }, "json");
                }
            },
            agregarEvento: {
                text: "Agregar evento",
                click: function () {
                    $("#agregarEventoModal").modal("show");
                }
            }
        },
        locale: 'es',
        timeZone: 'UTC',
        initialView: 'dayGridMonth',
        editable: true,
        selectable: true,
        selectMirror: true,
        events: construirEventosSupervision(supervisiones, eventos),
        dateClick: function (event) {
            print(event);
        },
        eventClick: function (info) {
            (info.event.extendedProps.tipo === tiposEventos.SUPERVISION
                    ? abrirModalSupervision
                    : abrirModalDescripcionEvento)(info);
        },
        eventDrop: function (e) {
            var detalles = e.event.extendedProps.detalles;
            if (detalles.detalles.supervision_hecha) {
                e.revert();
            } else {
                actualizarAgenda(e.event.start, detalles, e.revert);
            }

        }
    });
    calendar.render();
}

function abrirModalDescripcionEvento(info) {
    const detallesEvento = info.event.extendedProps.detalles;
    const eventoUnDia = detallesEvento.fecha_hora_fin === null;
    $('#idEvento').val(detallesEvento.id);
    $('#nombreEventoEdit').val(detallesEvento.nombre);
    $('#eventoUnDiaEdit').prop('checked', eventoUnDia);
    $('#fechaHoraInicioEdit').val(detallesEvento.fecha_hora_inicio);
    $('#fechaHoraFinEdit').val(detallesEvento.fecha_hora_fin || '');
    $('#fechaHoraFinEdit').prop("disabled", eventoUnDia);
    $('#lugarEdit').val(detallesEvento.lugar);
    $('#detallesEdit').val(detallesEvento.detalles);
    $('#visualizarEventoModal').modal('show');
}

function eliminarEvento() {
    alertaEliminar({
        mensajeAlerta: "El evento ya no estará disponible",
        url: urlAPI,
        data: {
            case: "eliminar_evento",
            data: "id_evento=" + $('#idEvento').val()
        }
    });
}

function actualizarEvento(e) {
    const campo = $(e.target).data("nombre-campo");
    const val = $(e.target).val();
    enviarPeticionActualizarEvento(campo, val);
}

function enviarPeticionActualizarEvento(campo, val) {
    const id_evento = $('#idEvento').val();
    crearPeticion(urlAPI, {
        case: "actualizar_evento",
        data: $.param({
            id_evento: id_evento,
            campo: campo,
            val: val
        })
    });
}

function abrirModalSupervision(info) {
    let parsearFecha = function (fecha) {
        var año = fecha.getUTCFullYear();
        var mes = (fecha.getUTCMonth() + 1).toString().padStart(2, '0');
        var dia = (fecha.getUTCDate()).toString().padStart(2, '0');
        var horas = (fecha.getUTCHours()).toString().padStart(2, '0');
        var minutos = (fecha.getUTCMinutes()).toString().padStart(2, '0');
        var segundos = (fecha.getUTCSeconds()).toString().padStart(2, '0');
        return `${año}${mes}${dia}T${horas}${minutos}${segundos}`;
    };

    var nombreDocente = info.event.title;
    var start = info.event.start ? info.event.start.toISOString().slice(0, 16).replace('T', ' ') : 'No especificada';
    var end = info.event.end ? info.event.end.toISOString().slice(0, 16).replace('T', ' ') : 'No especificada';
    var nombreMateria = info.event.extendedProps.nombreMateria;
    var status = info.event.extendedProps.status;
    var sup_hecha = info.event.extendedProps.sup_hecha;
    var idAgenda = info.event.extendedProps.idAgenda;
    var idSupervision = info.event.extendedProps.detalles;

    print(info.event.extendedProps);

    // Llenar el modal con la información extraída
    $('#modalDocente').html(nombreDocente);
    $('#modalStart').html(start);
    $('#modalEnd').html(end);
    $('#modalMateria').html(nombreMateria);
    $('#modalEstatus').html(status);
    $('#modalEstatus').removeClass();
    $('#modalEstatus').addClass("text-" + (sup_hecha ? "success" : "warning"));
    $('#div-num-expediente').attr("hidden", !sup_hecha);
    $('#expediente').val(idSupervision);
    $("#btnSupervisarDocente").html(sup_hecha ? "Ver resumen" : "Supervisar docente");
    $("#btnSupervisarDocente").click(function () {
        redireccionar("../supervision?id_agenda=" + idAgenda);
    });
    $("#btnEliminarSupervision").prop("hidden", !sup_hecha);
    $("#btnEliminarSupervision").data("id-agenda", idAgenda);
    var url = 'https://calendar.google.com/calendar/u/0/r/eventedit?' +
            '&text=Supervisión a ' + encodeURIComponent(nombreDocente) +
            '&dates=' + parsearFecha(info.event.start) + '/' + parsearFecha(info.event.end) +
            '&details=' + encodeURIComponent("Supervision de " + nombreDocente + " en la materia '" + nombreMateria) + "'" +
            '&location=' + encodeURIComponent("Plantel UNE " + $("#selectorPlantel").find('option:selected').text()) +
            '&ctz=America/Mexico_City';
    $('#btnAddToCalendar').attr('href', url);
    $("#agregarGCalendar").attr("hidden", sup_hecha);
    $("#eventModal").modal("show");
}

function actualizarAgenda(diaActual, detallesEvento, revertir) {
    const dias = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
    const diaSemanaActual = dias[diaActual.getDay()];
    const horarios = detallesEvento.detalles.materias[Object.keys(detallesEvento.detalles.materias)[0]].horarios;

    const horarioEncontrado = horarios.some((horario) => {
        return diaSemanaActual === horario.dia_semana;
    });

    if (!horarioEncontrado) {
        let dia = horarios.map((item) => {
            return "\n" + item.dia_semana;
        });
        mostrarMensajeAdvertencia("No se puede elegir otro día que no sea " + dia, false);
        revertir();
    } else {
        let fechaActualizar = formatDate(diaActual);
        if (diaActual > (new Date())) {
            crearPeticion(urlAPI, {
                case: "actualizar_dia",
                data: $.param({
                    horario: detallesEvento,
                    fecha_actualizar: fechaActualizar
                })
            }, function (res) {
                print(res);
                mostrarMensajeOk("Dia actualizado ", false);
            });
        } else {
            mostrarMensajeAdvertencia("No se puede esta fecha " + fechaActualizar, false);
            revertir();
        }
    }
}

function construirEventosSupervision(supervisiones, eventos) {
    function obtenerHorarioAgendado(detalles) {
        const materias = detalles.materias || {}; // Asegurarse de que materias existe
        for (const materia in materias) {
            if (materias.hasOwnProperty(materia)) {
                const horarios = materias[materia].horarios || [];
                const horarioAgendado = horarios.find(horario => horario.es_horario_agendado);
                if (horarioAgendado) {
                    return {"horario": horarioAgendado, "materia": materia};
                }
            }
        }
        return null;
    }

    var listaEventos = [];

    // Procesar las supervisiones
    supervisiones.forEach(function (e) {
        const horarioAgendado = obtenerHorarioAgendado(e.detalles);
        if (!horarioAgendado)
            return;
        const fechaAgenda = new Date(e.detalles.fecha_agenda);
        const supervisionHecha = e.detalles.supervision_hecha;

        const color = supervisionHecha ? 'green' : 'red';
        const borderColor = supervisionHecha ? 'darkgreen' : 'darkred';
        const textColor = 'white';

        listaEventos.push({
            title: e.nombre,
            start: `${fechaAgenda.toISOString().split('T')[0]}T${horarioAgendado.horario.hora_inicio}:00`,
            end: horarioAgendado.horario.hora_fin ? `${fechaAgenda.toISOString().split('T')[0]}T${horarioAgendado.horario.hora_fin}:00` : null,
            extendedProps: {
                status: supervisionHecha ? "Supervisión realizada" : "Supervisión no realizada",
                sup_hecha: supervisionHecha,
                nombreMateria: horarioAgendado.materia,
                idAgenda: e.detalles.id_agenda,
                detalles: e,
                tipo: tiposEventos.SUPERVISION
            },
            backgroundColor: color,
            borderColor: borderColor,
            textColor: textColor
        });
    });

    eventos.forEach(function (e) {
        const fechaInicio = getFechaHoraActual(new Date(e.fecha_hora_inicio));
        const fechaFin = e.fecha_hora_fin ? getFechaHoraActual(new Date(e.fecha_hora_fin)) : null;
        listaEventos.push({
            title: e.nombre,
            start: fechaInicio,
            end: fechaFin,
            extendedProps: {
                lugar: e.lugar,
                detalles: e,
                tipo: tiposEventos.EVENTO
            },
            backgroundColor: 'blue',
            borderColor: 'darkblue',
            textColor: 'black'
        });
    });

    return listaEventos;
}

function limipiarContenedoresDocentes() {
    $('#listaSinAgendar').empty();
    $('#listaMateriasContainer').empty();
}

function crearListaProfesores(data) {
    var supervisionesAgendadas = [];
    limipiarContenedoresDocentes();
    if (Object.keys(data).length) {
        $.each(data, function (nombre, detalles) {
            if (detalles.es_profesor_agendado) {
                supervisionesAgendadas.push({"nombre": nombre, "detalles": detalles});
                agregarListaAgendados(nombre, detalles);
            } else {
                agregarListaSinAgendar(nombre, detalles);
            }
        });
    }
    return supervisionesAgendadas;
}

function agregarListaSinAgendar(nombreProfesor, detalles) {
    const nombre = nombreProfesor.replace(/\s+/g, '');
    const accordion = $('#listaMateriasContainer');
    const card = $('<div class="accordion-item"></div>');
    const header = $(`
            <h2 class="accordion-header" id="heading${nombre}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${nombre}" aria-expanded="false" aria-controls="collapse${nombre}">
                    ${nombreProfesor}
                </button>
            </h2>
        `);
    const collapse = $(`
            <div id="collapse${nombre}" class="accordion-collapse collapse" aria-labelledby="heading${nombre}" data-bs-parent="#accordionProfesores">
                <div class="accordion-body">
                    <ul class="list-group list-group-flush"></ul>
                </div>
            </div>
        `);
    const listGroup = collapse.find('.list-group');
    $.each(detalles.materias, function (materia, info) {
        const listItem = $(`
        <li class="list-group-item">
            <strong>${materia}</strong>:
            <ul>
                ${info.horarios.map(horario => `
                    <li>
                        ${horario.dia_semana}: ${horario.hora_inicio} - ${horario.hora_fin}
                        <p><button class="btn btn-outline-primary btn-sm ms-2" onclick='agendarMateria("${nombreProfesor}", \`${JSON.stringify(horario).replace(/'/g, "\\'")}\`)'>Agendar</button></p>
                    </li>`).join('')}
            </ul>
        </li>
    `);
        listGroup.append(listItem);
    });

    card.append(header, collapse);
    accordion.append(card);
}

function agregarListaAgendados(nombre, detalles) {
    const accordion = $('#listaSinAgendar');
    const card = $('<div class="accordion-item"></div>');
    const header = $(`
            <h2 class="accordion-header" id="heading${nombre.replace(/\s+/g, '')}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${nombre.replace(/\s+/g, '')}" aria-expanded="false" aria-controls="collapse${nombre.replace(/\s+/g, '')}">
                    ${nombre}
                </button>
            </h2>
        `);
    const collapse = $(`
            <div id="collapse${nombre.replace(/\s+/g, '')}" class="accordion-collapse collapse" aria-labelledby="heading${nombre.replace(/\s+/g, '')}" data-bs-parent="#accordionProfesores">
                <div class="accordion-body">
                    <ul class="list-group list-group-flush"></ul>
                </div>
            </div>
        `);
    const listGroup = collapse.find('.list-group');
    //print(detalles.materias);
    $.each(detalles.materias, function (materia, info) {
        const listItem = $(`
                <li class="list-group-item">
                    <strong>${materia}</strong>:
                    <ul>
                        ${info.horarios.map(horario =>
                `<li>
                            <h6 class='text-${horario.es_horario_agendado ? "danger" : ""}'>${horario.dia_semana}: ${horario.hora_inicio} - ${horario.hora_fin}${horario.es_horario_agendado ? " <span class='badge text-bg-primary'>Agendado</span>" : ""}</h6>
                            ${horario.es_horario_agendado ? `<button class="btn btn-sm btn-outline-dark" type="button" onclick="reagendarHorario(${horario.id_horario})"> Reagendar </button>` : ""}
                         </li>`).join('')}
                    </ul>
                </li>
            `);
        listGroup.append(listItem);
    });
    card.append(header, collapse);
    accordion.append(card);
}

function reagendarHorario(idHorario) {
    alertaEliminar({
        mensajeAlerta: "Toda la información de esta supervisión se eliminará",
        url: urlAPI,
        data: {"case": "eliminar", "data": "id_horario=" + idHorario}
    });
}

const diasSemana = {
    "Domingo": 0,
    "Lunes": 1,
    "Martes": 2,
    "Miércoles": 3,
    "Jueves": 4,
    "Viernes": 5,
    "Sábado": 6
};


function numeroMes(mes) {
    const meses = {
        enero: 0,
        febrero: 1,
        marzo: 2,
        abril: 3,
        mayo: 4,
        junio: 5,
        julio: 6,
        agosto: 7,
        septiembre: 8,
        octubre: 9,
        noviembre: 10,
        diciembre: 11
    };
    return meses[mes.toLowerCase()];
}

function obtenerDiasDeLaSemanaEnElMes(diaSemana, fecha) {
    const mes = fecha.getMonth();
    const año = fecha.getFullYear();
    const fechaActual = new Date();
    const fechas = [];
    for (let dia = 1; dia <= 31; dia++) {
        const fechaGenerada = new Date(año, mes, dia);
        if (fechaGenerada < fechaActual) {
            continue;
        }
        if (fechaGenerada.getDay() === diasSemana[diaSemana]) {
            fechas.push(dia);
        }
        if (dia >= new Date(año, mes + 1, 0).getDate()) {
            break;
        }
    }
    return fechas;
}

function agendarMateria(nombreProfesor, horarioClase) {
    const horario = JSON.parse(horarioClase);
    //print(horario);
    const [mes, , año] = calendar.getCurrentData().viewTitle.split(' ');
    const diaSemana = horario.dia_semana;
    const fechasDisponibles = obtenerDiasDeLaSemanaEnElMes(diaSemana, new Date(parseInt(año), numeroMes(mes)));
    $("#fechaSupervisionSelector").empty();
    $("#nombreProfesorTitleAgenda").html(nombreProfesor);
    if (fechasDisponibles.length > 0) {
        fechasDisponibles.forEach(function (numeroDia) {
            let date = new Date(parseInt(año), numeroMes(mes));
            date.setDate(numeroDia);
            crearOpcionSelector($("#fechaSupervisionSelector"), formatDate(date), diaSemana + " " + numeroDia + " de " + mes + " de " + año);
        });
        $("#idHorarioSupervision").val(horario.id_horario);
    } else {
        crearOpcionSelector($("#fechaSupervisionSelector"), "", "No hay fechas disponibles para " + mes + " de " + año);

    }
    $("#supervisionModal").modal("show");
}

function agendarSupervision(e) {
    e.preventDefault();
    crearPeticion(urlAPI, {case: "agendar_supervision", data: $(this).serialize()});
}


function eliminarSupervision() {
    console.log($("#btnEliminarSupervision").data("id-agenda"));
}

function guardarEvento(e) {
    e.preventDefault();
    crearPeticion(urlAPI, {case: "guardar_evento", data: $(this).serialize()});
}