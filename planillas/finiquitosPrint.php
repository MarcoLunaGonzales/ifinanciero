<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES

$codigo = $_GET["codigo"];//codigoactivofijo
try{

    //====================================
    //
    $stmt = $dbh->prepare("SELECT f.*,p.primer_nombre,p.paterno,p.materno FROM finiquitos f,personal p where f.cod_personal=p.codigo and f.codigo =:codigo");
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();

$html = '';
$html.='<html>'.
            '<head>'.
                '<!-- CSS Files -->'.
                '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
                '<link href="../assets/libraries/plantillaPDF.css" rel="stylesheet" />'.
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
            '<img class="imagen-logo-der" src="../assets/img/ibnorca2.jpg">'.
            
            '<div id="header_titulo_texto_inf_2">Instituto Boliviano de Normalización y Calidad</div>'.
            '<div id="header_titulo_texto_inf_2">NIT 1020745020</div>'.
            '<br>'.'<br>'.
            '<div id="header_titulo_texto">LIQUIDACIÓN DE BENEFICIOS SOCIALES</div>'.
            '<br>'.'<br>'.
            '<table border="1" align="center" style="width: 80%;border-collapse: collapse;">'.
                '<tbody style=" font-family: Times New Roman;
                                font-size: 12px;
                                    ">';

                    $html.='<tr>'.
                        '<td><b>Nombre : </b></td>'.
                        '<td align="center" colspan=3><b>'.$result['primer_nombre'].' '.$result['paterno'].' '.$result['materno'].'</b></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td><b>Motivo retiro</b></td>'.
                        '<td align="center"colspan=3><b>'.$result['motivo_retiro'].'</b></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td><b>Fecha Ingreso</b></td>'.
                        '<td>'.$result['fecha_ingreso'].'</td>'.
                        '<td></td>'.
                        '<td></td>'.
                    '</tr>'.    
                    '<tr>'.
                        '<td><b>Fecha Retiro</b></td>'.
                        '<td>'.$result['fecha_retiro'].'</td>'.
                        '<td></td>'.
                        '<td></td>'.
                    '</tr>'.                    
                    '<tr>'.
                        '<td colspan="4"><br></td>           '.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan=2><b>Sueldo Promedio</b></td>'.
                        '<td align="center"><b>'.$result['sueldo_promedio'].'</b></td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>3 meses atrás</td>'.
                        '<td>'.$result['sueldo_3_atras'].'</td>'.
                        '<td></td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>2 meses atrás</td>'.
                        '<td>'.$result['sueldo_2_atras'].'</td>'.
                        '<td></td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>1 mes atrás</td>'.
                        '<td>'.$result['sueldo_1_atras'].'</td>'.
                        '<td></td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="4"><br></td>           '.
                    '</tr>'.
                    '<tr>'.
                        '<td><b>Desahucio tres meses</b></td>'.
                        '<td></td>'.
                        '<td></td>'.
                        '<td><b>'.$result['desahucio_3_meses'].'</b></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="4"><br></td>           '.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan=3><b>Indemnización</b></td>'.
                        '<td><b>'.($result['indemnización_anios_monto']+$result['indemnización_meses_monto']+$result['indemnización_dias_monto']).'</b></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Años</td>'.
                        '<td></td>'.
                        '<td>'.$result['indemnización_anios_monto'].'</td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Meses</td>'.
                        '<td></td>'.
                        '<td>'.$result['indemnización_meses_monto'].'</td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Días</td>'.
                        '<td></td>'.
                        '<td>'.$result['indemnización_dias_monto'].'</td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="4"><br></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan=3><b>Aguinaldo</b></td>'.
                        '<td><b>'.($result['aguinaldo_anios_monto']+$result['aguinaldo_meses_monto']+$result['aguinaldo_dias_monto']).'</b></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Años</td>'.
                        '<td></td>'.
                        '<td>'.$result['aguinaldo_anios_monto'].'</td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Meses</td>'.
                        '<td></td>'.
                        '<td>'.$result['aguinaldo_meses_monto'].'</td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Días</td>'.
                        '<td></td>'.
                        '<td>'.$result['aguinaldo_dias_monto'].'</td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="4"><br></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan=3><b>Vacaciones</b></td>'.
                        '<td><b>'.($result['vacaciones_dias_monto']+$result['vacaciones_duodecimas_monto']).'</b></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Días</td>'.
                        '<td></td>'.
                        '<td>'.$result['vacaciones_dias_monto'].'</td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Duodecimas</td>'.
                        '<td></td>'.
                        '<td>'.$result['vacaciones_duodecimas_monto'].'</td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="4"><br></td>           '.
                    '</tr>'.
                    '<tr>'.
                        '<td><b>Desahucio</b></td>'.
                        '<td></td>'.
                        '<td></td>'.
                        '<td><b>'.$result['desahucio_monto'].'</b></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="4"><br></td>           '.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan=3><b>Otros</b></td>'.
                        '<td><b>'.($result['servicios_adicionales']+$result['subsidios_meses']+$result['finiquitos_a_cuenta']).'</b></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Pago por otros servicios adicionales</td>'.
                        '<td></td>'.
                        '<td>'.$result['servicios_adicionales'].'</td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Subsidios (meses)</td>'.
                        '<td></td>'.
                        '<td>'.$result['subsidios_meses'].'</td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Finiquitos a cuenta</td>'.
                        '<td></td>'.
                        '<td>'.$result['finiquitos_a_cuenta'].'</td>'.
                        '<td></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="4"><br></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td><b>DEDUCCIONES</b></td>'.
                        '<td></td>'.
                        '<td></td>'.
                        '<td><b>'.$result['deducciones_total'].'</b></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="4"><br></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td><b>TOTAL A PAGAR</b></td>'.
                        '<td></td>'.
                        '<td></td>'.
                        '<td><b>'.$result['total_a_pagar'].'</b></td>'.
                    '</tr>'.
                    '<tr>'.                        
                        '<td colspan=4><b>'.$result['observaciones'].'</b></td>'.
                    '</tr>'.
                '</tbody>'.            
            '</table>'.
            '</header>'.
        '</body>'.
      '</html>';           
descargarPDF("IBNORCA - ".$unidadC." (".$tipoC.", ".$numeroC.")",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
