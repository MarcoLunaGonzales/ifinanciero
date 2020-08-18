<?php
require_once '../conexion.php';
require_once '../functions.php';

//header('Content-Type: application/json');

$cod_tipo = $_GET["cod_tipo"];

$dbh = new Conexion();

?>
<div class="row">
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
</div>