<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT sr.*,e.nombre as estado from pagos_lotes sr join estados_pago e on sr.cod_estadopagolote=e.codigo order by sr.codigo desc");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('fecha', $fecha);
//$stmt->bindColumn('glosa', $descripcion);
$stmt->bindColumn('nombre', $observaciones);
$stmt->bindColumn('cod_comprobante', $codComprobante);
$stmt->bindColumn('estado', $estado);
$stmt->bindColumn('cod_estadopagolote', $codEstado);
$stmt->bindColumn('cod_ebisalote', $cod_ebisa);

?>
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
                  <a href="#" title="Actualizar Lista" class="btn btn-default btn-sm btn-fab float-right" onclick="actualizarSimulacionSitios()">
                    <i class="material-icons">refresh</i>
                  </a>
                  <h4 class="card-title"><b>Pagos por Lotes</b></h4>
                  
                </div>
                <div class="card-body">
                    <table class="table table-condesed small" id="tablePaginator">
                      <thead>
                        <tr style="background:#21618C; color:#fff;">
                          <th>Proveedor</th>
                          <th>Detalle</th>
                          <th>Fecha Pago</th>
                          <th>Fecha Sol.</th>
                          <th># Sol.</th>
                          <th>Oficina</th>
                          <th>Observaciones</th>
                          <th>Estado</th>
                          <th class="text-right" width="20%">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
            $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $datosArray=obtenerDatosProveedoresPagoDetalle($codigo);
                          $descripcion=obtenerGlosaComprobante($codComprobante);
                          if(strlen($descripcion)>50){
                            $descripcion=substr($descripcion, 0, 50)."...";
                          }
                          /*if($nombre_lote!=""){
                            $datosArray[0]="<a href='#' title='".$datosArray[0]."' class='btn btn-primary btn-sm'><i class='material-icons'>view_comfy</i> ".$nombre_lote."</a>";
                          }*/
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
?>
                        <tr>
                          <td><?=$datosArray[0]?></td>
                          <td><?=$datosArray[1]?></td>
                          <!--<td><?=$descripcion?></td>-->
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td><?=$datosArray[2]?></td>
                          <td><div class="btn-group"><?=$datosArray[3]?></div></td>
                          <td><?=$datosArray[4]?></td>
                          <td><?=$observaciones;?></td>
                          <td class="text-muted"><?=$estado?></td>
                          <td class="td-actions text-right">
                            <?php 
                            if($codComprobante!=0){
                              ?>
                               <div class="btn-group dropdown">
                                     <button type="button" class="btn btn-primary dropdown-toggle" title="COMPROBANTE DE PAGOS" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="material-icons"><?=$iconImp;?></i>
                                     </button>
                                    <div class="dropdown-menu">
                                      <?php
                                        $stmtMoneda = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
                                       $stmtMoneda->execute();
                                       while ($row = $stmtMoneda->fetch(PDO::FETCH_ASSOC)) {
                                         $codigoX=$row['codigo'];
                                         $nombreX=$row['nombre'];
                                         $abrevX=$row['abreviatura'];
                                            ?>
                                             <a href="#" onclick="javascript:window.open('<?=$urlImpComp;?>?comp=<?=$codComprobante;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                                 <i class="material-icons">list_alt</i> <?=$abrevX?>
                                             </a> 
                                           <?php
                                         }
                                         ?>
                                    </div>
                                  </div>   
                              <?php  
                            }
                            ?>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu">
                                <a href="<?=$urlListPago?>&codigo=<?=$codigo?>" class="dropdown-item">
                                       <i class="material-icons">attach_money</i> Lista Pagos 
                                    </a>
                                <?php 
                                if($codEstado!=2){
                                  if($codEstado==1){
                                    ?><a href="<?=$urlEdit2Lote?>?cod=<?=$codigo?>&estado=4&admin=0" class="dropdown-item">
                                       <i class="material-icons text-warning">send</i> Enviar Solicitud Lote
                                    </a><?php 
                                  }else{
                                    if($codEstado==3){
                                      if($cod_ebisa!=0){
                                        ?>
                                       <a href="#" onclick="alerts.showSwal('warning-message-crear-comprobante','<?=$urlGenerarComprobanteLote?>?cod=<?=$codigo?>')" class="dropdown-item">
                                       <i class="material-icons text-success">attach_money</i> Generar Comprobante Lote
                                      </a>
                                      <a href="<?=$urlGenerarEbisaLote?>?cod=<?=$codigo?>" class="dropdown-item">
                                       <i class="material-icons text-muted">note</i> Descargar Archivo TXT Lote
                                      </a>  
                                        <?php
                                      }else{
                                        ?>
                                       <a href="<?=$urlGenerarEbisaLote?>?cod=<?=$codigo?>" class="dropdown-item">
                                       <i class="material-icons text-muted">note</i> Generar Archivo TXT Lote
                                      </a> 
                                        <?php
                                      }
                                    }else{
                                      if($codEstado==4){
                                        ?><a href="<?=$urlEdit2Lote?>?cod=<?=$codigo?>&estado=1&admin=0" class="dropdown-item">
                                       <i class="material-icons text-danger">clear</i> Cancelar Envio Lote
                                      </a><?php
                                      }else{
                                        //cod 5 PAGADO
                                        ?><a href="#" class="dropdown-item">
                                       <i class="material-icons text-info">attach_money</i> Pago Lote Registrado
                                      </a><?php
                                      }        
                                    }               
                                 }
                                }
                               ?>
                                      
                              </div>
                            </div>             
                          </td>
                        </tr>
