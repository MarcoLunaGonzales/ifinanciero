<?php

require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$anioActual=nameGestion($globalGestion);
$fechaActual=date("d/m/Y");
$codCuenta=$_GET['cod_cuenta'];
$tipo=$_GET['tipo'];
$mes=$_GET['mes'];

//desde aqui datos para el buscador
$datos=$_GET['datos'];//llegan los datos para el filtro
$array_datos=explode("#####@@@@@", $datos);
$anio_busqueda=$array_datos[0];
$fecha_busqueda=$array_datos[1];
$glosa_buscar=$array_datos[2];

if(isset($_GET['monto_cajachica'])){
  $monto_cajachica=$_GET['monto_cajachica'];
  $codigo_cajachica=$_GET['codigo_cajachica'];
}else{
  $monto_cajachica=0;
  $codigo_cajachica=0;
}



?>
<table class="table table-bordered table-condensed table-warning">
	<thead>
	  <tr class="">
	  	<th class="text-left"></th>
      <th class="text-left">Of</th>
      <th class="text-left">Tipo</th>
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
    $sql="SELECT e.*,e.codigo as codigo_ec,d.glosa,d.haber,d.debe,d.cod_cuentaauxiliar,(select concat(c.cod_tipocomprobante,'|',c.numero,'|',cd.cod_unidadorganizacional,'|',MONTH(c.fecha),'|',c.fecha) from comprobantes_detalle cd, comprobantes c where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle)as extra, d.cod_comprobante FROM estados_cuenta e,comprobantes_detalle d, comprobantes cc where cc.codigo=d.cod_comprobante and e.cod_comprobantedetalle=d.codigo and (d.cod_cuenta=$codCuenta) and e.cod_comprobantedetalleorigen=0  and year(cc.fecha)=$anioActual "; //year(e.fecha)=$anioActual

    if($anio_busqueda!="" && $anio_busqueda!=0 ){
      $sql.=" and YEAR(cc.fecha) = $anio_busqueda"; 
    }

    if($fecha_busqueda!="" ){
      $sql.=" and e.fecha like '%$fecha_busqueda%'"; 
    }

    if($glosa_buscar!="" ){
      $sql.=" and d.glosa like '%$glosa_buscar%'"; 
    }

    // if($monto_buscar!="" ){
    //   $sql.=" and f.nit=$nit_f"; 
    // }

    $sql.=" order by e.fecha";

    //echo $sql;

    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $i=0;$saldo=0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  	 $codigoX=$row['codigo_ec'];
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
     $cod_comprobante_x=$row['cod_comprobante'];
     list($tipoComprobante, $numeroComprobante, $codUnidadOrganizacional, $mesComprobante, $fechaComprobante)=explode("|", $codigoExtra);
     $nombreUnidadO=abrevUnidad_solo($codUnidadOrganizacional);
     // $nombreTipoComprobante=abrevTipoComprobante($tipoComprobante)."-".$mesComprobante;

     $nombreTipoComprobante=nombreComprobante($cod_comprobante_x);

     $credito_padre=ObtenerMontoTotalEstadoCuentas_hijos($codCuenta,$codigoX);
     $saldo=$montoX-$credito_padre;
     $saldo=round($saldo,2);
     

     $fechaComprobante_x=strtotime($fechaComprobante);
     $fechaComprobante_comparar=strtotime("2020-08-01 00:00:00");
     $fechaComprobante=strftime('%d/%m/%Y',strtotime($fechaComprobante));

     if(obtenerProveedorCuentaAux($row['cod_cuentaaux'])==""){
      $proveedorX="Sin Proveedor";
     }else{
      $proveedorX=obtenerProveedorCuentaAux($row['cod_cuentaaux']);
     }
     //buscamos al personal correspondiente
     $sqlDetalleX="SELECT s.codigo AS cod_solicitudrecurso from solicitud_recursos s where s.cod_comprobante in ('$cod_comprobante_x')";
     //echo $sqlDetalleX;
     // $sqlDetalleX="SELECT cod_solicitudrecurso from solicitud_recursosdetalle where cod_estadocuenta=$codigoX limit 1";        
      $stmtDetalleX = $dbh->prepare($sqlDetalleX);
      $stmtDetalleX->execute();
      $cod_solicitudrecurso_sr=0;
      while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_ASSOC)) {
        $cod_solicitudrecurso_sr=$rowDetalleX['cod_solicitudrecurso'];
      }                    
      //$resultado=$stmtDetalleX->fetch();      
      
      $sw_personal=0;
      // echo $codigoX."..";
      // $fechaComoEntero = strtotime($fechaComprobante);
      // $gestionComprobante = date("Y", $fechaComoEntero);

      $mesComprobante=date('m',strtotime($fechaComprobante));
      $gestionComprobante=date('Y',strtotime($fechaComprobante));

      if($mesComprobante>=8 && $gestionComprobante>=2020){
      // if($fechaComprobante_x>=$fechaComprobante_comparar){

        if($cod_solicitudrecurso_sr!=0 && $cod_solicitudrecurso_sr!='' && $cod_solicitudrecurso_sr!=null){
          // echo $cod_solicitudrecurso_sr."..";
          $sqlDetalleY="SELECT count(*) as contador  from solicitud_recursosencargado where cod_solicitudrecurso=$cod_solicitudrecurso_sr and cod_personal =$globalUser ";
          $stmtDetalleY = $dbh->prepare($sqlDetalleY);
          $stmtDetalleY->execute();                    
          $resultado=$stmtDetalleY->fetch();      
          $contador_sr=$resultado['contador'];
          if($contador_sr>0 ){
            $sw_personal=1;
          }else{
            $sw_personal=0;
          }
        }else{
          $sw_personal=1;
        }  
      }else{
        $sw_personal=1;
      }
      


  	 if($haberX>0 && $saldo>0){?>
    	 <tr class="bg-white" onclick="verDetalleEstadosCuenta2('<?=$i?>')">
          <td>
          <input type="hidden" id="codigoCuentaAux<?=$i?>" value="<?=$codCuentaAuxX?>">
          <!-- style="display:none"-->
    	   	<?php if($tipo==2 && $sw_personal>0 && (($saldo+1)>=$monto_cajachica)){ 
              ?>
              <div class="form-check">
                 <label class="form-check-label">
                       <input type="radio" class="form-check-input" id="cuentas_origen_detalle<?=$i?>" name="cuentas_origen_detalle" value="<?=$codCompDetX?>####<?=$codCuentaAuxX?>####<?=$codProveedorX?>####<?=$saldo?>####<?=$proveedorX?>####<?=$codigoX?>">
                       
                      <span class="form-check-sign">
                        <span class="check"></span>
                      </span>       
                 </label>
               </div>
              <?php    
    	    } ?>
    	    </td>

          <!--td class="text-center small"><?=$sw_personal." ".$sqlDetalleX." ".$sqlDetalleY;?> <?=$nombreUnidadO;?></td-->
          <td class="text-center small"><?=$nombreUnidadO;?></td>
          <td class="text-center small"><?=$nombreTipoComprobante;?></td>
          <td class="text-left small"><?=$fechaComprobante;?></td>
          <td class="text-left small"><?=$fechaX;?></td>
          <td class="text-left"><?=$proveedorX?></td>
          <td class="text-left"><?=$glosaX?></td>
          <td class="text-right"><?=number_format($credito_padre, 2, '.', ',')?></td>
          <td class="text-right"><?=number_format($montoX, 2, '.', ',')?></td>
          
          <td class="text-right font-weight-bold"><?=number_format($saldo, 2, '.', ',');?></td>
        </tr>
        <?php 
          $stmtHijos = $dbh->prepare("SELECT e.* FROM estados_cuenta e where e.cod_plancuenta=$codCuenta and e.cod_comprobantedetalleorigen=$codigoX order by e.fecha");
            $stmtHijos->execute();
            $j=0;$saldo_hijo=$montoX;
            while ($row = $stmtHijos->fetch(PDO::FETCH_ASSOC)) {
              $codigoX_hijos=$row['codigo'];
              $codPlanCuentaX_hijos=$row['cod_plancuenta'];
              $codCompDetX_hijos=$row['cod_comprobantedetalle'];
              $codProveedorX_hijos=$row['cod_proveedor'];
              $fechaX_hijos=$row['fecha'];
              $fechaX_hijos=strftime('%d/%m/%Y',strtotime($fechaX_hijos));
              $montoX_hijos=$row['monto'];
              // $glosaX_hijos=$row['glosa'];
              // $debeX_hijos=$row['debe'];
              // $haberX_hijos=$row['haber'];
              $codCuentaAuxX_hijos=$row['cod_cuentaaux'];
              $saldo_hijo=$saldo_hijo-$montoX_hijos;
              // if(obtenerProveedorCuentaAux($row['cod_cuentaaux'])==""){
              //   $proveedorX_hijos="Sin Proveedor";
              //  }else{
              //   $proveedorX_hijos=obtenerProveedorCuentaAux($row['cod_cuentaaux']);
              //  }
              ?>
              <tr class="bg-white det-estados-<?=$i?>"  style="display:none">
                <td></td>
                <td class="text-left font-weight-bold"><small></small></td>
                <td class="text-left font-weight-bold"><small><?=$fechaX_hijos?></small></td>
                <td class="text-left"><small><?=$proveedorX?></small></td>
                <td class="text-left"></td>
                <td class="text-right"><small><?=number_format($montoX_hijos, 2, '.', ',')?></small></td>
                <td class="text-right"></td>
                <td class="text-right font-weight-bold"><small><?=number_format($saldo_hijo, 2, '.', ',');?></small></td>
              </tr>

           <?php
           $j++;
          } ?>

    	    <?php
  	 }else{ ?>
      	<!-- <tr class="bg-white det-estados">
          <td></td>
          <td class="text-left font-weight-bold"><?=$fechaX?></td>
          <td class="text-left"><?=$proveedorX?></td>
          <td class="text-left"><?=$glosaX?></td>
          <td class="text-right"></td>
          <td class="text-right"><?=number_format($montoX, 2, '.', ',')?></td>
          <td class="text-right font-weight-bold"><?=number_format($saldo, 2, '.', ',');?></td>
        </tr>    -->     
        <?php
          
  	  }
      $i++;
    }

    if($i==0){?>
      <tr class="" onclick="verDetalleEstadosCuenta()"><td>
        </td><td>
        </td><td class="text-left font-weight-bold">
        </td><td class="text-left font-weight-bold">Saldo Inicial</td>
        <td class="text-right"></td><td class="text-right"></td>
        <td class="text-right font-weight-bold"><?=number_format(0, 2, '.', ',');?></td>
      </tr>
    	<?php
    }else{
    	
    }
  ?>
	</tbody>
</table>
<?php
echo "@".$saldo;?>