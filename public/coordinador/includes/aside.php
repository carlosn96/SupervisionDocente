<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar" data-simplebar>
        <div class="d-flex mb-4 align-items-center justify-content-between">
            <a href="../inicio" class="text-nowrap logo-img ms-0 ms-md-1">
                <img src="../../../assets/images/logos/dark-logo.svg" width="180" alt="">
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav" class="mb-4 pb-2">
                <!-- Menú principal -->
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-5"></i>
                    <span class="hide-menu">Menú</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link primary-hover-bg" href="../inicio/" aria-expanded="false">
                        <span class="aside-icon p-2 bg-light-info rounded-3">
                            <i class="ti ti-layout-dashboard fs-7 text-info"></i>
                        </span>
                        <span class="hide-menu ms-2 ps-1">Inicio</span>
                    </a>
                </li>

                <!-- Módulos -->
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-5"></i>
                    <span class="hide-menu">Módulos</span>
                </li>

                <!-- Grupo -->
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link primary-hover-bg" href="../grupos/" aria-expanded="false">
                        <span class="aside-icon p-2 bg-light-primary rounded-3">
                            <i class="ti ti-user-cog fs-7 text-primary"></i>
                        </span>
                        <span class="hide-menu ms-2 ps-1">Grupos</span>
                    </a>
                </li>

                <!-- Docentes con submenú colapsable -->
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link primary-hover-bg" href="javascript:void(0)" aria-expanded="false" data-bs-toggle="collapse" data-bs-target="#docentes-submenu">
                        <span class="aside-icon p-2 bg-light-primary rounded-3">
                            <i class="ti ti-users fs-7 text-primary"></i>
                        </span>
                        <span class="hide-menu ms-2 ps-1">Docentes</span>
                    </a>
                    <ul id="docentes-submenu" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="../docentes/" class="sidebar-link">
                                <span class="sidebar-icon"></span>
                                <span class="hide-menu">Por ciclo</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="../docentes/agregarDocente.php" class="sidebar-link">
                                <span class="sidebar-icon"></span>
                                <span class="hide-menu">Listado general</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Horario -->
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link primary-hover-bg" href="../horario/" aria-expanded="false">
                        <span class="aside-icon p-2 bg-light-primary rounded-3">
                            <i class="ti ti-calendar-time fs-7 text-primary"></i>
                        </span>
                        <span class="hide-menu ms-2 ps-1">Horario</span>
                    </a>
                </li>

                <!-- Agenda -->
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link primary-hover-bg" href="javascript:void(0)" aria-expanded="false" data-bs-toggle="collapse" data-bs-target="#agenda-submenu">
                        <span class="aside-icon p-2 bg-light-primary rounded-3">
                            <i class="ti ti-calendar-time fs-7 text-primary"></i>
                        </span>
                        <span class="hide-menu ms-2 ps-1">Agenda</span>
                    </a>
                    <ul id="agenda-submenu" class="collapse first-level">
                        <li class="sidebar-item">
                            <a href="../agenda/" class="sidebar-link">
                                <span class="sidebar-icon"></span>
                                <span class="hide-menu">Por carrera</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="../agendaGeneral/" class="sidebar-link">
                                <span class="sidebar-icon"></span>
                                <span class="hide-menu">General</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Informe -->
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link primary-hover-bg" href="../informe/" aria-expanded="false">
                        <span class="aside-icon p-2 bg-light-primary rounded-3">
                            <i class="ti ti-chart-bar fs-7 text-primary"></i>
                        </span>
                        <span class="hide-menu ms-2 ps-1">Informe</span>
                    </a>
                </li>

                <!-- Salir -->
                <li class="sidebar-item">
                    <a class="sidebar-link sidebar-link danger-hover-bg" href="#" onclick="cerrarSesion()" aria-expanded="false">
                        <span class="aside-icon p-2 bg-light-danger rounded-3">
                            <i class="ti ti-login fs-7 text-danger"></i>
                        </span>
                        <span class="hide-menu ms-2 ps-1">Salir</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
