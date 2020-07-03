<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];
//datos registrado de la simulacion en curso

  $stmt = $dbh->prepare("SELECT sf.*,es.nombre as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo where cod_estadosolicitudfacturacion=5 order by codigo desc limit 50");

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
  $stmt->bindColumn('observaciones_2', $observaciones_2);
  $stmt->bindColumn('cod_estadosolicitudfacturacion', $codEstado);
  $stmt->bindColumn('estado', $estado);
  $stmt->bindColumn('nro_correlativo', $nro_correlativo);
  $stmt->bindColumn('persona_contacto', $persona_contacto);
  $stmt->bindColumn('codigo_alterno', $codigo_alterno);
  $stmt->bindColumn('obs_devolucion', $obs_devolucion);
  $stmt->bindColumn('tipo_solicitud', $tipo_solicitud);//1 tcp - 2 capacitacion - 3 servicios - 4 manual - 5 venta de normas



  // busquena por Oficina
$stmtUO = $dbh->prepare("SELECT cod_unidadorganizacional,(select u.nombre from unidades_organizacionales u where u.codigo =cod_unidadorganizacional)as nombre, (select u.abreviatura from unidades_organizacionales u where u.codigo =cod_unidadorganizacional)as abreviatura FROM solicitudes_facturacion where cod_estadosolicitudfacturacion=5 GROUP BY nombre");
$stmtUO->execute();
$stmtUO->bindColumn('cod_unidadorganizacional', $codigo_uo);
$stmtUO->bindColumn('nombre', $nombre_uo);
$stmtUO->bindColumn('abreviatura', $abreviatura_uo);


