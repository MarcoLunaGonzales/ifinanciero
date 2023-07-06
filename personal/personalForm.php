<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

require_once 'conexion.php';
require_once 'styles.php';
require_once 'rrhh/configModule.php';


//$dbh = new Conexion();
$dbh = new Conexion();
$codigo=$codigo;
$stmt = $dbh->prepare("SELECT *,
    (select ga.nombre from personal_grado_academico ga where ga.codigo=cod_grado_academico) as nombre_grado_academico,
    (select ca.nombre from cargos ca where ca.codigo=cod_cargo) as nombre_cargo,
    (select uo.nombre from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional) as nombre_uo,
    (select a.nombre from areas a where a.codigo=cod_area) as nombre_area
     FROM personal where codigo =:codigo");
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
$bandera = $result['bandera'];
$nombre_grado_academico = $result['nombre_grado_academico'];
$nombre_cargo = $result['nombre_cargo'];
$nombre_uo = $result['nombre_uo'];
$nombre_area = $result['nombre_area'];
$email_empresa = $result['email_empresa'];
$personal_confianza = $result['personal_confianza'];
$cuenta_bancaria = $result['cuenta_bancaria'];
$cod_banco = $result['cod_banco'];
$codigo_dependiente = $result['codigo_dependiente'];

// Nuevo campo
$nro_casillero = $result['nro_casillero'];

//personal discapacitado
$stmtDiscapacitado = $dbh->prepare("SELECT * FROM personal_discapacitado where codigo =:codigo and cod_estadoreferencial=1");
$stmtDiscapacitado->bindParam(':codigo',$codigo);
$stmtDiscapacitado->execute();
$resultDiscapacitado = $stmtDiscapacitado->fetch();
$cod_tipo_persona_discapacitado = $resultDiscapacitado['tipo_persona_discapacitado'];
$nro_carnet_discapacidad = $resultDiscapacitado['nro_carnet_discapacidad'];
$fecha_nac_persona_dis = $resultDiscapacitado['fecha_nac_persona_dis'];
//IMAGEN
$stmtIMG = $dbh->prepare("SELECT * FROM personalimagen where codigo =:codigo");
$stmtIMG->bindParam(':codigo',$codigo);
$stmtIMG->execute();
$resultIMG = $stmtIMG->fetch();
$imagen = $resultIMG['imagen'];
//$archivo = __DIR__.DIRECTORY_SEPARATOR."imagenes".DIRECTORY_SEPARATOR.$imagen;//sale mal
$archivo = "personal/imagenes/".$imagen;//sale mal


//COMBOS...
$queryTPersonal = "SELECT codigo,nombre from tipos_personal where cod_estadoreferencial=1";
$statementTPersonal = $dbh->query($queryTPersonal);

$querytipos_afp = "SELECT codigo,nombre from tipos_afp where cod_estadoreferencial=1";
$statementtipos_afp = $dbh->query($querytipos_afp);

$querytipos_aporteafp = "SELECT codigo,nombre from tipos_aporteafp where cod_estadoreferencial=1";
$statementtipos_aporteafp = $dbh->query($querytipos_aporteafp);

$queryestados_personal = "SELECT codigo,nombre from estados_personal where cod_estadoreferencial=1";
$statementestados_personal = $dbh->query($queryestados_personal);
?>

<div class="content">
    <div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
                <form id="form1" action="<?=$urlSavePersonal;?>" method="post" enctype="multipart/form-data">                    
                    <div class="card">
                        <div class="card-header <?=$colorCard;?> card-header-text">
                            <div class="card-text">
                              <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?>  <?=$nombreSingularPersonal;?></h4>
                            </div>
                        </div>
                        <h3 align="center">CAMPOS NO GESTIONADOS POR RRHH</h3>
                        <div class="card-body">                    
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Código Personal</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" name="codigo" id="codigo" value="<?=$codigo;?>" readonly="readonly"/>
                                    </div>
                                </div>                           
                            </div><!--fin campo nro_casillero --> 
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Tipo Identificación</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" name="cod_tipoIdentificacion" id="cod_tipoIdentificacion" value="<?=obtenerNombreIdentificacionPersona($cod_tipoIdentificacion,1);?>" readonly="readonly"/>
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
                                        <input class="form-control"  name="cod_lugar_emision" id="cod_lugar_emision" readonly="readonly" value="<?=obtenerlugarEmision($cod_lugar_emision,1);?>"/>
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
                                        <input class="form-control" name="cod_nacionalidad" id="cod_nacionalidad" value="<?=obtenerNombreNacionalidadPersona($cod_nacionalidad,1);?>" readonly="readonly"/>
                                    </div>
                                </div>
                            </div><!--fin campo Nacionalidad -->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Pais</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" name="cod_pais" id="cod_pais" value="<?=obtenerNombreNacionalidadPersona($cod_pais,2);?>" readonly="readonly"/>
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Departamento</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" name="cod_departamento" id="cod_departamento" value="<?=empty($cod_departamento)?'':obtenerlugarEmision($cod_departamento,2);?>" readonly="readonly"/>
                                    </div>
                                </div>
                            </div><!--fin campo pais y departamento -->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Ciudad</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" name="cod_ciudad" id="cod_ciudad" value="<?=empty($cod_ciudad)?'':obtenerNombreCiudadPersona($cod_ciudad);?>" readonly="readonly"/>
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
                                        <input class="form-control"  name="cod_estadocivil" id="cod_estadocivil" readonly="readonly" value="<?=obtenerNombreEstadoCivilPersona($cod_estadocivil);?>"/>
                                    </div>
                                </div>
                                
                                <label class="col-sm-2 col-form-label">Genero</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" name="cod_genero" id="cod_genero" readonly="readonly" value="<?=obtenerNombreGeneroPersona($cod_genero);?>"/>
                                    
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
                                <label class="col-sm-2 col-form-label">Fecha de Ingreso Contr</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="date" name="ing_contr" id="ing_contr" required="true" value="<?=$ing_contr;?>" />                                    
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Fecha de Ingreso Planilla</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="date" name="ing_planilla" id="ing_planilla" required="true" value="<?=$ing_planilla;?>"/>
                                    </div>
                                </div>

                                <label class="col-sm-2 col-form-label">Apellido Casada</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="apellido_casada" id="apellido_casada" value="<?=$apellido_casada;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                    </div>
                                </div>
                            </div> <!--fin campo ing contrato y ing planilla-->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Personal de Confianza</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="personal_confianza" id="personal_confianza"  class="selectpicker form-control form-control-sm" data-style="btn btn-info">
                                            <option <?php if($personal_confianza == '0') echo "selected"; ?> value="0">NO</option>
                                            <option <?php if($personal_confianza == '1') echo "selected"; ?> value="1">SI</option>
                                        </select>           

                                    </div>
                                </div>
                                
                                <label class="col-sm-2 col-form-label">Tipo Personal</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_tipopersonal"  class="selectpicker form-control form-control-sm " data-style="btn btn-info" required>
                                            <?php while ($row = $statementTPersonal->fetch()) { ?>
                                                <option <?php if($cod_tipopersonal == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>                                                                   
                                <input class="form-control" type="hidden" name="otros_nombres" id="otros_nombres" value="<?=$otros_nombres;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                            </div><!--fin campo apellido casada y tipo personal-->
                            <?php
                            if($bandera==0)
                            { ?>
                                <div class="row">
                                  <label class="col-sm-2 col-form-label">Oficina</label>
                                  <div class="col-sm-4">
                                    <div class="form-group">                                        
                                        <!-- AJAX - DESPUES DE LA SELECCIÓN CARGA LOS DATOS DE ÁREA -->
                                        <!-- <select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-info" onChange="ajaxAreaContabilizacionDetalle(this);" data-show-subtext="true" data-live-search="true"> -->
                                            
                                        <select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true">
                                            <option value=""></option>
                                            <?php 
                                            $queryUO = "SELECT codigo,nombre,abreviatura from unidades_organizacionales where cod_estado=1 order by nombre";
                                            $statementUO = $dbh->query($queryUO);
                                            while ($row = $statementUO->fetch()){ ?>
                                                <option <?=($cod_unidadorganizacional==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                  </div>
                               
                                  <label class="col-sm-2 col-form-label">Area</label>
                                  <div class="col-sm-4">
                                    <div class="form-group" >
                                        <div id="div_contenedor_area">
                                            <select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-info" >
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
                                    <label class="col-sm-2 col-form-label">Cargo</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div id="div_contenedor_cargo">
                                                <select name="cod_cargo"  class="selectpicker form-control form-control-sm" data-style="btn btn-info" required>
                                                    <option value=""></option>
                                                    <?php 
                                                    $queryCargos = "SELECT codigo,nombre,abreviatura from cargos where cod_estadoreferencial=1";
                                                    $statementCargos = $dbh->query($queryCargos);
                                                    while ($row = $statementCargos->fetch()) { ?>
                                                        <option <?php if($cod_cargo == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                                    <?php } ?>
                                                </select>    
                                            </div>
                                            
                                        </div>                    
                                    </div>                
                                    <label class="col-sm-2 col-form-label">Grado Académico</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <select name="grado_academico" id="grado_academico"  class="selectpicker form-control form-control-sm" data-style="btn btn-info" required>
                                                <?php 
                                                $querygrado_academico = "SELECT codigo,nombre from personal_grado_academico where codestadoreferencial=1";
                                                $statementgrado_academico = $dbh->query($querygrado_academico);
                                                while ($row = $statementgrado_academico->fetch()) { ?>
                                                    <option <?php if($cod_grado_academico == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>                                           
                                </div><!--fin campo cargo -->
                                <div class="row">
                                    <label class="col-sm-2 col-form-label">Haber Basico (Bs)</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="haber_basico" id="haber_basico" value="<?=$haber_basico;?>" required/>
                                        </div>
                                    </div>
                                    <label class="col-sm-2 col-form-label">Email Empresarial</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="email_empresa" id="email_empresa" required="true" value="<?=$email_empresa;?>" />
                                        </div>
                                    </div> 
                                </div><!--haber basico-->
                                <?php 
                            }else{ ?>
                                <div class="row">
                                  <label class="col-sm-2 col-form-label">Oficina</label>
                                  <div class="col-sm-4">
                                    <div class="form-group">
                                        <input type="hidden" class="form-control"  name="cod_uo" id="cod_uo"  value="<?=$cod_unidadorganizacional;?>"/>
                                        <input class="form-control" readonly="readonly" value="<?=$nombre_uo;?>"/>
                                    </div>
                                  </div>
                               
                                  <label class="col-sm-2 col-form-label">Area</label>
                                  <div class="col-sm-4">
                                    <div class="form-group" >
                                        <div id="div_contenedor_area">
                                            <input type="hidden" class="form-control"  name="cod_area" id="cod_area"  value="<?=$cod_area;?>"/>
                                            <input class="form-control" readonly="readonly" value="<?=$nombre_area;?>"/>
                                        </div>                    
                                    </div>
                                  </div>
                                </div>              
                                <div class="row">
                                    <label class="col-sm-2 col-form-label">Cargo</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <input type="hidden" class="form-control"  name="cod_cargo" id="cod_cargo"  value="<?=$cod_cargo;?>"/>
                                            <input class="form-control" readonly="readonly" value="<?=$nombre_cargo;?>"/>
                                        </div>                    
                                    </div>                
                                    <label class="col-sm-2 col-form-label">Grado Académico</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <input type="hidden" class="form-control"  name="grado_academico" id="grado_academico"  value="<?=$cod_grado_academico;?>"/>
                                            <input class="form-control" readonly="readonly" value="<?=$nombre_grado_academico;?>"/>
                                        </div>
                                    </div>                                           
                                </div><!--fin campo cargo -->
                                <div class="row">
                                    <label class="col-sm-2 col-form-label">Haber Basico (Bs)</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="haber_basico" id="haber_basico" value="<?=formatNumberDec($haber_basico);?>" readonly="readonly"/>
                                        </div>
                                    </div> 
                                    <label class="col-sm-2 col-form-label">Email Empresarial</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-control" type="text" name="email_empresa" id="email_empresa" required="true" value="<?=$email_empresa;?>" />
                                        </div>
                                    </div> 
                                </div><!--haber basico-->
                            <?php }
                            ?>                
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Jubilado</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="jubilado"  class="selectpicker form-control form-control-sm " data-style="btn btn-info">
                                            <option <?php if($jubilado == '1') echo "selected"; ?> value="1">SI</option>
                                            <option <?php if($jubilado == '0') echo "selected"; ?> value="0">NO</option>
                                        </select>           

                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Nua / Cua Asignado</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="nua_cua_asignado" id="nua_cua_asignado" value="<?=$nua_cua_asignado;?>" required/>
                                </div>
                                </div>
                            </div><!--fin campo nua_cua_asignado-->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">AFP</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_tipoafp" id="cod_tipoafp"  class="selectpicker form-control form-control-sm " data-style="btn btn-info" required disabled>
                                            <?php while ($row = $statementtipos_afp->fetch()) { ?>
                                                <option <?php /*if($cod_tipoafp == $row["codigo"]) echo "selected";*/ if($row["codigo"] == 3) echo "selected";?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <label class="col-sm-2 col-form-label">Tipo de Aporte AFP</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_tipoaporteafp" id="cod_tipoaporteafp"  class="selectpicker form-control form-control-sm " data-style="btn btn-info" required>
                                            <?php while ($row = $statementtipos_aporteafp->fetch()) { ?>
                                                <option  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div><!--fin campo cod_tipoaporteafp-->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Nro. Seguro</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="number" name="nro_seguro" id="nro_seguro" required value="<?=$nro_seguro;?>"/>
                                </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Cod. Dependiente RC-IVA</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="codigo_dependiente" id="codigo_dependiente" required value="<?=$codigo_dependiente;?>"/>
                                </div>
                                </div>
                            </div><!--fin campo-->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Banco</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <select name="cod_banco" id="cod_banco" class="selectpicker form-control form-control-sm" data-style="btn btn-info" required>
                                        <option value=""></option>
                                        <?php 
                                        $queryBanc = "SELECT codigo,nombre from bancos where cod_estadoreferencial=1 order by 2";
                                        $stmtBanco = $dbh->query($queryBanc);
                                        while ($rowBanco = $stmtBanco->fetch()) { ?>
                                            <option <?php if($cod_banco == $rowBanco["codigo"]) echo "selected"; ?> value="<?=$rowBanco["codigo"];?>"><?=$rowBanco["nombre"];?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Cuenta Bancaria</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="number" name="cuenta_bancaria" id="cuenta_bancaria" required value="<?=$cuenta_bancaria;?>"/>
                                    </div>
                                </div>
                            </div><!--fin campo-->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Estado</label>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <select name="cod_estadopersonal"  class="selectpicker form-control form-control-sm " data-style="btn btn-info" required>
                                        <?php while ($row = $statementestados_personal->fetch()) { ?>
                                            <option <?php if($cod_estadopersonal == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                        </select>                  
                                    </div>
                                </div>

                                <label class="col-sm-2 col-form-label">Persona Contacto</label>
                                <div class="col-sm-6">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="persona_contacto" id="persona_contacto" required value="<?=$persona_contacto;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                </div>
                                </div>                        
                            </div><!--fin campo persona_contacto -->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Tipo De Persona Con Discapacidad</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="tipo_persona_discapacitado" id="tipo_persona_discapacitado"  class="selectpicker form-control form-control-sm " data-style="btn btn-info" >
                                            <option <?php if($cod_tipo_persona_discapacitado == 0) echo "selected"; ?> value="0"> NINGUNO</option>
                                            <option <?php if($cod_tipo_persona_discapacitado == 1) echo "selected"; ?> value="1">PERSONA CON DISCAPACIDAD</option>
                                            <option <?php if($cod_tipo_persona_discapacitado == 2) echo "selected"; ?> value="2"> TUTOR DE PERSONA CON DISCAPACIDAD</option>                            
                                        </select>
                                    </div>
                                </div>                                
                            </div><!--fin campo persona discapacidad -->

                            
                            <div id="contenedor_padre_discapacidad" >
                                <!-- <div id="div0">                                    
                                </div> -->
                               <!--  <div id="div1">                                
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">Nro Carnet Discapacidad</label>
                                        <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number" name="nro_carnet_discapacidad" id="nro_carnet_discapacidad" value="<?=$nro_carnet_discapacidad;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                        </div>
                                        </div>                                    
                                    </div>
                                </div> -->
                                <div id="div2">                                
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">Nro Carnet Discapacidad</label>
                                        <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-control" type="number" name="nro_carnet_discapacidad" id="nro_carnet_discapacidad" value="<?=$nro_carnet_discapacidad;?>"/>
                                        </div>
                                        </div>

                                        <label class="col-sm-2 col-form-label">Fecha Nacimiento De Persona Con Discapacidad</label>
                                        <div class="col-sm-4">
                                        <div class="form-group">
                                            <input class="form-control" type="date" name="fecha_nac_persona_dis" id="fecha_nac_persona_dis" value="<?=$fecha_nac_persona_dis;?>" />  
                                        </div>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                            
                            

                            <div class="row">
                                <label class="col-sm-2 col-form-label">Imagen</label>
                                <div class="col-md-4">
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
                                
                                <!--fin campo codigo --> 
                                <label class="col-sm-2 col-form-label">Nro casillero</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" name="nro_casillero" id="nro_casillero" value="<?=$nro_casillero;?>"/>
                                    </div>
                                </div> 
                            </div>
                            <!--fin campo imagen-->                    
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
</div>


<!-- <script type="text/javascript">
    $(document).ready(function(){
        $('#tipo_persona_discapacitado').on('change',function(){
            var selectVar='#div'+$(this).val();            
            $('#contenedor_padre_discapacidad').children('div').hide();
            $('#contenedor_padre_discapacidad').children(selectVar).show();
            $('#contenedor_padre_discapacidad').toggle();        
        });
    });
</script> -->

