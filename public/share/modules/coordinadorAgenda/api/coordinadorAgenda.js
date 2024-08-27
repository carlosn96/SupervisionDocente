var urlAPI = "api/CoordinadorAgendaAPI.php";
var calendar;

$(document).ready(function () {
    crearPeticion(urlAPI, {case: "consultar_agenda"}, function (res) {
        print(res);
        let docentes = res.docentes;
        
        if(docentes) {
            $("#nombreCarrera").text(res.carrera.carrera.tipo+" "+res.carrera.carrera.nombre);
            if (Object.values(docentes).length > 0) {
                iniciarCalendario(crearListaProfesores(docentes));
            } else {
                /*let carreraPlantel = carrera.text() + " del Plantel " + plantel.text();
                 let msg = "<p>No existen profesores en <strong>" + carreraPlantel + "</strong>";
                 insertarAlerta(calendario, msg, "warning");*/
            } 
        } else {
            redireccionar("../../../");
        }
        
        
    }, "json");
});

function iniciarCalendario(eventos) {
    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        themeSystem: 'bootstrap5',
        headerToolbar: {
            left: 'prev,next,today',
            center: 'title',
            right: 'imprimirAgenda'
        },
        customButtons: {
            imprimirAgenda: {
                text: "Imprimir agenda del mes",
                click: function () {
                    const [mes, , año] = calendar.getCurrentData().viewTitle.split(' ');
                    let data = {
                        "fecha": formatDate(new Date(parseInt(año), numeroMes(mes))),
                        "mes": mes,
                        "año": año
                    };
                    crearPeticion(urlAPI, {case: "recuperar_agenda_por_fecha", data: $.param(data)}, function (res) {
                        if (res.agendaVacia) {
                            mostrarMensajeAdvertencia("No hay supervisiones para este mes");
                        } else {
                            redireccionar("api/cronograma.php");
                        }
                    }, "json");
                }
            }
        },
        locale: 'es',
        timeZone: 'UTC',
        initialView: 'dayGridMonth',
        editable: true,
        selectable: true,
        selectMirror: true,
        events: construirEventosSupervision(eventos),
        dateClick: function (event) {
            print(event);
        },
        eventClick: function (info) {
            let parsearFecha = function (fecha) {
                var año = fecha.getUTCFullYear();
                var mes = (fecha.getUTCMonth() + 1).toString().padStart(2, '0'); // getUTCMonth devuelve de 0 a 11
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
            var idSupervision = info.event.extendedProps.detalles;

            print(info.event.extendedProps);

            $('#modalDocente').html(nombreDocente);
            $('#modalStart').html(start);
            $('#modalEnd').html(end);
            $('#modalMateria').html(nombreMateria);
            $('#modalEstatus').html(status);
            $('#modalEstatus').addClass("text-" + (sup_hecha ? "success" : "warning"));
            $('#div-num-expediente').attr("hidden", !sup_hecha);
            $('#expediente').val(idSupervision);
            $("#eventModal").modal("show");
        }
    });
    calendar.render();
}


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

function construirEventosSupervision(eventos) {
    function obtenerHorarioAgendado(detalles) {
        const materias = detalles.materias;
        for (const materia in materias) {
            if (materias.hasOwnProperty(materia)) {
                const horarios = materias[materia].horarios;
                const horarioAgendado = horarios.find(horario => horario.es_horario_agendado);
                if (horarioAgendado) {
                    return {"horario": horarioAgendado, "materia": materia};
                }
            }
        }
        return null; // Si no se encuentra ningún horario agendado
    }

    var listaEventos = [];
    eventos.forEach(function (e) {
        const horarioAgendado = obtenerHorarioAgendado(e.detalles);
        const fechaAgenda = new Date(e.detalles.fecha_agenda);
        const supervisionHecha = e.detalles.supervision_hecha;
        //print(e);
        listaEventos.push({
            title: e.nombre,
            start: `${fechaAgenda.toISOString().split('T')[0]}T${horarioAgendado.horario.hora_inicio}:00`,
            end: `${fechaAgenda.toISOString().split('T')[0]}T${horarioAgendado.horario.hora_fin}:00`,
            extendedProps: {
                status: supervisionHecha ? "Supervisión realizada" : "Supervisión no realizada",
                sup_hecha: supervisionHecha,
                nombreMateria: horarioAgendado.materia,
                idAgenda: e.detalles.id_agenda,
                detalles: e
            }
        });
    });
    return listaEventos;
}


function crearListaProfesores(data) {
    var supervisionesAgendadas = [];
    if (Object.keys(data).length) {
        $.each(data, function (nombre, detalles) {
            if (detalles.es_profesor_agendado) {
                supervisionesAgendadas.push({"nombre": nombre, "detalles": detalles});
            }
        });
    }
    return supervisionesAgendadas;
}


function salir() {
    crearPeticion(urlAPI, {case: "salir"}, function () {
        refresh();
    });
}