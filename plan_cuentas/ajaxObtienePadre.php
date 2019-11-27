<?php

require_once '../conexion.php';
require_once '../functions.php';


$cuenta=$_GET["cuenta"];

$digitos=strlen($cuenta);

if($digitos==14){
	list($nivel1, $nivel2, $nivel3, $nivel4, $nivel5) = explode('.', $cuenta);

	//echo $nivel1." ".$nivel2." ".$nivel3." ".$nivel4." ".$nivel5;
	$cuentaPadre="";
	if($nivel5!="000"){
		$cuentaPadre=$nivel1.$nivel2.$nivel3.$nivel4."000";
		$cuentaBuscar=buscarCuentaPadre($cuentaPadre);
		if($cuentaBuscar!=""){
			echo $cuentaPadre." ".$cuentaBuscar;
		}
	}
	if($nivel5=="000" && $nivel4="00"){
		$cuentaPadre=$nivel1.$nivel2.$nivel3."00"."000";
		$cuentaBuscar=buscarCuentaPadre($cuentaPadre);
		if($cuentaBuscar!=""){
			echo $cuentaPadre." ".$cuentaBuscar;
		}	
	}
	if($nivel5=="000" && $nivel4=="00" && $nivel3=="00"){
		$cuentaPadre=$nivel1.$nivel2."00"."00"."000";
		$cuentaBuscar=buscarCuentaPadre($cuentaPadre);
		if($cuentaBuscar!=""){
			echo $cuentaPadre." ".$cuentaBuscar;
		}	
	}
	if($nivel5=="000" && $nivel4=="00" && $nivel3=="00" && $nivel2=="00"){
		$cuentaPadre=$nivel1."00"."00"."00"."000";
		$cuentaBuscar=buscarCuentaPadre($cuentaPadre);
		if($cuentaBuscar!=""){
			echo $cuentaPadre." ".$cuentaBuscar;
		}	
	}
}else{
	echo "La cuenta debe tener 10 digitos.";
}

?>