<!doctype html>
<html lang="es">
    <?php
    include_once '../../../../loader.php';
    include_once '../../includes/head.php';
    $info = Sesion::getInfoTemporal("horario");

    $horario = $info["horario"];

    $bloques_horarios = [
        '07:00 - 08:00',
        '08:00 - 09:00',
        '09:00 - 10:00',
        '10:30 - 11:30',
        '11:30 - 12:30',
        '12:30 - 13:30',
        '13:30 - 14:30'
    ];

    $horario_materias = [];

    foreach ($horario as $materia) {
        $inicio = strtotime($materia['hora_inicio']);
        $fin = strtotime($materia['hora_fin']);

        foreach ($bloques_horarios as $bloque) {
            list($inicio_bloque, $fin_bloque) = explode(' - ', $bloque);
            $inicio_bloque = strtotime($inicio_bloque);
            $fin_bloque = strtotime($fin_bloque);

            if ($inicio < $fin_bloque && $fin > $inicio_bloque) {
                $horario_materias[$bloque][$materia['dia_semana']][] = $materia;
            }
        }
    }
    $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
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
                    <div class="row">
                        <div class="col-12">
                            <h2 class="mb-1 text-center text-primary">Horario de <?= htmlspecialchars($info["tipo"]) ?></h2>
                            <h4 class="mb-4 text-center text-danger"><?= isset($info["docente"]) ? htmlspecialchars($info["docente"]) : htmlspecialchars($info["id"]) ?></h4>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12">
                            <table class="table table-hover table-bordered table-striped border-primary">
                                <thead class="table-primary border-primary">
                                    <tr>
                                        <th scope="col">Hora</th>
                                        <?php foreach ($dias_semana as $dia): ?>
                                            <th scope="col"><?= htmlspecialchars($dia) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bloques_horarios as $bloque): ?>
                                        <tr>
                                            <td class="align-middle"><?= htmlspecialchars($bloque); ?></td>
                                            <?php foreach ($dias_semana as $dia): ?>
                                                <td class="align-middle">
                                                    <?php if (isset($horario_materias[$bloque][$dia])): ?>
                                                        <?php foreach ($horario_materias[$bloque][$dia] as $materia): ?>
                                                            <div class="fw-bold text-primary"><?= htmlspecialchars($materia['nombre_materia']); ?></div>
                                                            <small class="text-muted">
                                                                <?= htmlspecialchars($info["tipo"] === 'Docente' ? $materia['grupo'] : $materia['docente']); ?>
                                                            </small><br>
                                                            <?php if ($info["tipo"] === 'DOCENTE'): ?>
                                                                <div class="text-muted"><?= htmlspecialchars($materia['grupo']); ?></div>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <div class="text-muted"></div>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php
    include_once '../../includes/script.php';
    ?>
    <script>
        function ready() {

        }
    </script>
</body>

</html>