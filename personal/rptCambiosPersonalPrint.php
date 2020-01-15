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
// $unidadOrganizacional=$_POST["unidad_organizacional"];
// $unidadOrgString=implode(",", $unidadOrganizacional);
// $sqlUO="SELECT * from unidades_organizacionales where codigo in ($unidadOrgString)";  
// $stmtuo = $dbh->prepare($sqlUO);
// $stmtuo->execute();
// $stmtuo->bindColumn('codigo', $codigo_uo);
// $stmtuo->bindColumn('nombre', $nombre_uo);
$cod_uo=$_POST["cod_uo"];
$cod_personal=$_POST["cod_personal"];
$sqluo="SELECT nombre,abreviatura from unidades_organizacionales where codigo=$cod_uo";
$stmtuo=$dbh->prepare($sqluo);
$stmtuo->execute();
$resultUO=$stmtuo->fetch();
$nombre_uo=$resultUO['nombre'];
$abreviatura_uo=$resultUO['abreviatura'];

?>


<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">            
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="float-right col-sm-2">
                    <!-- <h6 class="card-title">Exportar como:</h6> -->
                  </div>
                  <h4 class="card-title"> <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">  Reporte De Cambios Del Personal</h4>
                  <!--<h6 class="card-title">Gestion: <?=$nameGestion;?></h6>  -->
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">
                    <?php
                      
                        $sqlPersonal="SELECT codigo,primer_nombre,paterno,materno,haber_basico,
                        (select c.nombre from cargos c where c.codigo=cod_cargo) as nombre_cargo,
                        (SELECT ga.nombre from personal_grado_academico ga where ga.codigo = cod_grado_academico)as nombre_grado 
                        from personal where cod_estadoreferencial=1 and codigo=$cod_personal and cod_unidadorganizacional=$cod_uo";
                        $stmtPersonal = $dbh->prepare($sqlPersonal);
                        $stmtPersonal->execute();
                        $stmtPersonal->bindColumn('codigo', $codigo_personal);
                        $stmtPersonal->bindColumn('paterno', $paterno);
                        $stmtPersonal->bindColumn('materno', $materno);
                        $stmtPersonal->bindColumn('primer_nombre', $primer_nombre);
                        $stmtPersonal->bindColumn('nombre_cargo', $nombre_cargo);
                        $stmtPersonal->bindColumn('haber_basico', $haber_basico);
                        $stmtPersonal->bindColumn('nombre_grado', $nombre_grado_academico);
                        $index=1;
                        while ($row = $stmtPersonal->fetch(PDO::FETCH_ASSOC)) {?>
                            <table class="table table-condensed">
                              <thead>
                                <tr >
                                  <th class="text-center"><b><?=$paterno;?> <?=$materno;?> <?=$primer_nombre;?><b></th>
                                  <th class="text-center">Oficina: <b><?=$nombre_uo;?></b></th>
                                  <th class="text-center">Cargo: <b><?=$nombre_cargo?></b></th>
                                </tr>
                                <tr >
                                  <th class="text-center">Haber Básico: <b><?=$haber_basico?></b></th>
                                  <th class="text-center">Grado Académico: <b><?=$nombre_grado_academico?></b></th>
                                </tr>
                                <tr>
                                  <th class="text-left"><small><b>Fecha Cambio</b></small></th>
                                  <th class="text-left"><small><b>Tipo</b></small></th>
                                  <th class="text-left"><small><b>Descripción</b></small></th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php 
                                $sqlPersonaluo="SELECT tipo,descripcion,fecha_cambio
                                      from historico_cambios_personal where cod_personal=$codigo_personal order by fecha_cambio desc";
                                  $stmtPersonaluo = $dbh->prepare($sqlPersonaluo);
                                  $stmtPersonaluo->execute();
                                  $stmtPersonaluo->bindColumn('tipo', $tipo);
                                  $stmtPersonaluo->bindColumn('descripcion', $descripcion);
                                  $stmtPersonaluo->bindColumn('fecha_cambio', $fecha_cambio);
                                  while ($row = $stmtPersonaluo->fetch(PDO::FETCH_ASSOC)) {?>
                                    <tr>
                                      <td class="text-left small"><?=$fecha_cambio;?></td>
                                      <td class="text-left small"><?=$tipo?></td>
                                      <td class="text-left small"><?=$descripcion?></td>
                                    </tr>
                                    <?php
                                  } ?>
                              </tbody>
                            </table>
                            <?php 
                            $index++;
                        }
                      
                    ?>                  
                    
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>