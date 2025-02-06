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
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img src="../../../assets/images/profile/user<?= rand(1, 5) ?>.jpg" width="110" class="rounded-3 mb-3" >
                                        <h5 class="mb-1" id="nombre"></h5>
                                        <span class="badge bg-primary-subtle text-primary fw-light rounded-pill">Docente</span>
                                    </div>

                                    <div class="mt-5">
                                        <div class="pb-1 mb-2 border-bottom">
                                            <h6>Información general</h6>
                                        </div>

                                        <ul class="list-unstyled">
                                            <li class="py-2">
                                                <p class="fw-normal text-dark mb-0">
                                                    Perfil profesional:
                                                    <span class="fw-light ms-1" alt="Perfil de usuario" id="perfil"></span>

                                                </p>
                                            </li>

                                            <li class="py-2">
                                                <p class="fw-normal text-dark mb-0">
                                                    RFC:
                                                    <span class="fw-light ms-1">Sin información</span>
                                                </p>
                                            </li>

                                            <li class="py-2">
                                                <p class="fw-normal text-dark mb-0">
                                                    Fecha de nacimiento:
                                                    <span class="fw-light ms-1">Sin información</span>
                                                </p>
                                            </li>

                                            <li class="py-2">
                                                <p class="fw-normal text-dark mb-0">
                                                    Teléfono:
                                                    <span class="fw-light ms-1">Sin información</span>
                                                </p>
                                            </li>

                                            <li class="py-2">
                                                <p class="fw-normal text-dark mb-0">
                                                    Correo electrónico institucional:
                                                    <span class="fw-light ms-1" id="correo"></span>
                                                </p>
                                            </li>
                                        </ul>

                                        <div class="row mt-4">
                                            <div class="col-6 mb-3 mb-sm-0">
                                                <button disabled type="button" class="btn btn-primary w-100 d-flex justify-content-center align-items-center">
                                                    <i class="ti ti-edit fs-5 me-2"></i> Editar
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button disabled type="button" class="btn btn-danger w-100 d-flex justify-content-center align-items-center">
                                                    <i class="ti ti-trash fs-5 me-2"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-lg-8">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item me-2" role="presentation">
                                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">
                                        Materias que imparte
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false" tabindex="-1">
                                        Asignación materias
                                    </button>
                                </li>
                            </ul>

                            <div class="card mt-4">
                                <div class="card-body">
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                                            <div class="table-responsive overflow-x-auto" id="tabMateriasContent">
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                            <div class="mb-4 border-bottom pb-3">
                                                <h4 class="card-title mb-0">Asignación de materias</h4>
                                            </div>
                                            <div class="d-grid w-100 mt-4 pt-2">
                                                <form id="materiaForm" class="container my-4">

                                                    <!-- Sección: Selector de Carrera -->
                                                    <?php include_once '../../includes/selectorCarrera.php'; ?>

                                                    <input id="id_docente" hidden="">

                                                    <!-- Sección: Nombre de la Materia -->
                                                    <div class="mb-3">
                                                        <label for="nombreMateria" class="form-label">Nombre de la Materia</label>
                                                        <input type="text" class="form-control" id="nombreMateria" required>
                                                    </div>

                                                    <!-- Sección: Selección de Grupo -->
                                                    <div class="mb-3">
                                                        <label for="grupoMateria" class="form-label">Grupo</label>
                                                        <select required class="form-control" id="grupoMateria">
                                                        </select>
                                                    </div>

                                                    <!-- Sección: Mensaje de error en caso de no haber grupo -->
                                                    <div class="mb-3" id="mensajeNoGrupo" style="display:none;">
                                                        <div class="alert alert-danger" role="alert">
                                                            <strong>Atención:</strong> No hay grupos para el filtro seleccionado.
                                                        </div>
                                                    </div>

                                                    <!-- Sección: Horarios -->
                                                    <div class="mb-3">
                                                        <label for="horarioMateria" class="form-label fw-bold">Horario</label>
                                                        <div id="horarioContainer">
                                                            <!-- Encabezados de las columnas -->
                                                            <div class="row mb-2">
                                                                <div class="col-12 col-md-4">
                                                                    <label class="form-label">Día</label>
                                                                </div>
                                                                <div class="col-12 col-md-3">
                                                                    <label class="form-label">Hora de inicio</label>
                                                                </div>
                                                                <div class="col-12 col-md-3">
                                                                    <label class="form-label">Hora de fin</label>
                                                                </div>
                                                            </div>

                                                            <!-- Horario inicial -->
                                                            <div class="horario-item row align-items-center mb-3">
                                                                <div class="col-12 col-md-4 mb-2 mb-md-0">
                                                                    <select class="form-select" name="diaSemana[]" required>
                                                                        <option value="Lunes">Lunes</option>
                                                                        <option value="Martes">Martes</option>
                                                                        <option value="Miércoles">Miércoles</option>
                                                                        <option value="Jueves">Jueves</option>
                                                                        <option value="Viernes">Viernes</option>
                                                                        <option value="Sábado">Sábado</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-12 col-md-3 mb-2 mb-md-0">
                                                                    <input type="time" class="form-control" id="horaInicio" name="horaInicio[]" required>
                                                                </div>
                                                                <div class="col-12 col-md-3 mb-2 mb-md-0">
                                                                    <input type="time" class="form-control" name="horaFin[]" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="agregarHorarioBtn">
                                                            <i class="ti ti-plus fs-5 me-2"></i> Agregar horario
                                                        </button>
                                                    </div>


                                                    <!-- Sección: Botón para asignar materia -->
                                                    <div class="d-grid gap-2 mt-3">
                                                        <button type="submit" class="btn btn-primary" id="agregarMateriaBtn">
                                                            <i class="ti ti-check fs-5 me-2"></i> Asignar materia
                                                        </button>
                                                    </div>

                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal -->
                    <div id="modalHorarios" class="modal fade" tabindex="-1" aria-labelledby="primary-header-modalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg">
                            <div class="modal-content">
                                <div class="modal-header modal-colored-header bg-primary text-white">
                                    <h4 class="modal-title text-white" id="primary-header-modalLabel">Horarios de <span id="nombreMateriaModal"></span> </h4>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="horariosContainer">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <?php
            include_once '../../includes/script.php';
            ?>
            <script src="api/detalleDocente.js"></script>
    </body>

</html>