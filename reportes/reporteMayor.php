<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

$dbh = new Conexion();
// Preparamos
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("Y-m-d");
if($_POST["fecha_desde"]==""){
  $y=$globalNombreGestion;
  $desde=$y."-01-01";
  $hasta=$y."-12-31";
}else{
  $porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
  $porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);

  $desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
  $hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
  //$desde=strftime('%Y-%m-%d',strtotime($_POST["fecha_desde"]));
  //$hasta=strftime('%Y-%m-%d',strtotime($_POST["fecha_hasta"]));
}

$moneda=$_POST["moneda"];

$codcuenta=$_POST["cuenta"];
$nombreMoneda=nameMoneda($moneda);
$unidadCosto=$_POST['unidad_costo'];
$areaCosto=$_POST['area_costo'];
$unidad=$_POST['unidad'];

$gestion= $_POST["gestion"];
$entidad = $_POST["entidad"];

if($gestion==null){
  $gestion=$globalGestion;
}

$stmtG = $dbh->prepare("SELECT * from gestiones WHERE codigo=:codigo");
$stmtG->bindParam(':codigo',$gestion);
$stmtG->execute();
$resultG = $stmtG->fetch();
$NombreGestion = $resultG['nombre'];

 

if(isset($_POST['glosa_len'])){
 $glosaLen=1; 
}else{
  $glosaLen=0;
}

if(isset($_POST['cuenta_especifica'])){
  $codcuenta=[];
  $codcuenta[0]=$_POST['cuenta_especifica']."@normal";
}
if($unidadCosto==null){
  $unidadCosto=[];$unidad=[];
  $iu=0;
  $stmtUnidad = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 and codigo='$globalUnidad' order by 3");
  $stmtUnidad->execute();
  while ($rowUnidad= $stmtUnidad->fetch(PDO::FETCH_ASSOC)) {
    $unidadCosto[$iu]=$rowUnidad['codigo'];
    $unidad[$iu]=$rowUnidad['codigo'];
    $iu++;
  }
}
if($areaCosto==null){
  $areaCosto=[];
  $iu=0;
  $stmtArea = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
  $stmtArea->execute();
  while ($rowArea= $stmtArea->fetch(PDO::FETCH_ASSOC)) {
    $areaCosto[$iu]=$rowArea['codigo'];
    $iu++;
  }
}
$unidadGeneral="";$unidadAbrev="";$areaAbrev="";
$queryFin="";
for ($i=0; $i < cantidadF($unidadCosto) ; $i++) { 
  $unidadAbrev.=abrevUnidad($unidadCosto[$i]);
  if(($i+1)==cantidadF($unidadCosto)){
    $queryFin.="d.cod_unidadorganizacional=".$unidadCosto[$i].")";
  }else{
    $queryFin.="d.cod_unidadorganizacional=".$unidadCosto[$i]." or ";
  }
   
}
$queryFin.=" and (";
for ($j=0; $j < cantidadF($areaCosto) ; $j++) {
  $areaAbrev.=abrevArea($areaCosto[$j]); 
  if(($j+1)==cantidadF($areaCosto)){
    $queryFin.="d.cod_area=".$areaCosto[$j].")";
  }else{
    $queryFin.="d.cod_area=".$areaCosto[$j]." or ";
  }
   
}
$queryFin.=" and (";
for ($k=0; $k < cantidadF($unidad) ; $k++) { 
  $unidadGeneral.=" ".abrevUnidad($unidad[$k]).", ";
  if(($k+1)==cantidadF($unidad)){
    $queryFin.="c.cod_unidadorganizacional=".$unidad[$k].")";
  }else{
    $queryFin.="c.cod_unidadorganizacional=".$unidad[$k]." or ";
  }
   
}
$queryFin.="order by c.fecha";
$nombreCuentaTitle="";
for ($jj=0; $jj < cantidadF($codcuenta); $jj++) { 
    $porciones1 = explode("@", $codcuenta[$jj]);
    $cuenta=$porciones1[0];
    if($porciones1[1]=="aux"){
      $nombreCuentaTitle.=trim(nameCuentaAux($cuenta)).", ";
    }else{
      $nombreCuentaTitle.=trim(nameCuenta($cuenta)).", ";
    }
}
$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

     if(strlen($nombreCuentaTitle)>190){
        $nombreCuentaTitle=substr($nombreCuentaTitle,0,190)."...";
      }
 ?>
