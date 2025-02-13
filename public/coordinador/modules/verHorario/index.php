<!doctype html>
<html lang="es">
    <?php
    include_once '../../../../loader.php';
    include_once '../../includes/head.php';

    $info = Sesion::getInfoTemporal("horario");
    $bloques = $info["bloques"];
    $horario = $info["horario"];
    
    $horario_materias = [];
    
    // Recorremos los bloques y asignamos las materias a los bloques correspondientes
    foreach ($horario as $materia) {
        $inicio = strtotime($materia['hora_inicio']);
        $fin = strtotime($materia['hora_fin']);
        
        foreach ($bloques as $bloque) {
            $inicio_bloque = strtotime($bloque["inicio"]);
            $fin_bloque = strtotime($bloque["fin"]);
            
            // Verificamos si la materia se cruza con el bloque
            if ($inicio < $fin_bloque && $fin > $inicio_bloque) {
                $horario_materias[$bloque['inicio']][$materia['dia_semana']][] = $materia;
            }
        }
    }
    
    // Días de la semana escolarizado
    $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
    ?>
    
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
                    <div class="row">
                        <div class="col-12">
                            <h2 class="mb-1 text-center text-primary">Horario de <?= htmlspecialchars($info["tipo"]) ?></h2>
                            <h4 class="mb-4 text-center text-danger"><?= isset($info["docente"]) ? htmlspecialchars($info["docente"]) : htmlspecialchars($info[$info["tipo"]]) ?></h4>
                            <h5 class="mb-2 text-center text-primary">Plantel <?= Sesion::getInfoTemporal("plantel")["nombre"] ?></h5>
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
                                    <?php foreach ($bloques as $bloque): ?>
                                        <tr>
                                            <td class="align-middle"><?= htmlspecialchars($bloque["inicio"]) ?> - <?= htmlspecialchars($bloque["fin"]) ?></td>
                                            <?php foreach ($dias_semana as $dia): ?>
                                                <td class="align-middle">
                                                    <?php if (isset($horario_materias[$bloque["inicio"]][$dia])): ?>
                                                        <?php foreach ($horario_materias[$bloque["inicio"]][$dia] as $materia): ?>
                                                            <div class="fw-bold text-primary"><?= htmlspecialchars($materia['nombre_materia']); ?></div>
                                                            <small class="text-muted">
                                                                <?= htmlspecialchars($info["tipo"] === 'Docente' ? $materia['grupo'] : $materia['docente']); ?>
                                                            </small><br>
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
        function ready() {}
    </script>
</body>
</html>
