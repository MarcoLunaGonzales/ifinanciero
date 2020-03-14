<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

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
				  <label class="col-sm-2 col-form-label">PROVEEDOR</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <select class="selectpicker form-control" name="banco" id="banco" data-style="<?=$comboColor;?>" data-live-search="true" required>
                          <?php
                  $stmt = $dbh->prepare("SELECT p.codigo,p.nombre FROM bancos p order by p.codigo"); //where NOT EXISTS (SELECT 1 FROM cheques d WHERE d.cod_banco=p.codigo)
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$row['codigo'];
                  $nombreX=$row['nombre'];
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
				  <label class="col-sm-2 col-form-label">Nro Inicio</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" name="inicio" id="inicio" value="1"/>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Nro Final</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" name="final" id="final" value="1"/>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Nro Serie</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" name="serie" id="serie" value="1"/>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Nro Cheque</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" name="cheque" id="cheque" value="1"/>
					</div>
				  </div>
				</div>
				
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