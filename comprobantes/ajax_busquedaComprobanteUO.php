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
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$cod_uo=$_GET['cod_uo'];
$tipo=$_GET['tipo'];
$fechaI=$_GET['fechaI'];
$fechaF=$_GET['fechaF'];
$glosa=$_GET['glosa'];

$comprobante=$_GET['comprobante'];
$cuenta=$_GET['cuenta'];

// $unidadOrgString=implode(",", $cod_uo);



$sql="SELECT (select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad, c.cod_gestion, 
  (select m.nombre from monedas m where m.codigo=c.cod_moneda)moneda, 
  (select t.abreviatura from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero,c.codigo, c.glosa,ec.nombre,c.cod_estadocomprobante,c.salvado_temporal
  from comprobantes c, estados_comprobantes ec, comprobantes_detalle cd where c.cod_estadocomprobante=ec.codigo and cd.cod_comprobante=c.codigo ";  

if($cod_uo!=""){
  $sql.=" and c.cod_unidadorganizacional in ($cod_uo)";
}
if($tipo!=""){
  $sql.=" and c.cod_tipocomprobante in ($tipo)";  
}
if($fechaI!="" && $fechaF!=""){
  $sql.=" and c.fecha BETWEEN '$fechaI 00:00:00' and '$fechaF 23:59:59'"; 
}
if($glosa!=""){
  $sql.=" and c.glosa like '%$glosa%'";
}
if($comprobante!=""){
  $sql.=" and c.numero = $comprobante";
}
if($cuenta!=""){
  $sql.=" and cd.cod_cuenta=$cuenta";
}
$sql.=" GROUP BY c.codigo order by c.cod_tipocomprobante, c.numero desc;";

// echo $sql;

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
$stmt->bindColumn('salvado_temporal', $salvadoC);
?>
<table id="tablePaginator" class="table table-condensed">
  <thead>
    <tr>
      <th class="text-center">#</th>                          
      <th class="text-center small">Oficina</th>
      <th class="text-center small">Tipo/Número</th>
      <th class="text-center small">Fecha</th>
      <th class="text-left small">Glosa</th>
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
        <a href='<?=$urlArchivo;?>?codigo=<?=$codigo;?>' target="_blank" rel="tooltip" class="btn btn-default">
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
          if($codigoSol[1]==0){
            if($existeCuenta==0){
              $codCajaChica=existeCajaChicaRelacionado($codigo);
              if($codCajaChica>0){
                $nombreCaja=obtenerObservacionCajaChica($codCajaChica);
                ?><a href='#'rel="tooltip" class="btn btn-primary" title="No Editable Caja Chica :<?=$nombreCaja?>">
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
        <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
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

