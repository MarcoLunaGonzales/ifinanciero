<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();

$codigoX=$codigo;
$codigoCuentaPadre=$codigo_padre;
$codigo=$codigoCuentaPadre;

require_once 'configModule.php';


$numeroCuentaPadre=obtieneNumeroCuenta($codigoCuentaPadre);
$nombreCuentaPadre=nameCuenta($codigoCuentaPadre);;

$sql="SELECT p.codigo, p.nombre, p.cod_tipoauxiliar, p.cod_proveedorcliente FROM $table p where p.codigo='$codigoX'";
//  echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$row['codigo'];
	$nombreX=$row['nombre'];
	$codTipoAuxiliar=$row['cod_tipoauxiliar'];
	$codProveedorCliente=$row['cod_proveedorcliente'];

}
?>


<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveEdit2;?>" method="post">
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
				  <label class="col-sm-2 col-form-label">Tipo</label>
						<div class="col-sm-4">
				        	<div class="form-group">
					        <select class="selectpicker form-control form-control-sm" name="tipo" id="tipo" data-style="<?=$comboColor;?>" required="true" onChange="ajaxTipoProveedorCliente(this);">
							  	<option disabled selected value="">Seleccionar una opcion</option>
								<option value="1" <?=($codTipoAuxiliar==1)?"selected":"";?> >Proveedor</option>	
								<option value="2" <?=($codTipoAuxiliar==2)?"selected":"";?> >Cliente</option>
								<option value="3" <?=($codTipoAuxiliar==3)?"selected":"";?> >Personal</option>
							</select>
							</div>
				      	</div>
				</div>

				<?php
				
				$sql="";
				if($codTipoAuxiliar==1){
					$sql="SELECT p.codigo, p.nombre from af_proveedores p where p.cod_estado=1 order by p.nombre";	
				}
				if($codTipoAuxiliar==2){
					$sql="SELECT c.codigo, c.nombre from clientes c where c.cod_estadoreferencial=1 order by c.nombre";
				}
				if($codTipoAuxiliar==3){
					$sql="SELECT codigo, CONCAT_WS(' ',primer_nombre,paterno,materno)as nombre from personal where cod_estadopersonal=1 and cod_estadoreferencial=1 order by nombre";
				}
				$stmt = $dbh->prepare($sql);
				$stmt->bindParam(':codigo', $codigoProvCli);
				$stmt->bindParam(':nombre', $nombreProvCli);
				$stmt->execute();

				?>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Proveedor/Cliente</label>
				  <div class="col-sm-7">
					<div class="form-group" id="divProveedorCliente">

						<select name="proveedor_cliente" id="proveedor_cliente" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" >
						    <?php 
						        while ($row = $stmt->fetch()){ 
						    ?>
						         <option value="<?=$row["codigo"];?>" <?=($codigoProvCli==$codProveedorCliente)?"selected":"";?> ><?=$row["nombre"];?></option>
						     <?php 
						        } 
						    ?>
						 </select>
					  
					</div>
				  </div>
				</div>

			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList2;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>