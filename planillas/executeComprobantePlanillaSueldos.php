<?php

set_time_limit(0);
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsPlanillas.php';
require_once '../layouts/bodylogin2.php';
require_once '../rrhh/configModule.php';

$array_personal;
$dbh = new Conexion();
$codigo_planilla=$_GET['codigo_planilla'];
// echo "planilla:".$codigo_planilla;
//comprobamos ue todos los sueldos del personal estén correctamente distribuidos

$sqlPersonalDistribucion="SELECT cod_personal, SUM(porcentaje) as porcentaje from personal_area_distribucion where cod_estadoreferencial=1 GROUP BY cod_personal
";
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
   //SE DEBE PARAMETRIZAR ESTE CODIGO DE CUENTA PARA LA DEPRECIACION
   $codCuentaDepreciacion=298;
   $codCuentaDepreciacionAF=256;
  $globalUnidadX=5; //cod unidad por defecto para contabilizacion LA PAZ
   //insertamos cabecera
  $tipoComprobante=3;
  $codEmpresa=1;
  $codAnio=$_SESSION["globalNombreGestion"];
  $codMoneda=1;
  $codEstadoComprobante=1;
  // $fechaActual=date("Y-m-d H:i:s");
  $mesPlanilla=$_GET["cod_mes"];
  $namemesPlanilla=nombreMes($mesPlanilla);
  $gestionPlanilla=$_GET["cod_gestion"];
  $anioPlanilla=nameGestion($gestionPlanilla);

  $mesTrabajo=$_SESSION['globalMes'];
  $gestionTrabajo=$_SESSION['globalNombreGestion'];
  $glosaCabecera="Personal IBNORCA registro de sueldos correspondiente a: ".$namemesPlanilla." ".$anioPlanilla;

  //$numeroComprobante=obtenerCorrelativoComprobante($tipoComprobante, $globalUnidadX, $gestionTrabajo, $mesTrabajo);



     // $globalUnidad_ofcen=1;//Oficina central
   // $cod_area_ofcen=obtenerValorConfiguracion(29);//area
   // $tipoComprobante=3;
   // $codEmpresa=1;
   // $mesPlanilla=$_GET["cod_mes"];
   // $gestionPlanilla=$_GET["cod_gestion"];
   // $nombreGestion=nameGestion($gestionPlanilla);
   // $nombreMes=nombreMes($mesPlanilla);

   // $mesTrabajo=$_SESSION['globalMes'];
    $anioActual=date("Y");
    $mesActual=date("m");
    $diaActual=date("d");
    // $mesTrabajo=$_SESSION['globalMes']; 
    // $gestionTrabajo=$_SESSION['globalNombreGestion'];
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

     // //indicamos que ya se realizo el comprbante      
     //  $stmtUdatePlanilla = $dbh->prepare("UPDATE planillas set comprobante=1 where codigo=$codigo_planilla");
     //  $stmtUdatePlanilla->execute();

   // $ordenDetalle=1;//
   $numeroComprobante=numeroCorrelativoComprobante($gestionTrabajo,$globalUnidadX,$tipoComprobante,$mesTrabajo);
   // $numeroComprobante=obtenerCorrelativoComprobante($tipoComprobante, $globalUnidadX, $gestionTrabajo, $mesTrabajo);

  $codComprobante=obtenerCodigoComprobante();
  $sqlInsertCab="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa) values ('$codComprobante','$codEmpresa','$globalUnidadX','$codAnio','$codMoneda','$codEstadoComprobante','$tipoComprobante','$fechaHoraActual','$numeroComprobante','$glosaCabecera')";
  $stmtInsertCab = $dbh->prepare($sqlInsertCab);
  $flagSuccess=$stmtInsertCab->execute();
  //creamos array
  $sqlUnidadX="SELECT pd.cod_area,(select ap.cod_area_planilla from areas_planillas_contabilizacion ap where ap.cod_area=pd.cod_area)as cod_area_contabilizacion
    from personal p join personal_area_distribucion pd on p.codigo=pd.cod_personal
    where pd.cod_estadoreferencial=1 and p.cod_estadoreferencial=1 and p.cod_estadopersonal=1 
    GROUP BY pd.cod_area";
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

    $totalGanadoAreax=totalGanadoArea($gestionPlanilla, $mesPlanilla, $cod_areaX);
    // echo $totalGanadoAreax."<br>";
    $array_monto_area[$cod_area_contabilizacionX]+=$totalGanadoAreax;
  }

  $ordenDetalle=1;
  for ($j=0; $j <$cont_areas_agrupado; $j++) { 
    $cod_area_contabilizacionX=$array_area_agrupado[$j];
    // $cod_area_contabilizacionX=$cod_areay['cod_area_contabilizacion']; //nombre de la sunida
    $monto_area=$array_monto_area[$cod_area_contabilizacionX];
    $nombre_area=abrevArea($cod_area_contabilizacionX);
    // $centroCostosDN=obtenerValorConfiguracion(29);//DN
    $montoAportesUODist=$monto_area*0.1671;
    // $codUODistX=$globalUnidadX;
    $cod_cuenta_personal=227;//sueldos al personal
    $cod_cuenta_aportes=228;
            
    $glosaDetalle1=$nombre_area." Personal IBNORCA registro planillas de sueldos correspondiente a: ".$mesPlanilla."/".$anioPlanilla;
    $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_personal','0','$globalUnidadX','$cod_area_contabilizacionX','$monto_area','0','$glosaDetalle1','$ordenDetalle')";
    $stmtInsertDet = $dbh->prepare($sqlInsertDet);
    $flagSuccessDet=$stmtInsertDet->execute();
    $ordenDetalle++;
    
    $glosaDetalle1=$nombre_area." Aportes Personal IBNORCA registro planillas de sueldos correspondiente a: ".$mesPlanilla."/".$anioPlanilla;
    $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_aportes','0','$globalUnidadX','$cod_area_contabilizacionX','$montoAportesUODist','0','$glosaDetalle1','$ordenDetalle')";
    $stmtInsertDet = $dbh->prepare($sqlInsertDet);
    $flagSuccessDet=$stmtInsertDet->execute();
  }

  //contra cuenta

      $glosaDetalleGeneral="Personal IBNORCA registro planillas de sueldos correspondiente a : ".$mesPlanilla."/".$anioPlanilla;      
      $codUOCentroCosto=$globalUnidadX;
      $codAreaCentroCosto="502";
      //SUELDOS POR PAGAR
      $totalLiquidoPagable=totalLiquidoPagable($gestionPlanilla, $mesPlanilla);
      $cod_cuenta=111;
      $cod_cuenta_aux=0;
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','$cod_cuenta_aux','$codUOCentroCosto','$codAreaCentroCosto','0','$totalLiquidoPagable','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;
      //CAJA PETROLERA      
      $totalCajaSalud=obtenerTotalCPS($gestionPlanilla,$mesPlanilla);
      $cod_cuenta="121";//por defecto
      $cod_cuenta_aux=0;
      $glosaDetalleGeneral="Caja Petrolera aporte correspondiente a : ".$mesPlanilla."/".$anioPlanilla;
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','$cod_cuenta_aux','$codUOCentroCosto','$codAreaCentroCosto','0','$totalCajaSalud','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;

      //AFP PREVISION BBV
      // $totalAFPPrevision=15514.25;
      $totalAFPPrevision=obtenerTotalAFP_prev1($gestionPlanilla,$mesPlanilla);
      $cod_cuenta="122";//por defecto
      $cod_cuenta_aux=0;
      $glosaDetalleGeneral="AFP Prevision aporte correspondiente a : ".$mesPlanilla."/".$anioPlanilla;

      
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','$cod_cuenta_aux','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPPrevision','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;

      //AFP PREVISION BBV
      // $totalAFPPrevision=298.24;
      $totalAFPPrevision=obtenerTotalAFP_prev2($gestionPlanilla,$mesPlanilla);
      $cod_cuenta="122";//por defecto
      $glosaDetalleGeneral="AFP Prevision aporte solidario correspondiente a : ".$mesPlanilla."/".$anioPlanilla;
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPPrevision','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;

      //AFP FUTURO      
      $totalAFPFuturo=obtenerTotalAFP_prev3($gestionPlanilla,$mesPlanilla);
      $cod_cuenta="123";//por defecto
      $glosaDetalleGeneral="AFP Futuro aporte correspondiente a : ".$mesPlanilla."/".$anioPlanilla;
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPFuturo','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;

      //AFP PREVISION BBV
      // $totalAFPFuturo=0;
      $glosaDetalleGeneral="AFP Futuro aporte solidario correspondiente a : ".$mesPlanilla."/".$anioPlanilla;

      
      $totalAFPFuturo=obtenerTotalAFP_prev4($gestionPlanilla,$mesPlanilla);
      $cod_cuenta="123";
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPFuturo','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;

      //PROVIVIENDA
      // $totalProVivienda=1781.20;
      $glosaDetalleGeneral="AFP Prevision provivienda aporte correspondiente a : ".$mesPlanilla."/".$anioPlanilla;
      $totalProVivienda=obtenerTotalprovivienda($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      $cod_cuenta="124";
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalProVivienda','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;

      //PROVIVIENDA
      // $totalProVivienda=1782.79;
      $glosaDetalleGeneral="AFP Futuro provivienda aporte correspondiente a : ".$mesPlanilla."/".$anioPlanilla;
      $totalProVivienda=obtenerTotalprovivienda2($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      $cod_cuenta="124";
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalProVivienda','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;


      //RC IVA
      // $totalRCIVA=108.03;
      $totalRCIVA=obtenerTotalOtrosdescuentos($gestionPlanilla,$mesPlanilla,$globalUnidadX);
      $cod_cuenta="118";
      $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalRCIVA','$glosaDetalleGeneral','$ordenDetalle')";
      $stmtInsertDet = $dbh->prepare($sqlInsertDet);
      $flagSuccessDet=$stmtInsertDet->execute();
      $ordenDetalle++;


      $sqlDifirencia="SELECT sum(debe)-sum(haber) as diferencia from comprobantes_detalle where cod_comprobante='$codComprobante'";
      $stmtdiferencia = $dbh->prepare($sqlDifirencia);
      $stmtdiferencia->execute();
      $sw_auxiliar=0;
      while ($rowdiferencia = $stmtdiferencia->fetch(PDO::FETCH_ASSOC)) 
      {
        $diferencia=$rowdiferencia['diferencia'];
      }
      if($diferencia>0) {
        $cod_cuenta="306";
        $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$diferencia','$glosaDetalleGeneral','$ordenDetalle')";
        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
        $flagSuccessDet=$stmtInsertDet->execute();
        $ordenDetalle++;
      }
      if($diferencia<0) {
        $diferencia=$diferencia*(-1);
        $cod_cuenta="306";
        $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','$diferencia','0','$glosaDetalleGeneral','$ordenDetalle')";
        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
        $flagSuccessDet=$stmtInsertDet->execute();
        $ordenDetalle++; 
      }


      //indicamos que ya se realizo el comprbante      
      $stmtUdatePlanilla = $dbh->prepare("UPDATE planillas set comprobante=1 where codigo=$codigo_planilla");
      $stmtUdatePlanilla->execute();

   ?>
   <script>
      $(document).ready(function()
      {           
         $("#mostrarmodal1").modal("show");         
      });
   </script>
<?php }else{ 
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
        <h4 class="modal-title" id="myModalLabel" align="left"><b>El Proceso ha finalizado.</b></h4>
      </div>
      <div class="modal-body" align="left">
         Por favor seleccione una opción.
      </div>       
      <div class="modal-footer">          
        <a href="<?=$urlComprobantesLista2;?>" type="button" class="btn btn-success">Ir a Comprobantes</a>
        <a href="<?=$urlPlanillasSueldoList2;?>" type="button" class="btn btn-danger">Ir a Planillas</a>
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
         foreach ($array_personal as $cod_personal_x) {
            $nombre_personal_x=obtenerNombrePersonal($cod_personal_x);
            echo "<b>- Nombre: </b>".$nombre_personal_x."(Cod:".$cod_personal_x.").<br>";
         }

         ?>
      </div>       
      <div class="modal-footer">                  
        <a href="<?=$urlPersonalLista2;?>" type="button" class="btn btn-success">Ir a Personal</a>
        <a href="<?=$urlPlanillasSueldoList2;?>" type="button" class="btn btn-danger">Ir a Planillas</a>
      </div>
    </div>
  </div>
</div>


