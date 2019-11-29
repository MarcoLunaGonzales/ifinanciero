<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');


//ini_set("display_errors", "1");
$db = new Conexion();
$stmt = $db->prepare("SELECT * FROM tiposbienes where cod_depreciaciones = :cod_depreciaciones");
$stmt->bindParam(':cod_depreciaciones', $_POST["cod_depreciaciones"]);
$stmt->execute();
if (!$stmt->execute()) {
    echo json_encode(print_r($stmt->errorInfo()));
}
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_depreciaciones', $cod_depreciaciones);
$stmt->bindColumn('tipo_bien', $tipo_bien);
echo "<select name='cod_tiposbienes' id='cod_tiposbienes' class='selectpicker' data-style='btn btn-primary' required='true'>";
echo "<option value=''>-</option>";
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
    <option value="<?=$codigo;?>"><?=$tipo_bien;?></option>s
<?php } 
echo "</select>";

//echo "edu";
//echo json_encode($_POST["cod_depreciaciones"]);