<?php

require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$cod_uo=$_GET['cod_uo'];
$rubro=$_GET['rubro'];
$fechaI=$_GET['fechaI'];
$fechaF=$_GET['fechaF'];

$responsable=$_GET['responsable'];
$tipoAlta=$_GET['tipoAlta'];
$proyecto=$_GET['proyecto'];

$glosa=$_GET['glosa'];


$codigoSistema=$_GET['codigoSistema'];
$codigoActivo=$_GET['codigoActivo'];

// $unidadOrgString=implode(",", $cod_uo);



$sql="SELECT af.codigo,af.codigoactivo,af.activo,af.fechalta, d.abreviatura as dep_nombre, tb.tipo_bien tb_tipo,af.contabilizado,af.cod_comprobante,
(select pr.abreviatura from proyectos_financiacionexterna pr where pr.codigo=af.cod_proy_financiacion)as proy_financiacion,
 (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=af.cod_unidadorganizacional)as nombre_unidad, 
 (select a.abreviatura from areas a where a.codigo=af.cod_area)as nombre_area,
 (select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable)as nombre_responsable
from activosfijos af, depreciaciones d, tiposbienes tb 
where af.cod_depreciaciones = d.codigo and af.cod_tiposbienes = tb.codigo and af.cod_estadoactivofijo = 1";  

if($cod_uo!=""){
  $sql.=" and af.cod_unidadorganizacional in ($cod_uo)";
}
if($rubro!=""){
  $sql.=" and af.cod_tiposbienes in ($rubro)";  
}
if($fechaI!="" && $fechaF!=""){
  $sql.=" and af.fechalta BETWEEN '$fechaI' and '$fechaF'"; 
}
if($responsable!=""){
  $sql.=" and af.cod_responsables_responsable in ($responsable)";
}
if($tipoAlta!=""){
  $sql.=" and af.tipoalta in ('$tipoAlta')";
}
if($proyecto!=""){
  $sql.=" and af.cod_proy_financiacion in ($proyecto)";
}
if($glosa!=""){
  $sql.=" and af.activo like '%$glosa%'";
}
if($codigoSistema!=""){
  $sql.=" and af.codigo = '$codigoSistema'";
}
if($codigoActivo!=""){
  $sql.=" and af.codigoactivo like '%$codigoActivo%'";
}
$sql.=" order by af.codigoactivo desc";
// echo $sql; 



$stmt = $dbh->prepare($sql);
$stmt->execute();
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
$stmt->bindColumn('contabilizado', $contabilizado);
$stmt->bindColumn('cod_comprobante', $cod_comprobante);
?>
<table class="table table-condensed" id="tablePaginatorHead">
                  <thead>
                    <tr>
                      <th></th>
                        <th>CodSistema</th>
                        <th>Codigo</th>
                        <th>Of/Area</th>
                        <th>Activo</th>
                        <th>F. Alta</th>
                        <th>Rubro/TipoBien</th>
                        <th>Responsable</th>
                        <th>Proyecto</th>
                        <th>Acc/Eventos</th>
                        <th></th>
                        <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                      <tr>
                        <td  class="td-actions text-right">    
                            <a href='<?=$printDepreciacion1;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-info">
                              <i class="material-icons" title="Ficha Activo Fijo" style="color:black">print</i>
                            </a>
                          </td>
                          <td class="text-center small"><?=$codigo;?></td>
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
                          <td class="text-center">
                            <?php
                            //si es mayo a cero, ya se genero el comprobante.
                              if($cod_comprobante>0){?>                                    
                                <a href="<?=$urlImp;?>?comp=<?=$cod_comprobante;?>&mon=1" target="_blank">
                                       <i class="material-icons" title="Imprimir Comporbante" style="color:red">print</i>
                                   </a> 
                              <?php }elseif($contabilizado==0){ ?>
                                <a href="<?=$urlprint_contabilizacion_af;?>?codigo_activo=<?=$codigo;?>" target="_blank" > 
                                  <i class="material-icons" title="Generar Comprobante" style="color:red">input</i>
                                </a>
                              <?php }
                            ?>
                          </td>
                      </tr>
                    <?php $index++; } ?>
                  </tbody>
                </table>

