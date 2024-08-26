<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Supervisión Docente</title>
        <link rel="shortcut icon" type="image/png" href="../../../assets/images/logos/favicon.ico" />
        <link rel="stylesheet" href='../../../assets/css/styles.min.css' />
    </head>
    <body>
        <!--  Body Wrapper -->
        <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
             data-sidebar-position="fixed" data-header-position="fixed">
            <div
                class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
                <div class="d-flex align-items-center justify-content-center w-100">
                    <div class="row justify-content-center w-100">
                        <div class="col-md-8 col-lg-6 col-xxl-3">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="text-end">
                                        <a href="https://universidad-une.com" class="text-lg-end text-md-end text-sm-end d-block py-3">
                                            <img src="../../../assets/images/logos/une-logo.png" width="180" alt="">
                                        </a>
                                    </div>

                                    <form id="revisar_supervision_form">
                                        <div class="mb-3">
                                            <label for="expediente" class="form-label">Número de expediente</label>
                                            <input required="" class="form-control" id="expediente" name="expediente" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                        </div>
                                        <button class="btn btn-primary w-100 fs-4 mb-4 rounded-2">
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            Ingresar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="../../../assets/libs/jquery/dist/jquery.min.js"></script>
        <script src="../../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../../../assets/js/sweetalert.min.js"></script>
        <script src="../../../assets/js/util.js"></script>
        <script src="api/index.js"></script>
    </body>

</html>