<?php

set_time_limit(0);
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsPlanillas.php';
require_once '../layouts/bodylogin2.php';
//require_once '../rrhh/configModule.php';

$array_personal;
$dbh = new Conexion();
$codigo_planilla=$_GET['codigo_planilla'];
$mesPlanilla=$_GET["cod_mes"];
$namemesPlanilla=nombreMes($mesPlanilla);
$gestionPlanilla=$_GET["cod_gestion"];
$anioPlanilla=nameGestion($gestionPlanilla);

//Obtenemos el codigo de planilla para la distribucion de areas
  $sqlCodPlanilla="SELECT codigo from planillas p where p.cod_gestion='$gestionPlanilla' and p.cod_mes='$mesPlanilla'";
  $stmtCodPlanilla = $dbh->prepare($sqlCodPlanilla);
  $stmtCodPlanilla->execute();
  $codigoPlanillaX=0;
  while ($rowCodPlanilla = $stmtCodPlanilla->fetch(PDO::FETCH_ASSOC)) {
    $codigoPlanillaX=$rowCodPlanilla['codigo'];
  }
  //Fin obtener codigo planilla

$globalUnidadX=5; //cod unidad por defecto para contabilizacion LA PAZ
 //insertamos cabecera
$tipoComprobante=3;
$codEmpresa=1;
$codAnio=$_SESSION["globalNombreGestion"];
$codMoneda=1;
$codEstadoComprobante=1;
$globalUser=$_SESSION['globalUser'];
$mesTrabajo=$_SESSION['globalMes'];
$gestionTrabajo=$codAnio;
$glosaCabecera="Personal IBNORCA registro de sueldos retroactivo correspondiente a : ".$namemesPlanilla." ".$anioPlanilla;
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
  from personal p join personal_area_distribucion_planilla pd on p.codigo=pd.cod_personal and pd.cod_planilla='$codigoPlanillaX'
  where pd.cod_estadoreferencial=1 and p.cod_estadoreferencial=1 and p.cod_estadopersonal=1 
  GROUP BY pd.cod_area
  order by 3";

//echo $sqlUnidadX;

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
$totalGanandoMes=0;
for ($i=0; $i <$cont_areas; $i++) {     
  $datos_area=$array_area_conta[$i];    
  $cod_areaX=$datos_area[0]; //
  $cod_area_contabilizacionX=$datos_area[1]; //
  $nombre_area_contabilizacionX="";
  $totalGanadoAreax=totalGanadoAreaRetro($gestionPlanilla, $mesPlanilla, $cod_areaX,null);
  
  echo $cod_areaX." ".$totalGanadoAreax."<br>";
  
  $array_monto_area[$cod_area_contabilizacionX]+=$totalGanadoAreax;
  $totalGanandoMes+=$totalGanadoAreax;
}

$ordenDetalle=1;
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
      $monto_area=totalGanadoAreaRetro($gestionPlanilla, $mesPlanilla, $cod_area_contabilizacionX,$codigoUOXY);
      // echo $monto_area."<br>";
      if($monto_area>0){
        $montoAportesUODist=$monto_area*0.1671;    
        $cod_cuenta_personal=227;//sueldos al personal
        $cod_cuenta_aportes=228;
        $glosaDetalle1=$nombre_area." ".$glosaCabecera;
        $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_personal','0','$codigoUOXY','$cod_area_contabilizacionX','$monto_area','0','$glosaDetalle1','$ordenDetalle')";
        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
        $flagSuccessDet=$stmtInsertDet->execute();
        $ordenDetalle++;
        $glosaDetalle1=$nombre_area." Aportes ".$glosaCabecera;;
        $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_aportes','0','$codigoUOXY','$cod_area_contabilizacionX','$montoAportesUODist','0','$glosaDetalle1','$ordenDetalle')";
        $stmtInsertDet = $dbh->prepare($sqlInsertDet);
        $flagSuccessDet=$stmtInsertDet->execute();
      }
    }
  }else{
    $monto_area=$array_monto_area[$cod_area_contabilizacionX];
    $nombre_area=abrevArea($cod_area_contabilizacionX);
    $montoAportesUODist=$monto_area*0.1671;    
    $cod_cuenta_personal=227;//sueldos al personal
    $cod_cuenta_aportes=228;
    $glosaDetalle1=$nombre_area." ".$glosaCabecera;
    $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_personal','0','$globalUnidadX','$cod_area_contabilizacionX','$monto_area','0','$glosaDetalle1','$ordenDetalle')";
    $stmtInsertDet = $dbh->prepare($sqlInsertDet);
    $flagSuccessDet=$stmtInsertDet->execute();
    $ordenDetalle++;
    $glosaDetalle1=$nombre_area." Aportes ".$glosaCabecera;
    $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta_aportes','0','$globalUnidadX','$cod_area_contabilizacionX','$montoAportesUODist','0','$glosaDetalle1','$ordenDetalle')";
    $stmtInsertDet = $dbh->prepare($sqlInsertDet);
    $flagSuccessDet=$stmtInsertDet->execute();
  }
}

