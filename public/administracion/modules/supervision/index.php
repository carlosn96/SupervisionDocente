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
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-body">
                                <h2 class="text-center fw-semibold mb-4">Elementos de supervisión docente</h2>
                                <span>Hacer clic para modificar rubros y criterios</span>
                                <div id="rubrosContainer" ></div>
                            </div>
                            <div class="card-footer">
                                <button onclick="guardarCambiosCriterios()" class="btn btn-outline-success"> Guardar cambios</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include_once '../../includes/script.php';
        ?>
        <script src="api/listarCriterios.js"></script> 
    </body>

</html>