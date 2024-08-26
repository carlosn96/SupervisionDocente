let rubroIndex = 1;
function ready() {
        $(document).ready(function () {
            $('#addRubro').click(function () {
            let rubroHtml = `
            <div class="rubro-item mb-4 card" id="rubroItem${rubroIndex}">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button type="button" class="btn btn-danger btn-sm remove-rubro" data-rubro-id="${rubroIndex}">Eliminar Rubro</button>
                    </div>
                <div class="mb-3">
                    <label for="rubro[${rubroIndex}][name]" class="h5 form-label">Nombre del Rubro</label>
                    <input value="Rubro ${rubroIndex + 1}" type="text" class="form-control" name="rubro[${rubroIndex}][name]" id="rubro[${rubroIndex}][name]" placeholder="Nombre del Rubro" required>
                </div>
            </div>
            <div class="card-body">
                <div id="criteriaContainer${rubroIndex}" class="criteria-container">
                    <div class="criterion-item mb-2">
                        <label for="rubro[${rubroIndex}][criteria][0][description]" class="form-label">Criterio</label>
                        <input value="Criterio 1 del Rubro ${rubroIndex + 1}" type="text" class="form-control" name="rubro[${rubroIndex}][criteria][0][description]" id="rubro[${rubroIndex}][criteria][0][description]" placeholder="Descripción del Criterio" required>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-sm btn-outline-primary btn-rounded add-criterion" data-rubro-index="${rubroIndex}">Añadir Criterio</button>
            </div>
        </div>`;
            $('#rubrosContainer').append(rubroHtml);
            rubroIndex++;
            });
        });
        $(document).on('click', '.add-criterion', function() {
        let rubroIndex = $(this).data('rubro-index');
                let criteriaContainer = $(`#criteriaContainer${rubroIndex}`);
                let criterionIndex = criteriaContainer.find('.criterion-item').length;
                let criterionHtml = `
    <div class="criterion-item mb-2">
      <label for="rubro[${rubroIndex}][criteria][${criterionIndex}][description]" class="form-label">Criterio</label>
      <input value="Criterio ${criterionIndex + 1} del Rubro ${rubroIndex + 1}" type="text" class="form-control" name="rubro[${rubroIndex}][criteria][${criterionIndex}][description]" id="rubro[${rubroIndex}][criteria][${criterionIndex}][description]" placeholder="Descripción del Criterio" required>
      <button type="button" class="btn btn-danger btn-sm mt-2 remove-criterion">Eliminar Criterio</button>
    </div>`;
        criteriaContainer.append(criterionHtml);
        });
        $(document).on('click', '.remove-criterion', function() {
            $(this).closest('.criterion-item').remove();
        });
        $(document).on('click', '.remove-rubro', function() {
        let rubroId = $(this).data('rubro-id');
            $(`#rubroItem${rubroId}`).remove();
        });
        $('#rubrosForm').submit(function (event) {
            event.preventDefault();
            crearPeticion("api/SupervisionAPI.php", {case:"guardar_criterios", data:$(this).serialize()});
        });
}