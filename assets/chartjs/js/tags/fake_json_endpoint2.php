<?php

//error_reporting(0);
header("Content-Type: text/html; charset=UTF-8");
require("../../conexion.inc");
//echo $_GET['term'];
$termino = $_GET['term'];
mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
$sql_productos = mysql_query("select b.codigo, CONCAT(b.descripcion,' ',b.presentacion) from muestras_medicas b where b.descripcion like '%$termino%' or b.presentacion like '%$termino%' order by 2 ASC ");
//echo "select b.codigo, CONCAT(b.descripcion,' ',b.presentacion) from muestras_medicas b where b.descripcion like '%$termino%' or b.presentacion like '%$termino%' order by 2 ASC ";
$count = 0;
while ($row = mysql_fetch_array($sql_productos)) {
//    $output .=$row[0] . "," . $row[1] . ",";
    $data[$count] = array("id" => $row[0], "label" => $row[1] . "@" . $row[0], "value" => $row[1] . "@" . $row[0]);
    $count++;
}
//print_r($data);
echo json_encode($data);
?>
