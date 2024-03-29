<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="SELECT af.codigo,af.codigoactivo,af.activo,DATE_FORMAT(af.fechalta, '%d/%m/%Y')as fechalta, d.abreviatura as dep_nombre, tb.tipo_bien tb_tipo,af.contabilizado,af.cod_comprobante,
(select pr.abreviatura from proyectos_financiacionexterna pr where pr.codigo=af.cod_proy_financiacion)as proy_financiacion,
 (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=af.cod_unidadorganizacional)as nombre_unidad, 
 (select a.abreviatura from areas a where a.codigo=af.cod_area)as nombre_area,
 (select concat_ws(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=af.cod_responsables_responsable)as nombre_responsable,(SELECT afi.imagen FROM activosfijosimagen afi where afi.codigo=af.codigo)as imagen,(select eaaf.nombre from activofijos_asignaciones afa join estados_asignacionaf eaaf on afa.cod_estadoasignacionaf=eaaf.codigo where afa.cod_activosfijos=af.codigo order by afa.codigo desc limit 1) as nombre_estado
from activosfijos af, depreciaciones d, tiposbienes tb 
where af.cod_depreciaciones = d.codigo and af.cod_tiposbienes = tb.codigo and af.cod_estadoactivofijo = 1 ORDER BY af.codigo desc limit 100";
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
$stmt->bindColumn('contabilizado', $contabilizado);
$stmt->bindColumn('cod_comprobante', $cod_comprobante);
$stmt->bindColumn('imagen', $imagen);

$stmt->bindColumn('nombre_estado', $nombre_estado);



// busquena por Oficina
$stmtUO = $dbh->prepare("SELECT codigo, (select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad,c.cod_unidadorganizacional as codigo_uo
from activosfijos c where c.cod_estadoactivofijo=1 GROUP BY unidad order by unidad");
$stmtUO->execute();
$stmtUO->bindColumn('unidad', $nombreUnidad_x);
$stmtUO->bindColumn('codigo_uo', $codigo_uo);
// busquena por rubro
$stmtRubro = $dbh->prepare("SELECT (select t.nombre from depreciaciones t where t.codigo=cod_depreciaciones)as rubro,cod_depreciaciones as codigoRubro from  activosfijos where cod_estadoactivofijo=1 GROUP BY rubro ORDER BY rubro");
$stmtRubro->execute();
$stmtRubro->bindColumn('rubro', $nombre_rubro);
$stmtRubro->bindColumn('codigoRubro', $codigo_rubro);
// busquena por respnsable
$stmtPersonal = $dbh->prepare("SELECT (select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_responsables_responsable)as personal,cod_responsables_responsable from  activosfijos where cod_estadoactivofijo=1 GROUP BY personal ORDER BY personal");
$stmtPersonal->execute();
$stmtPersonal->bindColumn('personal', $nombre_personal);
$stmtPersonal->bindColumn('cod_responsables_responsable', $codigo_personal);

// busquena por tipoActivo
$stmtProyecto = $dbh->prepare("SELECT (select p.nombre from proyectos_financiacionexterna p where p.codigo=cod_proy_financiacion)as proyecto, cod_proy_financiacion from  activosfijos where cod_estadoactivofijo=1 GROUP BY cod_proy_financiacion ORDER BY proyecto");
$stmtProyecto->execute();
$stmtProyecto->bindColumn('proyecto', $nombre_proyecto);
$stmtProyecto->bindColumn('cod_proy_financiacion', $codigo_proy);


$string_personal_baja=obtenerValorConfiguracion(101);
$array_personal_bajas=explode(",", $string_personal_baja);
$cont_per_bajas=count($array_personal_bajas);
$personal_baja=false;
for ($i=0; $i <$cont_per_bajas ; $i++) { 
  if($globalUser==$array_personal_bajas[$i]){
    $personal_baja=true;
  }
}
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
            <h4 align="right">
              <button type="button" class="btn btn-warning btn-round btn-fab" data-toggle="modal" data-target="#modalBuscador">
                <i class="material-icons" title="Buscador">search</i>
              </button>                      
            </h4>
          </div>
          <div class="card-body">
            <div class="table-responsive" >              
                <table class="table table-condensed" id="tablePaginatorHead">
                  <thead>
                    <tr>
                      <th></th>
                        <th>CodSis</th>
                        <th>Codigo</th>
                        <th>Of/Area</th>
                        <th>Activo</th>
                        <th>F. Alta</th>
                        <th>Rubro/TipoBien</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                        <th>Img</th>
                        <th>Acc/Eventos</th>
                    </tr>
                  </thead>
                  <tbody id="data_activosFijos">
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
                  </tbody>
                </table>
            </div>
          </div>
          <div class="card-footer fixed-bottom">
            <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
            <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegistrar_activosfijos;?>&codigo=0'">Registrar</button>
          </div>

        </div>
				        
      </div>
    </div>  
  </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #e86447;color:white;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><b>Baja de Activo Fijo</b></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="form-group col-sm-9">
            <input class="form-control" type="text" name="nombre_activo_b" id="nombre_activo_b" value=" *** " readonly="true" style="color: #e86447;font-size: 15px;background: white">
          </div>
          <div class="form-group col-sm-3">
            <input class="form-control" type="text" name="cod_activo_b2" id="cod_activo_b2" value="" readonly="true" style="color: #e86447;font-size: 15px;background: white">
          </div>
        </div>
        <input type="hidden" name="cod_activo_b" id="cod_activo_b" value="0">
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Fecha de Baja</label>
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fecha_baja" id="fecha_baja" value="<?=date('Y-m-d')?>" >
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Observaciones</label>
          <div class="col-sm-10">
            <div class="form-group">
              <textarea class="form-control"  name="obs_baja" id="obs_baja"></textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EditarPC"  data-dismiss="modal">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="material-icons" title="Volver">keyboard_return</i> Cerrar </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal busqueda de activos fijos-->
<div class="modal fade" id="modalBuscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Buscador Avanzado Activos Fijos</h4>
      </div>
      <div class="modal-body ">
        <div class="row">
            <label class="col-sm-3 text-center" style="color:#0040FF;">Oficina</label> 
            <label class="col-sm-6 text-center" style="color:#0040FF;">Fechas</label>                  
            <label class="col-sm-3 text-center" style="color:#0040FF;">Rubro</label>                                
        </div> 
        <div class="row">
          <div class="form-group col-sm-3">
            <select  name="OficinaBusqueda[]" id="OficinaBusqueda" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              <!-- <option value=""></option> -->
              <?php while ($rowUO = $stmtUO->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_uo;?>"> <?=$nombreUnidad_x;?></option>
              <?php }?>
            </select>
          </div>
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaInicio" id="fechaBusquedaInicio" value="<?=$globalGestion?>-01-01" min="<?=$globalGestion?>-01-01" max="<?=$globalGestion?>-12-31">
          </div>
          <div class="form-group col-sm-3">
            <input class="form-control input-sm" type="date" name="fechaBusquedaFin" id="fechaBusquedaFin" value="<?=$globalGestion?>-12-31" min="<?=$globalGestion?>-01-01" max="<?=$globalGestion?>-12-31"  >
          </div>
          <div class="form-group col-sm-3">            
            <select name="rubro[]" id="rubro" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              <!-- <option value=""></option> -->
              <?php while ($rowTC = $stmtRubro->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_rubro;?>"> <?=$nombre_rubro;?></option>
              <?php }?>
            </select>
            
          </div>              
        </div> 
        
        <div class="row">
            <label class="col-sm-4 text-center" style="color:#0040FF;">Responsable</label> 
            <label class="col-sm-4 text-center" style="color:#0040FF;">Tipo Alta</label>                  
            <label class="col-sm-4 text-center" style="color:#0040FF;">Proyecto</label>                           
        </div> 
        <div class="row">
          <div class="form-group col-sm-4">
            <select  name="responsable[]" id="responsable" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              <!-- <option value=""></option> -->
              <?php while ($rowPersonal = $stmtPersonal->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_personal;?>"> <?=$nombre_personal;?></option>
              <?php }?>
            </select>
          </div>
          <div class="form-group col-sm-4">
            <select name="tipoAlta[]" id="tipoAlta" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              <!-- <option value=""></option> -->
              <option value="NUEVO">NUEVO</option>
              <option value="USADO">USADO</option>
            </select>
          </div>          
          <div class="form-group col-sm-4">            
            <select name="proyecto[]" id="proyecto" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              
              <?php while ($rowPRo = $stmtProyecto->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_proy;?>"> <?=$nombre_proyecto;?></option>
              <?php }?>
            </select>
          </div>              
        </div> 
        <div class="row">
          <label class="col-sm-3 text-center" style="color:#0040FF;">Nombre Activo</label> 
          <div class="form-group col-sm-8">
            <input class="form-control input-sm" type="text" name="glosaBusqueda" id="glosaBusqueda"  >
          </div>           
        </div>

        <div class="row">
            <label class="col-sm-4 text-center" style="color:#0040FF;">Codigo de Sistema</label> 
            <label class="col-sm-4 text-center" style="color:#0040FF;">Codigo</label>
            <label class="col-sm-4 text-center" style="color:#0040FF;">AF Bajas</label>
        </div> 
        <div class="row">
          <div class="form-group col-sm-4">
            <input class="form-control input-sm" type="text" name="codigo_sistema" id="codigo_sistema">
          </div> 
          <div class="form-group col-sm-4">
            <input class="form-control input-sm" type="text" name="codigo_activo" id="codigo_activo">
          </div>
          <div class="form-group col-sm-4">
            <select name="altasbajas[]" id="altasbajas" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              <!-- <option value="">SELECCIONE</option> -->
              <option value="1">Activos Dados de Baja</option>              
            </select>
          </div>   
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="botonBuscarActivoFijo" name="botonBuscarActivoFijo" onclick="botonBuscarActivoFijo()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>

<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos...</h4>
     <p class="text-white">Aguarde un momento por favor.</p>  
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#EditarPC').click(function(){
      var cod_activo_b=document.getElementById("cod_activo_b").value;
      var obs_baja=$('#obs_baja').val();
      var fecha_baja=$('#fecha_baja').val();
      if(fecha_baja ==null || fecha_baja==""){
        Swal.fire('ERROR!','Por favor, ingrese fecha de baja.','error'); 
      }else{
        save_obs_AF_baja(cod_activo_b,obs_baja,1,fecha_baja);
      }
      
    });
  });
</script>
