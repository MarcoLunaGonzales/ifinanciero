<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();
// $mes_actual=date('F');
// $anio_actual=date('Y');
// $fecha_actual=date('Y-m-d');

$codigo_planilla = $_GET["codigo_planilla"];//
$cod_gestion = $_GET["cod_gestion"];//
$cod_mes = $_GET["cod_mes"];//

//nombre de mes
$stmtMes = $dbh->prepare("SELECT nombre from meses where codigo=$cod_mes");
$stmtMes->execute();     
$resultmes= $stmtMes->fetch();
$mes=$resultmes['nombre'];
//nombre de gestion
$stmtGestion = $dbh->prepare("SELECT nombre from gestiones where codigo=$cod_gestion");
$stmtGestion->execute();     
$resultGestion= $stmtGestion->fetch();
$gestion=$resultGestion['nombre'];
//cantidad bonos
$sql3="SELECT count(cod_bono) as cantidad_bonos from bonos_personal_mes where cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1";
$stmt3 = $dbh->prepare($sql3);
$stmt3->execute();     
$result3= $stmt3->fetch();
$cantidad_bonosX=trim($result3['cantidad_bonos']);

$sql4="SELECT count(cod_descuento) as cantidad_descuentos_otros from descuentos_personal_mes where cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1";
$stmt4 = $dbh->prepare($sql4);
$stmt4->execute();     
$result4= $stmt4->fetch();
$cantidad_descuentos_otros=trim($result4['cantidad_descuentos_otros']);

//echo "ver: ".$cuenta_filas_bonos;



//descuentos

$sqlDescuentos = "SELECT
(select count(b1.bono_antiguedad) from planillas_personal_mes b1 where b1.bono_antiguedad>0 and b1.cod_planilla=cod_planilla and b1.cod_estadoreferencial=1) as bono_antiguedad,
(select count(a1.afp_1) from planillas_personal_mes a1 where a1.afp_1>0 and a1.cod_planilla=cod_planilla and cod_estadoreferencial=1) as afp_1,
(select count(a2.afp_2) from planillas_personal_mes a2 where a2.afp_2>0 and a2.cod_planilla=cod_planilla and cod_estadoreferencial=1) as afp_2,
(SELECT count(ap13.a_solidario_13000) from planillas_personal_mes_patronal ap13 where ap13.a_solidario_13000>0 and ap13.cod_planilla=cod_planilla ) as as13,
(SELECT count(ap25.a_solidario_25000) from planillas_personal_mes_patronal ap25 where ap25.a_solidario_25000>0 and ap25.cod_planilla=cod_planilla) as as25,
(SELECT count(ap35.a_solidario_35000) from planillas_personal_mes_patronal ap35 where ap35.a_solidario_35000>0 and ap35.cod_planilla=cod_planilla) as as35,
(SELECT count(rc.rc_iva) from planillas_personal_mes_patronal rc where rc.rc_iva>0 and rc.cod_planilla=cod_planilla) as rc_iva,
(SELECT count(at.atrasos) from planillas_personal_mes_patronal at where at.atrasos>0 and at.cod_planilla=cod_planilla) as atrasos,
(SELECT count(ant.anticipo) from planillas_personal_mes_patronal ant where ant.anticipo>0 and ant.cod_planilla=cod_planilla) as anticipos,
(SELECT count(dot.dotaciones) from planillas_personal_mes dot where dot.dotaciones>0 and dot.cod_planilla=cod_planilla and cod_estadoreferencial=1) as dotaciones
from planillas								
where  cod_gestion=$cod_gestion and cod_mes=$cod_mes";
$stmtDescuentos = $dbh->prepare($sqlDescuentos);
$stmtDescuentos->execute();
$resultDescuentos = $stmtDescuentos->fetch();

$bono_antiguedad=$resultDescuentos['bono_antiguedad'];

$afp_1=$resultDescuentos['afp_1'];
$afp_2=$resultDescuentos['afp_2'];
$as13=$resultDescuentos['as13'];
$as25=$resultDescuentos['as25'];
$as35=$resultDescuentos['as35'];
$rc_iva=$resultDescuentos['rc_iva'];
$atrasos=$resultDescuentos['atrasos'];
$anticipos=$resultDescuentos['anticipos'];
$dotaciones=$resultDescuentos['dotaciones'];
$total_bonos=$cantidad_bonosX+$bono_antiguedad;
$total_aportes = $afp_1+$afp_2+$as13+$as25+$as35+$rc_iva;
$total_descuentos=$atrasos+$anticipos+$dotaciones+$cantidad_descuentos_otros;
?>


