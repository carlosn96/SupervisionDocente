
function enviarFormulario(idFormulario, urlAPI, submitCase, fnSuccess = mostrarMensajeResultado) {
    $(!idFormulario.startsWith('#') ? '#' + idFormulario : idFormulario).submit(function (e) {
        e.preventDefault();
        crearPeticion(urlAPI, {case: submitCase, data: $(this).serialize()}, fnSuccess, "json");
    });
}

function crearPeticion(url, data, fnSuccess = mostrarMensajeResultado, dataType = "text", contentType = 'application/x-www-form-urlencoded') {
    const isFormData = data instanceof FormData;
    if (isFormData) {
        contentType = false;
    }
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: fnSuccess,
        dataType: dataType,
        contentType: contentType,
        processData: !isFormData, // Evitar que jQuery convierta los datos en una cadena si es FormData
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud:', errorThrown);
            if (jqXHR.status === 0) {
                mostrarMensajeError("Sesión caducada");
            } else if (jqXHR.status === 404) {
                mostrarMensajeAdvertencia('Solicitud denegada: recurso'+url+' no encontrado');
            } else if (jqXHR.status === 500) {
                mostrarMensajeError(JSON.stringify(jqXHR));
            } else if (textStatus === 'parsererror') {
                mostrarMensajeError("Error en la presentación de datos " + errorThrown);
            } else if (textStatus === 'timeout') {
                mostrarMensajeError('Time out error.');
            } else if (textStatus === 'abort') {
                mostrarMensajeError('Petición abortada');
            } else {
                mostrarMensajeError('Uncaught Error: ' + jqXHR.responseText);
            }
        }
    });
}


function insertarAlerta(id, mensaje, type = "danger") {
    $(id).addClass("alert alert-" + type);
    $(id).prop("role", "alert");
    $(id).html(mensaje);
}

function getRandomColor() {
    let colors = ["warning", "info", "success", "danger", "primary", "secondary", "dark"];
    return colors[Math.floor(Math.random() * colors.length)];
}

function mostrarMensajeResultado(result) {
    var mensajeRespuesta = typeof result === "string" ? JSON.parse(result) : result;
    if (mensajeRespuesta.es_valor_error) {
        mostrarMensajeError(mensajeRespuesta.mensaje);
    } else {
        mostrarMensajeOk(mensajeRespuesta.mensaje);
    }
}

function mostrarMensajeOk(msg = "OK", reloading = true) {
    return mostrarMensajeReload("Operación completa", "success", msg, reloading);
    //location.reload();
}

function mostrarMensajeError(msg = "No se ha podido completar la operacion ...", reloading = true) {
    return mostrarMensajeReload("Error", "error", msg, reloading);
}

function mostrarMensajeAdvertencia(msg, reloading = true) {
    return mostrarMensajeReload("Atención", "warning", msg, reloading);
}

function mostrarMensajeInfo(msg, reloading = true) {
    return mostrarMensajeReload("Información:", "info", msg, reloading);
}

function mostrarMensajeThen(title, type, msg, then, moreOptions = {}) {
    return mostrarMensaje(title, type, msg, moreOptions).then((respuestaOK) => {
        if (respuestaOK) {
            then();
        }
    });
}

function mostrarMensajeReload(title, type, msg, reloading = true) {
    var reload = reloading ? function () {
        refresh();
    } : function () {
    };
    return mostrarMensajeThen(title, type, msg, reload);
}

function mostrarAlertaOpciones(msg, fn) {
    mostrarMensajeThen("¿Estás seguro?", "warning", msg, fn,
            {dangerMode: true, buttons: ['No, cancelar', 'Sí']});
}

function mostrarMensaje(title, type, msg, moreOptions) {
    var options = {
        title: title,
        text: msg,
        icon: type,
        closeOnClickOutside: false
    };
    $.each(moreOptions, function (i, val) {
        options[i] = val;
    });
    return swal(options);
}

function redireccionar(url) {
    window.location = url;
}

function refresh() {
    location.reload();
}

