<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codBono=$codigo_bono;
$codMes=$codigo_mes;
$codGestion=$_SESSION['globalGestion'];
$nombreGestion=$_SESSION['globalNombreGestion'];

//Mostrar tipo bono
$stmtb = $dbh->prepare("SELECT nombre FROM $table WHERE codigo=$codBono");
$stmtb->execute();
$stmtb->bindColumn('nombre', $nombreBono);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  $nomBono= $nombreBono;
}
                
//Mostrar Mes
$stmtc = $dbh->prepare("SELECT nombre FROM meses WHERE codigo=$codMes");
$stmtc->execute();
$stmtc->bindColumn('nombre', $nombreMes);

while ($row = $stmtc->fetch(PDO::FETCH_BOUND)) {
  $nomMes= $nombreMes;
}

?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveBonoMesPersona;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingularDetalle;?></h4>
				</div>
				<h4 class="card-title" align="center"><?= $nomBono. " : " . $nomMes . " " . $nombreGestion ?></h4>
			  </div>
			  <div class="card-body ">
				

				<div class="row">
				  <label class="col-sm-2 col-form-label">Personal</label>
						<div class="col-sm-4">
				        	<div class="form-group">
					        <select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" title="-- Elija un personal --" name="personal" id="personal" data-style="<?=$comboColor;?>" required="true">
							  	<option disabled selected value="">Persona</option>
							  	<?php
								  $stmt = $dbh->prepare("select p.codigo as codigo, concat(p.paterno,' ', p.materno, ' ', p.primer_nombre) as nombrepersona from personal p 
								  where p.codigo not in 
								  (select bpm.cod_personal from bonos_personal_mes bpm where bpm.cod_bono=$codBono and 
								  bpm.cod_mes=$codMes and bpm.cod_gestion=$codGestion) order by p.paterno");
								$stmt->execute();
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$codigoX=$row['codigo'];
									$nombrePersonaX=$row['nombrepersona'];
								?>
								<option value="<?=$codigoX;?>"><?=$nombrePersonaX;?></option>	
								<?php
							  	}
							  	?>
							</select>
							</div>
				      	</div>
				</div>



				<div class="row">
				  <label class="col-sm-2 col-form-label">Monto</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" step="0.001" name="monto" id="monto" required="true"/>
					</div>
				  </div>
				</div>
				
				<input class="form-control" type="text" hidden="true" name="codMes" id="codMes"  value="<?=$codMes;?>"/>
				<input class="form-control" type="text" hidden="true" name="codBono" id="codBono"  value="<?=$codBono;?>"/>
				<input class="form-control" type="text" hidden="true" name="codGestion" id="codGestion"  value="<?=$codGestion;?>"/>

				
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListMesPersona;?>&cod_mes=<?=$codMes;?>&cod_bono=<?=$codBono;?>" class="<?=$buttonCancel;?>">Cancelar</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>