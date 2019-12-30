<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codRefrigerio=$cod_refrigerio;
$codGestion=$_SESSION["globalGestion"];


//Seleccionar el monto de refrigerio de configuraciones
$stmtb = $dbh->prepare("SELECT c.valor_configuracion FROM configuraciones c WHERE c.id_configuracion=10");
$stmtb->execute();
$stmtb->bindColumn('valor_configuracion', $valorConfiguracion);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  $valorConfiguracionX = $valorConfiguracion;
}




?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveRefrigerioDetalle;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
			  <div class="row">
				  <label class="col-sm-2 col-form-label">Personal</label>
						<div class="col-sm-4">
				        	<div class="form-group">
					        <select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" title="-- Elija un personal --" name="personal" id="personal" data-style="<?=$comboColor;?>" required="true">
							  	<option disabled selected value="">Persona</option>
							  	<?php
								  $stmt = $dbh->prepare("select p.codigo as codigo,
								  concat(p.paterno,' ', p.materno, ' ', p.primer_nombre) as nombrepersona
								  from personal p  where p.codigo  not in 
								  (select rd.cod_personal from refrigerios_detalle rd, refrigerios r where rd.cod_refrigerio=r.codigo and rd.cod_refrigerio=$codRefrigerio)");
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




				<input class="form-control" type="text" hidden="true" name="monto" id="monto"  value="<?=$valorConfiguracionX;?>"/>
				<input class="form-control" type="text" hidden="true" name="codRefrigerio" id="codRefrigerio"  value="<?=$codRefrigerio;?>"/>



			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListDotacionPersonal;?>&cod_dot=<?=$codDotacion;?>" class="<?=$buttonCancel;?>">Cancelar</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>