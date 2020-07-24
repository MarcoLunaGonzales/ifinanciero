<?php
require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'functions.php';

date_default_timezone_set('America/La_Paz');

function obtenerListaVentasResumido($unidades,$areas,$soloTienda,$desde,$hasta){

    $dbh = new Conexion();
    $queryTienda="";
    if($soloTienda==1){
      $queryTienda=" and f.cod_solicitudfacturacion=-100";
    }
    $sql="SELECT f.codigo, f.cod_solicitudfacturacion, 
    (SELECT uo.abreviatura from unidades_organizacionales uo where uo.codigo=f.cod_unidadorganizacional)uo, 
    (SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area, 
    f.fecha_factura, f.razon_social, f.nit, f.cod_personal, 
    (SELECT SUM((cantidad*precio)-descuento_bob) as importe from facturas_ventadetalle where cod_facturaventa=f.codigo )as importe_real, f.nro_factura, 
    (SELECT concat(p.paterno,' ',p.primer_nombre) from personal p where p.codigo=f.cod_personal) as facturador, da.porcentaje, 
    (SELECT concat(p.paterno,' ',p.primer_nombre) from personal p, solicitudes_facturacion sf where p.codigo=sf.cod_personal and sf.codigo=f.cod_solicitudfacturacion) as solicitante
      FROM facturas_venta f, facturas_venta_distribucion da 
    WHERE da.cod_factura=f.codigo and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_estadofactura<>2 and f.cod_unidadorganizacional in ($unidades) and da.cod_area in ($areas) 
    $queryTienda
    order by area, fecha_factura, nro_factura";
    //echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}


function obtenerListaVentasArea($unidades,$areas,$desde,$hasta){

    $dbh = new Conexion();

    $valorIVA=100-(obtenerValorConfiguracion(1));

    $sql="SELECT da.cod_area, (SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area, SUM(((fd.cantidad*fd.precio)-fd.descuento_bob)*(da.porcentaje/100)*($valorIVA/100))as importe_real FROM facturas_venta f, facturas_ventadetalle fd, facturas_venta_distribucion da WHERE da.cod_factura=f.codigo and f.codigo=fd.cod_facturaventa and fd.cod_facturaventa=da.cod_factura
        and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_estadofactura<>2 and f.cod_unidadorganizacional in ($unidades) and da.cod_area in ($areas) group by area order by area";
    
    //echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}



 ?>