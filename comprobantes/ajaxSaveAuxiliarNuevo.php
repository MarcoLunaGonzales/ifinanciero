<?php
require_once '../conexion.php';
$dbh = new Conexion();

$tipo=$_GET['tipo'];
$aux=$_GET['cod_nuevo'];
if($tipo==1){
  $codigo=$_GET['cod_estadocuenta']; //estados cuenta
  $sqlUpdate="UPDATE estados_cuenta SET  cod_cuentaaux='$aux' where codigo=$codigo";
}else{
  $codigo=$_GET['cod_comprobantedetalle']; //comprobantes
  $sqlUpdate="UPDATE comprobantes_detalle SET  cod_cuentaauxiliar='$aux' where codigo=$codigo";
}
$stmtUpdate = $dbh->prepare($sqlUpdate);
$flagSuccess=$stmtUpdate->execute();
if($flagSuccess==true){
  echo "1";
}else{ 
  echo "0";
}

?>
