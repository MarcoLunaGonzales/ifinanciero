<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_GET['codigo'];
$stmt = $dbh->prepare("SELECT * from cheques where codigo=:codigo");
// Ejecutamos
$stmt->bindParam(':codigo',$codigo);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$row['codigo'];
	$inicioX=$row['nro_inicio'];
	$finalX=$row['nro_final'];
	$chequeX=$row['nro_cheque'];
	$serieX=$row['nro_serie'];
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
				  <label class="col-sm-2 col-form-label">Nro Cuenta</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="serie" id="serie" required="true" value="<?=$serieX;?>"/>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Final</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" name="final" id="final" required="true" value="<?=$finalX;?>"/>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label text-primary">Cheque</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" style="background-color:#E3CEF6;text-align: left" min="<?=$chequeX;?>" type="number" name="cheque" id="cheque" required="true" value="<?=$chequeX;?>"/>
					</div>
				  </div>
				</div>
				
			  </div>
			  <div  class="card-footer fixed-bottom">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList;?>" class="<?=$buttonCancel;?>">Volver</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>