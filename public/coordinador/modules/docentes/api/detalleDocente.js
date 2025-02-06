
const urlAPI = "api/DocenteAPI.php";

function ready() {
    recuperarCarreras(() => {
        const data = $.param({
            carrera: $("#selectorCarrera").val(),
            plantel: $("#selectorPlantel").val()
        });
        crearPeticion(urlAPI, {case: "recuperar_grupos", data: data}, (grupos) => {
            const selector = $("#grupoMateria");
            selector.empty();
            crearOpcionSelector(selector, "", "Elige un grupo");
            if (grupos.length) {
                grupos.forEach((grupo) => {
                    const text = grupo.clave + " (" + grupo.seudonimo + ")";
                    crearOpcionSelector(selector, grupo.id_grupo, text);
                });
                $("#mensajeNoGrupo").hide();
            } else {
                $("#mensajeNoGrupo").show();
            }
        }, "json");
    });

    crearPeticion(urlAPI, {case: "recuperar_detalles_docente"}, (rs) => {
//        print(rs);
        const key = Object.keys(rs);
        const docente = rs[key];
        $("#perfil").text(docente.perfil_profesional);
        $("#nombre").text(key);
        $("#correo").text(docente.correo_electronico);
        $("#id_docente").val(docente.id_docente);
        crearTablaMaterias(docente.materias);
    }, "json");

    ajustarEventosAsignacionMaterias();
}


function ajustarEventosAsignacionMaterias() {
    $("#grupoMateria").change(function () {
        const value = $(this).val();
        if (value.length) {
            const data = $.param({
                carrera: $("#selectorCarrera").val(),
                plantel: $("#selectorPlantel").val(),
                grupo: value
            });
            crearPeticion(urlAPI, {case: "obtener_horarios", data: data}, (res) => {
                print(res);
            });
        }
    });


    $('#agregarHorarioBtn').click(function () {
        // Create a new horario-item and append it to #horarioContainer
        var newHorarioItem = `
            <div class="horario-item row align-items-center mb-3">
                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <select class="form-select" name="diaSemana[]" required>
                        <option value="Lunes">Lunes</option>
                        <option value="Martes">Martes</option>
                        <option value="Miércoles">Miércoles</option>
                        <option value="Jueves">Jueves</option>
                        <option value="Viernes">Viernes</option>
                        <option value="Sábado">Sábado</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <input type="time" class="form-control" name="horaInicio[]" required>
                </div>
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <input type="time" class="form-control" name="horaFin[]" required>
                </div>
                <div class="col-12 col-md-1 text-center mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-outline-danger w-100 remove-horario">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
            </div>`;
        $('#horarioContainer').append(newHorarioItem);
    });


    $(document).on('click', '.remove-horario', function () {
        $(this).closest('.horario-item').remove();
    });

    $(document).on('change', 'input[name="horaFin[]"], input[name="horaInicio[]"], select[name="diaSemana[]"]', function () {
        const $horarioItem = $(this).closest('.horario-item');
        const horaInicio = $horarioItem.find('input[name="horaInicio[]"]').val();
        const horaFin = $horarioItem.find('input[name="horaFin[]"]').val();
        if (horaFin && horaInicio && horaFin <= horaInicio) {
            alert('La hora de término no puede ser anterior o igual a la hora de inicio.');
            $horarioItem.find('input[name="horaFin[]"]').val('');
            return;
        }
        if (horaInicio && horaFin) {
            if (verificarColisionesHorario()) {
                alert('Se ha detectado una colisión de horarios. Por favor, ajuste los horarios.');
                $horarioItem.find('input[name="horaInicio[]"], input[name="horaFin[]"]').val('');
            }
        }
    });

    $('#materiaForm').submit(function (e) {
        e.preventDefault();
        const nombreMateria = $('#nombreMateria').val();
        const grupoMateria = $('#grupoMateria').val();
        const horarioItems = $('.horario-item').map(function () {
            const dia = $(this).find('select[name="diaSemana[]"]').val();
            const horaInicio = $(this).find('input[name="horaInicio[]"]').val();
            const horaFin = $(this).find('input[name="horaFin[]"]').val();
            return `${dia}: ${horaInicio} - ${horaFin}`;
        }).get().join(', ');
        if (nombreMateria && horarioItems) {
            const data = {
                nombre: nombreMateria,
                grupo: grupoMateria,
                horarios: horarioItems,
                docente: $("#id_docente").val(),
                carrera: $("#selectorCarrera").val(),
                ciclo: $("#selectorCicloEscolar").val(),
                plantel: $("#selectorPlantel").val()
            };
            crearPeticion(urlAPI, {case: "agregar_materia", data: $.param(data)});
        }
    });
}

