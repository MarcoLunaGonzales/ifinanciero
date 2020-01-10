<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'rrhh/configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();


//por is es edit
if ($codigo > 0){
    $codigo=$codigo;
    $stmt = $dbh->prepare("SELECT * FROM tipos_contrato_personal where codigo =:codigo");
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $nombre = $result['nombre'];
    $duracion_meses = $result['duracion_meses'];
    $meses_alerta = $result['meses_alerta'];
} else {
    $codigo = 0;
    $nombre = ' ';
    $duracion_meses = ' ';
    $cod_estadoreferencial = ' ';
    $created_at = ' ';
    $created_by = ' ';
    $meses_alerta = ' ';
}
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveTiposContrato;?>" method="post">
            <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?> Tipo De Contratos</h4>
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
                    <label class="col-sm-2 col-form-label">Duración De Meses</label>
                    <div class="col-sm-7">
                    <div class="form-group">
                        <input class="form-control" type="text" name="duracion_meses" id="duracion_meses" required="true" value="<?=$duracion_meses;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                    </div>
                    </div>
                </div><!--fin campo abreviatura -->
                <div class="row">
                    <label class="col-sm-2 col-form-label">Meses De Alerta</label>
                    <div class="col-sm-7">
                    <div class="form-group">
                        <input class="form-control" type="text" name="meses_alerta" id="meses_alerta" required="true" value="<?=$meses_alerta;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                    </div>
                    </div>
                </div><!--fin campo abreviatura -->
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListTiposContratos;?>" class="<?=$buttonCancel;?>">Volver</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>