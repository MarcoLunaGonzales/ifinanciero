<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$sqlwhere="where sr.cod_pagolote=0 or sr.cod_pagolote IS NULL";
$sqlNombreLote="";
if(isset($_GET['codigo'])){
  $codigoLote=$_GET['codigo'];
  $sqlwhere="where sr.cod_pagolote=$codigoLote"; 
  $sqlNombreLote=" - ".nameLotesPago($codigoLote); 
}
// Preparamos
$sql="SELECT sr.*,e.nombre as estado from pagos_proveedores sr join estados_pago e on sr.cod_estadopago=e.codigo $sqlwhere order by sr.codigo desc";
// echo "<br><br>".$sql;
$stmt = $dbh->prepare($sql);
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_pagolote', $cod_pagolote);
$stmt->bindColumn('fecha', $fecha);
//$stmt->bindColumn('glosa', $descripcion);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_comprobante', $codComprobante);
$stmt->bindColumn('estado', $estado);
$stmt->bindColumn('cod_estadopago', $codEstado);
$stmt->bindColumn('cod_ebisa', $cod_ebisa);
$stmt->bindColumn('cod_cajachicadetalle', $cod_cajachicadetalle);

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
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">attach_money</i>
                  </div>
                  <a href="#" title="Actualizar Lista" class="btn btn-default btn-sm btn-fab float-right" onclick="actualizarSimulacionSitios()">
                    <i class="material-icons">refresh</i>
                  </a>
                  <h4 class="card-title"><b>Pagos<?=$sqlNombreLote?></b></h4>
                  
                </div>
                <div class="card-body">
                    <table class="table table-condesed small" id="tablePaginator">
                      <thead>
                        <tr style="background:#21618C; color:#fff;">
                          <th>Proveedor</th>
                          <th>Detalle</th>
                          <th>Fecha Pago</th>
                          <!-- <th>Fecha Sol.</th> -->
                          <!-- <th># Sol.</th> -->
                          <!-- <th>Oficina</th> -->
                          <th>Observaciones</th>
                          <th>Estado</th>
                          <th class="text-right" width="25%">Actions</th>
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
                          
                          
                          
                          <td><?=$observaciones;?></td>
                          <td class="text-muted"><?=$estado?></td>
                          <td class="td-actions text-right">
                            <?php 
                           if(!isset($_GET['codigo'])&&$codComprobante==0){
                            if($cod_ebisa==0){
                              ?>
                               <div class="btn-group dropdown">
                                     <button type="button" class="btn btn-info dropdown-toggle" title="Sin Cargar en e-banking" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="material-icons">note</i>
                                     </button>
                                    <div class="dropdown-menu">
                                      <h6 class="dropdown-header">Sin cargar a e-banking</h6>
                                      <a href="<?=$urlGenerarEbisa;?>?cod=<?=$codigo;?>&a=1" onclick="javascript:location.reload(true);" class="dropdown-item">
                                                 <i class="material-icons text-warning">offline_pin</i> Marcar como cargado en e-banking
                                             </a>
                                             <a href="<?=$urlGenerarEbisa?>?cod=<?=$codigo?>&a=0" class="dropdown-item">
                                                 <i class="material-icons text-dark">note</i> Descargar Archivo
                                             </a>
                                              
                                    </div>
                                  </div>   
                              <?php  
                            }else{
                              ?>
                               <div class="btn-group dropdown">
                                     <button type="button" class="btn btn-success dropdown-toggle" title="Cargado en e-banking" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="material-icons">offline_pin</i>
                                     </button>
                                    <div class="dropdown-menu">
                                      <h6 class="dropdown-header">Cargado en e-banking</h6>
                                             <a href="<?=$urlGenerarEbisa?>?cod=<?=$codigo?>&a=0" class="dropdown-item">
                                                 <i class="material-icons text-dark">note</i> Descargar Archivo
                                             </a>
                                    </div>
                                  </div>   
                              <?php 
                             }
                           } 
                            if($codComprobante!=0&&!isset($_GET['codigo'])){
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
                            if(!isset($_GET['codigo'])&&$codEstado==1){
                              ?>
                                   <a title="Editar Pago - Detalle" target="_self" href='<?=$urlEditPago;?>?cod=<?=$codigo;?>' class="btn btn-info">
                                    <i class="material-icons"><?=$iconEdit;?></i>
                                  </a>
                                    <?php 
                            }
                            ?>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">list</i> <?=$estado;?>
                              </button>
                              <div class="dropdown-menu">
                                
                                <?php 
                                if(!isset($_GET['codigo'])){
                                  ?><a href="<?=$urlVerPago?>?cod=<?=$codigo?>" target="_blank" class="dropdown-item">
                                       <i class="material-icons text-info">payment</i> Ver Pago
                                    </a><?php
                                if($codEstado!=2){
                                  if($codEstado==1){
                                    ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=4&admin=0" class="dropdown-item">
                                       <i class="material-icons text-warning">send</i> Enviar Solicitud
                                    </a><?php 
                                  }else{
                                    if($codEstado==3){
                                      if(($cod_cajachicadetalle==""||$cod_cajachicadetalle==0)){
                                        ?>
                                       <a href="#" onclick="alerts.showSwal('warning-message-crear-comprobante','<?=$urlGenerarComprobante?>?cod=<?=$codigo?>')" class="dropdown-item">
                                       <i class="material-icons text-success">attach_money</i> Generar Comprobante
                                      </a>
                                        <?php   
                                      }
                                    }else{
                                      if($codEstado==4){
                                        ?><a href="<?=$urlEdit2?>?cod=<?=$codigo?>&estado=1&admin=0" class="dropdown-item">
                                       <i class="material-icons text-danger">clear</i> Cancelar Envio
                                      </a><?php
                                      }else{
                                        //cod 5 PAGADO
                                        ?><a href="#" class="dropdown-item">
                                       <i class="material-icons text-info">attach_money</i> Pago Registrado
                                      </a><?php
                                      }        
                                    }               
                                 }
                                }
                              }else{
                                ?><a href="<?=$urlVerPago?>?cod=<?=$codigo?>&codl=<?=$codigoLote?>" target="_blank" class="dropdown-item">
                                       <i class="material-icons text-info">payment</i> Ver Pago
                                    </a><?php
                              }//fin if isset
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
              <?php 
              if(!isset($_GET['codigo'])){
               ?>
               <div class="card-footer fixed-bottom">
                <a href="#" onclick="javascript:window.open('<?=$urlRegister2;?>')" class="btn btn-info"><i class="material-icons">add</i> Nuevo Pago Proveedor</a>
                <!--<a href="#" onclick="javascript:window.open('<?=$urlRegisterLote;?>')" class="btn btn-primary"><i class="material-icons">view_comfy</i> Pagos Por Lotes</a>-->
                <!--<a href="#" onclick="nuevoArchivoTxtPagoLote()" class="<?=$buttonNormal;?>">Generar Archivo TXT</a>-->
              </div>
               <?php 
              }else{
                if(isset($_GET['admin'])){
                  $urlListPagoLotes=$urlListPagoAdmin; 
                }
                ?>
                <div class="card-footer fixed-bottom">
                <a href="<?=$urlListPagoLotes;?>" class="btn btn-danger">Volver</a>
                </div>
              <?php
              }
              ?>
                    
            </div>
          </div>  
        </div>
    </div>