function crearBotonMenuDesplegable(title, enlaces, color) {
    var links = "";
    enlaces.forEach(function (link) {
        if (link.button) {
            links += link.modal;
        } else {
            links += "<a class='dropdown-item' href='" + link.url + "'>" + link.titulo + "</a>";
        }
    });
    return "<div class='btn-group'>" +
            "<button id='group' type='button' class='btn btn-round btn-sm btn-outline-" + color + " dropdown-toggle' data-bs-toggle='dropdown' aria-expanded='false'>" +
            title +
            "</button>" +
            "<ul class='dropdown-menu'>" +
            links +
            "</ul>" +
            "</div>";
}

function crearBoton(title, clase, icono, value, id, action) {
    return "<button type='button' data-toggle='tooltip' title='" + title + "' " +
            "class='" + clase + "' data-original-title='" + title + "' value='" + value + "' onclick='" + action + "' id='" + id + "'>" +
            "<span class='btn-label'><i class='" + icono + "'></i></span> " + title + "</button>";
}

function crearBotonIcon(title, clase, icono, value, id, action) {
    return "<button type='button' data-toggle='tooltip' title='" + title + "' " +
            "class='" + clase + "' data-original-title='" + title + "' value='" + value + "' onclick='" + action + "' id='" + id + "'>" +
            "<i class='" + icono + "'></i>" + title + "</button>";
}

function crearBotonEliminar(opcionesPeticion) {
    var idRegistro = opcionesPeticion.idRegistro;
    var tituloBoton = opcionesPeticion.tituloBoton ?
            opcionesPeticion.tituloBoton : "";
    return crearBoton(
            tituloBoton,
            opcionesPeticion.clase ? opcionesPeticion.clase : "btn btn-outline-danger btn-raised btn-sm btn-rounded",
            "far fa-trash-alt",
            idRegistro, "btnEliminar" + idRegistro, "alertaEliminar(" + JSON.stringify(opcionesPeticion) + ")");
}

function alertaEliminar(opcionesPeticion) {
    var msg = opcionesPeticion.mensajeAlerta;
    var url = opcionesPeticion.url;
    var data = opcionesPeticion.data;
    mostrarAlertaOpciones(msg, function () {
        //crearPeticion(url, data, print);
        crearPeticion(url, data);
    });
}

function crearColumnaTablaCentrada(value, clase = "") {
    return crearColumnaTabla(value, "text-center " + clase);
}
function crearColumnaTabla(value, clase = "") {
    return "<td class='" + clase + "'>" + value + "</td>";
}

function crearColumnaHeaderTabla(value, clase = "") {
    return "<th class='" + clase + "'>" + value + "</th>";
}

function toMoneda(numero) {
    //return numeral(numero).format('$ 0,0.00');
    return numero.length !== 0 ? Intl.NumberFormat("es-MX", {style: "currency", currency: "MXN", minimunFractionDigits: 2}).format(numero) : "";
}

function deshabilitarHabilitarInput(input) {
    var attr = "disabled";
    if (input.prop(attr)) {
        input.removeAttr(attr);
    } else {
        input.attr(attr, attr);
    }
}

function crearDataTable(idTabla) {
    // Destroy the existing DataTable if it exists
    if ($.fn.DataTable.isDataTable(idTabla)) {
        $(idTabla).DataTable().clear().destroy();
    }

    // Initialize the DataTable with the specified options
    var tabla = $(idTabla).DataTable({
        retrieve: true,
        responsive: true,
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        order: [[0, "asc"]],
        lengthMenu: [
            [-1, 5, 10, 25, 50],
            ["Mostrar todo", 5, 10, 25, 50]
        ],
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty: "Sin registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sInfoPostFix: "",
            sSearch: "Filtrar:",
            searchPlaceholder: "",
            sInfoThousands: ",",
            sLoadingRecords: "Cargando...",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            },
            pagingType: "scrolling"
        }
    });

    return tabla;
}
function calcuarEdad(fechaNacimiento) {
    return parseInt((new Date() - new Date(fechaNacimiento)) /
            (1000 * 60 * 60 * 24 * 365));
}

function extraerFechaHora(input, fechaHora) {
    var strFechaHora = fechaHora.split(" ");
    $("#fecha" + input).val(strFechaHora[0]);
    $("#hora" + input).val(strFechaHora[1]);
}

function getFechaActual(sp = "-") {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();
    if (dd < 10)
        dd = '0' + dd;
    if (mm < 10)
        mm = '0' + mm;
    return (yyyy + sp + mm + sp + dd);
}

function formatDate(date) {
    var month = '' + (date.getMonth() + 1),
            day = '' + date.getDate(),
            year = date.getFullYear();

    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;
    return [year, month, day].join('-');
}

