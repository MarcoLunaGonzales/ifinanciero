<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();


$valor_forma_pago=obtenerValorConfiguracion(76);
$valor_razon_social=obtenerValorConfiguracion(77);
$valor_validacion=obtenerValorConfiguracion(90);
$montoLimiteCajaChica=obtenerValorConfiguracion(85);

if($valor_forma_pago==1)
  $sw_sf_fp="checked";
else $sw_sf_fp="";

if($valor_razon_social==1)
  $sw_f_rs="checked";
else $sw_f_rs="";

if($valor_validacion==1)
  $sw_v_lc="checked";
else $sw_v_lc="";
?>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="utilitarios/configuracion_edit_sf_save.php" method="post">
              <div class="card">
                <div class="card-header card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">assignment</i>
                  </div>
                  <h4 class="card-title">Configuraciones</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed  table-sm">
                      <thead class="fondo-boton">
                        <tr>
                          <th align="center">Configuración</th>
                          <th align="center">H/D</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="text-left">Edición de Forma de Pago en SF</td>
                          <td>
                            <div class="togglebutton">
                               <label>
                                 <input type="checkbox"  name="modal_check_sf" id="modal_check_sf" <?=$sw_sf_fp?> >
                                 <span class="toggle"></span>
                               </label>
                           </div>
                          </td>                          
                        </tr>
                        <tr>
                          <td class="text-left">Edición de Razón Social en Facturas</td>
                          <td>
                            <div class="togglebutton">
                               <label>
                                 <input type="checkbox"  id="modal_check_f" name="modal_check_f" <?=$sw_f_rs?> >
                                 <span class="toggle"></span>
                               </label>
                           </div>
                          </td>
                        </tr>
                        <tr>
                          <td class="text-left">Validación de Libretas Bancarias en Comprobantes</td>
                          <td>
                            <div class="togglebutton">
                               <label>
                                 <input type="checkbox"  id="modal_check_lb" name="modal_check_lb" <?=$sw_v_lc?> >
                                 <span class="toggle"></span>
                               </label>
                           </div>
                          </td>
                        </tr>

                        <tr>
                          <td class="text-left">Monto Limite para contabilizar por Caja Chica</td>
                          <td>
                            <div class="togglebutton">
                               <label>
                                 <input type="text" id="txt_montocajachica" name="txt_montocajachica" value="<?=$montoLimiteCajaChica;?>" class="form-control">
                               </label>
                           </div>
                          </td>
                        </tr>

                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="card-body">
                    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
              </div>
               </form>
            </div>
          </div>  
        </div>
    </div>
