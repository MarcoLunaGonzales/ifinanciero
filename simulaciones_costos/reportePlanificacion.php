<?php
session_start();
set_time_limit(0);
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
$desdeInicioAnio="";
if($_POST["fecha_desde"]==""){
  $y=$globalNombreGestion;
  $desde=$y."-01-01";
  $hasta=$y."-12-31";
  $desdeInicioAnio=$y."-01-01";
}else{
  $porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
  $porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);
  $desdeInicioAnio=$porcionesFechaDesde[0]."-01-01";
  $desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
  $hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
}

$moneda=1;//$_POST["moneda"]
$nombreMoneda=nameMoneda($moneda);
$tipoCurso=$_POST['tipo_curso'];

if(isset($_POST['resumido'])){
 $resumido=1; 
 $rowSpan=3;
 $sqlSolicitadosInicio="SELECT 1 as codigo,'<i class=\'material-icons bg-primary\'>insert_chart</i>' as nombre,'' as fecha_curso,l.cod_cuenta,GROUP_CONCAT(CONCAT(l.glosa,' ',l.nombre_proveedor)) as glosa,sum(l.presupuestado)as presupuestado,sum(l.ejecutado) as ejecutado,null as proveedor,1 as codigo_ejecutado FROM (";
 $sqlSolicitadosFin=") l where l.codigo_ejecutado!='' group by l.cod_cuenta";
 $solicitados=0;
 $anchoPartida="3%";
}else{
  $resumido=0;
  $anchoPartida="30%"; 
  if(isset($_POST['solicitados'])){
    $rowSpan=4; 
    $sqlSolicitadosInicio="";
    $sqlSolicitadosFin="";
    $solicitados=1;
   }else{
    $rowSpan=3;
    $sqlSolicitadosInicio="SELECT * FROM (";
    $sqlSolicitadosFin=") as l where l.codigo_ejecutado!=''";
    $solicitados=0;
   }
}


$tipoCursoArray=implode(",", $tipoCurso);
$tipoCursoAbrev="";
$tipoCursoAbrev=abrevTipoCurso($tipoCursoArray);

$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

?>
 <div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon bg-blanco">
                    <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
                  </div>
                   <!--<div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div>       -->
                   <h4 class="card-title text-center">Reporte Planificacion - Ejecución</h4>
                </div>
<script> periodo_mayor='<?=$periodoTitle?>';
 </script>

                <div class="card-body">
                  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
                  <h6 class="card-title">Cursos: <?=$tipoCursoAbrev;?></h6>
                  <h6 class="card-title">Capacitación</h6>
                  <div class="table-responsive">
     <?php
    $html='<table id="libro_mayor_rep" class="table table-bordered table-condensed" style="width:100%">'.
            '<thead >'.
            '<tr class="text-center">'.
              '<th colspan="'.$rowSpan.'" class=""></th>'.
              '<th colspan="3" class="">'.$nombreMoneda.'</th>'.
            '</tr>'.
            '<tr class="text-center">'.
              '<th width="'.$anchoPartida.'">Propuesta</th>'.
              '<th width="30%">Cuenta - Detalle</th>';
        if($solicitados==1){
            $html.= '<th width="5%">Estado</th>';
        }      
       $html.='<th width="6%">%</th>'.
              '<th width="5%">Presupuesto</th>'.
              '<th width="5%">Ejecutado</th>'.
              //'<th width="5%">Saldo</th>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';

  $query1=$sqlSolicitadosInicio."select s.codigo as cod_simulacion,s.nombre,s.fecha_curso,sd.codigo,sd.cod_cuenta,sd.glosa,sd.monto_total as presupuestado,
