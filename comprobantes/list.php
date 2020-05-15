<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'functions.php';
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

$sql="SELECT c.cod_tipocomprobante,(select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad, c.cod_gestion, 
(select m.nombre from monedas m where m.codigo=c.cod_moneda)moneda, 
(select t.abreviatura from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero,c.codigo, c.glosa,ec.nombre,c.cod_estadocomprobante
from comprobantes c join estados_comprobantes ec on c.cod_estadocomprobante=ec.codigo where c.cod_estadocomprobante!=2 ";

//if($globalAdmin!=1){
  $sql.=" and c.cod_unidadorganizacional='$globalUnidad' ";
//}

$sql.=" and c.cod_gestion='$globalGestion' order by c.fecha desc, unidad, tipo_comprobante, c.numero desc limit 100";

// echo $sql;

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
                  <h4 class="card-title"><?=$moduleNamePlural?></h4>
                  <h4 align="right">
                    <?php
                      $stmtTipoComprobante_x = $dbh->prepare("SELECT codigo,abreviatura,nombre from tipos_comprobante where cod_estadoreferencial=1 order by codigo asc");
                      $stmtTipoComprobante_x->execute();
                      $stmtTipoComprobante_x->bindColumn('codigo', $cod_tipo_comprobante_x);
                      $stmtTipoComprobante_x->bindColumn('abreviatura', $abreviatura_x);
                      $stmtTipoComprobante_x->bindColumn('nombre', $nombre_x);
                      while ($rowTC = $stmtTipoComprobante_x->fetch(PDO::FETCH_BOUND)) {?>
                        <button title="Filtrar por <?=$nombre_x?>" type="button" class="btn btn-success btn-sm btn-round " id="botonBuscarComprobanteIng" name="botonBuscarComprobanteIng" onclick="botonBuscarComprobanteIng(<?=$cod_tipo_comprobante_x?>)"><?=$abreviatura_x?></button>
                      <?php }
                    ?>                  
                    <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscador">
                      <i class="material-icons" title="Buscador Avanzado">search</i>
                    </button>                      
                  </h4>
                </div>      
                <div class="card-body">
                  <div class="" id="data_comprobantes">
                    <table id="tablePaginator" class="table table-condensed">
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
                          if($codTipoC==3){
                             $existeCuenta=obtenerEstadoCuentaSaldoComprobante($codigo);
                          }
                          $mes=date('n',strtotime($fechaComprobante));
                          // $mes=date("j",$fechaComprobante);
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
                          <td class="text-center small"><?=$nombreComprobante;?></td>
                          <td class="text-center small"><?=strftime('%d/%m/%Y',strtotime($fechaComprobante));?></td>
                          
                          <!--td><?=$nombreMoneda;?></td-->
                          <td class="text-left small"><?=$glosaComprobante;?></td>
                          <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estadoComprobante;?>  <span class="material-icons small"><?=$estadoIcon?></span></button></td>
                          <td class="td-actions text-right">
                            <div class="btn-group">
                            <div class="dropdown">
                              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ver Comprobante">
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
                            <div class="dropdown">
                              <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Ver Comprobante">
                                <i class="material-icons">more_horiz</i>
                              </button>
                              <div class="dropdown-menu">
                                <a href='<?=$urlArchivo;?>?codigo=<?=$codigo;?>' target="_blank" class="dropdown-item" title="Ver Adjuntos">
                                  <i class="material-icons text-default">attachment</i> Adjuntos
                                </a>
                                <?php
                                if($existeCuenta==0){
                                ?>
                                <a href='<?=$urlEdit3;?>?codigo=<?=$codigo;?>' target="_blank" class="dropdown-item" title="Editar">
                                  <i class="material-icons text-success"><?=$iconEdit;?></i> Editar
                                </a>
                                <?php
                                }
                                ?>
                                <a href="#" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')" title="Anular">
                                  <i class="material-icons text-danger"><?=$iconDelete;?></i> Eliminar
                                </a>
                              </div>
                            </div>
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
      				<div class="card-footer fixed-bottom">
                <a href="#" onclick="javascript:window.open('<?=$urlRegister2;?>')" class="<?=$buttonNormal;?>">Registrar</a>
              </div>		  
            </div>
          </div>  
        </div>
</div>


<!-- Modal busqueda de comprobantes-->
<div class="modal fade" id="modalBuscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Buscador de Comprobantes</h4>
      </div>
      <div class="modal-body ">
        <div class="row">
            <label class="col-sm-3 col-form-label text-center">Oficina</label> 
            <label class="col-sm-6 col-form-label text-center">Fechas</label>                  
            <label class="col-sm-3 col-form-label text-center">Tipo</label>                                
        </div> 
        <div class="row">
          <div class="form-group col-sm-3">
    <!--         <select class="selectpicker form-control" title="Seleccione una opcion" name="areas[]" id="areas" data-style="select-with-transition" data-size="5" data-actions-box="true" multiple required> -->

            <select  name="OficinaBusqueda[]" id="OficinaBusqueda" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              <option value="0"></option>
              <?php while ($rowUO = $stmtUO->fetch(PDO::FETCH_BOUND)) { ?>
                <option <?=($globalUnidad==$codigo_uo)?"selected":"";?> value="<?=$codigo_uo;?>"> <?=$nombreUnidad_x;?></option>                
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
            <select name="tipoBusqueda[]" id="tipoBusqueda" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
              <option value="0"></option>
              <?php while ($rowTC = $stmtTipoComprobante->fetch(PDO::FETCH_BOUND)) { ?>
                <option value="<?=$codigo_tipo_co;?>"> <?=$nombre_tipo_comprobante;?></option>
              <?php }?>
            </select>
            
          </div>              
        </div> 
        <div class="row">          
          <label class="col-sm-2 col-form-label text-center">#Cbte</label>
          <label class="col-sm-5 col-form-label text-center">Cuenta</label>
          <label class="col-sm-5 col-form-label text-center">Glosa</label>
        </div> 
        <div class="row">          
          <div class="form-group col-sm-2">
            <input class="form-control input-sm" type="number" name="nro_comprobante" id="nro_comprobante"  >
          </div>           
          <div class="form-group col-sm-5">
            <!-- <input class="form-control input-sm" type="number" name="nro_cuenta" id="nro_cuenta"  > -->            
                    <!-- <input class="form-control" type="text" name="cuenta_auto" id="cuenta_auto" placeholder="[numero] y nombre de cuenta" required />
                    <input class="form-control" type="hidden" name="cuenta_auto_id" id="cuenta_auto_id" required/> -->
            <?php                    
              //plan de cuentas
              $query_cuentas = "SELECT codigo,numero,nombre from plan_cuentas where cod_estadoreferencial=1 and nivel=5";
              $statementCuentas = $dbh->query($query_cuentas);
              ?>
              <select name="cuenta_auto_id" id="cuenta_auto_id" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required data-show-subtext="true" data-live-search="true">
                <option value=""></option>
                <?php while ($row = $statementCuentas->fetch()){ ?>
                    <option value="<?=$row["codigo"];?>"><?=$row["numero"];?> - <?=$row["nombre"];?></option>
                <?php } ?>
              </select>

          </div>           
          <div class="form-group col-sm-5">
            <input class="form-control input-sm" type="text" name="glosaBusqueda" id="glosaBusqueda"  >
          </div>           
        </div> 

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="botonBuscarComprobante" name="botonBuscarComprobante" onclick="botonBuscarComprobante()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>
