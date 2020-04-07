<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
//por is es edit
if ($codigo > 0){
    $codigo=$codigo;
    $stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura,cod_tipo_cargo FROM cargos where codigo =:codigo and cod_estadoreferencial=1");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $nombre = $result['nombre'];
    $abreviatura = $result['abreviatura'];    
    $cod_tipo_cargo = $result['cod_tipo_cargo'];
} else {
    $codigo = 0;
    $nombre = '';
    $abreviatura = '';
    $cod_estadoreferencial = '';
    $cod_tipo_cargo =0;
}
$sqlTiposCargos="SELECT codigo,nombre from tipos_cargos_personal where cod_estadoreferencial=1";
$stmtTCargos=$dbh->query($sqlTiposCargos);


?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveCargos;?>" method="post">
      <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularCargo;?></h4>
				</div>
			  </div>
			  <div class="card-body ">			
                    <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
                  <div class="row">
                    <label class="col-sm-2 col-form-label">Nombre</label>
                    <div class="col-sm-7">
                    <div class="form-group">
                        <input class="form-control" type="text" name="nombre" id="nombre" required="true" value="<?=$nombre;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                    </div>
                    </div>
                </div><!--fin campo nombre -->
                <div class="row">
                    <label class="col-sm-2 col-form-label">Abreviatura</label>
                    <div class="col-sm-7">
                    <div class="form-group">
                        <input class="form-control" type="text" name="abreviatura" id="abreviatura" required="true" value="<?=$abreviatura;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                    </div>
                    </div>
                </div><!--fin campo abreviatura -->
                <div class="row">
                    <label class="col-sm-2 col-form-label">Tipo De Cargo</label>
                    <div class="col-sm-7">
                    <div class="form-group">
                        <select name="cod_tipo_cargo" id="cod_tipo_cargo" data-style="btn btn-info" required onChange="ajaxPersonal_area_distribucionE(this);" class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
                            <?php while ($row = $stmtTCargos->fetch()) { ?>
                                <option <?php if($cod_tipo_cargo == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                            <?php } ?>
                        </select>
                    </div>
                    </div>
                </div><!--fin campo abreviatura -->

			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListCargos;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>