<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require '../assets/phpqrcode/qrlib.php';

//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
$dbh = new Conexion();
// set_time_limit(300)
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
                '<link href="../assets/libraries/plantillaPDFFiniquito.css" rel="stylesheet" />'.
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
                '</header>'.
                '<table  border="1" align="center" style="width: 100%;">                
                    <tbody>
                        <tr>
                            <td colspan="2">'.
                                '<table style="width: 100%;"    >'.
                                    '<tbody>'.
                                        '<tr>'.
                                            '<td><img class="imagen_izq" src="../assets/img/bolivia.jpg"></td>'.
                                            '<td width="40%"></td>'.
                                            '<td style="text-align: right;"><img class="imagen_der" src="../assets/img/ministerio.jpg"></td>'.
                                        '</tr>'.
                                        '<tr>'.
                                            '<td><p class="header_texto_inf">ESTADO PLURINACIONAL DE BOLIVIA</p></td>'.
                                            '<td width="40%"><p  class="header_titulo_texto">FINIQUITO</p></td>'.
                                            '<td><p class="header_texto_inf">MINISTERIO DE TRABAJO, EMPLEO Y PREVISIÓN SOCIAL</p></td>'.
                                        '</tr>'.
                                    '</tbody>'.
                                '</table>'.
                            '</td>
                        </tr>'.
                        '<tr>'.
                            '<td colspan="2">I.- DATOS GENERALES</td>'.
                        '</tr>'.
                        '<tr>'.
                            '<td colspan="2">'.
                                '<table border="1" class="table_hijo">
                                
                                    <tr>
                                        <td colspan="4">RAZÓN SOCIAL O NOMBRE DE LA EMPRESA</td>                                    
                                        <td colspan="7"></td>
                                        <td style="width: 3%">1</td>
                                        <td style="width: 3%">2</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">RAMA DE ACTIVIDAD ECONÓMICA</td>                                    
                                        <td></td>
                                        <td></td>
                                        <td colspan="2">DOMICILIO</td>
                                        <td colspan="5"></td>                                    
                                    </tr>
                                    <tr>
                                        <td colspan="4">NOMBRE DEL TRABAJADOR</td>
                                        <td colspan="7"></td>
                                        <td>1</td>
                                        <td>2</td>
                                    </tr>
                                    <tr>
                                        <td >ESTADO CIVIL</td>
                                        <td colspan="2"></td>
                                        <td></td>
                                        <td colspan="2">EDAD</td>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2">DOMICILIO</td>
                                        <td colspan="3"></td>                                
                                    </tr>
                                    <tr>
                                        <td colspan="2">PROFESION U OCUPACIÓN</td>
                                        <td colspan="9"></td>
                                        <td></td>
                                        <td></td>                                                                    
                                    </tr>
                                    <tr>
                                        <td>CI</td>
                                        <td colspan="3"></td>
                                        <td colspan="2">FECHA INGRESO</td>
                                        <td colspan="2"></td>                                                                    
                                        <td colspan="2">FECHA RETIRO</td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">MOTIVO DEL RETIRO</td>
                                        <td colspan="3"></td>
                                        <td ></td>
                                        <td colspan="4">REMUNERACION MENSUAL Bs</td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" >TIEMPO DE SERVICIO</td>
                                        <td ></td>
                                        <td >AÑOS</td>
                                        <td colspan="2"></td>
                                        <td colspan="2">MESES</td>
                                        <td ></td>
                                        <td >DIAS</td>
                                        <td ></td>
                                        <td ></td>
                                        <td ></td>                                    
                                    </tr>
                                </table>'.
                            '</td>'.
                        '</tr>'.
                        '<tr>'.
                            '<td colspan="2">II.- LIQUIDACIÓN DE LA REMUNERACIÓN PROMEDIO INDEMNIZABLE EN BASE A LOS 3 ÚLTIMOS MESES</td>'.
                        '</tr>'.
                        '<tr>
                            <td colspan="2">
                                <table border="1" class="table_hijo">
                                    <tr>
                                        <td>A) MESES</td>
                                        <td colspan="2"></td>
                                        <td colspan="2"></td>
                                        <td colspan="2"></td>
                                        <td colspan="2">TOTALES</td>                                    
                                    </tr>
                                    <tr>
                                        <td>REMUNERACIÓN MENSUAL</td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td>B) OTROS CONCEPTOS PERCIBIDOS EN EL MES</td>
                                        <td colspan="2"></td>
                                        <td colspan="2"></td>
                                        <td colspan="2"></td>
                                        <td colspan="2"></td>                                    
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td>TOTAL</td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>'.
                        '<tr>'.
                            '<td>III .- TOTAL REMUNERACIÓN PROMEDIO INDEMNIZABLE (A + B) DIVIDIDO ENTRE 3:</td>'.
                            '<td style="width: 20%">Bs</td>'.
                        '</tr>'.
                        '<tr>
                            <td colspan="2">
                                <table border="1" class="table_hijo">
                                    <tr>
                                        <td colspan="11">C) DESAHUCIO  TRES MESES (EN CASO DE RETIRO FORZOSO)</td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">D) INDEMNIZACIÓN POR TIEMPO DE TRABAJO:</td>
                                        <td style="width: 3%">DE</td>
                                        <td ></td>
                                        <td colspan="2">AÑOS</td>
                                        <td style="width: 3%">Bs</td>
                                        <td colspan="2"></td>
                                        <td style="width: 3%"></td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td style="width: 3%">DE</td>
                                        <td ></td>
                                        <td colspan="2">MESES</td>
                                        <td style="width: 3%">Bs</td>
                                        <td colspan="2"></td>
                                        <td style="width: 3%"></td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td style="width: 3%">DE</td>
                                        <td ></td>
                                        <td colspan="2">DIAS</td>
                                        <td style="width: 3%">Bs</td>
                                        <td colspan="2"></td>
                                        <td style="width: 3%"></td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">AGUINALDO DE NAVIDAD</td>
                                        <td style="width: 3%">DE</td>
                                        <td ></td>
                                        <td colspan="2">MESES Y</td>
                                        <td colspan="2"></td>
                                        <td >DIAS</td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">VACACIÓN</td>
                                        <td style="width: 3%">DE</td>
                                        <td ></td>
                                        <td colspan="2">MESES Y</td>
                                        <td colspan="2"></td>
                                        <td >DIAS</td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">PRIMA LEGAL (SI CORRESPONDE)</td>
                                        <td style="width: 3%">DE</td>
                                        <td ></td>
                                        <td colspan="2">MESES Y</td>
                                        <td colspan="2"></td>
                                        <td >DIAS</td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td >OTROS</td>
                                        <td colspan="10"></td>
                                        
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td >GESTION</td>
                                        <td colspan="4"></td>
                                        <td style="width: 3%">DE</td>
                                        <td colspan="2"></td>
                                        <td >DIAS</td>                                    
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                    </tr>                                
                                </table>
                            </td>
                        </tr>'.
                        '<tr>'.
                            '<td>IV .- TOTAL BENEFICIOS SOCIALES: C + D</td>'.
                            '<td style="width: 20%">Bs</td>'.
                        '</tr>'.
                        '<tr>
                            <td colspan="2">
                                <table border="1" class="table_hijo">
                                    <tr>
                                        <td style="width: 20%">E) DEDUCCIONES:</td>
                                        <td style="width: 35%"></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 12%"></td>
                                        <td ></td>                                    
                                    </tr>
                                    <tr>
                                        <td style="width: 20%"></td>
                                        <td style="width: 35%"></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 12%"></td>
                                        <td ></td>                                    
                                    </tr>
                                    <tr>
                                        <td style="width: 20%"></td>
                                        <td style="width: 35%"></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 12%"></td>
                                        <td ></td>                                    
                                    </tr>
                                    <tr>
                                        <td style="width: 20%"></td>
                                        <td style="width: 35%"></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 12%"></td>
                                        <td ></td>                                    
                                    </tr>
                                    <tr>
                                        <td style="width: 20%"></td>
                                        <td style="width: 35%"></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 12%"></td>
                                        <td ></td>                                    
                                    </tr>
                                    <tr>
                                        <td style="width: 20%"></td>
                                        <td style="width: 35%"></td>
                                        <td style="width: 3%">Bs</td>
                                        <td ></td>
                                        <td style="width: 12%"></td>
                                        <td ></td>                                    
                                    </tr>
                                </table>
                            </td>
                        </tr>'.
                        '<tr>'.
                            '<td>V. IMPORTE LÍQUIDO A PAGAR C + D - E =</td>'.
                            '<td style="width: 20%">Bs</td>'.
                        '</tr>'.
                        
                    '</tbody>
                </table>'.            
            

            '<hr style="page-break-after: always;
                border: none;
                margin: 0;
                padding: 0;">'.
            
                '<table border="1" align="center" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td ><p align="justify" style="padding-left:20px;padding-right:20px;"> FORMA DE PAGO: &nbsp;&nbsp;EFECTIVO (&nbsp;&nbsp;&nbsp;) &nbsp;CHEQUE (&nbsp;&nbsp;&nbsp;)  Nº &nbsp; __________________________ C/BANCO &nbsp;_____________________ <br>
                                IMPORTE DE LA SUMA CANCELADA:  &nbsp; _____________________________________________________________________<br>______________________________________________________________________________________________________
                            <p></td>
                        </tr>
                        <tr>
                            <td ><p align="justify" style="padding-left:20px;padding-right:20px;"> YO  &nbsp; __________________________________________________________________________________________________<br>

                            MAYOR DE EDAD, CON C.I. Nº ____________________ DECLARO QUE EN LA FECHA RECIBO A MI ENTERA
                            SATISFACCIÓN, EL IMPORTE DE ____________________ POR CONCEPTO DE LA LIQUIDACIÓN DE MIS BENEFICIOS SOCIALES, DE CONFORMIDAD CON LA LEY GENERAL DEL TRABAJO, SU DECRETO REGLAMENTARIO Y DISPOSICIONES CONEXAS.<br><br><br>

                            LUGAR Y FECHA &nbsp;________________________ &nbsp;,&nbsp; ___________&nbsp;DE &nbsp;_____________________________&nbsp;DE 20 &nbsp;__________                                  
                            <p>
                            <br><br><br><br><br><br>
                            <table style="width: 100%;">
                                <tr>
                                <td><p align="center">_______________________________<br>INTERESADO</p></td>
                                <td><p align="center">_______________________________<br>GERENTE GENERAL</p></td>
                                </tr>
                            </table><br><br><br><br><br>
                            <table style="width: 100%;">
                                <tr>
                                <td><p align="center">_______________________________<br>Vo. Bo. MINISTERIO DE TRABAJO</p></td>
                                <td> <p align="center"> _______________________________<br>SELLO</p></td>
                                </tr>
                            </table>
                            <br>
                            
                            </td>
                        </tr>
                        <tr>
                            <td ><p class="titulo_texto" align="center">INSTRUCCIONES<p>
                                <table align="center" style="width: 80%;">
                                <tr>
                                    <td style="width: 5%;"  VALIGN="TOP"><p> 1. </p></td>
                                    <td>
                                        <p align="justify" >
                                            En todos los casos en los cuales proceda el pago de beneficios sociales y que no estén comprendidos en el despido por las causales en el Art. 16 de la Ley General del Trabajo y el Art. 9 de su Reglamento, el Finiquito de contrato se suscribirá en el presente FORMULARIO
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td VALIGN="TOP" style="width: 5%;"><p> 2. </p></td>
                                    <td>
                                        <p align="justify" >
                                            Los señores Directores, Jefes Departamentales e Inspectores Regionales, son los únicos funcionarios facultados para revisar y refrendar todo finiquito de contrato de trabajo, con cuya intervención alcanzará la correspondiente eficacia jurídica, en aplicación del Art. 22 de la Ley General del Trabajo.<br><br>
                                            La intervención de cualquier otro funcionario del Ministerio de Trabajo y Microempresa carecerá de toda validez legal.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td VALIGN="TOP" style="width: 5%;"><p> 3. </p></td>
                                    <td>
                                        <p align="justify" >
                                            Las partes intervinientes en la suscripción del presente FINIQUITO, deberán acreditar su identidad personal con los documentos señalados por ley.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td  VALIGN="TOP" style="width: 5%;"><p> 4. </p></td>
                                    <td>
                                        <p align="justify" >
                                            Este Formulario no constituye Ley entre partes por su carácter esencialmente revisable, por lo tanto las cifras en él contenidas no causan estado ni revisten el sello de cosa juzgada.
                                        </p>
                                    </td>
                                </tr>
                                </table>
                                <br>
                                <br>
                                <br>
                            </td>
                        </tr>
                    </tbody>
                </table>'.
        '</body>'.
      '</html>';           
descargarPDFFiniquito("IBNORCA - ".$unidadC." (".$tipoC.", ".$numeroC.")",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>


<!-- '<table border="1" align="center" style="width: 80%;border-collapse: collapse;">'.
                '<tbody style=" font-family: Times New Roman;
                                font-size: 12px;
                                    ">'; -->
<!-- 
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
            '</table>'. -->