<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codDotacion=$cod_dot;
$stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura,descripcion,nro_meses,fecha_inicio,fecha_fin
						FROM $table_dotaciones where codigo=:codigo");
// Ejecutamos
$stmt->bindParam(':codigo',$codDotacion);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$row['codigo'];
	$nombreX=$row['nombre'];
	$abreviaturaX=$row['abreviatura'];
	$descripcionX=$row['descripcion'];
	$nro_mesesX=$row['nro_meses'];
	$fecha_inicioX=$row['fecha_inicio'];
	$fecha_finX=$row['fecha_fin'];
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
					  <input class="form-control" type="text" name="nombre" id="nombre" required="true" value="<?=$nombreX;?>" />
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Abreviatura</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="abreviatura" id="abreviatura" required="true" value="<?=$abreviaturaX;?>" />
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Descripción</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="descripcion" id="descripcion" required="true" value="<?=$descripcionX;?>" />
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Nro. de Meses</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" name="nro_meses" id="nro_meses" required="true" value="<?=$nro_mesesX;?>" />
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Mes de Inicio</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="date" name="fecha_inicio" id="fecha_inicio" required="true" value="<?=$fecha_inicioX;?>" />
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Mes Fin</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="date" name="fecha_fin" id="fecha_fin" required="true" value="<?=$fecha_finX;?>" />
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