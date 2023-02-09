<?php

require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$url_list_siat=obtenerValorConfiguracion(103);

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$cod_uo=$_GET['cod_uo'];
$cliente=$_GET['cliente'];
$fechaI=$_GET['fechaI'];
$fechaF=$_GET['fechaF'];
$razon_social_b=$_GET['razon_social'];
$nro_s=$_GET['nro_s'];

$sql="SELECT sf.*,es.nombre as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x,
  (select st.abreviatura from siat_tipos_documentoidentidad st where st.codigo=sf.siat_tipoidentificacion)as abrevTipoDoc, sf.siat_complemento  FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo where cod_estadosolicitudfacturacion=5 ";  

if($cod_uo!=""){
  $sql.=" and sf.cod_unidadorganizacional in ($cod_uo)";
}
if($cliente!=""){
  $sql.=" and sf.cod_cliente in ($cliente)";  
}
if($fechaI!="" && $fechaF!=""){
  $sql.=" and sf.fecha_solicitudfactura BETWEEN '$fechaI' and '$fechaF'"; 
}
if($razon_social_b!=""){
  $sql.=" and sf.razon_social like '%$razon_social_b%'";  
}
if($nro_s!=""){
  $sql.=" and sf.nro_correlativo = $nro_s";  
}


$sql.=" order by sf.codigo;";

//echo $sql;
$stmt = $dbh->prepare($sql);
 
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
  $stmt->bindColumn('siat_tipoidentificacion', $siatTipoDocIdentificacion);
  $stmt->bindColumn('abrevTipoDoc', $siatTipoDocAbrev);
  $stmt->bindColumn('siat_complemento', $siatComplemento);

