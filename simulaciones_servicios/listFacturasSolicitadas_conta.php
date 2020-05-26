<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];
//datos registrado de la simulacion en curso

  $stmt = $dbh->prepare("SELECT sf.*,es.nombre as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo where (cod_estadosolicitudfacturacion=3 or cod_estadosolicitudfacturacion=6 or cod_estadosolicitudfacturacion=5 ) order by codigo desc");

  $stmt->execute();
  $stmt->bindColumn('codigo', $codigo_facturacion);
  $stmt->bindColumn('cod_simulacion_servicio', $cod_simulacion_servicio);
  $stmt->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
  $stmt->bindColumn('cod_area', $cod_area);  
  $stmt->bindColumn('fecha_registro_x', $fecha_registro);
  $stmt->bindColumn('fecha_solicitudfactura_x', $fecha_solicitudfactura);
  $stmt->bindColumn('cod_tipoobjeto', $cod_tipoobjeto);
  $stmt->bindColumn('cod_tipopago', $cod_tipopago);
  $stmt->bindColumn('cod_cliente', $cod_cliente);
  $stmt->bindColumn('cod_personal', $cod_personal);
  $stmt->bindColumn('razon_social', $razon_social);
  $stmt->bindColumn('nit', $nit);
  $stmt->bindColumn('observaciones', $observaciones);
  $stmt->bindColumn('cod_estadosolicitudfacturacion', $codEstado);
  $stmt->bindColumn('estado', $estado);
  $stmt->bindColumn('nro_correlativo', $nro_correlativo);
  $stmt->bindColumn('persona_contacto', $persona_contacto);
  $stmt->bindColumn('codigo_alterno', $codigo_alterno);
  $stmt->bindColumn('tipo_solicitud', $tipo_solicitud);//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas
  ?>
  <div class="content">
    <div class="container-fluid">
          <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header card-header-warning card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">polymer</i>
                    </div>
                    <h4 class="card-title"><b>Solicitudes de Facturación</b></h4>                    
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <th>Of - Area</th>
                            <th>#Sol.</th>
                            <th>Responsable</th>
                            <th>Codigo<br>Servicio</th>                            
                            <th>Fecha<br>Registro</th>                            
                            <th style="color:#cc4545;">#Fact</th>                            
                            <th>Importe<br>(BOB)</th>  
                            <th>Persona<br>Contacto</th>                              
                            <th>Concepto</th>              
                            <th width="5%">Estado</th>                            
                            <th class="text-right">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          $codigo_fact_x=0;
                          $cont= array();
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                            


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
                                $btnEstado="btn-warning";
                              break;
                              case 6:
                                $btnEstado="btn-default";
                              break;
                            }
                            //verificamos si ya tiene factura generada y esta activa                           
                            $stmtFact = $dbh->prepare("SELECT codigo,nro_factura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura=1 ORDER BY codigo desc");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];
                            $nro_fact_x = $resultSimu['nro_factura'];                            
                            if ($nro_fact_x==null)$nro_fact_x="-";

                            $stmtFactMontoTotal = $dbh->prepare("SELECT SUM(importe) as importe from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura=1 ORDER BY codigo desc");
                            $stmtFactMontoTotal->execute();
                            $resultMontoTotalFAC = $stmtFactMontoTotal->fetch();
                            $importe_fact_x = $resultMontoTotalFAC['importe'];
                            
                            //sacamos nombre de los detalles
                            $stmtDetalleSol = $dbh->prepare("SELECT cantidad,precio,descripcion_alterna from solicitudes_facturaciondetalle where cod_solicitudfacturacion=$codigo_facturacion");
                            $stmtDetalleSol->execute();
                            $stmtDetalleSol->bindColumn('cantidad', $cantidad);  
                            $stmtDetalleSol->bindColumn('precio', $precio);     
                            $stmtDetalleSol->bindColumn('descripcion_alterna', $descripcion_alterna);                              
                            $concepto_contabilizacion=$codigo_alterno." - ";
                            while ($row_det = $stmtDetalleSol->fetch()){
                              $precio_natural=$precio/$cantidad;
                              $concepto_contabilizacion.=$descripcion_alterna." / F ".$nro_fact_x." / ".$razon_social."<br>\n";
                              $concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_natural)." = ".formatNumberDec($precio)."<br>\n";
                            }
                            $concepto_contabilizacion = (substr($concepto_contabilizacion, 0, 100))."..."; //limite de string
                            $cod_area_simulacion=$cod_area;
                            $nombre_simulacion='OTROS';
                            if($tipo_solicitud==1){// la solicitud pertence tcp-tcs
                              //obtenemos datos de la simulacion TCP
                              $sql="SELECT sc.nombre,ps.cod_area,ps.cod_unidadorganizacional
                              from simulaciones_servicios sc,plantillas_servicios ps
                              where sc.cod_plantillaservicio=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion_servicio";                            
                              $stmtSimu = $dbh->prepare($sql);
                              $stmtSimu->execute();
                              $resultSimu = $stmtSimu->fetch();
                              $nombre_simulacion = $resultSimu['nombre'];
                              $cod_area_simulacion = $resultSimu['cod_area'];
                            }elseif($tipo_solicitud==2){//  pertence capacitacion
                              $sqlCostos="SELECT sc.nombre,sc.cod_responsable,ps.cod_area,ps.cod_unidadorganizacional
                              from simulaciones_costos sc,plantillas_servicios ps
                              where sc.cod_plantillacosto=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion_servicio order by sc.codigo";
                              $stmtSimuCostos = $dbh->prepare($sqlCostos);
                              $stmtSimuCostos->execute();
                              $resultSimu = $stmtSimuCostos->fetch();
                              $nombre_simulacion = $resultSimu['nombre'];
                              $cod_area_simulacion = $resultSimu['cod_area'];
                            }elseif($tipo_solicitud==3){// pertence a propuestas y servicios
                              $sqlCostos="SELECT Descripcion,IdArea,IdOficina from servicios s where s.IdServicio=$cod_simulacion_servicio";
                              $stmtSimuCostos = $dbh->prepare($sqlCostos);
                              $stmtSimuCostos->execute();
                              $resultSimu = $stmtSimuCostos->fetch();
                              $nombre_simulacion = $resultSimu['Descripcion'];
                              $cod_area_simulacion = $resultSimu['IdArea'];
                            }
                            $name_area_simulacion=trim(abrevArea($cod_area_simulacion),'-');
                            // --------
                            $responsable=namePersonal($cod_personal);//nombre del personal
                            $nombre_contacto=nameContacto($persona_contacto);//nombre del personal
                            $nombre_area=trim(abrevArea($cod_area),'-');//nombre del area
                            $nombre_uo=trim(abrevUnidad($cod_unidadorganizacional),' - ');//nombre de la oficina

                            //los registros de la factura
                            $dbh1 = new Conexion();
                            $sqlA="SELECT sf.*,(select t.Descripcion from cla_servicios t where t.IdClaServicio=sf.cod_claservicio) as nombre_serv from solicitudes_facturaciondetalle sf where sf.cod_solicitudfacturacion=$codigo_facturacion";
                            $stmt2 = $dbh1->prepare($sqlA);                                   
                            $stmt2->execute(); 
                            $nc=0;
                            $sumaTotalMonto=0;
                            $sumaTotalDescuento_por=0;
                            $sumaTotalDescuento_bob=0;

                            while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                              $dato = new stdClass();//obejto
                              $codFila=(int)$row2['codigo'];
                              $cod_claservicioX=trim($row2['nombre_serv']);
                              $cantidadX=trim($row2['cantidad']);
                              $precioX=trim($row2['precio'])+trim($row2['descuento_bob']);
                              $descuento_porX=trim($row2['descuento_por']);
                              $descuento_bobX=trim($row2['descuento_bob']);                             
                              $descripcion_alternaX=trim($row2['descripcion_alterna']);
                              $dato->codigo=($nc+1);
                              $dato->cod_facturacion=$codFila;
                              $dato->serviciox=$cod_claservicioX;
                              $dato->cantidadX=$cantidadX;
                              $dato->precioX=$precioX;
                              $dato->descuento_porX=$descuento_porX;
                              $dato->descuento_bobX=$descuento_bobX;
                              $dato->descripcion_alternaX=$descripcion_alternaX;
                              $datos[$index-1][$nc]=$dato;                           
                              $nc++;
                              $sumaTotalMonto+=$precioX;
                              $sumaTotalDescuento_por+=$descuento_porX;
                              $sumaTotalDescuento_bob+=$descuento_bobX;
                            }
                            $sumaTotalImporte=$sumaTotalMonto-$sumaTotalDescuento_bob;
                            $cont[$index-1]=$nc;
                            $stringCabecera=$nombre_uo."##".$nombre_area."##".$nombre_simulacion."##".$name_area_simulacion."##".$fecha_registro."##".$fecha_solicitudfactura."##".$nit."##".$razon_social;                            
                            if($importe_fact_x!=$sumaTotalImporte){
                              if($importe_fact_x!=null){
                                $saldo=$sumaTotalImporte-$importe_fact_x;
                                $datos_FacManual=$codigo_facturacion."/".$sumaTotalImporte."/".$saldo;//dato para modal
                                $estado="FACTURADO A PAGOS";
                              }else{
                                $datos_FacManual=$codigo_facturacion."/".$sumaTotalImporte."/0";//dato para modal
                              }

                              ?>
                              <tr>
                                <td><small><?=$nombre_uo;?> - <?=$nombre_area;?></small></td>
                                <td class="text-right"><small><?=$nro_correlativo;?></small></td>
                                <td><small><?=$responsable;?></small></td>
                                <td><small><?=$codigo_alterno?></small></td>
                                <td><small><?=$fecha_registro;?></small></td>
                                <!-- <td><?=$fecha_solicitudfactura;?></td>          -->                   
                                <td style="color:#cc4545;"><small><?=$nro_fact_x;?></small></td>                             
                                <td class="text-right"><small><?=formatNumberDec($sumaTotalImporte);?></small></td>
                                <td class="text-left"><small><?=$nombre_contacto;?></small></td>
                                <!-- <td><?=$razon_social;?></td> -->                            
                                <td width="35%"><small><?=$concepto_contabilizacion?></small></td>
                                <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><small><?=$estado;?></small></button></td>
                                <td class="td-actions text-right">
                                  <?php
                                    if($globalAdmin==1){ //
                                      if($codigo_fact_x>0){//print facturas
                                        ?>
                                        <div class="btn-group dropdown">
                                            <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                               <small><?=$estado;?></small>
                                            </button>
                                            <div class="dropdown-menu">
                                              <?php                                            
                                                  // if($codEstado==3){
                                                   ?>
                                                   <!-- <a href='#' title="Generar Factura" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmation-generar-factura','<?=$urlGenerarFacturas2;?>?codigo=<?=$codigo_facturacion;?>')">
                                                    <i class="material-icons text-success">receipt</i> Generar Factura
                                                   </a> -->
                                                   <button title="Generar Factura Manual" class="dropdown-item" type="button" data-toggle="modal" data-target="#modalFacturaManual" onclick="agregaDatosFactManual('<?=$datos_FacManual;?>')">
                                                    <i class="material-icons text-info">receipt</i> Generar Factura Manual
                                                   </button>
                                                   <button title="Generar Factura a Pagos" class="dropdown-item" type="button" data-toggle="modal" data-target="#modalGenerarFacturapagos" onclick="agregaDatosFactPagos('<?=$datos_FacManual;?>')">
                                                    <i class="material-icons text-info">receipt</i> Generar Factura a Pagos
                                                   </button>
                                                   <?php      
                                                  // }
                                                ?>
                                                <a href='#' rel="tooltip" class="dropdown-item" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                                                  <i class="material-icons text-warning" title="Ver Detalle">settings_applications</i> Ver Detalle
                                                </a>
                                            </div>
                                        </div>                                     
                                        <?php 
                                      }else{// generar facturas
                                        if($codEstado==3  ){ ?>
                                          <!--      <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a> -->
                                          <?php
                                          ?>
                                          <div class="btn-group dropdown">
                                            <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                               <small><?=$estado;?></small>
                                            </button>
                                            <div class="dropdown-menu">
                                              <?php                                            
                                                  if($codEstado==3){
                                                   ?>
                                                   <a href='#' title="Generar Factura" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmation-generar-factura','<?=$urlGenerarFacturas2;?>?codigo=<?=$codigo_facturacion;?>')">
                                                    <i class="material-icons text-success">receipt</i> Generar Factura
                                                   </a>
                                                   <button title="Generar Factura Manual" class="dropdown-item" type="button" data-toggle="modal" data-target="#modalFacturaManual" onclick="agregaDatosFactManual('<?=$datos_FacManual;?>')">
                                                    <i class="material-icons text-info">receipt</i> Generar Factura Manual
                                                   </button>
                                                   <button title="Generar Factura a Pagos" class="dropdown-item" type="button" data-toggle="modal" data-target="#modalGenerarFacturapagos" onclick="agregaDatosFactPagos('<?=$datos_FacManual;?>')">
                                                    <i class="material-icons text-info">receipt</i> Generar Factura a Pagos
                                                   </button>
                                                   <?php      
                                                  }
                                                ?>
                                                <a href='#' rel="tooltip" class="dropdown-item" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                                                  <i class="material-icons text-warning" title="Ver Detalle">settings_applications</i> Ver Detalle
                                                </a>
                                            </div>
                                          </div>                           
                                          <?php 
                                        }else{
                                          if($codEstado==6){
                                            $cod_tipopago_cred=obtenerValorConfiguracion(48);
                                            // echo $cod_tipopago_cred; 
                                            if($cod_tipopago!=$cod_tipopago_cred){//si es distino a credito cambia de flujo
                                                ?>
                                                 <a title="Aceptar Solicitud" href='#'  class="btn btn-default" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=3&admin=0')">
                                                   <i class="material-icons">send</i>
                                                 </a>
                                                <?php                                          
                                            }else{
                                                ?>
                                                 <a title="Enviar Solicitud" href='<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=4&admin=0'  class="btn btn-default">
                                                   <i class="material-icons">send</i>
                                                 </a>
                                                <?php                                          
                                            }
                                              ?>                                        
                                              <a title="Volver al Estado Registro" href='<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=1&admin=0'  class="btn btn-danger">
                                                 <i class="material-icons">refresh</i>
                                              </a>
                                              <?php                                          
                                          }
                                          ?>
                                          <!-- <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir">print</i></a> -->
                                          <!--editar solicitud facturacion-->
                                          <?php
                                        }
                                      }?> 
                                      <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir">print</i></a>                           
                                    <?php }
                                  ?>
                                </td>
                              </tr>
                            <?php }
                            ?>                        
                          <?php
                              $index++;
                            }
                          ?>
                        </tbody>
                      </table>
                  </div>
                  <div class="card-footer fixed-bottom col-sm-9">
                    <a href='<?=$urlListHistoricoContabilidad;?>' class="btn btn-info float-right"><i class="material-icons">history</i> Histórico</a>
                  </div>    
                </div>     
              </div>
          </div>  
    </div>
  </div>
