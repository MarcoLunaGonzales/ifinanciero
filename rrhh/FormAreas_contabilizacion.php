<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';


$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;
if ($codigo>0) {
	$stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura,cod_area,cod_uo,contabilizacion_vista
	 FROM areas_contabilizacion where codigo=:codigo");
	// Ejecutamos
	$stmt->bindParam(':codigo',$codigo);
	$stmt->execute();

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$codigoX=$row['codigo'];
		$nombreX=$row['nombre'];
		$abreviaturaX=$row['abreviatura'];
		$cod_areax=$row['cod_area'];
		$cod_uox=$row['cod_uo'];
		// $nombre_areaX=$row['nombre_area'];
		// $nombre_uoX=$row['nombre_uo'];
		$contabilizacion_vistaX=$row['contabilizacion_vista'];
	}	
}else{
	$codigoX='';
	$nombreX='';
	$abreviaturaX='';
	$cod_areax='';
	$cod_uox='';
	$contabilizacion_vistaX='';
}

$queryUO = "select * from unidades_organizacionales where cod_estado=1 order by nombre";
$statementUO = $dbh->query($queryUO);

$queryArea = "SELECT codigo,nombre FROM  areas WHERE cod_estado=1
order by nombre";
$statementArea = $dbh->query($queryArea);//uo

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
					  	<option value="0">RESUMIDA</option>
					  	<option value="1">DETALLADA</option>
					  </select>	
					</div>
				  </div>
				</div>
				<div class="row">
                  <label class="col-sm-2 col-form-label">Centro Costos UO</label>
                  <div class="col-sm-4">
                    <div class="form-group">
                        <select name="cod_uo" id="cod_uo" class="selectpicker" data-style="btn btn-primary" onChange="ajaxAreaContabilizacionDetalle(this);">
                            <option value=""></option>
                            <?php while ($row = $statementUO->fetch()){ ?>
                                <option <?=($cod_uox==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                            <?php } ?>
                        </select>
                    </div>
                  </div>
               
                  <label class="col-sm-2 col-form-label">Centro Costos Area</label>
                  <div class="col-sm-4">
                    <div class="form-group" >
                        <div id="div_contenedor_area">
                            <select name="cod_area" id="cod_area" class="selectpicker" data-style="btn btn-primary" >
                                <option value=""></option>
                                <?php while ($row = $statementArea->fetch()){ ?>
                                    <option <?=($cod_areax==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                <?php } ?>
                            </select>
                        </div>                    
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