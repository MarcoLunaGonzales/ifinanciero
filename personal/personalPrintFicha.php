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
    $stmtPersonal = $dbh->prepare("SELECT *
    from personal
    WHERE codigo=:codigo");

    $stmtPersonal->bindParam(':codigo',$codigo);
    $stmtPersonal->execute();
    $result = $stmtPersonal->fetch();

    $codigo = $result['codigo'];
    $ci = $result['ci'];
    $ci_lugar_emision = $result['ci_lugar_emision'];
    $fecha_nacimiento = $result['fecha_nacimiento'];
    $cod_cargo = $result['cod_cargo'];
    $cod_unidadorganizacional = $result['cod_unidadorganizacional'];
    $cod_area = $result['cod_area'];
    $jubilado = $result['jubilado'];
    $cod_genero = $result['cod_genero'];
    $cod_tipopersonal = $result['cod_tipopersonal'];
    $haber_basico = $result['haber_basico'];
    $paterno = $result['paterno'];
    $materno = $result['materno'];
    $apellido_casada = $result['apellido_casada'];
    $primer_nombre = $result['primer_nombre'];
    $otros_nombres = $result['otros_nombres'];
    $nua_cua_asignado = $result['nua_cua_asignado'];
    $direccion = $result['direccion'];
    $cod_tipoafp = $result['cod_tipoafp'];
    $cod_tipoaporteafp = $result['cod_tipoaporteafp'];
    $nro_seguro = $result['nro_seguro'];
    $cod_estadopersonal = $result['cod_estadopersonal'];
    $telefono = $result['telefono'];
    $celular = $result['celular'];
    $email = $result['email'];
    $persona_contacto = $result['persona_contacto'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $modified_at = $result['modified_at'];
    $modified_by = $result['modified_by'];
    


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
            '<div id="header_titulo_texto">Ficha De Personal</div>'.

            '<br><br><br><br>'.
            '<table align="center">'.
                '<tbody>';                
                    
                    $html.='<tr>'.
                        '<td class="text-left small">'.
                            '<p>'.
                                '<b>Código Personal : </b>'.$codigo.'<br>'.                               
                                '<b>CI : </b>'.$ci.' '.$ci_lugar_emision.'<br>'.
                                '<b>Nombres Personal : </b>'.$primer_nombre.' '.$otros_nombres.' <br>'.
                                '<b>Apellidos : </b>'.$paterno.' '.$materno.' <br>'.                                
                                '<b>Apellido Casada : </b>'.$apellido_casada.' <br>'.                                
                                '<b>Fecha Nacimiento : </b>'.$fecha_nacimiento.' <br>'.
                                '<b>UO : </b>'.$cod_unidadorganizacional.'<br>'.
                                '<b>Area : </b>'.$cod_area.' <br>'.
                                '<b>Cargo : </b>'.$cod_cargo.'<br>'.
                                '<b>Jubilado : </b>'.$jubilado.'<br>'.
                                '<b>Haber Básico : </b> '.$haber_basico.'<br>'.
                                '<b>Tipo Personal : </b>'.$cod_tipopersonal.'<br>'.
                                '<b>Estado Personal : </b>'.$cod_estadopersonal.'<br>'.
                                '<b>Nua Cua Asignado : </b>'.$nua_cua_asignado.'<br>'.'<br>'.
                                '<b>Nro. Seguro : </b>'.$nro_seguro.'<br>'.
                                '<b>Tipo AFP : </b>'.$cod_tipoafp.'<br>'.
                                '<b>Tipo Aporte AFP : </b>'.$cod_tipoaporteafp.'<br>'.
                                '<b>Dirección : </b>'.$direccion.'<br>'.
                                '<b>Telefono : </b>'.$telefono.'<br>'.
                                '<b>Celular : </b>'.$celular.'<br>'.
                                '<b>Email : </b>'.$email.'<br>'.
                            '</p>'.
                        '</td>'.
                        '<td class="text-right small">'.
                            '<img src="imagenes/'.$imagen.'" style="width: 150px; height: 150px;"><br>'.
                        '</td>'.
                    '</tr>'.
                    '<hr>'.
                    '<tr>'. 
                        '<td>'.
                        '</td>'.
                        '<td>'.
                        '</td>'.
                        
                    '</tr>'.

                    '<hr>'.

                '</tbody>'.            
        '</body>'.
      '</html>';           
descargarPDF("IBNORCA - ".$unidadC." (".$tipoC.", ".$numeroC.")",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