//contra cuenta
$glosaDetalleGeneral=$glosaCabecera;
$codUOCentroCosto=$globalUnidadX;
$codAreaCentroCosto="502";
//SUELDOS POR PAGAR
//para sacar liquido pagable por mes, primero sacamos los descuentos

/*$ap_vejez=$totalGanandoMes*10/100;//10%
$riesgo_prof=$totalGanandoMes*1.71/100;//1.7%
$com_afp=$totalGanandoMes*0.5/100;//0.5%
$aporte_sol=$totalGanandoMes*0.5/100;//0.5%*/

$totalAFPMesRetroactivoCalculado=obtenerTotalAFPRetroactivos($gestionPlanilla, $mesPlanilla);
$aporte_sol_13_25_35=obtenerTotalAFP_prev2_retroactivos($gestionPlanilla, $mesPlanilla);

//$total_descuentosMes=$ap_vejez+$riesgo_prof+$com_afp+$aporte_sol+$aporte_sol_13_25_35;
$total_descuentosMes=$totalAFPMesRetroactivoCalculado+$aporte_sol_13_25_35;

echo "total AFP CALCULADO: ".$totalAFPMesRetroactivoCalculado." SOLIDARIO132535: ".$aporte_sol_13_25_35;

$totalLiquidoPagable=$totalGanandoMes-$total_descuentosMes;

//$afpmes=$ap_vejez+$riesgo_prof+$com_afp+$aporte_sol;
// $aporte_solidario_13000 = obtenerAporteSolidario13000($totalGanandoMes);
// $aporte_solidario_25000 = obtenerAporteSolidario25000($totalGanandoMes);
// $aporte_solidario_35000 = obtenerAporteSolidario35000($totalGanandoMes);

//aporte patronal
$cod_config_planilla_seguro_medico=16;//estatico
$cod_config_planilla_riesgo_prof=17;//estatico
$cod_config_planilla_provivienda=18;//estatico
$cod_config_planilla_solidario=19;//estatico
$seguro_de_salud=obtener_aporte_patronal_general($cod_config_planilla_seguro_medico,$totalGanandoMes);
$riesgo_profesional=obtener_aporte_patronal_general($cod_config_planilla_riesgo_prof,$totalGanandoMes);
$provivienda=obtener_aporte_patronal_general($cod_config_planilla_provivienda,$totalGanandoMes);
$a_patronal_sol=obtener_aporte_patronal_general($cod_config_planilla_solidario,$totalGanandoMes);

$total_a_patronal=$seguro_de_salud+$riesgo_profesional+$provivienda+$a_patronal_sol;

//$totalLiquidoPagable=totalLiquidoPagable($gestionPlanilla, $mesPlanilla);
$cod_cuenta=111;
$cod_cuenta_aux=0;
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','$cod_cuenta_aux','$codUOCentroCosto','$codAreaCentroCosto','0','$totalLiquidoPagable','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;
//CAJA PETROLERA      
//$totalCajaSalud=obtenerTotalCPS($gestionPlanilla,$mesPlanilla);
$totalCajaSalud=$seguro_de_salud;
$cod_cuenta="121";//por defecto
$cod_cuenta_aux=0;
$glosaDetalleGeneral="Caja Petrolera aporte retroactivo correspondiente a : ".$namemesPlanilla."/".$anioPlanilla;
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','$cod_cuenta_aux','$codUOCentroCosto','$codAreaCentroCosto','0','$totalCajaSalud','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;

