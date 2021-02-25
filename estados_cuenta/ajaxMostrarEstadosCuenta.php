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
$anioActual=nameGestion($globalGestion);
$fechaActual=date("d/m/Y");
$codCuenta=$_POST['cod_cuenta'];
$codCuentaAuxiliar=$_POST['cod_cuenta_auxiliar'];
$tipoComprobanteX=$_POST['tipo_comprobante'];
$cerrarEstadoCuenta=$_POST["cerrar_ec"];

if($codCuentaAuxiliar!=0){
  $sqlZ="SELECT e.*,d.glosa,d.haber,d.debe,d.cod_cuentaauxiliar,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra, c.codigo as codigocomprobante FROM estados_cuenta e,comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 and  e.cod_comprobantedetalle=d.codigo and (d.cod_cuenta=$codCuenta) and e.cod_comprobantedetalleorigen=0 and e.cod_cuentaaux=$codCuentaAuxiliar and year(e.fecha)=$anioActual order by c.cod_tipocomprobante,c.fecha,c.numero";
}else{
  $sqlZ="SELECT e.*,d.glosa,d.haber,d.debe,d.cod_cuentaauxiliar,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra, c.codigo as codigocomprobante FROM estados_cuenta e,comprobantes_detalle d, comprobantes c where c.codigo=d.cod_comprobante and c.cod_estadocomprobante<>2 and  e.cod_comprobantedetalle=d.codigo and (d.cod_cuenta=$codCuenta) and e.cod_comprobantedetalleorigen=0 and and year(e.fecha)=$anioActual order by c.cod_tipocomprobante,c.fecha,c.numero";
}


//echo $sqlZ;

?>
<table id="libreta_bancaria_reporte_modal" class="table table-bordered table-condensed">
  <thead>
    <tr style="background:#746F72; color:#fff;">
      <th class="text-left">Of</th>
      <th class="text-left">Tipo/#</th>
      <th class="text-left">FechaComp</th>
      <th class="text-left">FechaEC</th>
      <th class="text-left">Proveedor/Cliente</th>
      <th class="text-left">Glosa</th>
      <th class="text-right">Debe</th>
      <th class="text-right">Haber</th>
      <th class="text-right">Saldo</th>
      <th class="text-left">-</th>
    </tr>
  </thead>
  <tbody id="tabla_estadocuenta">
<?php
  
  
  $stmt = $dbh->prepare($sqlZ);
  $stmt->execute();
  $i=0;$saldo=0;
  $saldoIndividual=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$row['codigo'];
    $estiloFila="bg-white";$codOrigen=0;
    if(isset($_POST['comprobante_origen'])){
      if($_POST['comprobante_origen']==$codigoX){
       $estiloFila="bg-plomo";$codOrigen=1;
      }
    }

    
    
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
    $codOficinaDetalle=obtenerCodigoUnidadComprobanteDetalle($row['cod_comprobantedetalle']);
    $codAreaDetalle=obtenerCodigoAreaComprobanteDetalle($row['cod_comprobantedetalle']);

    $existeEstado=0;$colorFilaExiste="";
    $montoContraAjax=0;
    if(isset($_POST["estados_cuenta"])){
      $estados_cuenta=json_decode($_POST["estados_cuenta"]);
      for ($estado=0; $estado <count($estados_cuenta) ; $estado++) { 
        for ($nrofila=0; $nrofila <count($estados_cuenta[$estado]) ; $nrofila++) { 
          if(isset($estados_cuenta[$estado][$nrofila]->cod_comprobantedetalle)){
            if($codigoX==$estados_cuenta[$estado][$nrofila]->cod_comprobantedetalle){
              $existeEstado=$estado+1;
              $montoContraAjax+=(float)$estados_cuenta[$estado][$nrofila]->monto;
              $colorFilaExiste='style="background:#FF3333 !important;color:#fff !important;"';
            }
          }
        }
      }
    }
    


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
    if(isset($_POST['edicion'])){
      $codigoComprobante=$_POST['codigo_comprobante'];
      $sqlContra="SELECT sum(e.monto)as monto from estados_cuenta e, comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and c.cod_estadocomprobante<>2 and cd.codigo=e.cod_comprobantedetalle and e.cod_comprobantedetalleorigen='$codigoX' and cd.cod_comprobante!='$codigoComprobante'";
    }else{
      $sqlContra="SELECT sum(monto)as monto from estados_cuenta e, comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and c.cod_estadocomprobante<>2 and e.cod_comprobantedetalleorigen='$codigoX'";
    }
    
