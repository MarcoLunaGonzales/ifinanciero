<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';
require_once '../styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$mes=$_SESSION["globalMes"];
$codGestionGlobal=$_SESSION["globalGestion"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$fechaActual=date("Y-m-d");
setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

$codPago=$_GET['cod'];
$codigo=0;
$stmtPago = $dbh->prepare("SELECT pd.cod_proveedor,p.observaciones,p.fecha FROM pagos_proveedores p join pagos_proveedoresdetalle pd on pd.cod_pagoproveedor=p.codigo where p.codigo=$codPago limit 1");
                                     $stmtPago->execute();
while ($row = $stmtPago->fetch(PDO::FETCH_ASSOC)) {
     $codigo=$row['cod_proveedor'];
     $obsPago=$row['observaciones'];
     $fechaPago=strftime('%d/%m/%Y',strtotime($row['fecha']));
}

?>
<input type="hidden" id="cod_solicitud" value="<?=$codSol?>">
<input type="hidden" id="cod_pagoproveedor" value="<?=$codigoPago?>">

<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">edit</i>
                  </div>
                  <h4 class="card-title">Editar Pagos por Proveedor</h4>
                </div>
                <form id="form-pagos" action="<?='../'.$urlSaveEdit?>" method="post">
                  <input type="hidden" value="<?=$codPago?>" name="cod_pagoproveedoredit" id="cod_pagoproveedoredit">
                <div class="card-body">
                  <div class="row">
                    <table class="table table-condensed table-warning">
                      <tr>
                        <td class="text-right font-weight-bold">Proveedor</td>
                        <td class="text-left" width="26%">
                        	<div class="form-group">

                               <select class="selectpicker form-control form-control-sm" onchange="cargarDatosProveedorPagos()" data-live-search="true" name="proveedor" id="proveedor" data-style="btn btn-danger">
                                    <option disabled selected="selected" value="">--PROVEEDOR--</option>
                                    <?php 
                                     $stmt3 = $dbh->prepare("SELECT DISTINCT p.codigo,p.nombre FROM solicitud_recursosdetalle s join af_proveedores p on s.cod_proveedor=p.codigo 
                                      where p.codigo=$codigo order by p.nombre");
                                     $stmt3->execute();
                                     while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoSel=$rowSel['codigo'];
                                      $nombreSelX=$rowSel['nombre'];
                                      ?><option value="<?=$codigoSel;?>####<?=$nombreSelX?>" selected><?=$nombreSelX?></option><?php 
                                     }
                                    ?>
                                  </select>
                             </div>
                        </td>
                        <td class="text-right font-weight-bold">Fecha del pago</td>
                        <td class="text-left">
                        	<div class="form-group">
                               <input type="text" class="form-control datepicker" name="fecha_pago" id="fecha_pago" value="<?=$fechaPago?>">
                             </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-right font-weight-bold">Observaciones</td>
                        <td class="text-left" width="" colspan="3">
                        	<div class="form-group">
                               <textarea type="text" class="form-control" name="observaciones_pago" id="observaciones_pago"><?=$obsPago?></textarea>
                             </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                  <div class="row col-sm-12" id="data_pagosproveedores">
                  	 <?php
                      include "detallePagos.php"
                      ?>  
                  </div>
                </div>
              </div>
               <?php
              //if($globalAdmin==1){
              ?>
              <div class="card-footer fixed-bottom">
                <button type="submit" class="btn btn-success">GUARDAR</button> 
                <a href="<?="../".$urlListPago?>" class="btn btn-danger">VOLVER</a> 
              </div>
              
              </form>  
              <?php
             // }
              ?>
            </div>
          </div>  
        </div>
    </div>