?>
 <table class="table" id="">
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
              <tbody >
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
                      $btnEstado="btn-info";
                    break;
                    case 5:
                      $btnEstado="btn-warning";
                    break;
                    case 6:
                      $btnEstado="btn-default";
                    break;
                  }

                  if($siatComplemento!=""){
                    $siatComplemento="<span style='color:red'><b>-".$siatComplemento."</b></span>";
                  }
                  $datosFacturacion=$razon_social."<br>"."<span style='color:red'>".$siatTipoDocAbrev."-</span><span style='color:blue'>".$nit."</span>".$siatComplemento;



                  //verificamos si ya tiene factura generada y esta activa                           
                  $stmtFact = $dbh->prepare("SELECT codigo,nro_factura,cod_estadofactura,razon_social,nit,nro_autorizacion,importe,cod_comprobante, idTransaccion_siat from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion order by codigo desc limit 1");
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
                  $idTransaccionSiat = $resultSimu['idTransaccion_siat'];

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
                      // $cadenaFacturasM.="FM".$row_montos['nro_factura'].",";
                      $cadenaFacturas.="FM".$row_montos['nro_factura'].",";
                      $cadenaCodFacturas.="0,";
                    }elseif($cod_estadofactura==2){
                      $cadenaFacturas.="FA".$row_montos['nro_factura'].",";                        
                      $cadenaCodFacturas.="0,";
                    }else{
                      $cadenaFacturas.="F".$row_montos['nro_factura'].",";                          
                      $cadenaCodFacturas.=$row_montos['codigo'].",";
                    }
                    $importe_fact_x+=$row_montos['importe'];
                    $cont_facturas++;
                  }
                  // $cadenaFacturas.=$cadenaFacturasM;
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
                 
                  $responsable=namePersonal($cod_personal);//nombre del personal
                  
                  $string_formaspago=obtnerFormasPago($codigo_facturacion);
                  $nombre_area=trim(abrevArea($cod_area),'-');//nombre del area
                  $nombre_uo=trim(abrevUnidad($cod_unidadorganizacional),' - ');//nombre de la oficina

                  $sumaTotalImporte=obtenerSumaTotal_solicitudFacturacion($codigo_facturacion);
                  if($cont_facturas>1){                              
                      // $estado="FACTURA PARCIAL";
                      $nro_fact_x=trim($cadenaFacturas,',');
                  }                  
                  ?>
                  <tr>
                    <td><small><?=$nombre_uo;?> - <?=$nombre_area;?></small></td>
                    <td class="text-right"><small><?=$nro_correlativo;?></small></td>
                    <td><small><?=$responsable;?></small></td>
                    <td><small><?=$codigo_alterno?></small></td>
                    <td><small><?=$fecha_registro;?></small></td>
                    <td class="text-right"><small><?=formatNumberDec($sumaTotalImporte);?></small></td>                            
                    <td><small><small><?=$datosFacturacion;?></small></small></td>
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
                    <td class="td-actions text-right">
                      <!-- <button class="btn <?=$btnEstado?> btn-sm btn-link"><small><?=$estado;?></small></button><br> -->
                      <?php
                      if($cont_facturas>1){?>
                        <div class="btn-group dropdown">
                          <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><small>Facturas</small></button>
                          <div class="dropdown-menu"><?php 
                            $arrayCodFacturas = explode(",",trim($cadenaCodFacturas,','));
                            $arrayFacturas = explode(",",trim($cadenaFacturas,','));
                            for ($i=0; $i < $cont_facturas; $i++) { 
                              $cod_factura_x= $arrayCodFacturas[$i];
                              $nro_factura_x= $arrayFacturas[$i];
                              
                              if($cod_factura_x!=0){
                                if($idTransaccionSiat!=0){ ?>
                                <a class="dropdown-item" type="button" href='<?=$url_list_siat;?>formatoFacturaOnLine.php?codVenta=<?=$idTransaccion_siat?>' target="_blank"><i class="material-icons text-success" title="Imprimir Factura">print</i> Factura <?=$i+1;?> - Nro <?=$nro_factura_x?></a>
                              <?php 
                                }else{
                              ?>
                                <a class="dropdown-item" type="button" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$cod_factura_x;?>&tipo=1' target="_blank"><i class="material-icons text-success" title="Imprimir Factura">print</i> Factura <?=$i+1;?> - Nro <?=$nro_factura_x?></a>
                              <?php    
                                }
                              }else{?>
                                <a class="dropdown-item" type="button" href='#'><i class="material-icons text-success" title="Factura">list</i> Factura <?=$i+1;?> - Nro <?=$nro_factura_x?></a>
                              <?php }
                            }?>
                          </div>
                        </div> <?php 
                      }
                      ?>
                      <div class="btn-group dropdown">
                        <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                           <i class="material-icons" >list</i><small><small><?=$estado;?></small></small>
                        </button>
                        <div class="dropdown-menu" >   
                      <?php
                        if($globalAdmin==1){ //
                          if($codigo_fact_x>0 && $cont_facturas<2 && ($cod_estado_factura_x!=2)){//print facturas
                            if($idTransaccionSiat!=0){
                      ?>
                            <a class="btn btn-success" href='<?=$url_list_siat;?>formatoFacturaOnLine.php?codVenta=<?=$idTransaccionSiat?>' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>
                      <?php        
                            }else{
                      ?>
                            <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>
                      <?php        
                            }
                            ?>
                            <a href="<?=$urlImp;?>?comp=<?=$cod_comprobante_x;?>&mon=1" target="_blank" class="btn" style="background-color:#3f33ff">
                              <i class="material-icons" title="Imprimir Comprobante">print</i></a><?php
                          }elseif($cod_estado_factura_x==4){//factura manual ?>
                            <button title="Detalles Factura Manual" class="btn btn-success" type="button" data-toggle="modal" data-target="#modalDetalleFacturaManual" onclick="agregaDatosDetalleFactManual('<?=$datos_FacManual;?>')">
                              <i class="material-icons">list</i>
                            </button> <?php 
                          }?>
                            <a class="btn btn-danger" title="Imprimir Solicitud" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons">print</i></a>
                            <a href="<?=$urlVer_SF;?>?codigo=<?=$codigo_facturacion;?>" target="_blank" class="btn btn-info" title="Ver Solicitud">
                              <i class="material-icons">remove_red_eye</i>
                            </a>
                            <a href='#' title="Archivos Adjuntos" class="btn btn-primary" onclick="abrirArchivosAdjuntos('<?=$datos_otros;?>')"><i class="material-icons" ><?=$iconFile?></i></a>
                        <?php
                        }
                      ?>
                    </div></div>
                    </td>
                  </tr>
                  <?php
                    $index++;
                }
                ?>
              </tbody>
            </table>
 
