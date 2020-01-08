<?php

set_time_limit(0);
session_start();

//require_once '../layouts/bodylogin.php';
require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsPlanillas.php';
require_once '../layouts/bodylogin2.php';

require_once '../rrhh/configModule.php';


$dbh = new Conexion();


//SE DEBE PARAMETRIZAR ESTE CODIGO DE CUENTA PARA LA DEPRECIACION
$codCuentaDepreciacion=298;
$codCuentaDepreciacionAF=256;


//HACEMOS EL COMPROBANTE DE LA DN
$globalUnidadX=829;
$nombreUnidadX=nameUnidad($globalUnidadX);
$tipoComprobante=3;
$codEmpresa=1;
$codAnio=$_SESSION["globalNombreGestion"];
$codMoneda=1;
$codEstadoComprobante=1;
$fechaActual=date("Y-m-d H:i:s");

$mesPlanilla=$_GET["cod_mes"];
$gestionPlanilla=$_GET["cod_gestion"];
$anioPlanilla=nameGestion($gestionPlanilla);

$mesTrabajo=$_SESSION['globalMes'];
$gestionTrabajo=$_SESSION['globalNombreGestion'];

$numeroComprobante=obtenerCorrelativoComprobante($tipoComprobante, $globalUnidadX, $gestionTrabajo, $mesTrabajo);

$glosaCabecera="Sueldos Correspondientes a: ".$mesPlanilla." ".$anioPlanilla." Unidad: ".$nombreUnidadX;
$codComprobante=obtenerCodigoComprobante();

$sqlInsertCab="INSERT INTO comprobantes (codigo, cod_empresa, cod_unidadorganizacional, cod_gestion, cod_moneda, cod_estadocomprobante, cod_tipocomprobante, fecha, numero, glosa) values ('$codComprobante','$codEmpresa','$globalUnidadX','$codAnio','$codMoneda','$codEstadoComprobante','$tipoComprobante','$fechaActual','$numeroComprobante','$glosaCabecera')";
$stmtInsertCab = $dbh->prepare($sqlInsertCab);
$flagSuccess=$stmtInsertCab->execute();

//INSERTAMOS LA RELACION ENTRE COMPROBANTES Y DEPRECIACIONES.
/*
$sqlInsertCabRelacion="INSERT INTO comprobantes_depreciaciones (cod_comprobante, cod_depreciacion) values ('$codComprobante','$codDepreciacion')";
$stmtInsertCabRelacion = $dbh->prepare($sqlInsertCabRelacion);
$flagSuccessRelacion=$stmtInsertCabRelacion->execute();*/

$totalGanadoDN=totalGanadoDN($gestionPlanilla, $mesPlanilla, $globalUnidadX);
$totalPersonalSIS=totalPersonalProyectos(1);

$totalAportesSIS=$totalPersonalSIS*0.1671;

//echo $totalGanadoDN;
//echo $totalPersonalSIS;
$totalDistribuirDN=$totalGanadoDN-$totalPersonalSIS;

$sqlDistribucion="SELECT d.cod_unidadorganizacional, d.porcentaje from distribucion_gastosporcentaje d where d.porcentaje>0 order by d.porcentaje desc";
$stmtDistribucion = $dbh->prepare($sqlDistribucion);
$stmtDistribucion->execute();

$centroCostosDN="501";//DN

$ordenDetalle=1;
while ($rowDistribucion = $stmtDistribucion->fetch(PDO::FETCH_ASSOC)) {
   $codUODistX=$rowDistribucion['cod_unidadorganizacional'];
   $nombreUODistX=nameUnidad($codUODistX);

   $porcentajeDistX=$rowDistribucion['porcentaje'];

   $montoUODist=$totalDistribuirDN*($porcentajeDistX/100);
   $montoAportesUODist=$montoUODist*0.1671+($totalAportesSIS*($porcentajeDistX/100));

   //echo $codUODistX." ".$montoUODist." ".$montoAportesUODist."<br>";
   $numeroCuenta=cuentaCorrienteUO($codUODistX);

   $glosaDetalle1="Aportes Personal. Planilla de sueldos correspondiente a: ".$nombreUODistX." ".$mesPlanilla."/".$anioPlanilla;
   $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUODistX','$centroCostosDN','$montoUODist','0','$glosaDetalle1','$ordenDetalle')";
   $stmtInsertDet = $dbh->prepare($sqlInsertDet);
   $flagSuccessDet=$stmtInsertDet->execute();

   $ordenDetalle++;

   $glosaDetalle1="Aportes Personal2. Planilla de sueldos correspondiente a: ".$nombreUODistX." ".$mesPlanilla."/".$gestionPlanilla;
   $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUODistX','$centroCostosDN','$montoAportesUODist','0','$glosaDetalle1','$ordenDetalle')";
   $stmtInsertDet = $dbh->prepare($sqlInsertDet);
   $flagSuccessDet=$stmtInsertDet->execute();

   $ordenDetalle++;
}

