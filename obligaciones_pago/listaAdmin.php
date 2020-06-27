<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT sr.*,e.nombre as estado from pagos_proveedores sr join estados_pago e on sr.cod_estadopago=e.codigo where sr.cod_estadopago=4 and sr.cod_pagolote IS NULL order by sr.codigo desc");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('fecha', $fecha);
//$stmt->bindColumn('glosa', $descripcion);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_comprobante', $codComprobante);
$stmt->bindColumn('estado', $estado);
$stmt->bindColumn('cod_estadopago', $codEstado);

// Preparamos
$stmtLotes = $dbh->prepare("SELECT sr.*,e.nombre as estado from pagos_lotes sr join estados_pago e on sr.cod_estadopagolote=e.codigo where sr.cod_estadopagolote=4 order by sr.codigo desc");
// Ejecutamos
$stmtLotes->execute();
// bindColumn
$stmtLotes->bindColumn('codigo', $codigo);
$stmtLotes->bindColumn('fecha', $fecha);
//$stmtLotes->bindColumn('glosa', $descripcion);
$stmtLotes->bindColumn('nombre', $observaciones);
$stmtLotes->bindColumn('cod_comprobante', $codComprobante);
$stmtLotes->bindColumn('estado', $estado);
$stmtLotes->bindColumn('cod_estadopagolote', $codEstado);

?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <!--<div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">attach_money</i>
                  </div>
                  <h4 class="card-title"><b>Gestión de Pagos</b></h4>
                </div>-->
                <div class="card-header <?=$colorCard;?> card-header-text">
                  <a href="#" onclick="cambiarDosDivPantalla('list_pagos','list_pagos_lotes')">
                    <div id="button_list_pagos" class="card-text fondo-boton fondo-boton-active">
                      <h4 class="font-weight-bold">Gestión de Pagos <span class="badge bg-info d-none" id="n_list_pagos"></span></h4>
                    </div>
                  </a>
                  <a href="#" onclick="cambiarDosDivPantalla('list_pagos_lotes','list_pagos')">
                     <div id="button_list_pagos_lotes" class="card-text fondo-boton">
                        <h4 class="font-weight-bold">Gestión de Pagos Lotes <span class="badge bg-info d-none" id="n_list_pagos_lotes"></span></h4>
                     </div>
                  </a>
                  <!--<h4 class="card-title"><b>Gestión de Pagos</b></h4>-->
                </div>
                <div class="card-body">
                  <div id="list_pagos">
                    <table class="table table-condesed" id="tablePaginator">
                      <thead>
                        <tr>
                          <th>Proveedor</th>
                          <!--<th>Descripci&oacute;n</th>-->
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
                                     <button type="button" class="btn btn-primary dropdown-toggle" title="COMPROBANTE - DEVENGADO" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                                <a href="<?=$urlVerPago?>?cod=<?=$codigo?>&admin=0" target="_blank" class="dropdown-item">
                                       <i class="material-icons text-info">payment</i> Ver Pago
                                    </a>
                                <?php 
                                if($codEstado!=2){
                                  if($codEstado==1){
                                    ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=4&admin=0" class="dropdown-item">
                                       <i class="material-icons text-warning">send</i> Enviar Solicitud
                                    </a><?php 
                                  }else{
                                    if($codEstado==3){
                                      ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=5&admin=0" class="dropdown-item">
                                       <i class="material-icons text-success">attach_money</i> Generar Comprobante
                                      </a><?php 
                                    }else{
                                      if($codEstado==4){
                                        ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0" class="dropdown-item">
                                       <i class="material-icons text-warning">refresh</i> Devolver Pago
                                      </a><?php
                                       //if($globalAdmin==1){
                                        ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=3&admin=1" class="dropdown-item">
                                          <i class="material-icons text-success">offline_pin</i> Aprobar
                                         </a><?php
                                        //} 
                                      }else{
                                        //cod 5 PAGADO
                                        ?><a href="#" class="dropdown-item">
                                       <i class="material-icons text-info">attach_money</i> Pago Registrado
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
                  <div id="list_pagos_lotes" class="d-none">
                      <table class="table table-condesed" id="tablePaginator100">
                      <thead>
                        <tr>
                          <th>Descripción</th>
                          <th>Proveedor</th>
                          <th>Fecha Pago</th>
                          <th>Fecha Sol.</th>
                          <th>Oficina</th>
                          <th>Estado</th>
                          <th class="text-right" width="20%">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
            $index2=1;
                        while ($row = $stmtLotes->fetch(PDO::FETCH_BOUND)) {
                          $datosArray=obtenerDatosProveedoresPagoDetalleLote($codigo);
                          $descripcion=obtenerGlosaComprobante($codComprobante);
                          if(strlen($descripcion)>50){
                            $descripcion=substr($descripcion, 0, 50)."...";
                          }

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
                          <td><?=$observaciones?></td>
                          <td><?=$datosArray[0]?></td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td><?=$datosArray[2]?></td>
                          <td><?=$datosArray[4]?></td>
                          <td class="text-muted"><?=$estado?></td>
                          <td class="td-actions text-right">
                            <?php 
                            if($codComprobante!=0){
                              ?>
                               <div class="btn-group dropdown">
                                     <button type="button" class="btn btn-primary dropdown-toggle" title="COMPROBANTE - DEVENGADO" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                                <a href="<?=$urlListPago?>&codigo=<?=$codigo?>&admin=1" class="dropdown-item">
                                       <i class="material-icons">list</i> Lista Pagos 
                                    </a>
                                <?php 
                                if($codEstado!=2){
                                  if($codEstado==1){
                                    ?><a href="<?=$urlEdit2Lote?>?cod=<?=$codigo?>&estado=4&admin=0" class="dropdown-item">
                                       <i class="material-icons text-warning">send</i> Enviar Solicitud
                                    </a><?php 
                                  }else{
                                    if($codEstado==3){
                                      ?><a href="<?=$urlEdit2Lote?>?cod=<?=$codigo?>&estado=5&admin=0" class="dropdown-item">
                                       <i class="material-icons text-success">attach_money</i> Generar Comprobante
                                      </a><?php 
                                    }else{
                                      if($codEstado==4){
                                        ?><a href="<?=$urlEdit2Lote?>?cod=<?=$codigo?>&estado=1&admin=0" class="dropdown-item">
                                       <i class="material-icons text-warning">refresh</i> Devolver Pago
                                      </a><?php
                                       //if($globalAdmin==1){
                                        ?><a href="<?=$urlEdit2Lote?>?cod=<?=$codigo?>&estado=3&admin=1" class="dropdown-item">
                                          <i class="material-icons text-success">offline_pin</i> Aprobar
                                         </a><?php
                                        //} 
                                      }else{
                                        //cod 5 PAGADO
                                        ?><a href="#" class="dropdown-item">
                                       <i class="material-icons text-info">attach_money</i> Pago Registrado
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
              $index2++;
            }
?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>      
            </div>
          </div>  
        </div>
    </div>
<?php 
$index--;
$index2--;
 if($index>0){
   ?><script>$("#n_list_pagos").html('<?=$index?>');$("#n_list_pagos").removeClass('d-none');</script><?php
 }
 if($index2>0){
   ?><script>$("#n_list_pagos_lotes").html('<?=$index2?>');$("#n_list_pagos_lotes").removeClass('d-none');</script><?php
 }
?>
