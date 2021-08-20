<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';

require_once 'configModule.php';
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$unidad=$_POST["unidad"];
$areas=$_POST["areas"];
$stringUnidadesX=implode(",", $unidad);
$stringAreasX=implode(",", $areas);
$personal=$_POST["personal"];
$stringPersonalX=implode(",", $personal);
$estados=$_POST["estado"];
$stringEstadoX=implode(",", $estados);

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

$sql="SELECT f.*,DATE_FORMAT(f.fecha_registro,'%d/%m/%Y')as fecha_x,es.nombre as estado  from solicitudes_facturacion f join estados_solicitudfacturacion es on f.cod_estadosolicitudfacturacion=es.codigo where f.cod_unidadorganizacional in ($stringUnidadesX) and f.fecha_registro BETWEEN '$desde' and '$hasta' and f.cod_area in ($stringAreasX) and f.cod_personal in ($stringPersonalX) and f.cod_estadosolicitudfacturacion in ($stringEstadoX) ORDER BY f.fecha_registro asc";
$stmt2 = $dbh->prepare($sql);
// echo $sql; 
// Ejecutamos
$stmt2->execute();
//resultado
$stmt2->bindColumn('codigo', $codigo_facturacion);
$stmt2->bindColumn('fecha_x', $fecha);
$stmt2->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmt2->bindColumn('cod_area', $cod_area);
$stmt2->bindColumn('cod_cliente', $cod_cliente);
$stmt2->bindColumn('cod_personal', $cod_personal);
$stmt2->bindColumn('razon_social', $razon_social);
$stmt2->bindColumn('nit', $nit);
$stmt2->bindColumn('observaciones', $observaciones);
$stmt2->bindColumn('observaciones_2', $observaciones_2);
$stmt2->bindColumn('nro_correlativo', $nro_correlativo);
$stmt2->bindColumn('codigo_alterno', $codigo_alterno);
$stmt2->bindColumn('persona_contacto', $persona_contacto);
$stmt2->bindColumn('cod_estadosolicitudfacturacion', $codEstado);  
$stmt2->bindColumn('obs_devolucion', $obs_devolucion);  
$stmt2->bindColumn('estado', $estado_x);  

