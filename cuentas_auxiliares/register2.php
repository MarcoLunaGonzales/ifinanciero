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
				  <label class="col-sm-2 col-form-label">Tipo</label>
						<div class="col-sm-4">
				        	<div class="form-group">
					        <select class="selectpicker form-control form-control-sm" name="tipo" id="tipo" data-style="<?=$comboColor;?>" required="true" onChange="ajaxTipoProveedorCliente(this);">
							  	<option disabled selected value="">Seleccionar una opcion</option>
								<option value="1">Proveedor</option>	
								<option value="2">Cliente</option>	
								<option value="3">Personal</option>	
							</select>
							</div>
				      	</div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Proveedor/Cliente</label>
				  <div class="col-sm-7">
					<div class="form-group" id="divProveedorCliente">
					  
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