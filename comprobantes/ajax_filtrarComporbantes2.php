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
$globalGestion=$_SESSION['globalNombreGestion'];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];


$codigo_tipo=$_GET['codigo'];


// $unidadOrgString=implode(",", $cod_uo);


$sqlArray="SELECT c.codigo,c.cod_tipocomprobante,(select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad,
(select t.abreviatura from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero
from comprobantes c join estados_comprobantes ec on c.cod_estadocomprobante=ec.codigo where c.cod_estadocomprobante!=2
and c.cod_unidadorganizacional='$globalUnidad'
and c.cod_gestion='$globalGestion'";

$sqlArray.=" and c.cod_tipocomprobante in ($codigo_tipo)";
$sqlArray.=" order by c.fecha desc, unidad, tipo_comprobante, c.numero desc limit 100";
//echo $sqlArray;
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
// echo $sql;
$sql="SELECT c.cod_tipocomprobante,(select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad, c.cod_gestion, 
(select m.nombre from monedas m where m.codigo=c.cod_moneda)moneda, 
(select t.abreviatura from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero,c.codigo, c.glosa,ec.nombre,c.cod_estadocomprobante
from comprobantes c join estados_comprobantes ec on c.cod_estadocomprobante=ec.codigo where c.cod_estadocomprobante!=2 and c.codigo=$cod_comprobante_x";
$sql.=" and c.cod_unidadorganizacional='$globalUnidad' ";
$sql.=" and c.cod_gestion='$globalGestion' order by c.fecha desc, unidad, tipo_comprobante, c.numero desc limit 1";


$stmt = $dbh->prepare($sql);
$stmt->execute();
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
?>
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
          <button title="posicion - Total Items" type="button" style="padding: 0;font-size:10px;width:70px;height:23px;" class="btn btn-primary btn-sm btn-round " id="botonItems" name="botonItems"><?=$posicion?> - <?=$cantidad_itms?></button>
          <!-- <input type="hidden" name="posicion" id="posicion" value="0"> -->
        </div>
                              
        <div class="col-md-6" align="right"> 
          <?php
            $stmtTipoComprobante_x = $dbh->prepare("SELECT codigo,abreviatura,nombre from tipos_comprobante where cod_estadoreferencial=1 order by abreviatura asc");
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
            <th class="text-center small">Tipo</th>
            <th class="text-center small">Corre.</th>
            <th class="text-center small">Fecha</th>
            <th class="text-left small">Glosa</th>
            <th class="text-center small">Estado</th>
            <th class="text-center small">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $index=1;
          while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
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
            <td class="text-center small"><?=$nombreTipoComprobante;?>-<?=$mes;?></td>
            <td class="text-center small"><?=$nroCorrelativo;?></td>
            <td class="text-center small"><?=strftime('%Y/%m/%d',strtotime($fechaComprobante));?></td>
            
            <!--td><?=$nombreMoneda;?></td-->
            <td class="text-left small"><?=$glosaComprobante;?></td>
            <td><button class="btn <?=$btnEstado?> btn-sm btn-link"><?=$estadoComprobante;?>  <span class="material-icons small"><?=$estadoIcon?></span></button></td>
            <td class="td-actions text-right">
              <div class="btn-group dropdown">
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
              <a href='<?=$urlArchivo;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-default" title="Ver Adjuntos">
                <i class="material-icons">attachment</i>
              </a>
              <?php
              if($existeCuenta==0){
                ?>
              <a href='<?=$urlEdit3;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>" title="Editar">
                <i class="material-icons"><?=$iconEdit;?></i>
              </a>
                <?php
              }
              ?>
              <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')" title="Anular">
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
    <div class="card">            
      <h4 class="card-title" align="center">Detalle</h4>
      <div class="card-body">                                    
        <div class="" id="">
          <table id="tablePaginator" class="table table-condensed">
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
              $data = obtenerComprobantesDetImp($codigo_comprobante);                                    
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
  </div>
</div>
<script>
  var array_comprobantes_general=[];
  var numFilasA=0;
</script>
<?php 
// var_dump($array_cod_comprobante);

for ($i=0; $i < $cantidad_itms; $i++) {
  ?>  
  <script>array_comprobantes_general.push(<?=$array_cod_comprobante[$i]?>);
    // alert(array_comprobantes_general);
  </script><?php                    
}
?>
