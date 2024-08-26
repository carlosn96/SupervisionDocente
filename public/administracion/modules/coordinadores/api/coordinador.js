let apiURL = "api/CoordinadorAPI.php";

function ready() {
    $(document).ready(function () {
        recuperarCampos();
        $("#coordinadorForm").submit(function (e) {
            e.preventDefault();
            var $form = $(this);
            if ($('#grupoCarreras input[name="carreras[]"]:checked').length > 0) {
                const formDataObj = new FormData($form[0]);
                formDataObj.append('case', 'guardar');
                formDataObj.append('data', $form.serialize());
                crearPeticion(apiURL, formDataObj, print, 'text', 'multipart/form-data');
            } else {
                mostrarMensajeAdvertencia("Elige al menos una carrera ...", false);
            }
        });


        $('#nombre, #apellido').on('input', function () {
            const nombre = removerAccentos($('#nombre').val().trim().toLowerCase().replace(/ /g, ''));
            const apellidos = removerAccentos($('#apellido').val().trim().toLowerCase().split(' ')[0]);
            if (nombre && apellidos) {
                const correoUsuario = `${nombre}.${apellidos}`;
                $('#correo').val(correoUsuario);
            }
        });

        $('.change-tab-link').on('click', function () {
            $('.nav-tabs a[href="#tabSubirImagen"]').tab('show');
        });
    });
}

function recuperarCampos() {
    crearPeticion(apiURL, {case: "recuperar_campos_formulario"}, function (res) {
        //print(res);
        let rs = JSON.parse(res);
        if (rs.grupoCarreras.length > 0) {
            crearCheckboxes("grupoCarreras", rs.grupoCarreras, "carreras", "id");
        } else {
            $("#cardCoordinador").empty();
            insertarAlerta("#cardCoordinador", "No hay carreras almacenadas diponibles para asignar coordinador");
        }
        let html = "";
        $.each(rs.avatares, function (i, e) {
            html += `
                <label class="imagecheck mb-4">
                    <input name="avatar" type="radio" value="${e}" class="imagecheck-input">
                    <figure class="imagecheck-figure">
                        <img src="../../../assets/images/profile/${e}" alt="avatar" class="rounded-circle" width="50">
                    </figure>
                </label>
            `;
        });
        $("#listaAvatar").append(html);
    });
}