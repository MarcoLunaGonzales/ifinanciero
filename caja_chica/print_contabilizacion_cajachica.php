<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES
set_time_limit(3000);
$cod_cajachica = $_GET["cod_cajachica"];//codigoactivofijo
try{
    $stmtCajaChicaDet = $dbh->prepare("SELECT codigo,cod_tipodoccajachica,observaciones,monto,(select c.nombre from plan_cuentas c where c.codigo=cod_cuenta)nombre_cuenta,
(select c2.numero from plan_cuentas c2 where c2.codigo=cod_cuenta)numero_cuenta,
(select CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) from personal p where p.codigo=cod_personal)as personal,
(select u.abreviatura from unidades_organizacionales u where u.codigo=cod_uo)as nombre_uo,
(select a.abreviatura from areas a where a.codigo=cod_area)as nombre_area
from caja_chicadetalle where cod_estadoreferencial=1 and cod_cajachica=$cod_cajachica ORDER BY 1");
    $stmtCajaChicaDet->execute();
    $stmtCajaChicaDet->bindColumn('codigo', $codigo_ccdetalle);
    $stmtCajaChicaDet->bindColumn('nombre_cuenta', $nombre_cuenta);
    $stmtCajaChicaDet->bindColumn('numero_cuenta', $numero_cuenta);    
    $stmtCajaChicaDet->bindColumn('personal', $personal);
    $stmtCajaChicaDet->bindColumn('nombre_uo', $nombre_uo);
    $stmtCajaChicaDet->bindColumn('nombre_area', $nombre_area);
    $stmtCajaChicaDet->bindColumn('cod_tipodoccajachica', $cod_tipodoccajachica);
    $stmtCajaChicaDet->bindColumn('observaciones', $observaciones_dcc);
    $stmtCajaChicaDet->bindColumn('monto', $monto_dcc);
    
    //====================================
    //personal discapacitado
    $stmtCajaChica = $dbh->prepare("SELECT *,(select CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) from personal p where p.codigo=cod_personal) as name_personal,
        (select tc.nombre from tipos_caja_chica tc where tc.codigo=cod_tipocajachica) as name_tipocc
        FROM caja_chica where codigo =$cod_cajachica");
    $stmtCajaChica->execute();
    $resultCCD = $stmtCajaChica->fetch();
    $cod_tipocajachica = $resultCCD['cod_tipocajachica'];
    $fechaCCD = $resultCCD['fecha'];
    $numeroCC = $resultCCD['numero'];
    $monto_inicio = $resultCCD['monto_inicio'];
    $monto_reembolso = $resultCCD['monto_reembolso'];
    $observacionesCC = $resultCCD['observaciones'];
    $cod_personalCCD = $resultCCD['cod_personal'];
    $name_personalCC = $resultCCD['name_personal'];
    $name_tipoccCC = $resultCCD['name_tipocc'];

    $porcentajeIVA=obtenerValorConfiguracion(1);
    $cod_cuenta_iva=obtenerValorConfiguracion(3);
    $nro_cuenta_iva=obtieneNumeroCuenta($cod_cuenta_iva);
    $nombre_cuenta_iva=nameCuenta($cod_cuenta_iva);
    
    $porcentajeIUE_servicios=obtenerValorConfiguracion(23);
    $cod_cuenta_IUE_S=obtenerValorConfiguracion(27);
    $nro_cuenta_IUE_S=obtieneNumeroCuenta($cod_cuenta_IUE_S);
    $nombre_cuenta_IUE_s=nameCuenta($cod_cuenta_IUE_S);

    $porcentajeIUE_compras=obtenerValorConfiguracion(24);
    $cod_cuenta_IUE_C=obtenerValorConfiguracion(26);
    $nro_cuenta_IUE_C=obtieneNumeroCuenta($cod_cuenta_IUE_C);
    $nombre_cuenta_IUE_C=nameCuenta($cod_cuenta_IUE_C);
    
    $porcentajeIT=obtenerValorConfiguracion(2);
    $cod_cuenta_IT=obtenerValorConfiguracion(25);
    $nro_cuenta_IT=obtieneNumeroCuenta($cod_cuenta_IT);
    $nombre_cuenta_IT=nameCuenta($cod_cuenta_IT);
    //CONTRA CUENTA
    $cod_contra_cuenta=obtenerValorConfiguracion(28);
    $nro_contra_cuenta=obtieneNumeroCuenta($cod_contra_cuenta);
    $nombre_contra_cuenta=nameCuenta($cod_contra_cuenta);
    $monto_contra_cuenta=$monto_inicio-$monto_reembolso;

    $IUE_compras_IT=$porcentajeIUE_compras+$porcentajeIT;
    $IUE_servicios_IT=$porcentajeIUE_servicios+$porcentajeIT;
    $fecha_actual=date('Y/m/d');
    
    $USD_actual=obtenerValorTipoCambio(2,$fecha_actual);
    if($USD_actual=='')$USD_actual=0;
    // $concepto_contabilizacion=strtoupper($name_personalCC)." rendición de ".$observacionesCC." ".$name_tipoccCC."( - - - - )";
    $concepto_contabilizacion="CONTABILIZACIÓN CAJA CHICA N° ".$numeroCC." DE FECHA ( - - - - )";

$html = '';
$html.='<html>'.
            '<head>'.
                '<!-- CSS Files -->'.
                '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
                '<link href="../assets/libraries/plantillaPDFCajaChica.css" rel="stylesheet" />'.
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
            '<img class="imagen-logo-izq" src="../assets/img/ibnorca2.jpg">'.
            '<div id="header_titulo_texto">Comprobante de Contabilidad</div>'.
            '<div id="info_izq2">
              <h4 >
              <b>Entidad:'.'10'.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. obtenerValorConfiguracionEmpresa(7).' </b><br>
              <b>Fondo:  1011'. ' &nbsp;&nbsp;&nbsp;'. 'OFICINA '.' </b><br>
              </h4> 
            </div>'.            
            '<table class="table">'.
                '<tbody>
                    <tr>
                        <td>Fecha: '.$fecha_actual.'</td>
                        <td>t/c: USD: '.$USD_actual.'</td>
                        <td>Traspaso Ene Número: '.$numeroCC.'</td>
                    </tr>
                    <tr>
                        <td colspan="3">CONCEPTO: '.$concepto_contabilizacion.'</td>
                    </tr>
                </tbody>'.
            '</table>'.            
            '</header>'.
            
            '<table class="table">'.
                '<thead>
                    <tr>
                        <th colspan="2"></th>                        
                        <th colspan="2">Bolivianos</th>
                        <th colspan="2">Dólares</th>                        
                    </tr>
                    <tr>
                        <td width="15%"><b>Cuenta: </b></td>
                        <td width="45%"><b>Nombre de la cuenta/Descripción </b></td>
                        <td width="10%"><b>Debe</b></td>
                        <td width="10%"><b>Haber</b></td>
                        <td width="10%"><b>Debe</b></td>
                        <td width="10%"><b>Haber</b></td>
                    </tr>
                </thead>'.
                '<tbody>';
                    $sumaTotalDebeBolivianos=0;
                    $sumaTotalHaberBolivianos=0;
                    $sumaTotalDebeDolares=0;
                    $sumaTotalHaberDolares=0;
                    while ($rowCajaChicaDet = $stmtCajaChicaDet->fetch()) 
                    {
                        if($cod_tipodoccajachica==6){//6 compra con factura
                            $sw_facturas=0;
                            $stmtFacturas = $dbh->prepare("SELECT nro_factura,razon_social,importe from facturas_detalle_cajachica where cod_cajachicadetalle=$codigo_ccdetalle");
                            $stmtFacturas->execute();
                            $stmtFacturas->bindColumn('nro_factura', $nro_factura);
                            $stmtFacturas->bindColumn('razon_social', $razon_social);
                            $stmtFacturas->bindColumn('importe', $importe);                                
                            while ($rowFac = $stmtFacturas->fetch()) 
                            {
                                $descripcionIVA=$nombre_uo.' F/'.$nro_factura.' '.$personal.', '.$razon_social;
                                $montoIVA=$importe*$porcentajeIVA/100;
                                if($USD_actual!=0)
                                    $montoIVA_dolares=$montoIVA/$USD_actual;
                                else $montoIVA_dolares=0;
                                $monto_restante=$importe-$montoIVA;
                                $html.='<tr>
                                    <td class="text-left small">'.$nro_cuenta_iva.'</td>
                                    <td class="text-left small"><p>'.$nombre_cuenta_iva.'<br>'.$descripcionIVA.' </p></td>
                                    <td class="text-right small">'.formatNumberDec($montoIVA).'</td>
                                    <td class="text-right small"></td>
                                    <td class="text-right small">'.formatNumberDec($montoIVA_dolares).'</td>
                                    <td class="text-right small"></td>
                                </tr>';
                                $sumaTotalDebeBolivianos+=$montoIVA;
                                $sumaTotalDebeDolares+=$montoIVA_dolares;
                                $stmtOficina = $dbh->prepare("SELECT cod_unidadorganizacional,porcentaje,
                                (select u.abreviatura from unidades_organizacionales u where u.codigo=cod_unidadorganizacional)as oficina
                                from distribucion_gastosporcentaje where porcentaje>0");
                                $stmtOficina->execute();
                                $stmtOficina->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
                                $stmtOficina->bindColumn('porcentaje', $porcentaje);
                                $stmtOficina->bindColumn('oficina', $oficinaFac);
                                while ($rowOf = $stmtOficina->fetch()) 
                                {                                    
                                    $descripcion_of=$oficinaFac.'/'.$nombre_uo.' F/'.$nro_factura.' '.$personal.', '.$razon_social;
                                    $monto_of=$monto_restante*$porcentaje/100;
                                    if($USD_actual!=0)
                                        $monto_of_dolares=$monto_of/$USD_actual;
                                    else $monto_of_dolares=0;
                                    $sumaTotalDebeBolivianos+=$monto_of;
                                    $sumaTotalDebeDolares+=$monto_of_dolares;
                                    $html.='<tr>
                                        <td class="text-left small">'.$numero_cuenta.'</td>
                                        <td class="text-left small"><p>'.$nombre_cuenta.'<br>'.$descripcion_of.' </p></td>
                                        <td class="text-right small">'.formatNumberDec($monto_of).'</td>
                                        <td class="text-right small"></td>
                                        <td class="text-right small">'.formatNumberDec($monto_of_dolares).'</td>
                                        <td class="text-right small"></td>
                                    </tr>';
                                }
                                $sw_facturas++;
                            }
                            if($sw_facturas==0){//compra no tiene factura
                                //recalculando monto
                                $monto_recalculado=$monto_dcc/(1-($IUE_compras_IT/100));
                                //para retencion it 3%
                                $descripcionIT=$nombre_uo.' SF '.$personal.', '.$observaciones_dcc;
                                $monto_it=$monto_recalculado*$porcentajeIT/100;
                                if($USD_actual!=0)
                                    $monto_it_dolares=$monto_it/$USD_actual;
                                else $monto_it_dolares=0;
                                $html.='<tr>
                                    <td class="text-left small">'.$nro_cuenta_IT.'</td>
                                    <td class="text-left small"><p>'.$nombre_cuenta_IT.'<br>'.$descripcionIT.' </p></td>
                                    <td class="text-right small"></td>
                                    <td class="text-right small">'.formatNumberDec($monto_it).'</td>
                                    <td class="text-right small"></td>
                                    <td class="text-right small">'.formatNumberDec($monto_it_dolares).'</td>
                                </tr>';
                                $sumaTotalHaberBolivianos+=$monto_it;
                                $sumaTotalHaberDolares+=$monto_it_dolares;
                                //retencion iue 5%
                                $descripcioniue_c=$nombre_uo.' SF '.$personal.', '.$observaciones_dcc;
                                $monto_iue=$monto_recalculado*$porcentajeIUE_compras/100;
                                if($USD_actual!=0)
                                    $monto_iue_dolares=$monto_iue/$USD_actual;
                                else $monto_iue_dolares=0;
                                 $html.='<tr>
                                    <td class="text-left small">'.$nro_cuenta_IUE_C.'</td>
                                    <td class="text-left small"><p>'.$nombre_cuenta_IUE_C.'<br>'.$descripcioniue_c.' </p></td>
                                    <td class="text-right small"></td>
                                    <td class="text-right small">'.formatNumberDec($monto_iue).'</td>
                                    <td class="text-right small"></td>
                                    <td class="text-right small">'.formatNumberDec($monto_iue_dolares).'</td>
                                </tr>';
                                $sumaTotalHaberBolivianos+=$monto_iue;
                                $sumaTotalHaberDolares+=$monto_iue_dolares;
                                $stmtOficina = $dbh->prepare("SELECT cod_unidadorganizacional,porcentaje,
                                (select u.abreviatura from unidades_organizacionales u where u.codigo=cod_unidadorganizacional)as oficina
                                from distribucion_gastosporcentaje where porcentaje>0");
                                $stmtOficina->execute();
                                $stmtOficina->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
                                $stmtOficina->bindColumn('porcentaje', $porcentaje);
                                $stmtOficina->bindColumn('oficina', $oficinaFac);
                                while ($rowOf = $stmtOficina->fetch()) 
                                {                                    
                                    $descripcion_of=$oficinaFac.'/'.$nombre_uo.' SF '.$personal.', '.$observaciones_dcc;
                                    $monto_of=$monto_recalculado*$porcentaje/100;
                                    if($USD_actual!=0)
                                        $monto_of_dolares=$monto_of/$USD_actual;
                                    else $monto_of_dolares=0;
                                    $sumaTotalDebeBolivianos+=$monto_of;
                                    $sumaTotalDebeDolares+=$monto_of_dolares;
                                    $html.='<tr>
                                        <td class="text-left small">'.$numero_cuenta.'</td>
                                        <td class="text-left small"><p>'.$nombre_cuenta.'<br>'.$descripcion_of.' </p></td>
                                        <td class="text-right small">'.formatNumberDec($monto_of).'</td>
                                        <td class="text-right small"></td>
                                        <td class="text-right small">'.formatNumberDec($monto_of_dolares).'</td>
                                        <td class="text-right small"></td>
                                    </tr>';
                                }                                
                            } 
                        }elseif($cod_tipodoccajachica==5){//5 servicio
                            //recalculando monto
                            $monto_recalculado=$monto_dcc/(1-($IUE_servicios_IT/100));
                            //para retencion it 3%
                            $descripcionIT=$nombre_uo.' SF '.$personal.', '.$observaciones_dcc;

                            //$prueba="monto rec: ".$monto_recalculado." monto:".$monto_dcc." iue_s_it:".$IUE_servicios_IT;

                            $monto_it=$monto_recalculado*$porcentajeIT/100;
                            if($USD_actual!=0)
                                $monto_it_dolares=$monto_it/$USD_actual;
                            else $monto_it_dolares=0;
                            $html.='<tr>
                                <td class="text-left small">'.$nro_cuenta_IT.'</td>
                                <td class="text-left small"><p>'.$nombre_cuenta_IT.'<br>'.$descripcionIT.' </p></td>
                                <td class="text-right small"> </td>
                                <td class="text-right small">'.formatNumberDec($monto_it).'</td>
                                <td class="text-right small"></td>
                                <td class="text-right small">'.formatNumberDec($monto_it_dolares).'</td>
                            </tr>';
                            $sumaTotalHaberBolivianos+=$monto_it;
                            $sumaTotalHaberDolares+=$monto_it_dolares;
                            //retencion iue 12,5%
                            $descripcioniue_c=$nombre_uo.' SF '.$personal.', '.$observaciones_dcc;
                            $monto_iue=$monto_recalculado*$porcentajeIUE_servicios/100;
                            if($USD_actual!=0)
                                $monto_iue_dolares=$monto_iue/$USD_actual;
                            else $monto_iue_dolares=0;
                             $html.='<tr>
                                <td class="text-left small">'.$nro_cuenta_IUE_S.'</td>
                                <td class="text-left small"><p>'.$nombre_cuenta_IUE_s.'<br>'.$descripcioniue_c.' </p></td>
                                <td class="text-right small"></td>
                                <td class="text-right small">'.formatNumberDec($monto_iue).'</td>
                                <td class="text-right small"></td>
                                <td class="text-right small">'.formatNumberDec($monto_iue_dolares).'</td>
                            </tr>';
                            $sumaTotalHaberBolivianos+=$monto_iue;
                            $sumaTotalHaberDolares+=$monto_iue_dolares;
                            $stmtOficina = $dbh->prepare("SELECT cod_unidadorganizacional,porcentaje,
                            (select u.abreviatura from unidades_organizacionales u where u.codigo=cod_unidadorganizacional)as oficina
                            from distribucion_gastosporcentaje where porcentaje>0");
                            $stmtOficina->execute();
                            $stmtOficina->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
                            $stmtOficina->bindColumn('porcentaje', $porcentaje);
                            $stmtOficina->bindColumn('oficina', $oficinaFac);
                            while ($rowOf = $stmtOficina->fetch()) 
                            {                                    
                                $descripcion_of=$oficinaFac.'/'.$nombre_uo.' SF '.$personal.', '.$observaciones_dcc;
                                $monto_of=$monto_recalculado*$porcentaje/100;
                                if($USD_actual!=0)
                                    $monto_of_dolares=$monto_of/$USD_actual;
                                else $monto_of_dolares=0;
                                $sumaTotalDebeDolares+=$monto_of_dolares;
                                $sumaTotalDebeBolivianos+=$monto_of;
                                $html.='<tr>
                                    <td class="text-left small">'.$numero_cuenta.'</td>
                                    <td class="text-left small"><p>'.$nombre_cuenta.'<br>'.$descripcion_of.' </p></td>
                                    <td class="text-right small">'.formatNumberDec($monto_of).'</td>
                                    <td class="text-right small"></td>
                                    <td class="text-right small">'.formatNumberDec($monto_of_dolares).'</td>
                                    <td class="text-right small"></td>
                                </tr>';
                            }

                        } 
                    }
                    // $descripcionIVA=$nombre_uo.' F/'.$nro_factura.' '.$personal.', '.$razon_social;
                    // $montoIVA=$importe*$porcentajeIVA/100;
                    // $monto_restante=$importe-$montoIVA;


                    $descripcion_contra_cuenta=$concepto_contabilizacion;
                    if($USD_actual!=0)
                        $monto_contra_cuenta_dolares=$monto_contra_cuenta/$USD_actual;
                    else $monto_contra_cuenta_dolares=0;
                    $sumaTotalHaberBolivianos+=$monto_contra_cuenta;
                    $sumaTotalHaberDolares+=$monto_contra_cuenta_dolares;
                    $html.='<tr>
                        <td class="text-left small">'.$nro_contra_cuenta.'</td>
                        <td class="text-left small"><p>'.$nombre_contra_cuenta.'<br>'.$descripcion_contra_cuenta.' </p></td>
                        <td class="text-right small"></td>
                        <td class="text-right small">'.formatNumberDec($monto_contra_cuenta).'</td>
                        <td class="text-right small"></td>
                        <td class="text-right small">'.formatNumberDec($monto_contra_cuenta_dolares).'</td>
                    </tr>';
                    $html.='<tr>
                            <td class="text-left small"></td>
                            <td class="text-center small"><b>TOTAL</b></td>
                            <td class="text-right small">'.formatNumberDec($sumaTotalDebeBolivianos).'</td>
                            <td class="text-right small">'.formatNumberDec($sumaTotalHaberBolivianos).'</td>
                            <td class="text-right small">'.formatNumberDec($sumaTotalDebeDolares).'</td>
                            <td class="text-right small">'.formatNumberDec($sumaTotalHaberDolares).'</td>
                        </tr>';


                $html.='</tbody>'.
            '</table>'.
            '<footer>'.
                '<table class="table">'.
                    '<tbody>
                        <tr>
                            <td width="25%" height="35%"></td>
                            <td width="25%" height="35%"</td>
                            <td width="25%" height="35%"></td>
                            <td width="25%" height="35%"><p>Firma/Sello: _ _ _ _ _ _ _ _ _ _<br>Nombre:</p></td>
                        </tr>
                        <tr>
                            <td valign="top" height="25%" class="text-center small">ELABORADO POR</td>
                            <td valign="top" height="25%" class="text-center small">REVISADO POR</td>
                            <td valign="top" height="25%" class="text-center small">APROBADO POR</td>
                            <td height="25%" class="text-left small">C.I. N°</td>
                        </tr>
                    </tbody>'.
                '</table>'.
            '</footer>'.

        '</body>'.
      '</html>';           
descargarPDF("IBNORCA - ".$unidadC." (".$tipoC.", ".$numeroC.")",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
