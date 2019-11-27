<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$codSector=$_POST['cod_sector'];
$priorizada=$_POST['priorizada'];
$nombreCombo="norma";

if($priorizada==1){
	$nombreCombo="norma_priorizada";	
}

?>

<select class="selectpicker_1" name="<?=$nombreCombo?>" id="<?=$nombreCombo?>" data-style="<?=$comboColor;?>" required>
	<option disabled selected value=""></option>
	<?php
	if($priorizada==1){
		$sql="SELECT n.codigo, n.abreviatura FROM normas n, normas_priorizadas np where n.codigo=np.codigo and n.cod_sector='$codSector'";		
	}else{
		$sql="SELECT n.codigo, n.abreviatura FROM normas n where n.cod_sector='$codSector'";
	}

	//echo $sql;
	$stmt = $dbh->prepare($sql);
	$stmt->execute();
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$codigoX=$row['codigo'];
		$nombreX=$row['abreviatura'];
	?>
	<option value="<?=$codigoX;?>"><?=$nombreX;?></option>
	<?php	
	}
		?>
</select>