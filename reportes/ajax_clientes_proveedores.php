<?php
require_once '../conexion.php';
// require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo = $_GET["codigo"];
//ini_set("display_errors", "1");
$db = new Conexion();

/*if($codigo==1){//proveedores
	$sql="SELECT c.* from estados_cuenta ec, af_proveedores c where ec.cod_proveedor=c.codigo and cod_estado=1 GROUP BY c.codigo order by c.nombre";	
}else{//clientes
	$sql="SELECT c.* from estados_cuenta ec, clientes c where ec.cod_proveedor=c.codigo and cod_estadoreferencial=1 GROUP BY c.codigo order by c.nombre";	
}*/
if($codigo==1){//proveedores
  $sql="SELECT distinct(ca.codigo)as codigo, ca.nombre from estados_cuenta ec, cuentas_auxiliares ca  where ca.codigo=ec.cod_cuentaaux and ca.cod_cuenta in ($codigo) order by ca.nombre";  
}else{//clientes
  $sql="SELECT distinct(ca.codigo)as codigo, ca.nombre from estados_cuenta ec, cuentas_auxiliares ca  where ca.codigo=ec.cod_cuentaaux and ca.cod_cuenta in ($codigo) order by ca.nombre"; 
}
// echo $sql;
$stmt = $db->prepare($sql);
$stmt->execute();
?>
	
<select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" title="Seleccione una opcion" name="proveedores[]" id="proveedores" data-style="select-with-transition" data-size="5"  data-actions-box="true" multiple required data-live-search="true">
  <?php
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoX=$row['codigo'];
      $nombreX=$row['nombre'];
    ?>
    <option value="<?=$codigoX;?>"><?=$nombreX;?></option>
    <?php 
    }
  ?>
</select>
