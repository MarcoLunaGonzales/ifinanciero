<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

require_once 'functionsGeneral.php';
require_once 'functions.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];

$dbhU = new Conexion();

$stmt = $dbhU->prepare("SELECT *,
  (select t.nombre from configuracion_retenciones t where t.codigo=cod_tipodoc) as tipo_documento,
  (select e.nombre from estados_rendiciones e where e.codigo=cod_estado) as nombre_estado
from rendiciones 
where cod_estadoreferencial=1 and cod_personal=$globalUser ORDER BY codigo desc");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo); 
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('tipo_documento', $tipo_documento);
$stmt->bindColumn('monto_a_rendir', $monto_a_rendir);
$stmt->bindColumn('monto_rendicion', $monto_rendicion);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('nombre_estado', $nombre_estado);
$stmt->bindColumn('fecha_dcc', $fecha_dcc);
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
                  <h4 class="card-title">Mis Rendiciones</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator50_2">

                      <thead>
                        <tr>
                          <th>#</th>                        
                          <th>Fecha</th>
                          <th>Tipo doc.</th>
                          <th>Monto a Rendir</th>
                          <th>Monto Rendición</th>
                          
                          <th>Detalle</th>
                          <th>Estado</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        $idFila=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                          
                          
                          if($fecha==null){
                            $fecha="Sin Definir";
                          }
                          if($cod_estado==1)
                            $label='<span class="badge badge-danger">';
                          else
                            $label='<span class="badge badge-success">';
                          ?>

                          <tr>
                            <td><?=$index;?></td>                            
                              <td><?=$fecha;?></td>
                              <td><?=$tipo_documento;?></td>
                              <td><?=number_format($monto_a_rendir, 2, '.', ',');?></td>        
                              <td><?=number_format($monto_rendicion, 2, '.', ',');?></td>
                                    
                              <td><?=$observaciones;?></td>        
                              <td><?=$label.$nombre_estado."</span>";?></td>
                              
                              <td class="td-actions text-right">
                                <script>var nfac=[];itemFacturasDRC.push(nfac);</script>
                              <?php
                                if($cod_estado==1){
                                  $stmtFCCD = $dbhU->prepare("SELECT * FROM facturas_detalle_cajachica where cod_cajachicadetalle=$codigo");
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
                                        ?><script>abrirFacturaDRC(<?=$idFila?>,'<?=$nit?>',<?=$factura?>,'<?=$fechaFac?>','<?=$razon?>',<?=$importe?>,<?=$exento?>,'<?=$autorizacion?>','<?=$control?>');</script><?php
                                    }
                              ?>
                                <a href='#' title="Facturas" id="boton_fac<?=$idFila;?>" class="btn btn-success btn-sm btn-fab" onclick="listFacDRC(<?=$idFila;?>,'<?=$fecha_dcc;?>','<?=$observaciones;?>',<?=$monto_a_rendir;?>,0,<?=$codigo?>);">
                                  <i class="material-icons">add</i>
                                  <span id="nfac<?=$idFila;?>" class="count bg-warning"></span>
                                </a>


                                <!-- <a href='<?=$urlListaRendicionesDetalle;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons" title="Agregar Rendición">add</i>
                                </a> -->
                                <?php
                                  }
                                ?>
                              
                              </td>
                          </tr>
                        <?php $index++;$idFila=$idFila+1; } ?>
                      </tbody>
                    
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>
<!-- modal facturas -->
<div class="modal fade" id="modalFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice" style="max-width: 80% !important;">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
              <div class="card ">
                <div class="card-header" id="divTituloCuentaDetalle">
                  <h4 class="card-title">DETALLE FACTURAS

                    <!-- <small class="description"><input type="text" name="observaciones_dcc" id="observaciones_dcc" readonly="readonly"><input type="text" name="fecha_dcc" id="fecha_dcc" readonly="readonly"></small> -->
                  </h4>
                  <div class="row" >
                      <label class="col-sm-1 col-form-label text-right"></label>
                      <div class="col-sm-1">
                      <div class="form-group">
                          <input style="background-color:#ffffff;" class="form-control" name="nro_dcc" id="nro_dcc"  readonly="readonly" type="hidden" />
                      </div>
                      </div>

                      <label class="col-sm-1 col-form-label text-right"><b>Detalle</b></label>
                      <div class="col-sm-2">
                      <div class="form-group">
                          <input style="background-color:#ffffff;" class="form-control" name="observaciones_dcc" id="observaciones_dcc"  readonly="readonly"/>
                      </div>
                      </div>
                      <label class="col-sm-1 col-form-label text-right"><b>Monto</b></label>
                      <div class="col-sm-2">
                      <div class="form-group">
                          <input style="background-color:#ffffff;" class="form-control" name="monto_dcc" id="monto_dcc"  readonly="readonly"/>
                      </div>
                      </div>
                      <label class="col-sm-1 col-form-label text-right"><b>Fecha</b></label>
                      <div class="col-sm-2">
                      <div class="form-group">
                          <input style="background-color:#ffffff;" class="form-control" name="fecha_dcc" id="fecha_dcc"  readonly="readonly"/>
                      </div>
                      </div>
                      
                    </div>
                </div>
                <div class="card-body">
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
                      <form id="formRegFactRendiciones" class="form-horizontal" action="caja_chica/rendicionesdetalle_save.php" method="post" enctype="multipart/form-data">                        
                        <input class="form-control" type="hidden" name="cod_rd" id="cod_rd"/>
                        <input type="hidden" name="cantidad_filas" id="cantidad_filas">
                        <div class="card" style="background: #e0e0e0">
                          <div class="card-body">
                            <div id="divResultadoListaFac">
            
                            </div>
                          </div>                      
                        </div>
                        
                        <div class="form-group float-left">
                          <button type="submit" class="btn btn-info btn-round">Guardar</button>
                        </div>  
                      </form>
                      
                    </div>
                    <div class="tab-pane" id="link111">
                      <form name="form2">
                        <input class="form-control" type="hidden" name="codCuenta" id="codCuenta"/>
                        <div class="card" style="background: #e0e0e0">
                          <div class="card-body">
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
                          </div>
                        </div>
                      <div class="form-group float-right">
                        <button type="button" class="btn btn-info btn-round" onclick="saveFacturaDRC()">Guardar</button>
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