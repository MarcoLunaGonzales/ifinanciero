
<?php
	require_once '../conexion.php';
    $dbh = new Conexion();
    // Preparamos
    $stmt = $dbh->prepare("SELECT codigo,activo,cod_responsables_responsable FROM activosfijos where cod_estadoactivofijo=1 and codigo in (1,2);");
    $stmt->execute();
    $json=array();
    $row  = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //$json['activo'][]=$row;
    
	echo json_encode($row);




?>