function getFechaHoraActual() {
    return getFechaActual() + "T" + (new Date().toLocaleTimeString());
}


function habilitarDeshabilitarCampoBinario(inputRadioName, componenteName) {
    var inputRadio = $("input[name=" + inputRadioName + "]");
    var componente = $("#" + componenteName);
    inputRadio.on("change", function () {
        componente.attr("disabled", !Boolean(Number($(this).val())))
    });
}

function domicilioStr(domicilio) {
    var asentamiento;
    if (domicilio.calle !== null && domicilio.calle.length > 0) {
        asentamiento = domicilio.asentamiento;
        return " Calle " + domicilio.calle + " " + asentamiento.nombre +
                " (" + asentamiento.tipoAsentamiento.text + ") " + asentamiento.municipio.text + " " + asentamiento.estado.text;
    } else {
        return "Sin domicilio";
    }
}

function checkRadio(radio) {
    radio.attr("checked", "checked");
}

function elegirOpcionesMultipleSelect(input, values) {
    $.each(values, function (i, e) {
        $("#" + input + " option[value='" + e.value + "']").prop("selected", true);
    });
}

function separarDigitos() {
    $(this).val($.trim($(this).val().replace(/ /g, "").replace(/(\d{2})/g, '$1 ')));
}

function firstUpperCase(str, idxStart = 0) {
    var idx = -1;
    var isUpperCase = function (char) {
        return char === char.toUpperCase(char);
    };
    for (var i = idxStart, len = str.length; i < len; i++) {
        if (isUpperCase(str[i])) {
            idx = i;
            break;
        }
    }
    return idx;
}

function separarPalabras(str) {
    var cadena = str.substring(0, firstUpperCase(str, 1));
    return cadena + " " + str.substring(cadena.length);
}

function descargarTablaCSV(idTabla, nombreArchivo) {
    $("#" + idTabla).first().table2csv({"filename": nombreArchivo + ".csv"});
}

function descargarTablaXls(tabla, nombreLibro) {
    let fecha = getFechaActual();
    Exporter.export(tabla, nombreLibro + "_" + fecha + ".xls", fecha);
}


function print(res) {
    console.log(res);
}

function llenar_inputs_form(obj) {
    $.each(obj, function (i, v) {
        let input = $("#" + i);
        if (input.length) {
            input.val(v);
        }
    });
}


function crearOpcionSelector(selector, val, text) {
    selector.append($('<option>', {
        value: val,
        text: text
    }));
}

function construirInputChecbox(id, value, name, text, checked = false) {
    return `
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="${id}" value="${value}" name="${name}[]" ${checked ? "checked" : ""}>
            <label class="form-check-label" for="${id}">
                ${text}
            </label>
        </div>
    `;
}

function crearCheckboxes(idGrupo, valores, nombre, value = "str") {
    const container = $('#' + idGrupo);
    container.empty();
    const searchHtml = `
            <div class="mb-3">
                <input type="text" class="form-control" id="searchCheckboxes" placeholder="Buscar ${nombre} ...">
            </div>`;
    container.append(searchHtml);
    const checkboxesHtml = `
            <div class="grupo-${nombre}-scroll" style="max-height: 300px; overflow-y: auto;">
                <!-- Aquí se agregarán los checkboxes -->
            </div>`;
    container.append(checkboxesHtml);
    const checkboxesContainer = container.find('.grupo-' + nombre + '-scroll');

    valores.forEach((element) => {
        let keys = Object.keys(element);
        const checkboxId = nombre + (element[keys[0]]);
        const checkboxValue = value === "str" ? (element[keys[1]]) : element[keys[0]];
        const checkboxHtml = construirInputChecbox(checkboxId, checkboxValue, nombre, element[keys[1]]);
        checkboxesContainer.append(checkboxHtml);
    });

    $('#searchCheckboxes').on('input', function () {
        const searchTerm = $(this).val().toLowerCase().trim();
        checkboxesContainer.find('.form-check').each(function () {
            const checkboxLabel = $(this).find('.form-check-label').text().toLowerCase().trim();

            if (checkboxLabel.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
}

function removerAccentos(str) {
    return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}



function getRootUrl() {
    return `${window.location.protocol}//${window.location.host}/supervision_docente/`;
}