var urlAPI = "api/DocenteAPI.php";

function ready() {
    var idDocente = Number($("#id_docente").val());
    if (idDocente) {
        recuperarMaterias(idDocente);
        $(document).on('click', '.btn-eliminar-horario', function () {
            const horarioId = $(this).data('horario-id');
            $(`#horario_${horarioId}`).remove();
        });
    } else {
        redireccionar("../docentes");
    }
}



function recuperarMaterias(idDocente) {

    crearPeticion(urlAPI, {case: "recuperar_materias", data: "idDocente=" + idDocente}, function (docente) {
        print(Object.keys(docente)[0]);
        $("#nombreProfesor").append(Object.keys(docente)[0]);
        Object.keys(docente).forEach(function (docenteNombre) {
            const datosDocente = docente[docenteNombre];
            let accordionsHtml = '';

            // Iterar sobre las materias del docente
            Object.keys(datosDocente.materias).forEach(function (materiaNombre, index) {
                let materia = datosDocente.materias[materiaNombre];
                let idMateria = materia.id;
                let horariosHtml = '';

                materia.horarios.forEach(function (horario, hIndex) {
                    horariosHtml += `
            <div class="input-group mb-2" id="horario_${horario.id_horario}">
                <button onclick="actualizarHorario(${horario.id_horario})" class="btn btn-sm btn-outline-primary" type="button">
                    <i class="ti ti-refresh"></i>
                </button>
                <select class="form-select me-2" name="materias[${index}][horarios][${hIndex}][dia_semana]" required>
                    <option value="Lunes" ${horario.dia_semana === 'Lunes' ? 'selected' : ''}>Lunes</option>
                    <option value="Martes" ${horario.dia_semana === 'Martes' ? 'selected' : ''}>Martes</option>
                    <option value="Miércoles" ${horario.dia_semana === 'Miércoles' ? 'selected' : ''}>Miércoles</option>
                    <option value="Jueves" ${horario.dia_semana === 'Jueves' ? 'selected' : ''}>Jueves</option>
                    <option value="Viernes" ${horario.dia_semana === 'Viernes' ? 'selected' : ''}>Viernes</option>
                    <option value="Sábado" ${horario.dia_semana === 'Sábado' ? 'selected' : ''}>Sábado</option>
                </select>
                <input type="time" class="form-control me-2" name="materias[${index}][horarios][${hIndex}][hora_inicio]" value="${horario.hora_inicio}" required>
                <input type="time" class="form-control" name="materias[${index}][horarios][${hIndex}][hora_fin]" value="${horario.hora_fin}" required>
                <button class="btn btn-sm btn-danger ms-2" type="button" onclick="eliminarHorario(${horario.id_horario})">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
            `;
                });
                accordionsHtml += `
                <div class="accordion-item">
    <h2 class="accordion-header" id="heading${index}">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="true" aria-controls="collapse${index}">
            ${materiaNombre} (${materia.total_horas} horas)
        </button>
        
    </h2>
    <div id="collapse${index}" class="accordion-collapse collapse" aria-labelledby="heading${index}" data-bs-parent="#accordionExample">
        <div class="accordion-body">
            <input hidden type="text" class="form-control" id="idMateria${index}" value="${idMateria}" required>
            
            <div class="input-group mb-3">
                <button onclick="actualizarNombreMateria(${idMateria}, 'materia_${index}_nombre')" class="btn btn-sm btn-outline-primary" type="button">
                    <i class="ti ti-refresh"></i>
                </button>
                <input type="text" class="form-control" id="materia_${index}_nombre" name="materias[${index}][nombre]" value="${materiaNombre}" required>
            </div>
            <div class="input-group mb-3">
                <button onclick="actualizarGrupoMateria(${idMateria}, 'materia_${index}_grupo')" class="btn btn-sm btn-outline-primary" type="button">
                    <i class="ti ti-refresh"></i>
                </button>
                <input type="text" class="form-control" id="materia_${index}_grupo" name="materias[${index}][grupo]" value="${materia.grupo}" required>
            </div>
            
            <button type="button" id="btnHorario_${idMateria}_${index}" class="btn btn-sm btn-success mb-2" onclick='agregarHorario(${JSON.stringify({id: idMateria, index: index})})'>
                <i class="ti ti-plus"></i>
            </button>

<label class="form-label">Horarios</label>
            <div id="horarios_${index}">
                ${horariosHtml}
            </div>
        </div>
    </div>
</div>
        `;

            });

            $("#materias-list").html(`
    <div class="accordion" id="accordionExample">
        ${accordionsHtml}
    </div>
    `);
        });

    }, "json");
}


