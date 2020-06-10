<?php 
require_once '../functions.php';
require_once '../conexion.php';
set_time_limit(0);
$lista=obtenerListaNormasIbnorca();		
$dbh = new Conexion();

$sqlDelete="DELETE FROM normas";
$stmtDelete = $dbh->prepare($sqlDelete);
$stmtDelete->execute();
 foreach ($lista->lista as $listas) {
 	if($listas->FechaBaja=="0000-00-00"){
      $codigoNorma=obtenerCodigoNorma();
      $sqlInsert="INSERT INTO normas (codigo,nombre,abreviatura, cod_sector, cod_estado) 
      VALUES ('".$codigoNorma."','".$listas->NombreNorma."','".$listas->CodigoNorma."','".$listas->IdSector."',1)";
      $stmtInsert = $dbh->prepare($sqlInsert);
      $stmtInsert->execute();
 	}else{
      $codigoNorma=obtenerCodigoNorma();
      $sqlInsert="INSERT INTO normas (codigo,nombre,abreviatura, cod_sector, cod_estado) 
      VALUES ('".$codigoNorma."','".$listas->NombreNorma."','".$listas->CodigoNorma."','".$listas->IdSector."',2)";
      $stmtInsert = $dbh->prepare($sqlInsert);
      $stmtInsert->execute();
 	}
}

?>

