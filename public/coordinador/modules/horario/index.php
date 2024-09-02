<!doctype html>
<html lang="es">

    <?php
    include_once '../../includes/head.php';
    ?>
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
                    <div class="card">
                        <div class="card-body position-relative">
                            <?php
                            include_once '../../includes/selectorCarrera.php';
                            ?>
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <h2 class="card-title">Buscar horario</h2>
                                    </div>
                                    <div class="text-center mb-4">
                                        <h3 class="card-subtitle text-muted" id="carreraPlantel"></h3>
                                    </div>
                                    <div class="d-flex justify-content-center mb-4">
                                        <div class="btn-group" role="group" aria-label="Opciones de horario">
                                            <input value="grupo" type="radio" class="btn-check" name="tipoHorario" id="profesor" autocomplete="off" checked>
                                            <label class="btn btn-outline-primary" for="profesor">Horario por grupo</label>

                                            <input value="profesor" type="radio" class="btn-check" name="tipoHorario" id="grupo" autocomplete="off">
                                            <label class="btn btn-outline-primary" for="grupo">Horario por profesor</label>
                                        </div>
                                    </div>
                                    <div id="tabla" class="row">
                                        
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
            <script src="api/horario.js"></script> 
    </body>

</html>