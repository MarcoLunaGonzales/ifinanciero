<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

echo $codigo;

if ($codigo > 0){
    //$codigo=$codigo;
    $stmt = $dbh->prepare("SELECT * FROM af_proveedores where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();

    $codigo = $result['codigo'];
    $nombre = $result['nombre'];
    $direccion = $result['direccion'];
    $telefono = $result['telefono'];
    $email = $result['email'];
    $personacontacto = $result['personacontacto'];
    
} else {
    $codigo = 0;
    $nombre = ' ';
    $direccion = ' ';
    $telefono = ' ';
    $email = ' ';
    $personacontacto = ' ';
}
//html
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveProv;?>" method="post">

			<div class="card ">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?> <?=$moduleNameSingularProveedores;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
                <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>         
              <div class="row">
			    <label class="col-sm-2 col-form-label">Proveedor</label>
    		  <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="nombre" id="nombre" required="true" value="<?=$nombre;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo nombre -->
<div class="row">
    <label class="col-sm-2 col-form-label">Direccion</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="direccion" id="direccion" required="true" value="<?=$direccion;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo direccion -->
<div class="row">
    <label class="col-sm-2 col-form-label">Telefono</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="telefono" id="telefono" required="true" value="<?=$telefono;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo telefono -->
<div class="row">
    <label class="col-sm-2 col-form-label">Email</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="email" name="email" id="email" required="true" value="<?=$email;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo email -->
<div class="row">
    <label class="col-sm-2 col-form-label">Persona Contacto</label>
    <div class="col-sm-7">
    <div class="form-group">
        <input class="form-control" type="text" name="personacontacto" id="personacontacto" required="true" value="<?=$personacontacto;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
    </div>
    </div>
</div><!--fin campo personacontacto -->





      
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListProv;?>" class="<?=$buttonCancel;?>">Cancelar</a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>















