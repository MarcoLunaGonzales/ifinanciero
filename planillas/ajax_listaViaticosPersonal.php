<?php

require_once '../conexion.php';

// Variables de Entrada
$gestion = $_POST["gestion"];
$mes     = $_POST["mes"];

$dbh = new Conexion();

$sql = "SELECT cd.codigo, cd.fecha, cd.monto, cd.cod_proveedores, UPPER(p.nombre) as nombre_proveedor, SUM(cd.monto) as total
        FROM caja_chica c, caja_chicadetalle cd
        LEFT JOIN af_proveedores p ON p.codigo = cd.cod_proveedores
        WHERE c.codigo = cd.cod_cajachica
        AND cd.cod_cuenta = 469
        AND YEAR(cd.fecha) = '$gestion'
        AND MONTH(cd.fecha) = '$mes'
        AND c.cod_tipocajachica = 34
        AND c.cod_estadoreferencial <> 2
        AND cd.cod_estadoreferencial <> 2
        GROUP BY cd.cod_proveedores
        ORDER BY p.nombre";
// echo $sql;
$stmtViatico = $dbh->prepare($sql);
$stmtViatico->execute();
$stmtViatico->bindColumn('codigo', $codigo);
$stmtViatico->bindColumn('cod_proveedores', $cod_proveedores);
$stmtViatico->bindColumn('nombre_proveedor', $nombre_proveedor);
$stmtViatico->bindColumn('total', $total);

?>

<div class="table-responsive">
    <h5 class="mb-0 d-flex align-items-center" style="background-color: #f2f2f2; padding: 10px;">
        <span class="font-weight-bold">Nombre Completo</span>
        <span class="ml-auto font-weight-bold me">Monto Total</span>
    </h5>
    <?php 
        if ($stmtViatico->rowCount()) {
    ?>
    <div id="accordion">
        <?php 
            $index = 0;
            $total_sum = 0;
            while ($row = $stmtViatico->fetch(PDO::FETCH_BOUND)) {
                $index++;
                $total_sum += $total;
        ?>
        <div class="card m-0">
            <div class="card-header p-0" id="heading<?=$codigo?>">
                <h5 class="mb-0 d-flex align-items-center">
                    <button class="btn btn-link p-1" 
                            data-toggle="collapse" 
                            data-target="#collapse<?=$codigo?>" 
                            aria-expanded="true" 
                            aria-controls="collapse<?=$codigo?>"
                            style="color: black;">
                    <?=$nombre_proveedor?>
                    </button>
                    <span class="ml-auto font-weight-bold me" style="font-size: 14px;"><?=number_format($total, 2);?></span>
                </h5>
            </div>
            <div id="collapse<?=$codigo?>" class="collapse" aria-labelledby="heading<?=$codigo?>" data-parent="#accordion">
                <div class="card-body p-0">
                    <table class="table table-condensed">
                        <thead>
                            <tr style="background-color: #f2f2f2; padding: 10px;">
                                <th class="text-center" style="padding: 10px; font-size: 13px;" width="10%">Nro. Recibo</th>
                                <th class="text-center" style="padding: 10px; font-size: 13px;" width="10%">Fecha</th>
                                <th class="text-left" style="padding: 10px; font-size: 13px;" width="70%">Detalle</th>
                                <th class="text-right" style="padding: 10px; font-size: 13px;" width="10%">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sql = "SELECT cd.codigo, 
                                                DATE_FORMAT(cd.fecha,'%d-%m-%Y') as fecha, 
                                                cd.monto, 
                                                cd.cod_proveedores, 
                                                UPPER(p.nombre) as nombre_proveedor, 
                                                cd.observaciones,
                                                cd.nro_recibo
                                        FROM caja_chica c, caja_chicadetalle cd
                                        LEFT JOIN af_proveedores p ON p.codigo = cd.cod_proveedores
                                        WHERE c.codigo = cd.cod_cajachica
                                        AND cd.cod_cuenta = 469
                                        AND cd.cod_proveedores = '$cod_proveedores'
                                        AND YEAR(cd.fecha) = '$gestion'
                                        AND MONTH(cd.fecha) = '$mes'
                                        AND c.cod_tipocajachica = 34
                                        AND c.cod_estadoreferencial <> 2
                                        AND cd.cod_estadoreferencial <> 2
                                        ORDER BY p.nombre";
                                // echo $sql;
                                $stmtDetalle = $dbh->prepare($sql);
                                $stmtDetalle->execute();
                                $resultados = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($resultados as $resultado) {
                            ?>
                            <tr>
                                <td class="text-center"><?=$resultado['nro_recibo']?></td>
                                <td class="text-center"><?=$resultado['fecha']?></td>
                                <td class="text-left p-2"><?=$resultado['observaciones']?></td>
                                <td class="text-right pr-2"><b><?=$resultado['monto']?></b></td>
                            </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php 
            }
        ?>
        <table class="table table-condensed">
            <tbody>
                <tr style="background-color: #f2f2f2; padding: 10px;">
                    <td class="text-right text-primary"><b>Total:</b></td>
                    <td class="text-right"><b><?=number_format($total_sum,2);?></b></td>
                </tr>
            </tbody>                                      
        </table>
    </div>
    
    <?php 
        }else{
    ?>
        <div class="text-center"><h4 class="text-danger"><b>No se encontró lista de viáticos</b></h4></div>
    <?php 
        }
    ?>
</div>  