<script> periodo_mayor='<?=$periodoTitle?>';
          cuenta_mayor='<?=trim($nombreCuentaTitle)?>';
          unidad_mayor='<?=$unidadGeneral?>';
 </script>
 <div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon bg-blanco">
                    <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
                  </div>
                  <div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div>
                  <h4 class="card-title text-center">Reporte Libro Mayor</h4>
                  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
                  <h6 class="card-title">Cuenta: <?=$nombreCuentaTitle;?></h6>
                  <h6 class="card-title">Unidad:<?=$unidadGeneral?></h6>
                  <div class="row">
                    <div class="col-sm-6"><h5 class="card-title"><b>Unidades:</b> <small><?=$unidadAbrev?></small></h6></div>
                    <div class="col-sm-6"><h5 class="card-title"><b>Areas:</b> <small><?=$areaAbrev?></small></h6></div>
                  </div> 
                </div>
                <div class="card-body">
                  <div class="table-responsive">
     <?php
    $html='<table id="libro_mayor_rep" class="table table-bordered table-condensed" style="width:100%">'.
            '<thead >'.
            '<tr class="text-center">'.
              '<th colspan="5" class=""></th>'.
              // '<th colspan="3" class="">Bolivianos</th>'.
              '<th colspan="3" class="">'.$nombreMoneda.'</th>'.
            '</tr>'.
            '<tr class="text-center">'.
              //'<th>Entidad</th>'.
              '<th width="5%">Unidad</th>'.
              '<th width="5%">Area</th>'.
              '<th width="7%">Fecha</th>'.
              '<th width="60%">Concepto</th>'.
              '<th width="3%">t/c</th>'.
              // '<th>Debe</th>'.
              // '<th>Haber</th>'.
              // '<th>Saldos</th>'.
              '<th width="5%">Debe</th>'.
              '<th width="5%">Haber</th>'.
              '<th width="5%">Saldos</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>'; 
