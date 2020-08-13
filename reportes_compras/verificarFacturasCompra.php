<?php
session_start();
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsReportes.php';
require_once '../assets/libraries/CifrasEnLetras.php';

$dbh = new Conexion();
$sqlEstadoSol="";
if(isset($_GET['e'])){
  $sqlEstadoSol="and rep.cod_estadosolicitudrecurso in (".$_GET['e'].")";
}
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
                   <h4 class="card-title text-center">Reporte de Verificacion de Libro Compras</h4>
                </div>


<div class="card-body">
  <div class="">
    <?php
    $html='<table class="table table-bordered table-condensed" id="libro_mayor_rep">'.
            '<thead >'.
            '<tr class="text-center small" style="background:#40A3A8;color:#ffffff;">'.
              '<th width="5%"><small>Oficina / Area (Comp)</small></th>'.
              '<th width="5%"><small>Oficina / Area (Sol)</small></th>'.
              '<th width="8%"><small>Comprobante</small></th>'.
              '<th width="8%"><small>Fecha (Comp)</small></th>'.
              '<th width="5%"><small>Numero (Sol)</small></th>'.
              '<th width="10%"><small>Estado (Sol)</small></th>'.
              '<th width="5%"><small>Numero</small></th>'.
              '<th width="8%"><small>NIT</small></th>'.
              '<th width="8%"><small>Fecha</small></th>'.
              '<th width="20%"><small>Razon Social</small></th>'.
              '<th width="8%"><small>Importe</small></th>'.
              '<th width="8%"><small>EXE+ICE+TASA_CERO</small></th>'.
              '<th width="2%"><small>TIPO COMPRA</small></td>'.
            '</tr>'.
           '</thead>'.
           '<tbody>';

    
    $sql="SELECT * FROM 
