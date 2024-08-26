<!doctype html>
<html lang="es">
    <?php include_once '../../includes/head.php'; ?>
    <body>
        <!--  Body Wrapper -->
        <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
             data-sidebar-position="fixed" data-header-position="fixed">
            <!-- Sidebar Start -->
            <?php include_once '../../includes/aside.php'; ?>
            <!--  Sidebar End -->
            <!--  Main wrapper -->
            <div class="body-wrapper">
                <!--  Header Start -->
                <?php include_once '../../includes/header.php'; ?>
                <!--  Header End -->
                <div class="container-fluid">
                    <!--  Row 1 -->
                    <div class="row">
                        <div class="col-lg-7 mb-3 d-flex align-items-stretch">
                            <div class="card w-100">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h3 class="fw-semibold">Administración</h3>
                                            </div>
                                        </div>
                                        <!-- Planteles Section -->
                                        <div class="col-12 col-md-6 mb-3">
                                            <div class="dropdown w-100">
                                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" id="dropdownMenuPlanteles" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-building"></i> Planteles
                                                </button>
                                                <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuPlanteles">
                                                    <li><a class="dropdown-item" href="../planteles/"><i class="ti ti-settings"></i> Administrar</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <!-- Carreras Section -->
                                        <div class="col-12 col-md-6 mb-3">
                                            <div class="dropdown w-100">
                                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" id="dropdownMenuCarreras" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-book"></i> Carreras
                                                </button>
                                                <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuCarreras">
                                                    <li><a class="dropdown-item" href="../carreras/"><i class="ti ti-list"></i> Ver listado</a></li>
                                                    <li><a class="dropdown-item" href="../carreras/agregarCarrera.php"><i class="ti ti-plus"></i> Agregar nuevo</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <!-- Coordinadores Section -->
                                        <div class="col-12 col-md-6 mb-3">
                                            <div class="dropdown w-100">
                                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" id="dropdownMenuCoordinadores" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-users"></i> Coordinadores
                                                </button>
                                                <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuCoordinadores">
                                                    <li><a class="dropdown-item" href="../coordinadores/"><i class="ti ti-list"></i> Ver listado</a></li>
                                                    <li><a class="dropdown-item" href="../coordinadores/agregarCoordinador.php"><i class="ti ti-plus"></i> Agregar nuevo</a></li>
                                                    <li><a class="dropdown-item" href="../coordinadoresAgenda/"><i class="ti ti-calendar-bolt"></i> Agenda por coordinador</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <!-- Criterios de Supervisión Section -->
                                        <div class="col-12 col-md-6 mb-3">
                                            <div class="dropdown w-100">
                                                <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" id="dropdownMenuSupervision" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-clipboard"></i> Criterios de Supervisión
                                                </button>
                                                <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuSupervision">
                                                    <li><a class="dropdown-item" href="../supervision/"><i class="ti ti-list"></i> Ver listado</a></li>
                                                    <li><a class="dropdown-item" href="../supervision/agregarCriteriosSupervision.php"><i class="ti ti-plus"></i> Agregar nuevo</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="row">
                                <!-- Porcentaje de Supervisiones -->
                                <div class="col-12 col-md-6 col-lg-12 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title mb-3 fw-semibold">Porcentaje de Supervisiones</h5>
                                            <div class="row align-items-center">
                                                <div class="col-7">
                                                    <h4 class="fw-semibold mb-3">165</h4>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="me-1 rounded-circle bg-light-success d-flex align-items-center justify-content-center">
                                                            <i class="ti ti-arrow-up-left text-success"></i>
                                                        </span>
                                                        <p class="text-dark me-1 fs-6 mb-0">+9%</p>
                                                        <p class="fs-6 mb-0">Último ciclo</p>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <span class="bg-primary rounded-circle me-2 d-inline-block"></span>
                                                            <span class="fs-6">SICyT</span>
                                                        </div>
                                                        <div>
                                                            <span class="bg-danger rounded-circle me-2 d-inline-block"></span>
                                                            <span class="fs-6">UDG</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-5">
                                                    <div class="d-flex justify-content-center">
                                                        <div id="grade"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Desempeño por Plantel -->
                                <div class="col-12 col-md-6 col-lg-12 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="row align-items-start">
                                                <div class="col-8">
                                                    <h5 class="card-title mb-3 fw-semibold">Desempeño por Plantel</h5>
                                                    <h4 class="fw-semibold mb-3">Promedio general 9.5</h4>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="me-2 rounded-circle bg-light-danger d-flex align-items-center justify-content-center">
                                                            <i class="ti ti-arrow-down-right text-danger"></i>
                                                        </span>
                                                        <p class="text-dark me-1 fs-6 mb-0">-9%</p>
                                                        <p class="fs-6 mb-0">Último ciclo</p>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="d-flex justify-content-end">
                                                        <div class="text-white bg-danger rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="ti ti-currency-dollar fs-6"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="earning"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  Row 2 -->
                    <div class="row">
                        <div class="col-lg-4 d-flex align-items-stretch">
                            <div class="card w-100">
                                <div class="card-body p-4">
                                    <div class="mb-4">
                                        <h5 class="card-title fw-semibold">Próximas supervisiones</h5>
                                    </div>
                                    <ul class="timeline-widget mb-0 position-relative mb-n5">
                                        <li class="timeline-item d-flex position-relative overflow-hidden">
                                            <div class="timeline-time text-dark flex-shrink-0 text-end">09:30</div>
                                            <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                                <span class="timeline-badge border-2 border border-primary flex-shrink-0 my-2"></span>
                                                <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                            </div>
                                            <div class="timeline-desc fs-3 text-dark mt-n1">Administración de servidores en Ing. Computación</div>
                                        </li>
                                        <li class="timeline-item d-flex position-relative overflow-hidden">
                                            <div class="timeline-time text-dark flex-shrink-0 text-end">10:00 am</div>
                                            <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                                <span class="timeline-badge border-2 border border-info flex-shrink-0 my-2"></span>
                                                <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                            </div>
                                            <div class="timeline-desc fs-3 text-dark mt-n1 fw-semibold">Dinámica de Suelos en Ing. Civi <a
                                                    href="javascript:void(0)" class="text-primary d-block fw-normal">#ML-3467</a>
                                            </div>
                                        </li>
                                        <li class="timeline-item d-flex position-relative overflow-hidden">
                                            <div class="timeline-time text-dark flex-shrink-0 text-end">12:00 am</div>
                                            <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                                <span class="timeline-badge border-2 border border-success flex-shrink-0 my-2"></span>
                                                <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                            </div>
                                            <div class="timeline-desc fs-3 text-dark mt-n1">Teoría de la Comunicación II en Lic. Comunicación</div>
                                        </li>
                                        <li class="timeline-item d-flex position-relative overflow-hidden">
                                            <div class="timeline-time text-dark flex-shrink-0 text-end">09:30 am</div>
                                            <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                                <span class="timeline-badge border-2 border border-warning flex-shrink-0 my-2"></span>
                                                <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                            </div>
                                            <div class="timeline-desc fs-3 text-dark mt-n1 fw-semibold">Estadística para los negocios en Lic. Alta Dirección <a
                                                    href="javascript:void(0)" class="text-primary d-block fw-normal">#ML-3467</a>
                                            </div>
                                        </li>
                                        <li class="timeline-item d-flex position-relative overflow-hidden">
                                            <div class="timeline-time text-dark flex-shrink-0 text-end">09:30 am</div>
                                            <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                                <span class="timeline-badge border-2 border border-danger flex-shrink-0 my-2"></span>
                                                <span class="timeline-badge-border d-block flex-shrink-0"></span>
                                            </div>
                                            <div class="timeline-desc fs-3 text-dark mt-n1 fw-semibold">Habilidades para el Aprendizaje en Lic. Contaduría Pública 
                                            </div>
                                        </li>
                                        <li class="timeline-item d-flex position-relative overflow-hidden">
                                            <div class="timeline-time text-dark flex-shrink-0 text-end">12:00 am</div>
                                            <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                                                <span class="timeline-badge border-2 border border-success flex-shrink-0 my-2"></span>
                                            </div>
                                            <div class="timeline-desc fs-3 text-dark mt-n1">Estructuras de Datos en Ing. Computación</div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 d-flex align-items-stretch">
                            <div class="card w-100">
                                <div class="card-body p-4">
                                    <div class="d-flex mb-4 justify-content-between align-items-center">
                                        <h5 class="mb-0 fw-bold">Status de Coordinadores</h5>
                                    </div>

                                    <div class="table-responsive" data-simplebar>
                                        <table  class="table table-borderless align-middle text-nowrap"  >
                                            <thead>
                                                <tr>
                                                    <th scope="col">Profile</th>
                                                    <th scope="col">Extra classes</th>
                                                    <th scope="col">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-4">
                                                                <img
                                                                    src="../../../assets/images/profile/user1.jpg"
                                                                    width="50"
                                                                    class="rounded-circle"
                                                                    alt=""
                                                                    />
                                                            </div>

                                                            <div>
                                                                <h6 class="mb-1 fw-bolder">Alfonso Hernández</h6>
                                                                <p class="fs-3 mb-0">Prof. English</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="fs-3 fw-normal mb-0 text-success">
                                                            +53
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-light-success rounded-pill text-success px-3 py-2 fs-3"
                                                            >Available</span
                                                        >
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-4">
                                                                <img
                                                                    src="../../../assets/images/profile/user2.jpg"
                                                                    width="50"
                                                                    class="rounded-circle"
                                                                    alt=""
                                                                    />
                                                            </div>

                                                            <div>
                                                                <h6 class="mb-1 fw-bolder">Miguel Ángel Méndez</h6>
                                                                <p class="fs-3 mb-0">Prof. History</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="fs-3 fw-normal mb-0 text-success">
                                                            +68
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-light-primary rounded-pill text-primary px-3 py-2 fs-3"
                                                            >In Class</span
                                                        >
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-4">
                                                                <img
                                                                    src="../../../assets/images/profile/user3.jpg"
                                                                    width="50"
                                                                    class="rounded-circle"
                                                                    alt=""
                                                                    />
                                                            </div>

                                                            <div>
                                                                <h6 class="mb-1 fw-bolder">Federico Hernandez López</h6>
                                                                <p class="fs-3 mb-0">Prof. Maths</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="fs-3 fw-normal mb-0 text-success">
                                                            +94
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-light-danger rounded-pill text-danger px-3 py-2 fs-3"
                                                            >Absent</span
                                                        >
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-4">
                                                                <img
                                                                    src="../../../assets/images/profile/user4.jpg"
                                                                    width="50"
                                                                    class="rounded-circle"
                                                                    alt=""
                                                                    />
                                                            </div>

                                                            <div>
                                                                <h6 class="mb-1 fw-bolder">Miguel Jimenez Fonseca</h6>
                                                                <p class="fs-3 mb-0">Prof. Arts</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="fs-3 fw-normal mb-0 text-success">
                                                            +27
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-light-warning rounded-pill text-warning px-3 py-2 fs-3"
                                                            >On Leave</span
                                                        >
                                                    </td>
                                                </tr>
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
        <?php
        include_once '../../includes/script.php';
        ?>
        <script src="api/index.js"></script>
    </body>

</html>
