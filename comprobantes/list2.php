<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUnidad=$_SESSION['globalUnidad'];
$globalGestion=$_SESSION['globalNombreGestion'];
$globalMesTrabajo=$_SESSION['globalMes'];

$dbh = new Conexion();

$codTipoComprobanteDefault="3";

//preparamos el array con los codigo de comprobante
$sqlArray="SELECT c.codigo,c.cod_tipocomprobante,(select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad,
(select t.abreviatura from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero
from comprobantes c join estados_comprobantes ec on c.cod_estadocomprobante=ec.codigo where c.cod_unidadorganizacional='$globalUnidad'
and c.cod_gestion='$globalGestion' and c.cod_tipocomprobante in ($codTipoComprobanteDefault) and MONTH(c.fecha)='$globalMesTrabajo' order by unidad, tipo_comprobante, c.numero asc ";
$stmtArray = $dbh->prepare($sqlArray);
$stmtArray->execute();
$stmtArray->bindColumn('codigo', $codigo_comprobante);
$array_cod_comprobante=array();
while ($row = $stmtArray->fetch(PDO::FETCH_BOUND)) {
  array_push($array_cod_comprobante, $codigo_comprobante);
}
$cantidad_itms=count($array_cod_comprobante);
if($cantidad_itms>0){
  $cod_comprobante_x=$array_cod_comprobante[0];
  $posicion=1;
}else{ $cod_comprobante_x=null;
  $posicion=0;
}
// echo $cod_comprobante_x;

//el listado inicial
$sql="SELECT c.cod_tipocomprobante,(select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad, c.cod_gestion, 
(select m.nombre from monedas m where m.codigo=c.cod_moneda)moneda, 
(select t.abreviatura from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero,c.codigo, c.glosa,ec.nombre,c.cod_estadocomprobante,c.salvado_temporal
from comprobantes c join estados_comprobantes ec on c.cod_estadocomprobante=ec.codigo where  c.codigo=$cod_comprobante_x";
$sql.=" and c.cod_unidadorganizacional='$globalUnidad' ";
$sql.=" and c.cod_gestion='$globalGestion' order by unidad, tipo_comprobante, c.numero asc limit 1";
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
        <div id="data_comprobantes">
          <div class="card">
            <div class="card-header <?=$colorCard;?> card-header-icon">
              <div class="card-icon">
                <i class="material-icons"><?=$iconCard;?></i>
              </div>
              <h4 class="card-title"><?=$moduleNamePlural?></h4>                  
              <h4>
                <div class="row"> 
                  <div class="col-md-6" align="left">
                    <button title="Principio" type="button" style="padding: 0;font-size:10px;width:23px;height:23px;" class="btn btn-primary btn-sm btn-round " id="botonInicioComprobante" name="botonInicioComprobante" onclick="botonInicioComprobante()" ><<</button>
                    <button title="Anterior" type="button" style="padding: 0;font-size:10px;width:23px;height:23px;" class="btn btn-primary btn-sm btn-round " id="botonAnteriorComprobante" name="botonAnteriorComprobante" onclick="botonAnteriorComprobante()" ><</button>
                    <button title="Siguiente" type="button" style="padding: 0;font-size:10px;width:23px;height:23px;" class="btn btn-primary btn-sm btn-round " id="botonSiguienteComprobante" name="botonSiguienteComprobante" onclick="botonSiguienteComprobante()">></button>
                    <button title="Final" type="button" style="padding: 0;font-size:10px;width:23px;height:23px;" class="btn btn-primary btn-sm btn-round " id="botonFinComprobante" name="botonFinComprobante" onclick="botonFinComprobante()" >>></button>
                    <button title="posición - Total Items" type="button" style="padding: 0;font-size:10px;width:90px;height:23px;" class="btn btn-primary btn-sm btn-round" id="botonItems" name="botonItems" ><?=$posicion?> - <?=$cantidad_itms?></button>
                    <input type="number" name="intro_number" title="Buscar nro comprobante" id="intro_number" class="btn btn-primary btn-sm btn-round" style="padding: 0;font-size:10px;width:70px;height:23px;" onchange="input_buscar_comprobante()">
                    <!-- <input type="hidden" name="posicion" id="posicion" value="0"> -->
                  </div>
                                        
                  <div class="col-md-6" align="right"> 
                    <?php
                      $stmtTipoComprobante_x = $dbh->prepare("SELECT codigo,abreviatura,nombre from tipos_comprobante where cod_estadoreferencial=1 order by codigo asc");
                      $stmtTipoComprobante_x->execute();
                      $stmtTipoComprobante_x->bindColumn('codigo', $cod_tipo_comprobante_x);
                      $stmtTipoComprobante_x->bindColumn('abreviatura', $abreviatura_x);
                      $stmtTipoComprobante_x->bindColumn('nombre', $nombre_x);
                      while ($rowTC = $stmtTipoComprobante_x->fetch(PDO::FETCH_BOUND)) {?>
                        <button title="Filtrar por <?=$nombre_x?>" type="button" class="btn btn-success btn-sm btn-round " id="botonBuscarComprobanteIng" name="botonBuscarComprobanteIng" style="padding: 0;font-size:10px;width:50px;height:23px;" onclick="botonBuscarComprobanteIng2(<?=$cod_tipo_comprobante_x?>)"><?=$abreviatura_x?></button>
                      <?php }
                    ?>                  
                    <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscador" style="padding: 0;font-size:10px;width:30px;height:30px;">
                      <i class="material-icons" title="Buscador Avanzado">search</i>
                    </button> 
                  </div>  
                </div>
                                
              </h4>
            </div>      
            <div class="card-body">                                          
              <div class="" id="">
                <table id="" class="table table-condensed">
                  <thead>
                    <tr>
                      <th class="text-center">#</th>                          
                      <th class="text-center small">Oficina</th>
                      <th class="text-center small">Tipo/Número</th>
                      <th class="text-center small">Fecha</th>
                      <th class="text-center small">Glosa</th>
                      <th class="text-center small">Estado</th>
                      <th class="text-center small" width="15%">Actions</th>
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
                        
                        <div class="btn-group dropdown">
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
                        <a href='<?=$urlArchivo;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-default" title="Ver Adjuntos">
                          <i class="material-icons">attachment</i>
                        </a>
                        <?php 
          $codigoSol=obtenerCodigoSolicitudRecursosComprobante($codigo);
          if($codigoSol[0]!=0){
           ?>
           <a title=" Ver Solicitud de Recursos" target="_blank" href="<?=$urlVerSol;?>?cod=<?=$codigoSol[0];?>&comp=1" target="_blank" class="btn btn-success">
             <i class="material-icons">preview</i>
          </a>
          <a title="Imprimir Solicitud de Recursos" href='#' onclick="javascript:window.open('<?=$urlImpSol;?>?sol=<?=$codigoSol[0];?>&mon=1')" class="btn btn-info">
            <i class="material-icons"><?=$iconImp;?></i>
          </a><?php
          }
          ?>
                        <?php
                        if($codigoSol[1]==0){
                          if($existeCuenta==0){
                                      $codCajaChica=existeCajaChicaRelacionado($codigo);
              if($codCajaChica>0){
                $nombreCaja=obtenerObservacionCajaChica($codCajaChica);
                ?><a href='#' rel="tooltip" class="btn btn-primary" title="No Editable Caja Chica :<?=$nombreCaja?>">
                      <i class="material-icons"><?=$iconEdit;?></i>
                    </a><?php
               }else{
                ?><a href='<?=$urlEdit3;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>" title="Editar">
                      <i class="material-icons"><?=$iconEdit;?></i>
                    </a><?php
               }
                                    }else{
                                      ?>
                                      <a href='#' rel="tooltip" class="btn btn-danger" title="No Editable <?=obtenerNombresComprobanteCerrados($codigo)?>">
                                        <i class="material-icons text-dark"><?=$iconEdit;?></i>
                                      </a>
                                  <?php
                                    }
                        }
                        ?>
                        <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')" title="Anular">
                          <i class="material-icons"><?=$iconDelete;?></i>
                        </button>                        
                        <?php }?>
                      </td>
                    </tr>
                    <?php
                          $index++;
                        }
                    ?>
                  </tbody>
                </table>
              </div>
              <!-- card detalle -->
              <?php
              if($estadoC!=2){?>
              <div class="card">            
                <h4 class="card-title" align="center">Detalle</h4>                                    
                <div class="card-body">                                    
                  <div class="" id="">
                    <table id="" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th class="text-center">Centro Costos</th>
                          <th class="text-center small">Cuenta</th>
                          <th class="text-center small">Nombre Cuenta</th>
                          <th class="text-center small">Debe</th>
                          <th class="text-center small">Haber</th>
                          
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                        $totalDebe=0;
                        $totalHaber=0;
                        $indexDetalle=1;
                        $data = obtenerComprobantesDetImp($cod_comprobante_x);                                    
                        while ($rowDet = $data->fetch(PDO::FETCH_ASSOC)) {
                          $totalDebe+=$rowDet['debe'];
                          $totalHaber+=$rowDet['haber'];
                        ?>
                        <tr>                    
                          <td align="text-center small"><?=$indexDetalle;?></td>                          
                          <td class="text-center small"><?=$rowDet['unidadAbrev']?>/<?=$rowDet['abreviatura']?></td>
                          <td class="text-center small"><?=$rowDet['numero']?></td>
                          <td class="text-left small"><?=$rowDet['nombre']?></td>
                          <td class="text-right small"><?=formatNumberDec($rowDet['debe']);?></td>
                          <td class="text-right small"><?=formatNumberDec($rowDet['haber']);?></td>
                        </tr>
                        <?php
                              $indexDetalle++;
                            }
                        ?>
                        <tr>                    
                          <td align="text-center small" colspan="4">&nbsp;</td>                          
                          <td class="text-right small font-weight-bold"><?=formatNumberDec($totalDebe);?></td>
                          <td class="text-right small font-weight-bold"><?=formatNumberDec($totalHaber);?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <?php }
              ?>
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
        <button type="button" class="btn btn-success" id="botonBuscarComprobante" name="botonBuscarComprobante" onclick="botonBuscarComprobante2()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Enviando Correo..</h4>
     <p class="text-white">Aguarde un momento por favor.</p>  
  </div>
</div>

<?php 
// var_dump($array_cod_comprobante);

for ($i=0; $i < $cantidad_itms; $i++) {
  ?>  
  <script>array_comprobantes_general.push(<?=$array_cod_comprobante[$i]?>);
    // alert(array_comprobantes_general);
  </script><?php                    
}
?>