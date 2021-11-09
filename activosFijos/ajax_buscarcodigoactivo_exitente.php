<?php
require_once '../conexion.php';
require_once 'configModule.php';

$codigoactivo=$_GET["codigoactivo"];
$db = new Conexion();

$sqlCodAF="select codigo from activosfijos where cod_estadoactivofijo=1 and codigoactivo = '$codigoactivo'";
$stmtCodAF = $db->prepare($sqlCodAF);
$stmtCodAF->execute();
$contador=0;
while ($rowCodAF = $stmtCodAF->fetch()){
	$contador++;
} 
if($contador>0){
	$estilo='style="background: white;color:red;"';
	$valor="YA EXISTE";
}else{
	$estilo='style="background: white;color:green;"';
	$valor="CORRECTO";
}
?>

<input type="text"  readonly="readonly" <?=$estilo?> class="form-control" name="codigoactivo" id="codigoactivo" required="true"  value="<?=$valor;?>"/>
