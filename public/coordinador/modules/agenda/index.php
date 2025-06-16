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
                                <!-- Columna de navegación de pestañas -->
                                <div class="col-lg-12 col-md-12">
                                    <!-- Pestañas principales (Calendario y Materias) -->
                                    <ul class="nav nav-tabs" id="tabProfesor">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="calendarTab" data-bs-toggle="tab" href="#calendar">Calendario</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="materiasTab" data-bs-toggle="tab" href="#materias">Docentes</a>
                                        </li>
                                    </ul>
                                    <!-- Contenido de las pestañas -->
                                    <div class="tab-content mt-3">
                                        <!-- Pestaña de Calendario -->
                                        <div class="tab-pane fade show active" id="calendar">
                                            <div class="card" id="calendarContent">
                                                <!-- El calendario se cargará aquí -->
                                            </div>
                                        </div>
                                        <!-- Pestaña de Materias (Sin Agendar y Agendados) -->
                                        <div class="tab-pane fade" id="materias">
                                            <div class="card-title fw-semibold mb-4">Lista de docentes en <span id="nombreCarrera"></span></div>
                                            <!-- Pestañas internas para "Sin Agendar" y "Agendados" -->
                                            <ul class="nav nav-tabs" id="materiasTabs">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="sinAgendarTab" data-bs-toggle="tab" href="#sinAgendar">Sin agendar</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="agendadosTab" data-bs-toggle="tab" href="#agendados">Agendados</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" id="agendadosTab" data-bs-toggle="tab" href="#supervisiones">Supervisiones</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content mt-3">
                                                <!-- Pestaña de Sin Agendar -->
                                                <div class="tab-pane fade show active" id="sinAgendar">
                                                    <div id="listaMateriasContainer"></div>
                                                </div>
                                                <!-- Pestaña de Agendados -->
                                                <div class="tab-pane fade" id="agendados">
                                                    <div id="listaSinAgendar"></div>
                                                </div>
                                                <!-- Pestaña de Supervisiones -->
                                                <div class="tab-pane fade" id="supervisiones">
                                                    <!-- Filtro de supervisiones -->
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <h5 class="mb-0">Filtrar Supervisiones</h5>
                                                        <div class="btn-group btn-group-sm mb-3" role="group" aria-label="Segmented filter" style="border: 1px solid #ced4da; border-radius: 0.5rem; overflow: hidden;">
                                                            <input type="radio" class="btn-check" name="filtro" id="filtroTodos" data-filtro="docentesTodos" checked>
                                                            <label class="btn btn-outline-secondary" for="filtroTodos">Todos</label>

                                                            <input type="radio" class="btn-check" name="filtro" id="filtroSupervisados" data-filtro="docentesSupervisados">
                                                            <label class="btn btn-outline-success" for="filtroSupervisados">Supervisados</label>

                                                            <input type="radio" class="btn-check" name="filtro" id="filtroPendientes" data-filtro="docentesPendientes">
                                                            <label class="btn btn-outline-warning" for="filtroPendientes">No Supervisados</label>
                                                        </div>
                                                    </div>
                                                    <div id="listaSupervisiones"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="agregarEventoModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="agregarEventoForm">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="eventModalLabel">Agregar Evento</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="nombreEvento" class="form-label">Nombre del Evento</label>
                                            <input type="text" class="form-control" id="nombreEvento" name="nombreEvento" placeholder="Ingrese el nombre del evento" required>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="eventoUnDia" name="eventoUnDia" checked="">
                                            <label class="form-check-label" for="eventoUnDia">Evento de un solo día</label>
                                        </div>
                                        <div class="mb-3">
                                            <label for="fechaHoraInicioEvento" class="form-label">Fecha y Hora de Inicio</label>
                                            <input type="datetime-local" class="form-control" id="fechaHoraInicioEvento" name="fechaHoraInicioEvento" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="fechaHoraFinEvento" class="form-label">Fecha y Hora de Fin</label>
                                            <input type="datetime-local" class="form-control" id="fechaHoraFinEvento" name="fechaHoraFinEvento" disabled="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="lugar" class="form-label">Lugar</label>
                                            <input type="text" class="form-control" id="lugar" name="lugar" placeholder="Ingrese el lugar del evento" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="detalles" class="form-label">Detalles</label>
                                            <textarea class="form-control" id="detalles" name="detalles" rows="3" placeholder="Ingrese detalles del evento"></textarea>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-primary">Agregar Evento</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <!-- Modal para visualizar y editar la información del evento -->
                    <div class="modal fade" id="visualizarEventoModal" tabindex="-1" aria-labelledby="visualizarModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="visualizarModalLabel">Detalles del Evento</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="idEvento">
                                    <div class="mb-3">
                                        <label for="nombreEventoEdit" class="form-label">Nombre del Evento</label>
                                        <input type="text" class="form-control" id="nombreEventoEdit" onChange="actualizarEvento(event)" placeholder="Ingrese el nombre del evento" data-nombre-campo="nombre">
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="eventoUnDiaEdit" >
                                        <label class="form-check-label" for="eventoUnDiaEdit">Evento de un solo día</label>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fechaHoraInicioEdit" class="form-label">Fecha y Hora de Inicio</label>
                                        <input type="datetime-local" class="form-control" id="fechaHoraInicioEdit" onChange="actualizarEvento(event)" data-nombre-campo="fecha_hora_inicio">
                                    </div>
                                    <div class="mb-3">
                                        <label for="fechaHoraFinEdit" class="form-label">Fecha y Hora de Fin</label>
                                        <input type="datetime-local" class="form-control" id="fechaHoraFinEdit" onChange="actualizarEvento(event)" data-nombre-campo="fecha_hora_fin">
                                    </div>
                                    <div class="mb-3">
                                        <label for="lugarEdit" class="form-label">Lugar</label>
                                        <input type="text" class="form-control" id="lugarEdit" onChange="actualizarEvento(event)" placeholder="Ingrese el lugar del evento" data-nombre-campo="lugar">
                                    </div>
                                    <div class="mb-3">
                                        <label for="detallesEdit" class="form-label">Detalles</label>
                                        <textarea class="form-control" id="detallesEdit" rows="3" onChange="actualizarEvento(event)" placeholder="Ingrese detalles del evento" data-nombre-campo="detalles"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="button" class="btn btn-outline-danger" id="btnEliminarEvento"><i class="ti ti-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal detalles de supervisión -->
                    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="eventModalLabel">Detalles de la Supervisión</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Información de Supervisión -->
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-4"><strong>Docente:</strong></div>
                                            <div class="col-8"><span id="modalDocente"></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4"><strong>Materia:</strong></div>
                                            <div class="col-8"><span id="modalMateria"></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4"><strong>Grupo:</strong></div>
                                            <div class="col-8"><span id="modalGrupo"></span></div>
                                        </div>
                                    </div>

                                    <!-- Fechas de Supervisión -->
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-4"><strong>Inicio:</strong></div>
                                            <div class="col-8"><span id="modalStart"></span></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4"><strong>Finalización:</strong></div>
                                            <div class="col-8"><span id="modalEnd"></span></div>
                                        </div>
                                    </div>

                                    <!-- Estatus de Supervisión -->
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-4"><strong>Estatus:</strong></div>
                                            <div class="col-8"><span id="modalEstatus"></span></div>
                                        </div>
                                    </div>

                                    <!-- Botón para agregar a Google Calendar -->
                                    <div class="mb-4">
                                        <a target="_blank" class="btn btn-info w-100" id="btnAddToCalendar">
                                            <i class="ti ti-calendar-share me-2"></i> Agregar a Google Calendar
                                        </a>
                                    </div>

                                    <!-- QR Code Section -->
                                    <div id="qrcode" class="text-center mb-3">

                                    </div>
                                </div>
                                <div class="modal-footer d-flex justify-content-between">
                                    <button class="btn btn-outline-primary btn-sm" id="btnSupervisarDocente">
                                    </button>
                                    <button onclick="reagendarHorario()" class="btn btn-outline-info btn-sm" id="btnReagendarHorario">
                                        <i class="ti ti-edit-circle me-2"></i> Reagendar horario
                                    </button>
                                    <button onclick="eliminarSupervision()" class="btn btn-outline-danger btn-sm" id="btnEliminarSupervision">
                                        <i class="ti ti-trash me-2"></i> Eliminar supervisión
                                    </button>

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