<?php
include_once '../../../../../loader.php';
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Supervisión Docente | Coordinador </title>
        <link rel="shortcut icon" type="image/png" href="../../../../assets/images/logos/favicon.ico" />
        <link rel="stylesheet" href='../../../../assets/css/styles.min.css' />

    </head>
    <body>
        <?php
        $info  = Sesion::getInfoTemporal("agenda");
        $agenda = $info["listado"];
        $plantel = $info["plantel"];
        $mes = $info["mes"];
        $anio = $info["año"];
        ?>
        <div class="container" id="content">
            <div class="header mb-4">
                <img src="../../../../assets/images/logos/dark-logo.svg" alt="une" class="img-fluid" style="max-width: 100px;">
                <h1 class="h3 mb-0">Universidad de Especialidades</h1>
                <h5 class="h5">Plantel <?= $plantel ?></h5>
            </div>
            <h5>Supervisiones de <strong> <?= isset($agenda[0]) ? htmlspecialchars($agenda[0]["carrera"]) : "" ?> </strong></h5>
            <h6><?= strtoupper(htmlspecialchars($mes) . ", " . htmlspecialchars($anio)) ?></h6>
            <h5><?= isset($agenda[0]) ? "Coordinador: " . htmlspecialchars($agenda[0]["nombre_coordinador"]) : "" ?></h5>

            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre del Docente</th>
                            <th>Materia</th>
                            <th>Fecha y hora</th>
                            <th>Estado Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($agenda)) {
                            if (json_last_error() === JSON_ERROR_NONE && is_array($agenda)) {
                                foreach ($agenda as $item) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($item['nombre_docente']) . '</td>';
                                    echo '<td>' . htmlspecialchars($item['nombre_materia']) . ' (' . htmlspecialchars($item['grupo_materia']) . ')</td>';
                                    echo '<td>' . htmlspecialchars($item['dia_semana']) . ", " . htmlspecialchars($item['fecha']) .
                                    " " . htmlspecialchars(date("H:i", strtotime($item['hora_inicio']))) . " - " . htmlspecialchars(date("H:i", strtotime($item['hora_fin']))) . '</td>';
                                    echo '<td>' . htmlspecialchars($item['status']) . '</td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center">Error al decodificar los datos de la agenda.</td></tr>';
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center">No hay datos disponibles.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="footer text-center mt-4">
                &copy; 2024 Universidad de Especialidades. Todos los derechos reservados.
            </div>
        </div>

        <script src="../../../../assets/libs/jquery/dist/jquery.min.js"></script>
        <script src="../../../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            $(document).ready(function () {
                print();
            });

        </script>
    </body>
</html>