for ($xx=0; $xx < cantidadF($codcuenta); $xx++) { 
$porciones = explode("@", $codcuenta[$xx]);
$cuenta=$porciones[0];
if($porciones[1]=="aux"){
  $nombreCuenta=nameCuentaAux($cuenta);

 $query1="SELECT d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,d.glosa,d.debe,d.haber,
p.codigo,p.nro_cuenta,p.nombre,d.cod_cuentaauxiliar,
u.abreviatura,a.abreviatura as areaAbrev,
c.cod_unidadorganizacional as unidad,c.fecha
FROM cuentas_auxiliares p 
join comprobantes_detalle d on p.codigo=d.cod_cuentaauxiliar 
join areas a on d.cod_area=a.codigo 
join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional 
join comprobantes c on d.cod_comprobante=c.codigo
where c.cod_gestion=$NombreGestion and p.codigo=$cuenta and c.fecha BETWEEN '$desde' and '$hasta' and ($queryFin";
}else{
  $nombreCuenta=nameCuenta($cuenta);

  $query1="SELECT d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,d.glosa,d.debe,d.haber,
p.codigo,p.numero,p.nombre,d.cod_cuentaauxiliar,
u.abreviatura,a.abreviatura as areaAbrev,
c.cod_unidadorganizacional as unidad,c.fecha
FROM plan_cuentas p 
join comprobantes_detalle d on p.codigo=d.cod_cuenta 
join areas a on d.cod_area=a.codigo 
join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional 
join comprobantes c on d.cod_comprobante=c.codigo
where c.cod_gestion=$NombreGestion and p.codigo=$cuenta and c.fecha BETWEEN '$desde' and '$hasta' and ($queryFin";
}

//echo $query1;

$stmt = $dbh->prepare($query1);
// Ejecutamos
$stmt->execute();
$stmtCount = $dbh->prepare($query1);
$stmtCount->execute();
$contador=0;
while ($rowCount = $stmtCount->fetch(PDO::FETCH_ASSOC)) {
$contador++;
}
if($contador!=0){
$html.='<tr class="bg-plomo">'.
                  '<td colspan="5" class="text-left font-weight-bold">Nombre de la Cuenta: '.$nombreCuenta.' </td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  //'<td style="display: none;"></td>'.
                  // '<td></td>'.
                  // '<td></td>'.
                  // '<td></td>'.
                  '<td></td>'.
                  '<td></td>'.
                  '<td></td>'.      
              '</tr>';
  
}

$index=1; $tDebeTc=0;$tHaberTc=0;$tDebeBol=0;$tHaberBol=0;    
while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
  
    $fechaX=$rowComp['fecha'];
    $codigoX=$rowComp['cod_det'];
    $glosaX=$rowComp['glosa'];
    $unidadX=$rowComp['abreviatura'];
    $areaX=$rowComp['areaAbrev'];
    $debeX=$rowComp['debe'];
    $haberX=$rowComp['haber'];
    $codCuentaAuxiliar=$rowComp['cod_cuentaauxiliar'];
    $cuenta_auxiliarX=nameCuentaAuxiliar($codCuentaAuxiliar);
    $nombreUnidad=nameUnidad($rowComp['unidad']);
    //INICIAR valores de las sumas
    if($glosaLen==0){      
      if(strlen($glosaX)>15){
        $glosaX=substr($glosaX,0,15)."...";
      }
    }
    $tc=obtenerValorTipoCambio($moneda,strftime('%Y-%m-%d',strtotime($fechaX)));
    if($tc==0){$tc=1;}

            $tDebeBol+=$debeX;$tHaberBol+=$haberX;
            $tDebeTc+=$debeX/$tc;$tHaberTc+=$haberX/$tc; 
            $saldoX=$debeX-$haberX; 
            
             $html.='<tr>'.
                      //'<td class="font-weight-bold">'.$nombreUnidad.'</td>'.
                      '<td class="font-weight-bold small">'.$unidadX.'</td>'.
                      '<td class="font-weight-bold small">'.$areaX.'</td>'.
                      '<td class="font-weight-bold small">'.strftime('%d/%m/%Y',strtotime($fechaX)).'</td>'.
                      '<td class="text-left small">['.$cuenta_auxiliarX."] - ".$glosaX.'</td>'.
                      '<td class="font-weight-bold small">'.$tc.'</td>';
                      
                       $html.='<td class="text-right font-weight-bold small">'.number_format($debeX/$tc, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold small">'.number_format($haberX/$tc, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold small">'.number_format($saldoX, 2, '.', ',').'</td>';        
                      
                    $html.='</tr>';
      $entero=floor($tDebeBol);
      $decimal=$tDebeBol-$entero;
      $centavos=floor($decimal*100);
      if($centavos<10){
        $centavos="0".$decimal;
      }
    $index++; 
    }/* Fin del primer while*/
    if($contador!=0){
      $saldoY=$tDebeTc-$tHaberTc;

      $html.='<tr class="bg-secondary text-white">'.
                  '<td colspan="5" class="text-center">Sumas del periodo:</td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  //'<td style="display: none;"></td>'.
                  // '<td class="text-right font-weight-bold small">'.number_format($tDebeBol, 2, '.', ',').'</td>'.
                  // '<td class="text-right font-weight-bold small">'.number_format($tHaberBol, 2, '.', ',').'</td>'.
                  // '<td class="text-right font-weight-bold small">'.number_format(00000, 2, '.', ',').'</td>'. 
                  '<td class="text-right font-weight-bold small">'.number_format($tDebeTc, 2, '.', ',').'</td>'. 
                  '<td class="text-right font-weight-bold small">'.number_format($tHaberTc, 2, '.', ',').'</td>'.
                  '<td class="text-right font-weight-bold small">'.number_format($saldoY, 2, '.', ',').'</td>'.       
              '</tr>';
      $html.='<tr class="bg-secondary text-white">'.
                  '<td colspan="5" class="text-center">Sumas y saldos finales:</td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  //'<td style="display: none;"></td>'.
                  // '<td class="text-right font-weight-bold small">'.number_format($tDebeBol, 2, '.', ',').'</td>'.
                  // '<td class="text-right font-weight-bold small">'.number_format($tHaberBol, 2, '.', ',').'</td>'.
                  // '<td class="text-right font-weight-bold small">'.number_format(00000, 2, '.', ',').'</td>'. 
                  '<td class="text-right font-weight-bold small">'.number_format($tDebeTc, 2, '.', ',').'</td>'. 
                  '<td class="text-right font-weight-bold small">'.number_format($tHaberTc, 2, '.', ',').'</td>'.
                  '<td class="text-right font-weight-bold small">'.number_format($saldoY, 2, '.', ',').'</td>'.       
              '</tr>'; 
            }

}//fin del for de cuentas

$html.=    '</tbody></table>';

echo $html;
?>
                   </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>
