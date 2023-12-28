<?php

function generarHtmlBoletaSueldosMes($cod_planilla_personal, $cod_planilla, $cod_gestion, $mes, $cod_personal){
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
	// set_time_limit(0);
  	$mes=strtoupper($mes);
  	$gestion=nameGestion($cod_gestion);

	// Obtener el valor de GESTORA
	$fecha_gestora 	  = obtenerValorConfiguracionPlanillas(34);
	$conf_descripcion = obtenerValorConfiguracionPlanillas(35);
	$verificacion_gestora = false;
	if(strtotime(date('Y-m-d')) >= strtotime($fecha_gestora)  && !empty($fecha_gestora)){
		$verificacion_gestora = true;
	}
	
	//datos de cabecera
	$arrayOficinas=[];
	$sql="SELECT cod_uo,sucursal,direccion,nit,razon_social,nro_patronal,ciudad_pais from titulos_oficinas";
	$stmtOficinas = $dbh->prepare($sql);    
    $stmtOficinas->execute();
	while ($result = $stmtOficinas->fetch(PDO::FETCH_ASSOC)) {
		$cod_uo=$result['cod_uo'];
		$sucursal=$result['sucursal'];
		$direccion=$result['direccion'];
		$nit=$result['nit'];
		$razon_social=$result['razon_social'];
		$nro_patronal=$result['nro_patronal'];
		$ciudad_pais=$result['ciudad_pais'];
		$arrayOficinas[$cod_uo]=array($sucursal,$direccion,$nit,$razon_social,$nro_patronal,$ciudad_pais);
	}


  $sql="SELECT 
			p.codigo,
			p.primer_nombre AS nombres,
			CONCAT(p.paterno, ' ', p.materno) AS apellidos,
			(
				SELECT c.nombre
				FROM cargos c 
				WHERE c.codigo = pad.cod_cargo
			) AS cargo,
			(
				SELECT a.nombre 
				FROM areas a 
				WHERE a.codigo = pad.cod_area
			) AS area,
			pad.total_aguinaldo,
			pad.dias_trabajados,
			0 as seguro_de_salud,
			0 as riesgo_profesional,
			0 as rc_iva,
			0 as a_solidario_13000,
			0 as a_solidario_25000,
			0 as a_solidario_35000,
			0 as anticipo,
			p.ing_planilla,
			p.identificacion,
			p.cod_unidadorganizacional,
			(
				SELECT DATE_FORMAT(aux_pe.fecha, '%d-%m-%Y %H:%i:%s')
				FROM planillas_aguinaldos_email aux_pe
				LEFT JOIN planillas_aguinaldos_detalle aux_ppm ON aux_ppm.codigo = aux_pe.cod_planilla_mes
				WHERE aux_pe.cod_planilla_mes = pad.codigo 
				ORDER BY aux_pe.id ASC 
				LIMIT 1
			) AS primer_vista,
			pad.codigo AS cod_planilla_mes,
			pad.meses_trabajados,
			pad.dias_trabajados,
			p.haber_basico
		FROM personal p
		JOIN planillas_aguinaldos_detalle pad ON pad.cod_personal = p.codigo
		WHERE pad.codigo = '$cod_planilla_personal'";

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
        '$pdf->set_paper("A4", "portrait");'.
      '}'.
    '</script>';
    $codigo_generado="";
    // $index_planilla=1;
    // $urlBoletas="192.168.100.243/ifinanciero/boletas/";
    $urlBoletas=obtenerValorConfiguracion(113);
    $urlFirma="../assets/img/".obtenerValorConfiguracion(105);
    // echo $urlFirma;
	
	$detail_primer_vista 	= '';
	$detail_nombre_personal = '';
	while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
		
		$cod_unidadorganizacional=$result['cod_unidadorganizacional'];
		if($cod_unidadorganizacional==270 || $cod_unidadorganizacional==9){
			$cod_unidad_x=9;
		}elseif($cod_unidadorganizacional==271 || $cod_unidadorganizacional==10){    
			$cod_unidad_x=10;
		}else{
			$cod_unidad_x=5;
		}

		$cod_personal=$result['codigo'];
		$saldo_rciva=0;
		// $numero_exa=alghoBolPersonal($cod_personal,$cod_planilla_personal,$cod_mes,$cod_gestion);
		// $codigo_generado=$cod_personal.".".$cod_planilla_personal.".".$mes.".".$cod_gestion.".".$numero_exa;
		$codigo_generado=$cod_personal.".".$cod_planilla_personal.".".$mes.".".$cod_gestion;

		$meses_trabajados 	= $result['meses_trabajados'];
		$dias_trabajados 	= $result['dias_trabajados'];
		$tiempo_trabajo		= $meses_trabajados. ' meses '.($dias_trabajados > 0 ? (' y '.$dias_trabajados.' días') : '');
		$haber_basico 		= $result['haber_basico'];

		$total_aguinaldo=$result['total_aguinaldo'];
		$bono_antiguedad=0;
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
		$totalGanado=$result['total_aguinaldo'];
		
		$descuentoAFP=0;

		$aposol13=$result['a_solidario_13000'];
		$aposol25=$result['a_solidario_25000'];
		$aposol35=$result['a_solidario_35000'];

		$descuentoAFP=$descuentoAFP+$aposol13+$aposol25+$aposol35;


		$RC_IVA=$result['rc_iva'];
		$Anticipos=$result['anticipo'];
		$Prestamos=0;
		$Inventario=0;
		$Vencidos=0;
		$Atrasos=0;
		$Faltantes_Caja=0;
		$Otros_Descuentos=0;
		$Aporte_Sindical=0;

		$otrosBonos=0;
		$descuentos_otros=0;
		$atrasos=0;
		$index_planilla=0;

		$descuentos_otrosX=$descuentos_otros-$atrasos;
		// $otrosBonos=$bono_antiguedad-$otrosBonos;//$bono_antiguedad+ esta variable ya esta incluido en otrosBonos
		// $descuentos_otros
		
		$suma_ingresos=$total_aguinaldo+$bono_antiguedad+$otrosBonos;
		//$suma_egresos=$Ap_Vejez+$Riesgo_Prof+$ComAFP+$aposol+$aposol13+$aposol25+$aposol35+$RC_IVA+$Anticipos+$descuentos_otrosX+$atrasos;
		$suma_egresos=$descuentoAFP+$RC_IVA+$Anticipos+$descuentos_otrosX+$atrasos;

		$liquido_pagable=$totalGanado;
		// $liquido_pagable=$result['liquido_pagable'];
		
		if($cod_personal==-1000){
			// require 'boletas_html_aux.php';
			// $html.='<hr>';
			require 'boleta_html.php';	
		}else{
			// require 'boletas_html_aux.php';
			// $html.='<div class="page-break"></div>';
			require 'boleta_html.php';	
			$html.='<div class="page-break"></div>';

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

