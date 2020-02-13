<?php
require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';
require_once 'layouts/bodylogin2.php';

require_once 'functionsGeneral.php';
require_once 'functions.php';


//require_once 'modal.php';


$globalAdmin=$_SESSION["globalAdmin"];
$cod_cajachica=$codigo;
$cod_tcc=$cod_tcc;
$dbh = new Conexion();
//sacamos monto de caja chica
$stmtMCC = $dbh->prepare("SELECT monto_inicio,monto_reembolso,fecha,numero from caja_chica where  codigo =$cod_cajachica");
$stmtMCC->execute();
$resultMCC=$stmtMCC->fetch();
$monto_cajachica=$resultMCC['monto_inicio'];
$monto_reembolso=$resultMCC['monto_reembolso'];
$fecha_cc=$resultMCC['fecha'];
$numero_cc=$resultMCC['numero'];
//monto de rendiciones



$stmt = $dbh->prepare("SELECT codigo,cod_cuenta,fecha,
  (select td.nombre from tipos_documentocajachica td where td.codigo=cod_tipodoccajachica) as cod_tipodoccajachica,nro_documento,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal)as cod_personal,monto,monto_rendicion,observaciones,cod_estado,
  (select e.nombre from estados_rendiciones e where e.codigo=cod_estado) as nombre_estado 
from caja_chicadetalle
where cod_cajachica=$cod_cajachica and cod_estadoreferencial=1");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo_detalle_Cajachica);
$stmt->bindColumn('cod_cuenta', $cod_cuenta);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('cod_tipodoccajachica', $cod_tipodoccajachica);
$stmt->bindColumn('nro_documento', $nro_documento);
$stmt->bindColumn('monto', $monto);
$stmt->bindColumn('monto_rendicion', $monto_rendicion);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('nombre_estado', $nombre_estado);


$stmtb = $dbh->prepare("SELECT (select a.nombre from tipos_caja_chica a where a.codigo=cod_tipocajachica) as nombre_caja_chica FROM caja_chica WHERE codigo=$cod_cajachica");
$stmtb->execute();
$resulttb=$stmtb->fetch();
$nombre_caja_chica=$resulttb['nombre_caja_chica'];


