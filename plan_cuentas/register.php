<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
require_once 'functions.php';

$dbh = new Conexion();

$codigo=$codigo;

//echo $codigo;
$cuentaPadre="";
$nuevaCuenta="";
$nivelCuenta=0;
if($codigo>0){
	$cuentaPadre=obtieneNumeroCuenta($codigo);
	$nuevaCuentaFormateada=formateaPuntosPlanCuenta($cuentaPadre);
	$nivelCuenta=buscarNivelCuenta($nuevaCuentaFormateada);
	$nuevaCuenta=obtieneNuevaCuenta($nuevaCuentaFormateada);
}

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>


<script type="text/javascript">
    $("#numero").mask("0.00.00.00.000");
</script>



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
				  <label class="col-sm-2 col-form-label">Codigo</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="numero" id="numero" required="true" minLength="10" maxLength="10" onChange="ajaxObtienePadre(this);" value="<?=$nuevaCuenta;?>"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Cuenta Padre</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="padre" id="padre" required="true" readonly="true" value="<?=$cuentaPadre;?>"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Nombre</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="nombre" id="nombre" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Tipo de Cuenta</label>
						<div class="col-sm-4">
				        	<div class="form-group">
					        <select class="selectpicker form-control" name="tipocuenta" id="tipocuenta" data-style="<?=$comboColor;?>">
							  	<option disabled selected value="">Tipo de Cuenta</option>
							  	<?php
							  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM tipos_cuenta order by 2");
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
				  <label class="col-sm-2 col-form-label">Moneda</label>
						<div class="col-sm-4">
				        	<div class="form-group">
					        <select class="selectpicker form-control" name="moneda" id="moneda" data-style="<?=$comboColor;?>">
							  	<option disabled selected value="">Moneda</option>
							  	<?php
							  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas order by 2");
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
					<label class="col-sm-2 col-form-label">Cuentas Auxiliares</label>
					<div class="col-sm-4">
					<div class="form-group form-check">
                        <label class="form-check-label">
                      		<input class="form-check-input" type="checkbox" name="cuenta_auxiliar" value="1">
                          		<span class="form-check-sign">
                            		<span class="check"></span>
                              	</span>
                        </label>
                    </div>
                	</div>
				</div>
			  </div>
			  <div  class="card-footer fixed-bottom">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList2;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>