<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();


//por is es edit
if ($codigo > 0){
    //EDIT GET1 no guardar, sino obtener
    $codigo=$codigo;
    $stmt = $dbh->prepare("SELECT * FROM areas_organizacion where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $cod_unidad = $result['cod_unidad'];
    $cod_area = $result['cod_area'];
    $cod_areaorganizacion_padre = $result['cod_areaorganizacion_padre'];
    $cod_estadoreferencial = $result['cod_estadoreferencial'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $modified_at = $result['modified_at'];
    $modified_by = $result['modified_by'];
} else {
  $codigo = 0;
    $cod_unidad = ' ';
    $cod_area = ' ';
    $cod_areaorganizacion_padre = ' ';
    $cod_estadoreferencial = ' ';
    $created_at = ' ';
    $created_by = ' ';
    $modified_at = ' ';
    $modified_by = ' ';
}

//COMBOS...
$queryUO = "select * from unidades_organizacionales";
$statementUO = $dbh->query($queryUO);

$queryArea = "select * from areas";
$statementArea = $dbh->query($queryArea);

$queryArea2 = "select * from areas";//tendria q cambiarlo a fetchall...
$statementArea2 = $dbh->query($queryArea2);

?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveAreas_organizacion;?>" method="post">
     
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularAreas_organizacion;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				

              <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
              <div class="row">
    <label class="col-sm-2 col-form-label">Unidad</label>
    <div class="col-sm-7">
    <div class="form-group">
        <select name="cod_unidad"  class="selectpicker " data-style="btn btn-info">
					<?php while ($row = $statementUO->fetch()) { ?>
						<option <?php if($cod_unidad == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
					</select>

      
    </div>
    </div>
</div><!--fin campo cod_unidad -->
<div class="row">
    <label class="col-sm-2 col-form-label">Area</label>
    <div class="col-sm-7">
    <div class="form-group">
        <select name="cod_area"  class="selectpicker " data-style="btn btn-info">
					<?php while ($row = $statementArea->fetch()) { ?>
						<option <?php if($cod_area == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
					</select>
    </div>
    </div>
</div><!--fin campo cod_area -->
<div class="row">
    <label class="col-sm-2 col-form-label">Area Padre</label>
    <div class="col-sm-7">
    <div class="form-group">
        <select name="cod_areaorganizacion_padre"  class="selectpicker " data-style="btn btn-info">
					<?php while ($row = $statementArea2->fetch()) { ?>
						<option <?php if($cod_areaorganizacion_padre == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
					<?php } ?>
					</select>
      
    </div>
    </div>
</div><!--fin campo cod_areaorganizacion_padre -->






			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListAreas_organizacion;?>" class="<?=$buttonCancel;?>">Cancelar</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>