<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$query = "SELECT * from depreciaciones order by 3";
$statement = $dbh->query($query);
?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="activosfijos/afPrintActivosFijosAsignados.php" method="post" target="_blank">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
  				<div class="card-text">
  				  <h4 class="card-title">Reporte De Activos Fijos Asignados</h4>
  				</div>
			  </div>
			  <div class="card-body ">

          <div class="row">
            <label class="col-sm-2 col-form-label">Estado Asignación</label>
            <div class="col-sm-7">
              <div class="form-group">
                <select class="selectpicker form-control" title="Seleccione una opcion" 
                name="estado_asignacion_af[]" id="estado_asignacion_af" 
                data-style="select-with-transition" data-size="5" 
                data-actions-box="true" multiple required>
                    <option value="1">Asignado</option>
                    <option value="2">Recepcionado</option>
                    <option value="3">Asignación Rechazada</option>
                    <option value="5">Devuelto</option>
                    <option value="4">Historico</option>
                    
                  ?>
                </select>
              </div>
            </div>
          </div>
          <!--  fin de seleccion -->
          <div class="row">
            <label class="col-sm-2 col-form-label">Oficina</label>
            <div class="col-sm-7">
              <div class="form-group">
                <select class="selectpicker form-control" title="Seleccione una opcion" 
                name="unidad_organizacional[]" id="unidad_organizacional" 
                data-style="select-with-transition" data-size="5" 
                data-actions-box="true" multiple required data-show-subtext="true" data-live-search="true"> 
                  <?php
                    $sql="SELECT * FROM unidades_organizacionales where cod_estado=1 order by 2";
                    $stmt = $dbh->prepare($sql);
                    $stmt->execute();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      $codigoX=$row['codigo'];
                      $nombreX=$row['nombre'];
                    ?>
                    <option value="<?=$codigoX;?>"><?=$row['abreviatura'];?> - <?=$nombreX;?></option>
                    <?php 
                    }
                  ?>
                </select>
              </div>
            </div>
          </div>
          <!--  fin de seleccion unidad organizacional-->
          <div class="row">
            <label class="col-sm-2 col-form-label">Area</label>
            <div class="col-sm-7">
            <div class="form-group">
              <select class="selectpicker form-control" title="Seleccione una opcion" name="areas[]" id="areas" data-style="select-with-transition" data-size="5" data-actions-box="true" multiple required data-show-subtext="true" data-live-search="true">
                <?php
                $stmt = $dbh->prepare("SELECT * FROM areas where cod_estado=1 order by 2");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $codigoX=$row['codigo'];
                $nombreX=$row['nombre'];
              ?>
              <option value="<?=$codigoX;?>"><?=$row['abreviatura'];?> - <?=$nombreX;?></option>
              <?php 
              }
                ?>
              </select>
            </div>
            </div>
          </div>
  
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				  <button type="submit" class="<?=$buttonNormal;?>">Generar</button>
        </div>
			</div>
		  </form>
		</div>
	</div>
</div>