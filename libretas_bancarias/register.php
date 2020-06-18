<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$fechaActual=date("d/m/Y");
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="formChequePago" class="form-horizontal" action="<?=$urlSave;?>" method="post">
			<div class="card">
			  <div class="card-header card-header-info card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
			  	<div class="row">
				  <label class="col-sm-2 col-form-label">Nombre Libreta</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="nombre_libreta" id="nombre_libreta" value="" required/>
					</div>
				  </div>
				</div>
			  	<div class="row">
				  <label class="col-sm-2 col-form-label">Bancos</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <select class="selectpicker form-control" name="banco_libreta" id="banco_libreta" data-size="6" data-style="<?=$comboColor;?>" data-live-search="true" required>
                          <?php
                  $stmt = $dbh->prepare("SELECT p.codigo,p.nombre,p.abreviatura FROM bancos p order by p.codigo"); //where NOT EXISTS (SELECT 1 FROM cheques d WHERE d.cod_banco=p.codigo)
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$row['codigo'];
                  $nombreX=$row['nombre'];
                  $abreviaturaX=$row['abreviatura'];
                ?>
                <option value="<?=$codigoX;?>" data-subtext="<?=$nombreX;?>"><?=$abreviaturaX;?></option>  
                <?php
                  }
                  ?> 
                       </select>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Nro Cuenta</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="nro_cuenta" id="nro_cuenta" value="" required/>
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
                ?>
                <option value="<?=$codigoX;?>" ><?=$nombreX;?></option>  
                <?php
                  }
                  ?> 
                       </select>
					</div>
				  </div>
				</div>
				<!--<div class="row">
				  <label class="col-sm-2 col-form-label">Fecha</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control datepicker" type="text" name="fecha" id="fecha" value="<?=$fechaActual?>"/>
					</div>
				  </div>
				</div>-->
				<br><br><br>
			  </div>
			  <div  class="card-footer fixed-bottom">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList;?>" class="<?=$buttonCancel;?>">Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>