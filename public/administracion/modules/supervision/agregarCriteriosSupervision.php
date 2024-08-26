<!doctype html>
<html lang="es">
    <?php
    include_once '../../includes/head.php';
    ?>
    <style>
        .btn-rounded {
            border-radius: 50px;
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
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-body">
                                <form id="rubrosForm">
                                    <div id="rubrosContainer">
                                        <div class="rubro-item mb-4 card">
                                            <div class="card-header">
                                                <div class="mb-3">
                                                    <label for="rubro[0][name]" class="form-label h5">Nombre del Rubro</label>
                                                    <input value="Rubro 1" type="text" class="form-control" name="rubro[0][name]" id="rubro[0][name]" placeholder="Nombre del Rubro" required="">
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div id="criteriaContainer0" class="criteria-container">
                                                    <div class="criterion-item mb-2">
                                                        <label for="rubro[0][criteria][0][description]" class="form-label">Criterio</label>
                                                        <input value="Criterio 1 del Rubro 1" type="text" class="form-control" name="rubro[0][criteria][0][description]" id="rubro[0][criteria][0][description]" placeholder="Descripción del Criterio" required="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <button type="button" class="btn btn-sm btn-outline-primary add-criterion" data-rubro-index="0">Añadir Criterio</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-success btn-rounded" id="addRubro">Añadir Rubro</button>
                                    <button type="submit" class="btn btn-primary btn-rounded">Guardar</button>
                                </form>

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
