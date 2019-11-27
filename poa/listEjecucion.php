<?php

require_once 'conexion.php';
require_once 'styles.php';

$dbh = new Conexion();


//SACAMOS LA CONFIGURACION PARA REDIRECCIONAR EL PON
$stmt = $dbh->prepare("SELECT valor_configuracion FROM configuraciones where id_configuracion=6");
$stmt->execute();
$codigoIndicadorPON=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoIndicadorPON=$row['valor_configuracion'];
}

$table="poa";
$moduleName="POA Ejecución";

if($area!=0 && $unidad!=0){
  $_SESSION["globalAreaEjecucion"]=$area;
  $_SESSION["globalUnidadEjecucion"]=$unidad;
}


$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];
$globalUserPON=$_SESSION["globalUserPON"];

//SACAMOS LAS FECHAS DE REGISTRO DEL MES EN CURSO
$fechaActual=date("Y-m-d");
$sqlFechaEjecucion="SELECT f.mes, f.anio, DATE_FORMAT(f.fecha_fin, '%d/%m')fecha_fin from fechas_registroejecucion f 
where f.fecha_inicio<='$fechaActual' and f.fecha_fin>='$fechaActual'";
//echo $sqlFechaEjecucion;
$stmt = $dbh->prepare($sqlFechaEjecucion);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codMesX=$row['mes'];
  $codAnioX=$row['anio'];
  $fechaFinRegistroX=$row['fecha_fin'];
}
//FIN FECHAS


// Preparamos
$sql="SELECT (select p.nombre from perspectivas p where p.codigo=o.cod_perspectiva)as perspectiva, o.codigo, o.abreviatura, o.descripcion, (SELECT g.nombre from gestiones g WHERE g.codigo=o.cod_gestion) as gestion, i.nombre as nombreindicador, i.codigo as codigoindicador
  FROM objetivos o, indicadores i, indicadores_unidadesareas iua
WHERE o.codigo=i.cod_objetivo and o.cod_estado=1 and i.cod_estado=1 and o.cod_tipoobjetivo=1 and o.cod_gestion='$globalGestion' and i.codigo=iua.cod_indicador ";
//if($globalAdmin==0){
  $sql.=" and iua.cod_area in ($area) and iua.cod_unidadorganizacional in ($unidad)";
//}
if($globalUserPON==1){
  $sql.=" union ";
  $sql.=" SELECT (select p.nombre from perspectivas p where p.codigo=o.cod_perspectiva)as perspectiva, o.codigo, o.abreviatura, o.descripcion, (SELECT g.nombre from gestiones g WHERE g.codigo=o.cod_gestion) as gestion, i.nombre as nombreindicador, i.codigo as codigoindicador
  FROM objetivos o, indicadores i, indicadores_unidadesareas iua WHERE o.codigo=i.cod_objetivo and o.cod_estado=1 and i.cod_estado=1 and o.cod_tipoobjetivo=1 and o.cod_gestion='$globalGestion' and i.codigo=iua.cod_indicador and i.codigo in ($codigoIndicadorPON)";  
}
$sql.=" GROUP BY i.codigo ORDER BY perspectiva, abreviatura, nombreindicador";
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
                  <h4 class="card-title"><?=$moduleName;?></h4>
                  <h6 class="card-title">Mes Ejecucion: <?=$codMesX;?> Fecha Limite Registro: <?=$fechaFinRegistroX;?></h6>
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
                            <a href='index.php?opcion=listActividadesPOAEjecucion&codigo=<?=$codigoIndicador;?>&codigoPON=<?=$codigoIndicadorPON;?>&unidad=0&area=0' rel="tooltip" title="Ver Actividades" class="<?=$buttonDetail;?>">
                              <i class="material-icons">description</i>
                            </a>
                          </td>
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



<!-- Classic Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Definir Area/Unidad para la Ejecucion</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="text-align:center;">
      <select class="selectpicker" name="unidadModal" id="unidadModal" data-style="<?=$comboColor;?>" required>
        <option disabled selected value="">Unidad</option>
        <?php
        $sqlAreas="SELECT i.cod_indicador, u.codigo as codigoUnidad, u.nombre as nombreUnidad, u.abreviatura as abrevUnidad from indicadores_unidadesareas i, unidades_organizacionales u where i.cod_unidadorganizacional=u.codigo";
        if($globalAdmin==0){
          $sqlAreas.=" and i.cod_unidadorganizacional in ($globalUnidad)";
        }
        $sqlAreas.=" GROUP BY u.codigo order by 3";
        $stmt = $dbh->prepare($sqlAreas);
      $stmt->execute();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $codigoU=$row['codigoUnidad'];
        $nombreU=$row['nombreUnidad'];
        $abrevU=$row['abrevUnidad'];
      ?>
      <option value="<?=$codigoU;?>" data-subtext="<?=$nombreU;?>"><?=$abrevU;?></option>
      <?php 
      }
        ?>
      </select>

      <select class="selectpicker" name="areaModal" id="areaModal" data-style="<?=$comboColor;?>" required>
        <option disabled selected value="">Area</option>
        <?php
        $sqlAreas="SELECT i.cod_indicador, a.codigo as codigoArea, a.nombre as nombreArea, a.abreviatura as abrevArea from indicadores_unidadesareas i, areas a where i.cod_area=a.codigo ";
        if($globalAdmin==0){
          $sqlAreas.=" and i.cod_area in ($globalArea) ";
        }
        $sqlAreas.=" GROUP BY a.codigo order by 3";
        $stmt = $dbh->prepare($sqlAreas);
      $stmt->execute();
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $codigoA=$row['codigoArea'];
        $nombreA=$row['nombreArea'];
        $abrevA=$row['abrevArea'];
      ?>
      <option value="<?=$codigoA;?>" data-subtext="<?=$nombreA?>"><?=$abrevA;?></option>
      <?php 
      }
        ?>
      </select> 
      </div>
      <div class="modal-footer">
        <button type="button" class="<?=$button;?>" onclick="enviarDefinicionAreaUnidad();">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!--  End Modal -->


<?php
if($area==0 && $unidad==0){
?>
<script>
  verificaModalArea();
</script>
<?php
}
?>