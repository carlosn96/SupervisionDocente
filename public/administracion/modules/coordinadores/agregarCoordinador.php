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
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold mb-4">Nuevo Coordinador</h5>
                                <div class="card" id="cardCoordinador">
                                    <div class="card-body" >
                                        <form id="coordinadorForm">
                                            <div class="row mb-3">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <label for="nombre" class="form-label">Nombre</label>
                                                    <input type="text" class="form-control" id="nombre" name="nombre" required="">
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="apellido" class="form-label">Apellidos</label>
                                                    <input type="text" class="form-control" id="apellido" name="apellidos" required="">
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <label for="correo" class="form-label">Correo institucional</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="correo" name="correo" required="">
                                                        <span class="input-group-text">@</span>
                                                        <input type="text" id="dominio" name="dominio" class="form-control" readonly="" value="universidad-une.com">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="form-group form-group-default">
                                                        <label for="telefono">Teléfono</label>
                                                        <div class="input-group">
                                                            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Teléfono">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-12 col-md-6 mb-3 mb-md-0">
                                                    <div class="form-group form-group-default">
                                                        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                                                        <div class="input-group">
                                                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="form-group form-group-default">
                                                        <label for="genero">Género</label>
                                                        <div class="input-group">
                                                            <select class="form-control" id="genero" name="genero">
                                                                <option>Masculino</option>
                                                                <option>Femenino</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <label for="correo" class="form-label">Carreras que coordina</label>
                                                <div id="grupoCarreras">
                                                </div>
                                            </div>
                                            <div class="row" >
                                                <div class="container mt-5">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <!-- Nav tabs -->
                                                            <ul class="nav nav-tabs card-header-tabs">
                                                                <li class="nav-item">
                                                                    <a class="nav-link active" data-bs-toggle="tab" href="#tabAvatar">Avatar</a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" data-bs-toggle="tab" href="#tabSubirImagen">Subir Imagen</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="card-body">
                                                            <!-- Tab panes -->
                                                            <div class="tab-content">
                                                                <div class="tab-pane fade show active" id="tabAvatar">
                                                                    <div class="row mt-3">
                                                                        <label class="col-12">Elige un avatar</label>
                                                                        <div class="col-12" id="listaAvatar">
                                                                        </div>
                                                                        <p class="col-12 change-tab-link" style="cursor: pointer; color: blue;">O en su lugar sube una imagen ...</p>
                                                                    </div>
                                                                </div>
                                                                <!-- Subir Imagen Tab -->
                                                                <div class="tab-pane fade" id="tabSubirImagen">
                                                                    <div class="row mt-3">
                                                                        <label class="col-12">Subir Imagen</label>
                                                                        <div class="col-12">
                                                                            <div class="mb-3">
                                                                                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/jpeg">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-outline-primary">Guardar</button>
                                        </form>
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
        <script src="api/coordinador.js"></script> 
    </body>

</html>