<?php
require_once 'conexion.php';
require_once 'functionsGeneral.php';
require_once 'functions.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$mes=$_SESSION["globalMes"];
$codGestionGlobal=$_SESSION["globalGestion"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$fechaActual=date("Y-m-d");
setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

$codSol=1;
// Preparamos
$lista=listaObligacionesPagoDetalleSolicitudRecursosSolicitud($codSol);

$totalPagadoX=obtenerSaldoPagoProveedorDetallePorSolicitudRecurso($codSol);

//Mostrar tipo bono
$stmtb = $dbh->prepare("SELECT s.fecha,s.cod_personal,u.nombre as unidad,a.nombre as area FROM solicitud_recursos s 
  join unidades_organizacionales u on s.cod_unidadorganizacional=u.codigo 
  join areas a on s.cod_area=a.codigo
  WHERE s.codigo=$codSol");
// Ejecutamos
$stmtb->execute();
// bindColumn
$stmtb->bindColumn('fecha', $fechaSolicitudX);
$stmtb->bindColumn('cod_personal', $codPersonalX);
$stmtb->bindColumn('unidad', $unidadX);
$stmtb->bindColumn('area', $areaX);

$codigoPago=obtenerCodigoPagoProveedorDetallePorSolicitudRecurso($codSol);

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
                <div class="card-header card-header-primary card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">attach_money</i>
                  </div>
                  <h4 class="card-title">Pagos por Proveedor - Lotes</h4>
                </div>
                <form id="form-pagos" action="<?=$urlSaveLote?>" method="post">
                <div class="card-body">
                  <div class="row">
                    <table class="table table-condensed table-warning">
                      <tr>
                        <td class="text-right font-weight-bold">Nombre Lote</td>
                        <td class="text-left" width="26%">
                        	<div class="form-group">
                               <input type="text" class="form-control" value="" name="nombre_lote" id="nombre_lote" required>
                             </div>
                        </td>
                        <td class="text-right font-weight-bold">Fecha del pago</td>
                        <td class="text-left">
                        	<div class="form-group">
                               <input type="text" class="form-control datepicker" name="fecha_pago" id="fecha_pago" value="<?=date('d/m/Y')?>">
                             </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-right font-weight-bold">Observaciones</td>
                        <td class="text-left" width="" colspan="3">
                        	<div class="form-group">
                               <textarea type="text" class="form-control" name="observaciones_pago" required id="observaciones_pago" value=""></textarea>
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
                      </tbody>
                    </table>
                  </div>

                   <input type="hidden" id="cantidad_proveedores" name="cantidad_proveedores"  value="0">
                  </div>
                </div>
              </div>
               <?php
              //if($globalAdmin==1){
              ?>
              <div class="card-footer fixed-bottom">
                <button type="submit" class="btn btn-white" style="background:#F7FF5A; color:#07B46D;"><i class="material-icons">attach_money</i> PAGAR</button>
                
              </div>
              
              </form>  
              <?php
             // }
              ?>
            </div>
          </div>  
        </div>
    </div>

<div class="fixed-plugin" style="background:rgba(227, 155, 3,1);"><!-- #21618C  -->
  <a href="#" title="Agregar Proveedores" onclick="cargarLotesPago()" class="text-white"><i class="material-icons" style="font-size:40px;">add</i></a>
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
                               <select class="selectpicker form-control form-control-sm"  data-live-search="true" name="proveedor" id="proveedor" data-style="btn btn-primary">
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


    <!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalRegistrarPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 80% !important;">
    <div class="modal-content card">
               <div class="card-header card-header-danger card-header-text">
                  <div class="card-text">
                    <h4>Registrar Pago</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <input type="hidden" readonly class="form-control" name="codigo_detalle" id="codigo_detalle" value="">
                <div class="card-body">
                  <div class="row">
                           <label class="col-sm-2 col-form-label">Saldo de la Solicitud</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="number" readonly class="form-control" name="saldo_pago" id="saldo_pago" value="<?=$saldoXInput?>">
                             </div>
                           </div>
                           <label class="col-sm-2 col-form-label">Proveedor</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                              <input type="text" readonly class="form-control" name="nombre_proveedor" id="nombre_proveedor" value="">
                              <input type="hidden" readonly class="form-control" name="proveedores_pago" id="proveedores_pago" value="">
                               <!--<select class="selectpicker form-control form-control-sm" data-live-search="true" name="proveedores_pago" id="proveedores_pago" data-style="btn btn-danger">
                                    <option disabled selected="selected" value="">--PROVEEDOR--</option>
                                    <?php 
                                     $stmt4 = $dbh->prepare("SELECT DISTINCT p.nombre as proveedor,sd.cod_proveedor 
                                              from solicitud_recursosdetalle sd
                                              join af_proveedores p on sd.cod_proveedor=p.codigo where sd.cod_solicitudrecurso=$codSol");
                                     $stmt4->execute();
                                     while ($row2 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoSel2=$row2['cod_proveedor'];
                                      $proveedorSel2=$row2['proveedor'];
                                      ?><option value="<?=$codigoSel2;?>"><?=$proveedorSel2?></option><?php 
                                     }
                                    ?>
                                  </select>-->
                             </div>
                           </div>
                      </div>
                      <div class="row">
                          <label class="col-sm-2 col-form-label">Tipo de Pago</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">

                               <select class="selectpicker form-control form-control-sm" onchange="mostrarDatosCheque()" data-live-search="true" name="tipo_pago" id="tipo_pago" data-style="btn btn-danger">
                                    <option disabled selected="selected" value="">--TIPO--</option>
                                    <?php 
                                     $stmt3 = $dbh->prepare("SELECT * from tipos_pagoproveedor where cod_estadoreferencial=1");
                                     $stmt3->execute();
                                     while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoSel=$rowSel['codigo'];
                                      $nombreSelX=$rowSel['nombre'];
                                      $abrevSelX=$rowSel['abreviaruta'];
                                      ?><option value="<?=$codigoSel;?>"><?=$nombreSelX?></option><?php 
                                     }
                                    ?>
                                  </select>
                             </div>
                           </div>  
                      <div class="d-none col-sm-6" id="div_cheques">
                        <div class="row">
                          <label class="col-sm-4 col-form-label">Bancos</label>
                           <div class="col-sm-8">                     
                             <div class="form-group">
                               <select class="selectpicker form-control form-control-sm" onchange="cargarChequesPago()" data-live-search="true" name="banco_pago" id="banco_pago" data-style="btn btn-danger">
                                    <option disabled selected="selected" value="">--BANCOS--</option>
                                    <?php 
                                     $stmt3 = $dbh->prepare("SELECT * from bancos where cod_estadoreferencial=1");
                                     $stmt3->execute();
                                     while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoSel=$rowSel['codigo'];
                                      $nombreSelX=$rowSel['nombre'];
                                      $abrevSelX=$rowSel['abreviaruta'];
                                      ?><option value="<?=$codigoSel;?>"><?=$nombreSelX?></option><?php 
                                     }
                                    ?>
                                  </select>
                             </div>
                           </div>     
                        </div>
                      </div>
                      </div>
                    <div class="row" id="div_chequesemitidos">
                      
                    </div>  
                    <div class="row">
                          <label class="col-sm-2 col-form-label">Monto</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="number" step="0.01" class="form-control" value="0" onkeyup="mandarValorTitulo()" onkeydown="mandarValorTitulo()" onchange="mandarValorTitulo()" name="monto_pago" id="monto_pago">
                             </div>
                           </div>  
                           <label class="col-sm-2 col-form-label">Observaciones</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <textarea type="text" class="form-control" name="observaciones_pago" id="observaciones_pago"></textarea>
                             </div>
                           </div>
                      </div>
                      <hr>
                      <div style="background:#07B46D; color:#F7FF5A; border-radius:10px;">
                        <br><br>
                       <center>
                        <p class="">Monto a Pagar</p>
                        <h1 class="font-weight-bold" id="montoTitulo">0</h1>
                        <br>
                          <div class="form-group">
                              <a href="#" onclick="pagarSolicitudRecursos()" class="btn btn-white btn-lg" style="background:#F7FF5A; color:#07B46D;"><i class="material-icons">attach_money</i> PAGAR</a>
                          </div>
                         </center>
                       <br><br>
                      </div>
                          
                      
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

    <!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalHistorialPago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 80% !important;">
    <div class="modal-content card">
               <div class="card-header card-header-danger card-header-text">
                  <div class="card-text">
                    <h4>Historial de Pagos</h4>
                  </div>
                  <button type="button" class="btn btn-success btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                  <div>
                       <table class="table table-bordered">
                        <thead>
                         <tr style="background:#07B46D; color:#F7FF5A;">
                           <th class="text-left">PROVEEDOR</th>
                           <th class="text-left">NRO SOLICITUD</th>
                           <th class="text-left">OBSERVACIONES</th> 
                           <th class="text-right">MONTO</th>
                           <th class="text-right">FECHA</td>
                         </tr> 
                        </thead>
                        <tbody>
                         <?php 
                         $totalMonto=0;
                            $stmt5 = $dbh->prepare("SELECT s.numero,p.nombre as proveedor,sd.cod_proveedor,sd.* 
from pagos_proveedoresdetalle sd
join af_proveedores p on sd.cod_proveedor=p.codigo 
join solicitud_recursos s on s.codigo=sd.cod_solicitudrecursos
where sd.cod_solicitudrecursos=$codSol order by sd.fecha");
                            $stmt5->execute();
                            while ($row = $stmt5->fetch(PDO::FETCH_ASSOC)) {
                              $codigoDet=$row['cod_proveedor'];
                              $proveedorDet=$row['proveedor'];
                              $observacionesDet=$row['observaciones'];
                              $montoDet=$row['monto'];
                              $fechaDet=$row['fecha'];
                              $totalMonto+=$montoDet;
                              $numeroSol=$row['numero'];
                                      ?>
                          <tr>
                            <td class="text-left"><?=$proveedorDet?></td>
                            <td class="text-left"><?=$numeroSol?></td>
                            <td class="text-left"><?=$observacionesDet?></td>
                            
                            <td class="text-right font-weight-bold"><?=number_format($montoDet,2,".","")?></td>
                            <td class="text-right"><?=strftime('%d/%m/%Y',strtotime($fechaDet))?></td>
                          </tr>
                                      <?php 
                                     }
                         ?>
                         <tr style="background:#F7FF5A; color:#07B46D;">
                            <td class="text-left font-weight-bold" colspan="3">TOTAL</td>
                            <td class="text-right font-weight-bold"><?=number_format($totalMonto,2,".","")?></td>
                            <td class="text-right"></td>
                         </tr>
                         </tbody>
                       </table>
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->

