<?php

set_time_limit(0);
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsPlanillas.php';
require_once '../layouts/bodylogin2.php';
require_once '../rrhh/configModule.php';

/*
 error_reporting(E_ALL);
 ini_set('display_errors', '1');
*/

$dbh = new Conexion();
$cod_ref=$_GET['cod_ref'];

$array_personal=[];


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
if($sw_auxiliar==0){//sin  distribucion de sueldos pendientes  
  $globalUnidadX=5; //cod unidad por defecto para contabilizacion LA PAZ
   //insertamos cabecera
  $tipoComprobante=3;
  $codEmpresa=1;
  $codAnio=$_SESSION["globalNombreGestion"];
  $codMoneda=1;
  $codEstadoComprobante=1;  
  $mesPlanilla=$_GET["cod_mes"];
  $namemesPlanilla=nombreMes($mesPlanilla);
  $gestionPlanilla=$_GET["cod_gestion"];
  $anioPlanilla=nameGestion($gestionPlanilla);
  $globalUser=$_SESSION['globalUser'];
  $mesTrabajo=$_SESSION['globalMes'];
  $gestionTrabajo=$_SESSION['globalNombreGestion'];
  $glosaCabecera=": Personal IBNORCA registro refrigerios correspondiente a: ".$namemesPlanilla." ".$anioPlanilla; 
  $anioActual=date("Y");
  $mesActual=date("m");
  $diaActual=date("d");    
  $month = $gestionTrabajo."-".$mesTrabajo;
  $aux = date('Y-m-d', strtotime("{$month} + 1 month"));
  $diaUltimo = date('d', strtotime("{$aux} - 1 day"));
  $horasActual=date("H:i:s");
  if((int)$gestionTrabajo<(int)$anioActual){
    $fechaHoraActual=$gestionTrabajo."-".$mesTrabajo."-".$diaUltimo." ".$horasActual;
  }else{
    if((int)$mesActual==(int)$mesTrabajo){
        $fechaHoraActual=date("Y-m-d H:i:s");
    }else{
      $fechaHoraActual=$gestionTrabajo."-".$mesTrabajo."-".$diaUltimo." ".$horasActual;
    } 
  }   
  $numeroComprobante=numeroCorrelativoComprobante($gestionTrabajo,$globalUnidadX,$tipoComprobante,$mesTrabajo);   
  $codComprobante=obtenerCodigoComprobante();
  $sqlInsertCab="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa,created_at,created_by) values ('$codComprobante','$codEmpresa','$globalUnidadX','$codAnio','$codMoneda','$codEstadoComprobante','$tipoComprobante','$fechaHoraActual','$numeroComprobante','$glosaCabecera',NOW(),'$globalUser')";
  $stmtInsertCab = $dbh->prepare($sqlInsertCab);
  $flagSuccess=$stmtInsertCab->execute();
  //creamos array
  $sqlUnidadX="SELECT pd.cod_area,(select ap.cod_area_planilla from areas_planillas_contabilizacion ap where ap.cod_area=pd.cod_area)as cod_area_contabilizacion,(select ap2.orden from areas_planillas_contabilizacion ap2 where ap2.cod_area=pd.cod_area)as orden
    from personal p join personal_area_distribucion pd on p.codigo=pd.cod_personal
    where pd.cod_estadoreferencial=1 and p.cod_estadoreferencial=1 and p.cod_estadopersonal=1 
    GROUP BY pd.cod_area
    order by 3";
  $stmtX = $dbh->prepare($sqlUnidadX);
  $stmtX->execute();
  $cont_areas=0;
  $cont_areas_agrupado=0;
  while ($rowX = $stmtX->fetch(PDO::FETCH_ASSOC)) {
    $cod_areaX=$rowX['cod_area'];
    $cod_area_contabilizacionX=$rowX['cod_area_contabilizacion'];
    $array_area_conta[$cont_areas]=array($cod_areaX,$cod_area_contabilizacionX);
    if (!isset($array_monto_area[$cod_area_contabilizacionX])) {
      $array_monto_area[$cod_area_contabilizacionX]=0;
      $array_area_agrupado[$cont_areas_agrupado]=$cod_area_contabilizacionX;
      $cont_areas_agrupado++;
    }
    $cont_areas++;
 }
  //recorremos todas las areas
  for ($i=0; $i <$cont_areas; $i++) {     
    $datos_area=$array_area_conta[$i];    
    $cod_areaX=$datos_area[0]; //
    $cod_area_contabilizacionX=$datos_area[1]; //
    $nombre_area_contabilizacionX="";
    $totalGanadoAreax=totalRefrigerioArea($gestionPlanilla, $mesPlanilla, $cod_areaX,null);    
    $array_monto_area[$cod_area_contabilizacionX]+=$totalGanadoAreax;
  }
  $ordenDetalle=1;

  $totalRefrigerioX=0;
  for ($j=0; $j <$cont_areas_agrupado; $j++) { 
    $cod_area_contabilizacionX=$array_area_agrupado[$j];
    if($cod_area_contabilizacionX==11){//area de OI debe ser por oficina
      $monto_areaGlobal=$array_monto_area[$cod_area_contabilizacionX];
      $nombre_area=abrevArea($cod_area_contabilizacionX);      
      //
      $sqlUnidadXY="SELECT codigo FROM unidades_organizacionales where cod_estado=1 and centro_costos=1";
      $stmtXY = $dbh->prepare($sqlUnidadXY);
      $stmtXY->execute();      
      while ($rowXY = $stmtXY->fetch(PDO::FETCH_ASSOC)) {
        $codigoUOXY=$rowXY['codigo'];
        // echo "<br>".$codigoUOXY.":";
        $monto_area=totalRefrigerioArea($gestionPlanilla, $mesPlanilla, $cod_area_contabilizacionX,$codigoUOXY);
        // echo $monto_area."<br>";
        if($monto_area>0){
          $montoRefrigerio=$monto_area+$monto_area*0.13;//monto de refrigerio se le suma el 13% a cada area
          $totalRefrigerioX+=$montoRefrigerio;
          $cod_cuenta=233;//sueldos al personal          
          $glosaDetalle1=$nombre_area." Personal IBNORCA refrigerio correspondiene a: ".$namemesPlanilla."/".$anioPlanilla;
          $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codigoUOXY','$cod_area_contabilizacionX','$montoRefrigerio','0','$glosaDetalle1','$ordenDetalle')";
          $stmtInsertDet = $dbh->prepare($sqlInsertDet);
          $flagSuccessDet=$stmtInsertDet->execute();
          $ordenDetalle++;
        }
      }
    }else{
      $monto_area=$array_monto_area[$cod_area_contabilizacionX];
      $nombre_area=abrevArea($cod_area_contabilizacionX);
      $montoRefrigerio=$monto_area+$monto_area*0.13;//monto de refrigerio se le suma el 13% a cada area
      $totalRefrigerioX+=$montoRefrigerio;
      $cod_cuenta=233;//sueldos al personal      
      $glosaDetalle1=$nombre_area." Personal IBNORCA refrigerio correspondiene a: ".$namemesPlanilla."/".$anioPlanilla;
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$globalUnidadX','$cod_area_contabilizacionX','$montoRefrigerio','0','$glosaDetalle1','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;      
    }
  }

  //contra cuenta
  $glosaDetalleGeneral="Personal IBNORCA registro refrigerios correspondiente a: ".$namemesPlanilla."/".$anioPlanilla;      
  $codUOCentroCosto=$globalUnidadX;
  $codAreaCentroCosto="502";
  //TOTAL REFRIGEROP
  // $totalRefrigerioMes=totalLiquidoPagable($gestionPlanilla, $mesPlanilla);
  $totalRefrigerioMes=totalRefrigerioMes($gestionPlanilla, $mesPlanilla);  
  $cod_cuenta=118;
  $cod_cuenta_aux=0;
  $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','$cod_cuenta_aux','$codUOCentroCosto','$codAreaCentroCosto','0','$totalRefrigerioMes','$glosaDetalleGeneral','$ordenDetalle')";
  $stmtInsertDet = $dbh->prepare($sqlInsertDet);
  $flagSuccessDet=$stmtInsertDet->execute();
  $ordenDetalle++;


  ///retencion RC IVA
  $totalRefrigerioX=$totalRefrigerioMes*0.13;
  $glosaDetalleGeneral="Retención del 13% refirgerio mes de a: ".$namemesPlanilla."/".$anioPlanilla;      
  $codUOCentroCosto=$globalUnidadX;
  $codAreaCentroCosto="502";
  $cod_cuenta=132;
  $cod_cuenta_aux=0;
  $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','$cod_cuenta_aux','$codUOCentroCosto','$codAreaCentroCosto','0','$totalRefrigerioX','$glosaDetalleGeneral','$ordenDetalle')";
  $stmtInsertDet = $dbh->prepare($sqlInsertDet);
  $flagSuccessDet=$stmtInsertDet->execute();
  $ordenDetalle++;


  $debe=0;
  $haber=0;
  $sqlDifirencia="SELECT sum(debe)-sum(haber) as diferencia from comprobantes_detalle where cod_comprobante='$codComprobante'";
  $stmtdiferencia = $dbh->prepare($sqlDifirencia);
  $stmtdiferencia->execute();
  $sw_auxiliar=0;
  while ($rowdiferencia = $stmtdiferencia->fetch(PDO::FETCH_ASSOC)) 
  {
    $diferencia=$rowdiferencia['diferencia'];
  }
  if($diferencia>0) {
    $debe=0;
    $haber=$diferencia;
  }
  if($diferencia<0) {
    $debe=$diferencia*(-1);    
    $haber=0;    
  }
  if($debe>0 || $haber>0){
    $cod_cuenta="306";
    $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','$debe','$haber','$glosaDetalleGeneral','$ordenDetalle')";
    $stmtInsertDet = $dbh->prepare($sqlInsertDet);
    $flagSuccessDet=$stmtInsertDet->execute();
    $ordenDetalle++;
  }
  

  //indicamos que ya se realizo el comprbante      
  $stmtUdatePlanilla = $dbh->prepare("UPDATE refrigerios set cod_comprobante='$codComprobante' where codigo=$cod_ref");
  $stmtUdatePlanilla->execute();

  // volver
    ?>
    <script>
          $(document).ready(function()
          {           
             $("#mostrarmodal1").modal("show");         
          });
       </script>
    <?php  

}else{ 
   ?>
   <script>
      $(document).ready(function()
      {           
         $("#mostrarmodal2").modal("show");         
      });
   </script>
<?php }
?>


