<?php
require_once 'conexion.php';
require_once 'conexion_externa.php';

date_default_timezone_set('America/La_Paz');

function obtenerListaVentasResumido($unidades,$areas,$soloTienda,$desde,$hasta){

    $dbh = new Conexion();
    $queryTienda="";
    if($soloTienda==1){
      $queryTienda=" and f.cod_solicitudfacturacion=-100";
    }
    $sql=" ( SELECT f.codigo, f.cod_solicitudfacturacion, 
    (SELECT uo.abreviatura from unidades_organizacionales uo where uo.codigo=f.cod_unidadorganizacional)uo, 
    (SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area, 
    f.fecha_factura, f.razon_social, f.nit, f.cod_personal, 
    (SELECT SUM((cantidad*precio)-descuento_bob) as importe from facturas_ventadetalle where cod_facturaventa=f.codigo )as importe_real, f.nro_factura, 
    (SELECT concat(p.paterno,' ',p.primer_nombre) from personal p where p.codigo=f.cod_personal) as facturador, da.porcentaje
      FROM facturas_venta f, solicitudes_facturacion_areas da 
    WHERE da.cod_solicitudfacturacion=f.cod_solicitudfacturacion and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_estadofactura<>2 and f.cod_unidadorganizacional in ($unidades) and da.cod_area in ($areas))
    union 
    (SELECT f.codigo, f.cod_solicitudfacturacion, (SELECT uo.abreviatura from unidades_organizacionales uo where uo.codigo=f.cod_unidadorganizacional)uo, (SELECT a.abreviatura from areas a where a.codigo=f.cod_area)area, f.fecha_factura, f.razon_social, f.nit, f.cod_personal, (SELECT SUM((cantidad*precio)-descuento_bob) as importe from facturas_ventadetalle where cod_facturaventa=f.codigo )as importe_real, f.nro_factura, (SELECT concat(p.paterno,' ',p.primer_nombre) from personal p where p.codigo=f.cod_personal) as facturador, 100 FROM facturas_venta f WHERE f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_estadofactura<>2 and f.cod_unidadorganizacional in ($unidades) and f.cod_area in ($areas) and f.cod_solicitudfacturacion=-100) 
    order by area, fecha_factura, nro_factura";
    //echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}




 ?>