<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'activosFijos/configModule.php';

$dbh = new Conexion();
$query = "select * from unidades_organizacionales";
$statementCombo1 = $dbh->query($query);
//-----------------------------------------------------------------------------
$query2 ="select * from areas";
$statementCombo2 = $dbh->query($query2);

if ($codigo > 0){
	$stmt3 = $dbh->prepare("SELECT * FROM ubicaciones where codigo =:codigo");
	//Ejecutamos;
	$stmt3->bindParam(':codigo',$codigo);
	$stmt3->execute();
	$result3 = $stmt3->fetch();
	$codigo = $result3['codigo'];
	$cod_unidades_organizacionales = $result3['cod_unidades_organizacionales'];
	$edificio = $result3['edificio'];
	$oficina = $result3['oficina'];
	$cod_estado = $result3['cod_estado'];
	$created_at = $result3['created_at'];
	$created_by = $result3['created_by'];
	$modified_at = $result3['modified_at'];
	$modified_by = $result3['modified_by'];
	$cod_areas = $result3['cod_areas'];
} else {
    $codigo = 0;
    $cod_unidades_organizacionales=0;
	$edificio = "";
	$oficina = "";
}
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSave2;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?> <?=$moduleNameSingular2;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
			  <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Oficina</label>
				  <div class="col-sm-7">
					<div class="form-group">
					<select name="cod_unidades_organizacionales" class="selectpicker " data-style="btn btn-info">
					<?php while ($row = $statementCombo1->fetch()){ ?>
						<option <?php if($cod_unidades_organizacionales == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
					</select>
					</div>
				  </div>
				</div>

				<!--div class="row">
				  <label class="col-sm-2 col-form-label">Areas</label>
				  <div class="col-sm-7">
					<div class="form-group">
					<select name="cod_areas"  class="selectpicker " data-style="btn btn-info">
					<?php while ($row = $statementCombo2->fetch()) { ?>
						<option <?php if($cod_areas == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
					</select>
					</div>
				  </div>
				</div-->

    		<div class="row">
				  <label class="col-sm-2 col-form-label">Edificio</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" value="<?=$edificio;?>" type="text" name="edificio" id="edificio" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
					</div>
				  </div>
				</div>
                              <div class="row">
				  <label class="col-sm-2 col-form-label">Dirección de Oficina</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" value="<?=$oficina;?>" type="text" name="oficina" id="oficina" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
					</div>
				  </div>
				</div>
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList2;?>" class="<?=$buttonCancel;?>">Cancelar</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>