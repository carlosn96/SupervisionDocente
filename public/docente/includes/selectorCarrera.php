<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="card-title fw-semibold mb-4">
                    Seleccionar carrera
                    <select class="form-select" id="selectorCarrera" name="carrera" required onchange="recuperarPlanteles()">
                        <!-- Opciones de la carrera -->
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="card-title fw-semibold mb-4">
                    Seleccionar plantel
                    <select class="form-select" id="selectorPlantel" name="plantel" required>
                        <!-- Opciones del plantel -->
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let carrerasPlantelesAPI = "../../includes/CarrerasPlantelesAPI.php";
    let plantelActual;
    function recuperarCarreras(fnChange) {
        crearPeticion(carrerasPlantelesAPI, {case: "recuperar_carreras_coordinador"}, function (res) {
            let selectorCarrera = $("#selectorCarrera");
            let lista = JSON.parse(res);
            let idPlantel;
            lista.carreras.forEach(function (carrera) {
                crearOpcionSelector(selectorCarrera, carrera.id_carrera, carrera.nombre);
            });
            //print(res);
            if ((idPlantel = lista.carrera_plantel_actual.id_plantel_actual) === null) {
                selectorCarrera.first();
            } else {
                plantelActual = idPlantel;
                selectorCarrera.val(lista.carrera_plantel_actual.id_carrera_actual);
            }
            selectorCarrera.trigger("change");
            $("#selectorPlantel").change(() => {
                let data = "id_carrera=" + selectorCarrera.val() + "&id_plantel=" + $("#selectorPlantel").val();
                crearPeticion(carrerasPlantelesAPI, {case: "guardar_configuracion_plantel", data: data}, ()=>{});
                fnChange();
            });
        });
    }
    function recuperarPlanteles() {
        let data = {
            case: "recuperar_listado_planteles_por_carrera",
            data: "id_carrera=" + $("#selectorCarrera").val()
        };
        crearPeticion(carrerasPlantelesAPI, data, function (res) {
            //print(res);
            let planteles = JSON.parse(res);
            let selectorPlantel = $("#selectorPlantel");
            selectorPlantel.empty();
            planteles.forEach(function (plantel) {
                crearOpcionSelector(selectorPlantel, plantel.id_plantel, plantel.nombre);
            });
            if (plantelActual) {
                selectorPlantel.val(plantelActual);
            } else {
                selectorPlantel.first();
            }
            plantelActual = null;
            selectorPlantel.trigger("change");
        });
    }

</script>

