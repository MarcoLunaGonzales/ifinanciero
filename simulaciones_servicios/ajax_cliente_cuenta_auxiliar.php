<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$cod_cliente=$_GET["cod_cliente"];
$tipoProveedorCliente=2;//tipo clientes

$sql="";
if($tipoProveedorCliente==1){
	$sql="select p.codigo, p.nombre from af_proveedores p where p.cod_estado=1 order by p.nombre";	
}
if($tipoProveedorCliente==2){
	$sql="select c.codigo, c.nombre from clientes c where c.cod_estadoreferencial=1 order by c.nombre";
}
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':codigo', $codigo);
$stmt->bindParam(':nombre', $nombre);
$stmt->execute();

?>
<select name="proveedor_cliente" id="proveedor_cliente" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" >
    <?php 
        while ($row = $stmt->fetch()){ 
    ?>
         <option <?=($cod_cliente==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
     <?php 
        } 
    ?>
 </select>