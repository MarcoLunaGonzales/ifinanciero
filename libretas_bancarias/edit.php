<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_GET['codigo'];
$stmt = $dbh->prepare("SELECT * from libretas_bancarias where codigo=:codigo");
// Ejecutamos
$stmt->bindParam(':codigo',$codigo);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$row['codigo'];
	$cod_cuentaX=$row['cod_cuenta'];
	$cod_contracuentaX=$row['cod_contracuenta'];
	$nombreX=$row['nombre'];
	$nroCuenta=$row['nro_cuenta'];
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
					  <input class="form-control" type="text" name="nombre" id="nombre" required="true" value="<?=$nombreX;?>"/>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Nro Cuenta</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="nro_cuenta" id="nro_cuenta" required="true" value="<?=$nroCuenta;?>"/>
					</div>
				  </div>
				</div>
				 <div class="row">
				  <label class="col-sm-2 col-form-label">Cuenta</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <select class="selectpicker form-control" name="cod_cuenta" id="cod_cuenta" data-size="6" data-live-search="true" data-style="btn btn-info" required>
                          <?php
                  $stmt = $dbh->prepare("SELECT p.codigo,p.nombre FROM plan_cuentas p where p.nivel=5 order by p.codigo");
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$row['codigo'];
                  $nombreX=$row['nombre'];
                if($codigoX==$cod_cuentaX){
                    ?>
                <option value="<?=$codigoX;?>" selected><?=$nombreX;?></option>  
                <?php
                  }else{
                    ?>
                <option value="<?=$codigoX;?>" ><?=$nombreX;?></option>  
                <?php
                  }
                  }
                  ?> 
                       </select>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Contra Cuenta</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <select class="selectpicker form-control" name="cod_contracuenta" id="cod_contracuenta" data-size="6" data-live-search="true" data-style="btn btn-warning" required>
                          <?php
                  $stmt = $dbh->prepare("SELECT p.codigo,p.nombre FROM plan_cuentas p where p.nivel=5 order by p.codigo");
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$row['codigo'];
                  $nombreX=$row['nombre'];
                  if($codigoX==$cod_contracuentaX){
                    ?>
                <option value="<?=$codigoX;?>" selected><?=$nombreX;?></option>  
                <?php
                  }else{
                    ?>
                <option value="<?=$codigoX;?>" ><?=$nombreX;?></option>  
                <?php
                  }
                
                  }
                  ?> 
                       </select>
					</div>
				  </div>
				</div>
			  </div>
			  <div  class="card-footer fixed-bottom">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList;?>" class="<?=$buttonCancel;?>">Volver</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>