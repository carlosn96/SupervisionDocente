let apiURL = "api/DocenteAPI.php";

let contadorMaterias = 0;

function ready() {
    $(document).ready(function () {
        agregarEventosMateria();
        $('#profesorForm').submit(function (event) {
            event.preventDefault();
            let carrera = $("#selectorCarrera").find('option:selected');
            let plantel = $("#selectorPlantel").find('option:selected');
            var elementosMateria = $("input[name^='materias']");
            if (elementosMateria.length > 0) {
                crearPeticion(apiURL, {
                    case: "guardar_docente_materias",
                    data: $(this).serialize() + "&id_carrera=" + carrera.val() + "&id_plantel=" + plantel.val()
                });
            } else {
                mostrarMensajeAdvertencia("Agrega al menos una materia", false);
            }
        });

        $('#nombre, #apellido').on('input', function () {
            const nombre = removerAccentos($('#nombre').val().trim().toLowerCase().replace(/ /g, ''));
            const apellidos = removerAccentos($('#apellido').val().trim().toLowerCase().split(' ')[0]);
            if (nombre && apellidos) {
                const correoUsuario = `${nombre}.${apellidos}`;
                $('#correo').val(correoUsuario);
            }
        });

        recuperarCarreras(function () {
            let carrera = $("#selectorCarrera").find('option:selected');
            let plantel = $("#selectorPlantel").find('option:selected');
            const data = {
                case: "recuperar_docentes",
                data: "id_carrera=" + carrera.val() + "&id_plantel=" + plantel.val()
            };
            $("#nombreCarrera").text(carrera.text() + " del Plantel " + plantel.text());
            crearPeticion(apiURL, data, function (res) {
                //print(res);
                crearListaProfesores(JSON.parse(res));
            });
        });
    });
}

