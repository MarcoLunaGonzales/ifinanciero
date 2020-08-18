<?php
require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(0);
// $globalUser=$_SESSION["globalUser"];
//RECIBIMOS LAS VARIABLES
$cod_solicitud_e=$_POST['cod_solicitud_e'];
$cod_tipopagoE=$_POST['cod_tipopagoE'];

$sqlA="SELECT sf.* from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$cod_solicitud_e";
$stmt2 = $dbh->prepare($sqlA);                                   
$stmt2->execute(); 
$sumaTotalMonto=0;
// $sumaTotalDescuento_bob=0;
while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {  
  $cantidadX=trim($row2['cantidad']);                                
  $precioX=(trim($row2['precio'])*$cantidadX);
  $descuento_bobX=trim($row2['descuento_bob']);  
  $sumaTotalMonto+=$precioX;  
  // $sumaTotalDescuento_bob+=$descuento_bobX;  
}
$sumaTotalImporte=$sumaTotalMonto;
try{    
	$sqlDeleteTiposPago="DELETE from solicitudes_facturacion_tipospago where cod_solicitudfacturacion=$cod_solicitud_e";
    $stmtDelTiposPago = $dbh->prepare($sqlDeleteTiposPago);
    $flagSuccess=$stmtDelTiposPago->execute();
    if($flagSuccess){
    	$stmt = $dbh->prepare("UPDATE solicitudes_facturacion set cod_tipopago='$cod_tipopagoE' where codigo=$cod_solicitud_e");
    	$flagSuccess=$stmt->execute();

    	$sqlTiposPago="INSERT INTO solicitudes_facturacion_tipospago(cod_solicitudfacturacion, cod_tipopago, porcentaje, monto) VALUES ('$cod_solicitud_e','$cod_tipopagoE','100','$sumaTotalImporte')";
        $stmtTiposPago = $dbh->prepare($sqlTiposPago);
        $flagSuccess=$stmtTiposPago->execute();
    
    }
    if($flagSuccess){
        echo 1;
    }else echo 0;   

} catch(PDOException $ex){
    echo 0;
}

?>
