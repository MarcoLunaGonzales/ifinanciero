<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';


setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("Y-m-d");
$dbh = new Conexion();
if(isset($_GET['codigo'])){
  $codigo=$_GET['codigo'];
}else{
  $codigo=0;
}
if(isset($_GET['sw_estado'])){
  $sw_estado=$_GET['sw_estado'];
}else{
  $sw_estado=0;
}
$sql="SELECT (select t.nombre from tipos_pagoproveedor t where t.codigo=p.cod_ebisalote)as tipo_pago,p.nombre,p.fecha,p.nro_correlativo,p.cod_comprobante,p.cod_estadopagolote,p.observaciones,e.nombre as nombre_estado from pagos_lotes p join estados_pago e on p.cod_estadopagolote=e.codigo  where p.codigo=$codigo";
// echo $sql; 
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $stmt->bindColumn('nombre', $nombre_lote);
  $stmt->bindColumn('fecha', $fecha);
  $stmt->bindColumn('observaciones', $observaciones);
  $stmt->bindColumn('cod_comprobante', $codComprobante);
  $stmt->bindColumn('cod_estadopagolote', $codEstado);
  $stmt->bindColumn('nombre_estado', $nombre_estado);
  $stmt->bindColumn('nro_correlativo', $nro_correlativo);
  $stmt->bindColumn('tipo_pago', $tipo_pago);