(select 
  (SELECT cod_unidadorganizacional FROM comprobantes_detalle where codigo = f.cod_comprobantedetalle) as cod_unidadcomprobante,  
  (SELECT cod_area FROM comprobantes_detalle where codigo = f.cod_comprobantedetalle) as cod_areacomprobante,
  (SELECT cod_comprobante FROM comprobantes_detalle where codigo = f.cod_comprobantedetalle) as cod_comprobante,  
  (SELECT c.fecha FROM comprobantes_detalle cd join comprobantes c on c.codigo=cd.cod_comprobante where cd.codigo = f.cod_comprobantedetalle) as fecha_comprobante,
  (SELECT glosa FROM comprobantes_detalle where codigo = f.cod_comprobantedetalle) as glosa_comprobantedetalle,
  (SELECT cod_unidadorganizacional FROM solicitud_recursosdetalle where codigo = f.cod_solicitudrecursodetalle) as cod_unidadsolicitud,
  (SELECT cod_area FROM solicitud_recursosdetalle where codigo = f.cod_solicitudrecursodetalle) as cod_areasolicitud,    
  (SELECT cod_solicitudrecurso FROM solicitud_recursosdetalle where codigo = f.cod_solicitudrecursodetalle) as cod_solicitudrecurso,
  (SELECT sr.numero FROM solicitud_recursosdetalle sd join solicitud_recursos sr on sr.codigo=sd.cod_solicitudrecurso where sd.codigo = f.cod_solicitudrecursodetalle) as numero_solicitudrecurso,
  (SELECT e.nombre FROM solicitud_recursosdetalle sd join solicitud_recursos sr on sr.codigo=sd.cod_solicitudrecurso join estados_solicitudrecursos e on e.codigo=sr.cod_estadosolicitudrecurso where sd.codigo = f.cod_solicitudrecursodetalle) as estado_solicitudrecurso,
  (SELECT e.codigo FROM solicitud_recursosdetalle sd join solicitud_recursos sr on sr.codigo=sd.cod_solicitudrecurso join estados_solicitudrecursos e on e.codigo=sr.cod_estadosolicitudrecurso where sd.codigo = f.cod_solicitudrecursodetalle) as cod_estadosolicitudrecurso,
  (SELECT sr.cod_estadoreferencial FROM solicitud_recursosdetalle sd join solicitud_recursos sr on sr.codigo=sd.cod_solicitudrecurso join estados_solicitudrecursos e on e.codigo=sr.cod_estadosolicitudrecurso where sd.codigo = f.cod_solicitudrecursodetalle) as cod_estadorefsolicitudrecurso,   
  (SELECT c.cod_estadocomprobante FROM comprobantes_detalle cd join comprobantes c on c.codigo=cd.cod_comprobante where cd.codigo = f.cod_comprobantedetalle) as estado_comprobante,
  f.*
  from facturas_compra f 
  order by f.nit,f.nro_factura desc
) rep
where (rep.estado_comprobante=1 or rep.estado_comprobante is null)
and (rep.cod_estadorefsolicitudrecurso=1 or rep.cod_estadorefsolicitudrecurso is null) $sqlEstadoSol";


    //echo $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $totalImporte=0;
    while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $codigoX=$rowComp['codigo'];
        $cod_unidadcomprobante=abrevUnidad_solo($rowComp['cod_unidadcomprobante']);
        $cod_areacomprobante=abrevArea_solo($rowComp['cod_areacomprobante']);
        $cod_unidadsolicitud=abrevUnidad_solo($rowComp['cod_unidadsolicitud']);
        $cod_areasolicitud=abrevArea_solo($rowComp['cod_areasolicitud']);
        $nombreComprobante=nombreComprobante($rowComp['cod_comprobante']);
        $fecha_comprobante=$rowComp['fecha_comprobante'];
        $numero_solicitudrecurso=$rowComp['numero_solicitudrecurso'];
        $estado_solicitudrecurso=$rowComp['estado_solicitudrecurso'];
        $nro_factura=$rowComp['nro_factura'];
        $nit=$rowComp['nit'];
        $fecha=$rowComp['fecha'];
        $razon_social=$rowComp['razon_social'];
        $importe=$rowComp['importe'];
        $exe_ice_tasa=$rowComp['exento']+$rowComp['ice']+$rowComp['tasa_cero'];
        $tipo_compra=$rowComp['tipo_compra'];

        /*$a= new DateTime("1901-03-12");
        $b= new DateTime("2000-04-11");
        $res = ($a > $b) ? "mayor" : (($a < $b) ? "menor" : "igual");
        echo $res;*/
        $totalImporte+=$importe; 
        $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$cod_unidadcomprobante.'-'.$cod_areacomprobante.' </td>'.
                      '<td class="text-left font-weight-bold">'.$cod_unidadsolicitud.'-'.$cod_areasolicitud.'</td>'.
                      '<td class="text-right">'.$nombreComprobante.' </td>'.
                      '<td class="text-right">'.strftime('%d/%m/%Y',strtotime($fecha_comprobante)).' </td>'.
                      '<td class="text-right">'.$numero_solicitudrecurso.' </td>'.
                      '<td class="text-right">'.$estado_solicitudrecurso.' </td>'.
                      '<td class="text-right">'.$nro_factura.' </td>'.
                      '<td class="text-right">'.$nit.' </td>'.
                      '<td class="text-right">'.strftime('%d/%m/%Y',strtotime($fecha)).' </td>'.
                      '<td class="text-left">'.$razon_social.'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($importe).' </td>'.     
                      '<td class="text-right font-weight-bold">'.formatNumberDec($exe_ice_tasa).' </td>'.     
                      '<td class="text-right">'.$tipo_compra.'</td>'.
                  '</tr>';
    }
        $html.='<tr class="bg-secondary text-white">'.
                    '<td colspan="10" class="text-center">Importe Total</td>'.
                    '<td class="text-right font-weight-bold small">'.formatNumberDec($totalImporte).'</td>'.
                    '<td class="text-center"></td>'.
                    '<td class="text-center"></td>'.      
                '</tr>';

    $html.=    '</tbody></table>';

    echo $html;
    ?>
  </div>
</div>
              


              </div>
            </div>
          </div>  
        </div>
    </div>
