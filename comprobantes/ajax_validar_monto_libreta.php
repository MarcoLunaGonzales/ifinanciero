<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$codigoDetalle=$_GET['codigo'];
$stmt = $dbh->prepare("SELECT obtener_saldo_libreta_bancaria_detalle_oficial($codigoDetalle) as saldo");
$stmt->execute();
$saldo=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $saldo=$row['saldo']; 
}
echo $saldo;
?>
        