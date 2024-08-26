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
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12 text-center text-md-end">
                                            <a href="../docentes/" class="btn btn-outline-primary mb-3 mb-md-0">
                                                <i class="ti ti-arrow-left"></i>
                                                Regresar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <input hidden="" id="id_docente" value="<?= $_GET["docente"] ?? "" ?>">
                                            <h2 class="card-title text-center mb-2">Lista de materias</h2>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <h3 class="card-subtitle mb-4 text-muted text-center" id="nombreProfesor">Docente: </h3>
                                        </div>
                                    </div>

                                    <div id="materias-list" class="row">
                                        
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
        <script src="api/editarMaterias.js"></script> 
    </body>

</html>