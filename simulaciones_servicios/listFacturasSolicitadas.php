<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];
  $s=$_GET['s'];
  $u=$_GET['u'];
}

//datos registrado de la simulacion en curso
$sqlDatos="SELECT sf.*,es.nombre as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo order by codigo desc";
  $stmt = $dbh->prepare($sqlDatos);

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


  // $fecha_actual_cH=date('Y-m-d H:i:s');
  
  // $fecha_actual=date($fecha_actual_cH, strtotime("Y-m-d")); // gives 201101
  // echo $fecha_actual;
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
                            <th style="color:#cc4545;">#Fact.</th>                            
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
                                $label='<span class="badge badge-default">';
                              break;
                              case 2:                                
                                $label='<span class="badge badge-danger">';
                              break;
                              case 3:                                
                                $label='<span class="badge badge-success">';
                              break;
                              case 4:                                
                                $label='<span class="badge badge-warning">';
                              break;
                              case 5:                                
                                $label='<span class="badge badge-warning">';
                              break;
                              case 6:                                
                                $label='<span class="badge badge-default">';
                              break;
                            }
                            //verificamos si ya tiene factura generada y esta activa                           
                            $stmtFact = $dbh->prepare("SELECT codigo,nro_factura,cod_estadofactura,razon_social,nit,nro_autorizacion,importe from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura in (1,4)");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];
                            $nro_fact_x = $resultSimu['nro_factura'];
                            $cod_estado_factura_x = $resultSimu['cod_estadofactura'];
                            $nit_x = $resultSimu['nit'];
                            $razon_social_x = $resultSimu['razon_social'];
                            $nro_autorizacion_x = $resultSimu['nro_autorizacion'];
                            $importe_x = $resultSimu['importe'];
                            if ($nro_fact_x==null)$nro_fact_x="-";
                            if($cod_estado_factura_x==4){
                              // $btnEstado="btn-warning";
                              $label='<span class="badge badge-warning">';
                              $estado="FACTURA MANUAL";

                              $cliente_x=nameCliente($cod_cliente);                              
                              $datos_FacManual=$cliente_x."/".$razon_social_x."/".$nit_x."/".$nro_fact_x."/".$nro_autorizacion_x."/".$importe_x;
                            }

                            //sacamos monto total de la factura para ver si es de tipo factura por pagos
                            $sqlMontos="SELECT codigo,importe,nro_factura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura=1 ORDER BY codigo desc";
                            // echo $sqlMontos;
                            $stmtFactMontoTotal = $dbh->prepare($sqlMontos);
                            $stmtFactMontoTotal->execute();
                            $importe_fact_x=0;$cont_facturas=0;$cadenaFacturas="";$cadenaCodFacturas="";
                            while ($row_montos = $stmtFactMontoTotal->fetch()){
                              $importe_fact_x+=$row_montos['importe'];
                              $cadenaFacturas.=$row_montos['nro_factura']." - ";
                              $cadenaCodFacturas.=$row_montos['codigo'].",";
                              $cont_facturas++;
                            }                       
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
                            $cod_area_simulacion=$cod_area;
                            $nombre_simulacion='OTROS';
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
                            

                            if($cont_facturas>1){                              
                              $estado="FACTURA <br>PARCIAL";
                              $nro_fact_x=trim($cadenaFacturas,' - ');
                            }

                          ?>
                          <tr>
                            <!-- <td align="center"><?=$index;?></td> -->
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
                            <td><?=$label?><small><?=$estado;?></small></span></td>
                            <td class="td-actions text-right">
                              <?php                              
                                if($cod_estado_factura_x==1 || $cod_estado_factura_x==null){
                                  if($codigo_fact_x>0){//print facturas
                                    if($cont_facturas<2){
                                      ?>
                                      <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>          
                                     <?php               
                                    }elseif($cont_facturas>1){?>
                                      <div class="btn-group dropdown">
                                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><small>PAGOS</small></button>
                                        <div class="dropdown-menu"><?php 
                                          $arrayCodFacturas = explode(",",trim($cadenaCodFacturas,','));
                                          $arrayFacturas = explode(" - ",trim($cadenaFacturas,' - '));
                                          for ($i=0; $i < $cont_facturas; $i++) { $cod_factura_x= $arrayCodFacturas[$i];$nro_factura_x= $arrayFacturas[$i];?>
                                            <a class="dropdown-item" type="button" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$cod_factura_x;?>&tipo=1' target="_blank"><i class="material-icons text-success" title="Imprimir Factura">print</i> Factura <?=$i+1;?> - Nro <?=$nro_factura_x?></a>                                        
                                            <?php 

                                          }?>
                                        </div>
                                      </div> <?php 
                                    }
                                  }else{// generar facturas
                                    if($codEstado==4||$codEstado==3||$codEstado==5){                                   
                                      ?>
                                      <div class="btn-group dropdown">
                                        <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                          <small> <?=$estado;?></small>
                                        </button>
                                        <div class="dropdown-menu">
                                        <?php 
                                        if(isset($_GET['q'])){
                                          if($codEstado==4){
                                             ?>
                                             <a href="<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion;?>&estado=1&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="dropdown-item">
                                                <i class="material-icons text-danger">clear</i> Cancelar solicitud
                                             </a>
                                             <?php 
                                          }else{
                                            //if($codEstado==3){
                                             ?>
                                             <!-- <a href='#' title="Generar Factura" target="_blank" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmation-generar-factura','<?=$urlGenerarFacturas2;?>?codigo=<?=$codigo_facturacion;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>')">
                                              <i class="material-icons text-success">receipt</i> Generar Factura
                                             </a> -->
                                             <?php      
                                            //}
                                          }
                                          ?>
                                          <a href='#' rel="tooltip" class="dropdown-item" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                                            <i class="material-icons text-warning" title="Ver Detalle">settings_applications</i> Ver Detalle
                                          </a><?php 
                                        }else{
                                          if($codEstado==4){
                                           ?><a href="<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion;?>&estado=1&admin=0" class="dropdown-item">
                                              <i class="material-icons text-danger">clear</i> Cancelar solicitud
                                           </a><?php 
                                          }else{
                                            //if($codEstado==3){
                                             ?>
                                             <!-- <a href='#' title="Generar Factura" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmation-generar-factura','<?=$urlGenerarFacturas2;?>?codigo=<?=$codigo_facturacion;?>')">
                                              <i class="material-icons text-success">receipt</i> Generar Factura
                                             </a> -->
                                             <?php      
                                            //}
                                          }
                                          ?>
                                          <a href='#' rel="tooltip" class="dropdown-item" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                                              <i class="material-icons text-warning" title="Ver Detalle">settings_applications</i> Ver Detalle
                                          </a><?php  
                                        } ?>       
                                      </div>
                                      <?php 
                                    }else{
                                      if($codEstado==6){
                                        $cod_tipopago_cred=obtenerValorConfiguracion(48);
                                        // echo $cod_tipopago_cred; 
                                        if($cod_tipopago!=$cod_tipopago_cred){//si es distino a credito cambia de flujo
                                        }else{
                                          if(isset($_GET['q'])){
                                            ?>
                                             <a title="Enviar solicitud" href='<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=4&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>'  class="btn btn-warning">
                                               <i class="material-icons">send</i>
                                             </a>
                                             <a title="Volver al Estado Registro" href='<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=1&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>'  class="btn btn-danger">
                                               <i class="material-icons">refresh</i>
                                             </a>
                                            <?php
                                          }else{
                                            ?>
                                             <a title="Enviar solicitud" href='<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=4&admin=0'  class="btn btn-warning">
                                               <i class="material-icons">send</i>
                                             </a>
                                             <a title="Volver al Estado Registro" href='<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=1&admin=0'  class="btn btn-danger">
                                               <i class="material-icons">refresh</i>
                                             </a>
                                              <?php
                                          }  
                                        }
                                      }else{
                                        if($codEstado!=2){
                                          if(isset($_GET['q'])){ ?>
                                             <a title="Pre Envio - Solicitud Facturaciรณn" href='<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=6&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>' class="btn btn-default">
                                               <i class="material-icons">send</i>
                                             </a>
                                            <?php
                                          }else{
                                            ?>
                                             <a title="Pre Envio - Solicitud Facturaciรณn" href='<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=6&admin=0'  class="btn btn-default">
                                               <i class="material-icons">send</i>
                                             </a>
                                            <?php
                                          }                                           
                                        }
                                      }
                                    }
                                  }
                                }elseif($cod_estado_factura_x==4){//factura manual ?>
                                  <button title="Detalles" class="btn btn-success" type="button" data-toggle="modal" data-target="#modalDetalleFacturaManual" onclick="agregaDatosDetalleFactManual('<?=$datos_FacManual;?>')">
                                    <i class="material-icons">list</i>
                                  </button> <?php 
                                }
                              ?>
                               <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a>
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
                  <?php                 
                    if(isset($_GET['q'])){?>
                      <a href="<?=$urlRegister_solicitudfacturacion_manual;?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>" class="btn btn-primary">Solicitud Fact Manual</a>
                      <a href="<?=$urlListSolicitud_facturacion_normas;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&r=<?=$r?>" class="btn btn-warning">Solicitud Fact Normas</a><?php 
                    }else{?>
                      <a href="<?=$urlRegister_solicitudfacturacion_manual;?>" class="btn btn-primary">Solicitud Fact Manual</a>
                      <a href="<?=$urlListSolicitud_facturacion_normas;?>" class="btn btn-warning">Solicitud Fact Normas</a><?php 
                    }              
                  ?>
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
<div class="modal fade" id="modalDetalleFacturaManual" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b>Detalle Factura Manual</b></h3>
      </div>
      <div class="modal-body">        
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Cliente</label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="cliente_x" id="cliente_x" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Numero de Factura: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" name="nro_factura" id="nro_factura" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Nro de Autorización: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" name="nro_autorizacion" id="nro_autorizacion" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Nit Cliente </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" name="nit_cliente" id="nit_cliente" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Razón Social </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="razon_social" id="razon_social" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Importe</label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="importe" id="importe" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-success" id="guardarFacturaManual" name="guardarFacturaManual">Agregar</button> -->
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->

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


  