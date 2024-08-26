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
                                <h5 class="card-title fw-semibold mb-4">Nueva carrera</h5>
                                <div class="card" id="cardCarrera">
                                    <div class="card-body">
                                        <form id="carreraForm">
                                            <div class="mb-3 has-validation">
                                                <label for="nombre" class="form-label">Nombre</label>
                                                <input type="text" class="form-control" id="nombre" name="nombre" required="">
                                                <div class="invalid-feedback">
                                                    Esta carrera ya ha sido agregada.
                                                </div>

                                            </div>
                                            <div class="mb-3">
                                                <label for="nombre" class="form-label">Tipo de carrera</label>
                                                <select name="tipo" class="form-select" id="grupoTipos" required=""></select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Planteles en donde se oferta:</label>
                                                <div id="grupoPlanteles">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-outline-primary">Guardar</button>
                                        </form>
                                    </div>
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
        <script src="api/carrera.js"></script> 
    </body>

</html>