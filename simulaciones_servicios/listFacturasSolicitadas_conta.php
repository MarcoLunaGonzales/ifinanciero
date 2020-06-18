<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];
//datos registrado de la simulacion en curso

  $stmt = $dbh->prepare("SELECT sf.*,es.nombre as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo where cod_estadosolicitudfacturacion in (3,4,5) order by codigo desc");

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
  $stmt->bindColumn('obs_devolucion', $obs_devolucion);
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
                            <th><small>Of - Area</small></th>
                            <th><small>#Sol.</small></th>
                            <th><small>Responsable</small></th>
                            <th><small>Codigo<br>Servicio</small></th>                            
                            <th><small>Fecha<br>Registro</small></th>                            
                            <th style="color:#cc4545;"><small>#Fact</small></th>                            
                            <th><small>Importe<br>(BOB)</small></th>  
                            <th><small>Persona<br>Contacto</small></th>
                            <th width="15%"><small>Razón<br>Social</small></th>
                            <th width="35%"><small>Concepto</small></th>              
                            <!-- <th ><small>Estado</small></th>                             -->
                            <th width="15%"><small>Observaciones</small></th>
                            <th class="text-right"><small>Actions</small></th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          $codigo_fact_x=0;
                          $cont= array();
                          $cont_pagosParciales= array();
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {// para la parte de facturas parciales, items de sol_Fact
                            switch ($codEstado) {
                              case 1:                                
                                // $label='<span style="padding:1;" class="badge badge-default">';
                                $btnEstado="btn-default";
                              break;
                              case 2:                                
                                // $label='<span style="padding:1;" class="badge badge-danger">';
                                $btnEstado="btn-danger";
                              break;
                              case 3:                                
                                // $label='<span style="padding:1;" class="badge badge-success">';
                                $btnEstado="btn-success";
                              break;
                              case 4:                                
                                // $label='<span style="padding:1;" class="badge badge-warning">';
                                $btnEstado="btn-warning";
                              break;
                              case 5:                                
                                // $label='<span style="padding:1;" class="badge badge-warning">';
                                $btnEstado="btn-warning";
                              break;
                              case 6:                                
                                // $label='<span style="padding:1;" class="badge badge-default">';
                                $btnEstado="btn-default";
                              break;
                            }
                            //verificamos si ya tiene factura generada y esta activa                           
                            $stmtFact = $dbh->prepare("SELECT codigo,nro_factura,cod_estadofactura,razon_social,nit,nro_autorizacion,importe from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura in (1,4)");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];
                            $nro_fact_x = $resultSimu['nro_factura'];
                            $cod_estado_factura_x = $resultSimu['cod_estadofactura'];
                            if ($nro_fact_x==null)$nro_fact_x="-";
                            else $nro_fact_x="F".$nro_fact_x;
                            if($cod_estado_factura_x==4){
                              $btnEstado="btn-warning";
                              $estado="FACTURA MANUAL";                            
                            }
                            //sacamos monto total de la factura para ver si es de tipo factura por pagos
                            $sqlMontos="SELECT codigo,importe,nro_factura,cod_estadofactura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura in (1,4) ORDER BY codigo desc";
                            // echo $sqlMontos;
                            $stmtFactMontoTotal = $dbh->prepare($sqlMontos);
                            $stmtFactMontoTotal->execute();
                            $importe_fact_x=0;$cont_facturas=0;$cadenaFacturas="";$cadenaFacturasM="";$cadenaCodFacturas="";
                            while ($row_montos = $stmtFactMontoTotal->fetch()){
                              $cod_estadofactura=$row_montos['cod_estadofactura'];
                              if($cod_estadofactura==4){
                                $btnEstado="btn-warning";
                                $estado="FACTURA MANUAL";
                                $cadenaFacturasM.="FM".$row_montos['nro_factura'].",";
                              }else{
                                $cadenaFacturas.="F".$row_montos['nro_factura'].",";  
                              }
                              $importe_fact_x+=$row_montos['importe'];
                              
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
                              $concepto_contabilizacion.=$descripcion_alterna." / ".trim($cadenaFacturas,',').",".trim($cadenaFacturasM,",")." / ".$razon_social."<br>\n";
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
                            if($importe_fact_x!=$sumaTotalImporte && $cod_estado_factura_x!=4){ //para los items de la factura a pagos
                              ?>
                              <script>var nfac=[];itemGenerar_factura_parcial.push(nfac);</script>
                              <?php
                                $queryParciales = "SELECT codigo,cantidad,descuento_bob,precio,cod_claservicio,descripcion_alterna from solicitudes_facturaciondetalle where cod_solicitudfacturacion=$codigo_facturacion";
                                $statementParciales = $dbh->query($queryParciales);
                                $nc_parciales=0;
                                while ($row = $statementParciales->fetch()){ 
                                  $cod_claservicio=$row['cod_claservicio'];
                                  //busacmos el monto ya pagado;

                                  $cadenaCodFacturas_x=trim($cadenaCodFacturas,',');
                                  $sqlMontoFact="SELECT sum(precio) as precio_x from facturas_ventadetalle where cod_facturaventa in ($cadenaCodFacturas_x) and cod_claservicio=$cod_claservicio";
                                  // echo $sqlMontoFact;
                                  $stmtFactMontoFacturado = $dbh->prepare($sqlMontoFact);
                                  $stmtFactMontoFacturado->execute();
                                  $resultMontoFAC = $stmtFactMontoFacturado->fetch();
                                  $importe_facturato = $resultMontoFAC['precio_x'];
                                  // echo "importe:".$importe_facturato;
                                  // echo $importe_facturato;
                                  //objeto dato donde guarda tipos de pago
                                  $dato_parcial = new stdClass();//obejto
                                  $codFila=(int)$cod_claservicio;                                  
                                  $cantidad_x=trim($row['cantidad']);
                                  $precio_x=trim($row['precio']);
                                  $descuento_x=trim($row['descuento_bob']);
                                  $descripcion_x=trim($row['descripcion_alterna']);
                                  $dato_parcial->codigo=($nc_parciales+1);
                                  $dato_parcial->cod_claservicio=$codFila;
                                  $dato_parcial->preciox=$precio_x;
                                  $dato_parcial->cantidadxx=$cantidad_x;
                                  $dato_parcial->descuentox=$descuento_x;
                                  if($importe_fact_x!=0)$dato_parcial->importe_anterior_x=$importe_facturato;
                                  else $dato_parcial->importe_anterior_x=0;
                                  $dato_parcial->descripcionx=$descripcion_x;                
                                  $dato_parciales[$index-1][$nc_parciales]=$dato_parcial;                           
                                  $nc_parciales++;
                                } 
                                $cont_pagosParciales[$index-1]=$nc_parciales;
                              $saldo=0;

                              $saldo=$sumaTotalImporte-$importe_fact_x;
                              $datos_FacManual=$codigo_facturacion."/0/".$saldo."/".$index."/".$nit."/".$razon_social;//dato para modal
                              // if($importe_fact_x!=null){
                              //   $saldo=$sumaTotalImporte-$importe_fact_x;                                
                              //   $datos_FacManual=$codigo_facturacion."/0/".$saldo."/".$index."/".$nit."/".$razon_social;//dato para modal
                              // }else{                                
                              //   $datos_FacManual=$codigo_facturacion."/0/0/".$index."/".$nit."/".$razon_social;//dato para modal
                              // }
                              if($cont_facturas>1){                              
                                $estado="FACTURA PARCIAL";
                                $nro_fact_x=trim($cadenaFacturas,',');
                              }
                              $cadenaFacturasM=trim($cadenaFacturasM,',');
                              ?>
                              <tr>
                                <td><small><?=$nombre_uo;?> - <?=$nombre_area;?></small></td>
                                <td class="text-right"><small><?=$nro_correlativo;?></small></td>
                                <td class="text-left"><small><?=$responsable;?></small></td>
                                <td class="text-left"><small><?=$codigo_alterno?></small></td>
                                <td><small><?=$fecha_registro;?></small></td>
                                <!-- <td><?=$fecha_solicitudfactura;?></td>          -->                   
                                <td style="color:#298A08;"><small><?=$nro_fact_x;?><br><span style="color:#DF0101;"><?=$cadenaFacturasM;?></span></small></td>
                                <td class="text-right"><small><?=formatNumberDec($sumaTotalImporte);?></small></td>
                                <td class="text-left"><small><?=$nombre_contacto;?></small></td>
                                <td class="text-left"><small><small><?=$razon_social;?></small></small></td>
                                <td class="text-left"><small><?=$concepto_contabilizacion?></small></td>
                                <!-- <td><?=$label?><small><?=$estado;?></small></span></td> -->
                                <td><button class="btn btn-danger btn-sm btn-link" style="padding:0;"><small><?=$obs_devolucion;?></small></button></td>
                                <td class="td-actions text-right">
                                  <button class="btn <?=$btnEstado?> btn-sm btn-link" style="padding:0;"><small><?=$estado;?></small></button><br>
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
                                                  $cod_tipopago_deposito_cuenta=obtenerValorConfiguracion(55);                                                    
                                                    if($cod_tipopago==$cod_tipopago_deposito_cuenta){//si es deposito en cuenta se activa la libreta bancaria?>
                                                      <!-- <a href='#' title="Generar Factura Parcial" class="dropdown-item" onclick="abrirLibretaBancaria('<?=$datos_FacManual;?>','<?=$urlGenerarFacturas2;?>','2')">
                                                        <i class="material-icons text-info">receipt</i> <span style="color: #FF0000;">Generar Factura Parcial</span>
                                                      </a> -->
                                                       <a href='#' title="Generar Factura Manual" class="dropdown-item" onclick="abrirLibretaBancaria('<?=$datos_FacManual;?>','<?=$urlGenerarFacturas2;?>','3')">
                                                        <i class="material-icons text-info">receipt</i>Generar Factura Manual
                                                      </a>  
                                                    <?php }else{?>                                                   
                                                       <!-- <button title="Generar Factura Parcial" class="dropdown-item" type="button" data-toggle="modal" data-target="#modalGenerarFacturapagos" onclick="agregaDatosGenerarFactPagos('<?=$datos_FacManual;?>')">
                                                        <i class="material-icons text-info">receipt</i><span style="color: #FF0000;">Generar Factura Parcial</span>
                                                       </button> -->
                                                       <button title="Generar Factura Manual" class="dropdown-item" type="button" data-toggle="modal" data-target="#modalFacturaManual" onclick="agregaDatosFactManual('<?=$datos_FacManual;?>')">
                                                        <i class="material-icons text-info">receipt</i> Generar Factura Manual
                                                       </button><?php      
                                                      }
                                                    ?>
                                                <a href='#' rel="tooltip" class="dropdown-item" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                                                  <i class="material-icons text-warning" title="Ver Detalle">settings_applications</i> Ver Detalle
                                                </a>
                                            </div>
                                        </div>                                     
                                        <?php 
                                      }else{// generar facturas
                                        if($codEstado==3 ){ ?>                                          
                                          <div class="btn-group dropdown">
                                            <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                               <small><?=$estado;?></small>
                                            </button>
                                            <div class="dropdown-menu">
                                              <?php                                                  
                                                $cod_tipopago_deposito_cuenta=obtenerValorConfiguracion(55);                                                
                                                if($cod_tipopago==$cod_tipopago_deposito_cuenta){//si es deposito en cuenta se activa la libreta bancaria?>
                                                  <a href='#' title="Generar Factura Total" class="dropdown-item" onclick="abrirLibretaBancaria('<?=$datos_FacManual;?>','<?=$urlGenerarFacturas2;?>','1')">
                                                    <i class="material-icons text-success">receipt</i> Generar Factura Total
                                                  </a>
                                                  <!-- <a href='#' title="Generar Factura Parcial" class="dropdown-item" onclick="abrirLibretaBancaria('<?=$datos_FacManual;?>','<?=$urlGenerarFacturas2;?>','2')">
                                                    <i class="material-icons text-info">receipt</i><span style="color: #FF0000;">Generar Factura Parcial</span>
                                                  </a> -->
                                                  <a href='#' title="Generar Factura Manual" class="dropdown-item" onclick="abrirLibretaBancaria('<?=$datos_FacManual;?>','<?=$urlGenerarFacturas2;?>','3')">
                                                    <i class="material-icons text-info">receipt</i>Generar Factura Manual
                                                  </a><?php 
                                                }else{?>
                                                  <a href='#' title="Generar Factura Total" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmation-generar-factura','<?=$urlGenerarFacturas2;?>?codigo=<?=$codigo_facturacion;?>')">
                                                    <i class="material-icons text-success">receipt</i> Generar Factura Total
                                                  </a>
                                                  <!-- <button title="Generar Factura Parcial" class="dropdown-item" type="button" data-toggle="modal" data-target="#modalGenerarFacturapagos" onclick="agregaDatosGenerarFactPagos('<?=$datos_FacManual;?>')">
                                                    <i class="material-icons text-info">receipt</i><span style="color: #FF0000;">Generar Factura Parcial</span>
                                                  </button> -->
                                                  <button title="Generar Factura Manual" class="dropdown-item" type="button" data-toggle="modal" data-target="#modalFacturaManual" onclick="agregaDatosFactManual('<?=$datos_FacManual;?>')">
                                                    <i class="material-icons text-info">receipt</i> Generar Factura Manual
                                                  </button><?php 
                                                } ?>
                                                <a href='#' rel="tooltip" class="dropdown-item" onclick="filaTablaAGeneral($('#tablasA_registradas'),<?=$index?>,'<?=$stringCabecera?>')">
                                                  <i class="material-icons text-warning" title="Ver Detalle">settings_applications</i> Ver Detalle
                                                </a>
                                            </div>
                                          </div>                           
                                          <?php 
                                        }else{
                                          if($codEstado==6 || $codEstado==4){
                                            // $cod_tipopago_cred=obtenerValorConfiguracion(48);
                                            // echo $cod_tipopago_cred; 
                                            // if($cod_tipopago!=$cod_tipopago_cred){//si es distino a credito cambia de flujo
                                                ?>
                                                 <a title="Aceptar Solicitud" href='#'  class="btn btn-default" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=3&admin=0')">
                                                   <i class="material-icons">send</i>
                                                 </a>
                                                 <?php
                                                 $datos_devolucion=$codigo_facturacion."###".$nro_correlativo."###".$codigo_alterno."###1###10###".$urlEdit2Sol."###";
                                                 ?>
                                                 <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalDevolverSolicitud" onclick="modalDevolverSolicitud('<?=$datos_devolucion;?>')">
                                                  <i class="material-icons" title="Devolver Solicitud Facturación">settings_backup_restore</i>
                                                </button>
                                                <?php                                          
                                            // }else{
                                                ?>
                                                 <!-- <a title="Enviar Solicitud" href='<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=4&admin=0'  class="btn btn-default">
                                                   <i class="material-icons">send</i>
                                                 </a> -->
                                                <?php                                          
                                            // }
                                            
                                              ?> 

                                              
                                              <!-- <a title="Volver al Estado Registro" href='<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=1&admin=10'  class="btn btn-danger">
                                                 <i class="material-icons">settings_backup_restore</i>
                                              </a> -->

                                              <?php                                          
                                          }
                                          ?>                                          
                                          <?php
                                        }
                                      }?> 
                                      <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir">print</i></a>                           
                                    <?php }
                                  ?>
                                </td>
                              </tr>
                            <?php }else{
                              $index--;
                            }
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
<?php  require_once 'simulaciones_servicios/modal_facturacion.php';?>
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
      var cod_solicitudfacturacion_factmanual=document.getElementById("cod_solicitudfacturacion_factmanual").value;
      var cod_libreta_manual=document.getElementById("cod_libreta_manual").value;

      var nro_factura=$('#nro_factura').val();
      var nro_autorizacion=$('#nro_autorizacion').val();
      var nit_cliente=$('#nit_cliente').val();
      var razon_social=$('#razon_social').val();
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
                RegistrarFacturaManual(cod_solicitudfacturacion_factmanual,nro_factura,nro_autorizacion,fecha_factura,nit_cliente,razon_social,cod_libreta_manual);
              }          
            }          
          }          
        }
      }      
    });
    $('#rechazarSolicitud').click(function(){
      var q=0;var s=0;var u=0;var v=0;
      var cod_solicitudfacturacion=document.getElementById("cod_solicitudfacturacion").value;
      var estado=document.getElementById("estado").value;
      var admin=document.getElementById("admin").value;
      var direccion=document.getElementById("direccion").value;
      var observaciones=$('#observaciones').val();
      if(observaciones==null || observaciones==0 || observaciones=='' || observaciones==' '){
        Swal.fire("Informativo!", "Por favor introduzca la observación.", "warning");
      }else{        
        registrarRechazoSolicitud(cod_solicitudfacturacion,observaciones,estado,admin,direccion,q,s,u,v);
      }      
    }); 
  });
  function valida_modalFacPar(f) {
      var ok = true;
      calcular_monto_total_items_factura_parcial();
      if(f.elements["total_importe_pagar"].value == 0 || f.elements["total_importe_pagar"].value < 0 || f.elements["total_importe_pagar"].value == '')
      {
        var msg = "El Monto Total a pagar no debe ser 0 o Nulo...\n";      
        ok = false;
      }      
      if(f.elements["total_importe_anterior"].value!=0){
        var importe_anterior=f.elements["total_importe_anterior"].value;
        var importe=f.elements["total_importe"].value;
        var saldo=parseFloat(importe)-parseFloat(importe_anterior);
        // alert(saldo);

        if(parseFloat(f.elements["total_importe_pagar"].value)>saldo)
        {
          var msg = "El Monto Total a pagar es Superior al total del Saldo anterior ("+number_format(saldo,2)+") ...\n";      
          ok = false;
        }
      }else{        
        var total_importe_pagar =f.elements["total_importe_pagar"].value;
        var total_importe =f.elements["total_importe"].value;
        // alert(total_importe_pagar+"-"+total_importe);
        if(parseFloat(total_importe_pagar) > parseFloat(total_importe))
        {
          var msg = "El Monto Total a pagar es Superior al total del importe de la solicitud ("+number_format(total_importe_pagar,2)+">"+number_format(total_importe,2)+")...\n";
          ok = false;
        }  
      }      
      if(ok == false)    
        Swal.fire("Informativo!",msg, "warning");
      return ok;
    }
