<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codGestion=$_SESSION['globalGestion'];

$codDet=$cod_refrigeriodetalle;

$codRef=$_GET['cod_ref'];
$codMes=$_GET['cod_mes'];

$stmt = $dbh->prepare("SELECT dias_asistidos,monto FROM refrigerios_detalle WHERE cod_estadoreferencial=1 and codigo=$codDet");
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$dias=$row['dias_asistidos'];
	$monto=$row['monto'];
}
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="refrigerios/saveEditDetalle.php" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				<div class="row">
				  <label class="col-sm-2 col-form-label">Dias</label>
				  <div class="col-sm-4">
				       <div class="form-group">
					    <input type="number" class="form-control" name="dias" id="dias" value="<?=$dias?>">   
					   </div>
				   </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Monto refrigerio</label>
				  <div class="col-sm-4">
				       <div class="form-group">
					    <input type="number" step="0.01" class="form-control" name="monto" id="monto" value="<?=$monto?>">   
					   </div>
				   </div>
				</div>
				<input class="form-control" type="text" hidden="true" name="codigo" id="codigo"  value="<?=$codDet;?>"/>
				<input class="form-control" type="text" hidden="true" name="cod_ref" id="cod_ref"  value="<?=$codRef;?>"/>
				<input class="form-control" type="text" hidden="true" name="cod_mes" id="cod_mes"  value="<?=$codMes;?>"/>

			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?= $urlDetalle; ?>&cod_ref=<?= $codRef; ?>&cod_mes=<?=$codMes;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>