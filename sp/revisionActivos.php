<?php
require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();

$sql="select a.codigo, a.codigoactivo, a.activo, a.cod_estadoactivofijo, a.cod_unidadorganizacional, a.cod_area,
a.cod_responsables_responsable, (select concat(p.paterno, ' ',p.primer_nombre) from personal p where p.codigo=a.cod_responsables_responsable)as persona from activosfijos a;";
$stmt = $dbh->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoInterno=$row['codigo'];
  $codigoActivo=$row['codigoactivo'];
  
  $codigoInterno=$row['codigo'];
  $codigoActivo=$row['codigoactivo'];
  $nombreActivo=$row['activo'];
  $estadoActivo=$row['cod_estadoactivofijo'];
  $oficina=$row['cod_unidadorganizacional'];
  $area=$row['cod_area'];
  $codResponsable=$row['cod_responsables_responsable'];
  $nombreResponsable=$row['persona'];
  
  $sqlAreaCorrecta="select cod_area from personal p where p.codigo='$codResponsable'";
  $stmtArea = $dbh->prepare($sqlAreaCorrecta);
  $stmtArea->execute();
  while ($rowArea = $stmtArea->fetch(PDO::FETCH_ASSOC)) {
    $codAreaCorrecta=$rowArea['cod_area'];
  }
  if($codAreaCorrecta!=$area){
    //echo "$codigoInterno $codigoActivo $nombreActivo $estadoActivo $area $nombreResponsable $codAreaCorrecta<br>";
    echo "update activosfijos set cod_area='$codAreaCorrecta' where codigo='$codigoInterno';<br>";
  }  
  //echo "update facturas_venta set codigo_control='$codigoControl' where codigo='$codigoX';<br>";
}

?>