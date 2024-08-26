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
                        <div class="card-body">
                            <h2 class="text-center mb-5">Administración de Planteles</h2>
                            <form id="agregarPlantelForm">
                                <h5 class="fw-semibold mb-4">Agregar nuevo</h5>
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required="">
                                </div>
                                <button type="submit" class="btn btn-outline-primary">Guardar</button>
                            </form>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Lista de Planteles</h5>
                            <div class="table-responsive">
                                <table class="table table-striped" id="tablaPlanteles">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyPlanteles">
                                        <!-- Aquí se agregarán las filas de la tabla dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalEditarPlantel" tabindex="-1" aria-labelledby="modalEditarPlantelLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalEditarPlantelLabel">Editar Nombre del Plantel</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="formEditarPlantel">
                                <input hidden="" id="idPlantel" name="idPlantel">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="nombrePlantel" class="form-label">Nuevo nombre del plantel</label>
                                        <input type="text" class="form-control" id="nombrePlantel" name="nombrePlantel" required>
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
        </div>
        <?php
        include_once '../../includes/script.php';
        ?>
        <script src="api/plantel.js"></script> 
    </body>

</html>