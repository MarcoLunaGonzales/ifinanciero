<?php

require_once '../conexion.php';

// Variables de Entrada
$codigo = $_POST["codigo"];

$dbh = new Conexion();

?>

<div class="table-responsive">
    <div class="card-body p-0">
        <table class="table table-condensed">
            <thead>
                <tr style="background-color: #f2f2f2; padding: 10px;">
                    <th class="text-left" style="padding: 10px; font-size: 13px;" width="40%">Personal</th>
                    <th class="text-right" style="padding: 10px; font-size: 13px;" width="20%">Haber Básico</th>
                    <th class="text-right" style="padding: 10px; font-size: 13px;" width="20%">Bono Antigüedad</th>
                    <th class="text-right pr-2" style="padding: 10px; font-size: 13px;" width="20%">Otros Bonos</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "SELECT CONCAT(p.primer_nombre, ' ', p.paterno, ' ', p.materno) as personal, 
                                pre.haber_basico, 
                                pre.bono_antiguedad, 
                                pre.otros_bonos
                            FROM planillas_retroactivos_excepciones pre
                            LEFT JOIN personal p ON p.codigo = pre.cod_personal
                            WHERE pre.cod_planilla = '$codigo'";
                    // echo $sql;
                    $stmtDetalle = $dbh->prepare($sql);
                    $stmtDetalle->execute();
                    $resultados = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

                    if (empty($resultados)) {
                        echo '<tr>
                                <td colspan="4">
                                    <div class="text-center">
                                        <h4 class="text-danger">
                                            <b>No se encontraron registros</b>
                                        </h4>
                                    </div>
                                </td>
                            </tr>';
                    } else {
                        foreach ($resultados as $resultado) {
                ?>
                            <tr>
                                <td class="text-left"><?=$resultado['personal']?></td>
                                <td class="text-right">
                                    <b>
                                        <?php if ($resultado['haber_basico'] == 1) { ?>
                                            <i class="material-icons text-success" title="SI">check_circle</i>
                                        <?php } else { ?>
                                            <i class="material-icons text-danger" title="NO">cancel</i>
                                        <?php } ?>
                                    </b>
                                </td>
                                <td class="text-right">
                                    <b>
                                        <?php if ($resultado['bono_antiguedad'] == 1) { ?>
                                            <i class="material-icons text-success" title="SI">check_circle</i>
                                        <?php } else { ?>
                                            <i class="material-icons text-danger" title="NO">cancel</i>
                                        <?php } ?>
                                    </b>
                                </td>
                                <td class="text-right pr-2">
                                    <b>
                                        <?php if ($resultado['otros_bonos'] == 1) { ?>
                                            <i class="material-icons text-success" title="SI">check_circle</i>
                                        <?php } else { ?>
                                            <i class="material-icons text-danger" title="NO">cancel</i>
                                        <?php } ?>
                                    </b>
                                </td>
                            </tr>
                <?php
                        }
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>