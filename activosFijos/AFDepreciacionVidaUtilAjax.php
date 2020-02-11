<?php
require_once '../conexion.php';
require_once 'configModule.php';

$codigoRubro=$_GET["codigo"];
$db = new Conexion();

$sqlCodAF="SELECT vida_util from depreciaciones where codigo=:codigo";
$stmtCodAF = $db->prepare($sqlCodAF);
//echo $sql;
$stmtCodAF->bindParam(':codigo', $codigoRubro);
$stmtCodAF->execute();

while ($rowCodAF = $stmtCodAF->fetch()){
	$vida_util=$rowCodAF["vida_util"];	
?>	
<!-- <input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="codigoactivo" id="codigoactivo" required="true"  value="<?=$codigoActivoFijo;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/> -->

<input class="form-control" type="text" name="vidautilmeses" id="vidautilmeses" required="true" value="<?=$vida_util;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly="true" style="padding-left:20px"/>
<?php 
} 
?>
