<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'rrhh/configModule.php';
require_once 'functions.php';

//$dbh = new Conexion();
$dbh = new Conexion();
$codigo=$codigo;
$stmt = $dbh->prepare("SELECT * FROM personal where codigo =:codigo");
$stmt->bindParam(':codigo',$codigo);
$stmt->execute();
//resultados
$result = $stmt->fetch();
$codigo = $result['codigo'];
$cod_tipoIdentificacion = $result['cod_tipo_identificacion'];
$tipo_identificacionOtro = $result['tipo_identificacion_otro'];
$identificacion = $result['identificacion'];
$cod_lugar_emision = $result['cod_lugar_emision'];
$lugar_emisionOtro = $result['lugar_emision_otro'];
$fecha_nacimiento = $result['fecha_nacimiento'];
$cod_cargo = $result['cod_cargo'];
$cod_unidadorganizacional = $result['cod_unidadorganizacional'];
$cod_area = $result['cod_area'];
$jubilado = $result['jubilado'];
$cod_genero = $result['cod_genero'];
$cod_tipopersonal = $result['cod_tipopersonal'];
$haber_basico = $result['haber_basico'];
$paterno = $result['paterno'];
$materno = $result['materno'];
$apellido_casada = $result['apellido_casada'];
$primer_nombre = $result['primer_nombre'];
$otros_nombres = $result['otros_nombres'];
$nua_cua_asignado = $result['nua_cua_asignado'];
$direccion = $result['direccion'];
$cod_tipoafp = $result['cod_tipoafp'];
$cod_tipoaporteafp = $result['cod_tipoaporteafp'];
$nro_seguro = $result['nro_seguro'];
$cod_estadopersonal = $result['cod_estadopersonal'];
$telefono = $result['telefono'];
$celular = $result['celular'];
$email = $result['email'];
$persona_contacto = $result['persona_contacto'];
$created_at = $result['created_at'];
$created_by = $result['created_by'];
$modified_at = $result['modified_at'];
$modified_by = $result['modified_by'];

$cod_nacionalidad = $result['cod_nacionalidad'];
$cod_estadocivil = $result['cod_estadocivil'];//-
$cod_pais = $result['cod_pais'];
$cod_departamento = $result['cod_departamento'];
$cod_ciudad = $result['cod_ciudad'];
$ciudadOtro = $result['ciudad_otro'];
$cod_grado_academico = $result['cod_grado_academico'];   
$ing_contr = $result['ing_contr'];
$ing_planilla = $result['ing_planilla'];

//personal discapacitado
$stmtDiscapacitado = $dbh->prepare("SELECT * FROM personal_discapacitado where codigo =:codigo");
$stmtDiscapacitado->bindParam(':codigo',$codigo);
$stmtDiscapacitado->execute();
$resultDiscapacitado = $stmtDiscapacitado->fetch();
$discapacitado = $resultDiscapacitado['discapacitado'];
$tutor_discapacitado = $resultDiscapacitado['tutor_discapacitado'];
$celular_tutor = $resultDiscapacitado['celular_tutor'];
$parentesco = $resultDiscapacitado['parentesco'];
//IMAGEN
$stmtIMG = $dbh->prepare("SELECT * FROM personalimagen where codigo =:codigo");
//Ejecutamos;
$stmtIMG->bindParam(':codigo',$codigo);
$stmtIMG->execute();
$resultIMG = $stmtIMG->fetch();
$imagen = $resultIMG['imagen'];
//$archivo = __DIR__.DIRECTORY_SEPARATOR."imagenes".DIRECTORY_SEPARATOR.$imagen;//sale mal
$archivo = "personal/imagenes/".$imagen;//sale mal

//COMBOS...
$queryUO = "select * from unidades_organizacionales order by nombre";
$statementUO = $dbh->query($queryUO);

$queryCargos = "select * from cargos";
$statementCargos = $dbh->query($queryCargos);


