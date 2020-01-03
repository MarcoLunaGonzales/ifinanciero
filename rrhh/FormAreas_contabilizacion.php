<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';


$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;
if ($codigo>0) {
	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura, contabilizacion_vista FROM areas_contabilizacion where codigo=:codigo");
	// Ejecutamos
	$stmt->bindParam(':codigo',$codigo);
	$stmt->execute();

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$codigoX=$row['codigo'];
		$nombreX=$row['nombre'];
		$abreviaturaX=$row['abreviatura'];
		$contabilizacion_vistaX=$row['contabilizacion_vista'];
	}	
}else{
	$codigoX='';
	$nombreX='';
	$abreviaturaX='';
	$contabilizacion_vistaX='';

}

?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveAreas_contabilizacion;?>" method="POST">
			<input type="hidden" name="codigo" id="codigo" value="<?=$codigoX;?>"/>
			<div class="card ">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if($codigo>0){?>Editar <?=$nombreSingularAreas_contabilizacion;}else{?>Registrar <?=$nombreSingularAreas_contabilizacion;}?></h4>
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
				  <label class="col-sm-2 col-form-label">Abreviatura</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="abreviatura" id="abreviatura" required="true" value="<?=$abreviaturaX;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Contabilización Vista</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <!-- <input class="form-control" type="number" name="contabilizacion_vista" id="contabilizacion_vista" required="true" value="<?=$contabilizacion_vistaX;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/> -->
					  <select name="contabilizacion_vista" id="contabilizacion_vista" class="selectpicker" data-style="btn btn-primary">
					  	<option value="0">RESUMDIDA</option>
					  	<option value="1">DETALLADA</option>
					  </select>	
					</div>
				  </div>
				</div>
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListAreas_contabilizacion;?>" class="<?=$buttonCancel;?>">Cancelar</a>
			  </div>
			</div>
		  </form>
		</div>	
	</div>
</div>