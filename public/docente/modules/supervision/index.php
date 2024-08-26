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
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Reporte de Supervision docente</h5>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text" id="inputGroup-sizing-lg">Fecha y hora</span>
                                <input id="fechaHoraSupervision" readonly type="datetime-local" class="form-control" aria-label="Fecha y hora" aria-describedby="inputGroup-sizing-lg">
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="temaSupervision">
                            Tema: 
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Conclusiones y comentarios sobre la clase</h5>
                            <p class="card-text" id="conclusionGeneral"></p>
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
        <script src="api/supervision.js"></script>
    </body>

</html>