<!-- small modal -->
<div class="modal fade modal-primary" id="modalDetalleFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
              <div class="card-header card-header-warning card-header-icon">
                <div class="card-icon">
                  <i class="material-icons">settings_applications</i>
                </div>
                <h4 class="card-title">Detalle Solicitud</h4>
              </div>

              <div class="card-body">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="material-icons">close</i>
              </button>
              <div class="row" id="div_cabecera" >
                    
              </div>
                <table class="table table-condensed">
                  <thead>
                    <tr class="text-dark bg-plomo">
                    <th>#</th>
                    <th>Item</th>
                    <th>Cantidad</th>
                    <!-- <th>Precio(BOB)</th>  
                      <th>Desc(%)</th> 
                      <th>Desc(BOB)</th>  -->
                      <th width="10%">Importe(BOB)</th> 
                      <th width="45%">Glosa</th>                   
                    </tr>
                  </thead>
                  <tbody id="tablasA_registradas">
                    
                  </tbody>
                </table>
              </div>
    </div>  
  </div>
</div>
<!--    end small modal -->
<!-- FActura manual-->
<div class="modal fade" id="modalFacturaManual" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b>Factura Manual</b></h3>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_solicitudfacturacion_factmanual" id="cod_solicitudfacturacion_factmanual" value="0">
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Numero de Factura: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" name="nro_factura" id="nro_factura" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Nro de Autorización: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" name="nro_autorizacion" id="nro_autorizacion" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">        
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Fecha de Factura </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="date" name="fecha_factura" id="fecha_factura" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Nit Cliente </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" name="nit_cliente" id="nit_cliente" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Razón Social </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="razon_social" id="razon_social" class="form-control">
            </div>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="guardarFacturaManual" name="guardarFacturaManual">Agregar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalGenerarFacturapagos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b>Factura A Pagos</b></h3>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_solicitudfacturacion_factpagos" id="cod_solicitudfacturacion_factpagos" value="0">
        
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Importe De Solicitud de Facturación</label>
          <div class="col-sm-3">
            <div class="form-group">
              <input type="number" name="monto_sol_fact" id="monto_sol_fact" value="0" readonly="true" class="form-control" style="background-color:#E3CEF6;text-align: left">            
            </div>
          </div>
          <label class="col-sm-1 text-right col-form-label" style="color:#424242">Saldo Anterior</label>
          <div class="col-sm-2">
            <div class="form-group">
              
              <input type="number" name="saldo_anterior" id="saldo_anterior" value="0" readonly="true" class="form-control" style="background-color:#E3CEC8;text-align: left">            
            </div>
          </div>
          <label class="col-sm-1 text-right col-form-label" style="color:#424242">Saldo Nuevo</label>
          <div class="col-sm-2">
            <div class="form-group">
              <input type="number" name="saldo_a_pagar" id="saldo_a_pagar" value="0" readonly="true" class="form-control" style="background-color:#E3CEC8;text-align: left">                
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 text-right col-form-label" style="color:#424242">Porcentaje a Pagar</label>
          <div class="col-sm-4">
            <div class="form-group">
              <input type="number" step="0.01" name="porcentaje_pagar" id="porcentaje_pagar" class="form-control" onkeyup="monto_convertir_a_bolivianos_factPagos()">
            </div>
          </div>
          <label class="col-sm-2 text-right col-form-label" style="color:#424242">Monto a pagar</label>
          <div class="col-sm-4">
            <div class="form-group">
              <input type="number" step="0.01" name="monto_pagar" id="monto_pagar" class="form-control" onkeyup="monto_convertir_a_porcentaje_factPagos()">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="guardarFacturaPagos" name="guardarFacturaPagos">Generar Factura</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>



