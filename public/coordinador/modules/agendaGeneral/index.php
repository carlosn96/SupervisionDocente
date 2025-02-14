<!doctype html>
<html lang="es">

    <?php include_once '../../includes/head.php'; ?>

    <body>
        <!-- Body Wrapper -->
        <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
             data-sidebar-position="fixed" data-header-position="fixed">

            <!-- Sidebar Start -->
            <?php include_once '../../includes/aside.php'; ?>
            <!-- Sidebar End -->

            <!-- Main Wrapper -->
            <div class="body-wrapper">
                <!-- Header Start -->
                <?php include_once '../../includes/header.php'; ?>
                <!-- Header End -->

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-top border-primary shadow-lg overflow-hidden mt-4">
                                <div class="card-body p-4">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-5">
                                        <!-- Título -->
                                        <h3 class="text-center mb-4 text-primary">Agenda General de Supervisiones</h3>

                                        <!-- Selector Ciclo Escolar -->
                                        <div class="form-group mb-0">
                                            <label for="cicloEscolar" class="form-label">Ciclo Escolar</label>
                                            <select id="cicloEscolar" class="form-select form-control-lg shadow-sm border-secondary">
                                                <!-- Opciones del ciclo escolar (se agregarían dinámicamente) -->
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Calendar Section -->
                                    <div class="card shadow-sm border-light" id="calendarContent">
                                        <!-- El calendario se cargará aquí -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php include_once '../../includes/script.php'; ?>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.13/index.global.min.js'></script>
        <script src="api/agendaGeneral.js"></script>
    </body>

</html>
