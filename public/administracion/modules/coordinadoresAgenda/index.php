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


                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <div class="container py-5">
                                <h2 class="text-center mb-5">Lista de Coordinadores</h2>
                                <div class="row">
                                    <div class="table-responsive" id="coordinador-tab">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal agendar supervision-->
                <div class="modal fade" id="agendaModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Compartir agenda</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">

                                <figure class="mb-4">
                                    <div id="qrContainer" hidden></div>
                                    <img id="qr" class="img-fluid rounded mb-2" alt="Código QR" style="max-width: 250px; height: auto;">
                                    <figcaption class="text-muted">Escanea este código QR para acceder a la agenda o copia la liga para compartirla después</figcaption>
                                </figure>
                                
                                <strong>Sitio</strong> <a id="ligaCompartirAgenda"  href="#" id="url-supervision" target="_blank" class="text-decoration-none"> Agenda de supervisiones</a>

                                <div class="d-flex justify-content-center">
                                    <button onclick="copiarURL()" class="btn btn-sm btn-outline-primary me-3" data-bs-toggle="tooltip" data-bs-placement="top" title="Copiar enlace">
                                        <i class="ti ti-copy"></i>
                                    </button>
                                    <button onclick="descargarQR()" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Descargar QR">
                                        <i class="ti ti-download"></i>
                                    </button>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script src="api/listarCoordinador.js"></script>
    </body>

</html>