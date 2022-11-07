 <table class="table table-condensed" >
    <thead>
      <tr class="text-danger">
        <th>CC</th>
        <th>Personal</th>
        <th>Total Ganado</th>
        <th>Porcentaje</th>
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

// $array_personal;
$dbh = new Conexion();
$codigo_planilla=$_GET['codigo_planilla'];
$globalUnidadX=5; //cod unidad por defecto para contabilizacion LA PAZ
$nameUO=abrevUnidad($globalUnidadX);
$mesPlanilla=$_GET["cod_mes"];
$gestionPlanilla=$_GET["cod_gestion"];
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
  if (!isset($array_monto_area[$cod_area_contabilizacionX])) {
    $array_monto_area[$cod_area_contabilizacionX]=0;
    // $array_area_agrupado[$cont_areas_agrupado]=$cod_area_contabilizacionX;
    // $cont_areas_agrupado++;
  }
}
$array_personal_area=[];
//recorremos todas las areas
for ($i=0; $i <$cont_areas; $i++) {
  $datos_area=$array_area_conta[$i];    
  $cod_areaX=$datos_area[0]; //
  $cod_area_contabilizacionX=$datos_area[1];//
  // $nombre_area=abrevArea($cod_area_contabilizacionX);
  if($cod_area_contabilizacionX==11){//area de OI debe ser por oficina
    $sqlUnidadXY="SELECT codigo FROM unidades_organizacionales where cod_estado=1 and centro_costos=1";
    $stmtXY = $dbh->prepare($sqlUnidadXY);
    $stmtXY->execute();      
    while ($rowXY = $stmtXY->fetch(PDO::FETCH_ASSOC)) {
      $codigoUOXY=$rowXY['codigo'];
      $totalGanadoAreax=totalGanadoArea($gestionPlanilla, $mesPlanilla, $cod_areaX,$codigoUOXY);//para OI el cod_area_contabilizacionX es lo mismo que cod_areaX. no afecta en nada
      $array_uo[$codigoUOXY]=$totalGanadoAreax;
      if($totalGanadoAreax>0){
        //sacamos montos por pesonal
        $sqlPer="SELECT pm.cod_personalcargo,CONCAT_WS(' ',per.primer_nombre,per.paterno,per.materno)as personal,sum(pm.total_ganado*(pad.porcentaje/100))as montoGanado,sum(pad.porcentaje)as porcentaje
        from planillas p join planillas_personal_mes pm on p.codigo=pm.cod_planilla join personal_area_distribucion pad on pm.cod_personalcargo=pad.cod_personal and pad.cod_estadoreferencial=1 join personal per on pm.cod_personalcargo=per.codigo
        where p.cod_gestion='$gestionPlanilla' and p.cod_mes='$mesPlanilla' and pad.cod_area='$cod_areaX' and pad.cod_uo='$codigoUOXY'
        GROUP BY pm.cod_personalcargo 
        order by 2";
        // echo $sqlPer."<br>";
        $stmtPerMonto = $dbh->prepare($sqlPer);
        $stmtPerMonto->execute();
        $arrayPersonal=[];
        while ($rowPerMonto = $stmtPerMonto->fetch(PDO::FETCH_ASSOC)) {
          $cod_personalcargo=$rowPerMonto['cod_personalcargo'];
          $personal=$rowPerMonto['personal'];
          $montoGanado=$rowPerMonto['montoGanado'];
          $porcentaje=$rowPerMonto['porcentaje'];
          $arrayPersonal[$cod_personalcargo]=array($personal,$montoGanado,$porcentaje);
        }
        $array_personal_area[$cod_area_contabilizacionX."_".$codigoUOXY]=$arrayPersonal;
      }
    }
     // var_dump($array_uo);
    $array_monto_area[$cod_area_contabilizacionX]=$array_uo;
  }else{
    //mostramos solo cabecera
    $totalGanadoAreax=totalGanadoArea($gestionPlanilla, $mesPlanilla, $cod_areaX,null);
    $array_monto_area[$cod_area_contabilizacionX]+=$totalGanadoAreax;
    if($totalGanadoAreax>0){
      $sqlPer="SELECT pm.cod_personalcargo,CONCAT_WS(' ',per.primer_nombre,per.paterno,per.materno)as personal,sum(pm.total_ganado*(pad.porcentaje/100))as montoGanado,sum(pad.porcentaje)as porcentaje
      from planillas p join planillas_personal_mes pm on p.codigo=pm.cod_planilla join personal_area_distribucion pad on pm.cod_personalcargo=pad.cod_personal and pad.cod_estadoreferencial=1 join personal per on pm.cod_personalcargo=per.codigo
      where p.cod_gestion='$gestionPlanilla' and p.cod_mes='$mesPlanilla' and pad.cod_area='$cod_areaX'
      GROUP BY pm.cod_personalcargo 
      order by 2";
      // echo $sqlPer."<br>";
      $stmtPerMonto = $dbh->prepare($sqlPer);
      $stmtPerMonto->execute();      
      
      if(!isset($array_personal_area[$cod_area_contabilizacionX])){
        $arrayPersonal=[];  
      }
      while ($rowPerMonto = $stmtPerMonto->fetch(PDO::FETCH_ASSOC)){
        $cod_personalcargo=$rowPerMonto['cod_personalcargo'];
        $personal=$rowPerMonto['personal'];
        $montoGanado=$rowPerMonto['montoGanado'];
        $porcentaje=$rowPerMonto['porcentaje'];
        $arrayPersonal[$cod_personalcargo]=array($personal,$montoGanado,$porcentaje);
      }
      $array_personal_area[$cod_area_contabilizacionX]=$arrayPersonal;
    }
  }
}

