<?php

require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$nombre_norma=$_GET['nombre_norma'];
$sql="SELECT codigo from normas where abreviatura like '%".$nombre_norma."%'";
// echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$codigo=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigo=$row['codigo'];    
}
echo $codigo."";
?>
        