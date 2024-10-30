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

                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active" aria-current="page" data-bs-toggle="tab" href="#horario">Horario</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#disponibilidad">Disponibilidad</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="horario">
                                            <div class="container mt-4">
                                                <div class="d-flex justify-content-center mb-4">
                                                    <div class="btn-group" role="group" aria-label="Opciones de horario">
                                                        <input value="grupo" type="radio" class="btn-check" name="tipoHorario" id="profesor" autocomplete="off" checked>
                                                        <label class="btn btn-outline-primary" for="profesor">Horario por grupo</label>
                                                        <input value="profesor" type="radio" class="btn-check" name="tipoHorario" id="grupo" autocomplete="off">
                                                        <label class="btn btn-outline-primary" for="grupo">Horario por profesor</label>
                                                    </div>
                                                </div>
                                                <div id="tabla" class="row">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="disponibilidad">
                                            <h5 class="mt-3 text-center">Disponibilidad</h5>
                                            <div id="consultaHoraDia" class="mt-4">
                                                <h6>Consulta por Hora y Día</h6>
                                                <form id="disponibilidadHoraDiaForm">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="diaSemana" class="form-label">Selecciona un día:</label>
                                                            <select class="form-select" id="diaSemana" name="diaSemana" required>
                                                                <option value="">Elige un día</option>
                                                                <option value="Lunes">Lunes</option>
                                                                <option value="Martes">Martes</option>
                                                                <option value="Miércoles">Miércoles</option>
                                                                <option value="Jueves">Jueves</option>
                                                                <option value="Viernes">Viernes</option>
                                                                <option value="Sábado">Sábado</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="hora" class="form-label">Selecciona una hora:</label>
                                                            <input type="time" class="form-control" id="hora" name="hora">
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="submit" class="btn btn-primary">Consultar Disponibilidad</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="mt-4">
                                                <div id="compDisponibilidad" class="row">
                                                </div>
                                            </div>

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
            <script src="api/horario.js"></script> 
    </body>

</html>