<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codGestion=$_SESSION['globalGestion'];
$nombreGestion=$_SESSION['globalNombreGestion'];


?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSave;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingular?></h4>

				</div>
			  </div>
			  <div class="card-body ">
               <div class="row">
				  <label class="col-sm-2 col-form-label">Proyectos</label>
						<div class="col-sm-4">
				        	<div class="form-group">
					        <select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" title="-- Elija un proyecto --" name="proyecto" id="proyecto" data-style="<?=$comboColor;?>" required="true">
							  	<option disabled selected value="">Proyecto</option>
							  	<?php
								  $stmt = $dbh->prepare("select p.codigo as codigo, p.nombre from proyectos_financiacionexterna p ORDER BY p.nombre");
								$stmt->execute();
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$codigoX=$row['codigo'];
									$nombreProyectoX=$row['nombre'];
							
								?>
								<option value="<?=$codigoX;?>"><?=$nombreProyectoX;?></option>	
								<?php
							  	}
							  	?>
							</select>
							</div>
				      	</div>
				</div>				

				<div class="row">
				  <label class="col-sm-2 col-form-label">Personal</label>
						<div class="col-sm-4">
				        	<div class="form-group">
					        <select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" title="-- Elija un personal --" name="personal" id="personal" data-style="<?=$comboColor;?>" required="true">
							  	<option disabled selected value="">Persona</option>
							  	<?php
								  $stmt = $dbh->prepare("select p.codigo as codigo, concat(p.paterno,' ', p.materno, ' ', p.primer_nombre) as nombrepersona from personal p ORDER BY p.paterno");
								$stmt->execute();
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$codigoX=$row['codigo'];
									$nombrePersonaX=$row['nombrepersona'];
								
									//if(verificarPersonaMes($codigoX,$codMes,$codDescuento)==0){
								?>
								<option value="<?=$codigoX;?>"><?=$nombrePersonaX;?></option>	
								<?php
								//	}
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
					  <input class="form-control" type="number" step="any" min="0" name="monto" id="monto" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
					</div>
				  </div>
				</div>
				
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList;?>" class="<?=$buttonCancel;?>">Cancelar</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>