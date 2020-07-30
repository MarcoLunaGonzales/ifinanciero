<?php

require_once '../conexion.php';
require_once '../styles.php';
// require_once '../layouts/bodylogin2.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

// session_start();

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
set_time_limit(300);
session_start();
$globalGestion=$_SESSION['globalNombreGestion'];
$mesGestion=$_SESSION['globalMes'];
$globalUnidad=$_SESSION["globalUnidad"];
// $globalArea=$_SESSION["globalArea"];
$cod_uo=$globalUnidad;
$tipo="3,2";
$fechaI='';
$fechaF='';
$glosa='';
$comprobante='';
// $cuenta=16;
// $cuenta=obtenerValorConfiguracionCajachicaCuenta($globalUnidad);

$cod_tcc=$_GET['cod_tcc'];
$cuenta=obtenerCodigoCuentaCajaChica($cod_tcc);
// $codigos_cuenta_cajaschica=obtenerValorConfiguracion(54);
// $codigos_cuenta_cajaschica=obtenerValorConfiguracion(54);


// $unidadOrgString=implode(",", $cod_uo);

?>

<table id="tablePaginator" class="table table-condensed table-bordered">
  <thead>  
  <?php
    // $mes=date("j",$fechaComprobante);      
    ?>
<!--     <tr class="btn-dark">
    	<th colspan="8"> Comprobante</th>
    </tr>
    <tr style="background-color:#E3CEF6;">
      <th class="text-center">#</th>                          
      <th class="text-center small"><small>Oficina</small></th>
      <th class="text-center small"><small>Tipo/Número</small></th>
      <th class="text-center small"><small>Fecha</small></th>      
      <th class="text-left small"colspan="4"><small>Glosa</small></th>           
      <th class="text-center small"><small>Estado</small></th>
    </tr>
	<tr>
		<td align="text-center small"><?=$index;?></td>                          
		<td class="text-center small"><?=$nombreUnidad;?></td>
		<td class="text-center small"><?=$nombreComprobante;?></td>
		<td class="text-center small"><?=strftime('%d/%m/%Y',strtotime($fechaComprobante));?></td>		
		<td class="text-left small" colspan="4"><?=$glosaComprobante;?></td>
		
	</tr> -->
	<tr class="btn-dark">
    	<th colspan="9">Detalle</th>
    </tr> 
  	<tr style="background-color:#E3CEF6;">
      <td align="text-center small" width="4%">	#</td>   
      <td align="text-center small" width="4%">Fecha</td>   
      <td class="text-center small" width="5%">Centro<br>Costos</td>
      <td class="text-center small" width="5%">Cuenta</td>
      <td class="text-center small" width="20%">Nombre cuenta</td>
      <td class="text-center small" >Glosa</td>
      <td class="text-center small" width="7%" >Debe</td>
      <td class="text-center small" width="7%" >Haber</td>
      <td colspan="2" width="5%" ></td>
    </tr>
    </thead>
  <tbody>        
      <?php
      $index=1;
      $sql="SELECT c.cod_gestion, c.fecha, c.numero,c.codigo, c.glosa,ec.nombre,c.cod_estadocomprobante
      from comprobantes c, estados_comprobantes ec, comprobantes_detalle cd where c.cod_estadocomprobante=ec.codigo and cd.cod_comprobante=c.codigo and c.cod_estadocomprobante!=2 and cod_gestion=$globalGestion";  
      // $sql.=" and cd.cod_cuenta in ($codigos_cuenta_cajaschica)";
      if($cod_uo!=""){
        // $sql.=" and c.cod_unidadorganizacional in ($cod_uo)";
      }
      if($tipo!=""){
        $sql.=" and c.cod_tipocomprobante in ($tipo)";  
      }
      if($cuenta!=""){
        $sql.=" and cd.cod_cuenta=$cuenta";
      }
      $sql.="  GROUP BY c.codigo order by c.fecha desc, c.numero desc;";
      // echo $sql;
      $stmt = $dbh->prepare($sql);
      $stmt->execute();      
      $stmt->bindColumn('cod_gestion', $nombreGestion);            
      $stmt->bindColumn('fecha', $fechaComprobante);
      $stmt->bindColumn('numero', $nroCorrelativo);
      $stmt->bindColumn('glosa', $glosaComprobante);
      $stmt->bindColumn('nombre', $estadoComprobante);
      $stmt->bindColumn('cod_estadocomprobante', $estadoC);
      $stmt->bindColumn('codigo', $codigo_cobt);
      while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {     
        $nombreComprobante=nombreComprobante($codigo_cobt);
        $mes=date('n',strtotime($fechaComprobante));
        $glosaComprobante = (substr($glosaComprobante, 0, 100)); //limite de string
        $sqlDetalle="SELECT * from comprobantes_detalle where cod_comprobante=$codigo_cobt and cod_cuenta in ($cuenta) and debe<>0 and haber=0 order by orden";
            // echo $sqlDetalle;
         	$stmtDetalle = $dbh->prepare($sqlDetalle);
    		$stmtDetalle->execute();
    		$stmtDetalle->bindColumn('codigo', $codigo_detalle);
    		$stmtDetalle->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
    		$stmtDetalle->bindColumn('cod_area', $cod_area);
    		$stmtDetalle->bindColumn('debe', $debe);
    		$stmtDetalle->bindColumn('haber', $haber);
    		$stmtDetalle->bindColumn('glosa', $glosa_detalle);
    		$stmtDetalle->bindColumn('cod_cuenta', $cod_cuenta);
    		while ($row = $stmtDetalle->fetch(PDO::FETCH_BOUND)) {
          $glosa_detalle = preg_replace("[\n|\r|\n\r]", ", ", $glosa_detalle);
          $glosa_detalle=trim($glosa_detalle);
    			$nombre_uo=abrevUnidad($cod_unidadorganizacional);
    			$nombre_area=abrevArea($cod_area);
    			$nombre_cuenta=nameCuenta($cod_cuenta);
    			$numero_cuenta=obtieneNumeroCuenta($cod_cuenta);
    			if($debe!=null || $debe!=0 || $debe!='' || $debe!=' '){
    				$monto=$debe;
    			}else{
    				$monto=$haber;
    			}
    			?>
    			<tr>
    				<td align="text-center small"><?=$index?></td>
            <td class="text-center small"><?=strftime('%d/%m/%Y',strtotime($fechaComprobante));?></td>
    				<td class="text-center small"><?=$nombre_uo?>/<?=$nombre_area?></td>
    				<td class="text-right small"><?=$numero_cuenta;?></td>
    				<td class="text-left small"><?=$nombre_cuenta;?></td>								
    				<td class="text-left small"><?=$glosa_detalle;?></td>	
    				<td class="text-right small"><?=number_format($debe, 2, '.', ',');?></td>
    				<td class="text-right small"><?=$haber;?></td>				
    				<td class="td-actions text-right">
              <?php               
              $verificar=verificar_codComprobante_cajaChica($codigo_cobt,$codigo_detalle);              
              // echo $verificar;
              $nombreComprobante=nombreComprobante($codigo_cobt);
              if($verificar==0){?>
        				<button rel="tooltip" class="<?=$buttonEdit;?>" onclick="SeleccionarComprobante_cajachica_reembolso('<?=$codigo_cobt?>','<?=$codigo_detalle?>','<?=$glosa_detalle;?>','<?=$monto?>','<?=$nombreComprobante?>','<?=$fechaComprobante?>')">SELECCIONAR          
        				</button><?php 
              }else{ ?>
                <small><small><span class="badge badge-danger">Registrado</span></small></small>
              <?php } ?>
    				</td>
    			</tr>
    		<?php
        $index++; }      
      }    
    ?>
  </tbody>
</table>
