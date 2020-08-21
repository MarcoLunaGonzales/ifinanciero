<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;
$cod_tcc=$cod_tcc;
$cod_a=$cod_a;
// echo "cod:".$cod_a."codigo:".$codigo;
if($cod_a==2){//borrado
	// Prepare
	$stmt = $dbh->prepare("UPDATE caja_chica set cod_estadoreferencial=2,cod_estado=2 where codigo=$codigo");
	$flagSuccess=$stmt->execute();

    $sqlDetalleX="SELECT codigo From caja_chicadetalle where cod_cajachica=$codigo";
    $stmtDetalleX = $dbh->prepare($sqlDetalleX);
    $stmtDetalleX->execute();                    
    $stmtDetalleX->bindColumn('codigo', $cod_dcc);    
    while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)){ 
        //revertir el pago de SR
        
        //buscamos codigo estado cuenta origen
        $stmtEC = $dbh->prepare("SELECT cod_comprobantedetalleorigen from estados_cuenta where cod_cajachicadetalle = $cod_dcc");
        $stmtEC->execute();
        $resultEC = $stmtEC->fetch();
        $codigo_ec = $resultEC['cod_comprobantedetalleorigen'];
        //sacamos datos de SR
        $sqlDetalleX="SELECT sd.codigo,sd.cod_solicitudrecurso,sd.cod_proveedor,sd.cod_tipopagoproveedor 
        FROM solicitud_recursos s,solicitud_recursosdetalle sd
        WHERE s.codigo=sd.cod_solicitudrecurso and s.cod_comprobante in (select cd.cod_comprobante from estados_cuenta e,comprobantes_detalle cd where e.cod_comprobantedetalle=cd.codigo and e.codigo=$codigo_ec)";
        $stmtDetalleX = $dbh->prepare($sqlDetalleX);
        $stmtDetalleX->execute();                  
        $result_detalleX= $stmtDetalleX->fetch();
        $codigo_srd=$result_detalleX['codigo'];
        $cod_solicitudrecurso_sr=$result_detalleX['cod_solicitudrecurso'];
        $cod_proveedor_sr=$result_detalleX['cod_proveedor'];
        $cod_tipopagoproveedor_sr=$result_detalleX['cod_tipopagoproveedor'];
        
        $stmtCambioEstadoSR = $dbh->prepare("UPDATE solicitud_recursos set cod_estadosolicitudrecurso=5 where codigo=:codigo");
        $stmtCambioEstadoSR->bindParam(':codigo', $cod_solicitudrecurso_sr);
        $flagSuccess=$stmtCambioEstadoSR->execute();
        //si tiene algunpago de proveedor, borrar
        $stmtDELETEPagoProv = $dbh->prepare("UPDATE pagos_proveedores set cod_estadopago=2 where cod_cajachicadetalle=:codigo");
        $stmtDELETEPagoProv->bindParam(':codigo', $cod_dcc);
        $flagSuccess=$stmtDELETEPagoProv->execute();    
        //si tiene algun estado de cuentas registrado, borrar
        $stmtDeleteContraCuenta = $dbh->prepare("DELETE from estados_cuenta where cod_cajachicadetalle = $cod_dcc");
        $flagSuccess=$stmtDeleteContraCuenta->execute();
    } 

}elseif($cod_a==1){//cerrado
	$fecha_cierre=date('Y-m-d');
	$stmt = $dbh->prepare("UPDATE caja_chica set cod_estado=2,fecha_cierre='$fecha_cierre'  where codigo=$codigo");
	$flagSuccess=$stmt->execute();

	// $sql_rendicion="SELECT SUM(monto) monto_total from caja_chicadetalle where cod_cajachica=$codigo and cod_estadoreferencial=1";
    $sql_rendicion="SELECT SUM(c.monto)-IFNULL((select SUM(r.monto) from caja_chicareembolsos r where r.cod_cajachica=$codigo and r.cod_estadoreferencial=1),0) as monto_total from caja_chicadetalle c where c.cod_cajachica=$codigo and c.cod_estadoreferencial=1";
    $stmtSaldo = $dbh->prepare($sql_rendicion);
    $stmtSaldo->execute();
    $resultSaldo=$stmtSaldo->fetch();
    if($resultSaldo['monto_total']!=null || $resultSaldo['monto_total']!='')
      $monto_total=$resultSaldo['monto_total'];
    else $monto_total=0; 

    $stmtReembolso = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_total where codigo=$codigo");
    $stmtReembolso->execute();
    
}elseif($cod_a==3){//abrir
    $stmt = $dbh->prepare("UPDATE caja_chica set cod_estado=1  where codigo=$codigo");
    $flagSuccess=$stmt->execute();
}

showAlertSuccessError($flagSuccess,$urlListCajaChica."&codigo=".$cod_tcc);

?>