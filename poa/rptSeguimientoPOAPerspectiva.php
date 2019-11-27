<?php
set_time_limit(0);

require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsPOSIS.php';
require_once '../styles.php';

session_start();

$gestionX=$_POST["gestion"];
$mes=$_POST["mes"];
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


$sql="SELECT count(distinct(i.codigo))as contador, o.cod_perspectiva
    FROM objetivos o, indicadores i, indicadores_unidadesareas iua
  WHERE o.codigo=i.cod_objetivo and o.cod_estado=1 and i.cod_estado=1 and o.cod_tipoobjetivo=1 and o.cod_gestion='$globalGestion' and i.codigo=iua.cod_indicador";
if($globalAdmin==0){
  $sql.=" and iua.cod_area in ($globalAreasReports) and iua.cod_unidadorganizacional in ($globalUnidadesReports) ";
}
$sql.=" group by o.cod_perspectiva";
//echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('cod_perspectiva', $cod_perspectiva);
$stmt->bindColumn('contador', $contador);
$cantIndicadoresCli=0;
$cantIndicadoresFin=0;
$cantIndicadoresIns=0;
$cantIndicadoresProc=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  if($cod_perspectiva==3){
    $cantIndicadoresCli=$contador;
  }
  if($cod_perspectiva==4){
    $cantIndicadoresFin=$contador;
  }
  if($cod_perspectiva==1){
    $cantIndicadoresIns=$contador;
  }
  if($cod_perspectiva==2){
    $cantIndicadoresProc=$contador;
  }
}
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



          <div class="row">
            <div class="col-lg-5 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <a href="rptSeguimientoPOA.php?gestion=<?=$gestionX;?>&mes=<?=$mes;?>&perspectiva=3" target="_BLANK">
                      <i class="material-icons">person_pin</i>
                    </a>
                  </div>
                  <p class="card-category">Perspectiva</p>
                  <h3 class="card-title">Clientes</h3>
                </div>
                <div class="card-footer">
                  <div class="card-category">
                    <i class="material-icons text-danger">apps</i>
                    # Indicadores: <?=$cantIndicadoresCli;?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-rose card-header-icon">
                  <div class="card-icon">
                    <a href="rptSeguimientoPOA.php?gestion=<?=$gestionX;?>&mes=<?=$mes;?>&perspectiva=4" target="_BLANK">
                      <i class="material-icons">attach_money</i>
                    </a>
                  </div>
                  <p class="card-category">Perspectiva</p>
                  <h3 class="card-title">Financiera</h3>
                </div>
                <div class="card-footer">
                  <div class="card-category">
                    <i class="material-icons">apps</i>
                    # Indicadores: <?=$cantIndicadoresFin;?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                    <a href="rptSeguimientoPOA.php?gestion=<?=$gestionX;?>&mes=<?=$mes;?>&perspectiva=1" target="_BLANK">
                      <i class="material-icons">store</i>
                    </a>
                  </div>
                  <p class="card-category">Perspectiva</p>
                  <h3 class="card-title">Institucional</h3>
                </div>
                <div class="card-footer">
                  <div class="card-category">
                    <i class="material-icons">apps</i>
                    # Indicadores: <?=$cantIndicadoresIns;?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <a href="rptSeguimientoPOA.php?gestion=<?=$gestionX;?>&mes=<?=$mes;?>&perspectiva=2" target="_BLANK">
                      <i class="material-icons">blur_on</i>
                    </a>
                  </div>
                  <p class="card-category">Perspectivas</p>
                  <h3 class="card-title">Procesos Internos</h3>
                </div>
                <div class="card-footer">
                  <div class="card-category">
                    <i class="material-icons">blur_on</i>
                    # Indicadores: <?=$cantIndicadoresProc;?>
                  </div>
                </div>
              </div>
            </div>
          </div>


        </div>
      </div>
    </div>  
  </div>
</div>

