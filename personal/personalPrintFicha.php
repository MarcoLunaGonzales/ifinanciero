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
    $stmtPersonal = $dbh->prepare("SELECT *,
        (SELECT ga.nombre from personal_grado_academico ga where ga.codigo=cod_grado_academico) as grado_academico,
        (SELECT ti.nombre from tipos_identificacion_personal  ti where ti.codigo=cod_tipo_identificacion)as tipo_identificacion,
        (select c.nombre from cargos c where c.codigo=cod_cargo) as cod_cargoX,
        (select uo.nombre from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional) as cod_unidadorganizacionalX,
        (select a.nombre from areas a where a.codigo=cod_area) as cod_areaX,
        (select ep.nombre from estados_personal ep where ep.codigo=cod_estadopersonal) as cod_estadopersonalX,
        (select tp.nombre from tipos_personal tp where tp.codigo=cod_tipopersonal) as cod_tipopersonalX,
        (select tafp.nombre from tipos_afp tafp where tafp.codigo=cod_tipoafp) as cod_tipoafpX,
        (select taafp.nombre from tipos_aporteafp taafp where taafp.codigo=cod_tipoaporteafp) as cod_tipoaporteafpX
    from personal
    WHERE codigo=:codigo");

    $stmtPersonal->bindParam(':codigo',$codigo);
    $stmtPersonal->execute();
    $result = $stmtPersonal->fetch();

    $codigo = $result['codigo'];

    $cod_tipoIdentificacion = $result['tipo_identificacion'];
    $tipo_identificacionOtro = $result['tipo_identificacion_otro'];
    
    $identificacion = $result['identificacion'];
    $cod_lugar_emision = $result['cod_lugar_emision'];
    $lugar_emisionOtro = $result['lugar_emision_otro'];
    $grado_academico = $result['grado_academico'];
    

    $cod_nacionalidad = $result['cod_nacionalidad'];
    $cod_estadocivil = $result['cod_estadocivil'];//-
    $cod_pais = $result['cod_pais'];
    $cod_departamento = $result['cod_departamento'];
    $cod_ciudad = $result['cod_ciudad'];
    $ciudadOtro = $result['ciudad_otro']; 

    $fecha_nacimiento = $result['fecha_nacimiento'];
    $cod_cargo = $result['cod_cargoX'];
    $cod_unidadorganizacional = $result['cod_unidadorganizacionalX'];
    $cod_area = $result['cod_areaX'];
    $jubilado = $result['jubilado'];
    $cod_genero = $result['cod_genero'];
    $cod_tipopersonal = $result['cod_tipopersonalX'];
    $haber_basico = $result['haber_basico'];
    $paterno = $result['paterno'];
    $materno = $result['materno'];
    $apellido_casada = $result['apellido_casada'];
    $primer_nombre = $result['primer_nombre'];
    $otros_nombres = $result['otros_nombres'];
    $nua_cua_asignado = $result['nua_cua_asignado'];
    $direccion = $result['direccion'];
    $cod_tipoafp = $result['cod_tipoafpX'];
    $cod_tipoaporteafp = $result['cod_tipoaporteafpX'];
    $nro_seguro = $result['nro_seguro'];
    $cod_estadopersonal = $result['cod_estadopersonalX'];
    $telefono = $result['telefono'];
    $celular = $result['celular'];
    $email = $result['email'];
    $persona_contacto = $result['persona_contacto'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $modified_at = $result['modified_at'];
    $modified_by = $result['modified_by'];
    //====================================
    //personal discapacitado
    $stmtDiscapacitado = $dbh->prepare("SELECT * FROM personal_discapacitado where codigo =:codigo");
    $stmtDiscapacitado->bindParam(':codigo',$codigo);
    $stmtDiscapacitado->execute();
    $resultDiscapacitado = $stmtDiscapacitado->fetch();
    $discapacitado = $resultDiscapacitado['discapacitado'];
    $tutor_discapacitado = $resultDiscapacitado['tutor_discapacitado'];
    $celular_tutor = $resultDiscapacitado['celular_tutor'];
    $parentesco = $resultDiscapacitado['parentesco'];


        //==================================================================================================================
    //imagen
    $stmtIM = $dbh->prepare("SELECT * FROM personalimagen  where codigo =:codigo");
    $stmtIM->bindParam(':codigo',$codigo);
    $stmtIM->execute();
    $resultIM = $stmtIM->fetch();
    //$codigo = $result['codigo'];
    $imagen = $resultIM['imagen'];
    


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
        
            '<br>'.'<br>'.'<br>'.'<br>'.
            '<table border="1" align="center" style="width: 80%;border-collapse: collapse;">'.
                '<colgroup>'.
                    '<col style="width: 25%"/>'.
                    '<col style="width: 5%"/>'.
                    '<col style="width: 25%"/>'.
                    '<col style="width: 25%"/>'.
                '</colgroup>'.
                '<tbody style=" font-family: Times New Roman;
                                font-size: 11px;
                                    ">';

                    $html.='<tr>'.
                        '<td colspan="3" align="center">'.                        
                            '<h2><b>'.$paterno.' '.$materno.' '.$primer_nombre.'</b><br></h2>'.
                            $cod_cargo.' / '.$cod_unidadorganizacional.'<br>'.
                            $cod_area.'<br>'.

                        '</td>'.
                        '<td rowspan="8" align="center">'.
                            '<img src="imagenes/'.$imagen.'" style="width: 120px; height: 120px;">'.
                        '</td>'.
                    '</tr>'.
                    
                    '<tr>'.
                        '<td>Código</td>'.
                        '<td align="center">:</td>'.
                        '<td>'.$codigo.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Tipo De identificación</td>'.
                        '<td align="center">:</td>'.
                        '<td>'.$cod_tipoIdentificacion.' '.$tipo_identificacionOtro.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Identificación</td>'.
                        '<td align="center">:</td>'.
                        '<td>'.$identificacion.' - '.$cod_lugar_emision.' '.$lugar_emisionOtro.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Grado Académico</td>'.
                        '<td align="center">:</td>'.
                        '<td>'.$grado_academico.'</td>'.
                    '</tr>'.                    
                    '<tr>'.
                        '<td>Telefono</td>'.
                        '<td align="center">:</td>'.
                        '<td>'.$telefono.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Celular</td>'.
                        '<td align="center">:</td>'.
                        '<td>'.$celular.'</td>'.
                    '</tr>'.                    
                    '<tr>'.
                        '<td>Email</td>'.
                        '<td align="center">:</td>'.
                        '<td >'.$email.'</td>'.
                    '</tr>'.
                    
                    '<tr>'.
                        '<td colspan="4"><br></td>           '.
                    '</tr>'.
                    '<tr>'.
                        '<td>Fecha Nacimiento</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$fecha_nacimiento.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Dirección</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$direccion.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Nacionalidad</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$cod_nacionalidad.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Pais</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$cod_pais.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Departamento</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$cod_departamento.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Ciudad</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$cod_ciudad.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Otra Ciudad</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$ciudadOtro.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="4"><br></td>           '.
                    '</tr>'.
                    '<tr>'.
                        '<td>Tipo Personal</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$cod_tipopersonal.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Estado Personal</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$cod_estadopersonal.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Haber Básico</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$haber_basico.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Jubilado</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>';
                        if($jubilado==0) $nombreAux0="NO";
                        else $nombreAux0="SI";                        
                        $html.=$nombreAux0.'</td>'.                        
                    '</tr>'.
                    '<tr>'.
                        '<td>Contacto</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$persona_contacto.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="4"><br></td>           '.
                    '</tr>'.
                    '<tr>'.
                        '<td>Nro. Seguro</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$nro_seguro.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Nua Cua Asignado</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$nua_cua_asignado.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Tipo AFP</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$cod_tipoafp.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Tipo Aporte AFP</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$cod_tipoaporteafp.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="4"><br></td>           '.
                    '</tr>'.                 
                    '<tr>'.
                        '<td>Personal discapacitado</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>';
                        if($discapacitado==0) $nombreAux="NO";
                        else $nombreAux="SI";                        
                        $html.=$nombreAux.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Tutor De discapacitado</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>';
                        if($tutor_discapacitado==0) $nombreAux1="NO";
                        else $nombreAux1="SI";                        
                        $html.=$nombreAux1.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>parentesco</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$parentesco.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Celular Tutor</td>'.
                        '<td align="center">:</td>'.
                        '<td colspan=2>'.$celular_tutor.'</td>'.
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
