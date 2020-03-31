<?php

// require_once 'layouts/bodylogin2.php';
require_once 'conexion.php';
require_once 'estados_cuenta/configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$dbh = new Conexion();
$query = "select * from depreciaciones";
$statement = $dbh->query($query);

$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde=$y."-01-01";
$fechaHasta=$y."-12-31";

?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$rptEstadoCuentasprocesar;?>" method="post" target="_blank">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Reporte Estados De Cuentas</h4>
				</div>
			  </div>
			  <div class="card-body ">
          <div class="row">
            <label class="col-sm-2 col-form-label">Entidad</label>
            <div class="col-sm-7">
              <div class="form-group">                            
                        <select class="selectpicker form-control form-control-sm" name="entidad" id="entidad" data-style="<?=$comboColor;?>" required onChange="ajax_entidad_Oficina(this)">               
                        <?php
                        $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM entidades where cod_estadoreferencial=1 order by 2");
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
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label">Oficina</label>
          <div class="col-sm-7">
            <div class="form-group">
              <div id="div_contenedor_oficina1">                              
              </div>
               </div>
          </div>
       </div>
          <div class="row">
            <label class="col-sm-2 col-form-label">Gestion</label>
            <div class="col-sm-7">
              <div class="form-group">
                <select name="gestion" id="gestion" class="selectpicker form-control form-control-sm" data-style="btn btn-info"
                    required onChange="AjaxGestionFechaDesdeBG(this)">
                    <?php
                      $sql="SELECT * FROM gestiones order by 2 desc";
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
          </div><!--fin campo gestion -->

          <div class="row">
            <label class="col-sm-2 col-form-label">Cuenta</label>
            <div class="col-sm-7">
            <div class="form-group">
                <select name="cuenta[]" id="cuenta" class="selectpicker form-control"  data-style="select-with-transition" data-size="5"  data-actions-box="true" multiple required>
        					<?php
                      $sql="SELECT p.codigo,p.nombre from configuracion_estadocuentas c,plan_cuentas p where c.cod_plancuenta=p.codigo";
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
          </div><!--fin campo mes -->

          <div class="row">
            <label class="col-sm-2 col-form-label">Proveedores/Cliente</label>
            <div class="col-sm-7">
              <div class="form-group">
                <select class="selectpicker form-control" data-show-subtext="true" data-live-search="true" title="Seleccione una opcion" name="proveedores[]" id="proveedores" data-style="select-with-transition" data-size="5"  data-actions-box="true" multiple required>
                  <?php
                    $sql="SELECT codigo,nombre from af_proveedores where cod_estado=1 order by nombre";
                    $stmt = $dbh->prepare($sql);
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
          <!--  fin de seleccion unidad organizacional-->

          <div class="row">
              <label class="col-sm-2 col-form-label">A Fecha:</label>
              <div class="col-sm-7">
                <div class="form-group">
                  <div id="div_contenedor_fechaH">                    
                    <!-- <input type="text" class="form-control datepicker " autocomplete="off" name="fecha_hasta" id="fecha_hasta" min="<?=$fechaDesde?>" max="<?=$fechaHasta?>" value="<?=$fechaHasta?>">   -->
                    <input type="date" name="fecha" id="fecha" class="form-control" min="<?=$fechaDesde?>" max="<?=$fechaHasta?>">
                  </div>                    
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