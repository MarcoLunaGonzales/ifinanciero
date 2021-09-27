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

$codPagoLote=$_GET['cod'];
$codigo=0;
$codigosProv=[];
$indexProv=0;
$stmtPago = $dbh->prepare("SELECT DISTINCT p.observaciones,pd.cod_proveedor,pl.nombre,pl.fecha FROM pagos_lotes pl join pagos_proveedores p on pl.codigo=p.cod_pagolote join pagos_proveedoresdetalle pd on pd.cod_pagoproveedor=p.codigo where pl.codigo=$codPagoLote");
$stmtPago->execute();
while ($row = $stmtPago->fetch(PDO::FETCH_ASSOC)) {
     $codigosProv[$indexProv]=$row['cod_proveedor'];
     $obsPago=$row['nombre'];
     $observacionesPago=$row['observaciones'];
     $fechaPago=strftime('%d/%m/%Y',strtotime($row['fecha']));
     $indexProv++;
}
$codigo=implode(",", $codigosProv);
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
                  <h4 class="card-title">Editar Lotes Pagos por Proveedor</h4>
                </div>
                <form id="form-pagos" action="<?='../'.$urlSaveEditLote?>" method="post">
                  <input type="hidden" value="<?=$codPagoLote?>" name="cod_pagoloteedit" id="cod_pagoloteedit">
                <div class="card-body">
                  <div class="row">
                    <table class="table table-condensed table-warning">
                      <tr>
                        <td class="text-right font-weight-bold">Nombre Lote</td>
                        <td class="text-left" width="26%">
                        	<div class="form-group">
                               <input type="text" class="form-control" value="<?=$obsPago?>" name="nombre_lote" id="nombre_lote" required>
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
                               <textarea type="text" class="form-control" name="observaciones_pago" id="observaciones_pago"><?=$observacionesPago?></textarea>
                             </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                  <div class="row col-sm-12">
                     <div class="col-sm-12">
                    <table id="" class="table table-condensed small">
                      <thead>
                        <tr>
                          <th width="20%">Proveedor</th>
                          <th width="20%">Detalle</th>
                          <th>F. Sol</th>     
                          <th>Nº Sol</th>
                          <th>Nº Comp</th>
                          <th>Oficina</th>
                          <th class="bg-warning text-dark">Importe</th>
                          <th class="" style="background:#07B46D; color:#F7FF5A;">Pagado</th>
                          <th>Saldo</th>
                          <th width="10%">Monto</th>
                          <th width="10%">Fecha Pago</th>
                          <th width="10%">Tipo</th>
                          <th width="10%">Bancos</th>
                          <th width="10%">Cheques</th>
                          <th width="10%">Nº Cheque</th>
                          <th width="10%">Beneficiario</th>
                        </tr>
                      </thead>
                      <tbody id="data_pagosproveedores">
                         <?php
                         $cantidadProveedores=0;
                         $stmtPago = $dbh->prepare("SELECT DISTINCT p.codigo as codigopagoprov, pd.cod_proveedor,pl.nombre,pl.fecha FROM pagos_lotes pl join pagos_proveedores p on pl.codigo=p.cod_pagolote join pagos_proveedoresdetalle pd on pd.cod_pagoproveedor=p.codigo where pl.codigo=$codPagoLote");
                         $stmtPago->execute();
                         while ($rowProve = $stmtPago->fetch(PDO::FETCH_ASSOC)) {
                             $codigo=$rowProve['cod_proveedor'];
                             $proveedorNombre=nameProveedor($codigo);
                            $cantidadProveedores++;
                            ?><input type="hidden" id="codPagoProveedor<?=$cantidadProveedores?>" name="codPagoProveedor<?=$cantidadProveedores?>"  value="<?=$rowProve["codigopagoprov"]?>"><?php
                            include "detallePagosLotes.php";

                     } 
                      ?> 
                      </tbody>
                    </table>
                  </div>
                  	 <input type="hidden" id="cantidad_proveedores" name="cantidad_proveedores"  value="<?=$cantidadProveedores?>">
                  </div>
                </div>
              </div>
               <?php
              //if($globalAdmin==1){
              ?>
              <div class="card-footer fixed-bottom">
                <button type="submit" class="btn btn-success">GUARDAR</button> 
                <a href="<?="../".$urlListPagoLotes?>" class="btn btn-danger">VOLVER</a> 
              </div>
              
              </form>  
              <?php
             // }
              ?>
            </div>
          </div>  
        </div>
    </div>


<div class="fixed-plugin" style="background:rgba(33, 97, 140,0.6);"><!-- #21618C  -->
  <a href="#" onclick="cargarLotesPago()" class="text-white"><i class="material-icons" style="font-size:40px;">view_comfy</i></a>
</div>

<!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalLotesPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 80% !important;">
    <div class="modal-content card">
               <div class="card-header card-header-info card-header-text">
                  <div class="card-text">
                    <h4>Lotes Pago - Proveedores</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                  <div>
                    <div class="row">
                       <label class="col-sm-2 col-form-label">Proveedor</label>
                         <div class="col-sm-6">
                             <div class="form-group">
                               <select class="selectpicker form-control form-control-sm"  data-size="6" data-live-search="true" name="proveedor" id="proveedor" data-style="btn btn-primary">
                                    <option selected="selected" value="####">--PROVEEDOR--</option>
                                    <?php 
                                     $stmt3 = $dbh->prepare("SELECT DISTINCT p.codigo,p.nombre FROM solicitud_recursosdetalle s join af_proveedores p on s.cod_proveedor=p.codigo order by p.nombre");
                                     $stmt3->execute();
                                     while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoSel=$rowSel['codigo'];
                                      $nombreSelX=$rowSel['nombre'];
                                      ?><option value="<?=$codigoSel;?>####<?=$nombreSelX?>"><?=$nombreSelX?></option><?php 
                                     }
                                    ?>
                                  </select>
                                </div>
                          </div> 
                          <!--select onchange="cargarDatosProveedorPagos()" -->
                          <div class="col-sm-1">
                            <a href="#" onclick="agregarLotePago()" class="btn btn-white btn-sm" style="background:#F7FF5A; color:#07B46D;"><i class="material-icons">add</i> Agregar</a>
                          </div>    
                    </div>
                    <br>

                       <table class="table table-bordered table-condensed small">
                        <thead>
                         <tr style="background:#21618C; color:#fff;">
                           <td class="text-left">PROVEEDOR</td>
                           <td width="8%" class="text-right">ACTIONS</td>
                         </tr> 
                        </thead>
                        <tbody id="tabla_proveedor">
                         
                         </tbody>
                       </table>
                       
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->