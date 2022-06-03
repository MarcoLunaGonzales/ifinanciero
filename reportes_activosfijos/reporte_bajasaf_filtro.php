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
		  <form id="form1" class="form-horizontal" action="reportes_activosfijos/reporte_bajasaf_print.php" method="post" target="_blank">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
  				<div class="card-text">
  				  <h4 class="card-title">Reporte De Activos Fijos Altas y Bajas</h4>
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
                data-actions-box="true" multiple required data-show-subtext="true" data-live-search="true" onChange="ajaxRPTAF_oficina();">
                  <?php
                    $sql="SELECT codigo,nombre,abreviatura FROM unidades_organizacionales where cod_estado=1 order by nombre";
                    $stmt = $dbh->prepare($sql);
                    $stmt->execute();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                      $codigoX=$row['codigo'];
                      $nombreX=$row['nombre'];
                    ?>
                    <option value="<?=$codigoX;?>" ><?=$row['abreviatura'];?> - <?=$nombreX;?></option>
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
              <div id="contenedor_areas_reporte">
                
              </div>
              
            </div>
            </div>
          </div>
          <!--  fin de seleccion area-->
          <div class="row">
              <label class="col-sm-2 col-form-label">Rubro</label>
              <div class="col-sm-7">
              <div class="form-group">
                  <select class="selectpicker form-control" title="Seleccione una opcion" name="rubros[]" id="rubros" data-style="select-with-transition" data-size="5" data-actions-box="true" multiple required data-show-subtext="true" data-live-search="true">
                  <?php while ($row = $statement->fetch()){ ?>
                      <option value="<?=$row["codigo"];?>"><?=$row['abreviatura'];?> - <?=$row["nombre"];?></option>
                  <?php } ?> 
                  </select>
              </div>
              </div>
          </div>
          <div class="row">
              <label class="col-sm-2 col-form-label">Tipo</label>
              <div class="col-sm-2">
              <div class="form-group">
                  <select name="tipo" id="tipo" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required="true">
                  <?php
                    $sql="SELECT codigo,nombre,abreviatura from tipos_activos_fijos where cod_estadoreferencial=1 order by nombre";
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

            <label class="col-sm-1 col-form-label">Gestión</label>
            <div class="col-sm-1">
              <div class="form-group">
                <select name="gestion" id="gestion" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required>
                    <?php
                      $sql="SELECT codigo,nombre FROM gestiones order by 2 desc";
                      $stmtg = $dbh->prepare($sql);
                      $stmtg->execute();
                      while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
                        $codigog=$rowg['codigo'];
                        $nombreg=$rowg['nombre'];
                      ?>
                      <option value="<?=$codigog;?>"><?=$nombreg;?></option>
                      <?php 
                      }
                    ?>
                </select>
              </div>
            </div>

            <label class="col-sm-1 col-form-label">Mes</label>
            <div class="col-sm-2">
              <div class="form-group">
                <select name="mes" id="mes" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required>
                    <?php
                      $sql="SELECT codigo,nombre from meses order by codigo";
                      $stmtg = $dbh->prepare($sql);
                      $stmtg->execute();
                      while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {
                        $codigog=$rowg['codigo'];
                        $nombreg=$rowg['nombre'];
                      ?>
                      <option value="<?=$codigog;?>"><?=$nombreg;?></option>
                      <?php 
                      }
                    ?>
                </select>
              </div>
            </div>

            <label class="col-sm-1 col-form-label">Altas/Bajas</label>
            <div class="col-sm-1">
              <div class="form-group">
                <select name="alta_baja" id="alta_baja" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required>
                  <option value="1">ALTAS</option>
                  <option value="2">BAJAS</option>
                </select>
              </div>
            </div>


          </div>
          <!--fin campo ufvinicio -->
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				  <button type="submit" class="<?=$buttonNormal;?>">Generar</button>
        </div>
			</div>
		  </form>
		</div>
	</div>
</div>