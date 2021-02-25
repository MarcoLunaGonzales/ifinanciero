<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

//header('Content-Type: application/json');

$cod_solicitud = $_GET["cod_solicitud"];
$monto_total = $_GET["monto_total"];

$dbh = new Conexion();
// $monto_total=200;
?>

<div class="row">          
	<label class="col-sm-6 col-form-label text-right" style="color:#000000; ">Monto Total de Solicitud de Facturación</label>
	<div class="col-sm-4">
	  <div class="form-group">
	    <input type="hidden" name="monto_total_ingreso_tipopago" id="monto_total_ingreso_tipopago" value="<?=$monto_total?>" readonly="true">
	    <input type="number" class="form-control"  value="<?=number_format($monto_total,2,".","");?>" readonly="true" style="background-color:#E3CEF6;text-align: left">
	  </div>
	</div>  
</div>
<div class="row">          
    <div class="col-sm-12">
		<table class="table table-bordered table-condensed table-sm">
			<tr class="fondo-boton">
				<th></th>
				<th>Forma de Pago</th>
				<th>Porcentaje(%)</th>
				<th>Monto(BOB)</th>
			</tr>
			<?php
			  $queryTipoPago = "SELECT codigo,nombre FROM  tipos_pago WHERE cod_estadoreferencial=1 order by nombre";
			  $statementPAgo = $dbh->query($queryTipoPago);
			  $index=0;
			  while ($row = $statementPAgo->fetch()){ 
			    $cod_tipopago_x=(int)$row["codigo"];
			    $nombre_x=trim($row['nombre']);
			    ?>
			    <tr>
			    	<td><?=$index+1?></td>
			    	<td><input type="hidden" name="codigo_tipopago<?=$index?>" id="codigo_tipopago<?=$index?>" value="<?=$cod_tipopago_x?>"><?=$nombre_x?></td>
			    	<?php
			    	$sw_monto = obtener_monto_formapago($cod_solicitud,$cod_tipopago_x);
			    	$sw_porcentaje = obtener_porcentaje_formapago($cod_solicitud,$cod_tipopago_x);
			    	if($sw_monto>0 && $sw_porcentaje>0){?>
			    		<td><input type="number" step="any" class="form-control" name="monto_porcentaje_tipopago<?=$index?>" id="monto_porcentaje_tipopago<?=$index?>" onkeyup="convertir_a_bolivianos_tipopago(<?=$index?>)" value="<?=$sw_porcentaje?>"></td>
			    		<td><input type="number" class="form-control" name="monto_bob_tipopago<?=$index?>" id="monto_bob_tipopago<?=$index?>" onkeyup="convertir_a_porcentaje_tipopago(<?=$index?>)" value="<?=$sw_monto?>"></td>
			    	<?php }else{?>
			    		<td><input type="number" step="any" class="form-control" name="monto_porcentaje_tipopago<?=$index?>" id="monto_porcentaje_tipopago<?=$index?>" onkeyup="convertir_a_bolivianos_tipopago(<?=$index?>)" ></td>
			    		<td><input type="number" class="form-control" name="monto_bob_tipopago<?=$index?>" id="monto_bob_tipopago<?=$index?>" onkeyup="convertir_a_porcentaje_tipopago(<?=$index?>)"></td>
			    	<?php }
			    	?>
			    	
			    </tr>


			<?php $index++;}
			?>
			<tr>
				<td></td>
				<td>TOTAL</td>
				<td><input type="hidden" class="form-control" name="total_monto_porcentaje_a_tipopago" id="total_monto_porcentaje_a_tipopago" value="0"><input type="text" step="any" class="form-control" name="total_monto_porcentaje_tipopago" id="total_monto_porcentaje_tipopago" value="<?=number_format(0,2)?>" readonly="true"></td>
				<td><input type="hidden" step="any" class="form-control" name="total_monto_bob_a_tipopago" id="total_monto_bob_a_tipopago" value="0"><input type="text" step="any" class="form-control" name="total_monto_bob_tipopago" id="total_monto_bob_tipopago" value="<?=number_format(0,2)?>" readonly="true"></td>
			</tr>
			<tr>
				<td></td>
				<td>DIFERENCIA</td>
				<td><input  type="text" step="any" class="form-control" name="total_diferencia_porcentaje_tipopago" id="total_diferencia_porcentaje_tipopago" value="<?=number_format(0,2)?>" readonly="true"></td>
				<td><input  type="text" step="any" class="form-control" name="total_diferencia_bob_tipopago" id="total_diferencia_bob_tipopago" value="<?=number_format(0,2)?>" readonly="true"> </td>
			</tr>
		</table>
		<!-- <div class="row">
			<label class="col-sm-3 text-right col-form-label" style="color:#424242">Forma de Pago</label>
			<div class="col-sm-5">
			    <div class="form-group" >
			        <select name="cod_tipopagoE" id="cod_tipopagoE" class="selectpicker form-control form-control-sm" data-style="btn btn-info">
			            <?php 
			            $queryTipoPago = "SELECT codigo,nombre FROM  tipos_pago WHERE cod_estadoreferencial=1 order by nombre";
			            $statementPAgo = $dbh->query($queryTipoPago);
			            $nc=0;$cont= array();
			            while ($row = $statementPAgo->fetch()){ ?>
			                <option <?=($cod_tipo==$row["codigo"])?"selected":"";?>   value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
			            <?php }             
			            ?>
			        </select>                                    
			    </div>
			</div>
		</div> -->
		<input type="hidden" id="total_items_tipopago" name="total_items_tipopago" value="<?=$index?>">
		<!-- <script> window.onload = calcularTotalFilaTipoPagoModal;</script> -->
	</div>
</div>