<?php
              $index++;
            }
?>
                      </tbody>
                    </table>
                </div>
              </div>
              <div class="card-footer fixed-bottom">
              
                <a href="#" onclick="javascript:window.open('<?=$urlRegisterLote;?>')" class="btn btn-primary"><i class="material-icons">add</i> Nuevon Pago por Lotes</a>
                <!--<a href="#" onclick="javascript:window.open('<?=$urlRegisterLote;?>')" class="btn btn-primary"><i class="material-icons">view_comfy</i> Pagos Por Lotes</a>-->
                <!--<a href="#" onclick="nuevoArchivoTxtPagoLote()" class="<?=$buttonNormal;?>">Generar Archivo TXT</a>-->
              </div>      
            </div>
          </div>  
        </div>
    </div>



    <!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modal_txtarchivo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 80% !important;">
    <div class="modal-content card">
               <div class="card-header card-header-primary card-header-text">
                  <div class="card-text">
                    <h4>Nuevo Archivo Txt</h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                  <div>
                    <center><h4 class="text-muted">Lista de Pagos Aprobados</h4></center>
                       <table class="table table-bordered table-condensed small">
                        <thead>
                         <tr style="background:#21618C; color:#fff;">
                          <th>H/D</th>
                           <th>Proveedor</th>
                          <th>Detalle</th>
                          <th>Fecha Pago</th>
                          <th>Fecha Sol.</th>
                          <th># Sol.</th>
                          <th>Oficina</th>
                          <th>Observaciones</th>
                          <th>Estado</th>
                         </tr> 
                        </thead>
                        <tbody>
                        <?php
                        $stmt = $dbh->prepare("SELECT sr.*,e.nombre as estado from pagos_proveedores sr join estados_pago e on sr.cod_estadopago=e.codigo where sr.cod_estadopago=3 order by sr.codigo desc");
                        $stmt->execute();
                        $index=0;
                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $codigo=$row['codigo'];
                            $nombre_lote=$row['nombre_lote'];
                            $fecha=$row['fecha'];
                            $observaciones=$row['observaciones'];
                            $codComprobante=$row['cod_comprobante'];
                            $estado=$row['estado'];
                            $codEstado=$row['cod_estadopago'];
                            $cod_ebisa=$row['cod_ebisa'];

                          $datosArray=obtenerDatosProveedoresPagoDetalle($codigo);
                          $descripcion=obtenerGlosaComprobante($codComprobante);
                          if(strlen($descripcion)>50){
                            $descripcion=substr($descripcion, 0, 50)."...";
                          }
                          if($nombre_lote!=""){
                            $datosArray[0]="<a href='#' title='".$datosArray[0]."' class='btn btn-primary btn-sm'><i class='material-icons'>view_comfy</i> ".$nombre_lote."</a>";
                          }
                          if($cod_ebisa!=0){
                            $banderaHab=1;
                          }else{
                            $banderaHab=0;
                          }
                          ?>
                          <tr>
                            <td>
                              <div class="togglebutton">
                                <label>
                                   <input type="checkbox" <?=($banderaHab==1)?"checked":"";?> id="modal_checkprov" onclick="activarInputFilaPago(<?=$index?>)">
                                   <span class="toggle"></span>
                                </label>
                              </div>
                              <input type="hidden" id="codigo_pagofila<?=$index?>" value="<?=$codigo?>" <?=($banderaHab==0)?"readonly":"";?>>
                             </td>
                             <td><?=$datosArray[0]?></td>
                             <td><?=$datosArray[1]?></td>
                             <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                             <td><?=$datosArray[2]?></td>
                             <td><div class="btn-group"><?=$datosArray[3]?></div></td>
                             <td><?=$datosArray[4]?></td>
                             <td><?=$observaciones;?></td>
                             <td class="text-muted"><?=$estado?></td>
                          </tr>
                                      <?php 
                                      $index++;
                                     }
                         ?>
                         </tbody>
                       </table>
                    <input type="hidden" id="cantidad_filaspago" value="<?=$index?>">   
                </div>
                <hr>
                <a href="#" onclick="generarArchivosTXTVarios()" class="btn btn-white float-right" style="background:#F7FF5A; color:#07B46D;" >Generar TXT</a>
                <br><br>
      </div>  
    </div>
  </div>
<!--    end small modal -->