//desde aqui para mostrar los datos
foreach ($array_monto_area as $keyArea => $montoTotalAreal) {
  $nombre_area=abrevArea($keyArea);
  if($keyArea==11){//es OI?
    foreach ($montoTotalAreal as $keyUO => $valorUO) {
      $nombreUOXY=abrevUnidad($keyUO);
      if($valorUO>0){ 
        $keyEspecial=$keyArea."_".$keyUO;
        ?>
        <tr style="color:#8e44ad">
          <td class="text-left" onclick="mostrarFilaTablaHorario('<?=$keyEspecial?>');return false;"><i style="font-size: 18px;" class="material-icons text-success" id="icono_<?=$keyEspecial?>">add_circle</i><b><?=$nombreUOXY?>/<?=$nombre_area?></b></td>
          <td class="text-left">-</td><td class="text-right"><b><?=formatNumberDec($valorUO)?></b></td><td></td></tr><?php 
          $totalLiquidoPagable+=$valorUO;
        $arrayPersonal_aux=$array_personal_area[$keyArea."_".$keyUO];
        foreach ($arrayPersonal_aux as $valorPersonal) {
          if($valorPersonal[1]>0){ ?>
            <tr class="d-none fila_<?=$keyEspecial?>"><td><?=$nombreUOXY?>/<?=$nombre_area?></td><td class="text-left"><?=$valorPersonal[0]?></td><td class="text-right"><?=formatNumberDec($valorPersonal[1])?></td><td class="text-left"> <?=$valorPersonal[2]?> % </td></tr>
            <?php
          }
        }
      }  
    }
  }else{
    if($montoTotalAreal>0){ ?>
      <tr style="color:#8e44ad">
        <td class="text-left" onclick="mostrarFilaTablaHorario(<?=$keyArea?>);return false;"><i style="font-size: 18px;" class="material-icons text-success" id="icono_<?=$keyArea?>">add_circle</i><b><?=$nameUO?>/<?=$nombre_area?></b></td>
        <td class="text-left">-</td><td class="text-right"><b><?=formatNumberDec($montoTotalAreal)?></b></td><td></td></tr><?php 
        $totalLiquidoPagable+=$montoTotalAreal;
      $arrayPersonal_aux=$array_personal_area[$keyArea];
      foreach ($arrayPersonal_aux as $valorPersonal) {
        if($valorPersonal[1]>0){ ?>
          <!-- <tr class="d-none fila_<?=$gestion?>"> -->
          <tr class="d-none fila_<?=$keyArea?>" ><td><?=$nameUO?>/<?=$nombre_area?></td><td class="text-left"><?=$valorPersonal[0]?></td><td class="text-right"><?=formatNumberDec($valorPersonal[1])?></td><td class="text-left"> <?=$valorPersonal[2]?> % </td></tr>
          <?php
        }
      }
    }
  }

}

?>
<tr><td><b>TOTAL</b></td><td></td><td class="text-right"><b><?=formatNumberDec($totalLiquidoPagable)?></b></td><td></td></tr>
</tbody>
</table>

