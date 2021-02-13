<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'functions.php';
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalUnidad=3000;
$globalGestion=$_SESSION['globalNombreGestion'];
$globalMesTrabajo=$_SESSION['globalMes'];

$dbh = new Conexion();

// Preparamos
$codTipoComprobanteDefault="3";

$sql="SELECT c.cod_tipocomprobante,(select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad, c.cod_gestion, 
(select m.nombre from monedas m where m.codigo=c.cod_moneda)moneda, 
(select t.abreviatura from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero,c.codigo, c.glosa,ec.nombre,c.cod_estadocomprobante,c.salvado_temporal
from comprobantes c join estados_comprobantes ec on c.cod_estadocomprobante=ec.codigo where c.cod_tipocomprobante in ($codTipoComprobanteDefault) and MONTH(c.fecha)='$globalMesTrabajo' ";

//if($globalAdmin!=1){
  $sql.=" and c.cod_unidadorganizacional='$globalUnidad'";
//}

$sql.=" and c.cod_gestion='$globalGestion' order by c.numero desc limit 200";

//echo $sql;

$stmt = $dbh->prepare($sql);

// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('unidad', $nombreUnidad);
$stmt->bindColumn('cod_gestion', $nombreGestion);
$stmt->bindColumn('moneda', $nombreMoneda);
$stmt->bindColumn('tipo_comprobante', $nombreTipoComprobante);
$stmt->bindColumn('fecha', $fechaComprobante);
$stmt->bindColumn('numero', $nroCorrelativo);
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('glosa', $glosaComprobante);
$stmt->bindColumn('nombre', $estadoComprobante);
$stmt->bindColumn('cod_estadocomprobante', $estadoC);
$stmt->bindColumn('cod_tipocomprobante', $codTipoC);
$stmt->bindColumn('salvado_temporal', $salvadoC);

// busquena por Oficina
$stmtUO = $dbh->prepare("SELECT (select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad,(select u.codigo from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)as codigo_uo
from comprobantes c where c.cod_estadocomprobante!=2 GROUP BY unidad order by unidad");
$stmtUO->execute();
$stmtUO->bindColumn('unidad', $nombreUnidad_x);
$stmtUO->bindColumn('codigo_uo', $codigo_uo);

// busquena por tipo de comprobante
$stmtTipoComprobante = $dbh->prepare("SELECT (select t.nombre from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)as tipo_comprobante,(select t.codigo from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)as cod_tipo_comprobante
from comprobantes c where c.cod_estadocomprobante!=2 GROUP BY tipo_comprobante order by tipo_comprobante
");
$stmtTipoComprobante->execute();
$stmtTipoComprobante->bindColumn('tipo_comprobante', $nombre_tipo_comprobante);
$stmtTipoComprobante->bindColumn('cod_tipo_comprobante', $codigo_tipo_co);

?>

<div class="content">
	<div class="container-fluid">
        <div style="overflow-y: scroll;">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title"><?=$moduleNamePlural?> Proyecto SIS</h4>
                </div>      
                <div class="card-body">
                  <div class="" id="data_comprobantes">
                    <table id="tablePaginator100" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>                          
                          <th class="text-center small">Oficina</th>
                          <th class="text-center small">Tipo/Número</th>
                          <th class="text-center small">Fecha</th>
                          <th class="text-left small">Glosa</th>
                          <th class="text-center small">Estado</th>
                          <th class="text-center small" width="10%">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
						            $index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $nombreComprobante=nombreComprobante($codigo);
                          $existeCuenta=0;
                          $existeCuenta=obtenerEstadoCuentaSaldoComprobante($codigo);
                          $mes=date('n',strtotime($fechaComprobante));
                          // $mes=date("j",$fechaComprobante);
                          switch ($estadoC) {
                            case 1:
                             $btnEstado="btn-info";$estadoIcon="how_to_vote";
                            break;
                            case 2:
                            $btnEstado="btn-danger";$estadoIcon="thumb_down";
                            $glosaComprobante="***ANULADO***";
                            break;
                            case 3:
                              $btnEstado="btn-warning";$estadoIcon="thumb_up";
                            break;
                          }
                          $tamanioGlosa=obtenerValorConfiguracion(72); 
                          if($glosaComprobante>$tamanioGlosa){
                            $glosaComprobante=substr($glosaComprobante, 0, $tamanioGlosa);
                          }

                          $cambiosDatos=obtenerDatosUsuariosComprobante($codigo);
                          if($cambiosDatos!=""){
                            $cambiosDatos="\n".$cambiosDatos;
                          }
                          if($salvadoC==1){
        $btnEstado="btn btn-danger font-weight-bold";
        $estadoComprobante="Salvado Temporal";
        $estadoIcon="save";
       } 
                        ?>
                        <tr>
                          
                          <td align="text-center small"><?=$index;?></td>                          
                          <td class="text-center small"><?=$nombreUnidad;?></td>
                          <td class="text-center small"><?=$nombreComprobante;?></td>
                          <td class="text-center small"><?=strftime('%d/%m/%Y',strtotime($fechaComprobante));?></td>
                          
                          <!--td><?=$nombreMoneda;?></td-->
                          <td class="text-left small"><?=$glosaComprobante;?></td>
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estadoComprobante;?>  <span class="material-icons small"><?=$estadoIcon?></span></button></td>
                          <td class="td-actions text-right">
                            
                            <div class="btn-group">
                              <div class="dropdown">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ver Comprobante <?=$cambiosDatos?>">
                                  <i class="material-icons"><?=$iconImp;?></i>
                                </button>
                                <div class="dropdown-menu">
                                  <a href="#" onclick="javascript:window.open('<?=$urlImp;?>?comp=<?=$codigo;?>&mon=-1')" class="dropdown-item">
                                                 <i class="material-icons text-muted">monetization_on</i> BIMONETARIO (Bs - Usd)
                                      </a>
                                      <div class="dropdown-divider"></div>
                                  <?php
                                    $stmtMoneda = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
                                   $stmtMoneda->execute();
                                    while ($row = $stmtMoneda->fetch(PDO::FETCH_ASSOC)) {
                                      $codigoX=$row['codigo'];
                                      $nombreX=$row['nombre'];
                                      $abrevX=$row['abreviatura'];
                                      //if($codigoX!=1){
                                        ?>
                                         <a href="#" onclick="javascript:window.open('<?=$urlImp;?>?comp=<?=$codigo;?>&mon=<?=$codigoX?>')" class="dropdown-item">
                                             <i class="material-icons">keyboard_arrow_right</i> <?=$abrevX?>
                                         </a> 
                                       <?php
                                      //}
                                     }
                                     ?>
                                </div>
                              </div>
                              <?php if($estadoC!=2){?>
                              <div class="dropdown">
                                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ver Comprobante">
                                  <i class="material-icons">more_horiz</i>
                                </button>
                                <div class="dropdown-menu">
                                  <a href='<?=$urlArchivo;?>?codigo=<?=$codigo;?>' target="_blank" class="dropdown-item" title="Ver Adjuntos">
                                    <i class="material-icons text-default">attachment</i> Adjuntos
                                  </a>
                                  <?php 
                                  
                                  if($codigoSol[0]!=0){
                                   ?>
                                   <a title=" Ver Solicitud de Recursos" target="_blank" href="<?=$urlVerSol;?>?cod=<?=$codigoSol[0];?>&comp=1" target="_blank" class="dropdown-item">
                                    <i class="material-icons text-success">preview</i> <b class="text-dark">Adjuntos SR</b>
                                  </a>
                                  <a title="Imprimir Solicitud de Recursos" href='#' onclick="javascript:window.open('<?=$urlImpSol;?>?sol=<?=$codigoSol[0];?>&mon=1')" class="dropdown-item">
                                    <i class="material-icons text-info"><?=$iconImp;?></i> <b class="text-dark">SR</b>
                                  </a><?php
                                  }
                                  ?>
                                  
                                </div>
                              </div>
                              <?php }?>
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
            </div>
          </div>  
        </div>
</div>

