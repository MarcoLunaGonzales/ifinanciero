<?php
require_once '../../conexion.php';
require_once '../../conexion_externa.php';
$dbh = new ConexionIBNORCA();
$sql="select * from dbdocumentos.documentos order by 1 desc;";
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
