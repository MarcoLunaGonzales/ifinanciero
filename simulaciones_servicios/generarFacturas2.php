<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';
include '../assets/controlcode/sin/ControlCode.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';

$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
set_time_limit(300);
//RECIBIMOS LAS VARIABLES

$codigo = $_GET["codigo"];
try{

  // verificamos si ya se registro la factura
  $stmtVerif = $dbh->prepare("SELECT codigo FROM facturas_venta where cod_solicitudfacturacion=$codigo");
  $stmtVerif->execute();
  $resultVerif = $stmtVerif->fetch();    
  $codigo_facturacion = $resultVerif['codigo'];
  if($codigo_facturacion==null){//no se registro
    $stmt = $dbh->prepare("SELECT sf.*,t.descripcion as nombre_serv from solicitudes_facturaciondetalle sf,cla_servicios t 
    where sf.cod_claservicio=t.idclaservicio and sf.cod_solicitudfacturacion=$codigo");
    $stmt->execute();

    //datos de la solicitud de facturacion
    $stmtInfo = $dbh->prepare("SELECT sf.*,t.nombre as nombre_cliente FROM solicitudes_facturacion sf,clientes t  where sf.cod_cliente=t.codigo and sf.codigo=$codigo");
    $stmtInfo->execute();
    $resultInfo = $stmtInfo->fetch();    
    $cod_simulacion_servicio = $resultInfo['cod_simulacion_servicio'];
    $cod_unidadorganizacional = $resultInfo['cod_unidadorganizacional'];
    $cod_area = $resultInfo['cod_area'];
    $cod_tipoobjeto = $resultInfo['cod_tipoobjeto'];
    $cod_tipopago = $resultInfo['cod_tipopago'];
    $cod_cliente = $resultInfo['cod_cliente'];
    $cod_personal = $resultInfo['cod_personal'];
    $razon_social = $resultInfo['razon_social'];
    $nitCliente = $resultInfo['nit'];
    $observaciones = $resultInfo['observaciones'];
    $nombre_cliente = $resultInfo['nombre_cliente'];

    // echo "uo:",$cod_unidadorganizacional."<br>";
    $fecha_actual=date('Y-m-d');
    $fecha_actual_cH=date('Y-m-d H:i:s');
    $stmtInfo = $dbh->prepare("SELECT d.codigo,d.nro_autorizacion, d.llave_dosificacion,d.fecha_limite_emision
    from dosificaciones_facturas d where d.cod_sucursal='$cod_unidadorganizacional' order by codigo");
    $stmtInfo->execute();
    $resultInfo = $stmtInfo->fetch();  
    $cod_dosificacionfactura = $resultInfo['codigo'];  
    $nroAutorizacion = $resultInfo['nro_autorizacion'];
    $llaveDosificacion = $resultInfo['llave_dosificacion'];
    $fecha_limite_emision = $resultInfo['fecha_limite_emision'];

    //monto total redondeado
    $stmtMontoTotal = $dbh->prepare("SELECT sum(sf.precio) as monto from solicitudes_facturaciondetalle sf,cla_servicios t 
    where sf.cod_claservicio=t.idclaservicio and sf.cod_solicitudfacturacion=$codigo");
    $stmtMontoTotal->execute();
    $resultMontoTotal = $stmtMontoTotal->fetch();   
    $monto_total= $resultMontoTotal['monto'];
    $totalFinalRedondeado=round($monto_total,0);
    echo "motno total:".$totalFinalRedondeado;
    //NUMERO CORRELATIVO DE FACTURA
    $stmtNroFac = $dbh->prepare("SELECT IFNULL(nro_factura+1,1)as correlativo from facturas_venta where cod_solicitudfacturacion=$codigo order by codigo desc LIMIT 1");
    $stmtNroFac->execute();
    $resultNroFact = $stmtNroFac->fetch();    
    $nro_correlativo = $resultNroFact['correlativo'];
    if($nro_correlativo==null)$nro_correlativo=1;    
    // echo "auto:".$nroAutorizacion." - nro_corr:".$nro_correlativo." - nitCliente:".$nitCliente." - fecha_actual:".$fecha_actual." - totalFinalRedondeado:".$totalFinalRedondeado." - llaveDosificacion:".$llaveDosificacion;
    $controlCode = new ControlCode();
    $code = $controlCode->generate($nroAutorizacion,//Numero de autorizacion
    $nro_correlativo,//Numero de factura
    $nitCliente,//Número de Identificación Tributaria o Carnet de Identidad
    str_replace('-','',$fecha_actual),//fecha de transaccion de la forma AAAAMMDD
    $totalFinalRedondeado,//Monto de la transacción
    $llaveDosificacion//Llave de dosificación
    );
    // echo "cod:".$code;
    $stmtInsertSoliFact = $dbh->prepare("INSERT INTO facturas_venta(cod_sucursal,cod_solicitudfacturacion,cod_unidadorganizacional,cod_area,fecha_factura,fecha_limite_emision,cod_tipopago,cod_cliente,cod_personal,razon_social,nit,cod_dosificacionfactura,nro_factura,nro_autorizacion,codigo_control,importe,observaciones,cod_estadofactura) 
      values ('$cod_unidadorganizacional','$codigo','$cod_unidadorganizacional','$cod_area','$fecha_actual_cH','$fecha_limite_emision','$cod_tipopago','$cod_cliente','$cod_personal','$razon_social','$nitCliente','$cod_dosificacionfactura','$nro_correlativo','$nroAutorizacion','$code','$totalFinalRedondeado','$observaciones','1')");
    $flagSuccess=$stmtInsertSoliFact->execute();

    if($flagSuccess){
      //obtenemos el registro del ultimo insert
      $stmtNroFac = $dbh->prepare("SELECT codigo from facturas_venta where cod_solicitudfacturacion='$codigo' order by codigo desc LIMIT 1");
    $stmtNroFac->execute();
    $resultNroFact = $stmtNroFac->fetch();    
    $cod_facturaVenta = $resultNroFact['codigo'];

      while ($row = $stmt->fetch()) 
      { 
        $cod_claservicio_x=$row['cod_claservicio'];
        $cantidad_x=$row['cantidad'];
        $precio_x=$row['precio'];
        $descripcion_alterna_x=$row['descripcion_alterna'];

        $stmtInsertSoliFactDet = $dbh->prepare("INSERT INTO facturas_ventadetalle(cod_facturaventa,cod_claservicio,cantidad,precio,descripcion_alterna) 
        values ('$cod_facturaVenta','$cod_claservicio_x','$cantidad_x','$precio_x','$descripcion_alterna_x')");
        $flagSuccess=$stmtInsertSoliFactDet->execute();

        header('Location: ../simulaciones_servicios/generarFacturas.php?codigo='.$codigo);
      }  
    }

  }else{//ya se registro



    echo "ya se registró la factura.";
    header('Location: ../simulaciones_servicios/generarFacturas.php?codigo='.$codigo);
  }




    
    


?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
