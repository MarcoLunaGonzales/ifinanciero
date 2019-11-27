<?php

header("Content-Type: text/html; charset=UTF-8");

require("../../conexion.inc");

$termino = $_GET['term'];

mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
$sql_productos = mysql_query("SELECT b.codigo_material, b.descripcion_material from material_apoyo b where b.descripcion_material like '%$termino%' order by 2 ASC ");

$count = 0;

while ($row = mysql_fetch_array($sql_productos)) {
    $data[$count] = array("id" => $row[0], "label" => $row[1] . "@" . $row[0], "value" => $row[1] . "@" . $row[0]);
    $count++;
}

echo json_encode($data);
?>