//datos de la factura
$stmtPersonal = $dbh->prepare("SELECT * from titulos_oficinas where cod_uo in ($stringUnidadesX)");
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$sucursal=$result['sucursal'];
$direccion=$result['direccion'];
$nit=$result['nit'];
$razon_social=$result['razon_social'];

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
            
            <h4 class="card-title text-center">Solicitudes de Facturación</h4>                  
            <h6 class="card-title">Unidad: <?=$stringUnidades;?></h6>
            <h6 class="card-title">Area: <?=$stringAreas;?></h6>
            <div class="row">
               <h6 class="card-title col-sm-3"><?=$fechaTitulo?></h6>                     
            </div> 
          </div>
          <div class="card-body">
            <div class="table-responsive">
              
              <!-- reporte_solicitud_facturacion -->
              <table id="tablePaginatorHeaderFooter" class="table table-bordered table-condensed" style="width:100%">
                <thead>                              
                  <tr>
                    <th><small><b>-</b></small></th>   
                    <th width="5%"><small><b>Of/Area</b></small></th>                                
                    <th width="4%"><small><b>#Sol.</b></small></th>
                    <th width="10%"><small><b>Responsable</b></small></th>
                    <th width="10%"><small><b>Código Servicio</b></small></th>
                    <th width="10%"><small><b>Cliente</b></small></th>
                    <th width="5%"><small><b>Fecha</b></small></th>
                    <th width="5%"><small><b>Importe</b></small></th>                                  
                    <th><small><b>Razón Social</b></small></th>
                    <th><small><b>Observaciones</b></small></th>                      
                    <th width="3%"><small><b>Nro.<br>Factura</b></small></th>
                    <th width="8%"><small><b>Forma Pago</b></small></th>
                    <th width="5%"><small><b>Estado</b></small></th>
                    <th ></th>
                  </tr>                                  
                </thead>
                <tbody>
                  <?php
                  $index=0;                   
                  while ($row = $stmt2->fetch()) { 
                    $index++;
                    $nombre_personal=namePersonal($cod_personal);
                    $nombre_uo=abrevUnidad($cod_unidadorganizacional);
                    $nombre_area=abrevArea($cod_area);
                    $observaciones_string=obtener_string_observaciones($obs_devolucion,$observaciones,$observaciones_2);
                    $cliente_x=nameCliente($cod_cliente);
                    $string_formaspago=obtnerFormasPago($codigo_facturacion);
                    $sumaTotalImporte=obtenerSumaTotal_solicitudFacturacion($codigo_facturacion);
                    switch ($codEstado) {
                      case 1:                                
                        $label='<span style="padding:1;" class="badge badge-default">';                        
                      break;
                      case 2:                                
                        $label='<span style="padding:1;" class="badge badge-danger">';                        
                      break;
                      case 3:                                
                        $label='<span style="padding:1;" class="badge badge-success">';                        
                      break;
                      case 4:                                
                        $label='<span style="padding:1;" class="badge badge-warning">';                        
                      break;
                      case 5:                                
                        $label='<span style="padding:1;" class="badge badge-warning">';                        
                      break;
                      case 6:                                
                        $label='<span style="padding:1;" class="badge badge-default">';                        
                      break;
                    }
                    //verificamos si ya tiene factura generada y esta activa                           
                    $stmtFact = $dbh->prepare("SELECT codigo, nro_factura, cod_estadofactura, razon_social, nit, nro_autorizacion, importe, cod_comprobante from facturas_venta where cod_solicitudfacturacion=$codigo_facturacion order by codigo desc limit 1");
                    $stmtFact->execute();
                    $resultSimu = $stmtFact->fetch();
                    $codigo_fact_x = $resultSimu['codigo'];
                    $nro_fact_x = $resultSimu['nro_factura'];
                    $cod_estado_factura_x = $resultSimu['cod_estadofactura'];                    
                    if ($nro_fact_x==null)$nro_fact_x="-";
                    else $nro_fact_x="F".$nro_fact_x;
                    if($cod_estado_factura_x==4){
                      // $btnEstado="btn-warning";
                      $label='<span class="badge badge-warning">';
                      $estado_x="FACTURA MANUAL";                    
                    }

                    ?>
                    <tr>
                      <td class="text-center small"><?=$index;?></td>
                      <td class="text-center small"><?=$nombre_uo;?>/<?=$nombre_area;?></td>
                      <td class="text-right small"><?=$nro_correlativo;?></td>                      
                      <td class="text-left small"><?=$nombre_personal;?></td>
                      <td class="text-center small"><?=$codigo_alterno;?></td>
                      <td class="text-right small"><?=$cliente_x;?></td>
                      <td class="text-center small"><?=$fecha;?></td>
                      <td class="text-right small"><?=formatNumberDec($sumaTotalImporte);?></td>
                      <td class="text-left small"><small><?=$razon_social;?></small></td>
                      <td class="text-left small"><?=$observaciones_string;?></td>
                      <td class="text-right small"><small><?=$nro_fact_x;?><br></small></td>
                      <td class="text-left small"><small><?=$string_formaspago;?></small></td>
                      <td class="text-right small"><?=$label."<small>".$estado_x;?></small></span></td>
                      <td class="td-actions text-right">    
                        <a href="<?=$urlVer_SF;?>?codigo=<?=$codigo_facturacion;?>" target="_blank" class="btn btn-info" title="Ver Solicitud">
                          <i class="material-icons">remove_red_eye</i>
                        </a>
                      </td>
                    </tr>
                    <?php                                  
                  }?>
                
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>

