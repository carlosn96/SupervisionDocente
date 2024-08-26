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
                    <div class="container mt-5">
                        <div class="row">
                            <div class="col">
                                <div class="card card-with-nav">
                                    <div class="card-header">
                                        <div class="row row-nav-line">
                                            <ul class="nav nav-tabs nav-line nav-color-secondary" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active show" data-bs-toggle="tab" href="#home" role="tab" aria-selected="true">Información general</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#settings" role="tab" aria-selected="false">Acceso</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <!-- General Information Tab -->
                                            <div class="tab-pane fade show active" id="home" role="tabpanel">
                                                <div class="text-center mb-4">
                                                    <input type="file" id="uploadImage" hidden="" accept="image/jpeg">
                                                    <img style="cursor: pointer;" id="imgPerfil" alt="Profile Picture" class="rounded-circle mb-3" width="150" height="150">
                                                    <h5 id="profileName" class="card-title"></h5>
                                                </div>
                                                <div class="input-group">
                                                    <input id="nombre" type="text" aria-label="Nombre" class="form-control" placeholder="Nombre">
                                                    <input id="apellidos" type="text" aria-label="Apellidos" class="form-control" placeholder="Apellidos">
                                                    <button class="btn btn-sm btn-primary" type="button" id="btnActualizarNombre" aria-controls="nombre,apellidos">
                                                        <i class="ti ti-reload"></i>
                                                    </button>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-md-4">
                                                        <div class="form-group form-group-default">
                                                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                                                            <div class="input-group mb-3">
                                                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                                                                <button class="btn btn-sm btn-primary" type="button" id="btnActualizarFechaNacimiento" aria-controls="fecha_nacimiento">
                                                                    <i class="ti ti-reload"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group form-group-default">
                                                            <label for="telefono">Teléfono</label>
                                                            <div class="input-group mb-3">
                                                                <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Teléfono">
                                                                <button class="btn btn-sm btn-primary" type="button" id="btnActualizarTelefono" aria-controls="telefono">
                                                                    <i class="ti ti-reload"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group form-group-default">
                                                            <label for="genero">Género</label>
                                                            <div class="input-group">
                                                                <select class="form-control" id="genero">
                                                                    <option>Masculino</option>
                                                                    <option>Femenino</option>
                                                                </select>
                                                                <button class="btn btn-sm btn-primary" type="button" id="btnActualizarGenero" aria-controls="genero">
                                                                    <i class="ti ti-reload"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Configuration Tab -->
                                            <div class="tab-pane fade" id="settings" role="tabpanel">
                                                <form id="configuracionForm">
                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <p id="profileEmail" class="card-text"></p>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="actualizarCorreo" checked="">
                                                                <label class="form-check-label" for="actualizarCorreo">
                                                                    Actualizar correo electrónico
                                                                </label>
                                                            </div>
                                                            <label for="correo" class="form-label">Correo nuevo</label>
                                                            <div class="input-group mb-3">
                                                                <input type="text" class="form-control" id="correo" name="correo" required="">
                                                                <span class="input-group-text">@</span>
                                                                <input type="text" id="dominio" name="dominio" class="form-control" readonly="" value="universidad-une.com">
                                                                <div class="invalid-feedback" id="correoFeedback">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="actualizarContrasena" checked="">
                                                                <label class="form-check-label" for="actualizarContrasena">
                                                                    Actualizar contraseña
                                                                </label>
                                                            </div>
                                                            <div class="form-group form-group-default">
                                                                <label>Nueva contraseña</label>
                                                                <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Nueva Contraseña" required="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3 mb-1">
                                                        <div class="col-md-12">
                                                            <div class="form-group form-group-default">
                                                                <label>Confirmar nueva contraseña</label>
                                                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirmar Nueva Contraseña" required="">
                                                                <div class="invalid-feedback" id="confirmPasswordFeedback">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-right mt-3 mb-3">
                                                        <button class="btn btn-primary" id="btnGuardar">Guardar cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once '../../includes/script.php'; ?>
        <script src="api/perfil.js"></script>
    </body>
</html>
