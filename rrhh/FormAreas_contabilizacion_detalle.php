<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';


$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$codigo;

$queryUO = "SELECT codigo,nombre,abreviatura FROM  unidades_organizacionales WHERE cod_estado=1
order by nombre";
$statementUO = $dbh->query($queryUO);//uo

// $queryArea = "SELECT codigo,nombre FROM  areas WHERE cod_estado=1
// order by nombre";
// $statementArea = $dbh->query($queryArea);//uo

?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveAreas_contabilizacion_detalle;?>" method="POST">
			<input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
			<div class="card ">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar</h4>
				</div>
			  </div>
			  <div class="card-body ">
				<div class="row">
				  <label class="col-sm-3 col-form-label">Unidad Organizacional</label>
				  <div class="col-sm-7">
					<div class="form-group">
						<select name="cod_uo" id="cod_uo" data-style="btn btn-primary" onChange="ajaxAreaContabilizacionDetalle(this);" onChange="ajaxPersonal_area_distribucionE(this);" class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
	                        <option value=""></option>
	                        <?php while ($row = $statementUO->fetch()){ ?>
	                            <option value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
	                        <?php } ?>
	                    </select>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-3 col-form-label">Area</label>
				  <div class="col-sm-7">
					<div class="form-group" >
						<div id="div_contenedor_area">
							<select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" >
		                        <option value="" disabled="disabled"></option>
		                       
	                    	</select>
						</div>					  
					</div>
				  </div>
				</div>				


			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$list_areas_contabilizacion_Detalle;?>&codigo=<?=$codigo?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>