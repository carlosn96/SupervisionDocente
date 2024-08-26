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
                            <?php
                            include_once '../../includes/selectorCarrera.php';
                            ?>
                            <div class="row">
                                <!-- Columna para el formulario del profesor -->
                                <div class="col-lg-8 col-md-12 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title fw-semibold mb-4">Nuevo Docente</h5>
                                            <form id="profesorForm">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="nombre" class="form-label">Nombre</label>
                                                        <input type="text" class="form-control" id="nombre" name="nombre" required="">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="apellido" class="form-label">Apellidos</label>
                                                        <input type="text" class="form-control" id="apellido" name="apellidos" required="">
                                                    </div>
                                                </div>
                                                <label for="correo" class="form-label">Correo institucional</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" id="correo" name="correo" required="">
                                                    <span class="input-group-text">@</span>
                                                    <input type="text" id="dominio" name="dominio" class="form-control" readonly="" value="universidad-une.com">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="perfilProfesional" class="form-label">Perfil Profesional</label>
                                                    <input type="text" class="form-control" id="perfilProfesional" name="perfil_profesional" required>
                                                </div>
                                                <div class="mb-4">
                                                    <h6>Materias</h6>
                                                    <div id="materiasContainer">
                                                        <!-- Las materias se agregarán aquí -->
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#materiaModal">Nueva materia</button>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Guardar docente</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Columna para la lista de materias guardadas -->
                                <div class="col-lg-4 col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-title fw-semibold mb-4">Lista de docentes en <span id="nombreCarrera"></span></div>
                                            <div class="accordion accordion-flush" id="listaMateriasContainer">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal para agregar materias -->
                <div class="modal fade" id="materiaModal" tabindex="-1" aria-labelledby="materiaModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg"> <!-- Use modal-lg for a larger dialog, more space for content -->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="materiaModalLabel">Agregar Materia</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="materiaForm">
                                    <div class="mb-3">
                                        <label for="nombreMateria" class="form-label">Nombre de la Materia</label>
                                        <input type="text" class="form-control" id="nombreMateria" required onchange="verificarExisteMateria(this)">
                                    </div>
                                    <div class="mb-3">
                                        <label for="grupoMateria" class="form-label">Grupo</label>
                                        <input type="text" class="form-control" id="grupoMateria" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="horarioMateria" class="form-label">Horario</label>
                                        <div id="horarioContainer">
                                            <div class="horario-item row align-items-center mb-2">
                                                <div class="col-12 col-md-3 mb-2 mb-md-0">
                                                    <select class="form-select" name="diaSemana[]" required>
                                                        <option value="Lunes">Lunes</option>
                                                        <option value="Martes">Martes</option>
                                                        <option value="Miércoles">Miércoles</option>
                                                        <option value="Jueves">Jueves</option>
                                                        <option value="Viernes">Viernes</option>
                                                        <option value="Sábado">Sábado</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 col-md-2 mb-2 mb-md-0">
                                                    <input type="time" class="form-control" name="horaInicio[]" required>
                                                </div>
                                                <div class="col-12 col-md-1 text-center mb-2 mb-md-0">
                                                    <span>a</span>
                                                </div>
                                                <div class="col-12 col-md-2 mb-2 mb-md-0">
                                                    <input type="time" class="form-control" name="horaFin[]" required>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="agregarHorarioBtn">Agregar Horario</button>
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="agregarMateriaBtn">Agregar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php
            include_once '../../includes/script.php';
            ?>
            <script src="api/docente.js"></script>
    </body>

</html>