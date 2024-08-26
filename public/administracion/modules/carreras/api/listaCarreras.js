
function ready() {
    $(document).ready(function () {
        recuperar_info_campos();
        crearPeticion("api/CarreraAPI.php", {case: "recuperar_listado"}, function (rs) {
            let carreras = JSON.parse(rs);
            print(carreras);
            if (carreras.length > 0) {
                carreras.forEach(carrera => {
                    const tarjeta = `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">${carrera.nombre}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">${carrera.tipo}</h6>
                        <p class="card-text"><strong>Coordinador:</strong> ${typeof carrera.coordinador !== "undefined" ? carrera.coordinador : "Sin coordinador asignado" }</p>
                        <p class="card-text"><strong>Planteles en donde se oferta:</strong> ${carrera.planteles}</p>
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-primary btn-sm" onclick="editarCarrera(${carrera.id_carrera}, '${carrera.nombre}', '${carrera.tipo}', '${carrera.coordinador}', '${carrera.planteles}')">Editar</button>
                            <button class="btn btn-danger btn-sm" onclick="eliminarCarrera(${carrera.id_carrera})">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>`;
                    $('#carreras-list').append(tarjeta);
                });
            } else {
                insertarAlerta('#carreras', "Sin carreras", "warning");
            }
        });
        $("#formEditarCarrera").submit(actualizarCarrera);
        ajustarEventosFiltrado();
    });
}

function ajustarEventosFiltrado() {
    const $searchInput = $('#searchInput');

    $searchInput.on('input', function () {
        const searchTerm = $searchInput.val().toLowerCase().trim();

        $('#carreras-list').find('.card').each(function () {
            const $tarjeta = $(this);
            const nombreCarrera = $tarjeta.find('.card-title').text().toLowerCase().trim();
            const tipoCarrera = $tarjeta.find('.card-subtitle').text().toLowerCase().trim();
            const coordinadorCarrera = $tarjeta.find('.card-text:nth-child(3)').text().toLowerCase().trim();
            const plantelesCarrera = $tarjeta.find('.card-text:nth-child(4)').text().toLowerCase().trim();

            const matches = nombreCarrera.includes(searchTerm) ||
                    tipoCarrera.includes(searchTerm) ||
                    coordinadorCarrera.includes(searchTerm) ||
                    plantelesCarrera.includes(searchTerm);

            if (matches) {
                $tarjeta.parent().show(); // Mostrar el contenedor de la tarjeta
            } else {
                $tarjeta.parent().hide(); // Ocultar el contenedor de la tarjeta
            }
        });
    });
}



function recuperar_info_campos() {
    crearPeticion("api/CarreraAPI.php", {"case": "recuperar_campos_formulario"}, function (res) {
        //print(res);
        let rs = JSON.parse(res);
        if (rs.grupoPlanteles.length > 0) {
            crearCheckboxes("grupoPlanteles", rs.grupoPlanteles, "planteles", "int");
            rs.grupoTipos.forEach(function (idx) {
                crearOpcionSelector($("#grupoTipos"), idx, idx);
            });
            rs.grupoCoordinadoresCarrera.forEach(function (coord) {
                crearOpcionSelector($("#grupoCoordinadoresCarrera"), coord.id_coordinador, coord.nombre);
            });
        } else {
            $("#cardCarrera").empty();
            insertarAlerta("#cardCarrera", "Primero debes agregar planteles");
        }
    });
}

function editarCarrera(id, nombre, tipo, coordinador, planteles) {
    let plantelesArray = planteles.split(",").map(p => p.trim());
    $('#grupoPlanteles .form-check').each(function () {
        var label = $(this).find('.form-check-label').text().trim();
        if (plantelesArray.includes(label)) {
            $(this).find('.form-check-input').prop('checked', true);
        }
    });
    $('#id_carrera').val(id);
    $('#nombre').val(nombre);
    $('#grupoTipos').val(tipo);
    $('#grupoCoordinadoresCarrera').val(coordinador);
    $('#editarCarreraModal').modal('show');
}


function eliminarCarrera(id) {
    alertaEliminar({
        mensajeAlerta: "La carrera no podrá ser recuperada",
        url: "api/CarreraAPI.php",
        data: {"case": "eliminar", "data": "id=" + id}
    });
}

function actualizarCarrera(e) {
    e.preventDefault();
    crearPeticion("api/CarreraAPI.php", {case: "actualizar_carrera", data: $(this).serialize()}, print);
}