//AFP PREVISION BBV
$totalAFPPrevision=$riesgo_profesional+$a_patronal_sol+$totalAFPMesRetroactivoCalculado;
// $totalAFPPrevision=obtenerTotalAFP_prev1($gestionPlanilla,$mesPlanilla);
$cod_cuenta="125";//por defecto
$cod_cuenta_aux=0;
$glosaDetalleGeneral="Aporte Retroactivo correspondiente a : ".$namemesPlanilla."/".$anioPlanilla;
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','$cod_cuenta_aux','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPPrevision','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;

//AFP FUTURO      
// $totalAFPFuturo=obtenerTotalAFP_prev3($gestionPlanilla,$mesPlanilla);
// $cod_cuenta="123";//por defecto
// $glosaDetalleGeneral="AFP Futuro aporte retroactivo correspondiente a : ".$namemesPlanilla."/".$anioPlanilla;
// $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPFuturo','$glosaDetalleGeneral','$ordenDetalle')";
// $stmtInsertDet = $dbh->prepare($sqlInsertDet);
// $flagSuccessDet=$stmtInsertDet->execute();
// $ordenDetalle++;

//AFP PREVISION BBV
$totalAFPPrevision=$aporte_sol_13_25_35;
//$totalAFPPrevision=obtenerTotalAFP_prev2($gestionPlanilla,$mesPlanilla);
// $totalAFPPrevision=obtenerTotalAFP_prev2_retroactivos($gestionPlanilla, $mesPlanilla);
$cod_cuenta="125";//por defecto
$glosaDetalleGeneral="Aporte Retroactivo Solidario correspondiente a : ".$namemesPlanilla."/".$anioPlanilla;
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPPrevision','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;

//PROVIVIENDA
$glosaDetalleGeneral="Provivienda aporte correspondiente a : ".$namemesPlanilla."/".$anioPlanilla;
$totalProVivienda=$provivienda;
// $totalProVivienda=obtenerTotalprovivienda($gestionPlanilla,$mesPlanilla,$globalUnidadX);
$cod_cuenta="124";
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalProVivienda','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;

//PROVIVIENDA

// $glosaDetalleGeneral="AFP Futuro provivienda aporte correspondiente a : ".$namemesPlanilla."/".$anioPlanilla;
// $totalProVivienda=obtenerTotalprovivienda2($gestionPlanilla,$mesPlanilla,$globalUnidadX);
// $cod_cuenta="124";
// $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalProVivienda','$glosaDetalleGeneral','$ordenDetalle')";
// $stmtInsertDet = $dbh->prepare($sqlInsertDet);
// $flagSuccessDet=$stmtInsertDet->execute();
// $ordenDetalle++;    

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
$cod_cuenta="306";
if( isset($debe) && ($debe>0.001 || $haber>0.001) ){
  $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$cod_cuenta','0','$codUOCentroCosto','$codAreaCentroCosto','$debe','$haber','$glosaDetalleGeneral','$ordenDetalle')";
  $stmtInsertDet = $dbh->prepare($sqlInsertDet);
  $flagSuccessDet=$stmtInsertDet->execute();
  $ordenDetalle++;    
}

//indicamos que ya se realizo el comprbante
 switch ($mesPlanilla) {
    case 1:
      $sql_cabecera="cod_comprobante1='$codComprobante'";
    break;
    case 2:
      $sql_cabecera="cod_comprobante2='$codComprobante'";
    break;
    case 3:
      $sql_cabecera="cod_comprobante3='$codComprobante'";
    break;
    case 4:
      $sql_cabecera="cod_comprobante4='$codComprobante'";
    break;
  }
$stmtUdatePlanilla = $dbh->prepare("UPDATE planillas_retroactivos set $sql_cabecera where codigo=$codigo_planilla");
$stmtUdatePlanilla->execute();

   ?>
   <script>
      $(document).ready(function()
      {           
         $("#mostrarmodal1").modal("show");         
      });
   </script>

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
        <a href="../index.php?opcion=listComprobantes" type="button" class="btn btn-success">Ir a Comprobantes</a>
        <a href="../index.php?opcion=planillasRetroactivoPersonal" type="button" class="btn btn-danger">Ir a Planillas</a>
      </div>
    </div>
  </div>
</div>