$queryTPersonal = "select * from tipos_personal";
$statementTPersonal = $dbh->query($queryTPersonal);

$querytipos_afp = "select * from tipos_afp";
$statementtipos_afp = $dbh->query($querytipos_afp);

$querytipos_aporteafp = "select * from tipos_aporteafp";
$statementtipos_aporteafp = $dbh->query($querytipos_aporteafp);


$queryestados_personal = "select * from estados_personal";
$statementestados_personal = $dbh->query($queryestados_personal);

$querygrado_academico = "select * from personal_grado_academico";
$statementgrado_academico = $dbh->query($querygrado_academico);
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">                
                <div class="card">
                    <div class="card-header <?=$colorCard;?> card-header-text">
                        <div class="card-text">
                          <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularPersonal;?></h4>
                        </div>
                    </div>                
                    <div class="card-body">
                        <form id="form1" action="<?=$urlSavePersonal;?>" method="post">    
                        <h3 align="center">CAMPOS NO GESTIONADOS POR RRHH</h3>
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Código Personal</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input  name="codigo" id="codigo" value="<?=$codigo;?>" readonly="readonly"/>
                                </div>
                            </div>                            
                        </div><!--fin campo codigo --> 
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Tipo Identificación</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="cod_tipoIdentificacion" id="cod_tipoIdentificacion" value="<?=$cod_tipoIdentificacion;?>" readonly="readonly"/>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Tipo Identificación Otro</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="tipo_identificacionOtro" id="tipo_identificacionOtro" value="<?=$tipo_identificacionOtro;?>" readonly="readonly"/>
                                </div>
                            </div>                            
                        </div><!--fin campo tipo_identificacionOtro--> 
                                               
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Identificación</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="identificacion" id="identificacion" readonly="readonly" value="<?=$identificacion;?>">
                                </div>
                            </div>

                            <label class="col-sm-2 col-form-label" >Lugar Emision</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control"  name="cod_lugar_emision" id="cod_lugar_emision" readonly="readonly" value="<?=$cod_lugar_emision;?>"/>                                      
                                </div>
                            </div>                            
                        </div><!--fin campo ci_lugar_emision -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Lugar Emisión Otro</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="lugar_emisionOtro" id="lugar_emisionOtro" value="<?=$lugar_emisionOtro;?>" readonly="readonly"/>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Nacionalidad</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="cod_nacionalidad" id="cod_nacionalidad" value="<?=$cod_nacionalidad;?>" readonly="readonly"/>
                                </div>
                            </div>
                        </div><!--fin campo Nacionalidad -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Pais</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="cod_pais" id="cod_pais" value="<?=$cod_pais;?>" readonly="readonly"/>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Departamento</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="cod_departamento" id="cod_departamento" value="<?=$cod_departamento;?>" readonly="readonly"/>
                                </div>
                            </div>
                        </div><!--fin campo pais y departamento -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Ciudad</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="cod_ciudad" id="cod_ciudad" value="<?=$cod_ciudad;?>" readonly="readonly"/>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Otra Ciudad</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="ciudadOtro" id="ciudadOtro" value="<?=$ciudadOtro;?>" readonly="readonly"/>
                                </div>
                            </div>
                        </div><!--fin campo ciudad -->
                        <div class="row">                            
                            <label class="col-sm-2 col-form-label">Estado Civil</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control"  name="cod_estadocivil" id="cod_estadocivil" readonly="readonly" value="<?=$cod_estadocivil;?>"/>
                                </div>
                            </div>
                            
                            <label class="col-sm-2 col-form-label">Genero</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="cod_genero" id="cod_genero" readonly="readonly" value="<?=$cod_genero;?>"/>
                                
                                </div>
                            </div>
                        </div><!--fin genero y estadoCivil-->
                        <div class="row">                            
                            <label class="col-sm-2 col-form-label">Fecha Nacimiento</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control"  name="fecha_nacimiento" id="fecha_nacimiento" readonly="readonly" value="<?=$fecha_nacimiento;?>"/>
                                </div>
                            </div>
                        </div><!--Fecha Nac-->
                        
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Paterno</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="paterno"  id="paterno" readonly="readonly" value="<?=$paterno;?>" />
                                </div>
                            </div>

                            <label class="col-sm-2 col-form-label">Materno</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="materno" id="materno"  readonly="readonly" value="<?=$materno;?>" />
                                </div>
                            </div>
                        </div><!--fin campo materno -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Primer Nombre</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="primer_nombre" id="primer_nombre" readonly="readonly" value="<?=$primer_nombre;?>" />
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Telefono</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" name="telefono" id="telefono" readonly="readonly" value="<?=$telefono;?>" />
                                </div>
                            </div>
                        </div><!--fin campo primer nombre y tel-->                        
                        <div class="row">                        
                            <label class="col-sm-2 col-form-label">Celular</label>
                            <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" name="celular" id="celular" readonly="readonly" value="<?=$celular;?>"/>
                            </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" name="email" id="email" readonly="readonly" value="<?=$email;?>"/>
                            </div>
                            </div>
                        </div><!--fin campo celular y email -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Direccion</label>
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <input class="form-control" name="direccion" id="direccion" readonly="readonly" value="<?=$direccion;?>" />
                                </div>                    
                            </div>                        
                        </div><!--fin campo direccion -->

                        <h3 align="center">CAMPOS GESTIONADOS POR RRHH</h3>
                        
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Fecha de Ingreso</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="ing_contr" id="ing_contr" required="true" value="<?=$ing_contr;?>" />                                    
                                </div>
                            </div>                                                                
                        </div><!--fin campo ing contrato & ing planilla -->

                        <div class="row">
                            <label class="col-sm-2 col-form-label">Apellido Casada</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="apellido_casada" id="apellido_casada" value="<?=$apellido_casada;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                </div>
                            </div>

                            
                            <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="hidden" name="otros_nombres" id="otros_nombres" value="<?=$otros_nombres;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                            </div>
                            </div>
                        </div><!--fin campo otros_nombres y apellido casada -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Unidad Organizacional</label>
                            <div class="col-sm-4">
                            <div class="form-group">
                                <select name="cod_unidadorganizacional" id="cod_unidadorganizacional" class="selectpicker" data-style="btn btn-info" required>
                                    <option value="">-</option>
                                    <?php while ($row = $statementUO->fetch()) { ?>
                                        <option <?=($cod_unidadorganizacional==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select>                               
                            </div>
                            </div>

                            <label class="col-sm-2 col-form-label">Area</label>
                            <div class="col-sm-4">
                            <div class="form-group">
                                <div id="cod_area_containers">
                                    <select name="cod_area"  class="form-control" id="cod_area" data-style="btn btn-info" required>
                                    </select>
                                    <input type="hidden" name="cod_area2" id="cod_area2" value="<?=$cod_area;?>"/>

                                </div>
                                </div>
                            </div>
                        </div><!--fin campo cod_area -->
                        <div class="row" id="prueba">
                            <label class="col-sm-2 col-form-label">Cargo</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select name="cod_cargo"  class="selectpicker" data-style="btn btn-info" required>
                                        <?php while ($row = $statementCargos->fetch()) { ?>
                                            <option <?php if($cod_cargo == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                    </select>
                                </div>                    
                            </div>                
                            <label class="col-sm-2 col-form-label">Grado Académico</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select name="grado_academico" id="grado_academico"  class="selectpicker " data-style="btn btn-info" required>
                                        <?php while ($row = $statementgrado_academico->fetch()) { ?>
                                            <option <?php if($cod_grado_academico == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>         
                                      
                        </div><!--fin campo cargo -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Haber Basico</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="haber_basico" id="haber_basico" value="<?=$haber_basico;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" required/>
                                </div>
                            </div> 
                            <label class="col-sm-2 col-form-label">Tipo Personal</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select name="cod_tipopersonal"  class="selectpicker " data-style="btn btn-info" required>
                                        <?php while ($row = $statementTPersonal->fetch()) { ?>
                                            <option <?php if($cod_tipopersonal == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>                                                        
                        </div><!--tipos personal -->
                        
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Jubilado</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select name="jubilado"  class="selectpicker " data-style="btn btn-info">
                                        <option <?php if($jubilado == '1') echo "selected"; ?> value="1">SI</option>
                                        <option <?php if($jubilado == '0') echo "selected"; ?> value="0">NO</option>
                                    </select>           

                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Nua / Cua Asignado</label>
                            <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="text" name="nua_cua_asignado" id="nua_cua_asignado" value="<?=$nua_cua_asignado;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" required/>
                            </div>
                            </div>
                        </div><!--fin campo nua_cua_asignado -->

                        <div class="row">
                            <label class="col-sm-2 col-form-label">Afp</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select name="cod_tipoafp" id="cod_tipoafp"  class="selectpicker " data-style="btn btn-info" required>
                                        <?php while ($row = $statementtipos_afp->fetch()) { ?>
                                            <option <?php if($cod_tipoafp == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <label class="col-sm-2 col-form-label">Tipo de Aporte AFP</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select name="cod_tipoaporteafp" id="cod_tipoaporteafp"  class="selectpicker " data-style="btn btn-info" required>
                                        <?php while ($row = $statementtipos_aporteafp->fetch()) { ?>
                                            <option  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div><!--fin campo cod_tipoaporteafp -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Nro seguro</label>
                            <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="number" name="nro_seguro" id="nro_seguro" required value="<?=$nro_seguro;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                            </div>
                            </div>

                            <label class="col-sm-2 col-form-label">Estado</label>
                            <div class="col-sm-4">
                            <div class="form-group">
                                <select name="cod_estadopersonal"  class="selectpicker " data-style="btn btn-info" required>
                                            <?php while ($row = $statementestados_personal->fetch()) { ?>
                                                <option <?php if($cod_estadopersonal == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                            </select>                  
                            </div>
                            </div>
                        </div><!--fin campo cod_estadopersonal -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Persona Contacto</label>
                            <div class="col-sm-7">
                            <div class="form-group">
                                <input class="form-control" type="text" name="persona_contacto" id="persona_contacto" required value="<?=$persona_contacto;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                            </div>
                            </div>
                        </div><!--fin campo persona_contacto -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Persona Con Discapacidad</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select name="cod_discapacidad" id="cod_discapacidad"  class="selectpicker " data-style="btn btn-info" >                                
                                        <option <?php if($discapacitado == 0) echo "selected"; ?> value="0"> NO</option>
                                        <option <?php if($discapacitado == 1) echo "selected"; ?> value="1"> SI</option>                            
                                    </select>
                                </div>
                            </div>

                            <label class="col-sm-2 col-form-label">Tutor De Persona</label>
                            <div class="col-sm-4">
                            <div class="form-group">
                                <select name="cod_tutordiscapacidad" id="cod_tutordiscapacidad"  class="selectpicker " data-style="btn btn-info" >                                
                                                <option <?php if($tutor_discapacitado == 0) echo "selected"; ?> value="0"> NO</option>
                                                <option <?php if($tutor_discapacitado == 1) echo "selected"; ?> value="1"> SI</option>                            
                                </select>                    
                            </div>
                            </div>
                        </div><!--fin campo persona discapacidad -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Parentesco</label>
                            <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="text" name="parentescotutor" id="parentescotutor" value="<?=$parentesco;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                            </div>
                            </div>

                            <label class="col-sm-2 col-form-label">Celular Tutor</label>
                            <div class="col-sm-4">
                            <div class="form-group">
                                <input class="form-control" type="number" name="celularTutor" id="celularTutor" value="<?=$celular_tutor;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                            </div>
                            </div>
                        </div><!--fin campo celular tutor discapacidad -->

                        <div class="row">
                            <label class="col-sm-2 col-form-label">Imagen</label>
                            <div class="col-md-7">
                                <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                                    <div class="fileinput-new img-raised">
                                        <img src="<?=$archivo;?>" alt="..." style="width:250px;">
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail img-raised">
                                    </div>
                                    <div>
                                        <span class="btn btn-raised btn-round <?=$buttonNormal;?> btn-file">
                                        <span class="fileinput-new">Seleccionar Imagen</span>
                                        <span class="fileinput-exists">Cambiar</span>
                                        <input type="file" name="image" /><!-- ARCHHIVO -->
                                        </span>
                                        <a href="#" class="btn <?=$buttonNormal;?> btn-round fileinput-exists" data-dismiss="fileinput">
                                        <i class="fa fa-times"></i> Quitar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                        <!--fin campo imagen-->
                    </div>
                    <div class="card-footer ml-auto mr-auto">
                        <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                        <a href="<?=$urlListPersonal;?>" class="<?=$buttonCancel;?>">Cancelar</a>
                    </div>
                </div>
            
                    <!-- SOLO MUESTRO SI ES EN ESTADO EDICION... -->
                    <?php if ($codigo > 0){ ?>

                    <!-- tabs -->
                   <!--  <div class="card card-nav-tabs card-plain">
                        <div class="card-header <?=$colorCard;?>">                    
                            <div class="nav-tabs-navigation">
                                <div class="nav-tabs-wrapper">
                                    <ul class="nav nav-tabs" data-tabs="tabs">
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#uno" data-toggle="tab">Distribucion Salarial</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#dos" data-toggle="tab">Historico Cargos</a>
                                        </li>          
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body ">
                            <div class="tab-content text-center">
                                <div class="tab-pane active" id="uno">
                                    <p>I think that&#x2019;s a responsibility that I have, to push possibilities, to show people, this is the level that things could be at. So when you get something that has the name Kanye West on it, it&#x2019;s supposed to be pushing the furthest possibilities. I will be the leader of a company that ends up being worth billions of dollars, because I got the answers. I understand culture. I am the nucleus.</p>
                                </div>
                                <div class="tab-pane" id="dos">
                                    <p> I will be the leader of a company that ends up being worth billions of dollars, because I got the answers. I understand culture. I am the nucleus. I think that&#x2019;s a responsibility that I have, to push possibilities, to show people, this is the level that things could be at. I think that&#x2019;s a responsibility that I have, to push possibilities, to show people, this is the level that things could be at. </p>
                                </div>                        
                            </div>
                        </div>
                    </div> -->
                        <!-- fin tabs-->
                    <?php } ?>
            </div>
        </div>
        
    </div>
</div>

<script>
    function cargarAreas(codigo){
        $.post("rrhh/areas_organizacionAjax.php", "cod_unidadorganizacional="+codigo, function (data) {
        //console.log("llega0");
        $('#cod_area').remove();//elimino totalmente el combo
        //console.log("llega1");
        $("#cod_area_containers").empty();//vacio el padre
        //console.log("llega2");
        $('#cod_area_containers').append(data);//inserto el html+data q me llego
        //console.log("llega3");
        $('.selectpicker').selectpicker();//le doy estilo
        //console.log("llega4");

        //---------------------------------------------------------------------
        //intentar seleccionar si es q hay un valor x defecto
        });
    }

    $('#cod_unidadorganizacional').on('change', function() {
      //alert( this.value );
        //cod_tiposbienes
        //console.log("llega-1");
        cargarAreas($('#cod_unidadorganizacional').val());
    });
    cargarAreas($('#cod_unidadorganizacional').val());
</script>