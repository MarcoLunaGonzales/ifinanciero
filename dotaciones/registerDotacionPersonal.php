<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codDotacion=$cod_dot;
$codGestion=$_SESSION["globalGestion"];

//Mostrar tipo dotacion
$stmtb = $dbh->prepare("SELECT nombre FROM dotaciones WHERE codigo=$codDotacion");
$stmtb->execute();
$stmtb->bindColumn('nombre', $nombreDotacion);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  $nomDotacion = $nombreDotacion;
}


?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveDotacionPersonal;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingularDP;?></h4>
				</div>
				<h4 class="card-title" align="center"><?=  "Dotación de " . $nomDotacion ?></h4>
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
								  (select rd.cod_personal from refrigerios_detalle rd, refrigerios r where rd.cod_refrigerio=r.codigo)");
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
					  <input class="form-control" type="number" step="any" min="0" name="monto" id="monto" required="true"/>
					</div>
				  </div>
				</div>
				<input class="form-control" type="text" hidden="true" name="codDotacion" id="codDotacion"  value="<?=$codDotacion;?>"/>
				<input class="form-control" type="text" hidden="true" name="codGestion" id="codGestion"  value="<?=$codGestion;?>"/>


			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListDotacionPersonal;?>&cod_dot=<?=$codDotacion;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>