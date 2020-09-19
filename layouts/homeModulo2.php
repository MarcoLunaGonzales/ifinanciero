<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';

$nombreModulo="RRHH";
$cardTema="card-themes";
$iconoTitulo="local_atm";
$estiloHome="#DC5143";
$fondoModulo="fondo-dashboard-recursoshumanos";

?>
<input type="hidden" id="modulo" value="<?=$codModulo?>">
  <div class="container">
    <div class="div-center">
      <!--inicio dashboard-->
      <div class="content">
            <div class="row">
              <div class="col-md-12">
                <div class="card" style="background-color: rgba(255, 0, 0, 0) !important;">                  
                  <div class="card <?=$fondoModulo?>"></div>
                  <div class="card-header card-header-text">
                    <div class="card-text">
                      <h4 class="card-title"><b>REPORTES INGRESOS - EGRESOS</b></h4>
                    </div>                   
                  </div>
                  <div class="card-body">
                    <div class="row" style="background-color: rgba(255, 255, 255, 0.6) !important;">
                          <div class="col-md-4">
                            <div class="card card-chart text-center">
                              <div class="card-header card-header-rose" data-header-animation="false">
                                <h4>INGRESOS</h4>
                              </div>
                              <div class="card-body">
                                <div class="card-actions">
                                  
                                </div>
                                <a class="btn btn-warning text-white" href="homeIngresos.php" target="_blank">VER REPORTE</a>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="card card-chart text-center">
                              <div class="card-header card-header-rose" data-header-animation="false">
                                <h4>EGRESOS</h4>
                              </div>
                              <div class="card-body">
                                <div class="card-actions">
                                  
                                </div>
                                <a class="btn btn-warning text-white" href="homeEgresos.php" target="_blank">VER REPORTE</a>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="card card-chart text-center">
                              <div class="card-header card-header-rose" data-header-animation="false">
                                <h4>RESULTADOS</h4>
                              </div>
                              <div class="card-body">
                                <div class="card-actions">
                                  
                                </div>
                                <a class="btn btn-warning text-white" href="homeResultados.php" target="_blank">VER REPORTE</a>
                              </div>
                            </div>
                          </div>
                      
                    </div>

                    <br>
                  </div>
                </div>
              </div>
            </div>
      </div>
      <!--fin dashboard-->            
      
    </div>
 </div>