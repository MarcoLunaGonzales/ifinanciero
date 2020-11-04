<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

$cuentas="and p.codigo in (153,113)";
//$cuentas="";
$stmtG = $dbh->prepare("select DISTINCT p.* from estados_cuenta e join plan_cuentas p on p.codigo=e.cod_plancuenta where e.cod_plancuenta<>0 $cuentas order by 1 desc;");
$stmtG->execute();


?>
<br>
<br>
<br>
<br>
<br>
<div class="container-fluid">

<?php
while ($row = $stmtG->fetch()) {
   $codigo=$row['codigo'];
   $nombre=$row['nombre'];
   $numero=$row['numero'];
   ?>
   <center><h4>INICIO - <b>[<?=$numero?>] <?=$nombre?></b></h4></center>
    <table class="table table-sm table-bordered table-condensed bg-white" width="80%">
   <thead>
    <tr>
        <td class="small font-weight-bold">COMPROBANTE</td>
        <td class="small font-weight-bold">OFICINA</td>
        <td class="small font-weight-bold">AREA</td>
        <td class="small font-weight-bold">CUENTA</td>
        <td class="small font-weight-bold">CUENTA AUXILIAR</td>
        <td class="small font-weight-bold">GLOSA</td>
        <td class="small font-weight-bold">DEBE</td>
        <td class="small font-weight-bold">HABER</td>
    </tr>
   </thead>
<tbody>
   <?php

    

    $stmt2 = $dbh->prepare("SELECT d.* FROM plan_cuentas p join comprobantes_detalle d on p.codigo=d.cod_cuenta join areas a on d.cod_area=a.codigo join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional join comprobantes c on d.cod_comprobante=c.codigo where c.cod_gestion=2020 and p.codigo=$codigo and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '2020-07-01 00:00:00' and '2020-10-31 23:59:59' and d.cod_unidadorganizacional in (829,1,9,5,8,272,10,270,271,1103,2692,3000) and d.cod_area in (39,38,501,13,40,12,11,1235,502) and c.cod_unidadorganizacional in (829,1,9,5,8,272,10,270,271,1103,2692,3000) 
and d.codigo not in (SELECT cod_comprobantedetalle from estados_cuenta where cod_comprobantedetalle in (SELECT d.codigo FROM plan_cuentas p join comprobantes_detalle d on p.codigo=d.cod_cuenta join areas a on d.cod_area=a.codigo join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional join comprobantes c on d.cod_comprobante=c.codigo where c.cod_gestion=2020 and p.codigo=$codigo and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '2020-07-01 00:00:00' and '2020-10-31 23:59:59' and d.cod_unidadorganizacional in (829,1,9,5,8,272,10,270,271,1103,2692,3000) and d.cod_area in (39,38,501,13,40,12,11,1235,502) and c.cod_unidadorganizacional in (829,1,9,5,8,272,10,270,271,1103,2692,3000) order by c.fecha,c.codigo));");
    $stmt2->execute();

    while ($row2 = $stmt2->fetch()) {
    $glosa=$row2['glosa'];
    $debe=$row2['debe'];
    $haber=$row2['haber'];
    $cod_cuenta=$row2['cod_cuenta'];
    $cod_unidadorganizacional=$row2['cod_unidadorganizacional'];
    $cod_area=$row2['cod_area'];
    $cod_cuentaauxiliar=$row2['cod_cuentaauxiliar'];
    $cod_comprobante=$row2['cod_comprobante'];
    
   ?>
     <tr>
       <td width="8%"><small><small><?=nombreComprobante($cod_comprobante)?></small></small></td>
       <td width="4%"><small><small><?=abrevUnidad_solo($cod_unidadorganizacional)?></small></small></td>
       <td width="4%"><small><small><?=abrevArea_solo($cod_area)?></small></small></td>
       <td width="24%"><small><small><?=nameCuenta($cod_cuenta)?></small></small></td>
       <td width="5%"><small><small><?=nameCuentaAux($cod_cuentaauxiliar)?></small></small></td>
       <td width="35%"><small><small><small><?=$glosa?></small></small></small></td>
       <td width="5%"><small><small><?=number_format($debe,2,'.',',')?></small></small></td>
       <td width="5%"><small><small><?=number_format($haber,2,'.',',')?></small></small></td>
     </tr>
   <?php
   }
   ?>

   <?php

    

    $stmt2 = $dbh->prepare("SELECT d.* FROM plan_cuentas p join comprobantes_detalle d on p.codigo=d.cod_cuenta join areas a on d.cod_area=a.codigo join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional join comprobantes c on d.cod_comprobante=c.codigo where c.cod_gestion=2020 and p.codigo=$codigo and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '2020-07-01 00:00:00' and '2020-10-31 23:59:59' and d.cod_unidadorganizacional in (829,1,9,5,8,272,10,270,271,1103,2692,3000) and d.cod_area in (39,38,501,13,40,12,11,1235,502) and c.cod_unidadorganizacional in (829,1,9,5,8,272,10,270,271,1103,2692,3000) 
and d.codigo in (SELECT cod_comprobantedetalle from estados_cuenta where cod_comprobantedetalle in (SELECT d.codigo FROM plan_cuentas p join comprobantes_detalle d on p.codigo=d.cod_cuenta join areas a on d.cod_area=a.codigo join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional join comprobantes c on d.cod_comprobante=c.codigo where c.cod_gestion=2020 and p.codigo=$codigo and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '2020-07-01 00:00:00' and '2020-10-31 23:59:59' and d.cod_unidadorganizacional in (829,1,9,5,8,272,10,270,271,1103,2692,3000) and d.cod_area in (39,38,501,13,40,12,11,1235,502) and c.cod_unidadorganizacional in (829,1,9,5,8,272,10,270,271,1103,2692,3000) and d.cod_cuentaauxiliar=0 order by c.fecha,c.codigo));");
    $stmt2->execute();

    while ($row2 = $stmt2->fetch()) {
    $glosa=$row2['glosa'];
    $debe=$row2['debe'];
    $haber=$row2['haber'];
    $cod_cuenta=$row2['cod_cuenta'];
    $cod_unidadorganizacional=$row2['cod_unidadorganizacional'];
    $cod_area=$row2['cod_area'];
    $cod_cuentaauxiliar=$row2['cod_cuentaauxiliar'];
    $cod_comprobante=$row2['cod_comprobante'];
    
   ?>
     <tr class="bg-danger text-white">
       <td width="8%"><small><small><?=nombreComprobante($cod_comprobante)?></small></small></td>
       <td width="4%"><small><small><?=abrevUnidad_solo($cod_unidadorganizacional)?></small></small></td>
       <td width="4%"><small><small><?=abrevArea_solo($cod_area)?></small></small></td>
       <td width="24%"><small><small><?=nameCuenta($cod_cuenta)?></small></small></td>
       <td width="5%"><small><small><?=nameCuentaAux($cod_cuentaauxiliar)?></small></small></td>
       <td width="35%"><small><small><small><?=$glosa?></small></small></small></td>
       <td width="5%"><small><small><?=number_format($debe,2,'.',',')?></small></small></td>
       <td width="5%"><small><small><?=number_format($haber,2,'.',',')?></small></small></td>
     </tr>
   <?php
   }

   $sqlFiltro="SELECT d.codigo FROM plan_cuentas p join comprobantes_detalle d on p.codigo=d.cod_cuenta join areas a on d.cod_area=a.codigo join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional join comprobantes c on d.cod_comprobante=c.codigo where c.cod_gestion=2020 and p.codigo=$codigo and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '2020-07-01 00:00:00' and '2020-10-31 23:59:59' and d.cod_unidadorganizacional in (829,1,9,5,8,272,10,270,271,1103,2692,3000) and d.cod_area in (39,38,501,13,40,12,11,1235,502) and c.cod_unidadorganizacional in (829,1,9,5,8,272,10,270,271,1103,2692,3000) 
and d.codigo in (SELECT cod_comprobantedetalle from estados_cuenta where cod_comprobantedetalle in (SELECT d.codigo FROM plan_cuentas p join comprobantes_detalle d on p.codigo=d.cod_cuenta join areas a on d.cod_area=a.codigo join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional join comprobantes c on d.cod_comprobante=c.codigo where c.cod_gestion=2020 and p.codigo=$codigo and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '2020-07-01 00:00:00' and '2020-10-31 23:59:59' and d.cod_unidadorganizacional in (829,1,9,5,8,272,10,270,271,1103,2692,3000) and d.cod_area in (39,38,501,13,40,12,11,1235,502) and c.cod_unidadorganizacional in (829,1,9,5,8,272,10,270,271,1103,2692,3000) order by c.fecha,c.codigo));";
   $stmt2 = $dbh->prepare("SELECT d.* FROM estados_cuenta e join comprobantes_detalle de on e.cod_comprobantedetalle=de.codigo where e.cod_comprobantedetalleorigen not in 
(select codigo from estados_cuenta where cod_comprobantedetalle in ($sqlFiltro) and cod_comprobantedetalleorigen=0)
and e.cod_comprobantedetalle in ($sqlFiltro) and e.cod_comprobantedetalleorigen<>0;");
    $stmt2->execute();
   
    while ($row2 = $stmt2->fetch()) {
    $glosa=$row2['glosa'];
    $debe=$row2['debe'];
    $haber=$row2['haber'];
    $cod_cuenta=$row2['cod_cuenta'];
    $cod_unidadorganizacional=$row2['cod_unidadorganizacional'];
    $cod_area=$row2['cod_area'];
    $cod_cuentaauxiliar=$row2['cod_cuentaauxiliar'];
    $cod_comprobante=$row2['cod_comprobante'];
    
   ?>
     <tr class="bg-warning text-white">
       <td width="8%"><small><small><?=nombreComprobante($cod_comprobante)?></small></small></td>
       <td width="4%"><small><small><?=abrevUnidad_solo($cod_unidadorganizacional)?></small></small></td>
       <td width="4%"><small><small><?=abrevArea_solo($cod_area)?></small></small></td>
       <td width="24%"><small><small><?=nameCuenta($cod_cuenta)?></small></small></td>
       <td width="5%"><small><small><?=nameCuentaAux($cod_cuentaauxiliar)?></small></small></td>
       <td width="35%"><small><small><small><?=$glosa?></small></small></small></td>
       <td width="5%"><small><small><?=number_format($debe,2,'.',',')?></small></small></td>
       <td width="5%"><small><small><?=number_format($haber,2,'.',',')?></small></small></td>
     </tr>
   <?php
   }
   ?>

    </tbody>
</table>

<center><h6 class="text-primary"><b>FIN - <?=$nombre?></b></h6></center>
<hr>
   <?php
}
echo $sqlFiltro;
?>
</div>


