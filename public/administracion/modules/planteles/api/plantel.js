let urlAPI = "api/PlantelAPI.php";

function ready() {
    listarPlanteles();
    $("#agregarPlantelForm").submit(function (e) {
        e.preventDefault();
        crearPeticion(urlAPI, {"case": "agregar", "data": $(this).serialize()}, print);
    });
}

function listarPlanteles() {
    crearPeticion(urlAPI, {case: "recuperar_campos"}, function (res) {
        const resObj = JSON.parse(res);
        const planteles = resObj.planteles;
        const horarios = resObj.horarios;
        let html = "";
        planteles.forEach(function (plantel) {
            const p = JSON.stringify(plantel);
            const btnsMenuPlantel = [
                {"url": "javascript:editarPlantel(" + p + ")", "titulo": "<i class='ti ti-edit'></i> Editar"},
                {"url": "javascript:eliminar(" + p + ")", "titulo": "<i class='ti ti-trash'></i>Eliminar"}
            ];
            html += "<tr>";
            html += crearColumnaTabla(plantel.nombre);
            html += crearColumnaTabla(plantel.director);
            html += crearColumnaTablaCentrada(crearBotonMenuDesplegable("Acciones", btnsMenuPlantel, "primary"));
            html += "</tr>";
        });
        $("#tbodyPlanteles").html(html);
        crearDataTable($("#tablaPlanteles"));
        construirTurnos(horarios);
    });
}

function editarPlantel(plantel) {
    $('#modalEditarPlantelLabel').text('Editar "' + plantel.nombre + '"');
    $('#nombrePlantel').val(plantel.nombre);
    $('#idPlantel').val(plantel.id_plantel);
    $('#directorPlantel').val(plantel.director);
    $('#modalEditarPlantel').modal('show');
    print(plantel);
    enviarFormulario('#formEditarPlantel', urlAPI, "editar_plantel");
}

