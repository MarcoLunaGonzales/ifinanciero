<?php
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

session_start();

$dbh = new Conexion();
// Preparamos

$unidad=$_POST["unidad"];
$nombreUnidad=nameUnidad($unidad);
$tipo=$_POST["tipo_comprobante"];
$desde=strftime('%Y-%m-%d',strtotime($_POST["fecha_desde"]));
$hasta=strftime('%Y-%m-%d',strtotime($_POST["fecha_hasta"]));
$moneda=$_POST["moneda"];

$query1="SELECT (select u.nombre from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad,
(select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)cod_unidad,c.cod_gestion, 
(select m.nombre from monedas m where m.codigo=c.cod_moneda)moneda, (select m.codigo from monedas m where m.codigo=c.cod_moneda)cod_moneda,
(select t.nombre from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero,c.codigo, c.glosa
from comprobantes c where c.cod_unidadorganizacional=$unidad and c.fecha BETWEEN '$desde' and '$hasta' and(";
for ($i=0; $i < cantidadF($tipo); $i++) { 
  
  if($i==(cantidadF($tipo)-1)){
  $query1.=" c.cod_tipocomprobante=".$tipo[$i].")";
  }else{
  $query1.=" c.cod_tipocomprobante=".$tipo[$i]." or";
  }   
}
$stmt = $dbh->prepare($query1);
// Ejecutamos
$stmt->execute();
// bindColumn
/*$stmt->bindColumn('unidad', $nombreUnidad);
$stmt->bindColumn('cod_unidad', $codigoUnidad);
$stmt->bindColumn('cod_gestion', $nombreGestion);
$stmt->bindColumn('moneda', $nombreMoneda);
$stmt->bindColumn('cod_moneda', $codigoMoneda);
$stmt->bindColumn('tipo_comprobante', $nombreTipoComprobante);
$stmt->bindColumn('fecha', $fechaComprobante);
$stmt->bindColumn('numero', $nroCorrelativo);
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('glosa', $glosaComprobante);*/
$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF2.css" rel="stylesheet" />'.
           '</head>';
$html.='<body>';
$html.=  '<header class="header">'.            
            '<img class="imagen-logo-izq" src="../assets/img/ibnorca2.jpg">'.
            '<div id="header_titulo_texto">LIBRO DIARIO</div>'.
         '<div id="header_titulo_texto_inf">IBNORCA</div>'.
         '<table class="table">'.
            '<tr class="bold table-title">'.
              '<td  class="td-border-none" width="10%">'.$unidad.'</td>'.
              '<td  class="td-border-none" width="90%">'.$nombreUnidad.'</td>'.
            '</tr>'.
         '</table>'.
         '</header>';

$html.='<table class="table">'.
            '<thead>'.
            '<tr class="bold table-title text-center">'.
              '<td colspan="2" class="td-border-none"></td>'.
              '<td colspan="2" class="td-border-none">Bolivianos</td>'.
              '<td colspan="2" class="td-border-none">Dólares</td>'.
            '</tr>'.
            '<tr class="bold table-title text-center">'.
              '<td>Cuenta</td>'.
              '<td>Nombre de la cuenta / Descripción</td>'.
              '<td>Debe</td>'.
              '<td>Haber</td>'.
              '<td>Debe</td>'.
              '<td>Haber</td>'.
            '</tr>'.
            '<tr class="bold table-title text-center">'.
              '<td class="td-border-none"colspan="2">Transporte</td>'.
              '<td class="td-border-none"></td>'.
              '<td class="td-border-none"></td>'.
              '<td class="td-border-none"></td>'.
              '<td class="td-border-none"></td>'.
            '</tr>'.
           '</thead>'.
           '<tbody>'; 
           $index=1;        
while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $fechaC=$rowComp['fecha'];
    $glosaC=$rowComp['glosa'];
    $unidadC=$rowComp['unidad'];
    $codUC=$rowComp['cod_unidad'];
    $monedaC=$rowComp['moneda'];
    $codMC=$rowComp['cod_moneda'];
    $tipoC=$rowComp['tipo_comprobante'];
    $numeroC=$rowComp['numero'];
    $codigo=$rowComp['codigo'];
    //INICIAR valores de las sumas
    $tDebeDol=0;$tHaberDol=0;$tDebeBol=0;$tHaberBol=0;
   // Llamamos a la funcion para obtener el reporte de comprobantes
    $data = obtenerComprobantesDet($codigo);
    $fechaActual=date("Y-m-d");
    
    $html.='<tr>'.
            '<td class="td-border-tb td-border-l">N. correlativo:<b>'.$index.'</b></td>'.
            '<td class="td-border-tb">Fecha:<b>'.strftime('%d/%m/%Y',strtotime($fechaC)).'</b></td>'.
            '<td class="td-border-tb" colspan="3">t/c:<b>'.$monedaC.'</b></td>'.
            '<td class="td-border-tb td-border-r">Numero:<b>'.generarNumeroCeros(6,$numeroC).'</b></td>'.
            '</tr>';
    $html.='<tr>'.
            '<td colspan="6">'.$glosaC.'</td>'.
            '</tr>';
    $html.='<tr>'.
            '<td class="td-border-tb td-border-l text-medium"><b>Ppto:</b></td>'.
            '<td colspan="5" class="td-border-tb td-border-r text-center">Comprometido Devengado Pag/Ing</td>'.
            '</tr>';    
            while ($row = $data->fetch(PDO::FETCH_ASSOC)) {
             $html.='<tr>'.
                      '<td><b>'.$row['numero'].'<br>'.$row['unidadAbrev'].'<br>'.$row['abreviatura'].'</b></td>'.
                      '<td><b>'.$row['nombre'].'<br>'.$row['glosa'].'</b></td>';
                 switch ($codMC) {
                     case 1:
                       //BOLIVIANOS
                      $tDebeBol+=$row['debe'];$tHaberBol+=$row['haber'];
                      $tDebeDol+=$row['debe']/6.96;$tHaberDol+=$row['haber']/6.96;
                       $html.='<td class="text-right">'.number_format($row['debe'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['haber'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['debe']/6.96, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['haber']/6.96, 2, '.', ',').'</td>';
                       break;
                       case 2:
                       //Dolares
                       $tDebeDol+=$row['debe'];$tHaberDol+=$row['haber'];
                       $tDebeBol+=$row['debe']*6.96;$tHaberBol+=$row['haber']*6.96;
                       $html.='<td class="text-right">'.number_format($row['debe']*6.96, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['haber']*6.96, 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['debe'], 2, '.', ',').'</td>'.
                      '<td class="text-right">'.number_format($row['haber'], 2, '.', ',').'</td>';
                       break;
                     default:
                       # code...
                       break;
                   }         
                      
                    $html.='</tr>';
              }
      $entero=floor($tDebeBol);
      $decimal=$tDebeBol-$entero;
      $centavos=floor($decimal*100);
      if($centavos<10){
        $centavos="0".$decimal;
      }
      $html.='<tr class="bold table-title">'.
                  '<td colspan="2" class="text-center">Sumas:</td>'.
                  '<td class="text-right">'.number_format($tDebeBol, 2, '.', ',').'</td>'.
                  '<td class="text-right">'.number_format($tHaberBol, 2, '.', ',').'</td>'. 
                  '<td class="text-right">'.number_format($tDebeDol, 2, '.', ',').'</td>'. 
                  '<td class="text-right">'.number_format($tHaberDol, 2, '.', ',').'</td>'.       
              '</tr>';
    
$index++;          
}/* Fin del primer while*/
$html.=    '</tbody></table>';
/*$html.='<p class="bold table-title">Son: '.ucfirst(CifrasEnLetras::convertirNumeroEnLetras($entero)).'      '.$centavos.'/100 Bolivianos</p>';*/
$html.='</body>'.
      '</html>';
                    
descargarPDF("IBNORCA - ".$unidadC." (".$tipoC.", ".$numeroC.")",$html);
?>
