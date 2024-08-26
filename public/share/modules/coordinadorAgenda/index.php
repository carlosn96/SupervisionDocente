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
            include_once 'aside.php';
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
                            <div class="row">
                                <div class="col">
                                    <div class="card" id="calendar">
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
                                <div id="qrcode"></div>
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
        <script src="api/coordinadorAgenda.js"></script> 
    </body>

</html>