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

$fechaDesde2=$y."-01-01";
$fechaHasta2=$y."-12-31";

$fechaMesUltimoDia=$y."-".$m."-".$d;

$globalAdmin=$_SESSION["globalAdmin"];
$globalUnidad=$_SESSION["globalUnidad"];
?>

<div class="content">
	<div class="container-fluid">
    <div style="overflow-y: scroll;">
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
                       <option value="<?=$codigoX;?>" selected><?=$nombreX?></option>  
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
                  <?php
                  $sqlUO="SELECT uo.codigo, uo.nombre,uo.abreviatura from entidades_uo e, unidades_organizacionales uo where e.cod_uo=uo.codigo";
                  $stmt = $dbh->prepare($sqlUO);
                  $stmt->execute();
                  ?>
                    <select class="selectpicker form-control form-control-sm" name="unidad[]" id="unidad" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
                        <?php 
                          while ($row = $stmt->fetch()){ 
                      ?>
                             <option value="<?=$row["codigo"];?>" data-subtext="<?=$row["nombre"];?>" selected><?=$row["abreviatura"];?></option>
                  <?php 
                          } 
                  ?>
                    </select>
                </div>
              </div>
            </div>
         </div>
         <div class="row">
                    <div class="col-sm-6">
                      <div class="row">
                       <label class="col-sm-4 col-form-label">Centro de Costos - Oficina</label>
                       <div class="col-sm-8">
                        <div class="form-group">
                          <div id="div_contenedor_oficina_costo">
                              <?php
                      $sqlUO="SELECT uo.codigo, uo.nombre,uo.abreviatura from unidades_organizacionales uo order by 2";
                      $stmt = $dbh->prepare($sqlUO);
                      $stmt->execute();
                      ?>
                        <select class="selectpicker form-control form-control-sm" name="unidad_costo[]" id="unidad_costo" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
                            <?php 
                              while ($row = $stmt->fetch()){ 
                          ?>
                                 <option value="<?=$row["codigo"];?>" data-subtext="<?=$row["nombre"];?>" <?=($row["codigo"]==$globalUnidad)?"selected":""?> ><?=$row["abreviatura"];?></option>
                        <?php 
                            } 
                      ?>
                        </select>                           
                          </div>
                                <!-- <select class="selectpicker form-control form-control-sm" name="unidad_costo[]" id="unidad_costo" data-style="select-with-transition" multiple data-actions-box="true" required>
                                    <?php
                                    $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
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
                                 </select> -->
                            </div>
                        </div>
                   </div>
                     </div>
                    <div class="col-sm-6">
                      <div class="row">
                       <label class="col-sm-4 col-form-label">Centro de Costos - Area</label>
                       <div class="col-sm-8">
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
                             <option value="<?=$codigoX;?>" selected><?=$abrevX;?></option> 
                               <?php
                                 }
                                 ?>
                             </select>
                            </div>
                        </div>
                    </div>
              </div>
                  </div><!--div row-->
            <div class="row">
              <label class="col-sm-2 col-form-label">Gestion</label>
              <div class="col-sm-7">
                <div class="form-group">
                  <select name="gestion" id="gestion" class="selectpicker form-control form-control-sm" data-style="btn btn-info"
                      required onChange="AjaxGestionFechaDesde(this)">
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
                  <select name="tipo_cp" id="tipo_cp" class="selectpicker form-control form-control-sm" data-style="btn btn-info" required onChange="ajax_tipo_filtro_reporte_prove_cliente()">
                        <option value=""></option>
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

            <!--<div class="row">
                <label class="col-sm-2 col-form-label">A Fecha:</label>
                <div class="col-sm-7">
                  <div class="form-group">
                    <div id="div_contenedor_fechaH">                    
                
                      <input type="date" name="fecha" id="fecha" class="form-control" min="<?=$fechaDesde?>" max="<?=$fechaHasta?>" value="<?=$fechaMesUltimoDia;?>">
                    </div>                    
                  </div>
                </div>
            </div>--><!--fin campo RUBRO -->

            <div class="row">
                      <div class="col-sm-6">
                        <div class="row">
                         <label class="col-sm-4 col-form-label">Desde</label>
                         <div class="col-sm-8">
                          <div class="form-group">
                            <div id="div_contenedor_fechaI">                              
                              <input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde" min="<?=$fechaDesde2?>" max="<?=$fechaHasta2?>" value="<?=$fechaDesde?>">  
                            </div>                                    
                             </div>
                          </div>
                     </div>
                       </div>
                      <div class="col-sm-6">
                        <div class="row">
                         <label class="col-sm-4 col-form-label">Hasta</label>
                         <div class="col-sm-8">
                          <div class="form-group">
                            <div id="div_contenedor_fechaH">                              
                              <input type="date" class="form-control" autocomplete="off" name="fecha_hasta" id="fecha_hasta" min="<?=$fechaDesde2?>" max="<?=$fechaHasta2?>" value="<?=$fechaHasta?>">
                            </div>
                                   
                            </div>
                          </div>
                      </div>
                </div>
                  </div><!--div row-->

            <div class="row">
              <label class="col-sm-2 col-form-label">Filtrar</label>
              <div class="col-sm-4">
              <div class="form-group">
                  <select name="ver_saldo" id="ver_saldo" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required>
                        <option value="1">SOLO SALDOS DETALLADO</option>
                        <option value="3">SOLO SALDOS GLOBALES</option>
                        <option value="2">TODO</option>
                        
                  </select>
              </div>
              </div>
              
            </div><!--fin tipo tipo -->
            <div class="row">
              <div class="col-sm-12">
                      <div class="row">
                     <label class="col-sm-8 col-form-label">Incluir aperturas de estados de cuenta ANTERIORES al periodo</label>
                           <div class="col-sm-4">
                        <div class="form-group">
                                <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" id="cierre_anterior" name="cierre_anterior[]" checked value="1">
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>  
                             </div>     
                        </div>  
                     </div>
              <div class="col-sm-12">
                      <div class="row">
                     <label class="col-sm-8 col-form-label">Incluir cierres de estados de cuenta POSTERIORES al periodo</label>
                           <div class="col-sm-4">
                        <div class="form-group">
                                <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" id="cierre_posterior" name="cierre_posterior[]" checked value="1">
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>  
                             </div>     
                        </div>  
                     </div>
            </div><!--fin tipo tipo -->
          </div>
          <div class="card-footer ml-auto mr-auto">
          <button type="submit" class="<?=$buttonNormal;?>">Generar</button>
              <!--<a href="index.php?opcion=reporteAdminEstadoCuentas" target="_blank" class="btn btn-danger">Reporte Administrador</a>-->
            </div>
        </div>
        </form>
      </div>  
    </div>
	</div>
</div>