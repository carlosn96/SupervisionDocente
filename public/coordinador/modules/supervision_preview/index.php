<!doctype html>
<html lang="es">

    <?php
    include_once '../../includes/head.php';
    ?>
    <style>
        .tab-content {
            padding: 20px;
            border: 1px solid #dee2e6;
            border-top: 0;
        }
        .tab-content .tab-pane {
            height: 300px; /* Set a fixed height for each tab pane */
            overflow-y: auto; /* Add vertical scroll */
        }
        .card {
            position: relative;
            padding: 20px;
        }
        .edit-button-header {
            position: absolute;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .edit-button-footer {
            position: absolute;
            bottom: 10px;
            right: 10px;
            cursor: pointer;
        }
        .card-header {
            position: relative;
        }
        .card-body {
            position: relative;
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

                <div class="container-fluid" id="content">
                    <div class="card">
                        <input hidden="" id="id_agenda" value="<?= $_GET["id_agenda"] ?? "0" ?>">
                        <input hidden="" id="id_supervision">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Reporte de Supervision docente</h5>
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroup-sizing-lg">Fecha y hora</span>
                                <input onchange="actualizarFecha()" id="fechaHoraSupervision" type="datetime-local" class="form-control" aria-label="Fecha y hora" aria-describedby="inputGroup-sizing-lg">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <button data-column="tema" class="btn btn-outline-primary btn-sm edit-button-header" data-bs-toggle="modal" data-bs-target="#editModal" data-id="temaSupervision" data-type="tema" title="Editar Tema">
                                <i class="ti ti-pencil text-primary"></i>
                            </button>
                            Tema:
                            <p id="temaSupervision" class="mb-0"></p> 
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Conclusiones y comentarios sobre la clase</h5>
                            <p class="card-text" id="conclusionGeneral"></p>
                            <button data-column="conclusion_general" class="btn btn-outline-primary btn-sm edit-button-footer" data-bs-toggle="modal" data-bs-target="#editModal" data-id="conclusionGeneral" data-type="conclusion" title="Editar Conclusiones">
                                <i class="ti ti-pencil text-primary"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="container my-4">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="tab-home" data-bs-toggle="tab" href="#docente" role="tab" aria-controls="docente" aria-selected="true">Docente</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-profile" data-bs-toggle="tab" href="#valoraciónGlobal" role="tab" aria-controls="valoracionGlobal" aria-selected="false">Valoración global</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-contact" data-bs-toggle="tab" href="#criteriosContables" role="tab" aria-controls="criteriosContables" aria-selected="false">Criterios contables</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-contact" data-bs-toggle="tab" href="#criteriosNoContables" role="tab" aria-controls="criteriosNoContables" aria-selected="false">Criterios no contables</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="tab-contact" data-bs-toggle="tab" href="#compartir" role="tab" aria-controls="compartir" aria-selected="false">Compartir resultados</a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="docente" role="tabpanel" aria-labelledby="tab-docente">
                                    <div class="card" id="infoDocente">
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="valoraciónGlobal" role="tabpanel" aria-labelledby="tab-valoracionGlobal">
                                    <section class="card p-3">
                                        <h2>VALORACIÓN GLOBAL</h2>
                                        <table id="rubro-table" class="table table-bordered">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>Rubro</th>
                                                    <th>Valoración</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                        <div class="text-center">
                                            <p class="display-5 fw-bold text-primary" id="valoracionGlobal"></p>
                                        </div>
                                        <canvas id="valoracionChart" width="400" height="200"></canvas>
                                    </section>
                                </div>
                                <div class="tab-pane fade" id="criteriosContables" role="tabpanel" aria-labelledby="tab-criteriosContables">
                                    <section id="tarjetas-rubros-contables">
                                    </section>
                                </div>
                                <div class="tab-pane fade" id="criteriosNoContables" role="tabpanel" aria-labelledby="tab-criteriosNoContables">
                                    <section id="tarjetas-rubros-no-contables">
                                    </section>
                                </div>
                                <div class="tab-pane fade" id="compartir" role="tabpanel" aria-labelledby="tab-compatir">
                                    <div class="card mb-3 shadow-sm">
                                        <div class="row g-0">
                                            <!-- Sección de la imagen QR y botones -->
                                            <div class="col-md-4 text-center p-3">
                                                <figure class="mb-3">
                                                    <div id="qrContainer" hidden></div>
                                                    <img id="qr" class="img-fluid rounded" alt="Código QR" style="max-width: 100%; height: auto;">
                                                    <figcaption class="text-muted mt-2">Escanea este código QR para acceder al reporte o copia la liga </figcaption>
                                                </figure>
                                                <div class="d-flex justify-content-center">
                                                    <a href="#" id="urlResultadosGmail" class="btn btn-sm btn-outline-primary me-2" target="_blank"  data-bs-toggle="tooltip" data-bs-placement="top" title="Compartir por Gmail">
                                                        <i class="ti ti-mail"></i>
                                                    </a>
                                                    <button onclick="descargarQR()" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Descargar QR">
                                                        <i class="ti ti-download"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Sección de contenido textual -->
                                            <div class="col-md-8">
                                                <div class="card-body">
                                                    <h5 class="card-title">Compartir reporte de supervisión</h5>
                                                    <p class="card-text">
                                                        <?php
                                                        $server = ($server = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") .
                                                                ($_SERVER["SERVER_NAME"] == "localhost" ? $_SERVER["SERVER_NAME"] . "/SupervisionDocente" :
                                                                $_SERVER["SERVER_NAME"])) . "/public/docente";
                                                        ?>
                                                        <strong>Sitio</strong> <a href="<?= $server ?>" id="url-supervision" target="_blank" class="text-decoration-none"><?= $server ?></a>
                                                        <button onclick="copiarURL()" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Copiar">
                                                            <i class="ti ti-copy"></i>
                                                        </button>
                                                    </p>
                                                    <p class="card-text">
                                                        <strong>Número de expediente:</strong> <span class="badge bg-primary" id="id-expediente"></span>
                                                    </p>
                                                    <p class="card-text">
                                                        <strong>Contraseña:</strong> <span class="badge bg-primary" id="contrasenia"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Editar</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="editInput">Contenido:</label>
                                    <textarea class="form-control" id="editInput" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-primary" id="saveChanges">Guardar cambios</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include_once '../../includes/script.php';
        ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="api/supervision_preview.js"></script>
    </body>

</html>