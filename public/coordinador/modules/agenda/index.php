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
                            <h2 class="text-center mb-5">Agenda de Supervisiones</h2>
                            <?php
                            include_once '../../includes/selectorCarrera.php';
                            ?>
                            <div class="row">
                                <div class="col">
                                    <div class="card" id="calendar">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-12">
                                    <div class="row">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title fw-semibold mb-4">Lista de docentes en <span id="nombreCarrera"></span> sin agendar</div>
                                                <div class="accordion accordion-flush" id="listaMateriasContainer">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="card-title fw-semibold mb-4">Lista de profesores agendados</div>
                                                <p id="fechaAgenda"></p>
                                                <div class="accordion accordion-flush" id="listaSinAgendar">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal detalles de supervision-->
                <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="eventModalLabel">Detalles de la Supervisión</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-2">
                                    <div class="col-4"><strong>Docente:</strong></div>
                                    <div class="col-8"><span id="modalDocente"></span></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4"><strong>Materia:</strong></div>
                                    <div class="col-8"><span id="modalMateria"></span></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4"><strong>Inicio:</strong></div>
                                    <div class="col-8"><span id="modalStart"></span></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4"><strong>Finalización:</strong></div>
                                    <div class="col-8"><span id="modalEnd"></span></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4"><strong>Estatus:</strong></div>
                                    <div class="col-8"><span id="modalEstatus"></span></div>
                                </div>
                                <div id="agregarGCalendar">
                                    <a target="_blank" class="btn btn-sm btn-info mb-3" id="btnAddToCalendar">
                                        Agregar a Google Calendar <i class="ti ti-calendar-share ms-1"></i>
                                    </a>
                                </div>
                                <div id="qrcode"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-primary" id="btnSupervisarDocente">Supervisar Docente</button>
                                <button onclick="eliminarSupervision()" type="button" class="btn btn-outline-danger" id="btnEliminarSupervision">Eliminar supervisión</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal agendar supervision-->
                <div class="modal fade" id="supervisionModal">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h5 class="modal-title">Agendar supervisión para <strong id="nombreProfesorTitleAgenda">Profesor</strong></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <form id="agendaSupervisionForm">
                                    <div class="form-group">
                                        <label for="supervisionDate">Fecha de supervisión:</label>
                                        <select class="form-control" name="fechaSupervision" id="fechaSupervisionSelector" required>
                                        </select>
                                        <input id="idHorarioSupervision" name="idHorario" type="text" hidden>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Agendar</button>
                                    </div>

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
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.13/index.global.min.js'></script>
        <script src="api/agenda.js"></script> 
    </body>

</html>