?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">DETALLE</h4>
                  <h4 class="card-title" align="center"><?=$nombre_caja_chica?></h4>
                  
                  <div class="row">
                      <label class="col-sm-1 col-form-label text-right"><b>Monto Inicial</b></label>
                      <div class="col-sm-2">
                          <div class="form-group">
                              <input style="background-color:#ffffff;" class="form-control" readonly="readonly" value="<?=number_format($monto_cajachica, 2, '.', ',')?>" />
                          </div>
                      </div>
                      <label class="col-sm-1 col-form-label text-right"><b>Saldo</b></label>
                      <div class="col-sm-2">
                      <div class="form-group">
                          <input style="background-color:#ffffff;" class="form-control" name="numero" id="numero" value="<?=number_format($monto_reembolso, 2, '.', ',')?>"  readonly="readonly"/>
                      </div>
                      </div>
                      <label class="col-sm-1 col-form-label text-right"><b>Fecha</b></label>
                      <div class="col-sm-2">
                      <div class="form-group">
                          <input style="background-color:#ffffff;" class="form-control" name="numero" id="numero" value="<?=$fecha_cc?>"  readonly="readonly"/>
                      </div>
                      </div>
                      <label class="col-sm-1 col-form-label text-right"><b>Número</b></label>
                      <div class="col-sm-2">
                      <div class="form-group">
                          <input style="background-color:#ffffff;" class="form-control" name="numero" id="numero" value="<?=$numero_cc?>"  readonly="readonly"/>
                      </div>
                      </div>
                  </div> <!--fin campo fecha numero-->

                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator50_2">

                      <thead>
                        <tr>
                          <th>#</th>                        
                          <th>Cuenta</th>
                          <th>Fecha</th>
                          <th>Tipo</th>
                          <th>N. Doc</th>
                          <th>Entregado a</th>
                          <th>Monto</th>                          
                          <th>Monto Rendición</th> 
                          <th>Monto Devolución</th>
                          <th>Detalle</th>
                          <th>Estado</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          if($monto_rendicion == 0 || $monto_rendicion ==null) $monto_devuelto=0;
                          else $monto_devuelto=$monto-$monto_rendicion;
                          if($cod_estado==1)
                            $label='<span class="badge badge-danger">';
                          else
                            $label='<span class="badge badge-success">';
                         ?>
                          <tr>
                            <td><?=$index;?></td>                            
                              <td><?=$cod_cuenta;?></td>
                              <td><?=$fecha;?></td>
                              <td><?=$cod_tipodoccajachica;?></td>        
                              <td><?=$nro_documento;?></td>        
                              <td><?=$cod_personal;?></td>        
                              
                              <td><?=number_format($monto, 2, '.', ',');?></td>        
                              <td><?=number_format($monto_rendicion, 2, '.', ',');?></td>        
                              <td><?=number_format($monto_devuelto, 2, '.', ',');?></td>
                              <td><?=$observaciones;?></td>
                              <td><?=$label.$nombre_estado."</span>";?></td>
                              <td class="td-actions text-right">
                                <script>var nfac=[];itemFacturasDCC.push(nfac);var nest=[];itemEstadosCuentas.push(nest);</script>
                              <?php
                                if($globalAdmin==1 and $cod_estado==1){


                                  $stmtFCCD = $dbh->prepare("SELECT * FROM facturas_detalle_cajachica where cod_cajachicadetalle=$codigo_detalle_Cajachica");
                                  $stmtFCCD->execute();
                                  while ($row = $stmtFCCD->fetch(PDO::FETCH_ASSOC)) {
                                        $nit=$row['nit'];
                                        $factura=$row['nro_factura'];
                                        $fechaFac=$row['fecha'];
                                        $razon=$row['razon_social'];
                                        $importe=$row['importe'];
                                        $exento=$row['exento'];
                                        $autorizacion=$row['nro_autorizacion'];
                                        $control=$row['codigo_control'];
                                        ?><script>abrirFacturaDCC(<?=$codigo_detalle_Cajachica?>,'<?=$nit?>',<?=$factura?>,'<?=$fechaFac?>','<?=$razon?>',<?=$importe?>,<?=$exento?>,'<?=$autorizacion?>','<?=$control?>');</script><?php
                                    }
                              ?> 
                                

                               <!--  <a href='<?=$urlFormAgregarFacturas;?>&codigo=<?=$codigo_detalle_Cajachica;?>&cod_tcc=<?=$cod_tcc?>&cod_cc=<?=$cod_cajachica?>' title="Facturas" id="boton_fac<?=$codigo_detalle_Cajachica;?>" class="btn btn-info btn-sm btn-fab">
                                  <i class="material-icons">featured_play_list</i>
                                </a> -->
                               
                                <a href='#' title="Facturas" id="boton_fac<?=$codigo_detalle_Cajachica;?>" class="btn btn-info btn-sm btn-fab" onclick="listFacDCC(<?=$codigo_detalle_Cajachica;?>);">
                                  <i class="material-icons">featured_play_list</i>
                                  <span id="nfac<?=$codigo_detalle_Cajachica;?>" class="count bg-warning">0</span>
                                </a>
                                
                                <a href='<?=$urlFormDetalleCajaChica;?>&codigo=<?=$codigo_detalle_Cajachica;?>&cod_tcc=<?=$cod_tcc?>&cod_cc=<?=$cod_cajachica?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteDetalleCajaChica;?>&codigo=<?=$codigo_detalle_Cajachica;?>&cod_tcc=<?=$cod_tcc?>&cod_cc=<?=$cod_cajachica?>')">
                                  <i class="material-icons" title="Borrar"><?=$iconDelete;?></i>
                                </button> 
                                <?php
                                  }
                                ?>
                              
                              </td>
                          </tr>
                        <?php $index++; } ?>
                      </tbody>
                    
                    </table>
                  </div>
                </div>
              </div>
              
              <div class="card-footer fixed-bottom">
                <?php

              if($globalAdmin==1){
              ?>
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormDetalleCajaChica;?>&codigo=0&cod_tcc=<?=$cod_tcc?>&cod_cc=<?=$cod_cajachica?>'">Registrar</button>
                    <?php
              }
              ?>
              <button class="btn btn-danger" onClick="location.href='<?=$urlListCajaChica;?>&codigo=<?=$cod_tcc?>'"><i class="material-icons" title="Volver">keyboard_return</i>Volver</button>
              </div>              
            </div>
          </div>  
        </div>
    </div>
