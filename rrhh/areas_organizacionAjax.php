<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');         //hace q de error en html

//ini_set("display_errors", "1");
$db = new Conexion();
$stmt = $db->prepare("select a.*, ar.nombre as xarea from areas_organizacion a, areas ar 
where a.cod_area = ar.codigo and a.cod_unidad = :cod_unidad");
$stmt->bindParam(':cod_unidad', $_POST["cod_unidadorganizacional"]);
$stmt->execute();
if (!$stmt->execute()) {
    echo json_encode(print_r($stmt->errorInfo()));
}
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('xarea', $xarea);
//$stmt->bindColumn('tipo_bien', $tipo_bien);
echo "<select name='cod_area' id='cod_area' class='selectpicker' data-style='btn btn-info'>";
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
    <option value="<?=$codigo;?>"><?=$xarea;?></option>
<?php } 
echo "</select>";