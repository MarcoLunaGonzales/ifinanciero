<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();


$stmt = $dbh->prepare(" SELECT *, (select c.nombre from cargos c where c.codigo=cod_cargo)as xcargo,
 (select uo.nombre from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional)as xuonombre,
 (select a.nombre from areas a where a.codigo=cod_area)as xarea,
 (select g.nombre from tipos_genero g where g.codigo=cod_genero)as xgenero,
 (select ep.nombre from estados_personal ep where ep.codigo=cod_estadopersonal)as xestado,
 (select tafp.nombre from tipos_afp tafp where tafp.codigo=cod_tipoafp)as xtipoafp,
 (select taafp.nombre from tipos_aporteafp taafp where taafp.codigo=cod_tipoaporteafp) as xtipos_aporteafp,
 (select tp.nombre from tipos_personal tp where tp.codigo=cod_tipopersonal)as xcod_tipopersonal
 
 from personal_datos
 where cod_estadoreferencial=1
 ");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('ci', $ci);
$stmt->bindColumn('ci_lugar_emision', $ci_lugar_emision);

$stmt->bindColumn('fecha_nacimiento', $fecha_nacimiento);
$stmt->bindColumn('cod_cargo', $cod_cargo);
$stmt->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('jubilado', $jubilado);
$stmt->bindColumn('cod_genero', $cod_genero);
$stmt->bindColumn('cod_tipopersonal', $cod_tipopersonal);

$stmt->bindColumn('haber_basico', $haber_basico);
$stmt->bindColumn('paterno', $paterno);
$stmt->bindColumn('materno', $materno);
$stmt->bindColumn('apellido_casada', $apellido_casada);
$stmt->bindColumn('primer_nombre', $primer_nombre);
$stmt->bindColumn('otros_nombres', $otros_nombres);
$stmt->bindColumn('nua_cua_asignado', $nua_cua_asignado);
$stmt->bindColumn('direccion', $direccion);
$stmt->bindColumn('cod_tipoafp', $cod_tipoafp);
$stmt->bindColumn('cod_tipoaporteafp', $tipos_aporteafp);
$stmt->bindColumn('nro_seguro', $nro_seguro);
$stmt->bindColumn('cod_estadopersonal', $cod_estadopersonal);
$stmt->bindColumn('created_at', $created_at);
$stmt->bindColumn('created_by', $created_by);
$stmt->bindColumn('modified_at', $modified_at);
$stmt->bindColumn('modified_by', $modified_by);
$stmt->bindColumn('xcargo', $xcargo);
$stmt->bindColumn('xuonombre', $xuonombre);
$stmt->bindColumn('xarea', $xarea);
$stmt->bindColumn('xgenero', $xgenero);
$stmt->bindColumn('xestado', $xestado);
$stmt->bindColumn('xtipoafp', $xtipoafp);
$stmt->bindColumn('xcod_tipopersonal', $xcod_tipopersonal);
$stmt->bindColumn('xtipos_aporteafp', $xtipos_aporteafp);

?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"><?=$nombrePluralPersonal?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                    <thead>
    <tr>
        <th>Ci</cIte></th>
        <th>Cargo</th>
        <th>U.O.</th>
        <th>Area</th>
        <th>Genero</th>
        <th>Tipo Personal</th>
        <th>Basico</th>
        <th>Paterno</th>
        <th>Materno</th>
        <th>Nombre</th>
        
        <th>Afp</th>
        <th>Estado</th>
        
        <th></th>
    </tr>
</thead>
<tbody>
<?php $index=1;
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
    <tr>
        <td><?=$ci;?> <?=$ci_lugar_emision;?></td>
        <td><?=$xcargo;?></td>
        <td><?=$xuonombre;?></td>
        <td><?=$xarea;?></td>
        <td><?=$xgenero;?></td>
        <td><?=$xcod_tipopersonal;?></td>
        <td><?=$haber_basico;?></td>
        <td><?=$paterno;?></td>
        <td><?=$materno;?></td>
        <td><?=$primer_nombre;?></td>
        <td><?=$xtipoafp;?></td>
        <td><?=$xestado;?></td>
        
        <td class="td-actions text-right">
        <?php
          if($globalAdmin==1){
        ?>
          <a href='<?=$urlFormPersonal;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
            <i class="material-icons"><?=$iconEdit;?></i>
          </a>
          <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeletePersonal;?>&codigo=<?=$codigo;?>')">
            <i class="material-icons"><?=$iconDelete;?></i>
          </button>
          <?php
            }
          ?>
        
        </td>
    </tr>
<?php $index++; } ?>
</tbody>
                    
                    </table>
                  </div>
                </div>
              </div>
              <?php





              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormPersonal;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
