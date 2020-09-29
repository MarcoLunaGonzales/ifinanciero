<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
set_time_limit(300);
$globalUser=$_SESSION["globalUser"];


$codigoSolicitud=$_GET['codigo'];
$codCajaChica=$_GET['cod_cajachica'];
//$codPersonal=$_GET['cod_personal'];
$fechaActual=date("d/m/Y");

$fecha=date("Y-m-d");
$fechaHora=date("Y-m-d H:i:s");

$solicitudDetalle=obtenerSolicitudRecursosDetalle($codigoSolicitud);
$codPersonal=obtenerPersonalSolicitanteRecursos($codigoSolicitud);
$numeroRecibo=obtenerNumeroReciboCajaChica($codCajaChica);
$numeroDocumento=obtenerNumeroDocumentoReciboCajaChica($codCajaChica);
$index=0;
    while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
    	$numeroCuentaX=trim($rowDetalles['numero']);
		  $nombreCuentaX=trim($rowDetalles['nombre']);
		  $proveedorX=nameProveedor($rowDetalles["cod_proveedor"]);
      $retencionX=$rowDetalles["cod_confretencion"];
    if($retencionX!=0){
		  $tituloImporte=nameRetencion($retencionX);
		}else{
		  $tituloImporte="Ninguno";	
		}
		$importeSolX=$rowDetalles["importe"];
		$detalleX=$rowDetalles["detalle"];
		$codAreaXX=$rowDetalles['cod_area'];
    $codOficinaXX=$rowDetalles['cod_unidadorganizacional'];

    $nombreOficinaXX=abrevUnidad_solo($codOficinaXX);
    $nombreAreaXX=abrevArea_solo($codAreaXX);
    $codCuentaX=$rowDetalles['cod_plancuenta']; 
    $codProveedor=$rowDetalles["cod_proveedor"];
    $codigoSolicitudDetalle=$rowDetalles["codigo"];
    //crear recibo
    //Obtener el monto Rembolso
    $stmtMCC = $dbh->prepare("SELECT monto_reembolso from caja_chica where  codigo =$codCajaChica");
    $stmtMCC->execute();
    $resultMCC=$stmtMCC->fetch();    
    $monto_reembolso_x=$resultMCC['monto_reembolso'];
    $monto_reembolso=$monto_reembolso_x-$importeSolX;
    $codigoDetalle=obtenerCodigoReciboCajaChicaDetalle();
    $cod_estado=1;
    $cod_estadoreferencial=1;
    $monto_rendicion=0;
    $cod_actividad_sw=0;

    $stmt = $dbh->prepare("INSERT INTO caja_chicadetalle(codigo,cod_cajachica,cod_cuenta,fecha,cod_tipodoccajachica,nro_documento,cod_personal,monto,observaciones,cod_estado,cod_estadoreferencial,cod_area,cod_uo,nro_recibo,cod_proveedores,cod_actividad_sw,created_at,created_by) 
    VALUES ($codigoDetalle,$codCajaChica,$codCuentaX,'$fecha',$retencionX,$numeroDocumento,'$codPersonal',$importeSolX,'$detalleX',$cod_estado,$cod_estadoreferencial,'$codAreaXX','$codOficinaXX',$numeroRecibo,'$codProveedor','$cod_actividad_sw',NOW(),$globalUser)");
    $flagSuccess=$stmt->execute();
    if($flagSuccess){//registramos rendiciones
      $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$codCajaChica");
      $stmtReembolso->execute();

      $stmtrendiciones = $dbh->prepare("INSERT INTO rendiciones(codigo,numero,cod_tipodoc,monto_a_rendir,monto_rendicion,cod_personal,observaciones,cod_estado,cod_cajachicadetalle,cod_estadoreferencial,fecha_dcc) values ($codigoDetalle,$numeroDocumento,$retencionX,$importeSolX,$monto_rendicion,'$codPersonal','$detalleX',$cod_estado,$codigoDetalle,$cod_estadoreferencial,'$fecha')");
      $flagSuccess=$stmtrendiciones->execute();
      
      $stmtSolicitudDetalle = $dbh->prepare("UPDATE solicitud_recursosdetalle set cod_cajachicadetalle=$codigoDetalle where codigo=$codigoSolicitudDetalle");
      $stmtSolicitudDetalle->execute();
      
      $stmtSolicitudFacturas = $dbh->prepare("INSERT INTO facturas_detalle_cajachica (cod_cajachicadetalle,nit,nro_factura,fecha,razon_social,importe,exento,nro_autorizacion,codigo_control,ice,tasa_cero) 
        (SELECT $codigoDetalle as cod_cajachicadetalle,nit,nro_factura,fecha,razon_social,importe,exento,nro_autorizacion,codigo_control,ice,tasa_cero  FROM facturas_compra where cod_solicitudrecursodetalle=$codigoSolicitudDetalle)");
      $stmtSolicitudFacturas->execute();

      $index++;
      $numeroRecibo++;
      $numeroDocumento++;
    }  
 
  } 


if($index==0){
 echo "0";
}else{
  $stmtSolicitud = $dbh->prepare("UPDATE solicitud_recursos set cod_estadosolicitudrecurso=9 where codigo=$codigoSolicitud");
  $stmtSolicitud->execute();
  $fechaHoraActual=date("Y-m-d H:i:s");
  $idTipoObjeto=2708;
  $idObjeto=2725; //regristado
  $obs="Solicitud Contabilizada";
  actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codigoSolicitud,$fechaHoraActual,$obs);    

  echo "1";
}
     ?>