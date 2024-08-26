var urlAPI = "api/CoordinadorAPI.php";
function ready() {

    crearPeticion(urlAPI, {case: "listar_coordinadores"}, function (res) {
        let $table = $('<table>', {class: 'table'});

        let $thead = $('<thead>').append(
                $('<tr>').append(
                $('<th>').text('Coordinador'),
                $('<th>').text('Carreras y Planteles')
                )
                );

        let $tbody = $('<tbody>');

        $.each(JSON.parse(res), function (index, coordinador) {
            const carrerasCoordina = coordinador.carreras_coordina
                    ? coordinador.carreras_coordina.split(',').map(function (carrera) {
                return carrera.trim();
            })
                    : [];
            const idCarrerasCoordina = coordinador.id_carreras_coordina
                    ? coordinador.id_carreras_coordina.split(',').map(function (id) {
                return id.trim();
            })
                    : [];
            let $selectCarreras = $('<select>', {
                name: "carrera",
                class: 'form-select',
                css: {'margin-left': '10px'},
                required: true,
                change: function () {
                    consultarPlanteles($(this).val(), $(this).closest('form').find('.select-planteles'));
                }
            }).append(
                    $('<option>', {value: '', text: 'Selecciona una carrera'})
                    );

            let $selectPlanteles = $('<select>', {
                name: "plantel",
                class: 'form-select select-planteles',
                css: {'margin-left': '10px'},
                required: true
            }).append(
                    $('<option>', {value: '', text: 'Selecciona un plantel'})
                    );

            carrerasCoordina.forEach(function (carrera, idx) {
                $selectCarreras.append($('<option>', {value: idCarrerasCoordina[idx], text: carrera}));
            });

            let $tr = $('<tr>');
            let $coordinadorCell = $('<td>').append(
                    $('<img>', {
                        src: coordinador.avatar,
                        alt: 'Avatar',
                        css: {
                            width: '50px',
                            height: '50px',
                            'border-radius': '50%',
                            'margin-right': '10px'
                        }
                    }),
                    `${coordinador.nombre} ${coordinador.apellidos}`
                    );
            let $form = $('<form>', {
                class: 'row g-3 align-items-center'
            }).append(
                    $('<input>', {hidden: true, value: coordinador.id_coordinador, name: "coordinador"}),
                    $('<div>', {class: 'col-auto'}).append($selectCarreras),
                    $('<div>', {class: 'col-auto'}).append($selectPlanteles),
                    $('<div>', {class: 'col-auto'}).append(
                    $('<button>', {
                        class: 'btn btn-sm btn-outline-primary',
                        type: 'submit'
                    }).text('Compartir agenda')
                    )
                    );
            $form.on('submit', consultarAgenda);
            $tr.append($coordinadorCell, $('<td>').append($form));
            $tbody.append($tr);
        });
        $table.append($thead, $tbody);
        $('#coordinador-tab').html($table);
    });

}

function consultarPlanteles(carreraId, $selectPlanteles) {
    $selectPlanteles.empty().append($('<option>', {value: '', text: 'Selecciona un plantel'}));
    if (carreraId) {
        crearPeticion(urlAPI, {case: "recuperar_listado_planteles_por_carrera", data: "carrera=" + carreraId}, function (res) {
            let planteles = JSON.parse(res);
            $.each(planteles, function (index, plantel) {
                $selectPlanteles.append($('<option>', {value: plantel.id_plantel, text: plantel.nombre}));
            });
        });
    }
}

function consultarAgenda(e) {
    e.preventDefault();
    let url = `${window.location.protocol}//${window.location.hostname}${window.location.hostname === "localhost" ? 
    "/supervision_docente" : ""}/public/share/modules/getAgenda/?${$(this).serialize()}`;
    $("#ligaCompartirAgenda").attr("href", url);
    var qr = new QRCode(document.getElementById("qrContainer"), {
        text: url,
        width: 200,
        height: 200
    });
    var canvas = $('#qrContainer canvas')[0];
    if (canvas) {
        var img = canvas.toDataURL("image/png");
        $('#qr').attr('src', img);
    }
    $("#agendaModal").modal("show");
}

function descargarQR() {
    var canvas = $('#qrContainer canvas')[0];
    if (canvas) {
        var img = canvas.toDataURL("image/png");
        var link = document.createElement('a');
        link.href = img;
        link.download = 'AgendaSupervisionDocenteQR.png';
        link.click();
    }
}

function copiarURL() {
    let texto = $("#ligaCompartirAgenda").prop("href");
    navigator.clipboard.writeText(texto)
            .then(() => {
                alert('Contenido copiado al portapapeles');
            })
            .catch(err => {
                console.error('Error al copiar: ', err);
                alert('Error al copiar el contenido.');
            });
}