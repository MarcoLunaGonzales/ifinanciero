<?php
require_once '../conexion.php';
$dbh = new Conexion();
$codigo=$_GET['cod_cuenta'];
$sql="SELECT * from cuentas_auxiliares where cod_cuenta=$codigo";
 $stmt = $dbh->prepare($sql);
 $stmt->execute();
 ?>
<select class="selectpicker form-control" data-size="6" data-live-search="true" name="cod_nuevo_sel" id="cod_nuevo_sel" data-style="btn btn-info">
 <?php
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codAux=$row['codigo'];
  $nomAux=$row['nombre'];
  ?><option value="<?=$codAux?>"><?=$nomAux?> (<?=$codAux?>)</option><?php
}
?>
</select>
