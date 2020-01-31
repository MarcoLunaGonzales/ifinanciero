<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$stmt = $dbh->prepare("SELECT * FROM depreciaciones order by nombre");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigoX);
$stmt->bindColumn('cod_empresa', $cod_empresa);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('vida_util', $vida_util);
$stmt->bindColumn('cod_estado', $cod_estado);
//-----------------------------------------------------------------------------
$cod_depreciaciones2=0;
$tipo_bien2="";
if ($codigo > 0){
	$sql2="SELECT * FROM tiposbienes where codigo='$codigo' order by tipo_bien";
	//echo $sql2;
	$stmt2 = $dbh->prepare($sql2);
	//Ejecutamos;
	$stmt2->execute();
	$result2 = $stmt2->fetch();
	$codigo2 = $result2['codigo'];
	$cod_depreciaciones2 = $result2['cod_depreciaciones'];
	$tipo_bien2 = $result2['tipo_bien'];
} else {
    $codigo = 0;
}
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSave5;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?> <?=$moduleNameSingular5;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
			  <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Rubro</label>
				  <div class="col-sm-7">
					<div class="form-group">
					<select name="cod_depreciaciones"  class="selectpicker form-control" data-style="btn btn-info">
					<?php while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
						<option <?php if($cod_depreciaciones2 == $codigoX) echo "selected"; ?> value="<?=$codigoX;?>"><?=$nombre;?></option>
					<?php } ?>
					</select>
					</div>
				  </div>
				</div>
				

    		<div class="row">
				  <label class="col-sm-2 col-form-label">Tipo Bien</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="tipo_bien" id="tipo_bien" value="<?=$tipo_bien2;?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
					</div>
				  </div>
				</div>
          
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList5;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>