function construirTurnos(turnos) {
    const container = $("#turnosContainer");
    container.empty();  // Limpiar contenido previo

    // Iterar sobre los turnos
    $.each(turnos, function (turno, turnoData) {
        const turnoCard = $("<div>").addClass("card mb-3");

        // Crear el checkbox para cada turno en la cabecera
        const checkboxDiv = $("<div>").addClass("form-check mb-2");
        const checkbox = $("<input>").attr({
            class: "form-check-input",
            type: "checkbox",
            id: `turno${turno}`,
            name: `turnos[${turno}][seleccionado]`,
            value: turno
        });
        const label = $("<label>").addClass("form-check-label")
                .attr("for", `turno${turno}`)
                .text(turno);
        checkboxDiv.append(checkbox).append(label);
        turnoCard.append($("<div>").addClass("card-header").append(checkboxDiv));

        // Crear el cuerpo de la tarjeta (inicialmente oculto)
        const cardBody = $("<div>").addClass("card-body").hide(); // Ocultamos el cardBody inicialmente

        // Contenedor para los campos de horario
        const horarioDiv = $("<div>").addClass("mt-3").attr("id", `turno${turno}Horario`);

        // Crear una fila con los campos en una sola línea: inicio, fin y duración
        const horarioRow = $("<div>").addClass("row g-3");

        // Hora de entrada
        const inicioDiv = $("<div>").addClass("col-12 col-md-4");
        inicioDiv.append($("<label>").attr("for", `${turno}_entrada`).text("Hora de entrada:"));
        inicioDiv.append($("<input>").attr({
            type: "time",
            class: "form-control",
            id: `${turno}_entrada`,
            name: `turnos[${turno}][hora_entrada]`,
            value: turnoData.entrada,
            disabled: true  // Deshabilitamos el campo por defecto
        }));

        // Hora de salida
        const finDiv = $("<div>").addClass("col-12 col-md-4");
        finDiv.append($("<label>").attr("for", `${turno}_salida`).text("Hora de salida:"));
        finDiv.append($("<input>").attr({
            type: "time",
            class: "form-control",
            id: `${turno}_salida`,
            name: `turnos[${turno}][hora_salida]`,
            value: turnoData.salida,
            disabled: true  // Deshabilitamos el campo por defecto
        }));

        // Duración del bloque
        const duracionDiv = $("<div>").addClass("col-12 col-md-4");
        duracionDiv.append($("<label>").attr("for", `${turno}_duracionBloque`).text("Duración del bloque (minutos):"));
        duracionDiv.append($("<input>").attr({
            type: "number",
            class: "form-control",
            id: `${turno}_duracionBloque`,
            name: `turnos[${turno}][duracion_bloque]`,
            value: turnoData.duracionBloque,
            disabled: true  // Deshabilitamos el campo por defecto
        }));

        // Agregar las columnas a la fila
        horarioRow.append(inicioDiv).append(finDiv).append(duracionDiv);
        horarioDiv.append(horarioRow);

        // Crear checkbox "¿Incluye descanso?"
        const descansoCheckDiv = $("<div>").addClass("form-check mt-3");
        const descansoCheckbox = $("<input>").attr({
            class: "form-check-input",
            type: "checkbox",
            id: `${turno}_descansoCheck`,
            checked: false  // Desmarcado por defecto
        });

        const descansoLabel = $("<label>").addClass("form-check-label")
                .attr("for", `${turno}_descansoCheck`)
                .text("¿Incluye descanso?");
        descansoCheckDiv.append(descansoCheckbox).append(descansoLabel);

        // Agregar el checkbox de descanso al contenedor de horarios
        horarioDiv.append(descansoCheckDiv);

        // Crear el div de descanso que solo se muestra si el usuario marca "¿Incluye descanso?"
        const descansoDiv = $("<div>").addClass("mt-3").attr("id", `${turno}_descansoDiv`).hide();
        descansoDiv.append($("<label>").attr("for", `${turno}_descanso`).text("Tiempo de descanso (minutos):"));
        descansoDiv.append($("<input>").attr({
            type: "number",
            class: "form-control",
            id: `${turno}_descanso`,
            name: `turnos[${turno}][descanso][tiempo]`,
            value: turnoData.duracionDescanso,
            disabled: true  // Deshabilitado desde el principio
        }));
        descansoDiv.append($("<label>").attr("for", `${turno}_descansoHora`).text("Hora de descanso:"));
        descansoDiv.append($("<input>").attr({
            type: "time",
            class: "form-control",
            id: `${turno}_descansoHora`,
            name: `turnos[${turno}][descanso][hora]`,
            value: turnoData.descansoHora,
            disabled: true  // Deshabilitado desde el principio
        }));

        // Agregar el div de descanso al contenedor de horarios
        horarioDiv.append(descansoDiv);

        cardBody.append(horarioDiv);
        turnoCard.append(cardBody);
        container.append(turnoCard);

        // 1. Event listener para el checkbox de turno (mostrar/ocultar el cardBody y habilitar campos)
        checkbox.on("change", function () {
            if ($(this).is(':checked')) {
                cardBody.show();
                // Solo habilitar los inputs principales del turno, no los de descanso
                cardBody.find("input").not(`#${turno}_descanso, #${turno}_descansoHora`).prop('disabled', false);
            } else {
                cardBody.hide();
                // Deshabilitar todos los inputs del turno, pero mantener deshabilitados los de descanso
                cardBody.find("input").prop('disabled', true);
            }
        });

        // 2. Event listener para el checkbox de descanso (mostrar/ocultar el div de descanso y habilitar campos)
        descansoCheckbox.on("change", function () {
            if ($(this).is(':checked')) {
                descansoDiv.show(); // Mostrar campos de descanso
                descansoDiv.find("input").prop('disabled', false); // Habilitar campos de descanso
            } else {
                descansoDiv.hide(); // Ocultar campos de descanso
                descansoDiv.find("input").prop('disabled', true); // Deshabilitar campos de descanso
            }
        });
    });
}
