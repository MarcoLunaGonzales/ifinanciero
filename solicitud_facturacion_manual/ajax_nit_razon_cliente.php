<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$cod_cliente=$_GET['cod_cliente'];

$query="SELECT * FROM clientes where codigo=$cod_cliente ";
$stmt = $dbh->prepare($query);
$stmt->execute();
$respuesta=$stmt->fetch();

$nit=$respuesta['identificacion'];
$razon_social=$respuesta['nombre'];
$descuento=$respuesta['descuento'];
$correo_contacto=obtenerCorreosCliente($cod_cliente);
$correo_contacto=trim($correo_contacto,",");
$codigo_identificacion=null;
$complemento=null;
?> 
<div class="row">
	<label class="col-sm-2 col-form-label">Razón Social</label>
	<div class="col-sm-4">
	    <div class="form-group">                                    
	        <input class="form-control" type="text" name="razon_social" id="razon_social" required="true" value="<?=$razon_social;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>                                        
	    </div>
	</div>
	<!-- <label class="col-sm-1 col-form-label">Nit</label>
	<div class="col-sm-4">
	    <div class="form-group">                                        
	            <input class="form-control" type="number" name="nit" id="nit" required="true" value="<?=$nit;?>" required="true"/>
	    </div>
	</div> -->

	<div class="col-sm-2">
        <select class="selectpicker form-control form-control-sm" name="tipo_documento" id="tipo_documento" data-style="btn btn-danger" data-show-subtext="true" title="Tipo de documento" onChange='mostrarComplemento();' required="true">
        <?php
        $sql2="SELECT codigo,nombre from siat_tipos_documentoidentidad where cod_estadoreferencial=1";
        $stmtTipoIdentificacion = $dbh->prepare($sql2);
        $stmtTipoIdentificacion->execute();
        while ($rowTipoIden = $stmtTipoIdentificacion->fetch(PDO::FETCH_ASSOC)) {
            $codigo_identificacionx=$rowTipoIden['codigo'];    
            $nombre_identificacionx=$rowTipoIden['nombre'];
            ?><option <?=($codigo_identificacion==$codigo_identificacionx)?"selected":"";?> value="<?=$codigo_identificacionx?>" class="text-right"><?=$nombre_identificacionx?></option>
           <?php 
        } ?> 
        </select>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <input class="form-control" type="text" name="nit" id="nit" required="true" value="<?=$nit;?>"/>
        </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
                <input class="form-control" type='hidden' name="complemento" id="complemento" placeholder="Complemento" value="<?=$complemento;?>" style="position:absolute;width:100px !important;background:#D2FFE8;" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
        </div>
    </div>

	<input class="form-control" type="hidden" name="descuento" id="descuento" value="<?=$descuento;?>" />
</div>

<!--div class="row">
    <label class="col-sm-2 col-form-label">Correo De Contacto <br>Para Envío De Factura.</label>
    <div class="col-sm-10">
        <div class="form-group">
            <!-- <input class="form-control" type="email" name="correo_contacto" id="correo_contacto" value="<?=$correo_contacto;?>" required /> -->
            <!--input type="text" name="correo_contacto" id="correo_contacto" value="<?=$correo_contacto;?>" class="form-control tagsinput" data-role="tagsinput" data-color="info"  > 
        </div>
    </div>
</div-->

