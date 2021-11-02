<?php

// require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
require_once 'conexion.php';
?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSave_cambiar_resp?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Cambiar Resposanble AF.</h4>
				</div>
			  </div>
			<div class="card-body ">                
                <div class="row">
                    <label class="col-sm-2 col-form-label">Resposanble</label>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <select name="cod_responable1" id="cod_responable1" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                                <option value=""></option>
                                <?php 
                                $query = "SELECT codigo,paterno,materno,primer_nombre from personal where cod_estadoreferencial=1";
                                $statement = $dbh->query($query);
                                while ($row = $statement->fetch()){ 
                                    ?>
                                    <option  value="<?=$row["codigo"];?>"><?=$row["paterno"];?> <?=$row["materno"];?> <?=$row["primer_nombre"];?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>                    
                    <label class="col-sm-2 col-form-label">Nuevo Respo</label>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <select name="nuevo_cod_responable1" id="nuevo_cod_responable1" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                                <option value=""></option>
                                <?php 
                                $query = "SELECT codigo,paterno,materno,primer_nombre from personal where cod_estadoreferencial=1";
                                $statement = $dbh->query($query);
                                while ($row = $statement->fetch()){ 
                                    ?>
                                    <option  value="<?=$row["codigo"];?>"><?=$row["paterno"];?> <?=$row["materno"];?> <?=$row["primer_nombre"];?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>                    
                </div>                
                <div id="contenedor_detalle">                    
                </div>
			</div>
			<div class="card-footer fixed-bottom">
				<button type="button" class="btn btn-info" onclick="ajaxBuscar_personal_af_cambio()">Buscar</button>
                <div id="contenedor_boton_save_elimprod">
                    
                </div>
			</div>
			</div>
		  </form>
		</div>
	
	</div>
</div>


<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde un momento por favor.</p>  
  </div>
</div>
