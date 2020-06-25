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
				  <label class="col-sm-2 col-form-label">INSTANCIAS</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <select class="selectpicker form-control" name="instancia" id="instancia" data-style="<?=$comboColor;?>" data-live-search="true" required>
                          <?php
                             $stmt = $dbh->prepare("SELECT p.codigo,p.descripcion FROM instancias_envios_correos p order by p.codigo");
                             $stmt->execute();
                             while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                               $codigoX=$row['codigo'];
                               $nombreX=$row['descripcion'];
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
				  <label class="col-sm-2 col-form-label">PERSONAL</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <select class="selectpicker form-control" data-size="6" name="personal" onchange="ponerCorreoPersona()" id="personal" data-style="btn btn-warning" data-live-search="true" required>
					  	  <option value="" selected disabled>--Seleccione Personal--</option>  
                          <?php
                             $stmt = $dbh->prepare("SELECT p.codigo,CONCAT (p.primer_nombre,' ',p.otros_nombres,' ',p.paterno,' ',p.materno) as personal,p.email_empresa,p.email FROM personal p order by p.paterno");
                             $stmt->execute();
                             while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                               $codigoX=$row['codigo'];
                               $nombreX=$row['personal'];
                             ?>
                          <option value="<?=$codigoX;?>$$$<?=$row["email_empresa"];?>" data-subtext="<?=$row["email_empresa"];?>"><?=$nombreX;?></option>  
                         <?php
                           }
                           ?> 
                       </select>
					</div>
				  </div>
				</div>
				<br>
				<div class="row">
				  <label class="col-sm-2 col-form-label">CORREO</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="email" name="correo" id="correo" required/>
					</div>
				  </div>
				</div>
				<!--<div class="row">
				  <label class="col-sm-2 col-form-label">CORREO ALTERNATIVO</label>
				  <div class="col-sm-7">
					<div class="form-group">-->
					  <input class="form-control" type="hidden" name="correo_otro" id="correo_otro" value="" placeholder=""/>
					<!--</div>
				  </div>
				</div>-->
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