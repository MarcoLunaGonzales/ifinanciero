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
$moduleName="Actividades POA";

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

//SACAMOS EL ESTADO DEL POA PARA LA GESTION
$codEstadoPOAGestion=estadoPOAGestion($globalGestion);

//SACAMOS LA TABLA RELACIONADA

//SACAMOS SI EL INDICADOR TIENE ALGUNA PERSONA ASIGNADA
//SI ES ASI FILTRAMOS POR PERSONA Y SI NO PUES NO HACEMOS NADA
$sqlVerifica="SELECT count(*)as contador from actividades_poa a where a.cod_indicador='$codigoIndicador' and a.cod_estado=1 and a.cod_area in ($globalArea) and a.cod_unidadorganizacional in ($globalUnidad) 
  and a.cod_personal>0";
//echo $sqlVerifica;
$stmtVerifica=$dbh->prepare($sqlVerifica);
$stmtVerifica->execute();
$contadorVerifica=0;
while($rowVerifica = $stmtVerifica->fetch(PDO::FETCH_ASSOC)) {
  $contadorVerifica=$rowVerifica['contador'];
}

// Preparamos
$sql="SELECT a.codigo, a.orden, a.nombre, (SELECT n.abreviatura from normas n where n.codigo=a.cod_normapriorizada)as normapriorizada,
(SELECT s.abreviatura from normas n, sectores s where n.cod_sector=s.codigo and n.codigo=a.cod_normapriorizada)as sectorpriorizado,
(SELECT n.abreviatura from normas n where n.codigo=a.cod_norma)as norma,
(SELECT s.abreviatura from normas n, sectores s where n.cod_sector=s.codigo and n.codigo=a.cod_norma)as sector,
(a.cod_tiposeguimiento)as tipodato, 
a.producto_esperado, a.cod_unidadorganizacional, a.cod_area,
(a.cod_datoclasificador)as datoclasificador, 
(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=a.cod_personal) as personal, actividad_extra, clave_indicador
 from actividades_poa a where a.cod_indicador='$codigoIndicador' and a.cod_estado=1 "; 
if($globalAdmin==0){
  $sql.=" and a.cod_area in ($globalArea) and a.cod_unidadorganizacional in ($globalUnidad) ";
  //SI EL CONTADOR IDENTIFICA QUE HAY UNA PERSONA ASIGNADA AL INDICADOR LO FILTRA POR PERSONA
  if($contadorVerifica>0){
    $sql.=" and a.cod_personal in ($globalUser) ";
  }
} 
if($areaIndicador!=0){
  $sql.=" and a.cod_area in ($areaIndicador) ";
}
if($unidadIndicador!=0){
  $sql.=" and a.cod_unidadorganizacional in ($unidadIndicador) ";
} 
$sql.=" order by a.cod_unidadorganizacional, a.cod_area, a.orden";

//echo $sql;

$stmt = $dbh->prepare($sql);
// Ejecutamos
$stmt->execute();

// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('orden', $orden);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('normapriorizada', $normaPriorizada);
$stmt->bindColumn('sectorpriorizado', $sectorPriorizado);
$stmt->bindColumn('norma', $norma);
$stmt->bindColumn('sector', $sector);
$stmt->bindColumn('tipodato', $tipoDato);
$stmt->bindColumn('producto_esperado', $productoEsperado);
$stmt->bindColumn('cod_unidadorganizacional', $codUnidad);
$stmt->bindColumn('cod_area', $codArea);
$stmt->bindColumn('datoclasificador', $datoClasificador);
$stmt->bindColumn('personal', $personal);
$stmt->bindColumn('actividad_extra', $actividadExtra);
$stmt->bindColumn('clave_indicador', $actividadCMI);

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
                          <th>Actividad</th>
                          <th>Sector/Norma Priorizado</th>
                          <th>Sector/Norma</th>
                          <th>Act.CMI</th>
                          <th>Clasificador</th>
                          <th>Personal POAI</th>
                          <th data-orderable="false">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                        $index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $abrevArea=abrevArea($codArea);
                          $abrevUnidad=abrevUnidad($codUnidad);

                          $nombreTablaClasificador=obtieneTablaClasificador($codigoIndicador,$codArea);
                          $nombreDatoClasificador=obtieneDatoClasificador($datoClasificador,$nombreTablaClasificador);

                          if($actividadCMI==1){
                            $iconCheck="done";
                          }else{
                            $iconCheck="clear";
                          }
                      ?>
                        <tr>
                          <td class="text-center"><?=$index;?></td>
                          <td><?=$abrevUnidad."-".$abrevArea;?></td>
                          <td><?=$nombre;?></td>
                          <td><?=$sectorPriorizado." ".$normaPriorizada;?></td>
                          <td><?=$sector." ".$norma;?></td>
                          <td class="text-center">
                            <div class="card-icon">
                              <i class="material-icons"><?=$iconCheck;?></i>
                            </div>
                          </td>
                          <td class="text-left small"><?=$nombreDatoClasificador;?></td>
                          <td><?=$personal;?></td>
                          <td class="td-actions text-right">
                            <?php
                            if($codEstadoPOAGestion!=3 || $actividadExtra==1){
                            ?>
                            <a href="index.php?opcion=editPOAAct&codigo=<?=$codigo;?>&codigo_indicador=<?=$codigoIndicador?>&areaUnidad=<?=$codUnidad;?>|<?=$codArea;?>" class="btn btn-success">
                              <i class="material-icons">edit</i>
                            </a>
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
                    <!--button class="<?=$button;?>" onClick="location.href='index.php?opcion=registerPOAActInd&codigo=<?=$codigoIndicador?>'">Registrar</button-->
                    <button class="<?=$button;?>" onClick="location.href='index.php?opcion=registerPOAGroup&codigo=<?=$codigoIndicador?>&areaUnidad=0'">Registrar</button>

                    <button class="<?=$button;?>" onClick="location.href='index.php?opcion=registerPOAPlan&codigo=<?=$codigoIndicador?>'">Planificar</button>  

                    <!--button class="<?=$button;?>" onClick="location.href='index.php?opcion=asignarPOA&codigo=<?=$codigoIndicador?>&areaUnidad=0'">Asignar Personal</button>

                    <button class="<?=$button;?>" onClick="location.href='index.php?opcion=asignarPOAI&codigo=<?=$codigoIndicador?>&areaUnidad=0'">Asignar POAI</button-->

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
        $sqlAreas="SELECT i.cod_indicador, a.codigo as codigoArea, a.nombre as nombreArea, a.abreviatura as abrevArea from indicadores_unidadesareas i, areas a where i.cod_indicador='$codigoIndicador' and i.cod_area=a.codigo ";
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
        <button type="button" class="<?=$button;?>" onclick="enviarFiltroAreaUnidadPOA(<?=$codigoIndicador;?>,<?=$codigoIndicadorPON;?>);">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!--  End Modal -->