<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
			<div class="card-header <?=$colorCard;?> card-header-icon">            
				<h4 class="card-title"> 
					<img  class="card-img-top"  src="../marca.png" style="widtd:100%; max-width:250px;">
				    Prerequisitos para Planilla De Sueldos
				</h4>                  
				<h6 class="card-title">
				  Gestion: <?=$gestion; ?><br>
				  Mes: <?=$mes; ?><br>
				  Codigo Planilla: <?=$codigo_planilla; ?><br>                     
				</h6>             
			</div>
			<div class="card-body">
				<div class="table-responsive">                  
				  <table border="2"  align="center" style="width: 80%;border-collapse: collapse;" class="table table-bordered table-condensed table-hover" id="tablePaginator">
				  <!-- <table border="1" align="center" style="width: 80%;border-collapse: collapse;"> -->
				  	<tbody>
				  		<tr>
				  			<th class="bg-dark text-white text-center ">BONOS :</th>
				  			<th class="td-actions text-left" ><?=$total_bonos?> Registros Este Mes 
				  				<button type="button" class="btn"  style="background-color:#3b83bd;color:#ffffff;" id="botonBonos"> <i class="material-icons" title="Más Detalle">add</i>
				  				</button>
				  			
				  			</th>
				  		</tr>
				  		<tr>
				  			<td class="id_bonos" style="display:none"></td>
							<td class="id_bonos text-left" style="display:none"><b>BONO ANTIGUEDAD : </b> <?=$bono_antiguedad?> Registros este mes</td>
						</tr>			  						          				        			  		
				  			<?php 
					  			$sqlBonos = "SELECT cod_bono,count(*) as cantidad_items,(select b.nombre from bonos b where b.codigo=cod_bono) as nombre_bono
									from bonos_personal_mes 
									where  cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1  GROUP BY (cod_bono)";
								$stmtBonos = $dbh->prepare($sqlBonos);
								$stmtBonos->execute();                      
								$stmtBonos->bindColumn('cod_bono',$cod_bono);
								$stmtBonos->bindColumn('cantidad_items',$cantidad_items);
								$stmtBonos->bindColumn('nombre_bono',$nombre_bono);
				  			while ($row = $stmtBonos->fetch()){ ?>
				  			<tr>
				  			<td class="id_bonos" style="display:none"></td>
							<td class="id_bonos text-left" style="display:none"><b><?=$nombre_bono;?> : </b> <?=$cantidad_items?> Registros este mes</td>
							</tr>
							<?php }?>					  
				  		

						<tr>
				  			<th class="bg-dark text-white text-descuentos">APORTES :</th>
				  			<th clasS="td-actions text-left"><?=$total_aportes?> Registros Este Mes 				  				
				  				<button type="button" id="botonAportes" class="btn"  style="background-color:#3b83bd;color:#ffffff;" > <i class="material-icons" title="Más Detalle">add</i>
				  				</button>
				  			</th>
				  		</tr>
				  		<tr>
				  			<td class="id_aportes success" style="display:none"></td>
							<td class="id_aportes success text-left" style="display:none"><b>AFP_FUTURO : </b> <?=$afp_1?> Registros este mes</td>
				  		</tr>
				  		<tr>
				  			<td class="id_aportes success" style="display:none"></td>
							<td class="id_aportes success text-left" style="display:none"><b>AFP_PREVISION : </b> <?=$afp_2?> Registros este mes</td>
				  		</tr>
				  		<tr>
				  			<td class="id_aportes success" style="display:none"></td>
							<td class="id_aportes success text-left" style="display:none"><b>APOR. SOLID. 13 : </b> <?=$as13?> Registros este mes</td>
				  		</tr>
				  		<tr>
				  			<td class="id_aportes success" style="display:none"></td>
							<td class="id_aportes success text-left" style="display:none"><b>APOR. SOLID. 25 : </b> <?=$as25?> Registros este mes</td>
				  		</tr>
				  		<tr>
				  			<td class="id_aportes success" style="display:none"></td>
							<td class="id_aportes success text-left" style="display:none"><b>APOR. SOLID. 35 : </b> <?=$as35?> Registros este mes</td>
				  		</tr>			  				


				  		<tr>
				  			<th class="bg-dark text-white text-descuentos">DESCUENTOS :</th>
				  			<th clasS="td-actions text-left"><?=$total_descuentos?> Registros Este Mes 				  				
				  				<button type="button" id="botonDescuentos" class="btn"  style="background-color:#3b83bd;color:#ffffff;" > <i class="material-icons" title="Más Detalle">add</i>
				  				</button>
				  			</th>
				  		</tr>				  				
			  			
				  		<tr>
				  			<td class="id_descuentos" style="display:none"></td>
							<td class="id_descuentos text-left" style="display:none"><b>ATRASOS : </b> <?=$atrasos?> Registros este mes</td>
				  		</tr>
				  		<tr>
				  			<td class="id_descuentos" style="display:none"></td>
							<td class="id_descuentos text-left" style="display:none"><b>ANTICIPOS : </b> <?=$anticipos?> Registros este mes</td>
				  		</tr>				  		
				  		<tr>
				  			<td class="id_descuentos" style="display:none"></td>
							<td class="id_descuentos text-left" style="display:none"><b>DOTACIONES : </b> <?=$dotaciones?> Registros este mes</td>
				  		</tr>


				  		<!--OTROS DESCUeNTOS-->
				  		</tr>			  						          				        			  		
				  			<?php				  				
					  			$sqlBonos = "SELECT cod_descuento,count(*) as cantidad_descuentos,(select b.nombre from descuentos b where b.codigo=cod_descuento) as nombre_descuento
									from descuentos_personal_mes 
									where  cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1  GROUP BY (cod_descuento)";
								$stmtBonos = $dbh->prepare($sqlBonos);
								$stmtBonos->execute();                      
								$stmtBonos->bindColumn('cod_descuento',$cod_descuento_otros);
								$stmtBonos->bindColumn('cantidad_descuentos',$cantidad_descuentos_otros);
								$stmtBonos->bindColumn('nombre_descuento',$nombre_descuento_otros);
				  			while ($row = $stmtBonos->fetch()){ ?>
					  			<tr>
						  			<td class="id_descuentos" style="display:none"></td>
									<td class="id_descuentos text-left" style="display:none"><?=$nombre_descuento_otros;?> : <?=$cantidad_descuentos_otros?> Registros este mes</td>
								</tr>

							<?php }?>					  

				  	</tbody>
				    
				  </table>                                
				</div>                 
			</div>
        </div>
      </div>
    </div>  
  </div>
</div>

<script type="text/javascript">
  
  $("#botonBonos").on("click", function(){
  $(".id_bonos").toggle();
  });
  $("#botonDescuentos").on("click", function(){
  $(".id_descuentos").toggle();
	});

  $("#botonAportes").on("click", function(){
  $(".id_aportes").toggle();
	});
</script>



