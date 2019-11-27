<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();

//SACAMOS LA CONFIGURACION PARA REDIRECCIONAR EL PON
$codigoIndicadorPON=obtenerCodigoPON();

$table="poa";
$moduleName="POA Programacion";

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$globalAdmin=$_SESSION["globalAdmin"];
$globalUserPON=$_SESSION["globalUserPON"];


$sql="SELECT (select p.nombre from perspectivas p where p.codigo=o.cod_perspectiva)as perspectiva, o.codigo, o.abreviatura, o.descripcion, (SELECT g.nombre from gestiones g WHERE g.codigo=o.cod_gestion) as gestion, i.nombre as nombreindicador, i.codigo as codigoindicador
    FROM objetivos o, indicadores i, indicadores_unidadesareas iua
  WHERE o.codigo=i.cod_objetivo and o.cod_estado=1 and i.cod_estado=1 and o.cod_tipoobjetivo=1 and o.cod_gestion='$globalGestion' and i.codigo=iua.cod_indicador";
if($globalAdmin==0){
  $sql.=" and iua.cod_area in ($globalArea) and iua.cod_unidadorganizacional in ($globalUnidad) ";
}
if($globalUserPON==1){
  $sql.=" union SELECT (select p.nombre from perspectivas p where p.codigo=o.cod_perspectiva)as perspectiva, o.codigo, o.abreviatura, o.descripcion, (SELECT g.nombre from gestiones g WHERE g.codigo=o.cod_gestion) as gestion, i.nombre as nombreindicador, i.codigo as codigoindicador FROM objetivos o, indicadores i, indicadores_unidadesareas iua WHERE o.codigo=i.cod_objetivo and o.cod_estado=1 and i.cod_estado=1 and o.cod_tipoobjetivo=1 and o.cod_gestion='$globalGestion' 
    and i.codigo=iua.cod_indicador and i.codigo='$codigoIndicadorPON' ";
}
$sql.=" group by i.codigo ORDER BY perspectiva, abreviatura";
//echo $sql;
$stmt = $dbh->prepare($sql);
// Ejecutamos
$stmt->execute();

// bindColumn

$stmt->bindColumn('perspectiva', $perspectiva);
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('descripcion', $descripcion);
$stmt->bindColumn('gestion', $gestion);
$stmt->bindColumn('nombreindicador', $nombreIndicador);
$stmt->bindColumn('codigoindicador', $codigoIndicador);



?>

<div class="content">
	<div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon">
              <i class="material-icons">assignment</i>
            </div>
            <h4 class="card-title"><?=$moduleName?></h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped" id="tablePaginator">
                <thead>
                  <tr>
                    <th class="text-center">-</th>
                    <th data-orderable="false">Gestion</th>
                    <th>Perspectiva</th>
                    <th>Obj. Est.</th>
                    <th>Ind. Estrategico</th>
                    <th class="text-center" data-orderable="false">Actividades</th>
                    <th class="text-center" data-orderable="false">POAI</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  $index=1;
                	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                ?>
                  <tr>
                    <td class="text-center"><?=$index;?></td>
                    <td><?=$gestion;?></td>
                    <td><?=$perspectiva;?></td>
                    <td><?=$abreviatura;?></td>
                    <td><?=$nombreIndicador;?></td>
                    <td class="text-center">
                      <a href='index.php?opcion=listActividadesPOA&codigo=<?=$codigoIndicador;?>&codigoPON=<?=$codigoIndicadorPON;?>&area=0&unidad=0' rel="tooltip" title="Ver Actividades" class="<?=$buttonDetail;?>">
                        <i class="material-icons">description</i>
                      </a>
                    </td>
                    <?php
                    if($globalAdmin==1){
                    ?>
                    <td class="text-center">
                      <button class="<?=$buttonDetail;?>" data-toggle="modal" data-target="#myModal" onClick="ajaxCargosPOAI(<?=$codigoIndicador?>);" title="Registrar Cargos POAI"> 
                          <i class="material-icons">settings</i>
                      </button>
                    </td>
                    <?php
                    }else{
                    ?>
                    <td class="text-center">
                      -
                    </td>
                    <?php
                    }
                    ?>
                  </tr>
                  <?php
          				$index++;
          				}
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>


    <!-- PROPIEDAD INDICADOR -->
<form id="form1" class="form-horizontal" action="poa/saveConfigCargos.php" method="post">
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Configuración Propiedad Cargos para POAI</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
            <i class="material-icons">clear</i>
          </button>
        </div>
        <div class="modal-body" id="modal-body">
        </div>
          <div class="modal-footer">
            <button type="submit" class="<?=$button;?>">Guardar</button>
            <button type="button" class="btn btn-danger btn-link" data-dismiss="modal">Cerrar</button>  
          </div>
      </div>
    </div>
  </div>
</form>
  <!--  End Modal -->