<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'rrhh/configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
    $codigo_item=$codigo_item;
    $codigo_personal=$codigo_p;

    $stmt = $dbh->prepare("SELECT primer_nombre,paterno,materno,
        cod_area,cod_unidadorganizacional,cod_cargo,cod_grado_academico,haber_basico
    FROM personal where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo_personal);
    $stmt->execute();
    $result = $stmt->fetch();
    $primer_nombre = $result['primer_nombre'];
    $paterno = $result['paterno'];
    $materno = $result['materno'];
    $cod_area = $result['cod_area'];
    $cod_unidadorganizacional = $result['cod_unidadorganizacional'];
    $cod_cargo=$result['cod_cargo'];
    $cod_grado_academico=$result['cod_grado_academico'];
    $haber_basico=$result['haber_basico'];
    $fecha_cambio='0000-00-00';

if($codigo_item==1){?>
    <div class="content">
        <div class="container-fluid">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="<?=$urlPersonalOtrosSave;?>" method="post">
                <input type="hidden" name="codigo_item" id="codigo_item" value="<?=$codigo_item;?>"/>
                <input type="hidden" name="codigo_personal" id="codigo_personal" value="<?=$codigo_personal;?>"/>
                <div class="card">
                    <div class="card-header <?=$colorCard;?> card-header-text">
                        <div class="card-text">
                          <h4 class="card-title">Editar Oficina/Area</h4>
                        </div>
                        <center><label class="col-sm-12 col-form-label"><h4><b>Personal:</b> <?=$paterno." ".$materno." ".$primer_nombre?></h4></label></center>
                    </div>
                    <div class="card-body ">                        
                        <div class="row">
                          <label class="col-sm-2 col-form-label">Oficina</label>
                          <div class="col-sm-4">
                            <div class="form-group">
                                <select name="cod_uo" id="cod_uo" class="selectpicker" data-style="btn btn-info" onChange="ajaxAreaContabilizacionDetalle(this);">
                                    <option value=""></option>
                                    <?php 
                                    $queryUO = "SELECT codigo,nombre from unidades_organizacionales where cod_estado=1 order by nombre";
                                    $statementUO = $dbh->query($queryUO);
                                    while ($row = $statementUO->fetch()){ ?>
                                        <option <?=($cod_unidadorganizacional==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select>
                            </div>
                          </div>                    
                        </div>  
                        <div class="row">                                        
                          <label class="col-sm-2 col-form-label">Area</label>
                          <div class="col-sm-4">
                            <div class="form-group" >
                                <div id="div_contenedor_area">
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
                            <label class="col-sm-2 col-form-label">Fecha De Cambio</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_cambio" id="fecha_cambio" required="true" value="<?=$fecha_cambio;?>" />                                    
                                </div>
                            </div>
                        </div>
                    </div>            
                    <div class="card-footer ml-auto mr-auto">
                        <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                        <a href="<?=$urlListPersonal;?>" class="<?=$buttonCancel;?>">Volver</a>
                    </div>
                </div>
              </form>
            </div>
        
        </div>
    </div>
<?php }
if($codigo_item==2){?>
    <div class="content">
        <div class="container-fluid">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="<?=$urlPersonalOtrosSave;?>" method="post">
                <input type="hidden" name="codigo_item" id="codigo_item" value="<?=$codigo_item;?>"/>
                <input type="hidden" name="codigo_personal" id="codigo_personal" value="<?=$codigo_personal;?>"/>
                <div class="card">
                    <div class="card-header <?=$colorCard;?> card-header-text">
                        <div class="card-text">
                          <h4 class="card-title">Editar Cargo</h4>
                        </div>
                        <center><label class="col-sm-12 col-form-label"><h4><b>Personal:</b> <?=$paterno." ".$materno." ".$primer_nombre?></h4></label></center>
                    </div>
                        <div class="card-body ">            
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Cargo</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_cargo"  class="selectpicker" data-style="btn btn-info" required>
                                            <?php 
                                            $queryCargos = "SELECT codigo,nombre,abreviatura from cargos where cod_estadoreferencial=1";
                                            $statementCargos = $dbh->query($queryCargos);
                                            while ($row = $statementCargos->fetch()) { ?>
                                                <option <?php if($cod_cargo == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>                    
                                </div>                                                                            
                            </div><!--fin campo cargo -->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Fecha De Cambio</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="date" name="fecha_cambio" id="fecha_cambio" required="true" value="<?=$fecha_cambio;?>" />                                    
                                    </div>
                                </div>
                            </div>
                        </div>            
                    <div class="card-footer ml-auto mr-auto">
                        <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                        <a href="<?=$urlListPersonal;?>" class="<?=$buttonCancel;?>">Volver</a>
                    </div>
                </div>
              </form>
            </div>
        
        </div>
    </div>
<?php }
if($codigo_item==3){?>
    <div class="content">
        <div class="container-fluid">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="<?=$urlPersonalOtrosSave;?>" method="post">
                <input type="hidden" name="codigo_item" id="codigo_item" value="<?=$codigo_item;?>"/>
                <input type="hidden" name="codigo_personal" id="codigo_personal" value="<?=$codigo_personal;?>"/>
                <div class="card">
                    <div class="card-header <?=$colorCard;?> card-header-text">
                        <div class="card-text">
                          <h4 class="card-title">Editar Grado Academico</h4>
                        </div>
                        <center><label class="col-sm-12 col-form-label"><h4><b>Personal:</b> <?=$paterno." ".$materno." ".$primer_nombre?></h4></label></center>
                    </div>
                    <div class="card-body ">                                    
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Grado Académico</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select name="grado_academico" id="grado_academico"  class="selectpicker" data-style="btn btn-info" required>
                                        <?php 
                                        $querygrado_academico = "SELECT codigo,nombre from personal_grado_academico where codestadoreferencial=1";
                                        $statementgrado_academico = $dbh->query($querygrado_academico);
                                        while ($row = $statementgrado_academico->fetch()) { ?>
                                            <option <?php if($cod_grado_academico == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>          
                        </div>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Fecha De Cambio</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_cambio" id="fecha_cambio" required="true" value="<?=$fecha_cambio;?>" />                                    
                                </div>
                            </div>
                        </div>
                    </div>            
                    <div class="card-footer ml-auto mr-auto">
                        <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                        <a href="<?=$urlListPersonal;?>" class="<?=$buttonCancel;?>">Volver</a>
                    </div>
                </div>
              </form>
            </div>
        
        </div>
    </div>
<?php }
if($codigo_item==4){?>
    <div class="content">
        <div class="container-fluid">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="<?=$urlPersonalOtrosSave;?>" method="post">
                <input type="hidden" name="codigo_item" id="codigo_item" value="<?=$codigo_item;?>"/>
                <input type="hidden" name="codigo_personal" id="codigo_personal" value="<?=$codigo_personal;?>"/>
                <div class="card">
                    <div class="card-header <?=$colorCard;?> card-header-text">
                        <div class="card-text">
                          <h4 class="card-title">Editar Haber Básico</h4>
                        </div>
                        <center><label class="col-sm-12 col-form-label"><h4><b>Personal:</b> <?=$paterno." ".$materno." ".$primer_nombre?></h4></label>
                        </center>
                    </div>
                    <div class="card-body ">            
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Haber Basico</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="haber_basico" id="haber_basico" value="<?=$haber_basico;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" required/>
                                </div>
                            </div> 
                        </div><!--haber basico-->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Fecha De Cambio</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_cambio" id="fecha_cambio" required="true" value="<?=$fecha_cambio;?>" />                                    
                                </div>
                            </div>
                        </div>
                    </div>            
                    <div class="card-footer ml-auto mr-auto">
                        <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                        <a href="<?=$urlListPersonal;?>" class="<?=$buttonCancel;?>">Volver</a>
                    </div>
                </div>
              </form>
            </div>
        
        </div>
    </div>
<?php }?>


