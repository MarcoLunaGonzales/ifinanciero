<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();

//RECIBIMOS LA VARIABLE DE LA CUENTA 
$codigoCuentaPadre=$codigo;

$numeroCuentaPadre=obtieneNumeroCuenta($codigoCuentaPadre);
$nombreCuentaPadre=nameCuenta($codigoCuentaPadre);


require_once 'configModule.php';

$sql="SELECT c.cod_tipoestadocuenta from configuracion_estadocuentas c where c.cod_plancuenta='$codigoCuentaPadre'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$codTipoProveedorCliente=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codTipoProveedorCliente=$row['cod_tipoestadocuenta'];
}

if($codTipoProveedorCliente==0){
	$sql1="SELECT ca.cod_tipoauxiliar from cuentas_auxiliares ca where ca.cod_cuenta='$codigoCuentaPadre' limit 0,1";
	$stmt1 = $dbh->prepare($sql1);
	$stmt1->execute();
	while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
	  $codTipoProveedorCliente=$row['cod_tipoauxiliar'];
	}
}


if($codTipoProveedorCliente==1){
	$sqlProveedor="SELECT codigo, nombre from af_proveedores WHERE cod_estado=1";
	$stmtProveedor = $dbh->prepare($sqlProveedor);
	$stmtProveedor->execute();
	while ($rowProveedor = $stmtProveedor->fetch(PDO::FETCH_ASSOC)) {
	  $codProveedor=$rowProveedor['codigo'];
	  $nombreProveedor=$rowProveedor['nombre'];
	  $sqlCuenta="SELECT count(*)as contador from cuentas_auxiliares c where c.cod_cuenta='$codigoCuentaPadre' and c.cod_proveedorcliente='$codProveedor'";
	  $stmtCuenta=$dbh->prepare($sqlCuenta);
	  $stmtCuenta->execute();
	  $cuentaRegistros=0;
	  while($rowCuenta = $stmtCuenta->fetch(PDO::FETCH_ASSOC)){
	  	$cuentaRegistros=$rowCuenta['contador'];
	  }
  	  //echo $codProveedor." ".$nombreProveedor." ".$cuentaRegistros."<br>";
		$flagSuccessDetalle=TRUE;
		if($cuentaRegistros==0){
			$stmtInsert = $dbh->prepare("INSERT INTO cuentas_auxiliares (nombre, cod_estadoreferencial, cod_cuenta, cod_tipoauxiliar, cod_proveedorcliente) VALUES ('$nombreProveedor','1','$codigoCuentaPadre','1','$codProveedor')");
			$flagSuccessDetalle=$stmtInsert->execute();
		}
	}
	showAlertSuccessError($flagSuccessDetalle,$urlList2);
}elseif($codTipoProveedorCliente==2){
	$sqlProveedor="SELECT codigo, nombre from clientes where cod_estadoreferencial=1";
	$stmtProveedor = $dbh->prepare($sqlProveedor);
	$stmtProveedor->execute();
	while ($rowProveedor = $stmtProveedor->fetch(PDO::FETCH_ASSOC)) {
	  $codProveedor=$rowProveedor['codigo'];
	  $nombreProveedor=$rowProveedor['nombre'];
	  $sqlCuenta="SELECT count(*)as contador from cuentas_auxiliares c where c.cod_cuenta='$codigoCuentaPadre' and c.cod_proveedorcliente='$codProveedor'";
	  $stmtCuenta=$dbh->prepare($sqlCuenta);
	  $stmtCuenta->execute();
	  $cuentaRegistros=0;
	  while($rowCuenta = $stmtCuenta->fetch(PDO::FETCH_ASSOC)){
	  	$cuentaRegistros=$rowCuenta['contador'];
	  }
  	  //echo $codProveedor." ".$nombreProveedor." ".$cuentaRegistros."<br>";
		$flagSuccessDetalle=TRUE;
		if($cuentaRegistros==0){
			$stmtInsert = $dbh->prepare("INSERT INTO cuentas_auxiliares (nombre, cod_estadoreferencial, cod_cuenta, cod_tipoauxiliar, cod_proveedorcliente) VALUES ('$nombreProveedor','1','$codigoCuentaPadre','2','$codProveedor')");
			$flagSuccessDetalle=$stmtInsert->execute();
		}
	}	
	showAlertSuccessError(TRUE,$urlList2);
}elseif ($codTipoProveedorCliente==0) {
	//echo "la cuenta no esta asociada a un estado de cuentas.";
	showAlertSuccessError(TRUE,$urlList2);
}



//$flagSuccess=$stmt->execute();
//showAlertSuccessError($flagSuccess,$urlList);

?>
