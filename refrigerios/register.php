<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codGestion=$_SESSION['globalGestion'];
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSave;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				<div class="row">
				  <label class="col-sm-2 col-form-label">Mes</label>
				  <div class="col-sm-4">
				        	<div class="form-group">
					        <select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" title="-- Elija un mes --" name="mes" id="mes" data-style="<?=$comboColor;?>" required="true">
							  	<option disabled selected value="">Mes</option>
							  	<?php
								  $stmt = $dbh->prepare("select m.codigo,m.nombre from meses m where m.cod_estado=1
								  and m.codigo
								  not in (select r.cod_mes from refrigerios r where r.cod_gestion=$codGestion)");
								  $stmt->execute();
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$codigoX=$row['codigo'];
									$nombreMesX=$row['nombre'];							
								?>
								<option value="<?=$codigoX;?>"><?=$nombreMesX;?></option>	
								<?php
							  	}
							  	?>
							</select>
							</div>
				      	</div>
				</div>
				
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