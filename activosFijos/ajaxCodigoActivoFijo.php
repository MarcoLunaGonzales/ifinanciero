<?php
require_once '../conexion.php';
require_once 'configModule.php';

$codigoRubro=$_GET["codigo"];
$db = new Conexion();

$sqlCodAF="SELECT (count(*)+1)as contador, r.abreviatura from activosfijos a, depreciaciones r where a.cod_depreciaciones=r.codigo and a.cod_depreciaciones=:codigo";
$stmtCodAF = $db->prepare($sqlCodAF);
//echo $sql;
$stmtCodAF->bindParam(':codigo', $codigoRubro);
$stmtCodAF->execute();

while ($rowCodAF = $stmtCodAF->fetch()){
	$contadorAF=$rowCodAF["contador"];
	$abreviaturaRubro=$rowCodAF["abreviatura"]; 

	$codigoActivoFijo=$abreviaturaRubro."-".$contadorAF;
?>	
<input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="codigoactivo" id="codigoactivo" required="true"  value="<?=$codigoActivoFijo;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
<?php 
} 
?>
