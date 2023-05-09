<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';

$dbh = new Conexion();

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



if (isset($_POST["check_rs_librocompras"])) {
  $check_rs_librocompras=$_POST["check_rs_librocompras"]; 
  if($check_rs_librocompras){
    $razon_social=$_POST["razon_social"]; 
    $razon_social=trim($razon_social);
    $sql_rs=" and f.razon_social like '%$razon_social%'";
  }else{
    $sql_rs="";
  }
}else{
  $sql_rs="";
}

// echo $areaString;
$sql="SELECT f.fecha,DATE_FORMAT(f.fecha,'%d/%m/%Y')as fecha_x,f.nit,f.razon_social,f.nro_factura,f.nro_autorizacion,f.codigo_control,f.importe,f.ice,f.exento,f.tipo_compra,cc.codigo as cod_comprobante
  FROM facturas_compra f, comprobantes_detalle c, comprobantes cc 
  WHERE cc.codigo=c.cod_comprobante and f.cod_comprobantedetalle=c.codigo and cc.cod_estadocomprobante<>2 and cc.cod_unidadorganizacional in ($stringUnidadesX) and cc.fecha BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' $sql_rs ORDER BY f.fecha asc, f.nit, f.nro_factura"; //and MONTH(cc.fecha)=$cod_mes_x and YEAR(cc.fecha)=$nombre_gestion

//echo $sql;

$stmt2 = $dbh->prepare($sql);
// echo $sql;
// Ejecutamos                        
$stmt2->execute();
//resultado
$stmt2->bindColumn('fecha_x', $fecha);
$stmt2->bindColumn('nit', $nit);
$stmt2->bindColumn('cod_comprobante', $codComprobante);
$stmt2->bindColumn('razon_social', $razon_social);
$stmt2->bindColumn('nro_factura', $nro_factura);
$stmt2->bindColumn('nro_autorizacion', $nro_autorizacion);
$stmt2->bindColumn('codigo_control', $codigo_control);
$stmt2->bindColumn('importe', $importe);
$stmt2->bindColumn('ice', $ice);
$stmt2->bindColumn('exento', $exento);          
$stmt2->bindColumn('tipo_compra', $tipo_compra);  

$cant_unidad=sizeof($unidad);

if($cant_unidad>1){
  $cod_unidad_x=5;
}else{  
  
  if($stringUnidadesX==9 || $stringUnidadesX==10 ){
    $cod_unidad_x=$stringUnidadesX;
  }else{    
    $cod_unidad_x=5;
  }
}

//datos de la factura
$stmtPersonal = $dbh->prepare("SELECT * from titulos_oficinas where cod_uo in ($cod_unidad_x)");
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$sucursal=$result['sucursal'];
$direccion=$result['direccion'];
$nit=$result['nit'];
$razon_social=$result['razon_social'];

$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));

?>
 <script> 
          periodo='<?=$periodoTitle;?>';
          
 </script>

