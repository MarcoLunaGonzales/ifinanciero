<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$cod_cliente=$_GET["cod_cliente"];
$cod_solicitudfacturacion=$_GET["cod_solicitudfacturacion"];

$datos=$cod_solicitudfacturacion."/".$cod_cliente;
$cuenta_defecto_cliente=obtenerValorConfiguracion(78);//
$cuenta_auxiliar=obtenerCodigoCuentaAuxiliarProveedorClienteCuenta(2,$cod_cliente,$cuenta_defecto_cliente);
if($cuenta_auxiliar==0){
	$cuenta_auxiliar="";
}

// $sql="SELECT c.codigo, c.nombre from clientes c where c.cod_estadoreferencial=1 order by c.nombre";
// $stmt = $dbh->prepare($sql);
// $stmt->execute();

$sql_cuentasAux="SELECT codigo,nombre,cod_cuenta From cuentas_auxiliares where cod_estadoreferencial=1 and cod_tipoauxiliar=2 and cod_cuenta=$cuenta_defecto_cliente";
$stmt_cuentas_aux = $dbh->prepare($sql_cuentasAux);
$stmt_cuentas_aux->execute();
// echo $cuenta_auxiliar."--";
if($cuenta_auxiliar==""){?>
<div class="row">	
	<label class="col-sm-2 col-form-label"></label>
	<div class="col-sm-7">
		<div class="form-group">
			<span style="color: #ff0000;">El Cliente No Tiene Una Cuenta Auxiliar Asociada.</span>
		</div>
	</div>	
</div>
<?php }
?>

<div class="row" >
	<label class="col-sm-2 col-form-label">Cuenta Auxiliar</label>
	<div class="col-sm-7">
		<div class="form-group">
			<select name="cuenta_auxiliar_list" id="cuenta_auxiliar_list" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" >
				<option disabled selected value="">Seleccionar una opcion</option>
		    <?php 
		        while ($row_cuentaaux = $stmt_cuentas_aux->fetch()){ ?>
		         <option <?=($cuenta_auxiliar==$row_cuentaaux["codigo"])?"selected":"";?> value="<?=$row_cuentaaux["codigo"];?>"><?=$row_cuentaaux["nombre"];?></option><?php 
		        } 
		    ?>
		 </select>
		</div>
	</div>
	<div class="col-sm-1">
		<div class="form-group">
			<a href="#" style="background-color: #0489B1" class="btn btn-round btn-fab btn-sm" onclick="abrirRegistroCuentaAuxiliar('<?=$datos?>',2)">
            	<i class="material-icons" title="Registrar Cuenta Auxiliar">add</i>
          	</a>
		</div>
	</div>
</div>



