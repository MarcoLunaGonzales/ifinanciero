<?php
require_once '../../conexion.php';
require_once '../../conexion_externa.php';
$dbh = new ConexionIBNORCA();
$sql="SELECT * FROM comprobantes_detalle where cod_comprobante in (SELECT codigo from comprobantes where cod_gestion=2020 and MONTH(fecha) in (1,2,3,4,5,6) and cod_unidadorganizacional!=3000)";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$cadena="<table>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {      
  $cadena.="<tr><td>".$row['path']."</td></tr>";
}  
echo $cadena."</table>";

$sql="select * from dbdocumentos.directoriotablas order by 1 desc;";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$cadena="<table>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {      
  $cadena.="<tr><td>".$row['tabla']."</td><td>".$row['campo']."</td><td>".$row['condicion']."</td><td>".$row['tipoArchivo']."</td></tr>";
}  

echo $cadena."</table>";
