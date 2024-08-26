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
                    <!--  Row 1 -->

                    <div class="row">
                        <div class="col-12">
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h5 class="card-title mb-3 fw-semibold">Accesos rápidos</h5>
                                    <div class="d-flex flex-wrap">
                                        <a href="../docentes/agregarDocente.php" class="btn btn-outline-primary m-1 flex-grow-1"><i class="ti ti-plus"></i> Nuevo docente</a>
                                        <a href="../docentes" class="btn btn-outline-primary m-1 flex-grow-1"><i class="ti ti-list-search"></i> Ver docentes</a>
                                        <a href="../agenda/" class="btn btn-outline-primary m-1 flex-grow-1"><i class="ti ti-calendar-event"></i> Agenda</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="graficaAvanceSupervisiones">
                        <div class="col-12">
                            <!-- Yearly Breakup -->
                            <div class="card overflow-hidden mt-3">
                                <div class="card-body p-4">
                                    <h5 class="card-title mb-3 fw-semibold">Avance de Supervisiones</h5>
                                    <div class="row align-items-center">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <div class="d-flex justify-content-center">
                                                <div id="grade"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex flex-column align-items-start">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="d-inline-block rounded-circle bg-primary" style="width: 8px; height: 8px;"></span>
                                                    <span class="fs-6 text-primary ms-2">Supervisiones realizadas</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="d-inline-block rounded-circle bg-danger" style="width: 8px; height: 8px;"></span>
                                                    <span class="fs-6 text-danger ms-2">Supervisiones sin realizarse</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 d-flex align-items-stretch">
                            <div class="card w-100 mt-3">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <h5 class="card-title fw-semibold mb-0">Supervisiones por día</h5>
                                        <div class="d-flex">
                                            <button id="prevDay" class="btn btn-sm btn-outline-primary me-2" onclick="updateDate(-1)">
                                                <i class="ti ti-arrow-left"></i>
                                            </button>
                                            <button id="nextDay" class="btn btn-sm btn-outline-primary" onclick="updateDate(1)">
                                                <i class="ti ti-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <input class="form-control mb-4" id="fechaTimeLine" type="date">
                                    <ul class="timeline-widget mb-0 position-relative" id="timeLineSupervision">
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8 col-md-12 d-flex align-items-stretch">
                            <div class="card w-100 mt-3">
                                <div class="card-body p-4">
                                    <div class="d-flex mb-4 justify-content-between align-items-center">
                                        <h5 class="mb-0 fw-bold">Agenda general de supervisión docente</h5>
                                        <div class="dropdown">
                                            <button id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" class="rounded-circle btn-transparent btn-sm px-1 btn shadow-none">
                                                <i class="ti ti-dots-vertical fs-7 d-block"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                                <li>
                                                    <a class="dropdown-item" href="#" id="btnDescargarCronograma">Descargar cronograma</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="table-responsive" data-simplebar>
                                        <table class="table table-borderless align-middle text-nowrap" id="tablaAgenda">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Docente</th>
                                                    <th scope="col">Carrera</th>
                                                    <th scope="col">Horario de supervisión</th>
                                                    <th scope="col">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="bodyAgendaSupervision"> 
                                            </tbody>
                                        </table>
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
    <script src="api/inicio.js"></script>
</body>

</html>