?>
<div id="logo_carga" class="logo-carga" style="display:none;"></div>
<div class="content">
  <div id="contListaGrupos" class="container-fluid">
    <input type="hidden" name="cod_solicitudrecursos" id="cod_solicitudrecursos" value="<?=$codigo?>">
      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header card-header-deafult card-header-text text-center">
              <div class="card-text">
                <h4 class="card-title"><b>PAGO PROVEEDORES</b></h4>
              </div>
            </div>
            <div class="card-body">
              <div class="">  
              <div class="row" id="">
                <?php 
                while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                  // $datosArray=obtenerDatosProveedoresPagoDetalle($codigo);
                  // $descripcion=obtenerGlosaComprobante($codComprobante);
                  // if(strlen($descripcion)>50){
                  //   $descripcion=substr($descripcion, 0, 50)."...";
                  // }
                  $nombre_pago="";
                  switch ($codEstado) {
                    case 1:
                      $btnEstado="btn-default";
                    break;
                    case 2:
                      $btnEstado="btn-danger";
                    break;
                    case 3:
                      $btnEstado="btn-success";
                    break;
                    case 4:
                      $btnEstado="btn-warning";
                    break;
                    case 5:
                      $btnEstado="btn-info";
                    break;
                  }
                  $fechaPago=strftime('%d/%m/%Y',strtotime($fecha)); ?>         
                  <label class="col-sm-1 col-form-label" style="color:#000000; ">Nombre Pago :</label>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <input type="text" class="form-control" readonly="true" value="<?=$nombre_lote?>" style="background-color:#E3CEF6;text-align: left" >
                    </div>
                  </div>  
                  <label class="col-sm-1 col-form-label" style="color:#000000; ">Fecha Pago:</label>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <input type="text" class="form-control" readonly="true" value="<?=$fechaPago?>" style="background-color:#E3CEF6;text-align: left" >
                    </div>
                  </div> 
                  <label class="col-sm-1 col-form-label" style="color:#000000; ">N° Pago</label>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <input type="text" class="form-control" readonly="true" value="<?=$codigo?>" style="background-color:#E3CEF6;text-align: left" >
                    </div>
                  </div>
                  </div>
                  <div class="row">
                  <label class="col-sm-1 col-form-label" style="color:#000000; ">Estado</label>
                  <div class="col-sm-1">
                    <div class="form-group">
                      <input type="text" class="form-control" readonly="true" value="<?=$nombre_estado?>" style="background-color:#E3CEF6;text-align: left" >
                    </div>
                  </div> 
                  <label class="col-sm-1 col-form-label" style="color:#000000; ">Tipo</label>
                  <div class="col-sm-1">
                    <div class="form-group">
                      <input type="text" class="form-control" readonly="true" value="<?=$tipo_pago?>" style="background-color:#E3CEF6;text-align: left" >
                    </div>
                  </div> 
                  <label class="col-sm-1 col-form-label" style="color:#000000; ">Observaciones</label>
                  <div class="col-sm-5">
                    <div class="form-group">
                      <input type="text" class="form-control" readonly="true" value="<?=$observaciones?>" style="background-color:#E3CEF6;text-align: left" >
                    </div>
                  </div><?php
                } ?>
              </div>
          <div class="col-sm-4 div-center"><center><h3>Detalle de Pago</h3></center></div>
          <div class="col-sm-12 div-center">  
            <table class="table table-bordered table-condensed">
              <thead>
                <tr style="background:#21618C; color:#fff;">
                  <th>#</th>
                  <th>OF/CC</th>
                  <th class="text-left">Tipo</th>
                  <th>Fecha</th>
                  <th>Proveedor</th>
                  <th >Glosa</th>
                  <th >Monto</th>
                </tr>
              </thead>
              <tbody>
              <?php 
              $solicitudDetalle=obtenerPagoProveedorDetalle_v2($codigo);
              $index=1;$totalImportePres=0;$totalImporte=0;$totalPago=0;
              while ($rowDetalles = $solicitudDetalle->fetch(PDO::FETCH_ASSOC)) {
                $codigoX=nameProveedor($rowDetalles['codigo']);
                $nombre_proveedorX=nameProveedor($rowDetalles['cod_proveedor']);
                $cod_tipopagoproveedorX=$rowDetalles["cod_tipopagoproveedor"];
                $importeX=$rowDetalles["monto"];
                $observacionesX=$rowDetalles["observaciones"];
                $fechaX=$rowDetalles["fecha"];
                // $pronto_pagoX=$rowDetalles["pronto_pago"];
                // $retencionX=0;
                $totalImportePres+=$importeX;
                $totalPago+=$importeX;?>
                <tr>
                  <td><small><?=$index?></small></td>
                  <td class="font-weight-bold"></td>
                  <td class="text-left"></td>
                  <td><small><?=$fechaX?></small></td>
                  <td class="text-left"><small><?=$nombre_proveedorX?></small></td>
                  <td class="text-left"><small><?=$observacionesX?></small></td>
                  <td class="text-right"><small><?=number_format($importeX, 2, '.', ',')?></small></td>
                </tr><?php
                  $index++;
              } ?>
              
              <tr class="font-weight-bold bg-white text-dark">
                <td colspan="6" class="text-left">Total</td>
                <td class="text-right"><?=number_format($totalPago, 2, '.', ',')?></td>
              </tr>
              </tbody>
            </table>
          </div>    
            <div class="card-footer fixed-bottom col-sm-12">
            <a href="../<?=$urlListPagoLotes;?>" class="btn btn-danger">Volver</a>
          <!--  <div class="row col-sm-9 float-right">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="bmd-label-static text-white" style="background:#21618C;">Solicitado</label>  
                          <input class="form-control bg-info text-white text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="<?=$totalImporte?>" id="total_presupuestado" readonly="true"> 
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                          <label class="bmd-label-static text-white" style="background:#21618C;">Pagado</label> 
                          <input class="form-control bg-info text-white text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="<?=$totalPago?>" id="total_solicitado" readonly="true"> 
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                          <label class="bmd-label-static text-white" style="background:#21618C;">Saldo del pago</label> 
                          <input class="form-control bg-info text-white text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="<?=$totalImporte-$totalPago?>" id="total_solicitado" readonly="true"> 
                        </div>
                    </div>
              </div>
            </div> -->
         </div>
          </div><!--div end card-->     
               </div>
            </div>
  </div>
</div>