<!doctype html>
<html lang="es">

    <?php
    include_once '../../includes/head.php';
    ?>

    <style>
        .bs-stepper-content {
            max-height: 400px;
            overflow-y: auto;
        }
        .step-content {
            display: none;
        }
        .step-content.active {
            display: block;
        }
        .sticky-top {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        th, td {
            white-space: nowrap;
        }
        td {
            vertical-align: middle;
        }
    </style>


    <body>
        <!--  Body Wrapper -->
        <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
             data-sidebar-position="fixed" data-header-position="fixed">
            <!-- Sidebar Start -->
            <?php
            include_once '../../includes/aside.php';
            ?>
            <!--  Sidebar End -->
            <!--  Main wrapper -->
            <div class="body-wrapper">
                <!--  Header Start -->
                <?php
                include_once '../../includes/header.php';
                ?>
                <!--  Header End -->
                <div class="container-fluid">
                    <!--  Card fecha -->
                    <div class="card">
                        <input hidden="" id="id_agenda" value="<?= $_GET["id_agenda"] ?? "0" ?>">
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text" id="inputGroup-sizing-lg">Fecha y hora</span>
                                    <input id="fechaHoraSupervision" readonly type="datetime-local" class="form-control" aria-label="Fecha y hora" aria-describedby="inputGroup-sizing-lg">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  Fin card fecha -->

                    <!--  Accordion Supervision -->
                    <div class="accordion" id="accordionAgenda">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button id="tituloInfoDocente" class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#infoDocente" aria-expanded="false" aria-controls="infoDocente">
                                    Información del docente  
                                </button>
                            </h2>
                            <div id="infoDocente" class="accordion-collapse collapse">
                                <div class="accordion-body" id="profesor-card">

                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#supervision" aria-expanded="true" aria-controls="supervision">
                                    Supervisión
                                </button>
                            </h2>
                            <div id="supervision" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <div class="accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#detallesClase" aria-expanded="false" aria-controls="detallesClase">
                                                    Detalles de la clase
                                                </button>
                                            </h2>
                                            <div id="detallesClase" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="mb-3">
                                                        <label for="temaClase" class="form-label">Tema de la clase</label>
                                                        <input value="No indicado" class="form-control" id="temaClase" placeholder="Tema de la clase">
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <label for="conclusionesArea" class="form-label mb-0">CONCLUSIONES Y COMENTARIOS SOBRE LA CLASE</label>
                                                            <div class="btn-group" role="group">
                                                                <button type="button" class="btn btn-sm btn-outline-primary ms-2 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="ti ti-edit-circle"></i>
                                                                    Generar comentarios
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><button disabled="" class="dropdown-item" onclick="generarComentarios('gpt')">OpenAI</button></li>
                                                                    <li><button disabled="" class="dropdown-item" conclick="generarComentarios('huggingface')">Hugging Face</button></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <textarea class="form-control" id="conclusionesArea" rows="3"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#elementosContable" aria-expanded="false" aria-controls="elementosContable">
                                                    Elementos contables
                                                </button>
                                            </h2>
                                            <div id="elementosContable" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="mb-3 text-center">
                                                        <div class="btn-group" role="group" id="btnsNavegacionContables">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div id="stepper" class="bs-stepper">
                                                            <div class="bs-stepper-content" id="containerContables">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#elementosNoContables" aria-expanded="false" aria-controls="elementosNoContables">
                                                    Elementos no contables
                                                </button>
                                            </h2>
                                            <div id="elementosNoContables" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                                <div class="accordion-body">
                                                    <div class="mb-3 text-center">
                                                        <div class="btn-group" role="group" id="btnsNavegacionNoContables">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div id="stepper" class="bs-stepper">
                                                            <div class="bs-stepper-content" id="containerNoContables">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  Fin Supervision -->

                    <!--  Card enviar Supervision -->
                    <div class="card mt-4">
                        <div class="card-body position-absolute">
                            <button type="button" class="btn btn-sm btn-outline-info position-absolute top-0 end-0 mt-2 me-2" onclick="chartZoom()">
                                <i class="ti ti-zoom-in fs-7"></i>
                            </button>
                        </div>
                        <div class="card-body" id="valoracionGlobal">
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-success" onclick="abrirModalEnviarSupervision()" >Enviar supervision</button>
                            </div>
                        </div>
                    </div>
                    <!--  Fin card Supervision -->

                    <!-- Modal de Enviar supervision-->
                    <div class="modal fade" id="modalEnviarSupervision" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalEnviarSupervisionLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="modalEnviarSupervisionLabel">Resumen de la Supervisión</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" id="modalEnviarSupervisionBodyContent">
                                    <!-- Content will be appended here by the JavaScript function -->
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-primary" onclick="guardarSupervision()">
                                        Guardar
                                        <div class="spinner-border d-none" id="btnGuardarSupervision" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Modal de Chart-->
                    <div class="modal fade" id="chartModal" tabindex="-1" aria-labelledby="chartModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="chartInModal" style="height: 500px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include_once '../../includes/script.php';
        ?>
        <script src="api/supervision.js"></script>

    </body>

</html>