</script>
<!-- objeto tipo de pago -->
<?php 
    $lan_parciales=sizeof($cont_pagosParciales);//filas si lo hubiese         
    // echo "cont:".$lan_parciales;
    // var_dump($dato_parciales[2]);
    for ($i=0; $i < $lan_parciales; $i++) {
      // echo "i:".$i."<br>";
      ?>
      <script>var detalle_pagoparcial=[];</script>
      <?php      
        for ($j=0; $j < $cont_pagosParciales[$i]; $j++) {

             if($cont_pagosParciales[$i]>0){?>
                <script>
                    detalle_pagoparcial.push({codigo:<?=$dato_parciales[$i][$j]->codigo?>,codigox:<?=$dato_parciales[$i][$j]->cod_claservicio?>,preciox:'<?=$dato_parciales[$i][$j]->preciox?>',cantidadxx:'<?=$dato_parciales[$i][$j]->cantidadxx?>',descuentox:'<?=$dato_parciales[$i][$j]->descuentox?>',importe_anterior_x:'<?=$dato_parciales[$i][$j]->importe_anterior_x?>',descripcionx:'<?=$dato_parciales[$i][$j]->descripcionx?>'});
                    // console.log(detalle_pagoparcial);
                </script>

              <?php
              }          
            }
        ?><script>itemGenerar_factura_parcial_aux.push(detalle_pagoparcial);
        </script><?php                    
    }
?>
