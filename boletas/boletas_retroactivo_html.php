<?php



function generarHtmlBoletaSueldosMes($cod_planilla,$cod_gestion,$cod_mes,$cod_personal){
	require_once __DIR__.'/../conexion.php';
	require_once '../assets/phpqrcode/qrlib.php';
	require_once __DIR__.'/../functions.php';
	require_once __DIR__.'/../functionsGeneral.php';
	$dbh = new Conexion();
	$sql_add="";
	if($cod_personal>0){
		$sql_add=" and p.codigo in ($cod_personal)";
	}
	set_time_limit(0);  
  $porcentaje_aport_afp=obtenerValorConfiguracionPlanillas(12);
	$porcentaje_aport_sol=obtenerValorConfiguracionPlanillas(15);
	set_time_limit(0);
  $mes=strtoupper(nombreMes($cod_mes));
  $gestion=nameGestion($cod_gestion);

  $sql="SELECT p.codigo, p.primer_nombre as nombres,CONCAT(p.paterno,' ', p.materno) as apellidos,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,pm.haber_basico_pactado,pm.haber_basico as haber_basico2,pm.bono_antiguedad,pm.bonos_otros,pm.total_ganado,pm.descuentos_otros,pm.correlativo_planilla,pm.liquido_pagable,
    pm.dias_trabajados,pm.afp_1,pm.afp_2,pp.seguro_de_salud,pp.riesgo_profesional,pp.rc_iva,pp.a_solidario_13000,pp.a_solidario_25000,pp.a_solidario_35000,pp.anticipo,p.ing_planilla
    FROM personal p
    join planillas_personal_mes pm on pm.cod_personalcargo=p.codigo
      join planillas_personal_mes_patronal pp on pp.cod_planilla=pm.cod_planilla and pp.cod_personal_cargo=pm.cod_personalcargo

    where pm.cod_planilla=$cod_planilla $sql_add 
    order by pm.correlativo_planilla";


    $stmt = $dbh->prepare($sql);
    //Ejecutamos
    $stmt->execute();
    // $result = $stmt->fetch();
	$html = '';
	$html.='<html>'.
         '<head>'.
             '<!-- CSS Files -->'.
             '<link rel="icon" type="image/png" href="../assets/img/favicon.png">'.
             '<link href="../assets/libraries/plantillaPDFFActura.css" rel="stylesheet" />'.
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
    $codigo_generado="";
    // $index_planilla=1;
	while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$cod_personal=$result['codigo'];
	//generando Clave unico 
	// $nuevo_numero=$cod_personal+$cod_planilla+$cod_mes+$cod_gestion;
	// $cantidad_digitos=strlen($nuevo_numero);
	// $numero_adicional=$nuevo_numero+100+$cantidad_digitos;
	// $numero_exa=dechex($numero_adicional);//convertimos de decimal a hexadecimal 
	$numero_exa=alghoBolPersonal($cod_personal,$cod_planilla,$cod_mes,$cod_gestion);
	// echo $exa."_";
	// echo hexdec($exa);//se convierte hexa a decimal
	$codigo_generado=$cod_personal.".".$cod_planilla.".".$cod_mes.".".$cod_gestion.".".$numero_exa;
	// $cod_personal=$result['codigo'];

	$haber_basico_dias=$result['haber_basico2'];
	$bono_antiguedad=$result['bono_antiguedad'];
	$com_ventas=0;
	$fallo_caja=0;
	$hrs_noche=0;
	$hras_domingo=0;
	$hrs_feriado=0;
	$hras_extraordianrias=0;
	$reintegro=0;
	$movilidad=0;
	$refrigerio=0;
	$obs_reintegro=0;

	$ing_planilla=$result['ing_planilla'];

	$Ap_Vejez=$result['seguro_de_salud'];
	$Riesgo_Prof=$result['riesgo_profesional'];
	$totalGanado=$result['total_ganado'];
	$ComAFP=$totalGanado*$porcentaje_aport_afp/100;
	$aposol=$totalGanado*$porcentaje_aport_sol/100;

	$aposol13=$result['a_solidario_13000'];
	$aposol25=$result['a_solidario_25000'];
	$aposol35=$result['a_solidario_35000'];

	$RC_IVA=$result['rc_iva'];
	$Anticipos=$result['anticipo'];
	$Prestamos=0;
	$Inventario=0;
	$Vencidos=0;
	$Atrasos=0;
	$Faltantes_Caja=0;
	$Otros_Descuentos=0;
	$Aporte_Sindical=0;

	$otrosBonos=$result['bonos_otros'];
	$descuentos_otros=$result['descuentos_otros'];
	$index_planilla=$result['correlativo_planilla'];
	// $otrosBonos=$bono_antiguedad-$otrosBonos;//$bono_antiguedad+ esta variable ya esta incluido en otrosBonos
	// $descuentos_otros
	
	$suma_ingresos=$haber_basico_dias+$bono_antiguedad+$otrosBonos;
	$suma_egresos=$Ap_Vejez+$Riesgo_Prof+$ComAFP+$aposol+$aposol13+$aposol25+$aposol35+$RC_IVA+$Anticipos+$descuentos_otros;

	$liquido_pagable=$suma_ingresos-$suma_egresos;
	// $liquido_pagable=$result['liquido_pagable'];
	if($cod_personal==-1000){
		require 'boletas_html_aux.php';
		$html.='<hr>';
		require 'boletas_html_aux.php';	
	}else{
		require 'boletas_html_aux.php';
		$html.='<br>';
		// require 'boletas_html_aux.php';	
	}
	// $index_planilla++;
}

	$html.='</body>'.
	'</html>';
	$stmt=null;
	$dbh=null;
	return $html;
}

?>

