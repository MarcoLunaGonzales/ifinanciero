<?php
require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'functions.php';

date_default_timezone_set('America/La_Paz');

function obtenerListaVentasResumido($unidades,$areas,$soloTienda,$desde,$hasta,$formas_pago){

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
      FROM facturas_venta f, facturas_venta_distribucion da,solicitudes_facturacion_tipospago tp
WHERE da.cod_factura=f.codigo and f.cod_solicitudfacturacion=tp.cod_solicitudfacturacion and tp.cod_tipopago in ($formas_pago) and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_estadofactura<>2 and f.cod_unidadorganizacional in ($unidades) and da.cod_area in ($areas) 
    $queryTienda
    order by area, fecha_factura, nro_factura";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}


function obtenerListaVentasArea($unidades,$areas,$desde,$hasta,$formas_pago){

    $dbh = new Conexion();

    $valorIVA=100-(obtenerValorConfiguracion(1));

    $sql="SELECT da.cod_area, (SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area, SUM(((fd.cantidad*fd.precio)-fd.descuento_bob)*(da.porcentaje/100)*($valorIVA/100))as importe_real FROM facturas_venta f, facturas_ventadetalle fd, facturas_venta_distribucion da,solicitudes_facturacion_tipospago tp WHERE da.cod_factura=f.codigo and f.codigo=fd.cod_facturaventa and fd.cod_facturaventa=da.cod_factura and f.cod_solicitudfacturacion=tp.cod_solicitudfacturacion and tp.cod_tipopago in ($formas_pago)
        and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_estadofactura<>2 and f.cod_unidadorganizacional in ($unidades) and da.cod_area in ($areas) group by area order by area";
    // echo $sql;
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
    $sql="SELECT da.cod_area,(SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area,cs.IdTipo,cs.Codigo,cs.descripcion_n2,SUM(((s.cantidad*s.precio)-s.descuento_bob)*(da.porcentaje/100)) as importe_real 
    From facturas_venta f,facturas_ventadetalle s,facturas_venta_distribucion da, cla_servicios cs 
    where f.codigo=s.cod_facturaventa and da.cod_factura=f.codigo and s.cod_claservicio=cs.IdClaServicio $sql_aux and f.cod_estadofactura<>2 and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_unidadorganizacional in ($unidades) and f.cod_area in ($cod_area) GROUP BY cs.Idtipo order by f.cod_area";
    // echo $sql;

    //     if($servicios==0){ 
    //     $sql_aux=" and f.cod_area in ($cod_area)"; }
    // else $sql_aux="and sf.cod_simulacion_servicio in ($servicios)";

    // $dbh = new Conexion();
    // $valorIVA=100-(obtenerValorConfiguracion(1));
    // $sql="SELECT f.cod_area,(SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area,cs.IdTipo,cs.Codigo,cs.descripcion_n2,SUM(((fd.cantidad*fd.precio)-fd.descuento_bob)*(da.porcentaje/100))as importe_real 
    // From facturas_venta f,facturas_venta_distribucion da,facturas_ventadetalle fd, cla_servicios cs 
    // where f.codigo=fd.cod_facturaventa and da.cod_factura=f.codigo and fd.cod_claservicio=cs.IdClaServicio $sql_aux and f.cod_estadofactura<>2 and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_unidadorganizacional in ($unidades)  GROUP BY cs.Idtipo order by area";

    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}
function obtenerListaVentas_cursos($unidades,$IdtipoX,$cod_area,$desde,$hasta){
    if($IdtipoX==0) $sql_aux="";
    else $sql_aux="and sf.cod_simulacion_servicio in ($IdtipoX)";
    $dbh = new Conexion();
    $sql="SELECT sf.cod_simulacion_servicio, SUM((s.cantidad*s.precio)-s.descuento_bob)as importe_real From facturas_venta f,facturas_ventadetalle s, solicitudes_facturacion sf where f.codigo=s.cod_facturaventa and f.cod_solicitudfacturacion=sf.codigo and sf.tipo_solicitud in (2,6)  and f.cod_estadofactura<>2 and  sf.cod_estadosolicitudfacturacion<>2 and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_unidadorganizacional in ($unidades) $sql_aux and f.cod_area in ($cod_area) GROUP BY sf.cod_simulacion_servicio";
    // echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    return($stmt);
}
function obtenerListaVentas_cursos_grupal($unidades,$IdtipoX,$desde,$hasta){
    if($IdtipoX==0) $sql_aux="";
    else $sql_aux="and sfd.cod_curso in ($IdtipoX)";
    $dbh = new Conexion();
    $sql="SELECT sfd.cod_curso, SUM(sfd.cantidad*sfd.precio)as importe_real
    from facturas_venta f, solicitudes_facturacion sf, solicitudes_facturaciondetalle sfd
    WHERE f.cod_solicitudfacturacion=sf.codigo and sf.codigo=sfd.cod_solicitudfacturacion and sf.tipo_solicitud=7 and f.cod_estadofactura<>2 and  sf.cod_estadosolicitudfacturacion<>2 BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_unidadorganizacional in ($unidades) $sql_aux GROUP BY sfd.cod_curso
    ";
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
 ?>
