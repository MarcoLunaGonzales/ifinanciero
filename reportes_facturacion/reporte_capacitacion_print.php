<?php //ESTADO FINALIZADO

require_once '../conexion.php';

require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../functionsReportes.php';
require_once '../fpdf_html.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$unidad=$_POST["unidad"];
$areas=$_POST["areas"];
$stringUnidadesX=implode(",", $unidad);
$stringAreasX=implode(",", $areas);

$porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
$porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);
$desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
$hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
$fechaTitulo="De ".strftime('%d/%m/%Y',strtotime($desde))." a ".strftime('%d/%m/%Y',strtotime($hasta));
// echo $areaString;
$stringUnidades="";
foreach ($unidad as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}
$stringAreas="";
foreach ($areas as $valor ) {    
    $stringAreas.=" ".abrevArea($valor)." ";
}

$sql="SELECT f.cod_cliente,f.cod_unidadorganizacional,s.cod_claservicio as cod_modulo, ((s.cantidad*s.precio)-s.descuento_bob)*(da.porcentaje/100) as importe_real,s.ci_estudiante,f.nro_factura,f.cod_personal p_factura,f.cod_solicitudfacturacion, f.razon_social, f.nit
    From facturas_venta f,facturas_ventadetalle s,facturas_venta_distribucion da
    where f.codigo=s.cod_facturaventa and da.cod_factura=f.codigo and f.cod_estadofactura<>2 and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_unidadorganizacional in ($stringUnidadesX) and da.cod_area in ($stringAreasX) order by f.nro_factura,s.ci_estudiante";
$stmt2 = $dbh->prepare($sql);
// echo $sql; 
// Ejecutamos
$stmt2->execute();
//resultado
$stmt2->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);
$stmt2->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmt2->bindColumn('cod_modulo', $cod_modulo);
$stmt2->bindColumn('importe_real', $importe_real);
$stmt2->bindColumn('ci_estudiante', $ci_estudiante);
$stmt2->bindColumn('nro_factura', $nro_factura);
$stmt2->bindColumn('p_factura', $p_factura);
$stmt2->bindColumn('cod_cliente', $cod_cliente);
$stmt2->bindColumn('razon_social', $razonSocialCliente);
$stmt2->bindColumn('nit', $nitCliente);
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
            </div>
            <h4 class="card-title text-center">Recaudaciones Formación</h4>     

            <h6 class="card-title">Unidad: <?=$stringUnidades;?></h6>
            <h6 class="card-title">Area: <?=$stringAreas;?></h6>
            <div class="row">
               <h6 class="card-title col-sm-3"><?=$fechaTitulo?></h6>                     
            </div> 
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tablePaginatorHeaderFooter" class="table table-bordered table-condensed" style="width:100%">
                <thead>                              
                  <tr>
                    <th><small><b>-</b></small></th>   
                    <th width="5%"><small><b>Of.</b></small></th>                                
                    <th width="4%"><small><b>Factura</b></small></th>
                    <th width="4%"><small><b>NIT</b></small></th>
                    <th><small><b>Razón Social</b></small></th>
                    <th ><small><b>Facturado por:</b></small></th>
                    <th width="4%"><small><b>Solicitud</b></small></th>
                    <th ><small><b>Solicitado por:</b></small></th>
                    <th width="10%"><small><b>CodigoCurso</b></small></th>
                    <th ><small><b>Nombre Curso</b></small></th>
                    <th ><small><b>Nombre Módulo</b></small></th>
                    <th><small><b>C.I.</b></small></th>
                    <th><small><b>Estudiante</b></small></th>
                    <th width="5%"><small><b>Monto</b></small></th>
                    <th width="5%"><small><b>Monto Neto</b></small></th>
                  </tr>                                  
                </thead>
                <tbody>
                  <?php
                  $importe_real_total=0;
                  $index=0;                   
                  while ($row = $stmt2->fetch()) { 
                    $index++;
                    $stringDatos=obtenerDatosSolicitudFacturacion($cod_solicitudfacturacion,$cod_modulo);
                    // echo $stringDatos."-<br>";                    
                    $datos=explode("#####", $stringDatos);
                    if($cod_solicitudfacturacion==-100){
                      $nro_correlativo="-";
                      $cod_modulo=$datos[0];
                      $ci_estudiante=$datos[1]; 
                      $cod_curso=$datos[2];
                      $tipo_solicitud=$datos[3];
                      $encargado_sf="-";
                      $encargado_factura="Tienda virtual";
                    }else{//cuando no es de la tienda
                      $encargado_factura=namePersonal($p_factura);
                      $encargado_sf=namePersonal($datos[1]);//devuelve cod_personal
                      $nro_correlativo=$datos[0];
                      $cod_curso=$datos[2];
                      $tipo_solicitud=$datos[3];                      
                    }
                    switch ($tipo_solicitud){
                      case 4:
                        $Codigo_alterno=0;
                        $nombre_curso="Registro Manual";
                        $nombre_modulo="Registro Manual";
                        $ci_estudiante="-";
                        $nombre_estudiante="-";
                        break;
                      case 6:
                        $nombre_estudiante=nameCliente($cod_cliente);
                        break;
                      
                      default:
                        $Codigo_alterno=obtenerCodigoExternoCurso($cod_curso);
                        $nombre_curso=obtenerNombreCurso($cod_curso);
                        $nombre_modulo=obtenerNombreModulo($cod_modulo);
                        $nombre_estudiante=obtenerNombreEstudiante($ci_estudiante);
                        break;
                    }
                    $nombre_uo=abrevUnidad($cod_unidadorganizacional);
                    $importe_real_total+=$importe_real;
                    //echo $tipo_solicitud;
                    // if($tipo_solicitud!=4)
                    // {
                      
                      ?>
                      <tr>
                        <td class="text-center small"><?=$index;?></td>
                        <td class="text-left small"><?=$nombre_uo;?></td>
                        <td class="text-right small"><small><?=$nro_factura;?></small></td>
                        <td class="text-right small"><small><?=$nitCliente;?></small></td>
                        <td class="text-left small"><small><?=mb_strtoupper($razonSocialCliente);?></small></td>
                        <td class="text-left small"><small><?=$encargado_factura;?></small></td>
                        <td class="text-right small"><small><?=$nro_correlativo;?><br></small></td>
                        <td class="text-left small"><small><?=$encargado_sf;?></small></td>                      
                        <td class="text-left small"><?=mb_strtoupper($Codigo_alterno);?></td>
                        <td class="text-left small"><small><?=mb_strtoupper($nombre_curso);?></small></td>
                        <td class="text-left small"><small><?=mb_strtoupper($nombre_modulo);?></small></td>
                        <td class="text-right small"><?=$ci_estudiante;?></td>
                        <td class="text-left small"><?=mb_strtoupper($nombre_estudiante);?></td>
                        <td class="text-right small"><?=formatNumberDec($importe_real);?></td>
                        <td class="text-right small"><?=formatNumberDec($importe_real*0.87);?></td>
                      </tr>
                    <?php //}
                  }?>
                </tbody>
                <tfoot>
                  <tr>
                    <td>-</td>
                    <td>-</td>
                    <td></td>
                    <td></td>                      
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>                      
                    <td>-</td>
                    <td>-</td>
                    <td class="text-left small">TOTALES</td>
                    <td class="text-left small"><?=formatNumberDec($importe_real_total);?></td>
                    <td class="text-left small"><?=formatNumberDec($importe_real_total*0.87);?></td>
                  </tr>  
                </tfoot>
                
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>

