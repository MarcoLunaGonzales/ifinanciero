<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();

if($codigo>0){
    $codigo=$codigo;
    $stmt = $dbh->prepare("SELECT * from dosificaciones_facturas where codigo =$codigo");
    $stmt->execute();
    $result = $stmt->fetch();    
    $cod_sucursal=$result['cod_sucursal'];
    $nro_autorizacion=$result['nro_autorizacion'];
    $llave_dosificacion =$result['llave_dosificacion'];
    $fecha_limite_emision=$result['fecha_limite_emision'];
}else{
    $codigo = 0;
    $cod_sucursal=null;
    $nro_autorizacion=null;
    $llave_dosificacion =null;
    $fecha_limite_emision=null;
    
}
?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveDosificacion;?>" method="post">            
            <input type="hidden" name="codigo" id="codigo" value="<?=$codigo?>">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar Dosificación</h4>
				</div>
			  </div>
			  <div class="card-body ">			
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Sucursal</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select name="cod_sucursal" id="cod_sucursal" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" required="true">
                                    <option value=""></option>

                                    <option <?=($cod_sucursal==1)?"selected":"";?> value="1">Casa Matriz</option>
                                    <!-- <?php 
                                    $queryUO = "SELECT codigo,nombre from unidades_organizacionales where cod_estado=1 order by nombre";
                                    $statementUO = $dbh->query($queryUO);
                                    while ($row = $statementUO->fetch()){ ?>
                                        <option <?=($cod_uo==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                    <?php } ?> -->
                                </select>                                
                            </div>
                        </div>
                    </div><!--fin campo nombre -->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Nro. de Autorización</label>
                      <div class="col-sm-8">
                        <div class="form-group">
                            <input type="number" name="nro_autorizacion" id="nro_autorizacion" class="form-control" value="<?=$nro_autorizacion?>" required="true">
                        </div>
                      </div>                
                    </div>
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Llave de Dosificación</label>
                      <div class="col-sm-8">
                        <div class="form-group">
                            <input type="text" name="llave_dosificacion" id="llave_dosificacion" class="form-control" value="<?=$llave_dosificacion?>" required="true">
                        </div>
                      </div>                
                    </div>
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Fecha Límite Emisión</label>
                      <div class="col-sm-8">
                        <div class="form-group">
                            <input type="date" name="fecha_limite_emision" id="fecha_limite_emision" class="form-control" value="<?=$fecha_limite_emision?>" required="true">
                        </div>
                      </div>                
                    </div>
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListDosificacion;?>" class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>