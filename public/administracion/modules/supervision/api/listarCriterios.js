function ready() {
    $(document).ready(function () {
            crearPeticion("api/SupervisionAPI.php", {case: "recuperar_criterios_por_rubro"}, function (res) {
        print(res);
        let data = JSON.parse(res);
        let rubrosArray = [...data.contable, ...data.no_contable];
        if (rubrosArray.length > 0) {
            rubrosArray.forEach((rubro, rubroIndex) => {
                const rubroHtml = `
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 contenteditable="true" onblur="updateRubro(${rubro.id_rubro}, ${rubroIndex})">${rubro.descripcion}</h5>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeRubro(${rubro.id_rubro})">
                            <i class="ti ti-trash"></i> Eliminar Rubro
                        </button>
                    </div>
                    <div class="card-body">
                        <ul class="list-group" id="criteriosRubro${rubro.id_rubro}">
                            ${rubro.criterios.map((criterio, criterioIndex) => `
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span id="${criterio.id_criterio}" contenteditable="true" onblur="updateCriterio(this)">${criterio.descripcion}</span>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeCriterio(${criterio.id_criterio})">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCriterio(${rubro.id_rubro})">
                            <i class="ti ti-plus"></i> Añadir Criterio
                        </button>
                    </div>
                </div>
            `;
                $('#rubrosContainer').append(rubroHtml);
            });
        } else {
            redireccionar("agregarCriteriosSupervision.php");
        }
    });

    });
}

function updateRubro(id, rubroIndex) {
    let descripcion = document.querySelectorAll('.card-header h5')[rubroIndex].innerText;
    let data = {case: "actualizar_rubro", data: "descripcion=" + descripcion + "&id=" + id};
    crearPeticion("api/SupervisionAPI.php", data);
}

function updateCriterio(criterio) {
    let data = {case: "actualizar_criterio", data: "descripcion=" + criterio.innerText + "&id=" + criterio.id};
    crearPeticion("api/SupervisionAPI.php", data);
}

function addCriterio(idRubro) {
    let criterio = `
    <div class="criterion-item mb-2">
      <label  class="form-label">Criterio nuevo</label>
      <input type="text" class="form-control" name="rubro[${idRubro}][]" placeholder="Descripción del Criterio" required>
      <button type="button" class="btn btn-danger btn-sm mt-2 remove-criterion">Eliminar Criterio</button>
    </div>
  `;
    $("#criteriosRubro" + idRubro).append(criterio);
}


function removeRubro(id) {
    eliminarElementoSupervision(id, "rubro", "Los criterios pertenecientes a este rubro también desaparecerán");
}

function removeCriterio(id) {
    eliminarElementoSupervision(id, "criterio", "La información del criterio ya no estará disponible");
}

function eliminarElementoSupervision(id, elemento, mensaje) {
    alertaEliminar({
        mensajeAlerta: mensaje,
        url: "api/SupervisionAPI.php",
        data: {"case": "eliminar_" + elemento, "data": "id=" + id}
    });
}


function guardarCambiosCriterios() {
    var serializedValues = {};

    // Iterar sobre los inputs de tipo texto
    $('input[type="text"]').each(function () {
        // Obtener el nombre y el valor del input
        var name = $(this).attr('name');
        var value = $(this).val();

        // Verificar si ya existe una entrada con ese nombre
        if (serializedValues[name]) {
            // Si ya existe, agregar el valor al array existente
            serializedValues[name].push(value);
        } else {
            // Si no existe, crear una nueva entrada con un array que contenga el valor
            serializedValues[name] = [value];
        }
    });

    // Convertir el objeto serializado a una cadena JSON
    var serializedJSON = JSON.stringify(serializedValues);

    // Mostrar la cadena serializada en la consola
    console.log(serializedJSON);

    crearPeticion("api/SupervisionAPI.php", {
        case: "guardar_nuevos_criterios",
        data: JSON.stringify(serializedValues)
    }, print);
}
