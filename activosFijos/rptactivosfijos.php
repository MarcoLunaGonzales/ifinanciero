<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$query = "SELECT * from depreciaciones order by 3";
$statement = $dbh->query($query);
$fechaDesde=date('Y-01-01');
$fechaHasta=date('Y-12-31');
?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="activosFijos/afPrintActivosFijos.php" method="post" target="_blank">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
  				<div class="card-text">
  				  <h4 class="card-title">Reporte De Activos Fijos</h4>
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
                    $sql="SELECT * FROM unidades_organizacionales order by 2";
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
                      <option value="<?=$row['codigo'];?>"><?=$row['abreviatura'];?> - <?=$row["nombre"];?></option>
                  <?php } ?> 
                  </select>
              </div>
              </div>
          </div>
          <!--fin campo ufvinicio -->
          <div class="row">
            <div class="col-sm-6">
              <div class="row">
               <label class="col-sm-4 col-form-label">Fecha Alta I.</label>
               <div class="col-sm-4">
                <div class="form-group">
                  <div id="div_contenedor_fechaI">                              
                    <input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde" value="<?=$fechaDesde?>">  
                  </div>                                    
                   </div>
                </div>
           </div>
             </div>
            <div class="col-sm-4">
              <div class="row">
               <label class="col-sm-4 col-form-label">Fecha Alta F.</label>
               <div class="col-sm-8">
                <div class="form-group">
                  <div id="div_contenedor_fechaH">                              
                    <input type="date" class="form-control" autocomplete="off" name="fecha_hasta" id="fecha_hasta" value="<?=$fechaHasta?>">
                  </div>
                         
                  </div>
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