<!-- modal  -->
<div class="modal fade" id="mostrarmodal1" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-labelledby="basicModal" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">        
        <h4 class="modal-title" id="myModalLabel" align="left"><b>El Proceso ha finalizado correctamente.</b></h4>
      </div>
      <div class="modal-body" align="left">
         Por favor seleccione una opción.
      </div>       
      <div class="modal-footer">          
        <a href="<?=$urlComprobantesLista2;?>" type="button" class="btn btn-success">Ir a Comprobantes</a>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="mostrarmodal2" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel" aria-labelledby="basicModal" >
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">        
        <h4 class="modal-title" id="myModalLabel" align="left"><b>No Se Pudo Completar El Proceso...</b></h4>
      </div>
      <div class="modal-body" align="left">
         Por favor verifique que toda la distribución de sueldos esté correctamente distribuidos.<br><br>
         <b>Error en personal:</b> <br>
         <?php
         // $longitud_array=count($array_personal);
          if($sw_auxiliar==0){
            foreach ($array_personal as $cod_personal_x) {
              $nombre_personal_x=obtenerNombrePersonal($cod_personal_x);
              echo "<b>- Nombre: </b>".$nombre_personal_x."(Cod:".$cod_personal_x.").<br>";
            }
          }

         ?>
      </div>       
    </div>
  </div>
</div>


