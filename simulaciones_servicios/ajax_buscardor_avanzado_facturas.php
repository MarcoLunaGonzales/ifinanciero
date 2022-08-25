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
$cod_factura=$_GET['cod_factura'];


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
  $nro_f=trim($nro_f,",");
  $sql.=" and f.nro_factura in ($nro_f)"; 
}
if($personal_p!=""){  
  $sql.=" and f.cod_personal in ($personal_p)"; 
}
if($cod_factura!=""){  
  $sql.=" and f.codigo in ($cod_factura)"; 
}
$sql.=" order by f.fecha_factura desc, f.nro_factura desc;";
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
$stmt->bindColumn('glosa_factura3', $glosa_factura3);

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
      <th width="6%">Personal</th>
      <th width="8%">Fecha<br>Factura</th>
      <th width="15%">Razón Social</th>
      <th width="9%">Nit</th>
      <th width="8%">Importe<br>Factura</th>
      <th>Concepto</th>
      <!--th>Detalle</th-->
      <!---th width="15%">Observaciones</th-->
      <th width="15%">Glosa Factura E.</th>
      <th width="10%" class="text-right">Opciones</th>                            
    </tr>
  </thead>                        
  <tbody>
  <?php
    $index=1;
    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {

       /*FACTURA ESPECIAL*/
      if($glosa_factura3!=""){
        if (strlen($glosa_factura3)>50){
          $glosa_factura3= substr($glosa_factura3, 0, 50)."..."; 
        }
        $glosa_factura3="<i class='material-icons text-alert'>info</i>".$glosa_factura3;
      }
      /*FIN FACTURA ESPECIAL*/
      //para la anulacion de facturas
      if(isset($_GET['interno'])){
        $sw_anular=true;
        // echo "aqui";
      }else{
        $dias_alargo=obtenerValorConfiguracion(75);//defecto                            
        $fecha_inicio=date('Y-m-1'); 
        $fecha_fin = date("Y-m-t", strtotime($fecha_inicio)); 
        $fecha_inicio_x= date("Y-m-d",strtotime($fecha_inicio."- ".$dias_alargo." days")); 
        $fechaComoEntero = strtotime($fecha_factura_xy);
        $fecha_factura_xyz = date("Y-m-d", $fechaComoEntero);
        // echo $fecha_inicio_x."-".$fecha_fin."<br>";
        $sw_anular=verificar_fecha_rango($fecha_inicio_x, $fecha_fin, $fecha_factura_xyz);
      }
      //==
      $nombre_personal=namePersonalCompleto($cod_personal);
      if($cod_personal==0){
        $nombre_personal="Tienda Virtual";
      }                          
      $cadenaFacturas='F '.$nro_factura;
      $codigos_facturas=$codigo_factura;                          
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
      if (strlen($observaciones_solfac)>50){
        $observaciones_solfac= substr($observaciones_solfac, 0, 50)."..."; 
      }
      if (strlen($observaciones)>50){
        $observaciones= substr($observaciones, 0, 50)."..."; 
      }
      
      //FORMAMOS EL CONCEPTO DE LA FACTURA
      $stmtDetalleSol = $dbh->prepare("SELECT fv.cantidad, fv.precio, fv.descripcion_alterna from facturas_ventadetalle fv where cod_facturaventa=$codigo_factura");
      $stmtDetalleSol->execute();
      $stmtDetalleSol->bindColumn('cantidad', $cantidad);  
      $stmtDetalleSol->bindColumn('precio', $precio_unitario);
      $stmtDetalleSol->bindColumn('descripcion_alterna', $descripcion_alterna); 
      $cadenaFacturas="";
      $cadenaFacturasM="";
      $concepto_contabilizacion="";

      while ($row_det = $stmtDetalleSol->fetch()){
        $precio=$precio_unitario*$cantidad;
        $concepto_contabilizacion.=$descripcion_alterna." / ".trim($cadenaFacturas,',').",".trim($cadenaFacturasM,",")." / ".$razon_social."<br>\n";
        $concepto_contabilizacion.="Cantidad: ".$cantidad." * ".formatNumberDec($precio_unitario)." = ".formatNumberDec($precio)."<br>\n";
      }
      $concepto_contabilizacion = (substr($concepto_contabilizacion, 0, 100))."..."; //limite de string
      // --------


      $cod_tipopago_anticipo=obtenerValorConfiguracion(48);//tipo pago credito
      $cod_tipopago_aux=obtnerFormasPago_codigo($cod_tipopago_anticipo,$cod_solicitudfacturacion);//verificamos si en nuestra solicitud se hizo alguna distribucion de formas de pago y sacamos el de dep cuenta. devolvera 0 en caso de q no exista                            
      $datos=$codigo_factura.'/'.$cod_solicitudfacturacion.'/'.$nro_factura.'/'.$correos_string.'/'.$razon_social.'/'.$interno;
      ?>
      <tr>
        <!-- <td align="center"><?=$index;?></td> -->
        <td><?=$nro_factura;?></td>
        <td><?=$nombre_personal;?></td>
        <td><?=$fecha_factura?><br><?=$hora_factura?></td>
        <td class="text-left"><small><?=mb_strtoupper($razon_social);?></small></td>
        <td class="text-right"><?=$nit;?></td>
        <td class="text-right"><?=formatNumberDec($importe);?></td>
        <td><small><?=strtoupper($concepto_contabilizacion);?></small></td>                            
       <td style="color: #ff0000;"><?=$glosa_factura3;?></td>
        <td class="td-actions text-right">
          <!-- <button class="btn <?=$label?> btn-sm btn-link" style="padding:0;"><small><?=$estadofactura;?></small></button><br> -->
          <?php
          if($cod_estadofactura!=4){?>                                   
              <div class="btn-group dropdown">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Formato 1">
                   <i class="material-icons" title="Imprimir Factura <?=$correosEnviados?>">print</i>
                </button>
                <div class="dropdown-menu">                                      
                  <!--a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=1' target="_blank"><i class="material-icons text-success">print</i> Original Cliente y Copia Contabilidad</a-->
                  <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=2' target="_blank"><i class="material-icons text-success">print</i> Original Cliente</a>
                  <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=3' target="_blank"><i class="material-icons text-success">print</i>Copia Contabilidad</a>                                    
                </div>
              </div>
              <!--div class="btn-group dropdown">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Formato 2">
                   <i class="material-icons" title="Imprimir Factura <?=$correosEnviados?>">print</i>
                </button>
                <div class="dropdown-menu">                                      
                  <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=4' target="_blank"><i class="material-icons text-success">print</i> Original Cliente y Copia Contabilidad</a>
                  <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=5' target="_blank"><i class="material-icons text-success">print</i> Original Cliente</a>
                  <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=6' target="_blank"><i class="material-icons text-success">print</i>Copia Contabilidad</a>                                    
                  <a class="dropdown-item" href='<?=$urlGenerarFacturasPrint;?>?codigo=<?=$codigo_factura;?>&tipo=1&admin=7' target="_blank"><i class="material-icons text-warning">print</i>Copia Original 3</a>
                </div>
              </div--> 
              <?php                               
          }?>
          <div class="btn-group dropdown">
            <button type="button" class="btn <?=$label?> dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
               <i class="material-icons" >list</i><small><small><?=$estadofactura;?></small></small>
            </button>
            <div class="dropdown-menu" >
              <?php                                
              if(($cod_estadofactura==1)){?>
                <button  rel="tooltip" class="dropdown-item" data-toggle="modal" data-target="#modalEnviarCorreo" onclick="agregaformEnviarCorreo('<?=$datos;?>')">
                  <i class="material-icons text-warning" title="Enviar Correo">email</i> Enviar Correo
                </button><?php
              } 
              if($cod_estadofactura!=4&&$cod_solicitudfacturacion!=-100){?>  
                <a rel="tooltip" class="dropdown-item" href='<?=$urlPrintSolicitud;?>?codigo=<?=$cod_solicitudfacturacion;?>' target="_blank"><i class="material-icons text-primary" title="Imprimir Solicitud Facturación">print</i> Imprimir SF</a>
                <a rel="tooltip" class="dropdown-item" href="<?=$urlVer_SF;?>?codigo=<?=$cod_solicitudfacturacion;?>" target="_blank">
                  <i class="material-icons text-default" title="Ver Solicitud Facturación">print</i> Ver SF
                </a>
                <?php                               
              }
              $datos_devolucion=$cod_solicitudfacturacion."###".$cadenaFacturas."###".$razon_social."###".$urllistFacturasServicios."###".$codigos_facturas."###".$cod_comprobante."###".$cod_tipopago_aux."###".$interno;                                
              if($cod_estadofactura!=4 && $cod_estadofactura!=2 && $sw_anular){?>
                <button rel="tooltip" class="dropdown-item" data-toggle="modal" data-target="#modalDevolverSolicitud" onclick="modal_rechazarFactura('<?=$datos_devolucion;?>')">
                  <i class="material-icons text-danger" title="Anular Factura">delete</i> Anular Factura
                </button><?php 
              } 
              $configuracion_defecto_edit=obtenerValorConfiguracion(77);
              $datos_edit=$cadenaFacturas."###".$razon_social."###".$codigos_facturas."###".$glosa_factura3;
              if($cod_estadofactura!=2 && $configuracion_defecto_edit==1){?>
                <button rel="tooltip" class="dropdown-item" data-toggle="modal" data-target="#modalEditarFactura" onclick="modal_editarFactura_sf('<?=$datos_edit;?>')">
                  <i class="material-icons text-success" title="Editar Razón Social">edit</i> Editar Factura
                </button><?php 
              }?>
            </div>
          </div>
        </td>
      </tr>
      <?php
        $index++;
    }
    ?>
  </tbody>
</table>