//DESDE ACA HACEMOS PERSONAL PROYECTOS
$sqlPersonalProyectos="SELECT p.cod_personal, p.monto_subsidio from personal_proyectosfinanciacionexterna p where p.cod_estado_referencial=1 and p.cod_proyecto<>0";
$stmtPersonalProyectos = $dbh->prepare($sqlPersonalProyectos);
$stmtPersonalProyectos->execute();

$centroCostosSIS="1235";//SIS
while ($rowPersonalProyectos = $stmtPersonalProyectos->fetch(PDO::FETCH_ASSOC)) {
   $codPersonalX=$rowPersonalProyectos['cod_personal'];
   $nombrePersonalX=obtenerNombrePersonal($codPersonalX);
   $monto_subsidioX=$rowPersonalProyectos['monto_subsidio'];
   
   $numeroCuenta="387";//CUENTAS COBRAR PROYECTOS
   $glosaDetalle2=$nombrePersonalX." Sueldo correspondiente a: ".$mesPlanilla."/".$gestionPlanilla;
   $sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUODistX','$centroCostosSIS','$monto_subsidioX','0','$glosaDetalle2','$ordenDetalle')";
   $stmtInsertDet = $dbh->prepare($sqlInsertDet);
   $flagSuccessDet=$stmtInsertDet->execute();

   $ordenDetalle++;
}
//FIN PERSONAL PROYECTOS

$glosaDetalleGeneral="Sueldo correspondiente a: ".$mesPlanilla."/".$gestionPlanilla;
$codUOCentroCosto="829";
$codAreaCentroCosto="501";

//SUELDOS POR PAGAR
$totalLiquidoPagable=totalLiquidoPagable($gestionPlanilla, $mesPlanilla, $globalUnidadX);
$numeroCuenta="110";
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalLiquidoPagable','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;

//CAJA PETROLERA
$totalCajaSalud=17819.96;
$numeroCuenta="120";
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalCajaSalud','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;

//AFP PREVISION BBV
$totalAFPPrevision=15514.25;
$numeroCuenta="121";
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPPrevision','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;

//AFP PREVISION BBV
$totalAFPPrevision=298.24;
$numeroCuenta="121";
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPPrevision','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;


//AFP FUTURO
$totalAFPFuturo=15528.13;
$numeroCuenta="121";
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPFuturo','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;

//AFP PREVISION BBV
$totalAFPFuturo=0;
$numeroCuenta="121";
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalAFPFuturo','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;

//PROVIVIENDA
$totalProVivienda=1781.20;
$numeroCuenta="121";
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalProVivienda','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;

//PROVIVIENDA
$totalProVivienda=1782.79;
$numeroCuenta="121";
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalProVivienda','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;


//RC IVA
$totalRCIVA=108.03;
$numeroCuenta="131";
$sqlInsertDet="INSERT INTO comprobantes_detalle (cod_comprobante, cod_cuenta, cod_cuentaauxiliar, cod_unidadorganizacional, cod_area, debe, haber, glosa, orden) VALUES ('$codComprobante','$numeroCuenta','0','$codUOCentroCosto','$codAreaCentroCosto','0','$totalRCIVA','$glosaDetalleGeneral','$ordenDetalle')";
$stmtInsertDet = $dbh->prepare($sqlInsertDet);
$flagSuccessDet=$stmtInsertDet->execute();
$ordenDetalle++;


//showAlertSuccessError($flagSuccess,$urlList7);

?>
