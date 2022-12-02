<?php

	if (isset($_GET['ws'])) {
		
		$string_codigo=$_GET['ws'];
		//echo $string_codigo."****";
		$array_codigo=explode('.', $string_codigo);
		$cod_personal=$array_codigo[0];
		$cod_planilla=$array_codigo[1];
		$cod_mes=$array_codigo[2];
		$cod_gestion=$array_codigo[3];
		$numero_exa=hexdec($array_codigo[4]);//llegará en exadecimal
		//se convierte hexa a decimal
		//generando Clave unico 
		$nuevo_numero=$cod_personal+$cod_planilla+$cod_mes+$cod_gestion;
		$cantidad_digitos=strlen($nuevo_numero);
		$numero_adicional=$nuevo_numero+100+$cantidad_digitos;
		// $numero_exa=dechex($numero_adicional);//convertimos de decimal a hexadecimal 

		if($numero_adicional==$numero_exa){
			echo "BOLETA VALIDA";
		}else{
			echo "DATOS DE BOLETA INCORRETOS";
		}

	}else{
		echo "ACCESO DENEGADO..!!!";
	}
	


?>