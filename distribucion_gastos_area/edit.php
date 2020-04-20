<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codEscalaAntiguedad=$cod_esc_ant;
$stmt = $dbh->prepare("SELECT codigo,nombre,anios_inicio,anios_final,porcentaje FROM $table_escalaAntiguedad where codigo=:codigo");
// Ejecutamos
$stmt->bindParam(':codigo',$codEscalaAntiguedad);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$row['codigo'];
	$nombreX=$row['nombre'];
	$anios_inicioX=$row['anios_inicio'];
	$anios_finX=$row['anios_final'];
	$porcentajeX=$row['porcentaje'];
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
				  <label class="col-sm-2 col-form-label">Años Inicio</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" step="any" min="0" name="anios_inicio" id="anios_inicio" required="true" value="<?=$anios_inicioX;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" />
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Años Final</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" step="any" min="0" name="anios_fin" id="anios_fin" required="true" value="<?=$anios_finX;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" />
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Porcentaje %</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" step="any" min="0" name="porcentaje" id="porcentaje" required="true" value="<?=$porcentajeX;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" />
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