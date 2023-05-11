<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
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
				  <label class="col-sm-2 col-form-label">Nombre</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="nombre" id="nombre" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Abreviatura</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="abreviatura" id="abreviatura" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
					</div>
				  </div>
				</div>
          		<div class="row">
	                <label class="col-sm-2 col-form-label">Cuenta</label>
	                <div class="col-sm-7">
	                	<div class="form-group">
                        	<select class="selectpicker form-control form-control-sm" name="cuenta" id="cuenta" data-style="select-with-transition" data-actions-box="true" data-live-search="true" required>
	  	                    <?php
	  	                    $stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre FROM plan_cuentas p where p.cod_estadoreferencial=1 and (numero like '2%' or numero like '1%') order by 2");
		                    $stmt->execute();
		                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		                     	$codigoX=$row['codigo'];
		                     	$nombreX=$row['nombre'];
		                     	$numeroX=$row['numero'];
		                    ?>
		                    <option value="<?=$codigoX;?>" selected>[<?=$numeroX;?>] - <?=$nombreX;?></option>	
		                    <?php
	  	                    }
	  	                    ?>
	                       	</select>
                     	</div>
	                </div>
              	</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Observaciones</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="observaciones" id="observaciones" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
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