<?php
require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'functions.php';

date_default_timezone_set('America/La_Paz');

function obtenerListaVentasResumido($unidades,$areas,$soloTienda,$desde,$hasta,$soloCredito){

    $dbh = new Conexion();
    $queryTienda="";
    if($soloTienda==1){
      $queryTienda=" and f.cod_solicitudfacturacion=-100";
    }
    $queryCredito="";
    if($soloCredito==1){
      $queryCredito=" and f.cod_tipopago=217";
    }
    $sql="SELECT f.codigo, f.cod_solicitudfacturacion, 
    (SELECT uo.abreviatura from unidades_organizacionales uo where uo.codigo=f.cod_unidadorganizacional)uo, 
    (SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area, 
    f.fecha_factura, f.razon_social, f.nit, f.cod_personal, 
    (SELECT SUM((cantidad*precio)-descuento_bob) as importe from facturas_ventadetalle where cod_facturaventa=f.codigo )as importe_real, f.nro_factura, 
    (SELECT concat(p.paterno,' ',p.primer_nombre) from personal p where p.codigo=f.cod_personal) as facturador, da.porcentaje, 
    (SELECT concat(p.paterno,' ',p.primer_nombre) from personal p, solicitudes_facturacion sf where p.codigo=sf.cod_personal and sf.codigo=f.cod_solicitudfacturacion) as solicitante
      FROM facturas_venta f, facturas_venta_distribucion da
WHERE da.cod_factura=f.codigo  and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_estadofactura<>2 and f.cod_unidadorganizacional in ($unidades) and da.cod_area in ($areas) 
    $queryTienda $queryCredito
    order by area, fecha_factura, nro_factura";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}


function obtenerListaVentasArea($unidades,$areas,$desde,$hasta,$soloCredito){

    $dbh = new Conexion();

    $valorIVA=100-(obtenerValorConfiguracion(1));
    $queryCredito="";
    if($soloCredito==1){
      $queryCredito=" and f.cod_tipopago=217";
    }

    $sql="SELECT da.cod_area, (SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area, SUM(((fd.cantidad*fd.precio)-fd.descuento_bob)*(da.porcentaje/100)*($valorIVA/100))as importe_real FROM facturas_venta f, facturas_ventadetalle fd, facturas_venta_distribucion da WHERE da.cod_factura=f.codigo and f.codigo=fd.cod_facturaventa and fd.cod_facturaventa=da.cod_factura and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_estadofactura<>2 and f.cod_unidadorganizacional in ($unidades) and da.cod_area in ($areas) $queryCredito group by area order by area";
    //echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}



function obtenerListaVentasA_servicios($unidades,$cod_area,$servicios,$desde,$hasta){
    // if(11,12,38,39,40){

    // }
    if($servicios==0) 
        $sql_aux="";
        // $sql_aux=" and f.cod_area in ($cod_area)"; 
    else $sql_aux="and cs.Idtipo in ($servicios)";
    $valorIVA=100-(obtenerValorConfiguracion(1));
    $dbh = new Conexion();
    $sql="SELECT da.cod_area,(SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area,cs.IdTipo,cs.Codigo,cs.descripcion_n2,SUM(((s.cantidad*s.precio)-s.descuento_bob)*(da.porcentaje/100)*($valorIVA/100)) as importe_real 
    From facturas_venta f,facturas_ventadetalle s,facturas_venta_distribucion da, cla_servicios cs 
    where f.codigo=s.cod_facturaventa and da.cod_factura=f.codigo and s.cod_claservicio=cs.IdClaServicio $sql_aux and f.cod_estadofactura<>2 and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_unidadorganizacional in ($unidades) and da.cod_area in ($cod_area) GROUP BY cs.Idtipo order by area";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}
function obtenerListaVentas_cursos($unidades,$IdtipoX,$cod_area,$desde,$hasta){
    if($IdtipoX==0) $sql_aux="";
    else $sql_aux=" and m.IdCurso in ($IdtipoX)";
    $valorIVA=100-(obtenerValorConfiguracion(1));
    $dbh = new Conexion();
    $sql="SELECT da.cod_area,(SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area,m.IdCurso,SUM(((fd.cantidad*fd.precio)-fd.descuento_bob)*(da.porcentaje/100)*($valorIVA/100))as importe_real 
    FROM facturas_venta f,facturas_ventadetalle fd,facturas_venta_distribucion da,ibnorca.modulos m
    WHERE f.codigo=fd.cod_facturaventa and da.cod_factura=f.codigo and fd.cod_claservicio=m.IdModulo and f.cod_estadofactura<>2  
    and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_unidadorganizacional in ($unidades) $sql_aux and da.cod_area in ($cod_area) and f.cod_solicitudfacturacion<>-100 GROUP BY m.IdCurso order by area";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}
function obtenerListaVentas_cursos_tienda($unidades,$IdtipoX,$cod_area,$desde,$hasta){
    if($IdtipoX==0) $sql_aux="";
    else $sql_aux=" and m.curso_id in ($IdtipoX)";
    $valorIVA=100-(obtenerValorConfiguracion(1));
    $dbh = new Conexion();
    $sql="SELECT da.cod_area,(SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area,m.curso_id as IdCurso,SUM(((fd.cantidad*fd.precio)-fd.descuento_bob)*(da.porcentaje/100)*($valorIVA/100))as importe_real 
    FROM facturas_venta f,facturas_ventadetalle fd,facturas_venta_distribucion da,ibnorcatienda.pago_curso m
    WHERE f.codigo=fd.cod_facturaventa and da.cod_factura=f.codigo and fd.cod_claservicio=m.pago_id and f.cod_estadofactura<>2  
    and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_unidadorganizacional in ($unidades) $sql_aux and da.cod_area in ($cod_area) and f.cod_solicitudfacturacion=-100 GROUP BY m.curso_id order by area";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}
function obtenerListaVentas_libretas(){
    $dbh = new Conexion();
    //$sql="SELECT lbf.* from facturas_venta f,libretas_bancariasdetalle_facturas lbf where f.codigo=lbf.cod_facturaventa and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' GROUP BY lbf.cod_libretabancariadetalle order by f.nro_factura";
    $sql="SELECT ld.codigo,ld.descripcion,ld.informacion_complementaria,ld.monto from libretas_bancariasdetalle ld where ld.cod_estadoreferencial=1";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}

function obtenerFacturasLibreta($codigo){
    $dbh = new Conexion();
    $sql="SELECT f.nro_factura from libretas_bancariasdetalle_facturas l, facturas_venta f where l.cod_facturaventa=f.codigo and l.cod_libretabancariadetalle=$codigo and f.cod_estadofactura<>2 group by f.nro_factura";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor="";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $valor.="F ".$row['nro_factura'].",";
    }    
    $valor=trim($valor,",");
    return($valor);
}
function obtener_facturas_libreta($codigo){
    $dbh = new Conexion();
    $sql="SELECT f.codigo from libretas_bancariasdetalle_facturas l, facturas_venta f where l.cod_facturaventa=f.codigo and l.cod_libretabancariadetalle=$codigo and f.cod_estadofactura<>2";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor="";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {                
        $valor.=$row['codigo'].",";        
    }    
    $valor=trim($valor,',');    
    return($valor);
}
function sumatotaldetallefactura_libretas($codigo){
    $cod_facturas=obtener_facturas_libreta($codigo);
    $dbh = new Conexion();
    $sql="SELECT sum((f.precio*f.cantidad)-f.descuento_bob) as importe from facturas_ventadetalle f where cod_facturaventa in ($cod_facturas)";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {        
        $valor=$row['importe'];
    }        
    return($valor);
 }   
 function obtener_saldo_total_facturas(){
    $dbh = new Conexion();
    // $sql="SELECT f.codigo from facturas_venta f,libretas_bancariasdetalle_facturas lbf where f.codigo=lbf.cod_facturaventa and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_estadofactura<>2 GROUP BY lbf.cod_facturaventa";
    $sql="SELECT f.codigo from facturas_venta f,libretas_bancariasdetalle_facturas lbf where f.codigo=lbf.cod_facturaventa and f.cod_estadofactura<>2 GROUP BY lbf.cod_facturaventa";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {        
        $codigo=$row['codigo'];
        $monto_factura=sumatotaldetallefactura($codigo);
        $valor=$valor+$monto_factura;
    }        
    return($valor);

 }


 function obtenerListaGastosResumido($unidades,$areas,$desde,$hasta){
    $dbh = new Conexion();
    $sql="SELECT cd.codigo,c.fecha,c.numero,c.cod_tipocomprobante,cd.cod_comprobante,cd.cod_unidadorganizacional,cd.cod_area,cd.cod_cuenta,cd.cod_cuentaauxiliar,cd.debe,cd.haber,(cd.debe-cd.haber) as monto,
    cd.glosa,u.abreviatura as unidad,a.abreviatura as area,p.nombre as cuenta,p.numero as numero_cuenta,(SELECT CONCAT(pe.primer_nombre,' ',pe.otros_nombres,' ',pe.paterno,' ',pe.materno) from personal pe where pe.codigo=c.created_by) as personal,
(SELECT nombre from cuentas_auxiliares where codigo = cd.cod_cuentaauxiliar) as nombre_cliente_proveedor,
(SELECT cod_tipoauxiliar from cuentas_auxiliares where codigo = cd.cod_cuentaauxiliar) as cliente_proveedor 
FROM comprobantes_detalle cd
join comprobantes c on c.codigo=cd.cod_comprobante
join unidades_organizacionales u on u.codigo=cd.cod_unidadorganizacional
join areas a on a.codigo=cd.cod_area
join plan_cuentas p on p.codigo=cd.cod_cuenta
where cd.cod_unidadorganizacional in ($unidades) and cd.cod_area in ($areas) and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and p.numero like '5%' order by c.fecha,c.numero,c.cod_tipocomprobante";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
  }

function obtenerListaGastosPorArea($unidades,$areas,$desde,$hasta){
    $dbh = new Conexion();
    $sql="SELECT da.cod_area, (SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area, 
SUM(da.debe-da.haber)as monto_real 
FROM comprobantes_detalle da 
join comprobantes c on c.codigo=da.cod_comprobante
join unidades_organizacionales u on u.codigo=da.cod_unidadorganizacional
join areas a on a.codigo=da.cod_area
join plan_cuentas p on p.codigo=da.cod_cuenta
where da.cod_unidadorganizacional in ($unidades) and da.cod_area in ($areas) and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and p.numero like '5%' 
group by area order by area";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
  }

 function obtenerNombreCurso($codigo){
  $dbhIBNO = new ConexionIBNORCA();
  //datos del estudiante y el curso que se encuentra
  $sqlIBNORCA="SELECT pc.Nombre from programas_cursos pc where pc.IdCurso=$codigo";  
  $stmtIbno = $dbhIBNO->prepare($sqlIBNORCA);
  $stmtIbno->execute();
  $resultSimu = $stmtIbno->fetch();
  $valor = $resultSimu['Nombre'];
  return($valor);
}

function obtenerListaCuentasEgreso($unidades,$areas,$cuentas,$desde,$hasta){
    $dbh = new Conexion();
    $sql="SELECT da.cod_area,da.cod_cuenta,(SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area,p.nombre as cuenta,p.numero as numero_cuenta,SUM((da.debe-da.haber)) as monto_real 
    From comprobantes_detalle da
    join comprobantes c on c.codigo=da.cod_comprobante
    join unidades_organizacionales u on u.codigo=da.cod_unidadorganizacional
    join areas a on a.codigo=da.cod_area
    join plan_cuentas p on p.codigo=da.cod_cuenta
    where da.cod_unidadorganizacional in ($unidades) and da.cod_area in ($areas) and c.cod_estadocomprobante<>2 and c.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and p.numero like '5%'
    and p.codigo in ($cuentas)
    GROUP BY p.codigo order by da.cod_area";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}

function obtenerListaVentasResumidoAdministrativo($unidades,$areas,$formas,$desde,$hasta,$personal){

    $dbh = new Conexion();
    $sql="SELECT f.cod_personal,f.codigo, f.cod_solicitudfacturacion, 
    (SELECT uo.abreviatura from unidades_organizacionales uo where uo.codigo=f.cod_unidadorganizacional)uo, 
    (SELECT a.abreviatura from areas a where a.codigo=f.cod_area)area,
    (SELECT t.nombre from tipos_pago t where t.codigo=f.cod_tipopago) as tipo_pago,  
    f.fecha_factura, f.razon_social, f.nit, f.cod_personal, 
    (SELECT SUM((cantidad*precio)-descuento_bob) as importe from facturas_ventadetalle where cod_facturaventa=f.codigo )as importe_real, f.nro_factura, 
    (SELECT sf.nro_correlativo from solicitudes_facturacion sf where sf.codigo=f.cod_solicitudfacturacion)as nro_solicitud
      FROM facturas_venta f
WHERE f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_estadofactura<>2 and f.cod_unidadorganizacional in ($unidades) and f.cod_area in ($areas) 
    and f.cod_personal in ($personal) and f.cod_tipopago in ($formas)
    order by fecha_factura, nro_factura";
   // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}

function obtenerNombreDetalleFactura($codigoFactura){
    $dbh=new Conexion();
    $sql="SELECT fd.cod_claservicio,
CASE 
    WHEN da.cod_area <> 13 THEN (select cs.Descripcion from cla_servicios cs where cs.IdClaServicio=fd.cod_claservicio)
    WHEN (da.cod_area=13 and f.cod_solicitudfacturacion<>-100) THEN (select v.codigo_curso from v_cursosdetalles v where v.IdModulo=fd.cod_claservicio)
    WHEN (da.cod_area=13 and f.cod_solicitudfacturacion=-100) THEN (select vcd.codigo_curso from v_cursosdetalles vcd, v_facturacion_tienda vft where vft.pago_id=fd.cod_claservicio and vft.pcm_modulo_id=vcd.IdModulo limit 0,1)
    END AS item 
from facturas_venta f, facturas_ventadetalle fd, facturas_venta_distribucion da where f.codigo=fd.cod_facturaventa and f.codigo=da.cod_factura and 
f.codigo='$codigoFactura' GROUP BY fd.cod_claservicio;";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $valor="";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {        
        $codItem=$row['cod_claservicio'];
        $nombreItem=$row['item'];
        if (stristr($valor, $nombreItem)) {
            //  no pasa nada
        }else{
            $valor=$valor." - ".$nombreItem;        
        }
    }
    $valor=substr($valor, 2);     
    return($valor);
}

?>

