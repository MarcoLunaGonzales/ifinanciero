<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
//por is es edit
$cod_cc=$cod_cc;
$cod_tcc=$cod_tcc;
$cod_dcc=$codigo;
if ($codigo > 0){
    
    $stmt = $dbh->prepare("SELECT codigo,cod_cuenta,fecha,cod_tipodoccajachica,nro_documento,cod_personal,monto,observaciones
    from caja_chicadetalle
    where codigo =:codigo");
    
    $stmt->bindParam(':codigo',$cod_dcc);
    $stmt->execute();
    $result = $stmt->fetch();

    $cod_cuenta = $result['cod_cuenta'];
    $fecha = $result['fecha'];
    $cod_tipodoccajachica = $result['cod_tipodoccajachica'];    
    $nro_documento = $result['nro_documento'];    
    $cod_personal = $result['cod_personal'];    
    $observaciones = $result['observaciones'];    
    $monto = $result['monto'];    
    
} else {
    //para el numero correlativo
    $stmtCC = $dbh->prepare("SELECT nro_documento from caja_chicadetalle where cod_estadoreferencial=1 order by codigo desc");
    $stmtCC->execute();
    $resultCC = $stmtCC->fetch();
    $numero_caja_chica_aux = $resultCC['nro_documento'];
    if($numero_caja_chica_aux==null){
        $numero_caja_chica_aux=0;
    }


    $codigo=0;
    $cod_cuenta = 0;
    $fecha = "";
    $cod_tipodoccajachica = 0;
    $nro_documento = $numero_caja_chica_aux+1;    
    $cod_personal = 0;    
    $observaciones = " ";    
    $monto = 0;    
    $cod_estado = 1;
}
?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveDetalleCajaChica;?>" method="post">
            <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
            <input type="hidden" name="cod_cc" id="cod_cc" value="<?=$cod_cc;?>"/>
            <input type="hidden" name="cod_tcc" id="cod_tcc" value="<?=$cod_tcc;?>"/>
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar Nuevo"; else echo "Editar";?>  Detalle</h4>
				</div>
			  </div>
			  <div class="card-body ">			
                   
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Cuenta</label>
                      <div class="col-sm-8">
                        <div class="form-group">
                            <select name="cod_cuenta" id="cod_cuenta" class="selectpicker form-control" data-style="btn btn-info">
                                <option ></option>
                                <?php 
                                $querytipos_caja = "SELECT codigo,nombre from plan_cuentas where cod_estadoreferencial=1 order by nombre";
                                $stmtTcajaChica = $dbh->query($querytipos_caja);
                                while ($row = $stmtTcajaChica->fetch()){ ?>
                                    <option <?=($cod_cuenta==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                <?php } ?>
                            </select>
                        </div>
                      </div>
                    </div><!-- cuenta-->

                    <div class="row">
                        <label class="col-sm-2 col-form-label">Tipo Doc.</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <select name="tipo_documento" id="tipo_documento" class="selectpicker form-control" data-style="btn btn-info">
                                    <option ></option>
                                    <?php                                     
                                    $stmtTipoDoc = $dbh->query("SELECT codigo,nombre from tipos_doc_cajachica");
                                    while ($row = $stmtTipoDoc->fetch()){ ?>
                                        <option <?=($cod_tipodoccajachica==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select>                                  
                            </div>
                        </div>
                        <label class="col-sm-2 col-form-label">Nro. Doc.</label>
                        <div class="col-sm-4">
                        <div class="form-group">
                            <input class="form-control" type="number" name="numero" id="numero" value="<?=$nro_documento;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" readonly="readonly"/>
                        </div>
                        </div>
                    </div> <!--fin campo fecha numero-->
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Monto</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="text" step="any" name="monto" id="monto" value="<?=$monto;?>" required/>
                            </div>
                        </div>
                        <label class="col-sm-2 col-form-label">Fecha</label>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="date" name="fecha" id="fecha" value="<?=$fecha;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                            </div>
                        </div>
                    </div><!--monto inicio y reembolso-->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Personal</label>
                      <div class="col-sm-8">
                        <div class="form-group">
                            <select name="cod_personal" id="cod_personal" class="selectpicker form-control" data-style="btn btn-info">
                                <option value=""></option>
                                <?php 
                                $querypersonal = "SELECT codigo,CONCAT_WS(' ',paterno,materno,primer_nombre)AS nombre from personal where cod_estadoreferencial=1 order by nombre";
                                $stmtPersonal = $dbh->query($querypersonal);
                                while ($row = $stmtPersonal->fetch()){ ?>
                                    <option <?=($cod_personal==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=strtoupper($row["nombre"]);?></option>
                                <?php } ?>
                            </select>
                        </div>
                      </div>
                    </div>


                    <div class="row">
                        <label class="col-sm-2 col-form-label">Descripción</label>
                        <div class="col-sm-7">
                        <div class="form-group">
                            <textarea class="form-control rounded-0" name="observaciones" id="observaciones" rows="3" onkeyup="javascript:this.value=this.value.toUpperCase();"><?=$observaciones;?></textarea>

                            <!-- <input class="form-control" type="text" name="observaciones" id="observaciones" required="true" value="<?=$observaciones;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/> -->
                        </div>
                        </div>
                    </div><!--fin campo nombre -->              
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListDetalleCajaChica;?>&codigo=<?=$cod_cc;?>&cod_tcc=<?=$cod_tcc?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>
