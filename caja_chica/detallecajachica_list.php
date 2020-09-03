<?php
require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';
// require_once 'layouts/bodylogin2.php';

require_once 'functionsGeneral.php';
require_once 'functions.php';


//require_once 'modal.php';


$globalAdmin=$_SESSION["globalAdmin"];
$cod_cajachica=$codigo;
$cod_tcc=$cod_tcc;
$dbh = new Conexion();
//sacamos monto de caja chica
$stmtMCC = $dbh->prepare("SELECT monto_inicio,monto_reembolso,fecha,numero,monto_reembolso_nuevo from caja_chica where  codigo =$cod_cajachica");
$stmtMCC->execute();
$resultMCC=$stmtMCC->fetch();
$monto_cajachica=$resultMCC['monto_inicio'];
// $monto_saldo=$resultMCC['monto_reembolso'];
$fecha_cc=$resultMCC['fecha'];
$monto_reembolso_nuevo=$resultMCC['monto_reembolso_nuevo'];
if($monto_reembolso_nuevo==null || $monto_reembolso_nuevo== '')$monto_reembolso_nuevo=0;
$numero_cc=$resultMCC['numero'];
//monto de rendiciones

$sql_rendicion="SELECT SUM(c.monto)-IFNULL((select SUM(r.monto) from caja_chicareembolsos r where r.cod_cajachica=$cod_cajachica and r.cod_estadoreferencial=1),0)
 as monto_total from caja_chicadetalle c where c.cod_cajachica=$cod_cajachica and c.cod_estadoreferencial=1";
// echo $sql_rendicion;
$stmtSaldo = $dbh->prepare($sql_rendicion);
$stmtSaldo->execute();
$resultSaldo=$stmtSaldo->fetch();

if($resultSaldo['monto_total']!=null || $resultSaldo['monto_total']!='')
  $monto_total=$resultSaldo['monto_total'];
else $monto_total=0;                        
$monto_saldo=$monto_cajachica-$monto_total;

//listamos los gastos de caja chica
$stmt = $dbh->prepare("SELECT codigo,cod_cuenta,fecha,DATE_FORMAT(fecha,'%d/%m/%Y')as fecha_x,cod_tipodoccajachica,cod_uo,cod_area,
  (select pc.nombre from plan_cuentas pc where pc.codigo=cod_cuenta) as nombre_cuenta,
  (select td.abreviatura from configuracion_retenciones td where td.codigo=cod_tipodoccajachica) as nombre_tipodoccajachica,nro_documento,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal)as cod_personal,monto,monto_rendicion,observaciones,cod_estado,(select c.nombre from af_proveedores c where c.codigo=cod_proveedores)as cod_proveedores,nro_recibo
