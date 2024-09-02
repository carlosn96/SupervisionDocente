var urlAPI = "api/HorarioAPI.php";

function ready() {
    recuperarCarreras(construirTabla);
    ajustarEventos();
}


function ajustarEventos() {
    $("input[name=tipoHorario]").change(construirTabla);
}

function construirTabla() {
    let data = {
        carrera: $("#selectorCarrera").find('option:selected').val(),
        plantel: $("#selectorPlantel").find('option:selected').val(),
        tipoHorario: $("input[name=tipoHorario]:checked").val()
    };
    crearPeticion(urlAPI, {case: "obtener_lista_elementos", data: $.param(data)}, function (lista) {
        print(lista);
        let tipoElemento = Object.keys(lista)[0];
        const table = $('<table>', {id: "tablaHorario"}).addClass('table table-striped table-responsive');
        const thead = $('<thead>').append(
                $('<tr>').append(
                $('<th>').text(tipoElemento)
                )
                );
        const tbody = $('<tbody>');
        lista[tipoElemento].forEach(item => {
            const row = $('<tr>').append(
                    $('<td>').html($("<a>", {href: `javascript:verHorario("${tipoElemento}", "${item.id}")`, text: item.text, class: "btn btn-link"}))
                    );
            tbody.append(row);
        });

        table.append(thead).append(tbody);
        $('#tabla').html(table);
        crearDataTable("#tablaHorario");
    }, "json");
}


function construirBotones() {
    return "botones";
}

function verHorario(tipo, id) {
    let data = {
        tipo: tipo,
        id: id,
        carrera: $("#selectorCarrera").find('option:selected').val(),
        plantel: $("#selectorPlantel").find('option:selected').val()
    };
    crearPeticion(urlAPI, {case: "recuperar_horario", data: $.param(data)}, function (res) {
        print(res);
        redireccionar("../verHorario");
    });
}