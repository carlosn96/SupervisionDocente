var urlAPI = "api/CoordinadorAPI.php";
function ready() {
    $(document).ready(function () {
        crearPeticion(urlAPI, {case: "listar_coordinadores"}, function (res) {
            $.each(JSON.parse(res), function (index, coordinador) {
                const carrerasCoordina = coordinador.carreras_coordina
                        ? coordinador.carreras_coordina.split(',').map(function (carrera) {
                    return carrera.trim();
                })
                        : [];
                const nombreCompleto = coordinador.nombre + " " + coordinador.apellidos;
                let tarjetaCoordinador = `
            <div class="col-md-4">
                <div class="card coordinador-card mb-4">
                    <img src="${coordinador.avatar}" class="card-img-top coordinador-img" alt="Foto del Coordinador ${nombreCompleto}">
                    <div class="coordinador-body">
                        <h5 class="card-title coordinador-name">${nombreCompleto}</h5>
                        <p class="card-text coordinador-email">Correo: <a href="https://mail.google.com/mail/?view=cm&fs=1&to=${coordinador.correo_electronico}" target="_blank">${coordinador.correo_electronico}</a></p>
                        <p class="card-text coordinador-label">Carreras:</p>
                        <ul class="list-group">
        `;
                if (carrerasCoordina.length > 0) {
                    $.each(carrerasCoordina, function (index, carrera) {
                        tarjetaCoordinador += `<li class="list-group-item">${carrera}</li>`;
                    });
                } else {
                    tarjetaCoordinador += `<li class="list-group-item">No hay carreras asignadas</li>`;
                }

                tarjetaCoordinador += `
                        </ul>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-primary btn-sm" onclick='editarCoordinador(${JSON.stringify(coordinador)})'>Editar</button>
                            <button class="btn btn-danger btn-sm" onclick="eliminarCoordinador(${coordinador.id_usuario})">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
                $('#coordinadores-list').append(tarjetaCoordinador);
            });
        });
        crearPeticion(urlAPI, {case: "recuperar_campos_formulario"}, function (res) {
            let rs = JSON.parse(res);
            crearCheckboxes("grupoCarreras", rs.grupoCarreras, "carreras", "id");
        });

        $("#editarCoordinadorForm").submit(actualizarCoordinador);
        $('#nombre, #apellido').on('input', function () {
            const nombre = removerAccentos($('#nombre').val().trim().toLowerCase().replace(/ /g, ''));
            const apellidos = removerAccentos($('#apellido').val().trim().toLowerCase().split(' ')[0]);
            if (nombre && apellidos) {
                const correoUsuario = `${nombre}.${apellidos}`;
                $('#correo').val(correoUsuario);
            }
        });
        ajustarEventosFiltradoCoordinadores();
    });
}

function ajustarEventosFiltradoCoordinadores() {
    const $searchInputCoordinadores = $('#searchInput');

    $searchInputCoordinadores.on('input', function () {
        const searchTerm = $searchInputCoordinadores.val().toLowerCase().trim();

        $('#coordinadores-list').find('.card').each(function () {
            const $tarjeta = $(this);
            const nombreCompleto = $tarjeta.find('.coordinador-name').text().toLowerCase().trim();
            const correoElectronico = $tarjeta.find('.coordinador-email a').text().toLowerCase().trim();
            const carrerasCoordina = $tarjeta.find('.list-group-item').map(function () {
                return $(this).text().toLowerCase().trim();
            }).get().join(', ');

            const matches = nombreCompleto.includes(searchTerm) ||
                    correoElectronico.includes(searchTerm) ||
                    carrerasCoordina.includes(searchTerm);

            if (matches) {
                $tarjeta.parent().show(); // Mostrar el contenedor de la tarjeta
            } else {
                $tarjeta.parent().hide(); // Ocultar el contenedor de la tarjeta
            }
        });
    });
}

function editarCoordinador(coordinador) {
    crearPeticion(urlAPI, {case: "buscar_carreras_propias", data: "id_coordinador=" + coordinador.id_usuario}, function (res) {
        let carreras = $('#grupoCarreras').find('.grupo-carreras-scroll');
        res.forEach((carrera) => {
            let id = carrera.id_carrera;
            carreras.append(construirInputChecbox("carrera" + id, id, "carreras", carrera.tipo + " " + carrera.nombre, true));
        });
    }, "json");

    $('#id_coordinador').val(coordinador.id_coordinador);
    $('#nombre').val(coordinador.nombre);
    $('#apellido').val(coordinador.apellidos);

    $('#id_usuario').val(coordinador.id_usuario);
    $('#id_coordinador').val(coordinador.id_coordinador);
    $('#nombre').val(coordinador.nombre);
    $('#apellidos').val(coordinador.apellidos);
    $('#correo_electronico').val(coordinador.correo_electronico);
    $('#carreras_coordina').val(coordinador.carreras_coordina);
    $('#fecha_nacimiento').val(coordinador.fecha_nacimiento);
    $('#genero').val(coordinador.genero);
    $('#telefono').val(coordinador.telefono);

    // Mostrar el modal usando jQuery
    $('#editarCoordinadorModal').modal('show');
}

function actualizarCoordinador(e) {
    e.preventDefault();
    //crearPeticion(urlAPI, {case: "actualizar_coordinador", data: $(this).serialize()}, print);

}

function eliminarCoordinador(id) {
    alertaEliminar({
        mensajeAlerta: "Se perderá toda la información con referencia al coordinador",
        url: urlAPI,
        data: {"case": "eliminar", "data": "id=" + id}
    });
}