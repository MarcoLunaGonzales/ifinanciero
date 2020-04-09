<?php

require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$fechaActual=date("d/m/Y");
$codCuenta=$_GET['cod_cuenta'];
$codCuentaAuxiliar=$_GET['cod_cuenta_auxiliar'];
$tipoComprobanteX=$_GET['tipo_comprobante'];

$sqlZ="SELECT e.*,d.glosa,d.haber,d.debe,d.cod_cuentaauxiliar,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra FROM estados_cuenta e,comprobantes_detalle d where e.cod_comprobantedetalle=d.codigo and (d.cod_cuenta=$codCuenta) and e.cod_comprobantedetalleorigen=0 and e.cod_cuentaaux=$codCuentaAuxiliar order by e.fecha";
//echo $sqlZ;
?>
<table id="tablePaginatorReport" class="table table-bordered table-condensed table-warning">
  <thead>
    <tr class="">
      <th class="text-left">Of</th>
      <th class="text-left">Tipo</th>
      <th class="text-left">#</th>
      <th class="text-left">FechaComp</th>
      <th class="text-left">FechaEC</th>
      <th class="text-left">Proveedor/Cliente</th>
      <th class="text-left">Glosa</th>
      <th class="text-right">D&eacute;bito</th>
      <th class="text-right">Cr&eacute;dito</th>
      <th class="text-right">Saldo</th>
      <th class="text-left">-</th>
    </tr>
  </thead>
  <tbody id="tabla_estadocuenta">
<?php
  
  
  $stmt = $dbh->prepare($sqlZ);
  $stmt->execute();
  $i=0;$saldo=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$row['codigo'];
    $estiloFila="bg-white";$codOrigen=0;
    if(isset($_GET['comprobante_origen'])){
      if($_GET['comprobante_origen']==$codigoX){
       $estiloFila="bg-plomo";$codOrigen=1;
      }
    }

    $saldoIndividual=0;
    
    $codPlanCuentaX=$row['cod_plancuenta'];
    $codCompDetX=$row['cod_comprobantedetalle'];
    $codProveedorX=$row['cod_proveedor'];
    $fechaX=$row['fecha'];
    $fechaX=strftime('%d/%m/%Y',strtotime($fechaX));
    $montoX=$row['monto'];
    $glosaX=$row['glosa'];
    $debeX=$row['debe'];
    $haberX=$row['haber'];
    $codigoExtra=$row['extra'];
    $codCuentaAuxX=$row['cod_cuentaaux'];
    $glosaAuxiliar=$row['glosa_auxiliar'];
    $glosaMostrar="";
    if($glosaAuxiliar!=""){
      $glosaMostrar=$glosaAuxiliar;
    }else{
      $glosaMostrar=$glosaX;
    }
    list($tipoComprobante, $numeroComprobante, $codUnidadOrganizacional, $mesComprobante, $fechaComprobante)=explode("|", $codigoExtra);
    $nombreTipoComprobante=abrevTipoComprobante($tipoComprobante)."-".$mesComprobante;
    $nombreUnidadO=abrevUnidad_solo($codUnidadOrganizacional);

    $fechaComprobante=strftime('%d/%m/%Y',strtotime($fechaComprobante));
    //SACAMOS CUANTO SE PAGO DEL ESTADO DE CUENTA.
    $sqlContra="SELECT sum(monto)as monto from estados_cuenta e where e.cod_comprobantedetalleorigen='$codigoX'";
//    echo $sqlContra;
    $stmtContra = $dbh->prepare($sqlContra);
    $stmtContra->execute();
    $montoContra=0;
    while ($rowContra = $stmtContra->fetch(PDO::FETCH_ASSOC)) {
      $montoContra=$rowContra['monto'];
    }
 
    $proveedorX="";
    if($tipoComprobanteX==2){
      $proveedorX=obtenerProveedorCuentaAux($codCuentaAuxX);
    }
    if($tipoComprobanteX==1){
      $proveedorX=obtenerClienteCuentaAux($codCuentaAuxX);
    }

    if($tipoComprobanteX==1){
      $nombreProveedorClienteX=nameProveedorCliente(2,$codProveedorX);
    }
    if($tipoComprobanteX==2){
      $nombreProveedorClienteX=nameProveedorCliente(1,$codProveedorX);
    }
    $debeX=$montoContra;

    

    //Filtramos las cuentas que ya esten cerradas.
    
    
    $saldoIndividual=$montoX-$montoContra;
    if(isset($_GET['edicion'])){
      $edicion=$_GET['edicion'];
      if($edicion==1){
       if(($tipoComprobanteX!=3)&&$codOrigen==1){
        if($tipoComprobanteX==1){
         $saldoIndividual=$montoX;
         $montoContra=0;
        }else{     
         $saldoIndividual=$montoX;
         $montoContra=0;
        }     
       }      
      }
    }else{
      $edicion=0;
    }
    $saldo=$saldo+$saldoIndividual;
    if($montoContra<$montoX||$edicion!=0){
    ?>
    <tr class="<?=$estiloFila?> det-estados">
      <td class="text-center small"><?=$codigoX?>/<?=$nombreUnidadO;?></td>
      <td class="text-center small"><?=$nombreTipoComprobante;?></td>
      <td class="text-center small"><?=$numeroComprobante;?></td>
      <td class="text-left small"><?=$fechaComprobante;?></td>
      <td class="text-left small"><?=$fechaX;?></td>
      <td class="text-left small"><?=$proveedorX?></td>
      <td class="text-left small"><?=$glosaMostrar;?></td>
      <?php
      if($tipoComprobanteX==1){
      ?>
        <td class="text-right small"><?=formatNumberDec($montoX)?></td>
        <td class="text-right small"><?=formatNumberDec($montoContra)?></td>
      <?php
      }else{
      ?>
        <td class="text-right small"><?=formatNumberDec($montoContra)?></td>
        <td class="text-right small"><?=formatNumberDec($montoX)?></td>
      <?php  
      }
      ?>
      <td class="text-right small font-weight-bold"><?=formatNumberDec($saldoIndividual);?></td>
      <td>
        <input type="hidden" id="codigoCuentaAux<?=$i?>" value="<?=$codCuentaAuxX?>">
          <div class="form-check">
            <?php
              $valorCerrarEC=$codigoX."####".$codCuentaAuxX."####".$codProveedorX."####".$saldoIndividual;
              if($tipoComprobanteX!=3){
            ?>
              <a title="Cerrar EC" id="cuentas_origen_detalle<?=$i?>" href="#" onclick="agregarEstadoCuentaCerrar(<?=$i;?>,'<?=$valorCerrarEC;?>');" class="btn btn-sm btn-warning btn-fab"><span class="material-icons text-dark">double_arrow</span></a>
            <?php
              }
            ?>
          </div>
      </td>
    </tr>
    <?php
    $i++;
    }
  }
?>
    <tr>
      <td colspan="9">Saldo Total</td>
      <td class="text-center"><?=formatNumberDec($saldo);?></td>
    </tr>
  </tbody>
</table>
<?php
echo "@".$saldo;