<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$sql="SELECT * from gestiones order by 1 desc";
$stmtGestiones = $dbh->query($sql);




?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSave7;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
  				<div class="card-text">
  				  <h4 class="card-title">Registrar <?=$moduleNameSingular7;?></h4>
  				</div>
			  </div>
			  <div class="card-body ">
				  <div class="row">
              <label class="col-sm-2 col-form-label">Gestion</label>
              <div class="col-sm-7">
              <div class="form-group">
                  <!-- <input class="form-control" type="text" name="gestion" id="gestion" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/> -->
                  <select name="gestion" id="gestion" class="selectpicker " data-style="btn btn-info">
                    <?php
                      while ($row = $stmtGestiones->fetch()) { ?>
                        <option value="<?=$row["nombre"];?>"><?=$row["nombre"];?></option>
                        <!-- <option value="<?=$codigoX;?>"><?=$nombre;?></option>                                                   -->
                        <?php }?>

                  </select>

              </div>
              </div>
          </div><!--fin campo gestion -->

          <div class="row">
            <label class="col-sm-2 col-form-label">Mes</label>
            <div class="col-sm-7">
            <div class="form-group">
                <select name="mes" id="mes" class="selectpicker " data-style="btn btn-info">
                        <!--<select name="tipoalta" id="tipoalta" class="selectpicker " data-style="select-with-transition">-->
        					<option value="1">ENERO</option>
        					<option value="2">FEBRERO</option>
                  <option value="3">MARZO</option>
                  <option value="4">ABRIL</option>
                  <option value="5">MAYO</option>
                  <option value="6">JUNIO</option>
                  <option value="7">JULIO</option>
                  <option value="8">AGOSTO</option>
                  <option value="9">SEPTIEMBRE</option>
                  <option value="10">OCTUBRE</option>
                  <option value="11">NOVIEMBRE</option>
                  <option value="12">DICIEMBRE</option>
        				</select>
            </div>
            </div>
          </div><!--fin campo mes -->
          
			  </div>
			  <div class="card-footer ml-auto mr-auto">
  				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
  				<a href="?opcion=ejecutarDepreciacionLista" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>