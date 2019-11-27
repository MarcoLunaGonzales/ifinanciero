<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
require_once 'functions.php';

$dbh = new Conexion();

//RECIBIMOS LA VARIABLE DE LA CUENTA 
$codigoCuentaPadre=$codigo;

$numeroCuentaPadre=obtieneNumeroCuenta($codigoCuentaPadre);
$nombreCuentaPadre=nameCuenta($codigoCuentaPadre);

?>


<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSave;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
                  <h6 class="card-title"><?=$numeroCuentaPadre;?> <?=$nombreCuentaPadre;?></h6>
				</div>
			  </div>
			  <div class="card-body ">

				<input type="hidden" name="codigo" id="codigo" value="<?=$codigoCuentaPadre;?>"/>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Nombre</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="nombre" id="nombre" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Banco</label>
						<div class="col-sm-4">
				        	<div class="form-group">
					        <select class="selectpicker form-control" name="banco" id="banco" data-style="<?=$comboColor;?>" required="true">
							  	<option disabled selected value="">Banco</option>
							  	<?php
							  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM bancos order by 2");
								$stmt->execute();
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$codigoX=$row['codigo'];
									$nombreX=$row['nombre'];
									$abrevX=$row['abreviatura'];
								?>
								<option value="<?=$codigoX;?>"><?=$nombreX;?></option>	
								<?php
							  	}
							  	?>
							</select>
							</div>
				      	</div>
				</div>


				<div class="row">
				  <label class="col-sm-2 col-form-label">Nro. de Cuenta</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="nro_cuenta" id="nro_cuent-a"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Direccion</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="direccion" id="direccion"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Telefono</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="telefono" id="telefono"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Referencia 1</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="referencia1" id="referencia1"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Referencia 2</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="referencia2" id="referencia2"/>
					</div>
				  </div>
				</div>


			  </div>
			  <div  class="card-footer fixed-bottom">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList2;?>" class="<?=$buttonCancel;?>">Cancelar</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>