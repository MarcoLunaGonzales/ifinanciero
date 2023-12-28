<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';


//require_once 'configModule.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
        (select taafp.nombre from tipos_aporteafp taafp where taafp.codigo=cod_tipoaporteafp) as cod_tipoaporteafpX,
        (select b.nombre from bancos b where b.codigo=cod_banco) as banco_personal
    from personal
    WHERE codigo=:codigo");

    $stmtPersonal->bindParam(':codigo',$codigo);
    $stmtPersonal->execute();
    $result = $stmtPersonal->fetch();

    // $codigo = $result['codigo'];

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
    $ingreso_contrato = $result['ing_contr']; 

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
    $cuenta_bancaria = $result['cuenta_bancaria'];
    $banco_personal= $result['banco_personal'];
    $cod_dependiente_rciva=$result['codigo_dependiente'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $nro_casillero = $result['nro_casillero'];
    // $turno = $result['turno'];
    // $tipo_trabajo = $result['tipo_trabajo'];
    // $turno=obtenerNombreTurno($turno);
    // $tipo_trabajo=obtenerNombreTipoTrabajo($tipo_trabajo);
    //====================================
    //personal discapacitado
    $stmtDiscapacitado = $dbh->prepare("SELECT tipo_persona_discapacitado FROM personal_discapacitado where codigo =:codigo");
    $stmtDiscapacitado->bindParam(':codigo',$codigo);
    $stmtDiscapacitado->execute();
    $resultDiscapacitado = $stmtDiscapacitado->fetch();
    // var_dump($resultDiscapacitado);
    // if(count($resultDiscapacitado)>0){
    if(!empty($resultDiscapacitado)){
        $discapacitado = $resultDiscapacitado['tipo_persona_discapacitado'];
        $tutor_discapacitado = $resultDiscapacitado['tipo_persona_discapacitado'];
        $celular_tutor = "";
        $parentesco = "";
    }else{
        $discapacitado = '';
        $tutor_discapacitado = '';
        $celular_tutor = '';
        $parentesco = '';
    }


        //==================================================================================================================
    //imagen
    $stmtIM = $dbh->prepare("SELECT imagen FROM personalimagen  where codigo =:codigo");
    $stmtIM->bindParam(':codigo',$codigo);
    $stmtIM->execute();
    $resultIM = $stmtIM->fetch();
    if (isset($resultIM['imagen'])) {
        $imagen = $resultIM['imagen'];        
    }else{
        $imagen = "";
    }    
    
    


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
            '<table border="1" align="center" style="width: 100%;border-collapse: collapse;">'.

                '<tbody style=" font-family: Times New Roman;
                                font-size: 11px;
                                    ">';

                    $html.='<tr>'.
                        '<td colspan="2" align="center">'.                        
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
                        
                        '<td>'.$codigo.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Tipo de Identificación (CI, PAS, CEXT)</td>'.
                        
                        '<td>'.$cod_tipoIdentificacion.' '.$tipo_identificacionOtro.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Identificación</td>'.
                        
                        '<td>'.$identificacion.' - '.(empty($cod_lugar_emision)?'':obtenerlugarEmision($cod_lugar_emision,1)).' '.$lugar_emisionOtro.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Grado Académico</td>'.
                        
                        '<td>'.$grado_academico.'</td>'.
                    '</tr>'.                    
                    '<tr>'.
                        '<td>Telefono</td>'.
                        
                        '<td>'.$telefono.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Celular</td>'.
                        
                        '<td>'.$celular.'</td>'.
                    '</tr>'.                    
                    '<tr>'.
                        '<td>Email</td>'.
                        
                        '<td >'.$email.'</td>'.
                    '</tr>'.                    
                    '<tr>'.
                        '<td colspan="3"><br></td>           '.
                    '</tr>'.
                    '<tr>'.
                        '<td>Fecha Nacimiento</td>'.
                        
                        '<td colspan=2>'.date("d/m/Y",strtotime($fecha_nacimiento)).'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Dirección</td>'.
                        
                        '<td colspan=2>'.$direccion.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Nacionalidad</td>'.
                        
                        '<td colspan=2>'.(empty($cod_nacionalidad)?'':obtenerNombreNacionalidadPersona($cod_nacionalidad,1)).'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Pais</td>'.
                        
                        '<td colspan=2>'.(empty($cod_pais)?'':obtenerNombreNacionalidadPersona($cod_pais,2)).'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Departamento</td>'.
                        
                        '<td colspan=2>'.(empty($cod_departamento)?'':obtenerlugarEmision($cod_departamento,2)).'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Ciudad</td>'.
                        
                        '<td colspan=2>'.(empty($cod_ciudad)?'':obtenerNombreCiudadPersona($cod_ciudad)).'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Otra Ciudad</td>'.
                        
                        '<td colspan=2>'.$ciudadOtro.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="3"><br></td>           '.
                    '</tr>'.
                    '<tr>'.
                        '<td>Ingreso Contrato</td>'.
                        
                        '<td colspan=2>'.$ingreso_contrato.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Ingreso Planilla</td>'.
                        
                        '<td colspan=2>'.$ingreso_contrato.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Tipo Personal</td>'.
                        
                        '<td colspan=2>'.$cod_tipopersonal.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Estado Personal</td>'.
                        
                        '<td colspan=2>'.$cod_estadopersonal.'</td>'.
                    '</tr>'.
                    
                    
                    '<tr>'.
                        '<td>Haber Básico</td>'.
                        
                        '<td colspan=2>'.formatNumberDec($haber_basico).'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Jubilado</td>'.
                        
                        '<td colspan=2>';
                        if($jubilado==0) $nombreAux0="NO";
                        else $nombreAux0="SI";                        
                        $html.=$nombreAux0.'</td>'.                        
                    '</tr>'.
                    '<tr>'.
                        '<td>Contacto</td>'.
                        
                        '<td colspan=2>'.$persona_contacto.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Banco</td>'.
                        
                        '<td colspan=2>'.$banco_personal.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Cuenta Bancaria</td>'.
                        '<td colspan=2>'.$cuenta_bancaria.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Codigo Dependiente RC-IVA</td>'.
                        '<td colspan=2>'.$cod_dependiente_rciva.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Nro. de Casillero</td>'.
                        
                        '<td colspan=2>'.$nro_casillero.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="3"><br></td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Nro. Seguro</td>'.
                        
                        '<td colspan=2>'.$nro_seguro.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Nua Cua Asignado</td>'.
                        
                        '<td colspan=2>'.$nua_cua_asignado.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Tipo AFP</td>'.
                        
                        '<td colspan=2>'.$cod_tipoafp.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Tipo Aporte AFP</td>'.
                        
                        '<td colspan=2>'.$cod_tipoaporteafp.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td colspan="3"><br></td>           '.
                    '</tr>'.                 
                    '<tr>'.
                        '<td>Personal discapacitado</td>'.
                        
                        '<td colspan=2>';
                        if($discapacitado<>1) $nombreAux="NO";
                        else $nombreAux="SI";                        
                        $html.=$nombreAux.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Tutor De discapacitado</td>'.
                        
                        '<td colspan=2>';
                        if($tutor_discapacitado<>2) $nombreAux1="NO";
                        else $nombreAux1="SI";                        
                        $html.=$nombreAux1.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>parentesco</td>'.
                        
                        '<td colspan=2>'.$parentesco.'</td>'.
                    '</tr>'.
                    '<tr>'.
                        '<td>Celular Tutor</td>'.
                        
                        '<td colspan=2>'.$celular_tutor.'</td>'.
                    '</tr>'.
                '</tbody>'.            
            '</table>'.
            '</header>'.
        '</body>'.
      '</html>';           
//echo $html;
 descargarPDF("COBOFAR PERSONAL ",$html);

?>

<?php 
} catch(PDOException $ex){
    echo "Un error ocurrio".$ex->getMessage();
}
?>
