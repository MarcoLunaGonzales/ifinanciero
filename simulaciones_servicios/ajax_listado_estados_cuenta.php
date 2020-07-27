<?php
session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../styles.php';
require_once '../layouts/librerias.php';;
$dbh = new Conexion();
$saldo_solfac=$_GET['saldo'];
$tipo_listado=$_GET['tipo_listado'];
$listarLib=1;
?>
<style>
  tfoot input {
    width: 100%;
    padding: 3px;
  }
</style> 

<?php


// session_start();
// $globalAdmin=$_SESSION["globalAdmin"];
// $globalGestion=$_SESSION["globalGestion"];
// $globalUnidad=$_SESSION["globalUnidad"];
// $globalArea=$_SESSION["globalArea"];

$fechaActual=date("d/m/Y");
$codCuenta=obtenerValorConfiguracion(62);
$codCuentaAuxiliar=obtenerValorConfiguracion(63);

$tipoComprobanteX=1;
$cerrarEstadoCuenta=1;


$sqlZ="SELECT e.*,d.glosa,d.haber,d.debe,d.cod_cuentaauxiliar,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra, c.codigo as codigocomprobante FROM estados_cuenta e,comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 and  e.cod_comprobantedetalle=d.codigo and (d.cod_cuenta=$codCuenta) and e.cod_comprobantedetalleorigen=0 order by e.fecha";
//echo $sqlZ; //and e.cod_cuentaaux=$codCuentaAuxiliar

?>
<table id="estados_cuenta_reporte_modal" class="table table-condensed table-bordered table-sm" style="width:100% !important;">
  <thead>
    <tr style="background:#21618C; color:#fff;">
      <th class="text-left">Of</th>
      <th class="text-left">Tipo/#</th>
      <th class="text-left">FechaComp</th>
      <th class="text-left">FechaEC</th>
      <th class="text-left">Proveedor/Cliente</th>
      <th class="text-left">Glosa</th>
      <th class="text-right">Debe</th>
      <th class="text-right">Haber</th>
      <th class="text-right">Saldo</th>
      <th class="text-left">*</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $stmt = $dbh->prepare($sqlZ);
    $stmt->execute();
    $i=0;$saldo=0;
    $saldoIndividual=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $codigoX=$row['codigo'];
      $estiloFila="bg-white";$codOrigen=0;  
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
      $codigoComprobante=$row['codigocomprobante'];
      $nombreComprobante=nombreComprobante($codigoComprobante);
      
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
      $sqlContra="SELECT sum(monto)as monto from estados_cuenta e, comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and c.cod_estadocomprobante<>2 and e.cod_comprobantedetalleorigen='$codigoX'";    
      
    //    echo $sqlContra;
      $stmtContra = $dbh->prepare($sqlContra);
      $stmtContra->execute();
      $montoContra=0;
      while ($rowContra = $stmtContra->fetch(PDO::FETCH_ASSOC)) {
        $montoContra=$rowContra['monto'];
      } 
      $proveedorX="";
      // if($tipoComprobanteX==2){
      //   $proveedorX=obtenerProveedorCuentaAux($codCuentaAuxX);
      // }

      if($tipoComprobanteX==1){
        $proveedorX=obtenerClienteCuentaAux($codCuentaAuxX);
      }
      if($tipoComprobanteX==1){
        $nombreProveedorClienteX=nameProveedorCliente(2,$codProveedorX);
      }
      // if($tipoComprobanteX==2){
      //   $nombreProveedorClienteX=nameProveedorCliente(1,$codProveedorX);
      // }    
      $debeX=$montoContra;
      //Filtramos las cuentas que ya esten cerradas.    
      $saldoIndividual+=$montoX-$montoContra;    
      $edicion=0;    
      $saldo=$saldoIndividual;        
      if($montoContra<$montoX){
      ?>
      <tr class="<?=$estiloFila?> det-estados">
        <td class="text-center small"><?=$nombreUnidadO;?></td>
        <td class="text-center small"><?=$nombreComprobante;?></td>
        <td class="text-left small"><?=$fechaComprobante;?></td>
        <td class="text-left small"><?=$fechaX;?></td>
        <td class="text-left small"><?=$proveedorX?></td>
        <td class="text-left small"><?=$glosaMostrar;?></td>
        
        <td class="text-right small"><?=formatNumberDec($montoContra)?></td>        
        <td class="text-right small"><?=formatNumberDec($montoX);?></td>
        <td class="text-right small font-weight-bold"><?=formatNumberDec($saldoIndividual);?></td>
        <td>
          <input type="hidden" id="codigoCuentaAux<?=$i?>" value="<?=$codCuentaAuxX?>">
            <div class="form-check">
              <?php
                // $valorCerrarEC=$codigoX."####".$codCuentaAuxX."####".$codProveedorX."####".$saldoIndividual;
                if( $cerrarEstadoCuenta==1 ){?>
                  <a title="Cerrar Estado de Cuenta" href="#" onclick="seleccionar_estado_cuenta_sol_fac(<?=$codigoX;?>);" class="btn btn-sm btn-warning btn-fab"><span class="material-icons text-dark">double_arrow</span></a><?php
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
      <td class="d-none"></td>
      <td class="d-none"></td>
      <td class="d-none"></td>
      <td class="d-none"></td>                        
      <td class="d-none"></td>
      <td class="d-none"></td>
      <td class="d-none"></td>
      <td class="d-none"></td>
      <td colspan="8">Saldo Total</td>
      <td class="text-right font-weight-bold"><?=formatNumberDec($saldo);?></td>
    </tr>
  </tbody>
  <tfoot>
    <tr style="background:#21618C; color:#fff;">
      <th class="text-left">Of</th>
      <th class="text-left">Tipo/#</th>
      <th class="text-left">FechaComp</th>
      <th class="text-left">FechaEC</th>
      <th class="text-left">Proveedor/Cliente</th>
      <th class="text-left">Glosa</th>
      <th class="text-right">Debe</th>
      <th class="text-right">Haber</th>
      <th class="text-right">Saldo</th>
      <th class="text-left">*</th>
    </tr>
  </tfoot>
</table>