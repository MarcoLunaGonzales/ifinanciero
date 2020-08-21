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
$personal_p=$_GET['personal_p'];
$interno=$_GET['interno'];

$sql="SELECT f.*,DATE_FORMAT(f.fecha_factura,'%d/%m/%Y')as fecha_factura_x,DATE_FORMAT(f.fecha_factura,'%H:%i:%s')as hora_factura_x,(select s.abreviatura from unidades_organizacionales s where s.cod_sucursal=f.cod_sucursal limit 1)as sucursal
 from facturas_venta f where cod_estadofactura in (1,2,3)";
if($razon_social_f!=""){
  $sql.=" and f.razon_social like '%$razon_social_f%'";
}
if($detalle_f!=""){
  $sql.=" and f.observaciones like '%$detalle_f%'";  
}
if($fechaBusquedaInicio!="" && $fechaBusquedaFin!=""){
  $sql.=" and f.fecha_factura BETWEEN '$fechaBusquedaInicio 00:00:00' and '$fechaBusquedaFin 23:59:59'"; 
}
if($nit_f!="" ){
  $sql.=" and f.nit=$nit_f"; 
}
if($nro_f!="" ){
  $sql.=" and f.nro_factura=$nro_f"; 
}
if($personal_p!=""){  
  $sql.=" and f.cod_personal in ($personal_p)"; 
}
$sql.=" order by f.fecha_factura desc;";
// echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('codigo', $codigo_factura);
$stmt->bindColumn('cod_sucursal', $cod_sucursal);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);  
$stmt->bindColumn('fecha_factura_x', $fecha_factura);
$stmt->bindColumn('fecha_factura', $fecha_factura_xy);
$stmt->bindColumn('hora_factura_x', $hora_factura);
$stmt->bindColumn('fecha_limite_emision', $fecha_limite_emision);
$stmt->bindColumn('cod_tipopago', $cod_tipopago);
$stmt->bindColumn('cod_cliente', $cod_cliente);
$stmt->bindColumn('cod_personal', $cod_personal);
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

date_default_timezone_set('America/La_Paz');
$mes_actual=date('m');
if(isset($_GET['interno'])){
  $interno=$_GET['interno'];    
}else{
  $interno=0;
}
?>  

<table class="table" id="tablePaginator50NoFinder">
  <thead>
    <tr>
      <!-- <th class="text-center"></th> -->
      <th width="6%">#Fac</th>
      <th width="10%">Personal</th>
      <th width="8%">Fecha<br>Factura</th>
      <th width="25%">Razón Social</th>
      <th width="9%">Nit</th>
      <th width="8%">Importe<br>Factura</th>
      <th>Detalle</th>
      <th width="12%">Observaciones</th>
      <th width="10%" class="text-right">Opciones</th>                            
    </tr>
  </thead>                        
  <tbody>
  <?php
    $index=1;
    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
      //==
      $nombre_personal=namePersonalCompleto($cod_personal);
      if($cod_personal==0){
        $nombre_personal="Tienda Virtual";
      }                          
      $cadenaFacturas='F '.$nro_factura;
      $codigos_facturas=$codigo_factura;                          
      $importe=sumatotaldetallefactura($codigo_factura);
      // $correosEnviados=obtenerCorreosEnviadosFactura($codigo_factura);
      // if($correosEnviados!=""){
      //   $correosEnviados="\nFactura enviada a: \n *".$correosEnviados;
      // }
      $estadofactura=obtener_nombreestado_factura($cod_estadofactura);
      $cliente=nameCliente($cod_cliente);
      //correos de contactos
      $tipo_solicitud=obtenerTipoSolicitud($cod_solicitudfacturacion);
      // if($tipo_solicitud==2 || $tipo_solicitud==6 || $tipo_solicitud==7){
      //   $correos_string=obtenerCorreoEstudiante($nit);
      // }else $correos_string=obtenerCorreosCliente($cod_cliente);                            
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
      // $cod_tipopago_anticipo=obtenerValorConfiguracion(48);//tipo pago credito
      // $cod_tipopago_aux=obtnerFormasPago_codigo($cod_tipopago_anticipo,$cod_solicitudfacturacion);//verificamos si en nuestra solicitud se hizo alguna distribucion de formas de pago y sacamos el de dep cuenta. devolvera 0 en caso de q no exista                            
      // $datos=$codigo_factura.'/'.$cod_solicitudfacturacion.'/'.$nro_factura.'/'.$correos_string.'/'.$razon_social;
      ?>
      <tr>
        <!-- <td align="center"><?=$index;?></td> -->
        <td><?=$nro_factura;?></td>
        <td><?=$nombre_personal;?></td>
        <td><?=$fecha_factura?><br><?=$hora_factura?></td>
        <td class="text-left"><small><?=mb_strtoupper($razon_social);?></small></td>
        <td class="text-right"><?=$nit;?></td>
        <td class="text-right"><?=formatNumberDec($importe);?></td>
        <td><small><?=strtoupper($observaciones);?></small></td>                            
        <td style="color: #ff0000;"><?=strtoupper($observaciones_solfac)?></td>
        <td class="td-actions text-right">
          <button class="btn <?=$label?> btn-sm btn-link" style="padding:0;"><small><?=$estadofactura;?></small></button><br>
          <?php
            // $datos_devolucion=$cod_solicitudfacturacion."###".$cadenaFacturas."###".$razon_social."###".$urllistFacturasServicios."###".$codigos_facturas."###".$cod_comprobante."###".$cod_tipopago_aux."###".$interno;
          $datos_edit=$cadenaFacturas."###".$razon_social."###".$codigos_facturas;
            if($cod_estadofactura!=2 && $globalAdmin==1){?>
              <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditarFactura" onclick="modal_editarFactura_sf('<?=$datos_edit;?>')">
                <i class="material-icons" title="Editar Factura">edit</i>
              </button>
              <!-- <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalDevolverSolicitud" onclick="modal_rechazarFactura('<?=$datos_devolucion;?>')">
                <i class="material-icons" title="Anular Factura">delete</i>
              </button> -->
            <?php } ?>
        </td>
      </tr>
      <?php
        $index++;
    }
    ?>
  </tbody>
</table>
