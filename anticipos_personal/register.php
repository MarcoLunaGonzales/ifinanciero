<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codMes=$_GET['cod_mes'];
$codGestion=$_SESSION['globalGestion'];
$nombreGestion=$_SESSION['globalNombreGestion'];


//Mostrar Mes
$stmtc = $dbh->prepare("SELECT nombre FROM meses WHERE codigo=$codMes");
$stmtc->execute();
$stmtc->bindColumn('nombre', $nombreMes);

while ($row = $stmtc->fetch(PDO::FETCH_BOUND)) {
  $nomMes = $nombreMes;
}

?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form_anticipospersonal" class="form-horizontal" action="<?=$urlSave;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingular?></h4>

				</div>
				<h4 class="card-title" align="center"><?=  $nomMes . " " . $nombreGestion ?></h4>

			  </div>
			  <div class="card-body ">
				

				<div class="row">
				  <label class="col-sm-2 col-form-label">Personal</label>
						<div class="col-sm-4">
				        	<div class="form-group">
					        <select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" title="-- Elija un personal --" onchange="calcularHaberBasico()" name="personal" id="personal" data-style="<?=$comboColor;?>" required>
							  	<option disabled selected value="">Persona</option>
							  	<?php
								  $stmt = $dbh->prepare("select p.codigo as codigo, concat(p.paterno,' ', p.materno, ' ', p.primer_nombre) as nombrepersona from personal p 
								  where p.codigo not in 
								  (select d.cod_personal from anticipos_personal d where 
								  d.cod_mes=$codMes and d.cod_gestion=$codGestion and d.cod_estadoreferencial=1) ORDER BY p.paterno");
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
				  <label class="col-sm-2 col-form-label">Haber Basico <!--<small id="monto_max" class="text-danger"></small>--></label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" readonly name="haber_basico" value="" id="haber_basico"/>
					  <input class="form-control" type="hidden" name="haber_basico2" value="0" id="haber_basico2"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Monto <!--<small id="monto_max" class="text-danger"></small>--></label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" autocomplete="off" step="0.01" min="0" name="monto" id="monto" required="true" onchange="montoNoMayor()"/>					  
					</div>
				  </div>
				</div>
				
				<input class="form-control" type="text" hidden="true" name="codMes" id="codMes"  value="<?=$codMes;?>"/>
				<input class="form-control" type="text" hidden="true" name="codGestion" id="codGestion"  value="<?=$codGestion;?>"/>

				<div id="mensaje"></div>
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListMesPersona;?>&cod_mes=<?=$codMes;?>" class="<?=$buttonCancel;?>">Cancelar</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>