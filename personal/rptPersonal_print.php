<?php

error_reporting(-1);

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once  __DIR__.'/../fpdf_html.php';
require_once '../layouts/bodylogin2.php';


$dbh = new Conexion();


$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

/*
$gestion=$_POST["gestion"];
$nameGestion=nameGestion($gestion);
*/
//recibimos las variables
$unidadOrganizacional=$_POST["unidad_organizacional"];
$areas=$_POST["areas"];

$unidadOrgString=implode(",", $unidadOrganizacional);
$areaString=implode(",", $areas);

// echo $areaString;
$stringUnidades="";
foreach ($unidadOrganizacional as $valor ) {    
    $stringUnidades.=" ".abrevUnidad($valor)." ";
}
$stringAreas="";
foreach ($areas as $valor ) {    
    $stringAreas.=" ".abrevArea($valor)." ";
}


$sql="SELECT codigo,cod_tipo_identificacion,identificacion,cod_lugar_emision,fecha_nacimiento,cod_cargo,cod_unidadorganizacional,cod_area,haber_basico,CONCAT_WS(' ',paterno,materno,primer_nombre)as personal,cod_tipoafp,celular,telefono,email,email_empresa,ing_planilla from personal  where cod_estadopersonal=1 and cod_estadoreferencial=1 and cod_area in ($areaString) and cod_unidadorganizacional in ($unidadOrgString) order by paterno ";  

//echo $sql;

$stmtActivos = $dbh->prepare($sql);
$stmtActivos->execute();

// bindColumn
$stmtActivos->bindColumn('codigo', $codigo);
$stmtActivos->bindColumn('cod_tipo_identificacion', $cod_tipo_identificacion);
$stmtActivos->bindColumn('identificacion', $identificacion);
$stmtActivos->bindColumn('cod_lugar_emision', $cod_lugar_emision);
$stmtActivos->bindColumn('fecha_nacimiento', $fecha_nacimiento);
$stmtActivos->bindColumn('cod_cargo', $cod_cargo);
$stmtActivos->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmtActivos->bindColumn('cod_area', $cod_area);
$stmtActivos->bindColumn('haber_basico', $haber_basico);
$stmtActivos->bindColumn('personal', $personal);
$stmtActivos->bindColumn('celular', $celular);
$stmtActivos->bindColumn('telefono', $telefono);
$stmtActivos->bindColumn('email', $email);
$stmtActivos->bindColumn('email_empresa', $email_empresa);
$stmtActivos->bindColumn('ing_planilla', $ing_planilla);
$stmtActivos->bindColumn('cod_tipoafp', $cod_tipoafp);
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
                  <h4 class="card-title"> 
                    <img  class="card-img-top"  src="../marca.png" style="width:100%; max-width:250px;">
                      Personal 
                  </h4>
                  <h6 class="card-title">Oficinas: <?=$stringUnidades; ?></h6>                        
                  <h6 class="card-title">Areas: <?=$stringAreas;?></h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <?php
                    $html='<table class="table table-bordered table-condensed" id="tablePaginatorFixed_personal">'.
                      '<thead class="bg-secondary text-white">'.
                        '<tr >'.
                          '<th class="font-weight-bold">-</th>'.
                          '<th class="font-weight-bold">Personal</th>'.
                          '<th class="font-weight-bold">C.I.</th>'.
                          '<th class="font-weight-bold">Of/Area</th>'.
                          
                          '<th class="font-weight-bold">F. Ing.</th>'.
                          '<th class="font-weight-bold">Cargo</th>'.
                          '<th class="font-weight-bold">Básico</th>'.
                          '<th class="font-weight-bold">Afp</th>'.
                          '<th class="font-weight-bold">Tel.</th>'.
                          '<th class="font-weight-bold">Email</th>'.                          
                        '</tr>'.
                      '</thead>'.
                      '<tbody>';
                        //<?php  
                        $contador = 0;
                        while ($rowActivos = $stmtActivos->fetch(PDO::FETCH_ASSOC)) {
                          if($identificacion=="")$identificacion=0;
                          $contador++;   
                          $html.='<tr>'.
                            '<td class="text-center small">'.$contador.'</td>'.
                            '<td class="text-left small">'.$personal.'</td>'.
                            '<td class="text-left small">'.obtenerNombreIdentificacionPersona($cod_tipo_identificacion,1).' '.$identificacion.' '.obtenerlugarEmision($cod_lugar_emision,1).'</td>'.
                            '<td class="text-left small">'.abrevUnidad_solo($cod_unidadorganizacional).'/'.abrevArea_solo($cod_area).'</td>'.
                            
                            '<td class="text-right small">'.$ing_planilla.'</td>'.
                            '<td class="text-left small">'.nameCargo($cod_cargo).'</td>'.
                            '<td class="text-center small">'.formatNumberDec($haber_basico).'</td>'.
                            '<td class="text-left small">'.obtenerNameAfp($cod_tipoafp,1).'</td>'.
                            '<td class="text-left small">'.trim($telefono.' - '.$celular,' - ').'</td>'.
                            '<td class="text-left small">'.trim($email.' - '.$email_empresa,' - ').'</td>'.
                          '</tr>';
                        } 
                      $html.='</tbody>'.
                    '</table>';
                    echo $html;
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>  
        </div>
    </div>
