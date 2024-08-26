<!doctype html>
<html lang="es">

    <?php
    include_once '../../includes/head.php';
    ?>
    <style>
        .coordinador-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease-in-out;
        }

        .coordinador-card:hover {
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
        }

        .coordinador-img {
            object-fit: cover;
            width: 100%;
            height: 200px;
        }

        .coordinador-body {
            padding: 20px;
        }

        .coordinador-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .coordinador-email {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .coordinador-label {
            font-weight: bold;
            color: #6c757d;
        }
    </style>
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
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-body">
                                <div class="container py-5">
                                    <h2 class="text-center mb-5">Lista de Coordinadores</h2>
                                    <div class="row mb-4">
                                        <div class="col">
                                            <input type="text" class="form-control" id="searchInput" placeholder="Buscar carrera por nombre, plantel, coordinador, tipo...">
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-2 g-4" id="coordinadores-list">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="editarCoordinadorModal" tabindex="-1" aria-labelledby="editCoordinatorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCoordinatorModalLabel">Modificar Coordinador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editCoordinatorForm">

                            <input type="hidden" id="id_usuario" name="id_usuario">
                            <input type="hidden" id="id_coordinador" name="id_coordinador">
                            <div class="text-center mb-4">
                                <input type="file" id="uploadImage" hidden="" accept="image/jpeg">
                                <img style="cursor: pointer;" id="imgPerfil" alt="Profile Picture" class="rounded-circle mb-3" width="150" height="150">
                                <h5 id="profileName" class="card-title"></h5>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required="">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="apellidos" class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" id="apellidos" name="apellidos" required="">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="correo_electronico" class="form-label">Correo electrónico</label>
                                    <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" required="">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required="" >
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="genero" class="form-label">Género</label>
                                    <select class="form-control" id="genero" name="genero" required="">
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" required="">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="correo" class="form-label">Carreras que coordina</label>
                                    <div id="grupoCarreras">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <?php
        include_once '../../includes/script.php';
        ?>
        <script src="api/listarCoordinador.js"></script>
    </body>

</html>