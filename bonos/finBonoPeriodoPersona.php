<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codBono=$codigo_bono;
$codGestion=$_SESSION['globalGestion'];
$nombreGestion=$_SESSION['globalNombreGestion'];

//Mostrar tipo bono
$stmtb = $dbh->prepare("SELECT nombre FROM $table WHERE codigo=$codBono");
$stmtb->execute();
$stmtb->bindColumn('nombre', $nombreBono);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  $nomBono= $nombreBono;
}

//Mostrar tipo bono
$stmtb = $dbh->prepare("SELECT nombre FROM gestiones WHERE codigo=$codGestion");
$stmtb->execute();
$stmtb->bindColumn('nombre', $nombreGestion);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  $nomGestion= (int)$nombreGestion;
}

$codMes=date("m");                

?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveFinBonoPeriodoPersona;?>" method="post">
			<div class="card">
			  <div class="card-header card-header-warning card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Fin <?=$moduleNameSingularDetalle;?></h4>
				</div>
				<h4 class="card-title" align="center"><?= $nomBono. " -  " . $nombreGestion ?></h4>
			  </div>
			  <div class="card-body ">
				

				<div class="row">
				  <label class="col-sm-2 col-form-label">Personal</label>
						<div class="col-sm-10">
				        	<div class="form-group">
					        <select class="selectpicker form-control form-control-sm" onchange="mandarDatosBonoIndefinido()" data-style="select-with-transition" data-live-search="true" title="-- Elija un personal --" name="personal" id="personal" data-style="btn btn-warning" required>
							  	<option disabled selected value="">Persona</option>
							  	<?php
								  $stmt = $dbh->prepare("select bm.codigo as codigo_bm,bm.monto,bm.observaciones,p.codigo as codigo, concat(p.paterno,' ', p.materno, ' ', p.primer_nombre) as nombrepersona from personal p 
								 join bonos_personal_mes bm on bm.cod_personal=p.codigo
								  where bm.cod_bono=$codBono and bm.indefinido=1");
								$stmt->execute();
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$codigoX=$row['codigo'];
									$codigoBMX=$row['codigo_bm'];
									$montoX=$row['monto'];
									$observacionesX=$row['observaciones'];
									$nombrePersonaX=$row['nombrepersona'];
								?>
								<option value="<?=$codigoBMX;?>@<?=$montoX;?>@<?=$observacionesX;?>"><?=$nombrePersonaX;?></option>	
								<?php
							  	}
							  	?>
							</select>
							</div>
				      	</div>
				</div>



				<div class="row">
				  <label class="col-sm-2 col-form-label">Monto</label>
				  <div class="col-sm-10">
					<div class="form-group">
					  <input class="form-control" readonly autocomplete="off" type="number" step="0.001" name="monto" id="monto" required="true"/>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Observaciones</label>
				  <div class="col-sm-10">
					<div class="form-group">
					  <textarea class="form-control" readonly name="obs" id="obs" required="true"></textarea>
					</div>
				  </div>
				</div>
				<input class="form-control" type="text" hidden="true" name="codBono" id="codBono"  value="<?=$codBono;?>"/>
				<input class="form-control" type="text" hidden="true" name="codGestion" id="codGestion"  value="<?=$codGestion;?>"/>

				
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Finalizar</button>
				<a href="<?=$urlListMes;?>&codigo=<?=$codBono;?>" class="<?=$buttonCancel;?>">Volver</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>