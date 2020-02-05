<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
//por is es edit
if ($codigo > 0){
    $codigo=$codigo;
    $stmt = $dbh->prepare("SELECT * from tipos_caja_chica where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $cod_personal = $result['cod_personal'];
    $cod_uo = $result['cod_uo'];
    $cod_area = $result['cod_area'];
    $nombre = $result['nombre'];    
} else {
    $codigo = 0;
    $cod_uo=0;
    $cod_area =0;
    $nombre = '';
    $cod_personal=0;
    $cod_estadoreferencial = 1;
}



?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSaveTiposCajaChica;?>" method="post">
            <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularTiposCajaChica;?></h4>
				</div>
			  </div>
			  <div class="card-body ">			
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Nombre</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <input class="form-control" type="text" name="nombre" id="nombre" required="true" value="<?=$nombre;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                            </div>
                        </div>
                    </div><!--fin campo nombre -->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Oficina</label>
                      <div class="col-sm-4">
                        <div class="form-group">
                            <select name="cod_uo" id="cod_uo" class="selectpicker" data-style="btn btn-info" onChange="ajaxUOArea_personal_tipocajachica(this);">
                                <option value=""></option>
                                <?php 
                                $queryUO = "SELECT codigo,nombre from unidades_organizacionales where cod_estado=1 order by nombre";
                                $statementUO = $dbh->query($queryUO);
                                while ($row = $statementUO->fetch()){ ?>
                                    <option <?=($cod_uo==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                <?php } ?>
                            </select>
                        </div>
                      </div>
                   
                      <label class="col-sm-2 col-form-label">Area</label>
                      <div class="col-sm-4">
                        <div class="form-group" >
                            <div id="div_contenedor_area_tcc">
                                <select name="cod_area" id="cod_area" class="selectpicker" data-style="btn btn-info" >
                                    <option value=""></option>
                                    <?php 
                                    $queryArea = "SELECT codigo,nombre FROM  areas WHERE cod_estado=1 order by nombre";
                                    $statementArea = $dbh->query($queryArea);
                                    while ($row = $statementArea->fetch()){ ?>
                                        <option <?=($cod_area==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select>
                            </div>                    
                        </div>
                      </div>
                    </div>

                    <div class="row">
                        <label class="col-sm-2 col-form-label">Responsable</label>
                        <div class="col-sm-4">
                        <div class="form-group">
                            <div id="div_personal_UO_tcc">
                                <?php
                                $stmtPersonal = $dbh->prepare("SELECT p.codigo, p.paterno,p.materno,p.primer_nombre
                                from personal p, unidades_organizacionales uo 
                                where uo.codigo=p.cod_unidadorganizacional and uo.codigo=$cod_uo order by 2");
                                $stmtPersonal->execute();
                                ?>
                                <select id="cod_personal" name="cod_personal" class="selectpicker form-control" data-style="btn btn-info" data-size="5">
                                    <?php 
                                        while ($row = $stmtPersonal->fetch()){                                             
                                       ?>
                                       <option value="<?=$row["codigo"];?>" <?=($cod_personal==$row['codigo'])?"selected":"";?> <?=($codigo>0)?"disabled":"";?> >
                                            <?=$row["paterno"].' '.$row["materno"].' '.$row["primer_nombre"];?>
                                        </option>
                                        <?php 
                                        } ?>
                                </select>

                            </div>
                        </div>
                        </div><!--fin campo cod_responsables_responsable -->

                    </div><!--fin campo cod_responsables_autorizadopor -->              
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlListTiposCajaChica;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>