$stmtCliente = $dbh->prepare("
SELECT cod_cliente,(SELECT c.nombre from  clientes c where c.codigo=cod_cliente) as nombre from solicitudes_facturacion where cod_estadosolicitudfacturacion=5 GROUP BY nombre");
$stmtCliente->execute();
$stmtCliente->bindColumn('cod_cliente', $codigo_cli);
$stmtCliente->bindColumn('nombre', $nombre_cli);


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
                    <h4 class="card-title"><b>Historial Solicitudes de Facturación </b></h4>                    
                  </div>
                  <div class="row">
                      <div class="col-sm-12">
                          <div class="form-group" align="right">
                              <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscador">
                                  <i class="material-icons" title="Buscador Avanzado">search</i>
                              </button>                               
                          </div>
                      </div>
                  </div>
                  <div class="card-body">
                    <div id="data_solicitudes_facturacion">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <th><small>Of - Area</small></th>
                            <th><small>#Sol.</small></th>
                            <th><small>Responsable</small></th>
                            <th><small>Codigo<br>Servicio</small></th>                            
                            <th><small>Fecha<br>Registro</small></th>
                            <th><small>Importe<br>(BOB)</small></th>                              
                            <th width="15%"><small>Razón Social</small></th>
                            <th width="35%"><small>Concepto</small></th>                            
                            <th width="12%"><small>Observaciones</small></th>
                            <th style="color:#ff0000;"><small>#Fact</small></th>
                            <th style="color:#ff0000;" width="6%"><small>Forma<br>Pago</small></th>
                            <th class="text-right"><small>Actions</small></th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php                          
                          $index=1;
                          $codigo_fact_x=0;
                          $cont= array();
                          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                            $observaciones_string=obtener_string_observaciones($obs_devolucion,$observaciones,$observaciones_2);
                            $datos_otros=$codigo_facturacion."/0/0/0/".$nit."/".$razon_social;//dato 
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
                            $stmtFact = $dbh->prepare("SELECT codigo,nro_factura,cod_estadofactura,razon_social,nit,nro_autorizacion,importe,cod_comprobante from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion and cod_estadofactura in (1,4)");
                            $stmtFact->execute();
                            $resultSimu = $stmtFact->fetch();
                            $codigo_fact_x = $resultSimu['codigo'];
                            $nro_fact_x = $resultSimu['nro_factura'];
                            $cod_estado_factura_x = $resultSimu['cod_estadofactura'];
                            $nit_x = $resultSimu['nit'];
                            $razon_social_x = $resultSimu['razon_social'];
                            $nro_autorizacion_x = $resultSimu['nro_autorizacion'];
                            $importe_x = $resultSimu['importe'];
                            $cod_comprobante_x = $resultSimu['cod_comprobante'];
                            if ($nro_fact_x==null)$nro_fact_x="-";
                            else $nro_fact_x="F".$nro_fact_x;
                            if($cod_estado_factura_x==4){
                              $btnEstado="btn-warning";
                              $estado="FACTURA MANUAL";
                              $cliente_x=nameCliente($cod_cliente);                              
                              $datos_FacManual=$cliente_x."/".$razon_social_x."/".$nit_x."/".$nro_fact_x."/".$nro_autorizacion_x."/".$importe_x;
                            }
                            //sacamos monto total de la factura para ver si es de tipo factura por pagos
                            $sqlMontos="SELECT codigo,importe,nro_factura,cod_estadofactura from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion ORDER BY codigo desc";
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
                              }elseif($cod_estadofactura==2){
                                $cadenaFacturas.="FA".$row_montos['nro_factura'].",";  
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
                            $stmtDetalleSol->bindColumn('precio', $precio_unitario);     
                            $stmtDetalleSol->bindColumn('descripcion_alterna', $descripcion_alterna);
                            if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
                              $concepto_contabilizacion="";
                            }else{
                              $concepto_contabilizacion=$codigo_alterno." - ";  
                            }

                            
                            while ($row_det = $stmtDetalleSol->fetch()){
                              $precio=$precio_unitario*$cantidad;
                              $concepto_contabilizacion.=$descripcion_alterna." / F ".$nro_fact_x." / ".$razon_social."<br>\n";
                              $concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_unitario)." = ".formatNumberDec($precio)."<br>\n";
                            }
                            $concepto_contabilizacion = (substr($concepto_contabilizacion, 0, 100))."..."; //limite de string
                            
                            // $cod_area_simulacion=$cod_area;
                            // $nombre_simulacion='OTROS';
                            // if($tipo_solicitud==1){// la solicitud pertence tcp-tcs
                            //   //obtenemos datos de la simulacion TCP
                            //   $sql="SELECT sc.nombre,ps.cod_area,ps.cod_unidadorganizacional
                            //   from simulaciones_servicios sc,plantillas_servicios ps
                            //   where sc.cod_plantillaservicio=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion_servicio";                            
                            //   $stmtSimu = $dbh->prepare($sql);
                            //   $stmtSimu->execute();
                            //   $resultSimu = $stmtSimu->fetch();
                            //   $nombre_simulacion = $resultSimu['nombre'];
                            //   $cod_area_simulacion = $resultSimu['cod_area'];
                            // }elseif($tipo_solicitud==2){//  pertence capacitacion
                            //   $sqlCostos="SELECT sc.nombre,sc.cod_responsable,ps.cod_area,ps.cod_unidadorganizacional
                            //   from simulaciones_costos sc,plantillas_servicios ps
                            //   where sc.cod_plantillacosto=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion_servicio order by sc.codigo";
                            //   $stmtSimuCostos = $dbh->prepare($sqlCostos);
                            //   $stmtSimuCostos->execute();
                            //   $resultSimu = $stmtSimuCostos->fetch();
                            //   $nombre_simulacion = $resultSimu['nombre'];
                            //   $cod_area_simulacion = $resultSimu['cod_area'];
                            // }elseif($tipo_solicitud==3){// pertence a propuestas y servicios
                            //   $sqlCostos="SELECT Descripcion,IdArea,IdOficina from servicios s where s.IdServicio=$cod_simulacion_servicio";
                            //   $stmtSimuCostos = $dbh->prepare($sqlCostos);
                            //   $stmtSimuCostos->execute();
                            //   $resultSimu = $stmtSimuCostos->fetch();
                            //   $nombre_simulacion = $resultSimu['Descripcion'];
                            //   $cod_area_simulacion = $resultSimu['IdArea'];
                            // }

                            // $name_area_simulacion=trim(abrevArea($cod_area_simulacion),'-');

                            // --------
                            $responsable=namePersonal($cod_personal);//nombre del personal
                            // $nombre_tipopago=nameTipoPagoSolFac($cod_tipopago);//
                            $string_formaspago=obtnerFormasPago($codigo_facturacion);
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
                              // $dato = new stdClass();//obejto
                              $codFila=(int)$row2['codigo'];
                              $cod_claservicioX=trim($row2['nombre_serv']);
                              $cantidadX=trim($row2['cantidad']);
                              // $precioX=trim($row2['precio'])+trim($row2['descuento_bob']);
                              $precioX=(trim($row2['precio'])*$cantidadX)+trim($row2['descuento_bob']);
                              $descuento_porX=trim($row2['descuento_por']);
                              $descuento_bobX=trim($row2['descuento_bob']);                             
                              $descripcion_alternaX=trim($row2['descripcion_alterna']);
                              $nc++;
                              $sumaTotalMonto+=$precioX;
                              $sumaTotalDescuento_por+=$descuento_porX;
                              $sumaTotalDescuento_bob+=$descuento_bobX;
                            }
                            $sumaTotalImporte=$sumaTotalMonto-$sumaTotalDescuento_bob;
                            if($cont_facturas>1){                              
                                $estado="FACTURA PARCIAL";
                                $nro_fact_x=trim($cadenaFacturas,',');
                            }
                            $cadenaFacturasM=trim($cadenaFacturasM,',');
                            ?>
                          <tr>
                            <td><small><?=$nombre_uo;?> - <?=$nombre_area;?></small></td>
                            <td class="text-right"><small><?=$nro_correlativo;?></small></td>
                            <td><small><?=$responsable;?></small></td>
                            <td><small><?=$codigo_alterno?></small></td>
                            <td><small><?=$fecha_registro;?></small></td>
                            <td class="text-right"><small><?=formatNumberDec($sumaTotalImporte);?></small></td>                            
                            <td><small><small><?=$razon_social;?></small></small></td>
                            <td><small><small><?=$concepto_contabilizacion?></small></small></td>
                            <td>
                              <?php if($cod_estado_factura_x==3){
                                  $estadofactura=obtener_nombreestado_factura($cod_estadofactura);?>
                                  <span class="badge badge-dark"><small><?=$estadofactura?></small></span><?php
                              }else{?><small><?=$observaciones_string;?></small><?php 
                              }?>
                            </td>
                            <td style="color:#298A08;"><small><?=$nro_fact_x;?><br><span style="color:#DF0101;"><?=$cadenaFacturasM;?></span></small></td>
                            <td class="text-left" style="color:#ff0000;"><small><small><?=$string_formaspago;?></small></small></td>
                            <td class="td-actions text-right"><button class="btn <?=$btnEstado?> btn-sm btn-link"><small><?=$estado;?></small></button><br>
                              <?php
                                if($globalAdmin==1){ //
                                  if($codigo_fact_x>0 && $cod_estado_factura_x==1 && $cont_facturas<2){//print facturas
                                    ?>
                                    <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>
                                    <a href="<?=$urlImp;?>?comp=<?=$cod_comprobante_x;?>&mon=1" target="_blank" class="btn" style="background-color:#3f33ff">
                                      <i class="material-icons" title="Imprimir Comprobante">print</i>
                                    <?php 
                                  }elseif($cont_facturas>1){?>
                                    <div class="btn-group dropdown">
                                      <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><small>Facturas</small></button>
                                      <div class="dropdown-menu"><?php 
                                        $arrayCodFacturas = explode(",",trim($cadenaCodFacturas,','));
                                        $arrayFacturas = explode(",",trim($cadenaFacturas,','));                                        
                                        for ($i=0; $i < $cont_facturas; $i++) { $cod_factura_x= $arrayCodFacturas[$i];$nro_factura_x= $arrayFacturas[$i];?>
                                          <a class="dropdown-item" type="button" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$cod_factura_x;?>&tipo=1' target="_blank"><i class="material-icons text-success" title="Imprimir Factura">print</i> Factura <?=$i+1;?> - Nro <?=$nro_factura_x?></a>
                                          <?php 

                                        }?>
                                      </div>
                                    </div> <?php 
                                  }elseif($cod_estado_factura_x==4){//factura manual ?>
                                    <button title="Detalles Factura Manual" class="btn btn-success" type="button" data-toggle="modal" data-target="#modalDetalleFacturaManual" onclick="agregaDatosDetalleFactManual('<?=$datos_FacManual;?>')">
                                      <i class="material-icons">list</i>
                                    </button> <?php 
                                  }?>
                                    <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir">print</i></a>
                                      <a href='#' title="Archivos Adjuntos" class="btn btn-primary" onclick="abrirArchivosAdjuntos('<?=$datos_otros;?>')"><i class="material-icons" ><?=$iconFile?></i></a>
                                <?php
                                }
                              ?>
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
                  <div class="card-footer fixed-bottom col-sm-9">
                    <a href='<?=$urlListSolicitudContabilidad;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                  </div>    
                </div>                   
                </div>     
              </div>
          </div>  
    </div>
  </div>
  <?php  require_once 'simulaciones_servicios/modal_subir_archivos.php';?>

  <!-- Modal busqueda -->
