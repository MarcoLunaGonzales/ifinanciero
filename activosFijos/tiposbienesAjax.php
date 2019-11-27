<?php
require_once '../conexion.php';
require_once 'configModule.php';

header('Content-Type: application/json');


//ini_set("display_errors", "1");
$db = new Conexion();
$stmt = $db->prepare("SELECT * FROM tiposbienes where cod_depreciaciones = :cod_depreciaciones");
$stmt->bindParam(':cod_depreciaciones', $_POST["cod_depreciaciones"]);
$stmt->execute();
if (!$stmt->execute()) {
    echo json_encode(print_r($stmt->errorInfo()));
}
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
//$json = json_encode($results);
echo json_encode($results);

//echo "edu";
//echo json_encode($_POST["cod_depreciaciones"]);