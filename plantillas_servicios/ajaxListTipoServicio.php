<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$codigo=$_GET['plantilla'];
$sql1="SELECT s.*,c.descripcion,c.codigo as servicio_cod from plantillas_servicios_tiposervicio s,cla_servicios c where s.cod_plantillaservicio=$codigo and s.cod_claservicio=c.idclaservicio";
$stmt1 = $dbh->prepare($sql1);
$stmt1->execute();
$index=1;$total=0;
 while ($rowServ = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $descripcion=$rowServ['descripcion'];
    $servicio_cod=$rowServ['servicio_cod'];
    $observaciones=$rowServ['observaciones'];
    $cantidad=$rowServ['cantidad'];
    $monto=$rowServ['monto'];
    $codigo=$rowServ['codigo'];
    $montoTotal=$cantidad*$monto;
    $total+=$montoTotal;
    ?>
  <tr>
    <td><?=$index?></td>
    <td><?=$servicio_cod?></td>
    <td><?=$descripcion?></td>
    <td><?=$observaciones?></td>
    <!--<td class="text-right"><?=$cantidad?></td>
    <td class="text-right"><?=number_format($monto, 2, '.', ',');?></td>
    <td class="text-right"><?=number_format($montoTotal, 2, '.', ',');?></td>-->
    <td><a href="#" class="<?=$buttonDelete;?> btn-link btn-sm" onclick="removeServicioPlantilla(<?=$codigo?>); return false;">
            <i class="material-icons"><?=$iconDelete;?></i>
        </a>
    </td>
  </tr>
    <?php
    $index++;
}
?>
<!--<tr class="font-weight-bold">
    <td colspan="6" class="text-center">TOTAL</td>
    <td class="text-right"><?=number_format($total, 2, '.', ',');?></td>
    <td></td>
  </tr>-->