<?php
require_once '../conexion.php';
require_once 'configModule.php';

$codigoRubro=$_GET["codigo"];
$db = new Conexion();

$sqlCodAF="SELECT a.codigoactivo as codigoultimo from activosfijos a where a.cod_depreciaciones='$codigoRubro' and
	 a.cod_estadoactivofijo=1 order by a.codigoactivo desc limit 0,1";
$stmtCodAF = $db->prepare($sqlCodAF);
$stmtCodAF->execute();

while ($rowCodAF = $stmtCodAF->fetch()){
	$codigoUltimo=$rowCodAF["codigoultimo"];
	$codigoActivoFijo=intval($codigoUltimo)+1;
	$codigoStringAF=strval($codigoActivoFijo);
	$codigoStringAF=str_pad($codigoStringAF, 7, "0", STR_PAD_LEFT);
?>	
<input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="codigoactivo" id="codigoactivo" required="true"  value="<?=$codigoStringAF;?>"/>
<?php 
} 
?>
