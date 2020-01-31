<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();

$statement =  $dbh->query("SELECT * FROM v_af_cuentacontablepararubros");


//por is es edit
if ($codigo > 0){
  $codigo=$codigo;
  $stmt = $dbh->prepare("SELECT * FROM depreciaciones where codigo=:codigo");
  $stmt->bindParam(':codigo', $codigo);
  $stmt->execute();
  $result = $stmt->fetch();
  $codigo = $result['codigo'];
  $cod_empresa = $result['cod_empresa'];
  $nombre = $result['nombre'];
  $abreviatura = $result['abreviatura'];
  $vida_util = $result['vida_util'];
  $cod_estado = $result['cod_estado'];
  $cod_cuentacontable = $result['cod_cuentacontable'];
} else {
  $codigo = 0;
  $cod_empresa = '';
  $nombre = '';
  $abreviatura = '';
  $vida_util = '';
  $cod_estado = '';
  $cod_cuentacontable = '';
}
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSave4;?>" method="post">
      <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?> <?=$moduleNameSingular4;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				


<div class="row">
            <label class="col-sm-2 col-form-label">Oficina</label>
            <div class="col-sm-7">
              <div class="form-group">
                <select name="cod_uo" id="cod_uo" class="selectpicker" data-style="btn btn-info" onChange="ajaxOficinaPersonal(this);">
                    <option value=""></option>
                    <?php 
                    $queryUO = "SELECT codigo,nombre from unidades_organizacionales where cod_estado=1 order by nombre";
                    $statementUO = $dbh->query($queryUO);
                    while ($row = $statementUO->fetch()){ ?>
                        <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                    <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label">Personal</label>
              <div class="col-sm-7">
                <div class="form-group" >
                    <div id="div_contenedor_personal">
                        <select class="selectpicker" data-style="btn btn-info">
                          <option value=""></option>
                        </select>
                    </div>                    
                </div>
              </div>
          </div>


			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList4;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>