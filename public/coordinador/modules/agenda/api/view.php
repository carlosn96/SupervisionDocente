<?php
include_once '../../../../../loader.php';
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Supervisión Docente | Coordinador</title>
        <link rel="shortcut icon" type="image/png" href="../../../../assets/images/logos/favicon.ico" />
        <link rel="stylesheet" href='../../../../assets/css/styles.min.css' />
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <style>


            .header {
                text-align: center;
                margin-bottom: 30px;
            }

            .header img {
                max-width: 120px;
            }

            .header h1,
            .header h5 {
                color: #1d3557;
                font-weight: 700;
            }

            .header h5 {
                color: #457b9d;
                font-size: 18px;
            }

            h5,
            h6 {
                color: #1d3557;
            }

            .info-section {
                margin-bottom: 20px;
            }

            .info-section h5,
            .info-section h6 {
                margin: 5px 0;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }

            .table thead {
                background-color: #f1f3f5;
                font-weight: 600;
            }

            .table th,
            .table td {
                padding: 12px 15px;
                text-align: left;
                border: 1px solid #e1e1e1;
                vertical-align: middle;
            }

            .table tbody tr:nth-child(even) {
                background-color: #f9fafb;
            }

            .table tbody tr:hover {
                background-color: #e9ecef;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            .table td {
                color: #495057;
            }

            .table th {
                color: #212529;
            }

            .footer {
                text-align: center;
                font-size: 14px;
                color: #6c757d;
                margin-top: 20px;
            }

            .footer a {
                text-decoration: none;
                color: #6c757d;
                transition: color 0.2s;
            }

            .footer a:hover {
                color: #1d3557;
            }

            .btn {
                background-color: #1d3557;
                color: #fff;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                font-weight: 500;
                cursor: pointer;
                transition: background-color 0.3s;
                text-align: center;
                display: inline-block;
                margin-top: 20px;
            }

            .btn:hover {
                background-color: #457b9d;
            }

            .btn:focus {
                outline: none;
            }

            /* Media Queries */
            @media (max-width: 768px) {
                .container {
                    padding: 20px;
                }

                .header h1 {
                    font-size: 24px;
                }

                .header h5 {
                    font-size: 16px;
                }
            }

        </style>
    </head>
    <body>
        <?php
        $info = Sesion::getInfoTemporal("agenda");
        $agenda = $info["listado"];
        $coordinador = $info["coordinador"];
        $plantel = $info["plantel"];
        $mes = $info["mes"];
        $anio = $info["año"];
        ?>
        <div class="container" id="content">
            <div class="header mb-4">
                <img src="../../../../assets/images/logos/dark-logo.svg" alt="une" class="img-fluid">
                <h1 class="h3 mb-0">Centro Universitario UNE A. C.</h1>
                <h5 class="h5">Plantel <?= $plantel ?></h5>
            </div>
            <div class="info-section">
                <h5 class="mb-2">Supervisiones de <strong> <?= isset($agenda[0]) ? htmlspecialchars($agenda[0]["carrera"]) : "" ?> </strong></h5>
                <h6 class="mb-3"><?= strtoupper(htmlspecialchars($mes) . ", " . htmlspecialchars($anio)) ?></h6>
                <h5><?= isset($agenda[0]) ? "Coordinador: " . $coordinador : "" ?></h5>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Docente</th>
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
                                echo '<tr><td colspan="4" class="text-center">Error al decodificar los datos de la agenda.</td></tr>';
                            }
                        } else {
                            echo '<tr><td colspan="4" class="text-center">No hay datos disponibles.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="footer text-center mt-4">
                &copy; 2025 <a href="https://www.universidad-une.com" target="_blank">Centro Universitario UNE A. C. </a>. Todos los derechos reservados.
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