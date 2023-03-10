<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';

$dbh = new Conexion();

$bd_siat=obtenerValorConfiguracion(106);

//RECIBIMOS LAS VARIABLES
$gestion = $_POST["gestiones"];
//$cod_mes_x = $_POST["cod_mes_x"];
$unidad=$_POST["unidad"];
$stringUnidadesX=implode(",", $unidad);
$nombre_gestion=nameGestion($gestion);
//$nombre_mes=nombreMes($cod_mes_x);
$desdeInicioAnio="";
if($_POST["fecha_desde"]==""){
  $y=$globalNombreGestion;
  $desde=$y."-01-01";
  $hasta=$y."-12-31";
  $desdeInicioAnio=$y."-01-01";
}else{
  $porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
  $porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);

  $desdeInicioAnio=$porcionesFechaDesde[0]."-01-01";
  $desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
  $hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
}
//para la razon social
if (isset($_POST["check_rs_librocompras"])) {
  $check_rs_librocompras=$_POST["check_rs_librocompras"]; 
  if($check_rs_librocompras){
    $razon_social=$_POST["razon_social"]; 
    $sql_rs=" and f.razon_social like '%$razon_social%'";
  }else{
    $sql_rs="";
  }
}else{
  $sql_rs="";
}

$sql="SELECT *,DATE_FORMAT(f.fecha_factura,'%d/%m/%Y')as fecha_factura_x,
(select s.siat_cuf from ".$bd_siat.".salida_almacenes s where s.cod_salida_almacenes=f.idTransaccion_siat)as cuf 
from facturas_venta f where f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_unidadorganizacional in ($stringUnidadesX) $sql_rs ORDER BY DATE_FORMAT(f.fecha_factura,'%d/%m/%Y'), f.nro_factura asc";

//echo $sql;

$stmt2 = $dbh->prepare($sql);
$stmt2->execute();
//resultado
$stmt2->bindColumn('codigo', $codigo);
$stmt2->bindColumn('cod_sucursal', $cod_sucursal);
$stmt2->bindColumn('cod_solicitudfacturacion', $cod_solicitudfacturacion);
$stmt2->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmt2->bindColumn('fecha_factura_x', $fecha_factura);
$stmt2->bindColumn('fecha_limite_emision', $fecha_limite_emision);
$stmt2->bindColumn('cod_tipoobjeto', $cod_tipoobjeto);
$stmt2->bindColumn('cod_tipopago', $cod_tipopago);
$stmt2->bindColumn('cod_cliente', $cod_cliente);
$stmt2->bindColumn('cod_personal', $cod_personal);
$stmt2->bindColumn('razon_social', $razon_social);
$stmt2->bindColumn('nit', $nit);
$stmt2->bindColumn('cod_dosificacionfactura', $cod_dosificacionfactura);
$stmt2->bindColumn('nro_factura', $nro_factura);
$stmt2->bindColumn('nro_autorizacion', $nro_autorizacion);
$stmt2->bindColumn('codigo_control', $codigo_control);
$stmt2->bindColumn('observaciones', $observaciones);
$stmt2->bindColumn('cod_estadofactura', $cod_estadofactura);
$stmt2->bindColumn('cod_comprobante', $cod_comprobante);
$stmt2->bindColumn('cuf', $cufSiat);

//datos de la factura
$stmtPersonal = $dbh->prepare("SELECT * from titulos_oficinas where cod_uo in (5)");
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$sucursal=$result['sucursal'];
$direccion=$result['direccion'];
$nit=$result['nit'];
$razon_social_titulo=$result['razon_social'];
$nombre_mes="";
$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));
$html = '';
$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../ assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDF_ba.css" rel="stylesheet" />'.
           '</head>';
$html.='<body>'.
        '<script type="text/php">'.
      'if ( isset($pdf) ) {'. 
        '$font = Font_Metrics::get_font("helvetica", "normal");'.
        '$size = 9;'.
        '$y = $pdf->get_height() - 24;'.
        '$x = $pdf->get_width() - 15 - Font_Metrics::get_text_width("1/1", $font, $size);'.
        '$pdf->page_text($x, $y, "{PAGE_NUM}/{PAGE_COUNT}", $font, $size);'.
      '}'.
    '</script>';