(SELECT d.importe from solicitud_recursosdetalle d join solicitud_recursos so on so.codigo=d.cod_solicitudrecurso where so.cod_simulacion=s.codigo and d.cod_detalleplantilla=sd.codigo) as ejecutado, 
(SELECT d.cod_proveedor from solicitud_recursosdetalle d join solicitud_recursos so on so.codigo=d.cod_solicitudrecurso where so.cod_simulacion=s.codigo and d.cod_detalleplantilla=sd.codigo) as proveedor,
(SELECT pro.nombre from solicitud_recursosdetalle d join solicitud_recursos so on so.codigo=d.cod_solicitudrecurso join af_proveedores pro on pro.codigo=d.cod_proveedor where so.cod_simulacion=s.codigo and d.cod_detalleplantilla=sd.codigo) as nombre_proveedor, 
(SELECT d.cod_detalleplantilla from solicitud_recursosdetalle d join solicitud_recursos so on so.codigo=d.cod_solicitudrecurso where so.cod_simulacion=s.codigo and d.cod_detalleplantilla=sd.codigo) as codigo_ejecutado 
from simulaciones_detalle sd join simulaciones_costos s on s.codigo=sd.cod_simulacioncosto 
WHERE s.cod_tipocurso in($tipoCursoArray) and sd.habilitado=1 and s.cod_estadosimulacion=3 order by s.nombre,sd.cod_cuenta".$sqlSolicitadosFin;

  $stmt = $dbh->prepare($query1);
  // Ejecutamos
  $stmt->execute();
  $stmtCount = $dbh->prepare($query1);
  $stmtCount->execute();
  $contador=0;
  while ($rowCount = $stmtCount->fetch(PDO::FETCH_ASSOC)) {
    $contador++;
  }

  $tc=obtenerValorTipoCambio($moneda,strftime('%Y-%m-%d',strtotime($desdeInicioAnio)));
  if($tc==0){$tc=1;}

  $index=1; 
  $tPresupuesto=0;$tEjecutado=0;$tsaldo=0;
  $tPresupuestoS=0;$tEjecutadoS=0;$tsaldoS=0;    
  $cursoNombre="";
  while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$rowComp['codigo'];
    $nombreX=$rowComp['nombre'];
    $fechaX=$rowComp['fecha_curso'];
    $codCuenta=$rowComp['cod_cuenta'];  
    $glosaX=$rowComp['glosa'];
    $proveedorX=$rowComp['proveedor'];

    $presupuestadoX=$rowComp['presupuestado']/$tc;
    $ejecutadoX=$rowComp['ejecutado']/$tc;
    $nombreCuenta=nameCuenta($codCuenta);

    $estado="EJECUTADO";
    $claseEstado="";
    if($rowComp['codigo_ejecutado']==null){
      $estado="SIN SOLICITUD";
      $claseEstado="text-danger";
      $estadoEjecutado="";
    }else{
      $estadoEjecutado="bg-success";
      if($presupuestadoX>$ejecutadoX){
        $estadoEjecutado="bg-warning";
      }else{
         if($presupuestadoX<$ejecutadoX){
            $estadoEjecutado="bg-danger";
         }
      }
      $estadoEjecutado.=" text-white";
      if($resumido==0){
        $glosaX="<br><b>Detalle: </b>".$glosaX." ".$rowComp['nombre_proveedor'];
        $fechaCurso='<br><b>Fecha Curso: </b>'.strftime('%d/%m/%Y',strtotime($fechaX));
      }else{
        $glosaX="";
        $fechaCurso="";
      }
    } 
    if($cursoNombre!=$nombreX&&$index>1&&$resumido==0){
       $html.='<tr class="bg-plomo">'.
                  '<td colspan="'.($rowSpan-1).'" class="text-left font-weight-bold">Totales Curso '.$cursoNombre.' </td>'.
                  '<td class="text-right font-weight-bold">'.formatNumberDec(($tEjecutadoS/$tPresupuestoS)*100).' %</td>'.      
                  '<td class="text-right font-weight-bold">'.formatNumberDec($tPresupuestoS).'</td>'.      
                  '<td class="text-right font-weight-bold">'.formatNumberDec($tEjecutadoS).'</td>'. 
                  //'<td class="text-right font-weight-bold">'.formatNumberDec($tsaldoS).'</td>'.      
              '</tr>';
        $tPresupuestoS=0;$tEjecutadoS=0;$tsaldoS=0;  
    }

    if($cursoNombre!=$nombreX&&$resumido==0){
       $html.='<tr class="bg-info text-white">'.
                  '<td colspan="'.(($rowSpan-1)+3).'" class="text-center font-weight-bold">CURSO :'.$nombreX.' </td>'.
                  //'<td class="text-right font-weight-bold">'.formatNumberDec($tsaldoS).'</td>'.      
              '</tr>'; 
    }

    $cursoNombre=$nombreX;
    if($presupuestadoX==0){
      $presupuestadoX=1;
    }
    $porcentajePresX=($ejecutadoX/$presupuestadoX)*100;
    $saldoX=$presupuestadoX-$ejecutadoX;

    $tPresupuesto+=$presupuestadoX;
    $tEjecutado+=$ejecutadoX;
    $tsaldo+=$saldoX;
    $tPresupuestoS+=$presupuestadoX;
    $tEjecutadoS+=$ejecutadoX;
    $tsaldoS+=$saldoX;
            
        $html.='<tr class="'.$claseEstado.'">'.
                      '<td class="font-weight-bold small text-left">'.$nombreX.' '.$fechaCurso.'</td>'.
                      '<td class="font-weight-bold small text-left">'.$nombreCuenta.' '.$glosaX.'</td>';
        if($solicitados==1){
            $html.= '<td class="font-weight-bold small">'.$estado.'</td>';
        }                
               $html.='<td class="font-weight-bold small text-right '.$estadoEjecutado.'">'.formatNumberDec($porcentajePresX).' %</td>'.
                      '<td class="font-weight-bold small text-right">'.formatNumberDec($presupuestadoX).'</td>'.
                      '<td class="font-weight-bold small text-right">'.formatNumberDec($ejecutadoX).'</td>';
                      //'<td class="font-weight-bold small text-right">'.formatNumberDec($saldoX).'</td>';      
        $html.='</tr>';
      $index++; 
    }/* Fin del primer while*/
    if($resumido==0){
       $html.='<tr class="bg-plomo">'.
                  '<td colspan="'.($rowSpan-1).'" class="text-left font-weight-bold">Totales Curso '.$cursoNombre.' </td>'.
                  '<td class="text-right font-weight-bold">'.formatNumberDec(($tEjecutadoS/$tPresupuestoS)*100).' %</td>'.      
                  '<td class="text-right font-weight-bold">'.formatNumberDec($tPresupuestoS).'</td>'.      
                  '<td class="text-right font-weight-bold">'.formatNumberDec($tEjecutadoS).'</td>'. 
                  //'<td class="text-right font-weight-bold">'.formatNumberDec($tsaldoS).'</td>'.      
              '</tr>';

    }
    if($contador!=0){
      $html.='<tr class="bg-secondary text-white">'.
                  '<td colspan="'.($rowSpan-1).'" class="text-center">Totales:</td>'.
                  '<td class="text-right font-weight-bold small">'.formatNumberDec(($tEjecutado/$tPresupuesto)*100).' %</td>'.
                  '<td class="text-right font-weight-bold small">'.formatNumberDec($tPresupuesto).'</td>'. 
                  '<td class="text-right font-weight-bold small">'.formatNumberDec($tEjecutado).'</td>'.
                  //'<td class="text-right font-weight-bold small">'.formatNumberDec($tsaldo).'</td>'.      
              '</tr>';
    }

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