<!-- modal facturas -->
<div class="modal fade" id="modalFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice modal-lg">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
              <div class="card ">
                <div class="card-header" id="divTituloCuentaDetalle">
                  <h4 class="card-title">Facturas 
                    <!-- <small class="description">Cuenta :</small> -->
                  </h4>
                </div>
                <div class="card-body ">
                  <ul class="nav nav-pills nav-pills-warning" role="tablist">
                    <li class="nav-item">
                          <a id="nav_boton1"class="nav-link active" data-toggle="tab" href="#link110" role="tablist">
                            <span class="material-icons">view_list</span> Lista
                          </a>
                        </li>
                        <li class="nav-item">
                          <a id="nav_boton2"class="nav-link" data-toggle="tab" href="#link111" role="tablist">
                            <span class="material-icons">add</span> Nuevo
                          </a>
                        </li>
                        <li class="nav-item">
                          <a id="nav_boton3" class="nav-link" data-toggle="tab" href="#link112" role="tablist">
                            <span class="material-icons">filter_center_focus</span> QR quincho
                          </a>
                        </li>
                  </ul>
                  <div class="tab-content tab-space">
                    <div class="tab-pane active" id="link110">
                      <form id="formRegFactCajaChica" class="form-horizontal" action="caja_chica/detallecajachica_save_facturas.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="cod_cajachica" id="cod_cajachica" value="<?=$cod_cajachica;?>">
                        <input type="hidden" name="cod_tcc" id="cod_tcc" value="<?=$cod_tcc;?>">
                        <div id="divResultadoListaFac">
            
                        </div>
                        <div class="form-group float-left">
                          <button type="submit" class="btn btn-info btn-round">Guardar</button>
                        </div>  
                      </form>
                      
                    </div>
                    <div class="tab-pane" id="link111">
                      <form name="form2">
                           <input class="form-control" type="hidden" name="codCuenta" id="codCuenta"/>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">NIT</label>
                       <div class="col-sm-4">
                        <div class="form-group">
                          <input class="form-control" type="text" name="nit_fac" id="nit_fac" required="true"/>
                        </div>
                        </div>
                        <label class="col-sm-2 col-form-label">Nro. Factura</label>
                       <div class="col-sm-4">
                        <div class="form-group">
                          <input class="form-control" type="number" name="nro_fac" id="nro_fac" required="true"/>
                        </div>
                        </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Fecha</label>
                       <div class="col-sm-4">
                        <div class="form-group">
                          <input type="text" class="form-control datepicker" name="fecha_fac" id="fecha_fac" value="<?=$fechaActualModal?>">
                        </div>
                        </div>
                        <label class="col-sm-2 col-form-label">Importe</label>
                       <div class="col-sm-4">
                        <div class="form-group">
                          <input class="form-control" type="number" name="imp_fac" id="imp_fac" required="true"/>
                        </div>
                        </div>
                      </div>
                      <!-- Exento oculto-->
                      <input class="form-control" type="hidden" name="exe_fac" id="exe_fac" required="true"/>
                      <!--No tiene funcion este campo-->
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Nro. Autorizaci&oacute;n</label>
                       <div class="col-sm-4">
                        <div class="form-group">
                          <input class="form-control" type="text" name="aut_fac" id="aut_fac" required="true"/>
                        </div>
                        </div>
                        <label class="col-sm-2 col-form-label">Cod. Control</label>
                       <div class="col-sm-4">
                        <div class="form-group">
                          <input class="form-control" type="text" name="con_fac" id="con_fac" required="true"/>
                        </div>
                       </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Razon Social</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <textarea class="form-control" name="razon_fac" id="razon_fac" value=""></textarea>
                        </div>
                        </div>
                      </div>
                      <div class="form-group float-right">
                        <button type="button" class="btn btn-info btn-round" onclick="saveFacturaDCC()">Guardar</button>
                      </div>
                         </form>
                    </div>
                    <div class="tab-pane" id="link112">
                     <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                          <div class="fileinput-preview fileinput-exists thumbnail"></div>
                         <div>
                         <span class="btn btn-rose btn-round btn-file">
                           <span class="fileinput-new">Subir archivo .txt</span>
                           <span class="fileinput-exists">Subir archivo .txt</span>
                           <input type="file" name="qrquincho" id="qrquincho" accept=".txt"/>
                         </span>
                
                        </div>
                       </div>
                       <p>Los archivos cargados se adjuntaran a la lista de facturas existente</p>
                    </div>
                  </div>
                </div>
              </div>
        
        <!--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eaque ullam autem illum, minima doloribus doloremque adipisci dolorem, repellendus debitis animi laboriosam commodi dolores et sint, quod. Pariatur, repudiandae sequi assumenda.</p>-->
      </div>
      <div class="modal-footer justify-content-center">
        
      </div>
    </div>
  </div>
</div>

