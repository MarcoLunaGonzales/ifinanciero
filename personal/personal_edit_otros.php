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

if($codigo_item==1){?><!--oficina - area-->
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
                          <div class="col-sm-7">
                            <div class="form-group">
                                <select name="cod_uo" id="cod_uo" data-style="btn btn-info" onChange="ajaxAreaContabilizacionDetalle(this);" class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
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
                        </div>  
                        <div class="row">                                        
                          <label class="col-sm-2 col-form-label">Area</label>
                          <div class="col-sm-7">
                            <div class="form-group" >
                                <div id="div_contenedor_area">
                                    <!-- <select name="cod_area" id="cod_area"  data-style="btn btn-info" class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
                                        <option value=""></option>
                                        
                                    </select> -->
                                    <?php
                                    $sqlArea="SELECT cod_unidad,cod_area,(select a.nombre from areas a where a.codigo=cod_area) as nombre_area,(select a.abreviatura from areas a where a.codigo=cod_area) as abrev_area
                                    FROM areas_organizacion
                                    where cod_estadoreferencial=1 and cod_unidad=:cod_UO order by nombre_area";
                                    $stmtArea = $dbh->prepare($sqlArea);
                                    $stmtArea->bindParam(':cod_UO', $cod_unidadorganizacional);
                                    $stmtArea->execute();
                                    ?>
                                    <select name="cod_area" id="cod_area" data-style="btn btn-primary" class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
                                        <option ></option>
                                        <?php 
                                            while ($row = $stmtArea->fetch()){ 
                                        ?>
                                             <option <?=($cod_area==$row["cod_area"])?"selected":"";?> value="<?=$row["cod_area"];?>" data-subtext="<?=$row['cod_area'];?>"><?=$row["abrev_area"];?> - <?=$row["nombre_area"];?></option>
                                         <?php 
                                            } 
                                        ?>
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
if($codigo_item==2){?> <!--cargo-->
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
                                <div class="col-sm-7">
                                    <div class="form-group">
                                        <select name="cod_cargo" data-style="btn btn-info" required class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
                                            <?php 
                                            /*$queryCargos = "SELECT ca.cod_cargo,
                                            (select c.nombre from cargos c where c.codigo=ca.cod_cargo) as nombre_cargo
                                            from cargos_areasorganizacion ca,areas_organizacion ao
                                            where ca.cod_estadoreferencial=1 and ca.cod_areaorganizacion=ao.codigo and ao.cod_unidad=$cod_unidadorganizacional and ao.cod_area=$cod_area";*/
                                            $queryCargos="SELECT c.codigo as cod_cargo, c.nombre as nombre_cargo from cargos c where c.cod_estadoreferencial=1";
                                            $statementCargos = $dbh->query($queryCargos);
                                            while ($row = $statementCargos->fetch()) { ?>
                                                <option <?php if($cod_cargo == $row["cod_cargo"]) echo "selected"; ?> value="<?=$row["cod_cargo"];?>"><?=$row["nombre_cargo"];?></option>
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
if($codigo_item==3){?><!--Grado academico-->
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
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <select name="grado_academico" id="grado_academico"  data-style="btn btn-info" required class="selectpicker form-control form-control-sm" required data-show-subtext="true" data-live-search="true">
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
if($codigo_item==4){?><!--haber basico-->
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
                                    <input class="form-control" type="text" name="haber_basico" id="haber_basico" value="<?=$haber_basico;?>" required/>
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


<script>
    var cod_cargo_ant   = <?=empty($cod_cargo) ? '0' : $cod_cargo?>;
    var cod_cargo_nuevo = <?=empty($cod_cargo) ? '0' : $cod_cargo?>;
    var cod_personal    = <?=$codigo_personal?>;
    
    $(document).ready(function() {
        // Captura el cambio en el campo de selección de cargo
        $('select[name="cod_cargo"]').change(function() {
            cod_cargo_nuevo = $(this).val();
        });

        // Controla el envío del formulario
        $('#form1').submit(function(event) {
            event.preventDefault();
            // Verifica si ha habido cambios en el cargo
            if (cod_cargo_ant != cod_cargo_nuevo) {
                 // Pregunta al usuario si desea enviar un recordatorio
                Swal.fire({
                    title: 'Modificación de Cargo',
                    text: '¿Enviar recordatorio para revisión del manual?',
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, enviar',
                    cancelButtonText: 'No, continuar sin recordatorio',
                    allowOutsideClick: false  // Evita que se cierre al hacer clic fuera del cuadro de diálogo
                }).then((result) => {
                    if (result.value) {
                        Swal.fire({
                            title: 'Enviando notificación...',
                            onBeforeOpen: () => {
                                Swal.showLoading();
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        });
                        $.ajax({
                            type: "POST",
                            url: "sendEmailCambioCargo.php",
                            data: { 
                                cod_personal: cod_personal,
                                cod_cargo_ant: cod_cargo_ant,
                                cod_cargo_nuevo: cod_cargo_nuevo
                            },
                            success: function(response) {
                                // Cierra el Toast después de recibir la respuesta
                                Swal.close();

                                let resp = JSON.parse(response);
                                Swal.fire({
                                    type: 'success',
                                    title: 'Mensaje',
                                    text: resp.message,
                                    confirmButtonText: 'Aceptar'
                                });
                                $('#form1')[0].submit();
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                        });
                    }else{
                        $('#form1')[0].submit();
                    }
                });
            } else {
                $('#form1')[0].submit();
            }
        });
    });
</script>