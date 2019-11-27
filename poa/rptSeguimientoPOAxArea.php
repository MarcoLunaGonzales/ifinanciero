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
$moduleName="Indicadores x Area - $mes $anio";

//DEFINIMOS VARIABLES DE SESION
//echo $fondoArray."fondoArray";
$_SESSION['anioTemporal']=$anio;
$_SESSION['mesTemporal']=$mes;


$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$globalAdmin=$_SESSION["globalAdmin"];
$globalUserPON=$_SESSION["globalUserPON"];


$codArea="13";
$nombreArea=nameArea($codArea);

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
            <div class="col-lg-4 col-md-4 col-sm-4">
              <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <a href="../rpt_indicadores/rptSEC.php?gestion=<?=$gestionX;?>&anio=<?=$anio;?>&mes=<?=$mes;?>&codArea=<?=$codArea;?>" target="_BLANK">
                      <i class="material-icons">person_pin</i>
                    </a>
                  </div>
                  <p class="card-category">Area</p>
                  <h3 class="card-title"><?=$nombreArea;?></h3>
                </div>
                <div class="card-footer">
                  <div class="card-category">
                    <i class="material-icons text-danger">apps</i>
                  </div>
                </div>
              </div>
            </div>

            <?php
            $codArea="39";
            $nombreArea=nameArea($codArea);
            ?>
            <div class="col-lg-4 col-md-4 col-sm-4">
              <div class="card card-stats">
                <div class="card-header card-header-rose card-header-icon">
                  <div class="card-icon">
                    <a href="../rpt_indicadores/rptTCP.php?gestion=<?=$gestionX;?>&anio=<?=$anio;?>&mes=<?=$mes;?>&codArea=<?=$codArea;?>" target="_BLANK">
                      <i class="material-icons">attach_money</i>
                    </a>
                  </div>
                  <p class="card-category">Area</p>
                  <h3 class="card-title"><?=$nombreArea;?></h3>
                </div>
                <div class="card-footer">
                  <div class="card-category">
                    <i class="material-icons">apps</i>
                  </div>
                </div>
              </div>
            </div>

            <?php
            $codArea="38";
            $nombreArea=nameArea($codArea);
            ?>
            <div class="col-lg-4 col-md-4 col-sm-4">
              <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                    <a href="../rpt_indicadores/rptTCP.php?gestion=<?=$gestionX;?>&anio=<?=$anio;?>&mes=<?=$mes;?>&codArea=<?=$codArea;?>" target="_BLANK">
                      <i class="material-icons">store</i>
                    </a>
                  </div>
                  <p class="card-category">Area</p>
                  <h3 class="card-title"><?=$nombreArea;?></h3>
                </div>
                <div class="card-footer">
                  <div class="card-category">
                    <i class="material-icons">apps</i>
                  </div>
                </div>
              </div>
            </div>

            <?php
            $codArea="40";
            $nombreArea=nameArea($codArea);
            ?>
            <div class="col-lg-4 col-md-4 col-sm-4">
              <div class="card card-stats">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <a href="../rpt_indicadores/rptTCP.php?gestion=<?=$gestionX;?>&anio=<?=$anio;?>&mes=<?=$mes;?>&codArea=<?=$codArea;?>" target="_BLANK">
                      <i class="material-icons">blur_on</i>
                    </a>
                  </div>
                  <p class="card-category">Area</p>
                  <h3 class="card-title"><?=$nombreArea;?></h3>
                </div>
                <div class="card-footer">
                  <div class="card-category">
                    <i class="material-icons">blur_on</i>
                  </div>
                </div>
              </div>
            </div>

          <?php
            $codArea="12";
            $nombreArea=nameArea($codArea);
            ?>
            <div class="col-lg-4 col-md-4 col-sm-4">
              <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <a href="rptSeguimientoPOA.php?gestion=<?=$gestionX;?>&mes=<?=$mes;?>&perspectiva=2" target="_BLANK">
                      <i class="material-icons">blur_on</i>
                    </a>
                  </div>
                  <p class="card-category">Area</p>
                  <h3 class="card-title"><?=$nombreArea;?></h3>
                </div>
                <div class="card-footer">
                  <div class="card-category">
                    <i class="material-icons">blur_on</i>
                  </div>
                </div>
              </div>
            </div>


          <?php
            $codArea="11";
            $nombreArea=nameArea($codArea);
            ?>
            <div class="col-lg-4 col-md-4 col-sm-4">
              <div class="card card-stats">
                <div class="card-header card-header-primary card-header-icon">
                  <div class="card-icon">
                    <a href="../rpt_indicadores/rptOI.php?gestion=<?=$gestionX;?>&anio=<?=$anio;?>&mes=<?=$mes;?>&codArea=<?=$codArea;?>" target="_BLANK">
                      <i class="material-icons">blur_on</i>
                    </a>
                  </div>
                  <p class="card-category">Area</p>
                  <h3 class="card-title"><?=$nombreArea;?></h3>
                </div>
                <div class="card-footer">
                  <div class="card-category">
                    <i class="material-icons">blur_on</i>
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
</div>