// Función para verificar colisiones entre todas las materias
function verificarColisionesHorario() {
    // Obtenemos todos los horarios
    const horarios = [];

    // Iteramos sobre cada uno de los elementos de la fila de horarios
    $('input[name="horaInicio[]"]').each(function (index) {
        const horaInicio = $(this).val();
        const horaFin = $('input[name="horaFin[]"]').eq(index).val();
        const diaSemana = $('select[name="diaSemana[]"]').eq(index).val();

        // Si tenemos los valores completos, los añadimos a la lista de horarios
        if (horaInicio && horaFin && diaSemana) {
            horarios.push({diaSemana, horaInicio, horaFin});
        }
    });

    // Verificamos si hay colisiones
    for (let i = 0; i < horarios.length; i++) {
        for (let j = i + 1; j < horarios.length; j++) {
            const horario1 = horarios[i];
            const horario2 = horarios[j];

            // Comprobamos si los horarios son del mismo día
            if (horario1.diaSemana === horario2.diaSemana) {
                // Convertimos las horas de inicio y fin a minutos para facilitar la comparación
                const inicio1 = convertirAHoraMinutos(horario1.horaInicio);
                const fin1 = convertirAHoraMinutos(horario1.horaFin);
                const inicio2 = convertirAHoraMinutos(horario2.horaInicio);
                const fin2 = convertirAHoraMinutos(horario2.horaFin);

                // Verificamos si las horas se solapan
                if ((inicio1 < fin2 && fin1 > inicio2)) {
                    return true; // Se ha detectado una colisión
                }
            }
        }
    }

    return false; // No hay colisión
}

// Función para convertir una hora (HH:mm) a minutos desde la medianoche
function convertirAHoraMinutos(hora) {
    const [horas, minutos] = hora.split(':').map(num => parseInt(num, 10));
    return horas * 60 + minutos;
}

function crearTablaMaterias(materias) {
    var tabla = $('<table>', {class: 'table align-middle text-nowrap', id: 'tabMaterias'});
    var thead = $('<thead>');
    var tr = $('<tr>');
    tr.append('<th scope="col">Nombre</th>');
    tr.append('<th scope="col">Grupo</th>');
    tr.append('<th scope="col">Carrera</th>');
    tr.append('<th scope="col">Plantel</th>');
    tr.append('<th scope="col">Ciclo escolar</th>');
    tr.append('<th scope="col">Horario</th>');
    thead.append(tr);
    var tbody = $('<tbody>', {class: 'border-top'});
    $.each(materias, function (nombreMateria, infoMateria) {
        const horariostr = JSON.stringify(infoMateria.horarios).replace(/"/g, "'");
        const horario = `
<a href="javascript:void(0)" 
   onclick="verHorario(${horariostr}, '${nombreMateria}')" 
   class="link-primary"
   data-bs-toggle="tooltip"
   data-bs-placement="top"
   aria-label="View Details"
   data-bs-original-title="Ver detalles">
    <i class="ti ti-clock fs-7"></i>
</a>`;
        var tr = $('<tr>');
        tr.append(`<td><p class="text-dark mb-0 fw-normal">${nombreMateria}</p></td>`);
        tr.append(`<td><p class="text-dark mb-0 fw-normal">${infoMateria.grupo}</p></td>`);
        tr.append(`<td><p class="text-dark mb-0 fw-normal">${infoMateria.carrera}</p></td>`);
        tr.append(`<td><p class="text-dark mb-0 fw-normal">${infoMateria.plantel}</p></td>`);
        tr.append(`<td><p class="text-dark mb-0 fw-normal">${infoMateria.ciclo_escolar}</p></td>`);
        tr.append(`<td>${horario}</td>`);
        tbody.append(tr);
    });
    tabla.append(thead);
    tabla.append(tbody);
    $('#tabMateriasContent').append(tabla);
    crearDataTable('#tabMaterias');
}

function verHorario(horarios, nombreMateria) {
    $("#horariosContainer").empty();
    $("#nombreMateriaModal").text(nombreMateria);
    if (Array.isArray(horarios) && horarios.length > 0) {
        let horariosHTML = `<table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Día</th>
                        <th scope="col">Hora inicio</th>
                        <th scope="col">Hora fin</th>
                    </tr>
                </thead>
                <tbody>`;
        $.each(horarios, function (index, horario) {
            horariosHTML += `
                    <tr>
                        <td>${horario.dia_semana}</td>
                        <td>${horario.hora_inicio}</td>
                        <td>${horario.hora_fin}</td>
                    </tr>`;
        });
        horariosHTML += `</tbody></table>`;
        $("#horariosContainer").html(horariosHTML);
    } else {
        $("#horariosContainer").html('<p>No hay horarios disponibles para esta materia.</p>');
    }
    $("#modalHorarios").modal("show");
}