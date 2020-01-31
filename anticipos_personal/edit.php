<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codAnticipoPersonal=$cod_ant_per;
$codMes=$_GET['cod_mes'];

$stmt = $dbh->prepare("SELECT codigo,monto FROM $table_anticiposPersonal where codigo=:codigo");
// Ejecutamos
$stmt->bindParam(':codigo',$codAnticipoPersonal);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$row['codigo'];
	$montoX=$row['monto'];
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
					  <input class="form-control" type="text" name="monto" id="monto" required="true" value="<?=$montoX;?>" />
					  <input class="form-control" type="hidden" name="cod_mes" id="cod_mes" value="<?=$codMes;?>" />
					</div>
				  </div>
				</div>

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