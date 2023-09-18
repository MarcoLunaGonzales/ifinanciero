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
$globalUser=$_SESSION["globalUser"];

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

$altasbajas=$_GET['altasbajas'];


$string_personal_baja=obtenerValorConfiguracion(101);
$array_personal_bajas=explode(",", $string_personal_baja);
$cont_per_bajas=count($array_personal_bajas);
$personal_baja=false;
for ($i=0; $i <$cont_per_bajas ; $i++) { 
  if($globalUser==$array_personal_bajas[$i]){
    $personal_baja=true;
  }
}

$sql="SELECT af.codigo,af.codigoactivo,af.activo,af.fechalta, d.abreviatura as dep_nombre, tb.tipo_bien tb_tipo,af.contabilizado,af.cod_comprobante,
(select pr.abreviatura from proyectos_financiacionexterna pr where pr.codigo=af.cod_proy_financiacion)as proy_financiacion,
 (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=af.cod_unidadorganizacional)as nombre_unidad, 
 (select a.abreviatura from areas a where a.codigo=af.cod_area)as nombre_area,
 (select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable)as nombre_responsable,(SELECT afi.imagen FROM activosfijosimagen afi where afi.codigo=af.codigo)as imagen,(select eaaf.nombre from activofijos_asignaciones afa join estados_asignacionaf eaaf on afa.cod_estadoasignacionaf=eaaf.codigo where afa.cod_activosfijos=af.codigo order by afa.codigo desc limit 1) as nombre_estado
from activosfijos af, depreciaciones d, tiposbienes tb 
where af.cod_depreciaciones = d.codigo and af.cod_tiposbienes = tb.codigo ";  

if($altasbajas!=""){
  $sql.=" and af.cod_estadoactivofijo = 3 ";
}else{
  $sql.=" and af.cod_estadoactivofijo = 1 ";
}

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

//echo $sql; 

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

$stmt->bindColumn('imagen', $imagen);
$stmt->bindColumn('nombre_estado', $nombre_estado);
?>

                    <?php $index=1;
                  while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                      // $activo=addslashes($activo);
                      $activo= str_replace('"', '', $activo);
                      ?>
                      <tr>
                        <td  class="td-actions text-right">    
                            <a href='<?=$printDepreciacion1;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-info">
                              <i class="material-icons" title="Ficha Activo Fijo" style="color:black">print</i>
                            </a>
                            <!-- <a href='reportes_activosfijos/imp_actaEntrega_html.php?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-danger">
                              <i class="material-icons" title="Acta de Entrega" style="color:black">print</i>
                            </a> -->
                          </td>
                          <td class="text-center small"><?=$codigo;?></td>
                          <td class="text-center small"><?=$codigoactivo;?></td>
                          <td class="text-center small"><?=$nombreUnidad;?>-<?=$nombreArea;?></td>
                          <td class="text-left small"><?=$activo;?></td>
                          <td class="text-center small"><?=$fechalta;?></td>
                          <td class="text-left small"><?=$dep_nombre;?>/<?=$tb_tipo;?></td>
                          <td class="text-left small"><?=strtoupper($nombre_responsable)?></td>
                          <td class="text-left small"><?=$nombre_estado?></td>
                          <td class="text-left small">
                            <?php
                              if($imagen<>null && $imagen<>""){?>                                
                                 <i class="material-icons" >wallpaper</i>
                              <?php }
                              ?>
                          </td>
                          <td class="td-actions text-right">
                            <div class="btn-group dropdown">
                              <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 <i class="material-icons" >list</i><small><small></small></small>
                              </button>
                              <div class="dropdown-menu" >
                                <?php if($globalAdmin==1){ ?>
                                <a href='<?=$urlEdit6;?>&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-success" ><?=$iconEdit;?></i>Editar
                                </a>
                                <a href='index.php?opcion=activofijoCargarImagen&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-warning" >wallpaper</i>Cargar Imagen
                                </a>
                                <!-- <button rel="tooltip" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete2;?>&codigo=<?=$codigo;?>')">
                                  <i class="material-icons text-danger" ><?=$iconDelete;?></i>Borrar
                                </button> -->
                                <a href='<?=$urlEditTransfer;?>&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-info" >transfer_within_a_station</i>Transferir
                                </a>
                              <?php } 
                              if($personal_baja){
                              ?>
                              <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modalEditar" onclick="agregaformActivoFijo_baja('<?=$codigo;?>','<?=$codigoactivo;?>','<?=$activo?>')">
                                  <i class="material-icons text-danger"  title="Editar">flight_land</i>Dar de Baja
                                </button>
                              <?php
                              }
                              ?>
                              </div>
                            </div>
                            <div class="btn-group dropdown">
                              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                 <i class="material-icons" >list</i><small><small></small></small>
                              </button>
                              <div class="dropdown-menu" >
                              <?php if($globalAdmin==1){ ?>
                                <a href='<?=$urlafAccesorios;?>&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-warning"  style="color:black">extension</i>Accesorios AF
                                </a>
                                <a href='<?=$urlafEventos;?>&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-info"  style="color:black">event</i>Eventos AF
                                </a>
                                <a href='<?=$urlRevaluarAF;?>&codigo=<?=$codigo;?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-warning" style="color:black">trending_up</i>Reevaluar AF
                                </a><?php } ?>
                                <?php
                                //si es mayo a cero, ya se genero el comprobante.
                                  if($cod_comprobante>0){?>
                                    <a href="<?=$urlImp;?>?comp=<?=$cod_comprobante;?>&mon=1" class="dropdown-item" target="_blank">
                                           <i class="material-icons" title="Imprimir Comporbante" style="color:red">print</i>Imprimir Comporbante
                                       </a> 
                                  <?php }elseif($contabilizado==0){ ?>
                                    <a href="<?=$urlprint_contabilizacion_cajachica;?>?cod_cajachica=<?=$cod_cajachica;?>" class="dropdown-item" target="_blank" > 
                                      <i class="material-icons" title="Generar Comprobante" style="color:red">input</i>Generar Comprobante
                                    </a>
                                  <?php } ?>
                              </div>
                            </div>
                          </td>
                      </tr>
                    <?php $index++; } ?>