function agregarEventosMateria() {
    let contadorMaterias = 0;
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
            if (!verificarColisionesMateriaActual()) {
                $('#materiasContainer').append(`
                    <div class="materia-item border rounded p-3 mb-3">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Materia</label>
                            <input type="text" class="form-control" name="materias[${contadorMaterias}][nombre]" value="${nombreMateria}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Grupo</label>
                            <input type="text" class="form-control" name="materias[${contadorMaterias}][grupo]" value="${grupoMateria}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Horario</label>
                            <textarea class="form-control" name="materias[${contadorMaterias}][horario]" rows="2" readonly>${horarioItems}</textarea>
                        </div>
                        <button type="button" class="btn btn-danger remove-materia">Eliminar Materia</button>
                    </div>
                `);

                $('#materiaModal').modal('hide');
                $('#materiaForm')[0].reset();
                $('#horarioContainer').html(`
                    <div class="horario-item d-flex align-items-center mb-2">
                        <select class="form-select me-2" name="diaSemana[]" required>
                            <option value="">Seleccione un día</option>
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miércoles">Miércoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                            <option value="Sábado">Sábado</option>
                        </select>
                        <input type="time" class="form-control me-2" name="horaInicio[]" required>
                        <span class="me-2">a</span>
                        <input type="time" class="form-control me-2" name="horaFin[]" required>
                        <button type="button" class="btn btn-danger remove-horario">Eliminar</button>
                    </div>
                `);
                contadorMaterias++;
            } else {
                alert('Se ha detectado una colisión de horarios con las materias existentes. Por favor, ajuste los horarios.');
            }
        }
    });
    // Agregar nueva línea de horario
    $('#agregarHorarioBtn').click(function () {
        // Create a new horario-item and append it to #horarioContainer
        var newHorarioItem = `
            <div class="horario-item row align-items-center mb-2">
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                    <select class="form-select" name="diaSemana[]" required>
                        <option value="Lunes">Lunes</option>
                        <option value="Martes">Martes</option>
                        <option value="Miércoles">Miércoles</option>
                        <option value="Jueves">Jueves</option>
                        <option value="Viernes">Viernes</option>
                        <option value="Sábado">Sábado</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-2 mb-md-0">
                    <input type="time" class="form-control" name="horaInicio[]" required>
                </div>
                <div class="col-12 col-md-1 text-center mb-2 mb-md-0">
                    <span>a</span>
                </div>
                <div class="col-12 col-md-2 mb-2 mb-md-0">
                    <input type="time" class="form-control" name="horaFin[]" required>
                </div>
                <div class="col-12 col-md-2 text-center mb-2 mb-md-0">
                    <button type="button" class="btn btn-danger w-100 remove-horario">Eliminar</button>
                </div>
            </div>`;

        $('#horarioContainer').append(newHorarioItem);
    });
    // Validar hora de término y colisiones
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
            if (verificarColisionesMateriaActual()) {
                alert('Se ha detectado una colisión de horarios. Por favor, ajuste los horarios.');
                $horarioItem.find('input[name="horaInicio[]"], input[name="horaFin[]"]').val('');
            }
        }
    });
    // Eliminar una línea de horario
    $(document).on('click', '.remove-horario', function () {
        $(this).closest('.horario-item').remove();
    });
    // Eliminar materia
    $(document).on('click', '.remove-materia', function () {
        $(this).closest('.materia-item').remove();
    });
}
// Función para verificar colisiones entre todas las materias
function verificarColisionesMateriaActual() {
    const horarios = [];
    let hayColision = false;
    // Recoger todos los horarios de las materias ya agregadas
    $('.materia-item textarea[name^="materias"]').each(function () {
        const textoHorarios = $(this).val().split(', ');
        textoHorarios.forEach(texto => {
            const [dia, horas] = texto.split(': ');
            const [horaInicio, horaFin] = horas.split(' - ');
            horarios.push({diaSemana: dia, horaInicio, horaFin});
        });
    });
    // Añadir los horarios actuales
    $('#horarioContainer .horario-item').each(function () {
        const diaSemana = $(this).find('select[name="diaSemana[]"]').val();
        const horaInicio = $(this).find('input[name="horaInicio[]"]').val();
        const horaFin = $(this).find('input[name="horaFin[]"]').val();

        if (diaSemana && horaInicio && horaFin) {
            horarios.push({diaSemana, horaInicio, horaFin});
        }
    });
    // Verificar colisiones entre todos los horarios
    for (let i = 0; i < horarios.length; i++) {
        for (let j = i + 1; j < horarios.length; j++) {
            if (horarios[i].diaSemana === horarios[j].diaSemana) {
                if ((horarios[i].horaInicio < horarios[j].horaFin && horarios[i].horaFin > horarios[j].horaInicio)) {
                    hayColision = true;
                    break;
                }
            }
        }
        if (hayColision)
            break;
    }

    return hayColision;
}

function crearListaProfesores(data) {
    const accordion = $('#listaMateriasContainer');
    accordion.empty();
    if (Object.keys(data).length) {
        $.each(data, function (nombre, detalles) {
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
            $.each(detalles.materias, function (materia, info) {
                const listItem = $(`
                <li class="list-group-item">
                    <strong>${materia}</strong>:
                    <ul>
                        ${info.horarios.map(horario => `<li>${horario.dia_semana}: ${horario.hora_inicio} - ${horario.hora_fin}</li>`).join('')}
                    </ul>
                </li>
            `);
                listGroup.append(listItem);
            });
            card.append(header, collapse);
            accordion.append(card);
        });
    } else {
        accordion.append($(`<div class="alert alert-warning" role="alert">
                    No se han agregado docentes
                  </div>`));
    }
}


function verificarExisteMateria(inputNombre) {
    var elementosMateria = $("input[name^='materias']");
    var existeMateria = false;
    var nombre = inputNombre.value;
    elementosMateria.each(function () {
        if ((existeMateria = nombre === $(this).val())) {
            return; // Salir del bucle each ya que ya se encontró la materia
        }
    });
    if (existeMateria) {
        mostrarMensajeAdvertencia("Una materia con el nombre '" + nombre + "' ya ha sido agregada", false);
        inputNombre.value = "";
    }
}