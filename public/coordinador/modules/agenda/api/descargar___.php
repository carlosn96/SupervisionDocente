<?php
require "../../../../assets/libs/dompdf/autoload.inc.php";

use Dompdf\Dompdf;
use Dompdf\Options;

if (isset($_GET['agenda'])) {
    $agenda_json = $_GET['agenda'];
    $agenda = json_decode($agenda_json, true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($agenda)) {
        ob_start();
        ?>

        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Supervisión Docente | Coordinador</title>

            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    background-color: #ffffff;
                    margin: 0;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .header img {
                    max-width: 150px;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                    color: #333;
                }
                .header h2 {
                    margin-top: 10px;
                    font-size: 20px;
                    color: #555;
                }
                .header h4, .header h6 {
                    margin-top: 5px;
                    font-size: 16px;
                    color: #777;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                table, th, td {
                    border: 1px solid #ddd;
                }
                th, td {
                    padding: 10px;
                    text-align: left;
                }
                th {
                    background-color: #007bff;
                    color: white;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    font-size: 14px;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="<?='../../../../assets/images/logos/dark-logo.svg' ?>" alt="une">
                <h1>Universidad de Especialidades</h1>
                <h2>Plantel <?= htmlspecialchars($_GET["plantel"]) ?></h2>
                <h4>Supervisiones de <?= isset($agenda[0]) ? htmlspecialchars($agenda[0]["carrera"]) : "" ?> <?= htmlspecialchars($_GET["mes"]) . ", " . htmlspecialchars($_GET["anio"]) ?></h4>
                <h5><?= isset($agenda[0]) ? "Coordinador " . htmlspecialchars($agenda[0]["nombre_coordinador"]) : "" ?></h5>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nombre Docente</th>
                        <th>Materia</th>
                        <th>Fecha y hora</th>
                        <th>Estado Actual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (json_last_error() === JSON_ERROR_NONE && is_array($agenda)) {
                        foreach ($agenda as $item) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($item['nombre_docente']) . '</td>';
                            echo '<td>' . htmlspecialchars($item['nombre_materia']) . '</td>';
                            echo '<td>' . htmlspecialchars($item['dia_semana']) . ", " . htmlspecialchars($item['fecha']) ." " . date("H:i", strtotime($item["hora_inicio"])) ."-". date("H:i", strtotime($item['hora_fin'])).'</td>';
                            echo '<td>' . htmlspecialchars($item['status']) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">Error al decodificar los datos de la agenda.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <div class="footer">
                &copy; 2024 Universidad de Especialidades. Todos los derechos reservados.
            </div>
        </body>
        </html>

        <?php
        $html = ob_get_clean();

        // Opciones para Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Inicializar Dompdf
        $dompdf = new Dompdf($options);

        // Cargar HTML
        $dompdf->loadHtml($html);

        // Configuración del papel
        $dompdf->setPaper('A4', 'portrait');

        // Renderizar el PDF
        $dompdf->render();

        $anio = htmlspecialchars($_GET["anio"]);
        $mes = htmlspecialchars($_GET["mes"]);
        $dompdf->stream("Supervisiones_$mes" . "$anio.pdf", array("Attachment" => true));
    } else {
        echo 'Error al decodificar los datos de la agenda.';
    }
} else {
    echo 'No hay datos disponibles.';
}
?>
