<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

//SACAMOS LA CONFIGURACION PARA REDIRECCIONAR EL PON
$codigoIndicadorPON=obtenerCodigoPON();


$codigoIndicador=$codigo;
$nombreIndicador=nameIndicador($codigoIndicador);
$nombreObjetivo=nameObjetivoxIndicador($codigoIndicador);

$table="actividades_poa";
$moduleName="Ejecucion de Actividades PON";

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
$nombreMes=nameMes($codMesX);
//FIN FECHAS

// Preparamos
$sql="SELECT a.codigo, a.orden, a.nombre, (SELECT s.abreviatura from comites c, sectores s where c.cod_sector=s.codigo and c.codigo=a.cod_comite)as sector, (SELECT c.nombre from comites c where c.codigo=a.cod_comite)as comite,
(SELECT n.abreviatura from normas n where n.codigo=a.cod_norma)as norma, a.cod_unidadorganizacional, a.cod_area, (SELECT ep.nombre from estados_pon ep where ep.codigo=a.cod_estadopon)as estadopon, (select mg.nombre from modos_generacionpon mg where mg.codigo=a.cod_modogeneracionpon)as modogeneracionpon, (SELECT CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=a.cod_personal) as personal
 from actividades_poa a where a.cod_indicador='$codigoIndicador' and a.cod_estado=1 "; 
if($globalAdmin==0){
  $sql.=" and a.cod_area='$globalArea' and a.cod_unidadorganizacional='$globalUnidad' and a.cod_personal='$globalUser'";
}
if($globalUserPON==1){  
  $sql.=" union ";
  $sql.="SELECT a.codigo, a.orden, a.nombre, (SELECT s.abreviatura from comites c, sectores s where c.cod_sector=s.codigo and c.codigo=a.cod_comite)as sector, (SELECT c.nombre from comites c where c.codigo=a.cod_comite)as comite,
(SELECT n.abreviatura from normas n where n.codigo=a.cod_norma)as norma, a.cod_unidadorganizacional, a.cod_area, (SELECT ep.nombre from estados_pon ep where ep.codigo=a.cod_estadopon)as estadopon, (select mg.nombre from modos_generacionpon mg where mg.codigo=a.cod_modogeneracionpon)as modogeneracionpon, (SELECT CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=a.cod_personal) as personal
 from actividades_poa a where a.cod_indicador='$codigoIndicador' and a.cod_estado=1 and a.cod_personal='$globalUser' ";
}
$sql.=" order by cod_unidadorganizacional, cod_area, orden";
//echo $sql;
$stmt = $dbh->prepare($sql);
// Ejecutamos
$stmt->execute();

// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('orden', $orden);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('sector', $sector);
$stmt->bindColumn('comite', $comite);
$stmt->bindColumn('norma', $norma);
$stmt->bindColumn('cod_unidadorganizacional', $codUnidad);
$stmt->bindColumn('cod_area', $codArea);
$stmt->bindColumn('estadopon', $estadopon);
$stmt->bindColumn('modogeneracionpon', $modogeneracionpon);
$stmt->bindColumn('personal', $personal);

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
                  <h4 class="card-title"><?=$moduleName?> - Mes Ejecucion: <?=$nombreMes;?> </h4>
                  <h4 class="card-title">Fecha Limite: <?=$fechaFinRegistroX;?></h4>
                  <h6 class="card-title">Objetivo: <?=$nombreObjetivo?></h6>
                  <h6 class="card-title">Indicador: <?=$nombreIndicador?></h6>


                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th class="text-center">-</th>
                          <th>Area</th>
                          <th>Tema a Normalizar</th>
                          <th>Sector - Comite</th>
                          <th>Modo Generacion</th>
                          <th>Responsable</th>
                          <th class="table-warning">Plan</th>
                          <th class="table-success">Ej. PON</th>
                          <th>Explicacion<br>Logro</th>
                          <th>Archivo</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                        $index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $abrevArea=abrevArea($codArea);
                          $abrevUnidad=abrevUnidad($codUnidad);

                          //SACAMOS LA PLANIFICACION
                          $sqlRecupera="SELECT ep.nombre from actividades_poaplanificacion a, estados_pon ep where a.value_numerico=ep.codigo and a.cod_actividad='$codigo' and a.mes='$codMesX'";
                          $stmtRecupera = $dbh->prepare($sqlRecupera);
                          //echo $sqlRecupera;
                          $stmtRecupera->execute();
                          $estadoPon="";
                          while ($rowRec = $stmtRecupera->fetch(PDO::FETCH_ASSOC)) {
                            $estadoPon=$rowRec['nombre'];
                          }

                          $sqlRecupera="SELECT ep.nombre, a.descripcion, a.archivo from actividades_poaejecucion a, estados_pon ep, actividades_poa ap where ap.codigo=a.cod_actividad and a.value_numerico=ep.codigo and a.cod_actividad=$codigo and a.mes=$codMesX";
                          //echo $sqlRecupera;
                          $stmtRecupera = $dbh->prepare($sqlRecupera);
                          $stmtRecupera->execute();
                          $estadoPonEj="";
                          $descripcionLogroEj="";
                          $archivoEj="";
                          while ($rowRec = $stmtRecupera->fetch(PDO::FETCH_ASSOC)) {
                            $estadoPonEj=$rowRec['nombre'];
                            $descripcionLogroEj=$rowRec['descripcion'];
                            $archivoEj=$rowRec['archivo'];
                          }
                          //FIN PLANIFICACION

                      ?>
                        <tr>
                          <td class="text-center"><?=$index;?></td>
                          <td><?=$abrevArea."-".$abrevUnidad;?></td>
                          <td><?=$nombre;?></td>
                          <td><?=$sector;?> - <?=$comite?></td>
                          <td><?=$modogeneracionpon;?></td>
                          <td><?=$personal;?></td>
                          <td class="text-center table-warning font-weight-bold">
                              <?=$estadoPon;?>
                          </td>
                          <td class="text-center table-success font-weight-bold">
                              <?=$estadoPonEj;?>
                          </td>
                          <td><?=$descripcionLogroEj?></td>
                          <?php
                            if($archivoEj!=""){
                                $iconCheckFile="attach_file";
                              }else{
                                $iconCheckFile="";
                              }
                          ?>
                            <td><div class="card-icon">
                                <a href="filesApp/<?=$archivoEj;?>" target="_blank">
                                  <i class="material-icons"><?=$iconCheckFile;?></i>
                                </a>
                              </div>
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
        				<div class="card-body">
                    <button class="<?=$button;?>" onClick="location.href='index.php?opcion=registerPONEjecucion&codigo=<?=$codigoIndicador?>'">Registrar Ejecucion</button>   
                </div>
            </div>
          </div>  
        </div>
    </div>
