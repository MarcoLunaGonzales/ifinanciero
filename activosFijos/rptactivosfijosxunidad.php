<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="activosfijos/afPrintActivosFijosxunidad.php" method="post" target="_blank">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
  				<div class="card-text">
  				  <h4 class="card-title">Activos Fijos Por Oficina, Area y Responsable</h4>
  				</div>
			  </div>
			  <div class="card-body ">

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
          <!--  fin de seleccion area-->

          <div class="row">
            <label class="col-sm-2 col-form-label">Personal</label>
            <div class="col-sm-7">
            <div class="form-group">
              <select class="selectpicker form-control" title="Seleccione una opcion" name="personal[]" id="personal" data-style="select-with-transition" data-size="5" data-actions-box="true" multiple required data-show-subtext="true" data-live-search="true">
                <?php
                $stmt = $dbh->prepare("SELECT codigo,(CONCAT_WS(' ',paterno,materno,primer_nombre)) as nombre_personal FROM personal where cod_estadoreferencial=1 order by nombre_personal");
              $stmt->execute();
              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $codigoX=$row['codigo'];
                $nombreX=$row['nombre_personal'];
              ?>
              <option value="<?=$codigoX;?>"><?=$nombreX;?></option>
              <?php 
              }
                ?>
              </select>
            </div>
            </div>
          </div>
          <!--  fin de seleccion personal-->

			  </div>
			  <div class="card-footer ml-auto mr-auto">
				  <button type="submit" class="<?=$buttonNormal;?>">Generar</button>
        </div>
			</div>
		  </form>
		</div>
	</div>
</div>