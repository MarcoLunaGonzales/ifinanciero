<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'styles.php';

$dbh = new Conexion();


//SACAMOS LA CONFIGURACION PARA REDIRECCIONAR EL PON
$stmt = $dbh->prepare("SELECT valor_configuracion FROM configuraciones where id_configuracion=6");
$stmt->execute();
$codigoIndicadorPON=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codigoIndicadorPON=$row['valor_configuracion'];
}

$codigoIndicador=$codigo;
$areaIndicador=$area;
$unidadIndicador=$unidad;

$nombreIndicador=nameIndicador($codigoIndicador);
$nombreObjetivo=nameObjetivoxIndicador($codigoIndicador);

$table="actividades_poa";
$moduleName="Actividades POA PON";

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];
$globalUserPON=$_SESSION["globalUserPON"];

//echo "GLOBALADMIN: ".$globalAdmin."<br>";
//echo "GLOBALUSERPON: ".$globalUserPON."<br>";

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


//SACAMOS EL ESTADO DEL POA PARA LA GESTION
$codEstadoPOAGestion=estadoPOAGestion($globalGestion);


// Preparamos
$sql="SELECT a.codigo, a.orden, a.nombre, (SELECT s.abreviatura from comites c, sectores s where c.cod_sector=s.codigo and c.codigo=a.cod_comite)as sector, (SELECT c.nombre from comites c where c.codigo=a.cod_comite)as comite,
(SELECT n.abreviatura from normas n where n.codigo=a.cod_norma)as norma, a.cod_unidadorganizacional, a.cod_area, (SELECT ep.nombre from estados_pon ep where ep.codigo=a.cod_estadopon)as estadopon, (select mg.nombre from modos_generacionpon mg where mg.codigo=a.cod_modogeneracionpon)as modogeneracionpon, (SELECT CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=a.cod_personal) as personal, a.actividad_extra, a.solicitante
 from actividades_poa a where a.cod_indicador='$codigoIndicador' and a.cod_estado=1 "; 
if($globalAdmin==0 && $globalUserPON!=2){
  $sql.=" and a.cod_area in ($globalArea) and a.cod_unidadorganizacional in ($globalUnidad) ";
}
if($globalUserPON!=2){
  $sql.=" and a.cod_personal='$globalUser' ";
}
if($areaIndicador!=0){
  $sql.=" and a.cod_area='$areaIndicador' ";
}
if($unidadIndicador!=0){
  $sql.=" and a.cod_unidadorganizacional='$unidadIndicador' ";
}
if($globalUserPON==1){
  $sql.="UNION SELECT a.codigo, a.orden, a.nombre, (SELECT s.abreviatura from comites c, sectores s where c.cod_sector=s.codigo and c.codigo=a.cod_comite)as sector, (SELECT c.nombre from comites c where c.codigo=a.cod_comite)as comite,
(SELECT n.abreviatura from normas n where n.codigo=a.cod_norma)as norma, a.cod_unidadorganizacional, a.cod_area, (SELECT ep.nombre from estados_pon ep where ep.codigo=a.cod_estadopon)as estadopon, (select mg.nombre from modos_generacionpon mg where mg.codigo=a.cod_modogeneracionpon)as modogeneracionpon, (SELECT CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=a.cod_personal) as personal, a.actividad_extra, a.solicitante
 from actividades_poa a where a.cod_indicador='$codigoIndicadorPON' and a.cod_estado=1 and a.cod_personal='$globalUser' ";   
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
$stmt->bindColumn('actividad_extra', $actividadExtra);
$stmt->bindColumn('solicitante', $solicitante);

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
                  <h6 class="card-title">Objetivo: <?=$nombreObjetivo?></h6>
                  <h6 class="card-title">Indicador: <?=$nombreIndicador?> &nbsp;&nbsp;&nbsp;
                    <a href="#" class="<?=$buttonCeleste;?> btn-round" data-toggle="modal" data-target="#myModal"  title="Filtrar">
                        <i class="material-icons">filter_list</i>
                    </a>
                  </h6>
                  

                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped" id="tablePaginator">
                      <thead>
                        <tr>
                          <th class="text-center">-</th>
                          <th>Area</th>
                          <th>Tema a Normalizar</th>
                          <th>Sector - Comite</th>
                          <th>Norma de Ref.</th>
                          <th>Solicitante</th>
                          <th>Modo de Generacion</th>
                          <th>Responsable</th>
                          <th data-orderable="false">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                        $index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $abrevArea=abrevArea($codArea);
                          $abrevUnidad=abrevUnidad($codUnidad);
                      ?>
                        <tr>
                          <td class="text-center"><?=$index;?></td>
                          <td><?=$abrevUnidad."-".$abrevArea;?></td>
                          <td><?=$nombre;?></td>
                          <td><?=$sector." - ".$comite;?></td>
                          <td><?=$norma;?></td>
                          <td><?=$solicitante;?></td>
                          <td><?=$modogeneracionpon;?></td>
                          <td><?=$personal;?></td>
                          <td class="td-actions text-right">
                            <?php
                            if($codEstadoPOAGestion!=3 || $actividadExtra==1){
                            ?>
                            <button rel="tooltip" class="btn btn-danger" onclick="alerts.showSwal('warning-message-and-confirmation','index.php?opcion=deletePOAAct&codigo=<?=$codigo;?>&codigo_indicador=<?=$codigoIndicador?>')">
                              <i class="material-icons">close</i>
                            </button>
                            <?php
                            }
                            ?>
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
                    <button class="<?=$button;?>" onClick="location.href='index.php?opcion=registerPOAPONGroup&codigo=<?=$codigoIndicador?>&areaUnidad=0'">Registrar</button>
                    <button class="<?=$button;?>" onClick="location.href='index.php?opcion=registerPOAPONPlan&codigo=<?=$codigoIndicador?>'">Planificar</button>  
                    <a href="?opcion=listPOA" class="<?=$buttonCancel;?>">Cancelar</a> 
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
        <h5 class="modal-title" id="exampleModalLabel">Filtrar Area/Unidad</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="text-align:center;">
      <select class="selectpicker" name="unidadModal" id="unidadModal" data-style="<?=$comboColor;?>" required>
        <option disabled selected value="">Unidad</option>
        <?php
        $sqlAreas="SELECT i.cod_indicador, u.codigo as codigoUnidad, u.nombre as nombreUnidad, u.abreviatura as abrevUnidad from indicadores_unidadesareas i, unidades_organizacionales u where i.cod_indicador='$codigoIndicador' and i.cod_unidadorganizacional=u.codigo";
        if($globalAdmin==0){
          $sqlAreas.=" and i.cod_unidadorganizacional='$globalUnidad'";
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
        $sqlAreas="SELECT i.cod_indicador, a.codigo as codigoArea, a.nombre as nombreArea, a.abreviatura as abrevArea from indicadores_unidadesareas i, areas a where i.cod_indicador='$codigoIndicador' and i.cod_area=a.codigo ";
        if($globalAdmin==0){
          $sqlAreas.=" and i.cod_area='$globalArea' ";
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
        <button type="button" class="<?=$button;?>" onclick="enviarFiltroAreaUnidadPOA(<?=$codigoIndicador;?>,<?=$codigoIndicadorPON;?>);">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!--  End Modal -->