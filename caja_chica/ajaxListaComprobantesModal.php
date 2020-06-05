<?php

require_once '../conexion.php';
require_once '../styles.php';
require_once '../layouts/bodylogin2.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
set_time_limit(300);
// session_start();
// $globalAdmin=$_SESSION["globalAdmin"];
// $globalUnidad=$_SESSION["globalUnidad"];
// $globalArea=$_SESSION["globalArea"];
$cod_uo=$_GET['cod_uo'];
$tipo=$_GET['tipo'];
$fechaI=$_GET['fechaI'];
$fechaF=$_GET['fechaF'];
$glosa=$_GET['glosa'];
$comprobante=$_GET['comprobante'];
$cuenta=$_GET['cuenta'];

$codigos_cuenta_cajaschica=obtenerValorConfiguracion(54);


// $unidadOrgString=implode(",", $cod_uo);
$sql="SELECT (select u.abreviatura from unidades_organizacionales u where u.codigo=c.cod_unidadorganizacional)unidad, c.cod_gestion, 
  (select m.nombre from monedas m where m.codigo=c.cod_moneda)moneda, 
  (select t.abreviatura from tipos_comprobante t where t.codigo=c.cod_tipocomprobante)tipo_comprobante, c.fecha, c.numero,c.codigo, c.glosa,ec.nombre,c.cod_estadocomprobante
  from comprobantes c, estados_comprobantes ec, comprobantes_detalle cd where c.cod_estadocomprobante=ec.codigo and cd.cod_comprobante=c.codigo and c.cod_estadocomprobante!=2 and cd.cod_cuenta in ($codigos_cuenta_cajaschica)";  

if($cod_uo!=""){
  $sql.=" and c.cod_unidadorganizacional in ($cod_uo)";
}
if($tipo!=""){
  $sql.=" and c.cod_tipocomprobante in ($tipo)";  
}
if($fechaI!="" && $fechaF!=""){
  $sql.=" and c.fecha BETWEEN '$fechaI' and '$fechaF'"; 
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
$sql.="  GROUP BY c.codigo order by c.fecha desc, c.numero desc;";

// echo $sql;

$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('unidad', $nombreUnidad);
$stmt->bindColumn('cod_gestion', $nombreGestion);
$stmt->bindColumn('moneda', $nombreMoneda);
$stmt->bindColumn('tipo_comprobante', $nombreTipoComprobante);
$stmt->bindColumn('fecha', $fechaComprobante);
$stmt->bindColumn('numero', $nroCorrelativo);

$stmt->bindColumn('glosa', $glosaComprobante);
$stmt->bindColumn('nombre', $estadoComprobante);
$stmt->bindColumn('cod_estadocomprobante', $estadoC);
$stmt->bindColumn('codigo', $codigo_cobt);
?>

<table id="tablePaginator" class="table table-condensed table-bordered">
  <thead>  
  <?php
    $index=1;
    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {    	
      $nombreComprobante=nombreComprobante($codigo_cobt);
      $mes=date('n',strtotime($fechaComprobante));
      $glosaComprobante = (substr($glosaComprobante, 0, 100)); //limite de string
      // $mes=date("j",$fechaComprobante);      
    ?>
    <tr class="btn-dark">
    	<th colspan="8"> Comprobante</th>
    </tr>
    <tr style="background-color:#E3CEF6;">
      <th class="text-center">#</th>                          
      <th class="text-center small"><small>Oficina</small></th>
      <th class="text-center small"><small>Tipo/Número</small></th>
      <th class="text-center small"><small>Fecha</small></th>      
      <th class="text-left small"colspan="4"><small>Glosa</small></th>           
      <!-- <th class="text-center small"><small>Estado</small></th> -->
    </tr>
	<tr>
		<td align="text-center small"><?=$index;?></td>                          
		<td class="text-center small"><?=$nombreUnidad;?></td>
		<td class="text-center small"><?=$nombreComprobante;?></td>
		<td class="text-center small"><?=strftime('%d/%m/%Y',strtotime($fechaComprobante));?></td>		
		<td class="text-left small" colspan="4"><?=$glosaComprobante;?></td>
		<!-- <td><small><?=$estadoComprobante;?></small></td>		 -->
	</tr>
	<tr class="btn-dark">
    	<th colspan="8">Detalle</th>
    </tr>
  	<tr style="background-color:#E3CEF6;">
      <td align="text-center small" width="4%">	#</td>                          
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
      	$sqlDetalle="SELECT * from comprobantes_detalle where cod_comprobante=$codigo_cobt and cod_cuenta in ($codigos_cuenta_cajaschica) order by orden";
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
				<td align="text-center small"></td>                          
				<td class="text-center small"><?=$nombre_uo?>/<?=$nombre_area?></td>
				<td class="text-right small"><?=$numero_cuenta;?></td>
				<td class="text-left small"><?=$nombre_cuenta;?></td>								
				<td class="text-left small"><?=$glosa_detalle;?></td>	
				<td class="text-right small"><?=$debe;?></td>
				<td class="text-right small"><?=$haber;?></td>				
				<td class="td-actions text-right">
          <?php 
          $verificar=verificar_codComprobante_cajaChica($codigo_cobt,$codigo_detalle);
          $nombreComprobante=nombreComprobante($codigo_cobt);
          if(!$verificar){?>
    				<button rel="tooltip" class="<?=$buttonEdit;?>" onclick="SeleccionarComprobante_cajachica_reembolso('<?=$codigo_cobt?>','<?=$codigo_detalle?>','<?=$glosa_detalle;?>','<?=$monto?>','<?=$nombreComprobante?>')">SELECCIONAR          
    				</button><?php 
          }else{ ?>
            <small><small><span class="badge badge-danger">Registrado</span></small></small>
          <?php } ?>
				</td>
			</tr>
		<?php }
      ?>
    
    <?php
      $index++;
    }    
    ?>
  </tbody>
</table>
