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

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

if(isset($_GET['q'])){
  $q=$_GET['q'];
  $v=$_GET['v'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $globalUser=$_GET['q'];
}else{
  $globalUser=$_SESSION["globalUser"];
  $q=0;
  $v=0;
  $s=0;
  $u=0;
}

$cod_uo=$_GET['cod_uo'];
$cliente=$_GET['cliente'];
$fechaI=$_GET['fechaI'];
$fechaF=$_GET['fechaF'];
$razon_social_b=$_GET['razon_social'];
$nro_s=$_GET['nro_s'];

$sql="SELECT sf.*,es.nombre as estado,DATE_FORMAT(sf.fecha_registro,'%d/%m/%Y')as fecha_registro_x,DATE_FORMAT(sf.fecha_solicitudfactura,'%d/%m/%Y')as fecha_solicitudfactura_x FROM solicitudes_facturacion sf join estados_solicitudfacturacion es on sf.cod_estadosolicitudfacturacion=es.codigo where sf.cod_personal=$globalUser ";

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
  

  $nro_s=trim($nro_s,",");
  $sql.=" and sf.nro_correlativo in ($nro_s)";  
}
$sql.=" order by sf.codigo desc";

// echo $sql;
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
          $label='<span style="padding:1;" class="badge badge-default">';
          $btnEstado="btn-default";
        break;
        case 2:                                
          $label='<span style="padding:1;" class="badge badge-danger">';
          $btnEstado="btn-danger";
        break;
        case 3:                                
          $label='<span style="padding:1;" class="badge badge-success">';
          $btnEstado="btn-success";
        break;
        case 4:                                
          $label='<span style="padding:1;" class="badge badge-info">';
          $btnEstado="btn-info";
        break;
        case 5:                                
          $label='<span style="padding:1;" class="badge badge-warning">';
          $btnEstado="btn-warning";
        break;
        case 6:                                
          $label='<span style="padding:1;" class="badge badge-default">';
          $btnEstado="btn-default";
        break;
      }
      //verificamos si ya tiene factura generada y esta activa                           
      $stmtFact = $dbh->prepare("SELECT codigo, nro_factura, cod_estadofactura, razon_social, nit, nro_autorizacion, importe, cod_comprobante from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion order by codigo desc limit 1");
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
        // $btnEstado="btn-warning";
        $label='<span class="badge badge-warning">';
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
          $cadenaFacturas.="FM".$row_montos['nro_factura'].", ";
          $cadenaCodFacturas.="0,";
        }elseif($cod_estadofactura==2){
          $cadenaFacturas.="FA".$row_montos['nro_factura'].", ";  
          $cadenaCodFacturas.=$row_montos['codigo'].",";
        }else{
          $cadenaFacturas.="F".$row_montos['nro_factura'].", ";  
          $cadenaCodFacturas.=$row_montos['codigo'].",";
        }
        $importe_fact_x+=$row_montos['importe'];                            
        $cont_facturas++;
      }                  
      // echo $cont_facturas."<br>";
      //sacamos nombre de los detalles
      $stmtDetalleSol = $dbh->prepare("SELECT cantidad,precio,descripcion_alterna,ci_estudiante from solicitudes_facturaciondetalle where cod_solicitudfacturacion=$codigo_facturacion");
      $stmtDetalleSol->execute();
      $stmtDetalleSol->bindColumn('cantidad', $cantidad);  
      $stmtDetalleSol->bindColumn('precio', $precio_unitario);
      $stmtDetalleSol->bindColumn('descripcion_alterna', $descripcion_alterna);                
      $stmtDetalleSol->bindColumn('ci_estudiante', $ci_estudiante_x); 
      $concepto_contabilizacion="";
    
      while ($row_det = $stmtDetalleSol->fetch()){
        
        if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){                              
          $concepto_contabilizacion="CI: ".$ci_estudiante_x." / "; 
        }
        $concepto_contabilizacion.=$descripcion_alterna."<br>\n";
      }
      $concepto_contabilizacion = (substr($concepto_contabilizacion, 0, 100)); //limite de string                            
      $cod_area_simulacion=$cod_area;
      $nombre_simulacion='OTROS';
      $name_area_simulacion=trim(abrevArea($cod_area_simulacion),'-');
      // --------
      $responsable=namePersonal_2($cod_personal);//nombre del personal
      // $nombre_tipopago=nameTipoPagoSolFac($cod_tipopago);//
      //pude ver el caso que vea distibucion de formas de pago
      $string_formaspago=obtnerFormasPago($codigo_facturacion);
      $nombre_area=trim(abrevArea($cod_area),'-');//nombre del area
      $nombre_uo=trim(abrevUnidad($cod_unidadorganizacional),' - ');//nombre de la oficina
     
      $sumaTotalImporte=obtenerSumaTotal_solicitudFacturacion($codigo_facturacion);

      if($cont_facturas>1){      
          // $estado="FACTURA PARCIAL";
          $nro_fact_x=trim($cadenaFacturas,',');
        }
        // $cadenaFacturasM=trim($cadenaFacturasM,',');
        //datos para el envio de facturas
        $cod_factura=verificamosFacturaDuplicada($codigo_facturacion);//codigo de factura
        if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
          $correos_string=obtenerCorreoEstudiante($cod_cliente);
        }else $correos_string=obtenerCorreosCliente($cod_cliente);
        $nro_factura=obtenerNroFactura($cod_factura);
        $datos_factura_envio=$cod_factura.'/'.$codigo_facturacion.'/'.$nro_factura.'/'.$correos_string.'/'.$razon_social;
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
        <td><small><?=$observaciones_string;?></small></td>
        <td style="color:#298A08;"><small><?=$nro_fact_x;?><br><span style="color:#DF0101;"><?=$cadenaFacturasM;?></span></small>
          <?php if($cod_estado_factura_x==3){
            $estadofactura=obtener_nombreestado_factura($cod_estadofactura);
            ?>
              <span class="badge badge-dark"><small><?=$estadofactura?></small></span><?php
            }?>
        </td>

        <td class="text-left" style="color:#ff0000;"><small><small><?=$string_formaspago;?></small></small></td>
        <td class="td-actions text-right">                              
          
          <?php
            if($codigo_fact_x>0 && $cod_estado_factura_x!=2 && $cod_estado_factura_x!=5){//print facturas
                // echo "entra";//solo para facturas mayores a uno
                if($cont_facturas>1){ //para factura parcial?>
                  <div class="btn-group dropdown">
                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><small><small><small>Facturas</small></small></small></button>
                    <div class="dropdown-menu"><?php 
                      $arrayCodFacturas = explode(",",trim($cadenaCodFacturas,','));
                      $arrayFacturas = explode(",",trim($cadenaFacturas,','));
                      for ($i=0; $i < $cont_facturas; $i++) { 
                        $cod_factura_x= $arrayCodFacturas[$i];
                        $nro_factura_x= $arrayFacturas[$i];
                        if($cod_factura_x!=0){?>
                          <a class="dropdown-item" type="button" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$cod_factura_x;?>&tipo=1' target="_blank"><i class="material-icons text-success" title="Imprimir Factura">print</i> Factura <?=$i+1;?> - Nro <?=$nro_factura_x?></a>
                        <?php }else{?>
                          <a class="dropdown-item" type="button" href='#'><i class="material-icons text-success" title="Factura">list</i> Factura <?=$i+1;?> - Nro <?=$nro_factura_x?></a>
                        <?php }                                           
                      }?>
                    </div>
                  </div> <?php 
                }                                    
              }
          ?>
          <div class="btn-group dropdown">
            <button type="button" class="btn <?=$btnEstado?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
               <i class="material-icons" >list</i><small><small><?=$estado;?></small></small>
            </button>
            <div class="dropdown-menu" >   
          <?php        
          $patron15 = "/[^a-zA-Z0-9]+/";//solo numeros,letras M y m, tildes y la ñ
          $patron1="[\n|\r|\n\r]";
          $obs_devolucion = preg_replace($patron1, ", ", $obs_devolucion);//quitamos salto de linea
          $obs_devolucion = str_replace('"', " ", $obs_devolucion);//quitamos comillas dobles
          $obs_devolucion = str_replace("'", " ", $obs_devolucion);//quitamos comillas simples
          // echo $obs_devolucion;
            if($cod_estado_factura_x!=4){
              // echo $codigo_fact_x."-";
              if($codigo_fact_x>0 && $cod_estado_factura_x!=2 && $cod_estado_factura_x!=5){//print facturas
                // echo "entra";
                if($cont_facturas<2){
                  ?>
                  <a class="btn btn-success" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_facturacion;?>&tipo=2' target="_blank"><i class="material-icons" title="Imprimir Factura">print</i></a>          
                  
                 <?php               
                }
                
              }else{// generar facturas                                        
                if($codEstado==1){
                  $cod_tipopago=obtenemosformaPagoSolfact($codigo_facturacion);//fomra pago
                  $cod_tipopago_cred=obtenerValorConfiguracion(48);
                  // echo $cod_tipopago_cred; 
                  $cod_tipopago_aux=obtnerFormasPago_codigo($cod_tipopago_cred,$codigo_facturacion);//verificamos si en nuestra solicitud se hizo alguna distribucion de formas de pago y sacamos el de credito. devolvera 0 en caso de q no exista
                  if($cod_tipopago_aux!=0){
                    $cod_tipopago=$cod_tipopago_aux;
                  }
                  if($cod_tipopago==$cod_tipopago_cred){//si es igual a credito cambia de flujo
                    if(isset($_GET['q'])){
                      if($obs_devolucion==null || $obs_devolucion==''){//cuado se hace el rechazo de la fac y volvemos a enviar
                        ?>                                             
                        <a title="Enviar a Regional(En Revisión)" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=6&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>')" href='#' class="btn btn-default">
                         <i class="material-icons">send</i>
                       </a>
                        <?php 
                      }else{                                            
                        
                        $datos_devolucion=$codigo_facturacion."###".$nro_correlativo."###".$codigo_alterno."###6###0###".$urlEdit2Sol."###".$obs_devolucion;?>
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalReenviarSolicitudDevuelto" onclick="modalReenviarSolicitudDevuelto('<?=$datos_devolucion;?>')">
                          <i class="material-icons" title="Enviar a Regional(En Revisión)">send</i>
                        </button><?php
                      }
                    }else{
                      if($obs_devolucion==null || $obs_devolucion==''){//cuado se hace el rechazo de la fac y volvemos a enviar
                        ?>                                             
                        <a title="Enviar a Regional(En Revisión)" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=6&admin=0')" href='#'  class="btn btn-default">
                           <i class="material-icons">send</i>
                        </a>
                        <?php 
                      }else{
                        $datos_devolucion=$codigo_facturacion."###".$nro_correlativo."###".$codigo_alterno."###6###0###".$urlEdit2Sol."###".$obs_devolucion;?>
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalReenviarSolicitudDevuelto" onclick="modalReenviarSolicitudDevuelto('<?=$datos_devolucion;?>')">
                          <i class="material-icons" title="Enviar a Regional(En Revisión)">send</i>
                        </button><?php
                      }?>                                             
                      <?php
                    } 
                  }else{
                    if(isset($_GET['q'])){ 
                      if($obs_devolucion==null || $obs_devolucion==''){//cuado se hace el rechazo de la fac y volvemos a enviar                                              
                        ?>                                             
                        <a title="Enviar a contabilidad(Revisado)" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=4&admin=0&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>')" href='#' class="btn btn-default">
                         <i class="material-icons">send</i>
                       </a>
                        <?php 
                      }else{
                        $datos_devolucion=$codigo_facturacion."###".$nro_correlativo."###".$codigo_alterno."###4###0###".$urlEdit2Sol."###".$obs_devolucion;?>
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalReenviarSolicitudDevuelto" onclick="modalReenviarSolicitudDevuelto('<?=$datos_devolucion;?>')">
                          <i class="material-icons" title="Enviar a contabilidad(Revisado)">send</i>
                        </button><?php 
                      }
                    }else{
                      if($obs_devolucion==null || $obs_devolucion==''){//cuado se hace el rechazo de la fac y volvemos a enviar                                              
                        ?>                                             
                        <a title="Enviar a contabilidad(Revisado)" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlEdit2Sol?>?cod=<?=$codigo_facturacion?>&estado=4&admin=0')" href='#'  class="btn btn-default">
                           <i class="material-icons">send</i>
                        </a>                                              
                        <?php 
                      }else{
                        $datos_devolucion=$codigo_facturacion."###".$nro_correlativo."###".$codigo_alterno."###4###0###".$urlEdit2Sol."###".$obs_devolucion;?>
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modalReenviarSolicitudDevuelto" onclick="modalReenviarSolicitudDevuelto('<?=$datos_devolucion;?>')">
                          <i class="material-icons" title="Enviar a contabilidad(Revisado)">send</i>
                        </button><?php
                      }
                    } 

                  }
                  if(isset($_GET['q'])){?>
                    <a title="Editar Solicitud Facturación" href='<?=$urlEditSolicitudfactura;?>&codigo_s=<?=$codigo_facturacion?>&q=<?=$q?>&v=<?=$v?>&s=<?=$s?>&u=<?=$u?>' class="btn btn-success">
                      <i class="material-icons"><?=$iconEdit;?></i>
                    </a><?php
                  }else{?>
                    <a title="Editar Solicitud Facturación" href='<?=$urlEditSolicitudfactura;?>&codigo_s=<?=$codigo_facturacion?>' class="btn btn-success">
                      <i class="material-icons"><?=$iconEdit;?></i>
                    </a>
                  <?php 
                  }
                  ?>
                  <?php 
                }
              }
            }else{//factura manual                                   
              ?>
              <button title="Detalles Factura Manual" class="btn btn-success" type="button" data-toggle="modal" data-target="#modalDetalleFacturaManual" onclick="agregaDatosDetalleFactManual('<?=$datos_FacManual;?>')">
                <i class="material-icons">list</i>
              </button>                                    
               <?php 
            }

            if($codEstado!=2){?>
              <a class="btn btn-danger" href='<?=$urlPrintSolicitud;?>?codigo=<?=$codigo_facturacion;?>' target="_blank"><i class="material-icons" title="Imprimir Solicitud">print</i></a>
              <a href="<?=$urlVer_SF;?>?codigo=<?=$codigo_facturacion;?>" target="_blank" class="btn btn-info" title="Ver Solicitud">
                <i class="material-icons">remove_red_eye</i>
              </a>
              <a href='#' title="Archivos Adjuntos" class="btn btn-primary" onclick="abrirArchivosAdjuntos('<?=$datos_otros;?>')"><i class="material-icons" ><?=$iconFile?></i></a>
            <?php }
            if($codEstado==1){
              if(isset($_GET['q'])){?>
                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlAnular_SoliciutdFacturacion;?>?codigo=<?=$codigo_facturacion;?>&q=<?=$q?>&v=<?=$v?>&s=<?=$s?>&u=<?=$u?>')">
                  <i class="material-icons" title="Anular Solicitud Facturación">delete</i>
                </button>
              <?php }else{?>
                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlAnular_SoliciutdFacturacion;?>?codigo=<?=$codigo_facturacion;?>')">
                  <i class="material-icons" title="Anular Solicitud Facturación">delete</i>
                </button>
              <?php }
            }
            if($codigo_fact_x>0 && $cod_estado_factura_x==3 && $cont_facturas<2){
              $cadenaFacturas=trim($cadenaFacturas,",");
              $cadenaCodFacturas=trim($cadenaCodFacturas,",");
              $correosEnviados=obtenerCorreosEnviadosFactura($cadenaCodFacturas);
              if($correosEnviados!=""){
                $correosEnviados="\nFactura enviada a: \n *".$correosEnviados;

              }
              $correosEnviados = preg_replace("[\n|\r|\n\r]", ", ", $correosEnviados);     
              $correosEnviados=trim($correosEnviados,"Factura enviada a:,");

              $datos_envio_correo=$cadenaFacturas."######".$correosEnviados;?>
              <a href='#' title="Información de envío de Correo" class="btn btn-primary" onclick="modal_info_enviocorreo_f('<?=$datos_envio_correo;?>')"><i class="material-icons" >email</i></a>
             
            <?php }
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
