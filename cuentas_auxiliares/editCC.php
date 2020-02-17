<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();

$codigoX=$codigo;
$codigoCuentaPadre=$codigo_padre;
$codigo=$codigoCuentaPadre;

require_once 'configModule.php';


$numeroCuentaPadre=obtieneNumeroCuentaCC($codigoCuentaPadre);
$nombreCuentaPadre=nameCuentaCC($codigoCuentaPadre);

$sql="SELECT p.codigo, p.nombre, p.cod_banco, p.nro_cuenta, p.direccion, p.telefono, p.referencia1, p.referencia2 FROM cuentas_auxiliares_cajachica p where p.codigo='$codigoX'";
//  echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$row['codigo'];
	$nombreX=$row['nombre'];
	$nroCuentaX=$row['nro_cuenta'];
	$codBancoX=$row['cod_banco'];
	$direccionX=$row['direccion'];
	$telefonoX=$row['telefono'];
	$referencia1X=$row['referencia1'];
	$referencia2X=$row['referencia2'];
}


?>


<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveEditCC;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Editar <?=$moduleNameSingular;?></h4>
                  <h6 class="card-title"><?=$numeroCuentaPadre;?> <?=$nombreCuentaPadre;?></h6>
				</div>
			  </div>
			  <div class="card-body ">

				<input type="hidden" name="codigo" id="codigo" value="<?=$codigoX;?>"/>
				<input type="hidden" name="codigo_padre" id="codigo_padre" value="<?=$codigoCuentaPadre;?>"/>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Nombre</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="nombre" id="nombre" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?=$nombreX;?>"/>
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
									$codigoY=$row['codigo'];
									$nombreY=$row['nombre'];
									$abrevY=$row['abreviatura'];
								?>
								<option value="<?=$codigoY;?>" <?=($codigoY==$codBancoX)?"selected":"";?> ><?=$nombreY;?></option>	
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
					  <input class="form-control" type="text" name="nro_cuenta" id="nro_cuenta" value="<?=$nroCuentaX;?>"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Direccion</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="direccion" id="direccion" value="<?=$direccionX;?>"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Telefono</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="telefono" id="telefono" value="<?=$telefonoX;?>"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Referencia 1</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="referencia1" id="referencia1" value="<?=$referencia1X;?>"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Referencia 2</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="referencia2" id="referencia2" value="<?=$referencia2X;?>"/>
					</div>
				  </div>
				</div>


			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListCC2;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>