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
                            <div class="card shadow-sm">
                                <div class="card-header bg-white">
                                    <div class="row">
                                        <div class="col-12 text-center text-md-end">
                                            <a href="../docentes/agregarDocente.php" class="btn btn-outline-primary mb-3 mb-md-0">
                                                <i class="ti ti-plus"></i> Agregar nuevo docente
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h2 class="card-title text-center mb-2">Lista de profesores por carrera</h2>
                                    <h3 class="card-subtitle mb-4 text-muted text-center" id="carreraPlantel"></h3>
                                    <div class="mb-4">
                                        <input type="text" class="form-control form-control-lg" id="searchInput" placeholder="Buscar profesor, materia, perfil..." onkeyup="filtrarProfesores()">
                                    </div>
                                    <div id="profesor-list" class="row">
                                        <!-- Tarjetas para profesor -->
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Updating Docente -->
        <div class="modal fade" id="updateDocenteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateDocenteModalLabel">Actualizar Docente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="updateDocenteForm">
                            <input type="hidden" name="id_docente" id="id_docente" >
                            <!-- Información básica del docente -->
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                            </div>
                            <div class="mb-3">
                                <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" required>
                            </div>

                            <div class="mb-3">
                                <label for="perfil_profesional" class="form-label">Perfil Profesional</label>
                                <input type="text" class="form-control" id="perfil_profesional" name="perfil_profesional" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
        include_once '../../includes/script.php';
        ?>
        <script src="api/listaDocentes.js"></script> 
    </body>

</html>