let horarioCounter = 0;

function agregarHorario(data) {
    var index = data.index;
    data.counter = ++horarioCounter;

    const nuevoHorarioHtml = `
                <div class="input-group mb-2" id="horario_${horarioCounter}">
                    <button class="btn btn-sm btn-outline-primary" type="button" onclick='agregarNuevoHorario(${JSON.stringify(data)})'>
                        <i class="ti ti-check"></i>
                    </button>
                    <select class="form-select me-2" name="materias[${index}][horarios][${horarioCounter}][dia_semana]" required>
                        <option value="Lunes">Lunes</option>
                        <option value="Martes">Martes</option>
                        <option value="Miércoles">Miércoles</option>
                        <option value="Jueves">Jueves</option>
                        <option value="Viernes">Viernes</option>
                        <option value="Sábado">Sábado</option>
                    </select>
                    <input type="time" class="form-control me-2" name="materias[${index}][horarios][${horarioCounter}][hora_inicio]" required>
                    <input type="time" class="form-control" name="materias[${index}][horarios][${horarioCounter}][hora_fin]" required>
                    <button class="btn btn-sm btn-danger ms-2 btn-eliminar-horario" type="button" data-horario-id="${horarioCounter}">
                        <i class="ti ti-minus"></i>
                    </button>
                </div>
            `;

    $(`#horarios_${index}`).append(nuevoHorarioHtml);
}


function agregarMateria() {

}

function eliminarMateria(id) {
    print(id);
}

function eliminarHorario(id) {
    alertaEliminar({
        mensajeAlerta: "Eliminar horario",
        url: urlAPI,
        data: {"case": "eliminar_horario", "data": "id=" + id}
    });
}

function actualizarNombreMateria(id, input) {
    enviarPeticion("actualizar_nombre_materia", input, id);
}

function actualizarGrupoMateria(id, input) {
    enviarPeticion("actualizar_grupo_materia", input, id);
}

function actualizarHorario(idHorario) {
    var $horarioContainer = $(`#horario_${idHorario}`);
    var diaSemana = $horarioContainer.find('select').val();
    var horaInicio = $horarioContainer.find('input[name$="[hora_inicio]"]').val();
    var horaFin = $horarioContainer.find('input[name$="[hora_fin]"]').val();
    crearPeticion(urlAPI, {case: "actualizar_horario", data: $.param({
            id_horario: idHorario,
            dia_semana: diaSemana,
            hora_inicio: horaInicio,
            hora_fin: horaFin
        })});
}

function agregarNuevoHorario(data) {
    var $horarioContainer = $(`#horario_${data.counter}`);
    var diaSemana = $horarioContainer.find('select').val();
    var horaInicio = $horarioContainer.find('input[name$="[hora_inicio]"]').val();
    var horaFin = $horarioContainer.find('input[name$="[hora_fin]"]').val();

    crearPeticion(urlAPI, {case: "agregar_horario", data: $.param({
            id_materia: data.id,
            dia_semana: diaSemana,
            hora_inicio: horaInicio,
            hora_fin: horaFin
        })});
}

function enviarPeticion(caso, input, id) {
    crearPeticion(urlAPI, {case: caso, data: $.param({
            id: id,
            val: $("#" + input).val()
        })});
}

