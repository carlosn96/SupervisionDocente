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
                                    <div class="text-center mb-4">
                                        <h2 class="card-title">Informe de Supervisión</h2>
                                    </div>
                                    <table class="table table-striped table-hover" id="supervisionTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Docente</th>
                                                <th>Comentarios</th>
                                                <th>Fecha</th>
                                                <th>Resultado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            include_once '../../includes/script.php';
            ?>
            <script src="api/informe.js"></script> 
    </body>

</html>