//    echo $sqlContra;
    $stmtContra = $dbh->prepare($sqlContra);
    $stmtContra->execute();
    $montoContra=0;
    while ($rowContra = $stmtContra->fetch(PDO::FETCH_ASSOC)) {
      $montoContra=$rowContra['monto'];
    }
 
    if(($montoContra+$montoContraAjax)<$montoX){
      $existeEstado=0;$colorFilaExiste=""; 
    }
    $proveedorX="";


    /*if($tipoComprobanteX==2){
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
    }*/
    $nombreCuentaAuxEstadoCuenta=nameCuentaAuxiliar($codCuentaAuxX);
    $debeX=$montoContra;

    //Filtramos las cuentas que ya esten cerradas.

    $saldoIndividual+=$montoX-$montoContra;
    if(isset($_POST['edicion'])){
      $edicion=$_POST['edicion'];
    }else{
      $edicion=0;
    }
    $saldo=$saldoIndividual;
    //$saldoFila=$montoContra-$montoX;
    if($montoContra<$montoX){
    ?>
    <tr class="<?=$estiloFila?> det-estados" <?=$colorFilaExiste?>>
      <td class="text-center small"><?=$nombreUnidadO;?></td>
      <td class="text-center small"><?=$nombreComprobante;?></td>
      <td class="text-left small"><?=$fechaComprobante;?></td>
      <td class="text-left small"><?=$fechaX;?></td>
      <td class="text-left small"><?=$nombreCuentaAuxEstadoCuenta?></td>
      <td class="text-left small"><?=$glosaMostrar;?></td>
      <?php
      if($tipoComprobanteX==909090){ //909090 =1 anterior cuando se seleccionaba el tipo
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
      <td class="text-right small font-weight-bold"><?=formatNumberDec($saldoIndividual);?><br><small class="text-danger"><?=formatNumberDec($montoContraAjax);?></small></td>
      <td>
        <input type="hidden" id="codigoCuentaAux<?=$i?>" value="<?=$codCuentaAuxX?>">
          <div class="form-check">
            <?php
              $valorCerrarEC=$codigoX."####".$codCuentaAuxX."####".$codProveedorX."####".$montoX."####".$montoContra;
              if( $cerrarEstadoCuenta==1 ){
                if($codCuentaAuxiliar!=0){
                  if($existeEstado==0){
            ?>
              <a title="Cerrar EC" id="cuentas_origen_detalle<?=$i?>" href="#" onclick="ponerCentroCostoComprobanteDetalle(<?=$codOficinaDetalle?>,<?=$codAreaDetalle?>);agregarEstadoCuentaCerrar(<?=$i;?>,'<?=$valorCerrarEC;?>');return false;" class="btn btn-sm btn-warning btn-fab"><span class="material-icons text-dark">double_arrow</span></a>
            <?php        
                    
                  }else{
                    echo "Fila: ".$existeEstado;
                  }
                }else{
                  $codigoCuentaAux=$codCuentaAuxX;
                  $nombreCuentaAux=nameCuentaAuxiliar($codigoCuentaAux);
                  $codigoCuenta=$codPlanCuentaX;
                  $numeroCuenta=obtieneNumeroCuenta($codigoCuenta);
                  $nombreCuenta=nameCuenta($codigoCuenta);
            ?>
              <a title="Cerrar EC" id="cuentas_origen_detalle<?=$i?>" href="#" onclick="filaActiva=$('#estFila').val();setBusquedaCuenta('<?=$codigoCuenta?>','<?=$numeroCuenta?>','<?=$nombreCuenta?>','<?=$codigoCuentaAux?>','<?=$nombreCuentaAux?>');ponerCentroCostoComprobanteDetalle(<?=$codOficinaDetalle?>,<?=$codAreaDetalle?>);agregarEstadoCuentaCerrar(<?=$i;?>,'<?=$valorCerrarEC;?>');filaActiva=$('#cantidad_filas').val();" class="btn btn-sm btn-warning btn-fab"><span class="material-icons text-dark">double_arrow</span></a>
            <?php      
                }
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
    <tr style="background:#746F72; color:#fff;">
      <td colspan="8">Saldo Total</td>
              <td class="d-none"></td>
              <td class="d-none"></td>
              <td class="d-none"></td>                        
              <td class="d-none"></td>
              <td class="d-none"></td>
              <td class="d-none"></td>
              <td class="d-none"></td>
      <td class="text-right font-weight-bold"><?=formatNumberDec($saldo);?></td>
      <td></td>
    </tr>
  </tbody>
  <tfoot>
      <tr style="background:#746F72; color:#fff;">
        <th class="small text-left"><small>Of</small></th>
      <th class="small text-left"><small>Tipo/#</small></th>
      <th class="small text-left"><small>FechaComp</small></th>
      <th class="small text-left"><small>FechaEC</small></th>
      <th class="small text-left"><small>Proveedor/Cliente</small></th>
      <th class="small text-left"><small>Glosa</small></th>
      <th class="small text-right"><small>D&eacute;bito</small></th>
      <th class="small text-right"><small>Cr&eacute;dito</small></th>
      <th class="small text-right"><small>Saldo</small></th>
      <td class="small text-left"><small>*</small></td>     
      </tr>
    </tfoot>
</table>
<?php
echo "@".$saldo;