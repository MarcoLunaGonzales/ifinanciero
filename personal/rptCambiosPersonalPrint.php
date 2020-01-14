<?php

error_reporting(-1);

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once  __DIR__.'/../fpdf_html.php';
require '../assets/phpqrcode/qrlib.php';

require_once '../layouts/bodylogin2.php';



$dbh = new Conexion();



/*
$gestion=$_POST["gestion"];
$nameGestion=nameGestion($gestion);
*/
//recibimos las variables
$unidadOrganizacional=$_POST["unidad_organizacional"];
$unidadOrgString=implode(",", $unidadOrganizacional);




$sqlUO="SELECT * from unidades_organizacionales where codigo in ($unidadOrgString)";  
$stmtuo = $dbh->prepare($sqlUO);
$stmtuo->execute();
$stmtuo->bindColumn('codigo', $codigo_uo);
$stmtuo->bindColumn('nombre', $nombre_uo);
?>


<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">            
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="float-right col-sm-2">
                    <h6 class="card-title">Exportar como:</h6>
                  </div>
                  <h4 class="card-title"> <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">  Reporte De Cambios Del Personal</h4>
                  <!--<h6 class="card-title">Gestion: <?=$nameGestion;?></h6>  -->
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">
                    <?php
                      while ($row = $stmtuo->fetch(PDO::FETCH_ASSOC)) {
                        $sqlPersonal="SELECT codigo,primer_nombre,paterno,materno from personal where cod_estadoreferencial=1 and cod_unidadorganizacional=$codigo_uo";
                        $stmtPersonal = $dbh->prepare($sqlPersonal);
                        $stmtPersonal->execute();
                        $stmtPersonal->bindColumn('codigo', $codigo_personal);
                        $stmtPersonal->bindColumn('paterno', $paterno);
                        $stmtPersonal->bindColumn('materno', $materno);
                        $stmtPersonal->bindColumn('primer_nombre', $primer_nombre);
                        $index=1;
                        while ($row = $stmtPersonal->fetch(PDO::FETCH_ASSOC)) {?>
                            <table class="table table-condensed">
                              <thead>
                                <tr >
                                  <th class="text-left"><b><?=$index;?><b></th>
                                  <th class="text-center"><b><?=$paterno;?> <?=$materno;?> <?=$primer_nombre;?><b></th>                                  
                                  <th class="text-center">Oficina: <b><?=$nombre_uo;?></b></th>
                                </tr>
                              
                                <tr >
                                  <th class="text-left" colspan="3"><b>Oficina:</b></th>
                                </tr>
                                <tr >
                                  <td class="text-left"><small>Fecha Cambio</small></td>
                                  <td class="text-left"><small>Oficina</small></td>
                                  <td class="text-left"><small>Area</small></td>
                                </tr>
                                <?php 
                                $sqlPersonaluo="SELECT 
                                  (select uo.nombre from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo,
                                  (select a.nombre from areas a where a.codigo=cod_area)as nombre_area,fecha_cambio
                                      from historico_uo_area where cod_personal=$codigo_personal";
                                  $stmtPersonaluo = $dbh->prepare($sqlPersonaluo);
                                  $stmtPersonaluo->execute();
                                  $stmtPersonaluo->bindColumn('nombre_uo', $nombre_uo);
                                  $stmtPersonaluo->bindColumn('nombre_area', $nombre_area);
                                  $stmtPersonaluo->bindColumn('fecha_cambio', $fecha_cambio);
                                  while ($row = $stmtPersonaluo->fetch(PDO::FETCH_ASSOC)) {?>
                                    <tr>
                                      <td class="text-left small"><?=$fecha_cambio;?></td>
                                      <td class="text-left small"><?=$nombre_uo;?></td>
                                      <td class="text-left small"><?=$nombre_area;?></td>
                                    </tr>
                                    <?php
                                  } ?>
                                  <!-- CArgo-->
                                  <tr >
                                  <th class="text-left" colspan="3"><b>Cargo:</b></th>
                                </tr>
                                <tr >
                                  <td class="text-left"><small>Fecha Cambio</small></td>
                                  <td class="text-left" colspan="2"><small>Cargo</small></td>
                                </tr>
                                <?php 
                                $sqlPersonaluo="SELECT 
                                  (SELECT c.nombre from cargos c where c.codigo=cod_cargo)as nombre_cargo,fecha_cambio
                                  from historico_cargos where cod_personal=$codigo_personal";
                                  $stmtPersonaluo = $dbh->prepare($sqlPersonaluo);
                                  $stmtPersonaluo->execute();                                  
                                  $stmtPersonaluo->bindColumn('nombre_cargo', $nombre_cargo);
                                  $stmtPersonaluo->bindColumn('fecha_cambio', $fecha_cambio);
                                  while ($row = $stmtPersonaluo->fetch(PDO::FETCH_ASSOC)) {?>
                                    <tr>
                                      <td class="text-left small"><?=$fecha_cambio;?></td>
                                      <td class="text-left small" colspan="2"><?=$nombre_cargo;?></td>
                                    </tr>
                                    <?php
                                  } ?>
                                  <!-- Grado acad-->
                                  <tr >
                                  <th class="text-left" colspan="3"><b>Grado Académico:</b></th>
                                </tr>
                                <tr >
                                  <td class="text-left"><small>Fecha Cambio</small></td>
                                  <td class="text-left" colspan="2"><small>Grado Académico</small></td>
                                </tr>
                                <?php 
                                $sqlPersonaluo="SELECT 
                                  (SELECT c.nombre from personal_grado_academico c where c.codigo=cod_grado_academico)as nombre_grado,fecha_cambio
                                  from historico_grado_acad where cod_personal=$codigo_personal";
                                  $stmtPersonaluo = $dbh->prepare($sqlPersonaluo);
                                  $stmtPersonaluo->execute();                                  
                                  $stmtPersonaluo->bindColumn('nombre_grado', $nombre_grado);
                                  $stmtPersonaluo->bindColumn('fecha_cambio', $fecha_cambio);
                                  while ($row = $stmtPersonaluo->fetch(PDO::FETCH_ASSOC)) {?>
                                    <tr>
                                      <td class="text-left small"><?=$fecha_cambio;?></td>
                                      <td class="text-left small" colspan="2"><?=$nombre_grado;?></td>
                                    </tr>
                                    <?php
                                  } ?>
                                  <!--haber basico-->
                                  <tr >
                                  <th class="text-left" colspan="3"><b>Haber Básico:</b></th>
                                </tr>
                                <tr >
                                  <td class="text-left"><small>Fecha Cambio</small></td>
                                  <td class="text-left" colspan="2"><small>Monto</small></td>
                                </tr>
                                <?php 
                                $sqlPersonaluo="SELECT haber_basico,fecha_cambio
                                  from historico_haber_basico where cod_personal=$codigo_personal";
                                  $stmtPersonaluo = $dbh->prepare($sqlPersonaluo);
                                  $stmtPersonaluo->execute();                                  
                                  $stmtPersonaluo->bindColumn('haber_basico', $haber_basico);
                                  $stmtPersonaluo->bindColumn('fecha_cambio', $fecha_cambio);
                                  while ($row = $stmtPersonaluo->fetch(PDO::FETCH_ASSOC)) {?>
                                    <tr>
                                      <td class="text-left small"><?=$fecha_cambio;?></td>
                                      <td class="text-left small" colspan="2"><?=$haber_basico;?></td>
                                    </tr>
                                    <?php
                                  } ?>
                              </tbody>
                            </table>
                            <?php 
                            $index++;
                        }
                      }
                    ?>                  
                    
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>