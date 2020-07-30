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

$razon_social_f=$_GET['razon_social_f'];
$detalle_f=$_GET['detalle_f'];
$fechaBusquedaInicio=$_GET['fechaI'];
$fechaBusquedaFin=$_GET['fechaF'];
$nit_f=$_GET['nit_f'];
$nro_f=$_GET['nro_f'];


$sql="SELECT f.*,DATE_FORMAT(f.fecha_factura,'%d/%m/%Y')as fecha_factura_x,DATE_FORMAT(f.fecha_factura,'%H:%i:%s')as hora_factura_x,(select s.abreviatura from unidades_organizacionales s where s.cod_sucursal=f.cod_sucursal limit 1)as sucursal
 from facturas_venta f where cod_estadofactura in (1,2,3)";
if($razon_social_f!=""){
  $sql.=" and f.razon_social like '%$razon_social_f%'";
}
if($detalle_f!=""){
  $sql.=" and f.observaciones like '%$detalle_f%'";  
}
if($fechaBusquedaInicio!="" && $fechaBusquedaFin!=""){
  $sql.=" and f.fecha_factura BETWEEN '$fechaBusquedaInicio' and '$fechaBusquedaFin'"; 
}
if($nit_f!="" ){
  $sql.=" and f.nit=$nit_f"; 
}
if($nro_f!="" ){
  $sql.=" and f.nro_factura=$nro_f"; 
}
$sql.=" order by f.fecha_factura desc;";
//echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('codigo', $codigo_factura);
$stmt->bindColumn('cod_sucursal', $cod_sucursal);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);  
$stmt->bindColumn('fecha_factura_x', $fecha_factura);
$stmt->bindColumn('hora_factura_x', $hora_factura);
$stmt->bindColumn('fecha_limite_emision', $fecha_limite_emision);
$stmt->bindColumn('cod_tipopago', $cod_tipopago);
$stmt->bindColumn('cod_cliente', $cod_cliente);
$stmt->bindColumn('razon_social', $razon_social);
$stmt->bindColumn('nit', $nit);
$stmt->bindColumn('cod_dosificacionfactura', $cod_dosificacionfactura);
$stmt->bindColumn('nro_factura', $nro_factura);
$stmt->bindColumn('nro_autorizacion', $nro_autorizacion);
$stmt->bindColumn('codigo_control', $codigo_control);
$stmt->bindColumn('importe', $importe);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_estadofactura', $cod_estadofactura);
$stmt->bindColumn('sucursal', $sucursal);
$stmt->bindColumn('cod_comprobante', $cod_comprobante);
?>  
<?php
  $index=1;
  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
      if($cod_solicitudfacturacion!=-100){
        $stmtFActuras = $dbh->prepare("SELECT codigo,nro_factura from facturas_venta where cod_solicitudfacturacion=$cod_solicitudfacturacion");
        $stmtFActuras->execute(); 
        $stmtFActuras->bindColumn('codigo', $codigo_x);
        $stmtFActuras->bindColumn('nro_factura', $nro_factura_x);
        $cadenaFacturas="";
        $codigos_facturas="";
        while ($row = $stmtFActuras->fetch()) {
          $cadenaFacturas.="F ".$nro_factura_x.", ";
          $codigos_facturas.=$codigo_x.",";
        }
        $cadenaFacturas=trim($cadenaFacturas,", ");//todas las facturas del la solicitud
        $codigos_facturas=trim($codigos_facturas,", ");//todas las facturas del la solicitud
      }else{
        $cadenaFacturas='F '.$nro_factura;
        $codigos_facturas=$codigo_factura;
      }
      $importe=sumatotaldetallefactura($codigo_factura);
      $correosEnviados=obtenerCorreosEnviadosFactura($codigo_factura);
      if($correosEnviados!=""){
        $correosEnviados="\nFactura enviada a: \n *".$correosEnviados;
      }
      $estadofactura=obtener_nombreestado_factura($cod_estadofactura);
      $cliente=nameCliente($cod_cliente);
      //correos de contactos
      $tipo_solicitud=obtenerTipoSolicitud($cod_solicitudfacturacion);
      if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
        $correos_string=obtenerCorreoEstudiante($nit);
      }else $correos_string=obtenerCorreosCliente($cod_cliente);                            
      //colores de estados                            
      $observaciones_solfac="";
      switch ($cod_estadofactura) {
        case 1://activo
          $label='btn-success';
          break;
        case 2://anulado
          $label='btn-danger';
          $observaciones_solfac = obtener_observacion_factura($cod_solicitudfacturacion);
          break;
        case 3://enviado
          $label='btn-info';
          break;
      }
      $cod_tipopago_anticipo=obtenerValorConfiguracion(48);//tipo pago credito
      $cod_tipopago_aux=obtnerFormasPago_codigo($cod_tipopago_anticipo,$cod_solicitudfacturacion);//verificamos si en nuestra solicitud se hizo alguna distribucion de formas de pago y sacamos el de dep cuenta. devolvera 0 en caso de q no exista                            
      $datos=$codigo_factura.'/'.$cod_solicitudfacturacion.'/'.$nro_factura.'/'.$correos_string.'/'.$razon_social;
      ?>
    <tr>
      <!-- <td align="center"><?=$index;?></td> -->
      <td><?=$nro_factura;?></td>
      <!-- <td><?=$sucursal;?></td> -->
      <td><?=$fecha_factura?><br><?=$hora_factura?></td>
      <td class="text-left"><small><?=strtoupper($razon_social);?></small></td>
      <td class="text-right"><?=$nit;?></td>
      <td class="text-right"><?=formatNumberDec($importe);?></td>
      <td><small><?=strtoupper($observaciones);?></small></td>                            
      <td style="color: #ff0000;"><?=strtoupper($observaciones_solfac)?></td>
      <td class="td-actions text-right">
        <button class="btn <?=$label?> btn-sm btn-link" style="padding:0;"><small><?=$estadofactura;?></small></button><br>
        <?php                                
          if(($cod_estadofactura==1)){?>
            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalEnviarCorreo" onclick="agregaformEnviarCorreo('<?=$datos;?>')">
              <i class="material-icons" title="Enviar Correo">email</i>
            </button>
            <?php
          } if($cod_estadofactura!=4){?>  

            <div class="btn-group dropdown">
              <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                 <i class="material-icons" title="Imprimir Factura <?=$correosEnviados?>">print</i>
              </button>
              <div class="dropdown-menu">                                      
                <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=1' target="_blank"><i class="material-icons text-success">print</i> Original Cliente y Copia Contabilidad</a>
                <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=2' target="_blank"><i class="material-icons text-success">print</i> Original Cliente</a>
                <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=3' target="_blank"><i class="material-icons text-success">print</i>Copia Contabilidad</a>
              </div>
            </div>
            <?php
             $datos_devolucion=$cod_solicitudfacturacion."###".$cadenaFacturas."###".$razon_social."###".$urllistFacturasServicios."###".$codigos_facturas."###".$cod_comprobante."###".$cod_tipopago_aux;
            ?>
            
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalDevolverSolicitud" onclick="modal_rechazarFactura('<?=$datos_devolucion;?>')">
              <i class="material-icons" title="Anular Factura">delete</i>
            </button>
            <!-- <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation-anular-factura','<?=$urlAnularFactura;?>&codigo=<?=$codigo_factura;?>&cod_solicitudfacturacion=<?=$cod_solicitudfacturacion?>&cod_comprobante=<?=$cod_comprobante?>')">
            <i class="material-icons" title="Anular Factura">delete</i>
            </button> -->
            <?php 
          } ?>
      </td>
    </tr><?php
    $index++;
  }
?>
