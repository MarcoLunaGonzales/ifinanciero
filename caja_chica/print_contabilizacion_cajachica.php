<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';
require_once __DIR__.'/../functionsGeneral.php';
$dbh = new Conexion();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);//try
//RECIBIMOS LAS VARIABLES
set_time_limit(3000);
$cod_cajachica = $_GET["cod_cajachica"];//codigoactivofijo
try{
    //lsitado de todo el detalle de caja chica en curso
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
    $stmtCajaChicaDet->bindColumn('cod_tipodoccajachica', $cod_retencioncajachica);
    $stmtCajaChicaDet->bindColumn('observaciones', $observaciones_dcc);
    $stmtCajaChicaDet->bindColumn('monto', $monto_dcc);
    
    //====================================
    //Informacion caja chica en curso
    $stmtCajaChica = $dbh->prepare("SELECT *,(select CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) from personal p where p.codigo=cod_personal) as name_personal,
        (select tc.nombre from tipos_caja_chica tc where tc.codigo=cod_tipocajachica) as name_tipocc,
                (select (select uo.nombre from unidades_organizacionales uo where uo.codigo= tc2.cod_uo) from tipos_caja_chica tc2 where tc2.codigo=cod_tipocajachica)as nombre_uo_tcc
        FROM caja_chica where codigo=$cod_cajachica");
    $stmtCajaChica->execute();
    $resultCCD = $stmtCajaChica->fetch();
    $cod_tipocajachica = $resultCCD['cod_tipocajachica'];    
    $numeroCC = $resultCCD['numero'];
    $monto_inicio = $resultCCD['monto_inicio'];
    $monto_reembolso = $resultCCD['monto_reembolso'];
    $observacionesCC = $resultCCD['observaciones'];
    $cod_personalCCD = $resultCCD['cod_personal'];
    $name_personalCC = $resultCCD['name_personal'];
    $name_tipoccCC = $resultCCD['name_tipocc'];
    $nombre_uo_tcc = $resultCCD['nombre_uo_tcc'];
    $fecha_inicio_cc = $resultCCD['fecha'];
    $fecha_cierre_cc = $resultCCD['fecha_cierre'];

    // $porcentajeIVA=obtenerValorConfiguracion(1);
    // $cod_cuenta_iva=obtenerValorConfiguracion(3);
    // $nro_cuenta_iva=obtieneNumeroCuenta($cod_cuenta_iva);
    // $nombre_cuenta_iva=nameCuenta($cod_cuenta_iva);
    
    // $porcentajeIUE_servicios=obtenerValorConfiguracion(23);
    // $cod_cuenta_IUE_S=obtenerValorConfiguracion(27);
    // $nro_cuenta_IUE_S=obtieneNumeroCuenta($cod_cuenta_IUE_S);
    // $nombre_cuenta_IUE_s=nameCuenta($cod_cuenta_IUE_S);

    // $porcentajeIUE_compras=obtenerValorConfiguracion(24);
    // $cod_cuenta_IUE_C=obtenerValorConfiguracion(26);
    // $nro_cuenta_IUE_C=obtieneNumeroCuenta($cod_cuenta_IUE_C);
    // $nombre_cuenta_IUE_C=nameCuenta($cod_cuenta_IUE_C);
    
    // $porcentajeIT=obtenerValorConfiguracion(2);
    // $cod_cuenta_IT=obtenerValorConfiguracion(25);
    // $nro_cuenta_IT=obtieneNumeroCuenta($cod_cuenta_IT);
    // $nombre_cuenta_IT=nameCuenta($cod_cuenta_IT);
    //CONTRA CUENTA
    $cod_contra_cuenta=obtenerValorConfiguracion(28);
    $nro_contra_cuenta=obtieneNumeroCuenta($cod_contra_cuenta);
    $nombre_contra_cuenta=nameCuenta($cod_contra_cuenta);
    // $monto_contra_cuenta=$monto_inicio-$monto_reembolso;

    // $IUE_compras_IT=$porcentajeIUE_compras+$porcentajeIT;
    // $IUE_servicios_IT=$porcentajeIUE_servicios+$porcentajeIT;

    $fecha_actual=date('Y/m/d');
    $tipoC="Traspaso";

    $moneda=2;
    $abrevMon=abrevMoneda($moneda);
    $nombreMonedaG=nameMoneda($moneda);
    

    $USD_actual=obtenerValorTipoCambio(2,strftime('%Y-%m-%d',strtotime($fecha_actual)));
    if($USD_actual=='')$USD_actual=0;
    // $concepto_contabilizacion=strtoupper($name_personalCC)." rendición de ".$observacionesCC." ".$name_tipoccCC."( - - - - )";
    $concepto_contabilizacion="CONTABILIZACIÓN CAJA CHICA N° ".$numeroCC." DE ".$nombre_uo_tcc;
header('Content-type: text/html; charset=ISO-8859-1');
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
            '<img class="imagen-logo-izq" src="../assets/img/ibnorca2.jpg">'.
            '<div id="header_titulo_texto">Comprobante de Contabilidad</div>'.
            '<div id="header_titulo_texto_inf">UNIDAD DE '.$nombre_uo_tcc.'</div>'.

            // '<div id="info_izq2">
            //   <h4 >
            //   <b>Entidad:'.'10'.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. obtenerValorConfiguracionEmpresa(7).' </b><br>
            //   <b>Fondo:  1011'. ' &nbsp;&nbsp;&nbsp;'. 'OFICINA '.' </b><br>
            //   </h4> 
            // </div>'.            
            '<table class="table pt-2">'.
                '<tbody>
                    <tr lass="bold table-title">
                        <td>Fecha: '.$fecha_actual.'</td>
                        <td>t/c: '.$abrevMon.': '.$USD_actual.'</td>
                        <td>Traspaso '.strtoupper(abrevMes(strftime('%m',strtotime($fecha_actual)))).' N&uacute;mero: '.generarNumeroCeros(6,$numeroCC).'</td>                    
                    </tr>
                    <tr>
                        <td colspan="3">CONCEPTO: '.$concepto_contabilizacion.'</td>
                    </tr>
                </tbody>'.
            '</table>'.            
        '</header><br><br>'.
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
            
        '<table class="table">'.
            '<thead>
                <tr class="bold table-title text-center">
                    <th colspan="2"></th>                        
                    <th colspan="2">Bolivianos</th>
                    <th colspan="2">Dólares</th>                        
                </tr>
                <tr class="bold table-title text-center">
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
                        //verificamos si tiene factura
                        $sw_facturas=0;//contador de facturas
                        $stmtFacturas = $dbh->prepare("SELECT nro_factura,razon_social,importe from facturas_detalle_cajachica where cod_cajachicadetalle=$codigo_ccdetalle");
                        $stmtFacturas->execute();
                        $stmtFacturas->bindColumn('nro_factura', $nro_factura);
                        $stmtFacturas->bindColumn('razon_social', $razon_social);
                        $stmtFacturas->bindColumn('importe', $importe);                                
                        while ($rowFac = $stmtFacturas->fetch()) 
                        {     
                            //buscamos el tipo de retencion
                            $stmtRetenciones = $dbh->prepare("SELECT cod_cuenta,porcentaje,debe_haber from configuracion_retencionesdetalle where cod_configuracionretenciones=$cod_retencioncajachica");
                            $stmtRetenciones->execute();
                            $stmtRetenciones->bindColumn('cod_cuenta', $cod_cuenta_retencion);
                            $stmtRetenciones->bindColumn('porcentaje', $porcentaje_retencion);
                            $stmtRetenciones->bindColumn('debe_haber', $debe_haber);
                            while ($rowFac = $stmtRetenciones->fetch()) 
                            {                            
                                $descripcion=$nombre_uo.' F/'.$nro_factura.' '.$personal.', '.$observaciones_dcc;
                                $monto=$importe*$porcentaje_retencion/100;
                                if($USD_actual!=0)
                                    $monto_dolares=$monto/$USD_actual;
                                else $monto_dolares=0;  
                                //las retenciones
                                if($cod_cuenta_retencion>0){
                                    $nro_cuenta_retencion=obtieneNumeroCuenta($cod_cuenta_retencion);
                                    $nombre_cuenta_retencion=nameCuenta($cod_cuenta_retencion);
                                    if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
                                        $html.='<tr>'.
                                            '<td class="text-left">'.$nro_cuenta_retencion.'</td>'.
                                            '<td class="text-left">'.$nombre_cuenta_retencion.'<br>'.$descripcion.'</td>'.
                                            '<td class="text-right">'.formatNumberDec($monto).'</td>'.
                                            '<td class="text-right"></td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_dolares).'</td>'.
                                            '<td class="text-right"></td>'.
                                        '</tr>';
                                        $sumaTotalDebeBolivianos+=$monto;
                                        $sumaTotalDebeDolares+=$monto_dolares;
                                    }else{
                                        $html.='<tr>'.
                                            '<td class="text-left">'.$nro_cuenta_retencion.'</td>'.
                                            '<td class="text-left">'.$nombre_cuenta_retencion.'<br>'.$descripcion.'</td>'.
                                            '<td class="text-right"></td>'.
                                            '<td class="text-right">'.formatNumberDec($monto).'</td>'.
                                            '<td class="text-right"></td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_dolares).'</td>'.
                                        '</tr>';
                                        $sumaTotalHaberBolivianos+=$monto;
                                        $sumaTotalHaberDolares+=$monto_dolares;
                                    }
                                }else{
                                    //necesitamos el monto obtenido despues de aplicar las retenciones                                
                                    $monto_restante=$importe*$porcentaje_retencion/100;
                                    //desde aqui repartimos la contabilizacion a las oficinas 
                                    $stmtOficina = $dbh->prepare("SELECT dgd.cod_unidadorganizacional,dgd.porcentaje,
                                       (SELECT uo.nombre FROM unidades_organizacionales uo WHERE uo.codigo=dgd.cod_unidadorganizacional) as oficina
                                    from distribucion_gastosporcentaje_detalle dgd,distribucion_gastosporcentaje dg
                                    where dgd.cod_distribucion_gastos=dg.codigo and dg.estado=1 and porcentaje>0");
                                    $stmtOficina->execute();
                                    $stmtOficina->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
                                    $stmtOficina->bindColumn('porcentaje', $porcentaje);
                                    $stmtOficina->bindColumn('oficina', $oficinaFac);
                                    while ($rowOf = $stmtOficina->fetch()) 
                                    {                                    
                                        $descripcion_of=$oficinaFac.'/'.$nombre_uo.' F/'.$nro_factura.' '.$personal.', '.$observaciones_dcc;
                                        $monto_of=$monto_restante*$porcentaje/100;
                                        if($USD_actual!=0)
                                            $monto_of_dolares=$monto_of/$USD_actual;
                                        else $monto_of_dolares=0;

                                        if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
                                            $html.='<tr>'.
                                                '<td class="text-left">'.$numero_cuenta.'</td>'.
                                                '<td class="text-left">'.$nombre_cuenta.'<br>'.$descripcion_of.'</td>'.
                                                '<td class="text-right">'.formatNumberDec($monto_of).'</td>'.
                                                '<td class="text-right"></td>'.
                                                '<td class="text-right">'.formatNumberDec($monto_of_dolares).'</td>'.
                                                '<td class="text-right"></td>'.
                                            '</tr>';
                                            $sumaTotalDebeBolivianos+=$monto_of;
                                            $sumaTotalDebeDolares+=$monto_of_dolares;
                                        }else{
                                            $html.='<tr>'.
                                                '<td class="text-left">'.$numero_cuenta.'</td>'.
                                                '<td class="text-left">'.$nombre_cuenta.'<br>'.$descripcion_of.'</td>'.
                                                '<td class="text-right"></td>'.
                                                '<td class="text-right">'.formatNumberDec($monto_of).'</td>'.
                                                '<td class="text-right"></td>'.
                                                '<td class="text-right">'.formatNumberDec($monto_of_dolares).'</td>'.
                                            '</tr>';
                                            $sumaTotalHaberBolivianos+=$monto_of;
                                            $sumaTotalHaberDolares+=$monto_of_dolares;
                                        }                                     
                                    }

                                    // aqui la contra cuenta, invertimos el proceso
                                    $descripcion_contra_cuenta='CONTABILIZACIÓN CAJA CHICA. '.$personal.', '.$observaciones_dcc;
                                    $monto_contracuenta=$importe;
                                    if($USD_actual!=0)
                                        $monto_contracuenta_dolares=$monto_contracuenta/$USD_actual;
                                    else $monto_contracuenta_dolares=0;
                                    if($debe_haber==1){//si es debe, pondremos en haber
                                        $sumaTotalHaberBolivianos+=$monto_contracuenta;
                                        $sumaTotalHaberDolares+=$monto_contracuenta_dolares;
                                        $html.='<tr>'.
                                            '<td class="text-left">'.$nro_contra_cuenta.'</td>'.
                                            '<td class="text-left">'.$nombre_contra_cuenta.'<br>'.$descripcion_contra_cuenta.'</td>'.
                                            '<td class="text-right"></td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_contracuenta).'</td>'.
                                            '<td class="text-right"></td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_contracuenta_dolares).'</td>'.
                                        '</tr>'; 
                                    }else{
                                        $sumaTotalDebeBolivianos+=$monto_contracuenta;
                                        $sumaTotalDebeDolares+=$monto_contracuenta_dolares;
                                        $html.='<tr>'.
                                            '<td class="text-left">'.$nro_contra_cuenta.'</td>'.
                                            '<td class="text-left">'.$nombre_contra_cuenta.'<br>'.$descripcion_contra_cuenta.'</td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_contracuenta).'</td>'.
                                            '<td class="text-right"></td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_contracuenta_dolares).'</td>'.
                                            '<td class="text-right"></td>'.
                                        '</tr>'; 
                                    }
                                }
                                $sw_facturas++;//contador de facturas incrementa
                            }  
                        }
                        if($sw_facturas==0){//compra no tiene factura registrada                        
                            //buscamos el tipo de retencion
                            $stmtRetenciones = $dbh->prepare("SELECT cod_cuenta,porcentaje,debe_haber from configuracion_retencionesdetalle where cod_configuracionretenciones=$cod_retencioncajachica");
                            $stmtRetenciones->execute();
                            $stmtRetenciones->bindColumn('cod_cuenta', $cod_cuenta_retencion);
                            $stmtRetenciones->bindColumn('porcentaje', $porcentaje_retencion);
                            $stmtRetenciones->bindColumn('debe_haber', $debe_haber);
                            while ($rowFac = $stmtRetenciones->fetch()) 
                            {                            
                                //recalculando monto
                                // $monto_recalculado=$monto_dcc/(1-($IUE_compras_IT/100));
                                //para retencion it 3%
                                $descripcionIT=$nombre_uo.' SF '.$personal.', '.$observaciones_dcc;
                                // $monto_it=$monto_recalculado*$porcentajeIT/100;
                                $monto_it=$monto_dcc*$porcentaje_retencion/100;
                                if($USD_actual!=0)
                                    $monto_it_dolares=$monto_it/$USD_actual;
                                else $monto_it_dolares=0;
                                //las retenciones
                                if($cod_cuenta_retencion>0){
                                    $nro_cuenta_retencion=obtieneNumeroCuenta($cod_cuenta_retencion);
                                    $nombre_cuenta_retencion=nameCuenta($cod_cuenta_retencion);
                                    if($debe_haber==1){ //preguntamos si pertenece a la columna debe o haber
                                        $html.='<tr>'.
                                            '<td class="text-left">'.$nro_cuenta_retencion.'</td>'.
                                            '<td class="text-left">'.$nombre_cuenta_retencion.'<br>'.$descripcionIT.'</td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_it).'</td>'.
                                            '<td class="text-right"></td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_it_dolares).'</td>'.
                                            '<td class="text-right"></td>'.
                                        '</tr>';
                                        $sumaTotalDebeBolivianos+=$monto_it;
                                        $sumaTotalDebeDolares+=$monto_it_dolares;
                                    }else{
                                        $html.='<tr>'.
                                            '<td class="text-left">'.$nro_cuenta_retencion.'</td>'.
                                            '<td class="text-left">'.$nombre_cuenta_retencion.'<br>'.$descripcionIT.'</td>'.
                                            '<td class="text-right"></td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_it).'</td>'.
                                            '<td class="text-right"></td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_it_dolares).'</td>'.
                                        '</tr>';
                                        $sumaTotalHaberBolivianos+=$monto_it;
                                        $sumaTotalHaberDolares+=$monto_it_dolares;
                                    }
                                }else{
                                    //necesitamos el monto obtenido despues de aplicar las retenciones                                
                                    $monto_restante=$monto_dcc*$porcentaje_retencion/100;
                                    //desde aqui repartimos la contabilizacion a las oficinas 
                                    $stmtOficina = $dbh->prepare("SELECT dgd.cod_unidadorganizacional,dgd.porcentaje,
                                       (SELECT uo.nombre FROM unidades_organizacionales uo WHERE uo.codigo=dgd.cod_unidadorganizacional) as oficina
                                    from distribucion_gastosporcentaje_detalle dgd,distribucion_gastosporcentaje dg
                                    where dgd.cod_distribucion_gastos=dg.codigo and dg.estado=1 and porcentaje>0");
                                    $stmtOficina->execute();
                                    $stmtOficina->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
                                    $stmtOficina->bindColumn('porcentaje', $porcentaje);
                                    $stmtOficina->bindColumn('oficina', $oficinaFac);
                                    while ($rowOf = $stmtOficina->fetch()) 
                                    {                                                                            
                                        $descripcion_of=$oficinaFac.'/'.$nombre_uo.' SF '.$personal.', '.$observaciones_dcc;
                                        $monto_of=$monto_restante*$porcentaje/100;
                                        if($USD_actual!=0)
                                            $monto_of_dolares=$monto_of/$USD_actual;
                                        else $monto_of_dolares=0;
                                        if($debe_haber==1){ //preguntamps si pertenece a la columna debe o haber
                                            $html.='<tr>'.
                                                '<td class="text-left">'.$numero_cuenta.'</td>'.
                                                '<td class="text-left">'.$nombre_cuenta.'<br>'.$descripcion_of.'</td>'.
                                                '<td class="text-right">'.formatNumberDec($monto_of).'</td>'.
                                                '<td class="text-right"></td>'.
                                                '<td class="text-right">'.formatNumberDec($monto_of_dolares).'</td>'.
                                                '<td class="text-right"></td>'.
                                            '</tr>';
                                            $sumaTotalDebeBolivianos+=$monto_of;
                                            $sumaTotalDebeDolares+=$monto_of_dolares;
                                        }else{
                                            $html.='<tr>'.
                                                '<td class="text-left">'.$numero_cuenta.'</td>'.
                                                '<td class="text-left">'.$nombre_cuenta.'<br>'.$descripcion_of.'</td>'.
                                                '<td class="text-right"></td>'.
                                                '<td class="text-right">'.formatNumberDec($monto_of).'</td>'.
                                                '<td class="text-right"></td>'.
                                                '<td class="text-right">'.formatNumberDec($monto_of_dolares).'</td>'.
                                            '</tr>';
                                            $sumaTotalHaberBolivianos+=$monto_of;
                                            $sumaTotalHaberDolares+=$monto_of_dolares;
                                        }
                                    }

                                    // aqui la contra cuenta, invertimos el proceso
                                    $descripcion_contra_cuenta='CONTABILIZACIÓN CAJA CHICA.'.$personal.', '.$observaciones_dcc;
                                    $monto_contracuenta=$monto_dcc;
                                    if($USD_actual!=0)
                                        $monto_contracuenta_dolares=$monto_contracuenta/$USD_actual;
                                    else $monto_contracuenta_dolares=0;
                                    if($debe_haber==1){//si es debe, pondremos en haber
                                        $sumaTotalHaberBolivianos+=$monto_contracuenta;
                                        $sumaTotalHaberDolares+=$monto_contracuenta_dolares;
                                        $html.='<tr>'.
                                            '<td class="text-left">'.$nro_contra_cuenta.'</td>'.
                                            '<td class="text-left">'.$nombre_contra_cuenta.'<br>'.$descripcion_contra_cuenta.'</td>'.
                                            '<td class="text-right"></td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_contracuenta).'</td>'.
                                            '<td class="text-right"></td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_contracuenta_dolares).'</td>'.
                                        '</tr>'; 
                                    }else{
                                        $sumaTotalDebeBolivianos+=$monto_contracuenta;
                                        $sumaTotalDebeDolares+=$monto_contracuenta_dolares;
                                        $html.='<tr>'.
                                            '<td class="text-left">'.$nro_contra_cuenta.'</td>'.
                                            '<td class="text-left">'.$nombre_contra_cuenta.'<br>'.$descripcion_contra_cuenta.'</td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_contracuenta).'</td>'.
                                            '<td class="text-right"></td>'.
                                            '<td class="text-right">'.formatNumberDec($monto_contracuenta_dolares).'</td>'.
                                            '<td class="text-right"></td>'.
                                        '</tr>'; 
                                    }
                                }
                                $sw_facturas++;//contador de facturas incrementa
                            }
                        }
                }
                // //contra cuenta
                // $descripcion_contra_cuenta=$concepto_contabilizacion;
                // if($USD_actual!=0)
                //     $monto_contra_cuenta_dolares=$monto_contra_cuenta/$USD_actual;
                // else $monto_contra_cuenta_dolares=0;
                // $sumaTotalHaberBolivianos+=$monto_contra_cuenta;
                // $sumaTotalHaberDolares+=$monto_contra_cuenta_dolares;
                // $html.='<tr>
                //     <td class="text-left">'.$nro_contra_cuenta.'</td>
                //     <td class="text-left">'.$nombre_contra_cuenta.'<br>'.$descripcion_contra_cuenta.' </td>
                //     <td class="text-right"></td>
                //     <td class="text-right">'.formatNumberDec($monto_contra_cuenta).'</td>
                //     <td class="text-right"></td>
                //     <td class="text-right">'.formatNumberDec($monto_contra_cuenta_dolares).'</td>
                // </tr>';
                // $entero=floor($sumaTotalDebeBolivianos);
                // $decimal=$sumaTotalDebeBolivianos-$entero;
                // $centavos=round($decimal*100);
                // if($centavos<10){
                // $centavos="0".$centavos;
                // }
                $html.='<tr>
                        <td class="text-left"></td>
                        <td class="text-center"><b>TOTAL</b></td>
                        <td class="text-right">'.formatNumberDec($sumaTotalDebeBolivianos).'</td>
                        <td class="text-right">'.formatNumberDec($sumaTotalHaberBolivianos).'</td>
                        <td class="text-right">'.formatNumberDec($sumaTotalDebeDolares).'</td>
                        <td class="text-right">'.formatNumberDec($sumaTotalHaberDolares).'</td>
                    </tr>';
                


            $html.='</tbody>'.
        '</table>'.


    '</body>'.
  '</html>';
  $html.='<p class="bold table-title">Son: '.ucfirst(CifrasEnLetras::convertirNumeroEnLetras($entero)).'      '.$centavos.'/100 Bolivianos</p>';         
descargarPDF("IBNORCA - ".$nombre_uo_tcc." (".$tipoC.", ".$numeroCC.")",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