<?php 

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
              <td width="25%"><p>Razón Social : '.$razon_social.'<br>Sucursal : '.$sucursal.'<br>Nit : '.$nit.'<br>Dirección : '.$direccion.'
                </p></td>
              <td><center><span style="font-size: 13px"><b>Libro de Compras IVA<br>Periodo: '.$periodoTitle.'<br>Expresado En Bolivianos</b></center></td>
              <td width="25%" class="text-center"><img style="width:70px;height:70px;" src="../assets/img/ibnorca2.jpg"></td>
              </tr>
            </table>'.
         '</header>';


                       $html.=' <table class="table table-bordered table-condensed" style="width:100%">
                            <thead>
                              <tr >
                                  <th width="2%" style="border:2px solid;"><small><small><b>-</b></small></small></th>   
                                  <th style="border:2px solid;" width="4%"><small><small><small><b>Fecha</b></small></small></small></th>                                
                                  <th style="border:2px solid;" width="4%"><small><small><small><b>NIT</b></small></small></small></th>
                                  <th style="border:2px solid;" width="25%"><small><small><small><b>Razón Social </b></small></small></small></th>
                                  <th style="border:2px solid;" width="4%"><small><small><small><b>Nro. Factura</b></small></small></small></th>
                                  <th style="border:2px solid;" width="18%"><small><small><small><b>Nro de Autorización</b></small></small></small></th>
                                  <th style="border:2px solid;" width="10%"><small><small><small><b>Código de Control</b></small></small></small></th>                                 
                                  <th style="border:2px solid;" width="5%"><small><small><small><b>Total Factura (A)</b></small></small></small></th>
                                  <th style="border:2px solid;" width="3%"><small><small><small><b>Total I.C.E (B)</b></small></small></small></th>
                                  <th style="border:2px solid;" ><small><small><small><small><b>Importes<br> Exentos (C)</b></small></small></small></small></th>
                                  <th style="border:2px solid;"><small><small><small><small><b>Imp Neto<br>Suj a IVA<br>(A-B-C)</b></small></small></small></small></small></th>
                                  <th style="border:2px solid;" ><small><small><small><small><b>Crédito Fiscal Obtenido</b></small></small></small></small></th>
                              </tr>                                  
                            </thead>
                            <tbody>';
                              
                              $index=0; 
                              $total_importe=0;
                              $total_ice=0;
                              $total_exento=0;
                              $total_importe_sujeto_iva=0;
                              $total_iva_obtenido=0;
                              while ($row = $stmt2->fetch()) { 
                                $index++;
                                // $importe_sujeto_iva=$importe-$ice-$exento;
                                $importe_sujeto_iva=$importe-$ice-$exento;;
                                $iva_obtenido=$importe_sujeto_iva*13/100;
                                
                                //ESTE ES EL CASO PARA LAS FACTURAS SIN GRAVAMEN TIPO 2
                                if($tipo_compra==2){
                                  $exento=$importe_sujeto_iva;
                                  $importe_sujeto_iva=0;
                                  $iva_obtenido=0;
                                }

                                $caracter=substr($codigo_control, -1);
                                if($caracter=='-'){
                                  $codigo_control=trim($codigo_control, '-');
                                }
                                if($codigo_control==null || $codigo_control=="")
                                  $codigo_control=0;



                                $total_importe+=$importe;
                                $total_ice+=$ice;
                                $total_exento+=$exento;
                                $total_importe_sujeto_iva+=$importe_sujeto_iva;
                                $total_iva_obtenido+=$iva_obtenido;

                                // $sumadeimporte=$importe+$ice+$exento;
                                $sumadeimporte=$importe;

                                //si es mayor a 20 caracteres, se partira
                                $nro_autorizacion_1="";
                                $nro_autorizacion_2="";
                                if (strlen($nro_autorizacion)>28) {
                                  for ($i=0; $i <28 ; $i++) { 
                                        $nro_autorizacion_1.=$nro_autorizacion[$i];
                                  }
                                  for ($i=28; $i <strlen($nro_autorizacion) ; $i++) { 
                                        $nro_autorizacion_2.=$nro_autorizacion[$i];
                                  }
                                }else{
                                  $nro_autorizacion_1=$nro_autorizacion;
                                }
                              
                                $html.='<tr>
                                  <td class="text-center small"><small>'.$index.'</small></td>
                                  <td class="text-center small"><small>'.$fecha.'</small></td>
                                  <td class="text-right small"><small>'.$nit.'</small></td>
                                  <td class="text-left small"><small>'.$razon_social.'</small></td>
                                  <td class="text-right small"><small>'.$nro_factura.'</small></td>
                                  <td class="text-right small"><small>'.$nro_autorizacion_1.'<br>'.$nro_autorizacion_2.'</small></td>
                                  <td class="text-center small"><small>'.$codigo_control.'</small></td>
                                  <td class="text-right small"><small>'.formatNumberDec($sumadeimporte).'</small></td>
                                  <td class="text-right small"><small>'.formatNumberDec($ice).'</small></td>
                                  <td class="text-right small"><small>'.formatNumberDec($exento).'</small></td>
                                  <td class="text-right small"><small>'.formatNumberDec($importe_sujeto_iva).'</small></td>
                                  <td class="text-right small"><small>'.formatNumberDec($iva_obtenido).'</small></td>                                      
                                </tr>';
                                                                
                              }
                              $html.='<tr style="border:2px solid;">                               
                                  <td class="text-left small csp" colspan="3" style="border:2px solid;"><small>CI:</small></td>
                                  <td class="text-left small csp" colspan="3" style="border:2px solid;"><small>Nombre del Responsable:</small></td>
                                  <td class="text-center small"><small><b>SubTotal:</b></small></td>                                  
                                  <td class="text-right small"><small>'.formatNumberDec($total_importe).'</small></td>
                                  <td class="text-right small"><small>'.formatNumberDec($total_ice).'</small></td>
                                  <td class="text-right small"><small>'.formatNumberDec($total_exento).'</small></td>
                                  <td class="text-right small"><small>'.formatNumberDec($total_importe_sujeto_iva).'</small></td>
                                  <td class="text-right small"><small>'.formatNumberDec($total_iva_obtenido).'</small></td>                                      
                                </tr>
                            </tbody>
                        </table>';

//                    echo $html;
descargarPDFHorizontal_carta("Libro Compra",$html);