<div class="modal fade" id="modalBuscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Buscador de Solicitudes de Facturación</h4>
      </div>
      <div class="modal-body ">
        <div class="row">
            <label class="col-sm-3 col-form-label text-center">Oficina</label> 
            <label class="col-sm-6 col-form-label text-center">Fechas</label>                  
            <label class="col-sm-3 col-form-label text-center">Cliente</label>                                
        </div> 
        <div class="row">
          <div class="form-group col-sm-3">

            <select  name="OficinaBusqueda[]" id="OficinaBusqueda" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple>               
              <?php while ($rowUO = $stmtUO->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_uo;?>"> <?=$nombre_uo;?></option>
              <?php }?>
            </select>
          </div>
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaInicio" id="fechaBusquedaInicio">
          </div>
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaFin" id="fechaBusquedaFin">
          </div>
          <div class="form-group col-sm-3">            
            <select name="cliente[]" id="cliente" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple>               
              <?php while ($rowTC = $stmtCliente->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_cli;?>"> <?=$nombre_cli;?></option>
              <?php }?>
            </select>
            
          </div>              
        </div> 
        <!-- <div class="row">
          <label class="col-sm-3 col-form-label text-center">Razón Social</label> 
          <div class="form-group col-sm-8">
            <input class="form-control input-sm" type="text" name="glosaBusqueda" id="glosaBusqueda"  >
          </div>           
        </div>  -->

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="botonBuscarComprobante" name="botonBuscarComprobante" onclick="botonBuscarSolicitudes_conta()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>
<!-- small modal -->
<!-- modal detalle de facturac manuales -->
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
              <input type="text" name="cliente_facmanual" id="cliente_facmanual" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Numero de Factura: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="nro_factura_facmanual" id="nro_factura_facmanual" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Nro de Autorización: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" name="nro_autorizacion_facmanual" id="nro_autorizacion_facmanual" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Nit Cliente </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="number" name="nit_cliente_facmanual" id="nit_cliente_facmanual" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Razón Social </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="razon_social_facmanual" id="razon_social_facmanual" readonly="true" style="background-color:#D8CEF6;" class="form-control">
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-3 text-right col-form-label" style="color:#424242">Importe</label>
          <div class="col-sm-8">
            <div class="form-group">
              <input type="text" name="importe_facmanual" id="importe_facmanual" readonly="true" style="background-color:#D8CEF6;" class="form-control">
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
