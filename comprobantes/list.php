<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUnidad=$_SESSION['globalUnidad'];
$globalGestion=$_SESSION['globalNombreGestion'];

$dbh = new Conexion();

// Preparamos

// $stmt = $dbh->prepare("SELECT (select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad, c.cod_gestion, 
// (select m.nombre from monedas m where m.codigo=c.cod_moneda)moneda, 
// (select t.nombre from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero,c.codigo, c.glosa,ec.nombre,c.cod_estadocomprobante
// from comprobantes c join estados_comprobantes ec on c.cod_estadocomprobante=ec.codigo where c.cod_estadocomprobante!=2  order by c.fecha desc, c.numero desc");

$sql="SELECT (select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad, c.cod_gestion, 
(select m.nombre from monedas m where m.codigo=c.cod_moneda)moneda, 
(select t.abreviatura from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero,c.codigo, c.glosa,ec.nombre,c.cod_estadocomprobante
from comprobantes c join estados_comprobantes ec on c.cod_estadocomprobante=ec.codigo where c.cod_estadocomprobante!=2 ";
if($globalAdmin!=1){
  $sql.=" and c.cod_unidadorganizacional='$globalUnidad' ";
}
$sql.=" and c.cod_gestion='$globalGestion' order by c.fecha desc, unidad, tipo_comprobante, c.numero desc";

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


// busquena por Oficina
$stmtUO = $dbh->prepare("SELECT codigo,nombre  from unidades_organizacionales where cod_estado=1");
$stmtUO->execute();
$stmtUO->bindColumn('nombre', $nombreUnidad_x);
$stmtUO->bindColumn('codigo', $codigo_uo);

// busquena por tipo de comprobante
$stmtTipoComprobante = $dbh->prepare("SELECT codigo,nombre  from tipos_comprobante where cod_estadoreferencial=1");
$stmtTipoComprobante->execute();
$stmtTipoComprobante->bindColumn('nombre', $nombre_tipo_comprobante);
$stmtTipoComprobante->bindColumn('codigo', $codigo_tipo_co);
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
                  <h4 class="card-title"><?=$moduleNamePlural?></h4>
                </div>
                <div class="card-body">
                  
                  <!-- //busqueda de comprobantes por cuenta -->
                  <!-- <div class="col-sm-4 float-right">
                      <div class="input-group no-border">
                        <input type="text" value="" id="buscar_comprobantes" class="form-control" placeholder="Buscar..." onChange="buscarComprobantes('registrado')" OnKeyUp="buscarComprobantes('registrado')">
                        <a href="#" onclick="buscarComprobantes('registrado')" class="btn btn-white btn-round btn-just-icon">
                          <i class="material-icons">search</i>
                          <div class="ripple-container"></div>
                        </a>
                      </div>
                  </div> -->             
                  <button type="button" class="btn btn-white btn-round btn-just-icon" data-toggle="modal" data-target="#modalBuscador">
                    <i class="material-icons" title="Buscador">search</i>
                  </button>


                  <div class="" id="data_comprobantes">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>                          
                          <th class="text-center small">Oficina</th>
                          <th class="text-center small">Fecha</th>
                          <th class="text-center small">Tipo/#</th>
                          <th class="text-left small">Glosa</th>
                          <th class="text-center small">Estado</th>
                          <th class="text-center small">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
						            $index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          switch ($estadoC) {
                            case 1:
                             $btnEstado="btn-info";$estadoIcon="how_to_vote";
                            break;
                            case 2:
                            $btnEstado="btn-danger";$estadoIcon="thumb_down";
                            break;
                            case 3:
                              $btnEstado="btn-warning";$estadoIcon="thumb_up";
                            break;
                          }
                        ?>
                        <tr>
                          
                          <td align="text-center small"><?=$index;?></td>                          
                          <td class="text-center small"><?=$nombreUnidad;?></td>
                          <td class="text-center small"><?=strftime('%Y/%m/%d',strtotime($fechaComprobante));?></td>
                          <td class="text-center small"><?=$nombreTipoComprobante;?>-<?=$nroCorrelativo;?></td>
                          <!--td><?=$nombreMoneda;?></td-->
                          <td class="text-left small"><?=$glosaComprobante;?></td>
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estadoComprobante;?>  <span class="material-icons small"><?=$estadoIcon?></span></button></td>
                          <td class="td-actions text-right">
                            <div class="btn-group dropdown">
                              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons"><?=$iconImp;?></i>
                              </button>
                              <div class="dropdown-menu">
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
                            <a href='<?=$urlArchivo;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-default">
                              <i class="material-icons">attachment</i>
                            </a>
                            <a href='<?=$urlEdit2;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button>
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
      				<div class="card-footer fixed-bottom">
                <a href="#" onclick="javascript:window.open('<?=$urlRegister2;?>')" class="<?=$buttonNormal;?>">Registrar</a>
              </div>		  
            </div>
          </div>  
        </div>
    </div>


<!-- Modal busqueda de comprobantes-->
<div class="modal fade" id="modalBuscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Buscador</h4>
      </div>
      <div class="modal-body ">
        <div class="row">
            <label class="col-sm-4 col-form-label text-center">Buscar por Oficina</label> 
            <label class="col-sm-4 col-form-label text-center">Buscar por Fecha</label>                  
            <label class="col-sm-4 col-form-label text-center">Buscar por Tipo</label>                                
        </div> 
        <div class="row">
          <div class="form-group col-sm-4">
            
            <!-- <input class="form-control" type="text" name="OficinaBusqueda" id="OficinaBusqueda" onChange="buscarComprobantesUO()" OnKeyUp="buscarComprobantesUO()"> -->

            <select id="OficinaBusqueda" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true"> 
              <option value="">Seleccione uno</option>
              <?php while ($rowUO = $stmtUO->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_uo;?>"> <?=$nombreUnidad_x;?></option>
              <?php }?>
            </select>


          </div>
          <div class="form-group col-sm-4">
              
            <input class="form-control" type="date" name="fechaBusqueda" id="fechaBusqueda" onChange="buscarComprobantesFecha()" >
          </div>
          <div class="form-group col-sm-4">            
            <select id="tipoBusqueda" class="selectpicker form-control form-control-sm" data-style="btn btn-info" data-show-subtext="true" data-live-search="true"> 
              <option value="">Seleccione</option>
              <?php while ($rowTC = $stmtTipoComprobante->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_tipo_co;?>"> <?=$nombre_tipo_comprobante;?></option>
              <?php }?>
            </select>
            
          </div>

          <!-- 
                    <label class="col-sm-1 col-form-label text-right" style="color:#0B2161;font-size: 16px"><b>Oficina</b></label>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <input style="background-color:#F3F781;text-align: center" class="form-control"  />
                        </div>
                    </div>
                    <label class="col-sm-1 col-form-label text-right" style="color:#0B2161;font-size: 16px"><b>Fecha</b></label>
                    <div class="col-sm-2">
                    <div class="form-group">
                        <input style="background-color:#F3F781;text-align: center" class="form-control" name="numero" id="numero" />
                    </div>
                    </div>
                    <label class="col-sm-1 col-form-label text-right" style="color:#0B2161;font-size: 16px"><b>Tipo</b></label>
                    <div class="col-sm-2">
                    <div class="form-group">
                        <input style="background-color:#F3F781;text-align: center" class="form-control" name="numero" id="numero"/>
                    </div>
                    </div>      -->               
        </div> 

      </div>

      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-success" id="EnviarCorreo" name="EnviarCorreo" data-dismiss="modal">Aceptar</button> -->
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  
  $(document).ready(function(){
    $('#OficinaBusqueda').change(function(){
      var valor=$("#OficinaBusqueda").val();
      ajax=nuevoAjax();
      ajax.open('GET', 'comprobantes/ajax_busquedaComprobanteUO.php?cod_uo='+valor+'&tipo=null&fecha=null',true);
      ajax.onreadystatechange=function() {
        if (ajax.readyState==4) {
          var contenedor=$("#data_comprobantes");
          contenedor.html(ajax.responseText);
          $("#modalBuscador").modal("hide");
        }
      }
      ajax.send(null)
    });
    $('#tipoBusqueda').change(function(){
      var valor=$("#tipoBusqueda").val();
      ajax=nuevoAjax();
      ajax.open('GET', 'comprobantes/ajax_busquedaComprobanteUO.php?cod_uo=null&tipo='+valor+"&fecha=null",true);
      ajax.onreadystatechange=function() {
        if (ajax.readyState==4) {
          var contenedor=$("#data_comprobantes");
          contenedor.html(ajax.responseText);
          $("#modalBuscador").modal("hide");
        }
      }
      ajax.send(null)
    });
  });


</script>