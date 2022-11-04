 <table class="table table-condensed" >
    <thead>
      <tr class="text-danger">
        <th>CC</th>
        <th>Personal</th>
        <th>Total Ganado</th>
        <th>-</th>
      </tr>
    </thead>
    <tbody>
<?php
set_time_limit(0);
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsPlanillas.php';
// require_once '../layouts/bodylogin2.php';
// require_once '../rrhh/configModule.php';

$array_personal;
$dbh = new Conexion();
$codigo_planilla=$_GET['codigo_planilla'];
//comprobamos ue todos los sueldos del personal estén correctamente distribuidos
$sqlPersonalDistribucion="SELECT pd.cod_personal, SUM(pd.porcentaje) as porcentaje 
from personal_area_distribucion pd join personal p on pd.cod_personal=p.codigo 
where pd.cod_estadoreferencial=1 and p.cod_estadoreferencial=1 and p.cod_estadopersonal=1
GROUP BY pd.cod_personal";
$stmtPersonalDistribucion = $dbh->prepare($sqlPersonalDistribucion);
$stmtPersonalDistribucion->execute();
$sw_auxiliar=0;
while ($rowPersonal = $stmtPersonalDistribucion->fetch(PDO::FETCH_ASSOC)) 
{
   if($rowPersonal['porcentaje']!=100){
      $sw_auxiliar++;
      $array_personal[]=$rowPersonal['cod_personal'];
   } 
}
  $globalUnidadX=5; //cod unidad por defecto para contabilizacion LA PAZ
  $nameUO=abrevUnidad($globalUnidadX);
  // $codAnio=$_SESSION["globalNombreGestion"];
  $mesPlanilla=$_GET["cod_mes"];
  // $namemesPlanilla=nombreMes($mesPlanilla);
  $gestionPlanilla=$_GET["cod_gestion"];
  // $anioPlanilla=nameGestion($gestionPlanilla);
  // $globalUser=$_SESSION['globalUser'];
  // $mesTrabajo=$_SESSION['globalMes'];
  // $gestionTrabajo=$_SESSION['globalNombreGestion'];
  //creamos array
  $totalLiquidoPagable=0;
  $sqlUnidadX="SELECT pd.cod_area,(select ap.cod_area_planilla from areas_planillas_contabilizacion ap where ap.cod_area=pd.cod_area)as cod_area_contabilizacion,(select ap2.orden from areas_planillas_contabilizacion ap2 where ap2.cod_area=pd.cod_area)as orden
    from personal p join personal_area_distribucion pd on p.codigo=pd.cod_personal
    where pd.cod_estadoreferencial=1 and p.cod_estadoreferencial=1 and p.cod_estadopersonal=1 
    GROUP BY pd.cod_area
    order by 3";
  $stmtX = $dbh->prepare($sqlUnidadX);
  $stmtX->execute();
  $cont_areas=0;
  // $cont_areas_agrupado=0;
  while ($rowX = $stmtX->fetch(PDO::FETCH_ASSOC)) {
    $cod_areaX=$rowX['cod_area'];
    $cod_area_contabilizacionX=$rowX['cod_area_contabilizacion'];
    $array_area_conta[$cont_areas]=array($cod_areaX,$cod_area_contabilizacionX);    
    $cont_areas++;
  }
  //recorremos todas las areas
  for ($i=0; $i <$cont_areas; $i++) {     
    $datos_area=$array_area_conta[$i];    
    $cod_areaX=$datos_area[0]; //
    $cod_area_contabilizacionX=$datos_area[1]; //
    $nombre_area=abrevArea($cod_area_contabilizacionX);
    if($cod_area_contabilizacionX==11){//area de OI debe ser por oficina
      $sqlUnidadXY="SELECT codigo FROM unidades_organizacionales where cod_estado=1 and centro_costos=1";
      $stmtXY = $dbh->prepare($sqlUnidadXY);
      $stmtXY->execute();      
      while ($rowXY = $stmtXY->fetch(PDO::FETCH_ASSOC)) {
        $codigoUOXY=$rowXY['codigo'];
        $nombreUOXY=abrevUnidad($rowXY['codigo']);
        //sacamos montos por pesonal
        $sqlPer="SELECT pm.cod_personalcargo,CONCAT_WS(' ',per.primer_nombre,per.paterno,per.materno)as personal,pm.total_ganado*(pad.porcentaje/100)as montoGanado
        from planillas p join planillas_personal_mes pm on p.codigo=pm.cod_planilla join personal_area_distribucion pad on pm.cod_personalcargo=pad.cod_personal and pad.cod_estadoreferencial=1 join personal per on pm.cod_personalcargo=per.codigo
        where p.cod_gestion='$gestionPlanilla' and p.cod_mes='$mesPlanilla' and pad.cod_area='$cod_areaX' and pad.cod_uo='$codigoUOXY'";        
        $stmtPerMonto = $dbh->prepare($sqlPer);
        $stmtPerMonto->execute();      
        while ($rowPerMonto = $stmtPerMonto->fetch(PDO::FETCH_ASSOC)) {
          $cod_personalcargo=$rowPerMonto['cod_personalcargo'];
          $personal=$rowPerMonto['personal'];
          $montoGanado=$rowPerMonto['montoGanado'];
          if($montoGanado>0){
            $totalLiquidoPagable+=$montoGanado;
            ?>
            <tr><td><?=$nombreUOXY?>/<?=$nombre_area?></td><td class="text-left"><?=$personal?></td><td class="text-right"><?=formatNumberDec($montoGanado)?></td><td>0</td><td></td></tr>
            <?php
          }
        }
      }
    }else{
      $sqlPer="SELECT pm.cod_personalcargo,CONCAT_WS(' ',per.primer_nombre,per.paterno,per.materno)as personal,pm.total_ganado*(pad.porcentaje/100)as montoGanado
      from planillas p join planillas_personal_mes pm on p.codigo=pm.cod_planilla join personal_area_distribucion pad on pm.cod_personalcargo=pad.cod_personal and pad.cod_estadoreferencial=1 join personal per on pm.cod_personalcargo=per.codigo
      where p.cod_gestion='$gestionPlanilla' and p.cod_mes='$mesPlanilla' and pad.cod_area='$cod_areaX'";
      // echo $sqlPer."<br>";
      $stmtPerMonto = $dbh->prepare($sqlPer);
      $stmtPerMonto->execute();      
      while ($rowPerMonto = $stmtPerMonto->fetch(PDO::FETCH_ASSOC)){
        $cod_personalcargo=$rowPerMonto['cod_personalcargo'];
        $personal=$rowPerMonto['personal'];
        $montoGanado=$rowPerMonto['montoGanado'];
        if($montoGanado>0){
          $totalLiquidoPagable+=$montoGanado;
          ?>
          <tr><td><?=$nameUO?>/<?=$nombre_area?></td><td class="text-left"><?=$personal?></td><td class="text-right"><?=formatNumberDec($montoGanado)?></td><td>0</td><td></td></tr>
          <?php
        }
      }
    }
  }
?>
<tr><td>TOTAL</td><td></td><td></td><td class="text-right"><?=formatNumberDec($totalLiquidoPagable)?></td><td></td></tr>
</tbody>
</table>