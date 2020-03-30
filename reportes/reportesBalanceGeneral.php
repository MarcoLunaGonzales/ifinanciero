<?php

// require_once 'layouts/bodylogin2.php';
require_once 'conexion.php';
require_once 'comprobantes/configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$dbh = new Conexion();
$query = "select * from depreciaciones";
$statement = $dbh->query($query);

$gestionGlobal=$_SESSION['globalGestion'];
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlReporteBalance;?>" method="post" target="_blank">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Reporte Balance General</h4>
				</div>
			  </div>
			  <div class="card-body ">
          <div class="row">
            <label class="col-sm-2 col-form-label">Gestion</label>
            <div class="col-sm-7">
              <div class="form-group">
                <select name="gestion" id="gestion" class="selectpicker " data-style="btn btn-info"
                    required>
                    <?php
                      $sql="SELECT * FROM gestiones order by 2 desc";
                      $stmtg = $dbh->prepare($sql);
                      $stmtg->execute();
                      while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
                        $codigog=$rowg['codigo'];
                        $nombreg=$rowg['nombre'];
                        if($codigog!=$gestionGlobal){
                          ?>
                      <option value="<?=$codigog;?>"><?=$nombreg;?></option>
                      <?php 
                        }     
                      }
                    ?>
                </select>
              </div>
            </div>
          </div><!--fin campo gestion -->
          <div class="row">
            <label class="col-sm-2 col-form-label">Oficina</label>
            <div class="col-sm-7">
              <div class="form-group">
                <select class="selectpicker form-control form-control-sm" name="unidad[]" id="unidad" data-style="select-with-transition" multiple data-actions-box="true" required>
             
                                  <?php
                                  $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 3");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                  ?>
                               <option value="<?=$codigoX;?>"><?=$nombreX?></option>  
                                 <?php
                                   }
                                   ?>
                               </select>
              </div>
            </div>
          </div><!--fin campo gestion -->

          <!--<div class="row">
            <label class="col-sm-2 col-form-label">Area</label>
            <div class="col-sm-7">
            <div class="form-group">
                <select class="selectpicker form-control form-control-sm" name="area_costo[]" id="area_costo" data-style="select-with-transition" multiple data-actions-box="true" required>
                               <?php
                               $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
                             $stmt->execute();
                             while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                              $codigoX=$row['codigo'];
                              $nombreX=$row['nombre'];
                              $abrevX=$row['abreviatura'];
                             ?>
                             <option value="<?=$codigoX;?>"><?=$abrevX;?></option>  
                               <?php
                                 }
                                 ?>
                             </select>
            </div>
            </div>
          </div>--><!--fin campo mes -->

          <!--  fin de seleccion unidad organizacional-->

          <div class="row">
              <label class="col-sm-2 col-form-label">A Fecha:</label>
              <div class="col-sm-7">
                <div class="form-group">
                    <input type="text" name="fecha" id="fecha" class="form-control datepicker">
                </div>
              </div>
          </div><!--fin campo RUBRO -->

			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Generar</button>
			    </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>