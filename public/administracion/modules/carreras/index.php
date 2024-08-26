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
                                <h2 class="text-center mb-5">Lista de carreras</h2>
                                <div class="row mb-4">
                                    <div class="col">
                                        <input type="text" class="form-control" id="searchInput" placeholder="Buscar carrera por nombre, plantel, coordinador, tipo...">
                                    </div>
                                </div>
                                <div id="carreras-list" class="row">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editarCarreraModal" tabindex="-1" aria-labelledby="editarCarreraModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarCarreraModalLabel">Editar Carrera</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditarCarrera">
                            <input type="hidden" id="id_carrera" name="id_carrera">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input name="nombre" type="text" class="form-control" id="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="grupoTipos" class="form-label">Tipo</label>
                                <select name="tipo" class="form-select" id="grupoTipos" required=""></select>
                            </div>
                            <div class="mb-3">
                                <label for="coordinadorCarrera" class="form-label">Coordinador</label>
                                <select name="id_coordinador" class="form-select" id="grupoCoordinadoresCarrera" required=""></select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Planteles en donde se oferta:</label>
                                <div id="grupoPlanteles">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include_once '../../includes/script.php';
        ?>
        <script src="api/listaCarreras.js"></script> 
    </body>

</html>