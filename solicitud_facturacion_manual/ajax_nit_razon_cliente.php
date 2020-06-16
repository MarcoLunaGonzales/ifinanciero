<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$cod_cliente=$_GET['cod_cliente'];

$query="SELECT * FROM clientes where codigo=$cod_cliente ";
$stmt = $dbh->prepare($query);
$stmt->execute();
$respuesta=$stmt->fetch();

$nit=$respuesta['identificacion'];
$razon_social=$respuesta['nombre'];
$descuento=$respuesta['descuento'];
?> 


<div class="row">
	
	<label class="col-sm-2 col-form-label">Razón Social</label>
	<div class="col-sm-5">
	    <div class="form-group">                                    
	        <input class="form-control" type="text" name="razon_social" id="razon_social" required="true" value="<?=$razon_social;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>                                        
	    </div>
	</div>
	<label class="col-sm-1 col-form-label">Nit</label>
	<div class="col-sm-4">
	    <div class="form-group">                                        
	            <input class="form-control" type="number" name="nit" id="nit" required="true" value="<?=$nit;?>" required="true"/>
	    </div>
	</div>

	<input class="form-control" type="hidden" name="descuento" id="descuento" required="true" value="<?=$descuento;?>" required="true"/>


</div>

