<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'rrhh/configModule.php';


//$dbh = new Conexion();
$dbh = new Conexion();
$codigo=$codigo;
$stmt = $dbh->prepare("SELECT codigo,(CONCAT_WS(' ',paterno,materno,primer_nombre)) as nombre_personal,cod_estadopersonal
     FROM personal where codigo =:codigo");
$stmt->bindParam(':codigo',$codigo);
$stmt->execute();
//resultados
$result = $stmt->fetch();

$queryestados_personal = "SELECT codigo,nombre from estados_personal where cod_estadoreferencial=1";
$statementestados_personal = $dbh->query($queryestados_personal);

?>

<div class="content">
    <div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
                <form id="form1" action="<?=$urlSavePersonalRetirado;?>" method="post" enctype="multipart/form-data">                    
                    <div class="card">
                        <div class="card-header <?=$colorCard;?> card-header-text">
                            <div class="card-text">
                              <h4 class="card-title">Editar Personal</h4>
                            </div>
                        </div>                        
                        <div class="card-body">                    
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Código Personal</label>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <input class="form-control" name="codigo" id="codigo" value="<?=$result['codigo'];?>" readonly="readonly"/>
                                    </div>
                                </div>                            
                            </div><!--fin campo codigo --> 
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Nombre Personal</label>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <input class="form-control" name="nombre_personal" id="nombre_personal" value="<?=$result['nombre_personal'];?>" readonly="readonly"/>
                                    </div>
                                </div>                            
                            </div><!--fin campo codigo --> 
               
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Estado</label>
                                <div class="col-sm-8">
                                <div class="form-group">
                                <select name="cod_estadopersonal"  class="selectpicker form-control form-control-sm" data-style="btn btn-info" required>
                                <?php while ($row = $statementestados_personal->fetch()) { ?>
                                    <option <?php if($result['cod_estadopersonal'] == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                <?php } ?>
                                </select>                  
                                </div>
                                </div>
                            </div><!--fin campo cod_tipoaporteafp-->
                            
                        </div>
                        <div class="card-footer ml-auto mr-auto">
                            <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                            <a href="<?=$urlListPersonalRetirado;?>" class="<?=$buttonCancel;?>">Volver</a>
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