from caja_chicadetalle
where cod_cajachica=$cod_cajachica and cod_estadoreferencial=1 ORDER BY nro_documento desc");
$stmt->execute();
$stmt->bindColumn('codigo', $codigo_detalle_Cajachica);
$stmt->bindColumn('cod_cuenta', $cod_cuenta);
$stmt->bindColumn('nombre_cuenta', $nombre_cuenta);
$stmt->bindColumn('fecha_x', $fecha);
$stmt->bindColumn('cod_tipodoccajachica', $cod_tipodoccajachica);
$stmt->bindColumn('nombre_tipodoccajachica', $nombre_tipodoccajachica);
$stmt->bindColumn('nro_documento', $nro_documento);
$stmt->bindColumn('monto', $monto);
$stmt->bindColumn('monto_rendicion', $monto_rendicion);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('cod_proveedores', $cod_proveedores);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('cod_uo', $cod_uo);
$stmt->bindColumn('nro_recibo', $nro_recibo);
//listamos los reembolso de caja chica en curso
$stmtReembolso = $dbh->prepare("SELECT * from caja_chicareembolsos where cod_estadoreferencial=1 and cod_cajachica =$cod_cajachica");
$stmtReembolso->execute();
$stmtReembolso->bindColumn('codigo', $codigo_reembolso);
$stmtReembolso->bindColumn('monto', $monto_reembolso);
$stmtReembolso->bindColumn('fecha', $fecha_reembolso);
$stmtReembolso->bindColumn('cod_personal', $cod_personal_reembolso);
$stmtReembolso->bindColumn('observaciones', $observaciones_reembolso);

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
                      <label class="col-sm-1 col-form-label text-right" style="color:#0B2161;font-size: 11px"><b>Monto Inicial</b></label>
                      <div class="col-sm-2">
                          <div class="form-group">
                              <input style="background-color:#F3F781;text-align: center" class="form-control" readonly="readonly" value="<?=number_format($monto_cajachica, 2, '.', ',')?>" />
                          </div>
                      </div>
                      <label class="col-sm-1 col-form-label text-right" style="color:#0B2161;font-size: 11px"><b>Reembolso</b></label>
                      <div class="col-sm-2">
                      <div class="form-group">
                          <input style="background-color:#F3F781;text-align: center" class="form-control" name="numero" id="numero" value="<?=number_format($monto_reembolso_nuevo, 2, '.', ',')?>"  readonly="readonly"/>
                      </div>
                      </div>
                      <label class="col-sm-1 col-form-label text-right" style="color:#0B2161;font-size: 11px"><b>Saldo</b></label>
                      <div class="col-sm-2">
                      <div class="form-group">
                          <input style="background-color:#F3F781;text-align: center" class="form-control" name="numero" id="numero" value="<?=number_format($monto_saldo, 2, '.', ',')?>"  readonly="readonly"/>
                      </div>
                      </div>
                      <!-- <label class="col-sm-1 col-form-label text-right" style="color:#0B2161;font-size: 11px"><b>Fecha</b></label>
                      <div class="col-sm-2">
                      <div class="form-group">
                          <input style="background-color:#F3F781;text-align: center" class="form-control" name="numero" id="numero" value="<?=$fecha_cc?>"  readonly="readonly"/>
                      </div>
                      </div> -->
                      <label class="col-sm-1 col-form-label text-right" style="color:#0B2161;font-size: 11px"><b>Nro. Caja Chica</b></label>
                      <div class="col-sm-2">
                      <div class="form-group">
                          <input style="background-color:#F3F781;text-align: center" class="form-control" name="numero" id="numero" value="<?=$numero_cc?>"  readonly="readonly"/>
                      </div>
                      </div>
                  </div> <!--fin campo fecha numero-->

                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator50_2">
                      <thead>
                        <tr>
                          <th></th>
                          <th><small>#</small></th>
                          <th><small>Recibo</small></th>
                          <th><small>Cuenta</small></th>
                          <th >Fecha</small></th>
                          <th><small>Tipo</small></th>
                          <th><small>Entregado a</small></th>
                          <th><small>Monto</small></th>                          
                          <th><small>Monto Facturas</small></th> 
                          <!-- <th><small>Monto Devolución</small></th> -->
                          <th><small>Detalle</small></th>
                          <th><small>OF/Area</small></th>
                          <th width="6%"></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                        $index=1;
                        //listamos los reembolsos
                        while ($row = $stmtReembolso->fetch(PDO::FETCH_BOUND)) {
                          $nombre_personal_reembolso=namePersonal($cod_personal_reembolso);
                          ?>
                          <tr style="color: #b41010">
                            <td><small></small></td>
                            <td><small></small></td>
                            <td><small></small></td>
                            <td>-</td>
                            <td width="6%"><small><?=$fecha_reembolso;?></small></td>
                            <td>-</td>
                            <td><small><?=$nombre_personal_reembolso;?></small></td>
                            <td><small><?=number_format($monto_reembolso, 2, '.', ',');?></small></td>
                            <td>-</td>                                      
                            <td><small><?=$observaciones_reembolso;?></small></td>
                            <td>-</td>     
                            <td class="td-actions text-right">                                
                              <?php
                                if($globalAdmin==1){                                              
                              ?>                                                         
                                <a href='<?=$urlFormreembolsoCajaChica;?>&codigo=<?=$codigo_reembolso;?>&cod_tcc=<?=$cod_tcc?>&cod_cc=<?=$cod_cajachica?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteReembolsoCajaChica;?>&codigo=<?=$codigo_reembolso;?>&cod_tcc=<?=$cod_tcc?>&cod_cc=<?=$cod_cajachica?>')">
                                  <i class="material-icons" title="Borrar"><?=$iconDelete;?></i>
                                </button> 
                                <?php
                                  }
                                ?>
                              
                              </td>
                          </tr>
                        <?php $index++; } ?>
                        <?php $idFila=1;
                        // listamos gastos
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $abrevUnidad=abrevUnidad_solo($cod_uo);
                          $abrevArea=abrevArea_solo($cod_area);
                          // echo $monto."-".$monto_rendicion."/";
                          $cod_defecto_iva=obtenerValorConfiguracion(53);
                          if($cod_tipodoccajachica==$cod_defecto_iva){
                            if($monto_rendicion=='')$monto_rendicion=0;
                            if($monto==$monto_rendicion)
                              $labelM='<span class="badge badge-success">';                            
                            else
                              $labelM='<span class="badge badge-danger">';  
                          }else{
                            $labelM='<span>';  
                          }
                          
                            
                         ?>
                          <tr>
                            <td></td>
                            <td><small><?=$nro_documento;?></small></td>
                            <td style="background-color:  #d6dbdf " class="text-right"><small><?=$nro_recibo;?></small></td>
                            <td><small><?=$nombre_cuenta;?></small></td>
                            <td width="6%"><small><?=$fecha;?></small></td>
                              <td width="5%"><small><?=$nombre_tipodoccajachica;?></small></td>
                              <td><small><?=$cod_personal;?>/<?=$cod_proveedores?></small></td>        
                              <td><small><?=number_format($monto, 2, '.', ',');?></small></td>        
                              <td><small><?=$labelM.number_format($monto_rendicion, 2, '.', ',')."</span>";?></small></td>                                      
                              <td><small><?=$observaciones;?></small></td>                              
                              <td><small><?=$abrevUnidad;?>/<?=$abrevArea;?></small></td>     
                              <td class="td-actions text-right">
                                <script>var nfac=[];itemFacturasDCC.push(nfac);</script>
                              <?php
                                if($globalAdmin==1){
                                  $sqlDetalle="SELECT * FROM facturas_detalle_cajachica where cod_cajachicadetalle=$codigo_detalle_Cajachica
                                  union 
                                  SELECT * FROM detalle_cajachica_gastosdirectos where cod_cajachicadetalle=$codigo_detalle_Cajachica";
                                  // echo $sqlDetalle;
                                  $stmtFCCD = $dbh->prepare($sqlDetalle);
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
                                    $ice=$row['ice'];
                                    $tasa_cero=$row['tasa_cero'];
                                    ?><script>abrirFacturaDCC(<?=$idFila?>,'<?=trim($nit)?>',<?=trim($factura)?>,'<?=trim($fechaFac)?>','<?=trim($razon)?>',<?=trim($importe)?>,<?=trim($exento)?>,'<?=trim($autorizacion)?>','<?=trim($control)?>',<?=trim($ice)?>,<?=trim($tasa_cero)?>);</script><?php
                                  }
                                  // $cod_defecto_iva=obtenerValorConfiguracion(53);
                                  if($cod_tipodoccajachica==$cod_defecto_iva)//tipo retencios iva
                                  { ?>
                                    <a href='#' title="Facturas" id="boton_fac<?=$idFila;?>" class="btn btn-info" onclick="listFacDCC(<?=$idFila;?>,'<?=$fecha;?>','<?=$observaciones;?>',<?=$monto;?>,<?=$nro_documento;?>,<?=$codigo_detalle_Cajachica?>);">
                                      <i class="material-icons">featured_play_list</i>
                                      <span id="nfac<?=$idFila;?>" class="count bg-warning"></span>
                                    </a><?php 
                                  }
                                  ?> 
                                  <a href='#' title="Distribución de Gastos" class="btn btn-warning" onclick="listDistribuciones_cajachica(<?=$codigo_detalle_Cajachica?>);">
                                    <i class="material-icons">list</i>                                  
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
                        <?php $index++;$idFila=$idFila+1; } ?>
                        <!-- listamos remmbolsos -->
                        
                      </tbody>
                    
                    </table>
                  </div>
                </div>
              </div>
              
              <div class="card-footer fixed-bottom">
                <?php

              if($globalAdmin==1){
              ?>
                <button class="btn btn-success" onClick="location.href='<?=$urlFormDetalleCajaChica;?>&codigo=0&cod_tcc=<?=$cod_tcc?>&cod_cc=<?=$cod_cajachica?>'">Registrar Gastos</button>
                <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormreembolsoCajaChica;?>&codigo=0&cod_tcc=<?=$cod_tcc?>&cod_cc=<?=$cod_cajachica?>'">Registrar Reembolso</button>
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
  <div class="modal-dialog modal-notice" style="max-width: 80% !important;">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
              <div class="card ">
                <div class="card-header" id="divTituloCuentaDetalle">
                  <h4 class="card-title">DETALLE FACTURAS

                  </h4>
                  <div class="row" >
                      <label class="col-sm-1 col-form-label text-right"><b>Nro. Doc.</b></label>
                      <div class="col-sm-1">
                      <div class="form-group">
                          <input style="background-color:#ffffff;" class="form-control" name="nro_dcc" id="nro_dcc"  readonly="readonly"/>
                      </div>
                      </div>

                      <label class="col-sm-1 col-form-label text-right"><b>Detalle</b></label>
                      <div class="col-sm-3">
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
                          <a id="nav_boton4"class="nav-link" data-toggle="tab" href="#link113" role="tablist">
                            <span class="material-icons">add</span> Gasto Directo
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
                        <input class="form-control" type="hidden" name="cod_ccd" id="cod_ccd"/>
                        <input type="hidden" name="cantidad_filas_ccd" id="cantidad_filas_ccd">
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
                             <label class="col-sm-1 col-form-label" style="color: #4a148c;">NIT</label>
                             <div class="col-sm-3">
                              <div class="form-group">
                                <input class="form-control" type="text" name="nit_fac" id="nit_fac" required="true"/>
                              </div>
                              </div>
                              <label class="col-sm-1 col-form-label" style="color:#4a148c;">Nro. Factura</label>
                             <div class="col-sm-3">
                              <div class="form-group">
                                <input class="form-control" type="number" name="nro_fac" id="nro_fac" required="true"/>
                              </div>
                              </div>
                              <label class="col-sm-1 col-form-label" style="color:#4a148c;">Fecha</label>
                              <div class="col-sm-3">
                                <div class="form-group">
                                  <input type="text" class="form-control datepicker" name="fecha_fac" id="fecha_fac" value="<?=$fechaActualModal?>">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <label class="col-sm-1 col-form-label" style="color:#4a148c;">Importe</label>
                              <div class="col-sm-3">
                                <div class="form-group">
                                  <input class="form-control" type="number" name="imp_fac" id="imp_fac" required="true"/>
                                </div>
                              </div>
                              <label class="col-sm-1 col-form-label" style="color: #4a148c;">Exento</label>
                              <div class="col-sm-3">
                                <div class="form-group">                                 
                                  <input class="form-control" type="text" name="exe_fac" id="exe_fac" required="true" value="0" />
                                </div>
                              </div>
                              <label class="col-sm-1 col-form-label" style="color: #4a148c;">ICE</label>
                              <div class="col-sm-3">
                                <div class="form-group">                                 
                                  <input class="form-control" type="text" name="ice_fac" id="ice_fac" required="true" value="0" />
                                </div>
                              </div>
                            </div>                            
                            
                            <div class="row">
                              <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tasa Cero</label>
                              <div class="col-sm-3">
                                <div class="form-group">                              
                                  <input class="form-control" type="text" name="taza_fac" id="taza_fac" required="true" value="0" />
                                </div>
                              </div>
                              <label class="col-sm-1 col-form-label" style="color:#4a148c;">Autorizaci&oacute;n</label>
                              <div class="col-sm-3">
                                <div class="form-group">
                                  <input class="form-control" type="text" name="aut_fac" id="aut_fac" required="true"/>
                                </div>
                              </div>
                              <label class="col-sm-1 col-form-label" style="color:#4a148c;">Cod. Control</label>
                             <div class="col-sm-3">
                              <div class="form-group">
                                <input class="form-control" type="text" name="con_fac" id="con_fac" required="true"/>
                              </div>
                             </div>
                            </div>
                            <div class="row">

                              <label class="col-sm-1 col-form-label" style="color: #4a148c;">Razón Social</label>
                              <div class="col-sm-10">
                                <div class="form-group">
                                  <input type="text" class="form-control" name="razon_fac" id="razon_fac" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                </div>
                              </div>   
                            </div>
                          </div>
                        </div>
                      <div class="form-group float-right">
                        <button type="button" class="btn btn-info btn-round" onclick="saveFacturaDCC()">
                        Guardar</button>
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
                    <div class="tab-pane" id="link113">
                      <form name="form2">
                        <input class="form-control" type="hidden" name="codCuenta" id="codCuenta"/>
                        <div class="card" style="background: #e0e0e0">
                          <div class="card-body">
                            <div class="row">
                             <label class="col-sm-2 col-form-label" style="color: #4a148c;">Importe Del Gasto</label>
                             <div class="col-sm-3">
                              <div class="form-group">
                                <input class="form-control" type="text" name="importe_gasto" id="importe_gasto" required="true"/>
                              </div>
                              </div>                              
                            </div>
                          </div>
                        </div>
                      <div class="form-group float-right">
                        <button type="button" class="btn btn-info btn-round" onclick="saveImporteDirectoDCC()">
                        Guardar</button>
                      </div>
                         </form>
                    </div>
                  </div>
                </div>
              </div>
        

      </div>
      <div class="modal-footer justify-content-center">
        
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_distribuciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-success card-header-text">
          <div class="card-text">
            <h5>Distribución de Gastos <b id="titulo_distribucion"></b> </h5> 
          </div>
          <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
            <i class="material-icons">close</i>
          </button>
      </div>
          <div class="card-body">
            <div class="row col-sm-12">
              <div class="col-sm-6" id="contenedor_uo_distribucion">
                  
              </div>
              <div class="col-sm-6" id="contenedor_area_distribucion">
              </div> 
             </div>                     
             <div class="form-group float-right">

             </div>         
          </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalEditFac" tabindex="-1" role="dialog" style="z-index:99999"aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice modal-xl">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
              <div class="card ">
                <div class="card-header" id="divTituloCuentaDetalle">
                  <h4 class="card-title">Facturas -
                    <small class="description">Edicion</small>
                  </h4>
                </div>
                <div class="card-body ">
                        <input class="form-control" type="hidden" name="fila_fac" id="fila_fac"/>
                        <input class="form-control" type="hidden" name="indice_fac" id="indice_fac"/>
                        <div style="padding: 20px;">
                          <div class="row">                      
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">NIT</label>
                            <div class="col-sm-3">
                              <div class="form-group">  
                                <div id="">
                                  <input class="form-control" type="number" name="nit_fac_edit" id="nit_fac_edit" required="true">                        
                                </div>                                                                                                
                              </div>

                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Nro. Factura</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="">
                                  <!-- <label for="number" class="bmd-label-floating" style="color: #4a148c;">Nro. Factura</label>      -->
                                  <input class="form-control" type="number" name="nro_fac_edit" id="nro_fac_edit" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Fecha</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="fecha_fac" class="bmd-label-floating" style="color: #4a148c;">Fecha</label>      -->
                                <input type="date" class="form-control" name="fecha_fac_edit" id="fecha_fac_edit" value="<?=$fechaActualModal?>">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Importe</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="">
                                <input class="form-control" type="number" name="imp_fac_edit" id="imp_fac_edit" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Exento</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="exe_fac" class="bmd-label-floating" style="color: #4a148c;">Extento</label>      -->
                                <input class="form-control" type="text" name="exe_fac_edit" id="exe_fac_edit" required="true" value="0" />
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">ICE</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="ice_fac" class="bmd-label-floating" style="color: #4a148c;">ICE</label>      -->
                                <input class="form-control" type="text" name="ice_fac_edit" id="ice_fac_edit" required="true" value="0" />
                              </div>
                             </div>
                          </div>                                                                  
                          <!--No tiene funcion este campo-->
                          <div class="row">                                            
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tasa Cero</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="taza_fac" class="bmd-label-floating" style="color: #4a148c;">Taza Cero</label>      -->
                                <input class="form-control" type="text" name="taza_fac_edit" id="taza_fac_edit" required="true" value="0" />
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Autorizaci&oacute;n</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="">
                                <!-- <label for="aut_fac" class="bmd-label-floating" style="color: #4a148c;">Nro. Autorizaci&oacute;n</label>      -->
                                <input class="form-control" type="text" name="aut_fac_edit" id="aut_fac_edit" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Cod. Control</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="con_fac" class="bmd-label-floating" style="color: #4a148c;">Cod. Control</label>      -->
                                <input class="form-control" type="text" name="con_fac_edit" id="con_fac_edit" required="true"/>
                              </div>
                             </div>
                          </div> 
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tipo</label>
                            <div class="col-sm-2">
                              <div class="form-group">
                                <select class="selectpicker form-control form-control-sm" name="tipo_fac_edit" id="tipo_fac_edit" data-style="btn btn-primary">                                  
                                   <?php
                                         $stmt = $dbh->prepare("SELECT codigo, nombre FROM tipos_compra_facturas where cod_estadoreferencial=1");
                                       $stmt->execute();
                                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $codigoX=$row['codigo'];
                                        $nombreX=$row['nombre'];
                                        ?><option value="<?=$codigoX;?>"><?=$nombreX;?></option><?php
                                         }
                                     ?>
                                </select>
                              </div>
                            </div>                        
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Razón Social</label>
                            <div class="col-sm-8">
                              <div class="form-group" id="">                                
                                <input type="text" class="form-control" name="razon_fac_edit" id="razon_fac_edit">
                                
                              </div>
                            </div>   
                        </div>
                        
                          
                        </div>                     
                        <div class="form-group float-right">
                          <button type="button" class="btn btn-info btn-round" onclick="saveFacturaEdit_cajachica()">Guardar</button>
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