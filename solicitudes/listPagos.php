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

$codSol=$_GET['codigo'];
// Preparamos
$lista=listaObligacionesPagoDetalleSolicitudRecursosSolicitud($codSol);

$totalPagadoX=obtenerSaldoPagoProveedorDetallePorSolicitudRecurso($codSol);

//Mostrar tipo bono
$stmtb = $dbh->prepare("SELECT s.*,u.nombre as unidad,a.nombre as area FROM solicitud_recursos s 
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
$stmtb->bindColumn('cod_simulacion', $codSimulacion);
$stmtb->bindColumn('cod_simulacionservicio', $codSimulacionServicio);
$stmtb->bindColumn('numero', $numeroSol);

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
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons text-dark">attach_money</i>
                  </div>
                  <h4 class="card-title">Pagos de la Solicitud</h4>
                </div>
                 <form id="form-pagos" action="<?=$urlSavePago?>" method="post">
                <div class="card-body">

                  <div class="row">
                    <table class="table table-bordered table-condensed">
                      <?php
                       while ($row2 = $stmtb->fetch(PDO::FETCH_BOUND)) {
                        $solicitanteX=namePersonal($codPersonalX);
                        if($codSimulacion!=0){
                           $nombreCliente="Sin Cliente";
                           $nombreSimulacion=nameSimulacion($codSimulacion);
                          }else{
                           $nombreCliente=nameClienteSimulacionServicio($codSimulacionServicio);
                           $nombreSimulacion=nameSimulacionServicio($codSimulacionServicio);
                          }
                        ?>
                      <tr>
                        <td class="text-left font-weight-bold">Propuesta</td>
                        <td class="text-left"><?=$nombreSimulacion;?><input type="hidden" value="<?=$nombreSimulacion?>" name="nombre_solicitud" id="nombre_solicitud"></td>
                        <td class="text-left font-weight-bold">Cliente</td>
                        <td class="text-left"><?=$nombreCliente?></td>
                      </tr>  
                      <tr>
                        <td class="text-left font-weight-bold">Solicitante</td>
                        <td class="text-left"><img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitanteX;?></td>
                        <td class="text-left font-weight-bold">Fecha de Solicitud</td>
                        <td class="text-left"><?=strftime('%d/%m/%Y',strtotime($fechaSolicitudX));?></td>
                      </tr>
                      <tr>
                        <td class="text-left font-weight-bold">Unidad</td>
                        <td class="text-left"><?=$unidadX?></td>
                        <td class="text-left font-weight-bold">Area</td>
                        <td class="text-left"><?=$areaX?></td>
                      </tr>
                      <tr>
                        <td class="text-left font-weight-bold">Total Importe</td>
                        <td class="text-left" id="total_importe"></td>
                        <td class="text-left font-weight-bold">Total Pagado</td>
                        <td class="text-left"><?=number_format($totalPagadoX,2,".","")?></td>
                      </tr>
                      <tr>
                        <td class="text-left font-weight-bold">Total Saldo</td>
                        <td class="text-left" id="total_saldo"></td>
                        <td class="text-left font-weight-bold">N&uacute;mero de Solicitud</td>
                        <td class="text-left font-weight-bold"><?=$numeroSol?></td>
                      </tr>
                      <?php
                       }
                      ?>
                    </table>
                  </div>
                  <div class="row col-sm-10">
                    <table class="table table-condensed table-warning">
                      <tr>
                        <td class="text-right font-weight-bold">Fecha del pago</td>
                        <td class="text-left">
                          <div class="form-group">
                               <input type="text" class="form-control datepicker" name="fecha_pago" id="fecha_pago" value="<?=date('d/m/Y')?>">
                             </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-right font-weight-bold">Observaciones</td>
                        <td class="text-left" width="">
                          <div class="form-group">
                               <textarea type="text" class="form-control" name="observaciones_pago" id="observaciones_pago" value=""></textarea>
                             </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                  <div class="table-responsive" id="data_comprobantes">
                    <table id="" class="table table-condensed small">
                      <thead>
                        <tr>
                          <!--<th class="text-center">#</th>
                          <th>Unidad</th>
                          <th>Area</th>
                          <th>Nro Solicitud</th>-->
                          <th>Estado</th>
                          <th>Fecha</th>
                          <th>Detalle</th>
                          <th>Proveedor</th>
                          <th>Nº Sol</th>
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
                      <tbody>
                        <?php
                 $index=1;$cont=0;$totalImporte=0;

                        while ($row = $lista->fetch(PDO::FETCH_ASSOC)) {
                          $codDetalle=$row['codigo'];
                          $unidad=$row['unidad'];
                          $area=$row['area'];
                          $solicitante=namePersonal($row['cod_personal']);
                          $fecha=$row['fecha'];
                          $numero=$row['numero'];
                          $detalle=$row['detalle'];
                          $importe=$row['importe'];
                          $proveedor=$row['proveedor'];
                          $codProveedor=$row['cod_proveedor'];
                          $codSol=$row['cod_solicitudrecurso'];
                          $codSolDet=$codDetalle;

                          $dias=obtenerCantidadDiasCredito($codProveedor);
                          $pagadoFila=obtenerMontoPagadoDetalleSolicitud($codSol,$codDetalle);
                          if($dias==0){
                            $tituloDias="Sin Registro";
                          }else{
                            $tituloDias="".$dias;
                          }
                          $totalImporte+=$importe;
                          $saldoImporte=abs($pagadoFila-$importe);
                          $pagado=$importe-$saldoImporte;
                          
?>
                        <tr>
                          <!--<td align="center"><?=$index;?></td>                          
                          <td><?=$unidad;?></td>
                          <td><?=$area;?></td>
                          <td><?=$numero;?></td>-->
                          <td>
                            <input type="hidden" value="<?=$detalle?>" id="glosa_detalle<?=$index?>" name="glosa_detalle<?=$index?>">
                            <input type="hidden" value="<?=$codProveedor?>" id="codigo_proveedor<?=$index?>" name="codigo_proveedor<?=$index?>">
                            <input type="hidden" value="<?=$codSol?>" id="codigo_solicitud<?=$index?>" name="codigo_solicitud<?=$index?>">
                            <input type="hidden" value="<?=$codSolDet?>" id="codigo_solicitudDetalle<?=$index?>" name="codigo_solicitudDetalle<?=$index?>">
                            <?php 
                            if(($importe-$pagado)>0){
                             ?><img src="assets/img/progresa.jpg" alt="" width="80px" height="35px"><?php
                            }else{
                              ?><img src="assets/img/cancelado.png" alt="" width="80px" height="35px"><?php 
                            }?> 
                          </td>
                          <td class="text-left"><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td class="text-left"><?=$detalle;?></td>
                          <td class="text-left"><?=$proveedor;?></td>
                          <td class=""><?=$numero;?></td>
                          <td class="bg-warning text-dark text-right font-weight-bold"><?=number_format($importe,2,".","")?></td>
                          <td class="text-right font-weight-bold" style="background:#07B46D; color:#F7FF5A;"><?=number_format($pagado,2,".","")?></td>
                          <td class="text-right font-weight-bold"><?=number_format($importe-$pagado,2,".","")?></td>
                          <td class="text-right">
                            <?php 
                            if(($importe-$pagado)>0){
                              ?>
                              <input type="number" step="0.01" class="form-control text-right text-success" value="0" id="monto_pago<?=$index?>" name="monto_pago<?=$index?>">
                              
                              <?php
                            }else{
                              ?>
                              <input type="number" step="0.01" class="form-control text-right text-success" readonly value="0" id="monto_pago<?=$index?>" name="monto_pago<?=$index?>">
                              <?php
                            } 
                            ?>
                            
                          </td>
                          <td><input type="text" class="form-control datepicker" value="<?=date('d/m/Y')?>" id="fecha_pago<?=$index?>" name="fecha_pago<?=$index?>"></td>
                          <td>
                            <div class="form-group">
                               <select class="selectpicker form-control form-control-sm" onchange="mostrarDatosChequeDetalle(<?=$index?>)" data-live-search="true" name="tipo_pago<?=$index?>" id="tipo_pago<?=$index?>" data-style="btn btn-danger">
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
                          </td>
                          <td>
                            <div class="d-none" id="div_cheques<?=$index?>">                    
                                <div class="form-group">
                                     <select class="selectpicker form-control form-control-sm" onchange="cargarChequesPagoDetalle(<?=$index?>)" data-live-search="true" name="banco_pago<?=$index?>" id="banco_pago<?=$index?>" data-style="btn btn-danger">
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
                          </td>
                          <td>
                            <div id="div_chequesemitidos<?=$index?>">                    
                             </div>
                          </td>
                          <td>
                            <input type="number" readonly class="form-control text-right" readonly value="0" id="numero_cheque<?=$index?>" name="numero_cheque<?=$index?>">
                          </td>
                          <td>
                            <input type="text" readonly class="form-control" readonly value="" id="beneficiario<?=$index?>" name="beneficiario<?=$index?>">
                          </td>
                        </tr>
<?php
							$index++;
                      }

           $saldoX=$totalImporte-$totalPagadoX;
           $saldoXInput=number_format($saldoX,2,".","");           
?>
                    <script>
                       $("#total_importe").text(<?=number_format($totalImporte,2,".","")?>);
                       $("#total_saldo").text(<?=number_format($saldoX,2,".","")?>);
                    </script>
                    <script type="text/javascript">
        $(document).ready(function(e) {
           if(!($("body").hasClass("sidebar-mini"))){
             $("#minimizeSidebar").click()
           } 
        });
    </script>  
                      </tbody>
                    </table>
                  </div>
                  <input type="hidden" value="<?=$index-1?>" id="cantidad_filas" name="cantidad_filas">
                </div>
              </div>
               <?php
              if($globalAdmin==1){
              ?>
              <div class="card-footer fixed-bottom">
                <button class="<?=$buttonCancel;?>" onClick="location.href='<?=$urlList;?>'">Volver</button>
                <!--<a href="#" onclick="nuevoPagoSolicitudRecursos()" class="btn btn-warning" >Registrar Nuevo Pago</a>-->
                <a class="<?=$buttonNormal;?>" onclick="historialPagoSolicitudRecursos()"><i class="material-icons text-dark">history</i> Historial de Pagos</a>
                <button type="submit" class="btn btn-white" style="background:#F7FF5A; color:#07B46D;"><i class="material-icons">attach_money</i> PAGAR</button>
              </div>
              

              <?php
              }
              ?>
              </form>
            </div>
          </div>  
        </div>
    </div>


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