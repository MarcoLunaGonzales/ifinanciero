<?php

require_once '../conexion.php';
require_once '../styles.php';
$dbh = new Conexion();
$sqlBusqueda="SELECT p.codigo, p.numero, p.nombre from plan_cuentas p where p.nivel=5 ";
$sqlBusqueda.=" order by p.numero";

//echo $sqlBusqueda;

$stmtCuentas = $dbh->prepare($sqlBusqueda);
$stmtCuentas->execute();

// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
	0 =>'numero', 
	1 => 'nombre',
	2=>'nombre'
);

  // when there is no search parameter then total number rows = total number filtered rows.
$totalData=0;
while ($rowData = $stmtCuentas->fetch(PDO::FETCH_ASSOC)) {
	$totalData++;
}
$totalFiltered = $totalData;


if( !empty($requestData['search']['value']) ) {
	// if there is a search parameter
	$sql = "SELECT p.codigo, p.numero, p.nombre ";
	$sql.=" from plan_cuentas p where p.nivel=5 and (";
	$sql.="  p.numero LIKE '".$requestData['search']['value']."%' ";    // $requestData['search']['value'] contains search parameter
	$sql.=" OR p.nombre LIKE '".$requestData['search']['value']."%' )";

	$stmtCuentas = $dbh->prepare($sql);
    $stmtCuentas->execute();
// when there is a search parameter then we have to modify total number filtered rows as per search result without limit in the query 
    while ($rowData = $stmtCuentas->fetch(PDO::FETCH_ASSOC)) {
	   $totalData++;
    }
   $totalFiltered = $totalData;

	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   "; // $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc , $requestData['start'] contains start row number ,$requestData['length'] contains limit length.
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	
} else {	

	$sql = "SELECT p.codigo, p.numero, p.nombre from plan_cuentas p where p.nivel=5 ";
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
}

$data = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$nestedData=array(); 
	
	
	$codigoCuenta=$row['codigo'];
	$numeroCuenta=trim($row['numero']);
    $nombreCuenta=trim($row['nombre']);

			$sqlCuentasAux="SELECT codigo, nombre FROM cuentas_auxiliares where cod_cuenta='$codigoCuenta' order by 2";
			$stmtAux = $dbh->prepare($sqlCuentasAux);
			$stmtAux->execute();
			$stmtAux->bindColumn('codigo', $codigoCuentaAux);
			$stmtAux->bindColumn('nombre', $nombreCuentaAux);
			$txtAuxiliarCuentas="<table class='table table-condensed'>";
			while ($rowAux = $stmtAux->fetch(PDO::FETCH_ASSOC)) {
				$txtAuxiliarCuentas.="<tr>
				<td class='text-left small'>$codigoCuentaAux</td>
				<td class='text-left small'><a href='javascript:setBusquedaCuenta(\"$codigoCuenta\",\"$numeroCuenta\",\"$nombreCuenta\",\"$codigoCuentaAux\",\"$nombreCuentaAux\");'>$nombreCuentaAux</a></td>
				</tr>";
			}  	
			$txtAuxiliarCuentas.="</table>";

		$nestedData[] = "<div class='text-left'>".$numeroCuenta."</div>";
	    $nestedData[] = "<div class='text-left'><a href='javascript:setBusquedaCuenta(\"$codigoCuenta\",\"$numeroCuenta\",\"$nombreCuenta\",\"0\",\"\");'>$nombreCuenta</a></div>";
	    $nestedData[] = $txtAuxiliarCuentas;
		$data[] = $nestedData;
}


$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format

?>
