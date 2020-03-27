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
$tipo=$_GET['tipo'];
$tipoProveedorCliente=$_GET['tipo_proveedorcliente'];
//echo "tipo: ".$tipo." tipoproveecli:".$tipoProveedorCliente."";
$mes=$_GET['mes'];

$sqlZ="SELECT e.*,d.glosa,d.haber,d.debe,d.cod_cuentaauxiliar,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra FROM estados_cuenta e,comprobantes_detalle d where e.cod_comprobantedetalle=d.codigo and (d.cod_cuenta=$codCuenta or e.cod_plancuenta=$codCuenta or e.cod_cuentaaux=$codCuenta) and e.cod_comprobantedetalleorigen=0 order by e.fecha";
if(isset($_GET['cod_auxi'])){
  $codAuxi=$_GET['cod_auxi'];
  if($codAuxi!="all"){
    $codAuxiPar=explode("###", $codAuxi);
    $cuentaAuxi=$codAuxiPar[0];
   $sqlZ="SELECT e.*,d.glosa,d.haber,d.debe,d.cod_cuentaauxiliar,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra FROM estados_cuenta e,comprobantes_detalle d where e.cod_comprobantedetalle=d.codigo and (d.cod_cuenta=$codCuenta or e.cod_plancuenta=$codCuenta or e.cod_cuentaaux=$codCuenta) and e.cod_comprobantedetalleorigen=0 and e.cod_cuentaaux=$cuentaAuxi order by e.fecha";
  }
}

?>
<table class="table table-bordered table-condensed table-warning">
	<thead>
	  <tr class="">
	  	<th class="text-left"></th>
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
	  </tr>
	</thead>
	<tbody id="tabla_estadocuenta">
<?php
  
  
  $stmt = $dbh->prepare($sqlZ);
  $stmt->execute();
  $i=0;$saldo=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	 $codigoX=$row['codigo'];
   //$tipo_comprobanteX=$row['tipo_comprobante'];
   //$numero_comprobanteX=$row['numero_comprobante'];

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
    $sqlContra="SELECT sum(monto)as monto from estados_cuenta e where e.cod_comprobantedetalleorigen='$codCompDetX'";
    $stmtContra = $dbh->prepare($sqlContra);
    $stmtContra->execute();
    $montoContra=0;
    while ($rowContra = $stmtContra->fetch(PDO::FETCH_ASSOC)) {
      $montoContra=$rowContra['monto'];
    }
 
 if(($row['cod_cuentaaux']!=""||$row['cod_cuentaaux']!=0)){
   if($tipoProveedorCliente==1){
      $proveedorX=obtenerProveedorCuentaAux($row['cod_cuentaaux']);
    }else{
    if(($row['cod_cuentaauxiliar']!=0)){
     $proveedorX=obtenerClienteCuentaAux($row['cod_cuentaauxiliar']);
    }else{
     $proveedorX="Sin Cliente";
    }     
   }
  }else{
    if($tipoProveedorCliente==1){
         $proveedorX="Sin Proveedor";
      }else{
       $proveedorX="Sin Cliente";
      }
  }

	 if($haberX > 0){
      $debeX=$montoContra;
      $saldo=$saldo+$haberX-$debeX;
       ?>
  	   <tr class="bg-white det-estados">
        <td>
        <input type="hidden" id="codigoCuentaAux<?=$i?>" value="<?=$codCuentaAuxX?>">
        <!-- style="display:none"-->
  	   	<?php 
          if(($tipo==1 && $tipoProveedorCliente==1)){ 
        ?>
            <div class="form-check">
               <label class="form-check-label">
                     <input type="radio" class="form-check-input" id="cuentas_origen_detalle<?=$i?>" name="cuentas_origen_detalle" value="<?=$codigoX?>####<?=$codCuentaAuxX?>####<?=$codProveedorX?>">
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>       
               </label>
             </div>
            <?php    
  	   } ?>
  	   </td>
          <td class="text-center small"><?=$nombreUnidadO;?></td>
          <td class="text-center small"><?=$nombreTipoComprobante;?></td>
          <td class="text-center small"><?=$numeroComprobante;?></td>
          <td class="text-left small"><?=$fechaComprobante;?></td>
          <td class="text-left small"><?=$fechaX;?></td>
       <td class="text-left small"><?=$proveedorX?></td>
       <td class="text-left small"><?=$glosaMostrar;?></td>
       <td class="text-right small"><?=formatNumberDec($montoContra)?></td>
       <td class="text-right small"><?=formatNumberDec($montoX)?></td>
       <td class="text-right small font-weight-bold"><?=formatNumberDec($saldo);?></td>
     </tr>
  	   <?php
	 }else{
        ?>
  	   <tr class="bg-white det-estados"><td>
  	   	<?php 
          if(($tipo==2 && $tipoProveedorCliente==2)){
           //$saldo=$saldo+$debeX-$haberX;
           $haberX=$montoContra;
           $saldo=$saldo+$debeX-$haberX; 
        ?>
            <div class="form-check">
               <label class="form-check-label">
                     <input type="radio" class="form-check-input" id="cuentas_origen_detalle<?=$i?>" name="cuentas_origen_detalle" value="<?=$codigoX?>####<?=$codCuentaAuxX?>####<?=$codProveedorX?>">
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>       
               </label>
             </div>
            <?php    
       } ?>
  	   </td> 
       <td class="text-center small"><?=$nombreUnidadO;?></td>
          <td class="text-center small"><?=$nombreTipoComprobante;?></td>
          <td class="text-center small"><?=$numeroComprobante;?></td>
          <td class="text-left small"><?=$fechaComprobante;?></td>
          <td class="text-left small"><?=$fechaX;?></td>
       <td class="text-left"><?=$proveedorX?></td>
       <td class="text-left"><?=$glosaMostrar;?></td>
       <td class="text-right"><?=formatNumberDec($montoX)?></td>
       <td class="text-right"></td>
       <td class="text-right font-weight-bold"><?=formatNumberDec($saldo);?></td>
     </tr>
  	   <?php
	 }
	 $i++;
  }
?>
	</tbody>
</table>
<?php
echo "@".$saldo;