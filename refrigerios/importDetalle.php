<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

  $dbh = new Conexion();
?>


<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" enctype="multipart/form-data" class="form-horizontal" action="<?=$urlSaveRefrigerio;?>" method="post">
  
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Importar Archivo</h4>
				</div>
			  </div>
			  <div class="card-body ">
				
          <input type="hidden" name="cod_ref" value="<?=$cod_ref?>">
          <input type="hidden" name="cod_mes" value="<?=$cod_mes?>">
          
          <div class="row">
            <label class="col-sm-2 col-form-label">Archivo .xlsx</label>
            <div class="col-sm-7">
              <input class="form-control" type="file" name="file" id="file" accept=".xlsx" required="true">
            </div>
          </div>



      <div class="row">
        <label class="col-sm-2 col-form-label">Opciones</label>
          <div class="col-sm-4">
                <div class="form-group">
                <select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" title="-- Elija un opcion --" name="cod_opcion" id="cod_opcion" data-style="<?=$comboColor;?>" required="true">
                <option value="1">Anexar sin borrar</option>
                <option value="2">Borrar y cargar</option>
            </select>
            </div>
              </div>
      </div>



			  </div>
			  <div class="card-footer ml-auto mr-auto">
			    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
          <a href="?opcion=listRefrigerioDetalle&cod_ref=<?=$cod_ref?>&cod_mes=<?=$cod_mes?>" class="<?= $buttonCancel; ?>">Volver</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>