$html.=  '<header class="header">'.
          '<table width="100%">
              <tr>
              <td width="25%"><p>Razón Social : '.$razon_social_titulo.'<br>Sucursal : '.$sucursal.'<br>Nit : '.$nit.'<br>Dirección : '.$direccion.'
                </p></td>
              <td><center><span style="font-size: 13px"><b>Libro de Ventas<br>Periodo: '.$periodoTitle.'<br>Expresado En Bolivianos</b></center></td>
              <td width="25%" class="text-center"><img style="width:70px;height:70px;" src="../assets/img/ibnorca2.jpg"></td>
              </tr>
            </table>'.
         '</header>';



               
                    $html.=  '<table id="libro_ventas_rep" class="table table-bordered table-condensed" style="width:100%">
                        <thead>                         
                          <tr>
                            <td style="border:2px solid;"><small><small><b>Esp.</b></small></small></td>
                            <td style="border:2px solid;"><small><small><b>-</b></small></small></td>                            
                            <td style="border:2px solid;" width="6%"><small><small><b>Fecha</b></small></small></td>
                            <td style="border:2px solid;" width="3%"><small><small><b>Nro. <br>Factura</b></small></small></td>
                            <td style="border:2px solid;" ><small><small><b>Est <br>ado</b></small></small></td>
                            <td style="border:2px solid;"><small><small><b>Nit/CI<br>Cliente</b></small></small></td>
                            <td style="border:2px solid;" width="20%"><small><small><b>Nombre o<br>Razón Social</b></small></small></td>
                            <td style="border:2px solid;" width="8%"><small><small><b>Código Control</b></small></small></td>
                            <td style="border:2px solid;"><small><small><b>Nro. Autorización</b></small></small></td>
                            <td style="border:2px solid;" width="6%"><small><small><b>Importe Total<br> Venta (A)</b></small></small></td>
                            <td style="border:2px solid;" width="6%"><small><small><b>Imp otros <br>no sujetos a iva (B)</b></small></small></td>
                            <td style="border:2px solid;" width="3%"><small><small><b>Export.<br> y Operac. Extentas (C)</b></small></small></td>
                            <td style="border:2px solid;" width="3%"><small><small><b>Ventas Gravadas<br> a tasa Cero (D)</b></small></small></td>
                            <td style="border:2px solid;"><small><small><b>Subtotal <br>E=A-B-C-D</b></small></small></td>
                            <td style="border:2px solid;" width="3%"><small><small><b>Desc., Bonif. y<br> Rebajas sujetos al IVA <br>(F)</b></small></small></td>
                            <td style="border:2px solid;" width="5%"><small><small><b>Importe Débito <br>Fiscal (G=E-F)</b></small></small></td>
                            <td style="border:2px solid;" width="5%"><small><small><b>Débito Fiscal<br> (H=G*13%)</b></small></small></td>
                          </tr>                                
                        </thead>
                        <tbody>';
                                                  
                          $index=1;
                          $total_importe=0;
                          $total_importe_no_iva=0;
                          $total_extento=0;
                          $total_ventas_gravadas=0;
                          $total_subtotal=0;
                          $total_rebajas_sujetos_iva=0;
                          $total_importe_debito_fiscal=0;
                          $total_debito_fiscal=0;
                          while ($row = $stmt2->fetch()) {   
                            $importe=sumatotaldetallefactura($codigo);
                            switch ($cod_estadofactura) {
                              case 1:
                                $btnEstado='<span class="badge badge-success">';
                              break;
                              case 2:
                                $btnEstado='<span class="badge badge-danger">';
                                $razon_social="ANULADO";
                                $importe=0;
                                $codigo_control=0;
                                $nit=0;
                               // $fecha_factura=0;
                              break;
                              case 3:
                                $btnEstado='<span class="badge badge-success">';
                                $cod_estadofactura=1;
                              break;
                              case 4:
                                $btnEstado='<span class="badge badge-default">';
                                $cod_estadofactura=1;
                            }
                            $nombre_estado=nameEstadoFactura($cod_estadofactura);


                            $importe_no_iva=0;
                            $extento=0;
                            $ventas_gravadas=0;
                            $rebajas_sujetos_iva=0;
                            $subtotal=$importe-$importe_no_iva-$extento-$ventas_gravadas;
                            $importe_debito_fiscal=$subtotal-$rebajas_sujetos_iva;
                            $debito_fiscal=13*$importe_debito_fiscal/100;

                            /*SIAT AUTORIZACION CUF*/
                            if($nro_autorizacion==1 && $cufSiat<>""){
                              $nro_autorizacion=$cufSiat;
                              $nro_autorizacion=substr($cufSiat, 0, 20)."<br>".substr($cufSiat, 20, 20)."<br>".substr($cufSiat, 40, 20);
                            }

                            $total_importe+=$importe;
                            $total_importe_no_iva+=$importe_no_iva;
                            $total_extento+=$extento;
                            $total_ventas_gravadas+=$ventas_gravadas;
                            $total_subtotal+=$subtotal;
                            $total_rebajas_sujetos_iva+=$rebajas_sujetos_iva;
                            $total_importe_debito_fiscal+=$importe_debito_fiscal;
                            $total_debito_fiscal+=$debito_fiscal;


                            
                              // <!-- el ultimo no sale -->
                            $html.=  '<tr>                                
                                <td class="text-center small"><small>3</small></td>
                                <td class="text-center small"><small>'.$index.'</small></td>
                                <td class="text-center small"><small>'.$fecha_factura.'</small></td>
                                <td class="text-right small"><small>'.$nro_factura.'</small></td>
                                <td class="text-center small"><small>'.$nombre_estado.'</small></td>
                                <td class="text-right small"><small>'.$nit.'</small></td>                                
                                <td class="text-left small"><small><small>'.mb_strtoupper($razon_social,"utf-8").'</small></small></td>
                                <td class="text-center small"><small>'.$codigo_control.'</small></td>
                                <td class="text-left small"><small>'.$nro_autorizacion.'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($importe).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($importe_no_iva).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($extento).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($ventas_gravadas).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($subtotal).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($rebajas_sujetos_iva).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($importe_debito_fiscal).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($debito_fiscal).'</small></td>
                                
                            </tr>';
                           $index++; } 

                           $html.=  ' <tr style="border:2px solid;">
                                
                                <td class="text-left small csp" colspan="5" style="border:2px solid;"><small>CI:</small></td>
                                <td class="text-left small csp" colspan="3" style="border:2px solid;"><small>Nombre Responsable:</small></td>
                                <td class="text-center small"><small>TOTAL</small></td>                                
                                <td class="text-right small"><small>'.formatNumberDec($total_importe).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($total_importe_no_iva).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($total_extento).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($total_ventas_gravadas).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($total_subtotal).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($total_rebajas_sujetos_iva).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($total_importe_debito_fiscal).'</small></td>
                                <td class="text-right small"><small>'.formatNumberDec($total_debito_fiscal).'</small></td>
                            </tr>
                        </tbody>
                    </table>';

   descargarPDFHorizontal_carta("Libro Ventas",$html);