<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
require_once 'functions.php';
$dbh = new Conexion();
$globalUser=$_SESSION["globalUser"];
// RECIBIMOS LAS VARIABLES
$cod_cc=$cod_cc;
$cod_tcc=$cod_tcc;

$cod_dcc=$codigo;

$cod_solicitud=obtenerSolicitudRecursoPorCajaChica($cod_dcc);
$detalleCajaChica=obtenerCodigoCajaChicaDetalleSolicitud($cod_solicitud);

for ($dcc=0; $dcc < count($detalleCajaChica); $dcc++) { 
    $cod_dcc=$detalleCajaChica[$dcc];

   $stmtMontoAnterior = $dbh->prepare("SELECT monto,(select cc.monto_reembolso from caja_chica cc where cc.codigo=cod_cajachica) as monto_reembolso from caja_chicadetalle where codigo=$cod_dcc");
   $stmtMontoAnterior->execute();
   $resultMontoAnterior = $stmtMontoAnterior->fetch();
   $monto_anterior = $resultMontoAnterior['monto'];
   $monto_reembolso_x = $resultMontoAnterior['monto_reembolso'];

   $monto_reembolso=$monto_reembolso_x+$monto_anterior;
   //acctualiazmos reembolso
   $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$cod_cc");
   $stmtReembolso->execute();
   //================================================================
   $monto_rendicion=0;


   // Prepare
   $sqlDelCajaChica="UPDATE caja_chicadetalle set cod_estadoreferencial=2,modified_by=$globalUser,modified_at=NOW() where codigo=$cod_dcc";
   //echo $sqlDelCajaChica;
   $stmt = $dbh->prepare($sqlDelCajaChica);
   $flagSuccess=$stmt->execute();
   if($flagSuccess){
  	 
      $stmtRendicion = $dbh->prepare("UPDATE rendiciones set cod_estadoreferencial=2 where codigo=$cod_dcc");
	    $flagSuccess=$stmtRendicion->execute();
	

      //revertir el pago de SR
      $stmtCambioEstadoSR = $dbh->prepare("UPDATE solicitud_recursos set cod_estadosolicitudrecurso=3 where codigo=:codigo");
      $stmtCambioEstadoSR->bindParam(':codigo', $cod_solicitud);
      $flagSuccess=$stmtCambioEstadoSR->execute();

   }
    
}


 showAlertSuccessError($flagSuccess,$urlListDetalleCajaChica.'&codigo='.$cod_cc.'&cod_tcc='.$cod_tcc);


  $fechaHoraActual=date("Y-m-d H:i:s");
  $idTipoObjeto=2708;
  $idObjeto=2723; //regristado
  $obs="Solicitud Aprobada";
  actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,90,$cod_solicitud,$fechaHoraActual,$obs);  

?>