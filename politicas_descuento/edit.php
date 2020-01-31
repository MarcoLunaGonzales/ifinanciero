<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codPoliticaDescuento=$cod_pol_desc;

$stmt = $dbh->prepare("SELECT codigo,nombre,minutos_inicio,minutos_final,porcentaje_diahaber FROM $table_politicaDescuento where codigo=:codigo");

$stmt->bindParam(':codigo',$codPoliticaDescuento);

$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$row['codigo'];
	$nombreX=$row['nombre'];
	$minutos_inicioX=$row['minutos_inicio'];
	$minutos_finalX=$row['minutos_final'];
	$porcentaje_diahaberX=$row['porcentaje_diahaber'];
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
				  <label class="col-sm-2 col-form-label">Nombre</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="nombre" id="nombre" required="true" value="<?=$nombreX;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Minutos Inicio</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" step="any" min="0" name="minutos_inicio" id="minutos_inicio" required="true" value="<?=$minutos_inicioX;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" />
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Minutos Final</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" step="any" min="0" name="minutos_final" id="minutos_final" required="true" value="<?=$minutos_finalX;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" />
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Porcentaje Dia Haber %</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" step="any" min="0" name="porcentaje_diahaber" id="porcentaje_diahaber" required="true" value="<?=$porcentaje_diahaberX;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" />
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