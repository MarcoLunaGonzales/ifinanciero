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
    <label class="col-sm-2 col-form-label">Cuenta Contable Relacionada</label>
    <div class="col-sm-7">
    <div class="form-group">
        <select name="cod_cuentacontable" id="cod_cuentacontable" class="selectpicker form-control" data-style="btn btn-info">
          <?php  while ($fila = $statement->fetch()){ ?>
            <option <?php if ($fila['codigo']==$cod_cuentacontable){echo "selected";}?> value="<?=$fila['codigo'];?>"><?=$fila['codigocuenta'];?> - <?=$fila['cuentacontable'];?></option>
          <?php } ?>
        </select>
    </div>
    </div>
  </div><!--fin campo nombre -->

<div class="row">
    <label class="col-sm-2 col-form-label">Nombre Rubro</label>
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
    <label class="col-sm-2 col-form-label">Vida Útil (Meses)</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control"  type="number" step="1" name="vida_util" id="vida_util" required="true" value="<?=$vida_util;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo vida_util -->


        </div>
        <div class="card-footer ml-auto mr-auto">
        <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
        <a href="<?=$urlList4;?>" class="<?=$buttonCancel;?>">Volver</a>
        </div>
      </div>
      </form>
    </div>
  
  </div>
</div>