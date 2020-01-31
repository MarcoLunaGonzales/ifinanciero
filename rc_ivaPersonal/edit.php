<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codRciva=$cod_rciva;
$stmt = $dbh->prepare("SELECT codigo,monto,cod_mes FROM $table_rcivaPersonal where codigo=:codigo");
// Ejecutamos
$stmt->bindParam(':codigo',$codRciva);
$stmt->execute();
$codGestion=$_SESSION['globalGestion'];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$row['codigo'];
	$montoX=$row['monto'];
	$codMes=$row['cod_mes'];
}

?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveEdit;?>" method="post">
			<input type="hidden" name="codigo" id="codigo" value="<?=$codigoX;?>"/>
			<div class="card ">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Editar <?=$moduleNameSingular;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				<div class="row">
				  <label class="col-sm-2 col-form-label">Monto</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" step="0.01" name="monto" id="monto" required="true" value="<?=$montoX;?>" />
					</div>
				  </div>
				</div>
				<input type="hidden" name="monto_iva"  value="<?=calculaIva($montoX);?>"/>
				<input type="hidden" name="cod_mes"  value="<?=$codMes?>"/>
				<input class="form-control" type="text" hidden="true" name="codGestion" id="codGestion"  value="<?=$codGestion;?>"/>				
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>