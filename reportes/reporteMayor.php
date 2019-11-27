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
$desde=strftime('%Y-%m-%d',strtotime($_POST["fecha_desde"]));
$hasta=strftime('%Y-%m-%d',strtotime($_POST["fecha_hasta"]));
$moneda=$_POST["moneda"];

$codcuenta=$_POST["cuenta"];
$porciones = explode("@", $codcuenta);
$cuenta=$porciones[0];
if($porciones[1]=="aux"){
  $nombreCuenta=nameCuentaAux($cuenta);
 $query1="SELECT d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,d.glosa,d.debe,d.haber,
p.codigo,p.nro_cuenta,p.nombre,
u.abreviatura,a.abreviatura as areaAbrev,
c.cod_unidadorganizacional as unidad,c.fecha
FROM cuentas_auxiliares p 
join comprobantes_detalle d on p.codigo=d.cod_cuentaauxiliar 
join areas a on d.cod_area=a.codigo 
join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional 
join comprobantes c on d.cod_comprobante=c.codigo
where p.codigo=$cuenta and c.fecha BETWEEN '$desde' and '$hasta' and (";
}else{
  $nombreCuenta=nameCuenta($cuenta);
  $query1="SELECT d.codigo as cod_det,d.cod_area,d.cod_unidadorganizacional,d.glosa,d.debe,d.haber,
p.codigo,p.numero,p.nombre,p.cuenta_auxiliar,
u.abreviatura,a.abreviatura as areaAbrev,
c.cod_unidadorganizacional as unidad,c.fecha
FROM plan_cuentas p 
join comprobantes_detalle d on p.codigo=d.cod_cuenta 
join areas a on d.cod_area=a.codigo 
join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional 
join comprobantes c on d.cod_comprobante=c.codigo
where p.codigo=$cuenta and c.fecha BETWEEN '$desde' and '$hasta' and (";
}


$nombreMoneda=nameMoneda($moneda);
//$tcA=obtenerValorTipoCambio($moneda,$fechaActual);
$unidadCosto=$_POST['unidad_costo'];
$areaCosto=$_POST['area_costo'];
$unidad=$_POST['unidad'];
$unidadGeneral="";


for ($i=0; $i < cantidadF($unidadCosto) ; $i++) { 
  if(($i+1)==cantidadF($unidadCosto)){
    $query1.="d.cod_unidadorganizacional=".$unidadCosto[$i].")";
  }else{
    $query1.="d.cod_unidadorganizacional=".$unidadCosto[$i]." or ";
  }
   
}
$query1.=" and (";
for ($j=0; $j < cantidadF($areaCosto) ; $j++) { 
  if(($j+1)==cantidadF($areaCosto)){
    $query1.="d.cod_area=".$areaCosto[$j].")";
  }else{
    $query1.="d.cod_area=".$areaCosto[$j]." or ";
  }
   
}
$query1.=" and (";
for ($k=0; $k < cantidadF($unidad) ; $k++) { 
  $unidadGeneral.=" ".nameUnidad($unidad[$k]).", ";
  if(($k+1)==cantidadF($unidad)){
    $query1.="c.cod_unidadorganizacional=".$unidad[$k].")";
  }else{
    $query1.="c.cod_unidadorganizacional=".$unidad[$k]." or ";
  }
   
}
$query1.="order by c.fecha";

$stmt = $dbh->prepare($query1);
// Ejecutamos
$stmt->execute();

 ?><div class="content">
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
                  <h6 class="card-title">Periodo: Del <?=strftime('%d/%m/%Y',strtotime($desde));?> al <?=strftime('%d/%m/%Y',strtotime($hasta));?></h6>
                  <h6 class="card-title">Cuenta: <?=$nombreCuenta;?></h6>
                  <h6 class="card-title">Unidad:<?=$unidadGeneral?></h6> 
                </div>
                <div class="card-body">
                  <div class="table-responsive">
     <?php
    $html='<table id="libro_mayor_rep" class="table table-bordered table-condensed" style="width:100%">'.
            '<thead class="bg-principal text-white">'.
            '<tr class="text-center">'.
              '<th colspan="5" class=""></th>'.
              '<th colspan="3" class="">Bolivianos</th>'.
              '<th colspan="3" class="">'.$nombreMoneda.'</th>'.
            '</tr>'.
            '<tr class="text-center">'.
              //'<th>Entidad</th>'.
              '<th>Unidad</th>'.
              '<th>Area</th>'.
              '<th>Fecha</th>'.
              '<th>Concepto</th>'.
              '<th>t/c</th>'.
              '<th>Debe</th>'.
              '<th>Haber</th>'.
              '<th>Saldos</th>'.
              '<th>Debe</th>'.
              '<th>Haber</th>'.
              '<th>Saldos</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>'; 
$index=1; $tDebeTc=0;$tHaberTc=0;$tDebeBol=0;$tHaberBol=0;    
while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $fechaX=$rowComp['fecha'];
    $codigoX=$rowComp['cod_det'];
    $glosaX=$rowComp['glosa'];
    $unidadX=$rowComp['abreviatura'];
    $areaX=$rowComp['areaAbrev'];
    $debeX=$rowComp['debe'];
    $haberX=$rowComp['haber'];
    $nombreUnidad=nameUnidad($rowComp['unidad']);
    //INICIAR valores de las sumas
    
    $tc=obtenerValorTipoCambio($moneda,strftime('%Y-%m-%d',strtotime($fechaX)));
    if($tc==0){$tc=1;}    
             $html.='<tr>'.
                      //'<td class="font-weight-bold">'.$nombreUnidad.'</td>'.
                      '<td class="font-weight-bold">'.$unidadX.'</td>'.
                      '<td class="font-weight-bold">'.$areaX.'</td>'.
                      '<td class="font-weight-bold">'.strftime('%d/%m/%Y',strtotime($fechaX)).'</td>'.
                      '<td>'.$glosaX.'</td>'.
                      '<td class="font-weight-bold">'.$tc.'</td>';
                      $tDebeBol+=$debeX;$tHaberBol+=$haberX;
                      $tDebeTc+=$debeX/$tc;$tHaberTc+=$haberX/$tc;
                       $html.='<td class="text-right font-weight-bold">'.number_format($debeX, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($haberX, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format(00000, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($debeX/$tc, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format($haberX/$tc, 2, '.', ',').'</td>'.
                      '<td class="text-right font-weight-bold">'.number_format(00000, 2, '.', ',').'</td>';        
                      
                    $html.='</tr>';
      $entero=floor($tDebeBol);
      $decimal=$tDebeBol-$entero;
      $centavos=floor($decimal*100);
      if($centavos<10){
        $centavos="0".$decimal;
      }
    $index++; 
    }/* Fin del primer while*/
      $html.='<tr class="bg-secondary text-white">'.
                  '<td colspan="5" class="text-center">Sumas del periodo:</td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  //'<td style="display: none;"></td>'.
                  '<td class="text-right font-weight-bold">'.number_format($tDebeBol, 2, '.', ',').'</td>'.
                  '<td class="text-right font-weight-bold">'.number_format($tHaberBol, 2, '.', ',').'</td>'.
                  '<td class="text-right font-weight-bold">'.number_format(00000, 2, '.', ',').'</td>'. 
                  '<td class="text-right font-weight-bold">'.number_format($tDebeTc, 2, '.', ',').'</td>'. 
                  '<td class="text-right font-weight-bold">'.number_format($tHaberTc, 2, '.', ',').'</td>'.
                  '<td class="text-right font-weight-bold">'.number_format(00000, 2, '.', ',').'</td>'.       
              '</tr>';
      $html.='<tr class="bg-secondary text-white">'.
                  '<td colspan="5" class="text-center">Sumas y saldos finales:</td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  '<td style="display: none;"></td>'.
                  //'<td style="display: none;"></td>'.
                  '<td class="text-right font-weight-bold">'.number_format($tDebeBol, 2, '.', ',').'</td>'.
                  '<td class="text-right font-weight-bold">'.number_format($tHaberBol, 2, '.', ',').'</td>'.
                  '<td class="text-right font-weight-bold">'.number_format(00000, 2, '.', ',').'</td>'. 
                  '<td class="text-right font-weight-bold">'.number_format($tDebeTc, 2, '.', ',').'</td>'. 
                  '<td class="text-right font-weight-bold">'.number_format($tHaberTc, 2, '.', ',').'</td>'.
                  '<td class="text-right font-weight-bold">'.number_format(00000, 2, '.', ',').'</td>'.       
              '</tr></tbody>';
$html.=    '</table>';

echo $html;
?>
                   </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>
