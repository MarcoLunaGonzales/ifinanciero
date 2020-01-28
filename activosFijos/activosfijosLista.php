<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="SELECT af.codigo,af.codigoactivo,af.activo,af.fechalta, d.abreviatura as dep_nombre, tb.tipo_bien tb_tipo,
(select pr.abreviatura from proyectos_financiacionexterna pr where pr.codigo=af.cod_proy_financiacion)as proy_financiacion,
 (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=af.cod_unidadorganizacional)as nombre_unidad, 
 (select a.abreviatura from areas a where a.codigo=af.cod_area)as nombre_area,
 (select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable)as nombre_responsable
from activosfijos af, depreciaciones d, tiposbienes tb, ubicaciones u 
where af.cod_depreciaciones = d.codigo and af.cod_tiposbienes = tb.codigo and af.cod_ubicaciones  = u.codigo
and af.cod_estadoactivofijo = 1";
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('codigoactivo', $codigoactivo);
$stmt->bindColumn('fechalta', $fechalta);
$stmt->bindColumn('activo', $activo);
$stmt->bindColumn('nombre_responsable', $nombre_responsable);

$stmt->bindColumn('dep_nombre', $dep_nombre);
$stmt->bindColumn('tb_tipo', $tb_tipo);

$stmt->bindColumn('nombre_unidad', $nombreUnidad);
$stmt->bindColumn('nombre_area', $nombreArea);
$stmt->bindColumn('proy_financiacion', $proy_financiacion);

?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"><?=$moduleNamePlural6?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-condensed" id="tablePaginator">

                      <thead>
                        <tr>
                          <th></th>
                            <th>Codigo</th>
                            <th>Oficina/Area</th>
                            <th>Activo</th>
                            <th>F. Alta</th>
                            <th>Rubro/TipoBien</th>
                            <th>Responsable</th>
                            <th>Proyecto</th>
                            <th>Acc/Eventos</th>
                            <th></th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php $index=1;
                      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                          <tr>
                            <td  class="td-actions text-right">    
                                <a href='<?=$printDepreciacion1;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons">print</i>
                                </a>
                              </td>
                              <td class="text-center small"><?=$codigoactivo;?></td>
                              <td class="text-center small"><?=$nombreUnidad;?>-<?=$nombreArea;?></td>
                              <td class="text-left small"><?=$activo;?></td>
                              <td class="text-center small"><?=$fechalta;?></td>
                              <td class="text-left small"><?=$dep_nombre;?>/<?=$tb_tipo;?></td>
                              <td class="text-left small"><?=strtoupper($nombre_responsable)?></td>
                              <td class="text-left small"><?=$proy_financiacion;?></td>
                              <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                              ?>

                                <a href='<?=$urlafAccesorios;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-warning">
                                  <i class="material-icons" title="Accesorios AF" style="color:black">extension</i>
                                </a>
                                <a href='<?=$urlafEventos;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-info">
                                  <i class="material-icons" title="Eventos AF" style="color:black">event</i>
                                </a>
                                <a href='<?=$urlRevaluarAF;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-warning">
                                  <i class="material-icons" title="Reevaluar AF" style="color:black">trending_up</i>
                                </a>

                                <?php
                                  }
                                ?>
                              
                              </td>
                              <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                              ?>
                                <a href='<?=$urlEdit6;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons" title="Editar AF"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete2;?>&codigo=<?=$codigo;?>')">
                                  <i class="material-icons" title="Borrar AF"><?=$iconDelete;?></i>
                                </button>
                                <a href='<?=$urlEditTransfer;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonMorado;?>">
                                  <i class="material-icons" title="Transferir AF">transfer_within_a_station</i>
                                </a>
                                <?php
                                  }
                                ?>
                              
                              </td>
                          </tr>
                      <?php $index++; } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <?php
              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegistrar_activosfijos;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
