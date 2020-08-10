<?php
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';


$dbh = new Conexion();
$stmt = $dbh->prepare("SELECT cd.codigo, cd.glosa from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and year(c.fecha)=2020 and c.cod_tipocomprobante<>4 and c.cod_estadocomprobante<>2");
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('glosa', $glosa);

  $index=1;
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                          
    $buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
    $reemplazar=array("", "", "", "");
    //$glosa=clean_string($glosa);
    $glosa=str_ireplace($buscar,$reemplazar,$glosa);
    $glosa=addslashes($glosa);
    //$glosa=string_sanitize($glosa);
    
    $upd="update comprobantes_detalle set glosa='$glosa' where codigo='$codigo';";	
    echo $upd."<br>";
  }


?>
