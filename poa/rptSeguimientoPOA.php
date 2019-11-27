<?php
set_time_limit(0);

require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsPOSIS.php';
require_once '../styles.php';

session_start();

$gestionX=$_GET["gestion"];
$mes=$_GET["mes"];
$perspectiva=$_GET["perspectiva"];

$anio=nameGestion($gestionX);

$dbh = new Conexion();
$moduleName="Seguimiento POA - $mes $anio";

//DEFINIMOS VARIABLES DE SESION
//echo $fondoArray."fondoArray";
$_SESSION['anioTemporal']=$anio;
$_SESSION['mesTemporal']=$mes;


$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$globalUnidadesReports=$_SESSION["globalUnidadesReports"];
$globalAreasReports=$_SESSION["globalAreasReports"];

$globalAdmin=$_SESSION["globalAdmin"];
$globalUserPON=$_SESSION["globalUserPON"];


$sql="SELECT (select p.nombre from perspectivas p where p.codigo=o.cod_perspectiva)as perspectiva, o.codigo, o.abreviatura, o.descripcion, (SELECT g.nombre from gestiones g WHERE g.codigo=o.cod_gestion) as gestion, i.nombre as nombreindicador, i.codigo as codigoindicador
    FROM objetivos o, indicadores i, indicadores_unidadesareas iua
  WHERE o.codigo=i.cod_objetivo and o.cod_estado=1 and i.cod_estado=1 and o.cod_tipoobjetivo=1 and o.cod_gestion='$globalGestion' and i.codigo=iua.cod_indicador and o.cod_perspectiva in ($perspectiva)";
if($globalAdmin==0){
  $sql.=" and iua.cod_area in ($globalAreasReports) and iua.cod_unidadorganizacional in ($globalUnidadesReports) ";
}
if($globalUserPON==1){
  $sql.=" union SELECT (select p.nombre from perspectivas p where p.codigo=o.cod_perspectiva)as perspectiva, o.codigo, o.abreviatura, o.descripcion, (SELECT g.nombre from gestiones g WHERE g.codigo=o.cod_gestion) as gestion, i.nombre as nombreindicador, i.codigo as codigoindicador FROM objetivos o, indicadores i, indicadores_unidadesareas iua WHERE o.codigo=i.cod_objetivo and o.cod_estado=1 and i.cod_estado=1 and o.cod_tipoobjetivo=1 and o.cod_gestion='$globalGestion' 
    and i.codigo=iua.cod_indicador and i.codigo='$codigoIndicadorPON' ";
}
$sql.=" group by i.codigo ORDER BY perspectiva, abreviatura, i.nombre";

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
                    <th class="text-center" data-orderable="false">Seguimiento</th>
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
                    <a href='../graficos/rptPOA.php?tipo=1&codigo=<?=$codigoIndicador;?>&gestion=<?=$gestionX;?>&anio=<?=$anio;?>&mes=<?=$mes;?>' rel="tooltip" target="_BLANK" title="Ver Reporte" class="<?=$buttonDetail;?>">
                        <i class="material-icons">assessment</i>
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