<?php 
  $lan=sizeof($cont);
  error_reporting(0);
  for ($i=0; $i < $lan; $i++) {
    ?>
    <script>var detalle_fac=[];</script>
    <?php
       for ($j=0; $j < $cont[$i]; $j++) {     
           if($cont[$i]>0){
            ?><script>detalle_fac.push({codigo:<?=$datos[$i][$j]->codigo?>,cod_facturacion:<?=$datos[$i][$j]->cod_facturacion?>,serviciox:'<?=$datos[$i][$j]->serviciox?>',cantidadX:'<?=$datos[$i][$j]->cantidadX?>',precioX:'<?=$datos[$i][$j]->precioX?>',descuento_porX:'<?=$datos[$i][$j]->descuento_porX?>',descuento_bobX:'<?=$datos[$i][$j]->descuento_bobX?>',descripcion_alternaX:'<?=$datos[$i][$j]->descripcion_alternaX?>'});</script><?php         
            }          
          }
      ?><script>detalle_tabla_general.push(detalle_fac);</script><?php                    
  }
?>

<!-- para la factura manual -->
<script type="text/javascript">
  $(document).ready(function(){
    $('#guardarFacturaManual').click(function(){    
      cod_solicitudfacturacion_factmanual=document.getElementById("cod_solicitudfacturacion_factmanual").value;
      nro_factura=$('#nro_factura').val();
      nro_autorizacion=$('#nro_autorizacion').val();
      nit_cliente=$('#nit_cliente').val();
      razon_social=$('#razon_social').val();
      
      fecha_factura=$('#fecha_factura').val();
      if(nro_factura==null || nro_factura<=0){
        Swal.fire("Informativo!", "Por favor introduzca el Número de Factura.", "warning");
      }else{
        if(nro_autorizacion==null || nro_autorizacion<=0){
          Swal.fire("Informativo!", "Por favor introduzca el Número de Autorización.", "warning");
        }else{
          if(fecha_factura==null || fecha_factura=='' || fecha_factura==' '){
            Swal.fire("Informativo!", "Por favor introduzca la Fecha de Factura", "warning");
          }else{
            if(nit_cliente==null || nit_cliente=='' || nit_cliente==' '){
              Swal.fire("Informativo!", "Por favor introduzca el Nit del cliente", "warning");
            }else{
              if(razon_social==null || razon_social=='' || razon_social==' '){
                Swal.fire("Informativo!", "Por favor introduzca la Razón Social", "warning");
              }else{
                RegistrarFacturaManual(cod_solicitudfacturacion_factmanual,nro_factura,nro_autorizacion,fecha_factura,nit_cliente,razon_social);
              }          
            }          
          }          
        }
      }      
    });
    $('#guardarFacturaPagos').click(function(){    
      cod_solicitudfacturacion_factpagos=document.getElementById("cod_solicitudfacturacion_factpagos").value;
      saldo_anterior=document.getElementById("saldo_anterior").value;
      porcentaje_pagar=$('#porcentaje_pagar').val();
      monto_pagar=$('#monto_pagar').val();
      if(porcentaje_pagar==null || porcentaje_pagar<=0){
        Swal.fire("Informativo!", "Porcentaje a Pagar incorrecto.", "warning");
      }else{
        if(monto_pagar==null || monto_pagar<=0){
          Swal.fire("Informativo!", "Monto a Pagar incorrecto.", "warning");
        }else{
          if(monto_pagar>saldo_anterior){
            Swal.fire("Informativo!", "El monto a Pagar es mayor al Saldo Anterior.", "warning");
          }else{
            RegistrarFacturaPagos(cod_solicitudfacturacion_factpagos,porcentaje_pagar,monto_pagar);    
          }
        }
      }      
    });    
  });
</script>

  