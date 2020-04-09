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
				  <h4 class="card-title">Reporte Estado De Cuentas</h4>
				</div>
			  </div>
			  <div class="card-body ">
          <div class="row">
            <label class="col-sm-2 col-form-label">Entidad</label>
            <div class="col-sm-7">
              <div class="form-group">                            
                        <!-- <select class="selectpicker form-control form-control-sm" name="entidad" id="entidad" data-style="<?=$comboColor;?>" required onChange="ajax_entidad_Oficina(this)"> -->
                          
                        <select class="selectpicker form-control form-control-sm" name="entidad[]" id="entidad" required onChange="ajax_entidad_Oficina()" multiple data-actions-box="true" data-style="select-with-transition" data-actions-box="true">             
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
            <label class="col-sm-2 col-form-label">Tipo</label>
            <div class="col-sm-7">
            <div class="form-group">
                <select name="tipo_cp[]" id="tipo_cp" class="selectpicker form-control"  data-style="select-with-transition" data-size="5"  data-actions-box="true" multiple required onChange="ajax_tipo_filtro_reporte_prove_cliente()">
                  
                      <option value="1">PROVEEDOR</option>
                      <option value="2">CLIENTE</option>
                      
                </select>
            </div>
            </div>
          </div><!--fin tipo tipo -->

          <div class="row">
            <label class="col-sm-2 col-form-label">Cuenta</label>
            <div class="col-sm-7">
            <div class="form-group">
              <div id="div_contenedor_cuenta">
                  
            </div>
              </div>
                
            </div>
          </div><!--fin campo cuenta -->

          <div class="row">
            <label class="col-sm-2 col-form-label">Proveedores/Cliente</label>
            <div class="col-sm-7">
              <div class="form-group">
                <div id="div_contenedorProv_cli">
                  
                </div>
                
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