<?php
	require_once __DIR__.'/../conexion.php';
	require_once __DIR__.'/../functionsGeneral.php';
	require_once '../layouts/bodylogin2.php';
	$dbh = new Conexion();

	$cod_planilla = $_GET["codigo_planilla"];//
	$cod_gestion = $_GET["cod_gestion"];//
	$cod_uo = $_GET["codigo_uo"];//
	

	$sqlGestion="SELECT nombre from gestiones where codigo=$cod_gestion";
	$stmtGestion=$dbh->prepare($sqlGestion);
	$stmtGestion->execute();
	$resultGestion=$stmtGestion->fetch();
	$nombre_gestion=$resultGestion['nombre'];
	
	$stmtUO=$dbh->prepare("SELECT nombre from unidades_organizacionales where codigo=$cod_uo");
	$stmtUO->execute();
	$resultUO=$stmtUO->fetch();
	$nombre_uo=$resultUO['nombre'];


	$stmtArea = $dbh->prepare("SELECT cod_area,(SELECT a.abreviatura from areas a where a.codigo=cod_area) as nombre_area
	 from personal_area_distribucion
  where cod_estadoreferencial=1 and cod_uo=$cod_uo
  GROUP BY cod_area order by nombre_area");
  $stmtArea->execute();
  $stmtArea->bindColumn('cod_area', $cod_area_x);
  $stmtArea->bindColumn('nombre_area', $nombre_area_x);
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">            
            <h4 class="card-title"> 
              <img  class="card-img-top"  src="../marca.png" style="widtd:100%; max-width:250px;">
                Planilla De Aguinaldo
            </h4>                  
            <h6 class="card-title"><small>
              Codigo Planilla: <?=$cod_planilla;?><br>
              Gestion: <?=$nombre_gestion;?><br>              
              Oficina: <?=$nombre_uo;?>
              </small>                    
            </h6>             
          </div>
          <div class="card-body">
            <div class="table-responsive">                  
				<table class="table table-bordered table-condensed table-hover" id="tablePaginator">
                	<thead>
		                <tr class="bg-dark text-white">                  
		                    <th><small>#</small></th> 
		                    <th><small>Area</small></th>                   
		                    <th><small>Paterno</small></th>
		                    <th><small>Materno</small></th>
		                    <th><small>Nombres</small></th>                    
		                    <th><small>Doc. Id</small></th>                    
		                    <th><small>Cargo</small></th>
		                    <th><small>Porcentaje</small></th>
		                    <th><small>Fecha Ingreso</small></th>		                    
		                    <th class="bg-success text-white"><button id="botonAportes" style="border:none;" class="bg-success text-white small">Promedio Sueldos</button></th>
		                    <td class="aportesDet bg-success text-white" style="display:none"><small>Mes Sep,</small></td>
		                    <td class="aportesDet bg-success text-white" style="display:none"><small>Mes Oct.</small></td>
		                    <td class="aportesDet bg-success text-white" style="display:none"><small>MEs Nov,</small></td>
		                    

		                    
		                    <th><small>Meses Trabajados</small></th>
		                    <th><small>Dias Trabajados</small></th>
		                    <th class="bg-primary text-white"><small>Total Aguinaldo</small></th>                    
		                </tr>                                  
	                </thead>
	                <tbody>
						<?php 
						$index=1;
						$sum_total_sueldo1=0;
	                    $sum_total_sueldo2=0;
	                    $sum_total_sueldo3=0;
	                    $sum_total_promedio_tp=0;
	                    $sum_total_aguinaldo_tp=0;

						while ($row = $stmtArea->fetch(PDO::FETCH_BOUND)) 
						{
							$sql = "SELECT ppm.cod_personal,ppm.sueldo_1,ppm.sueldo_2,ppm.sueldo_3,ppm.meses_trabajados,ppm.dias_trabajados,pad.porcentaje,
									ppm.total_aguinaldo,
						        (select p.primer_nombre from personal p where p.codigo=ppm.cod_personal) as personal,
						        (select pa.paterno from personal pa where pa.codigo=ppm.cod_personal) as paterno,
						        (select pa.materno from personal pa where pa.codigo=ppm.cod_personal) as materno,
						        (select p3.identificacion from personal p3 where p3.codigo=ppm.cod_personal) as doc_id,
						        (select (select pd.abreviatura from personal_departamentos pd where pd.codigo=p3.cod_lugar_emision)
						             from personal p3 where p3.codigo=ppm.cod_personal) as lug_emision,
						  		(select p4.lugar_emision_otro from personal p4 where p4.codigo=ppm.cod_personal) as lug_emision_otro
								from planillas_aguinaldos_detalle ppm,personal_area_distribucion pad
								where ppm.cod_personal=pad.cod_personal and cod_planilla=1 and pad.cod_uo=$cod_uo and pad.cod_area=$cod_area_x order by paterno";

							$stmtPersonal = $dbh->prepare($sql);
							$stmtPersonal->execute();	

							$stmtPersonal->bindColumn('cod_personal', $cod_personalcargo);
							$stmtPersonal->bindColumn('sueldo_1', $sueldo_1);
							$stmtPersonal->bindColumn('sueldo_2', $sueldo_2);
							$stmtPersonal->bindColumn('sueldo_3', $sueldo_3);
							$stmtPersonal->bindColumn('meses_trabajados', $meses_trabajados);
							$stmtPersonal->bindColumn('dias_trabajados', $dias_trabajados);
							$stmtPersonal->bindColumn('total_aguinaldo', $total_aguinaldo);
							$stmtPersonal->bindColumn('porcentaje', $porcentaje);

							$stmtPersonal->bindColumn('personal', $personal);
							$stmtPersonal->bindColumn('paterno', $paterno);
							$stmtPersonal->bindColumn('materno', $materno);
							$stmtPersonal->bindColumn('doc_id', $doc_id);
							$stmtPersonal->bindColumn('lug_emision', $lug_emision);
							$stmtPersonal->bindColumn('lug_emision_otro', $lug_emision_otro);
							
							while ($row = $stmtPersonal->fetch()) 
							{  
		                          //dividiendo montos a su porcentaje respectivo
		                          $sueldo_1_tp=$sueldo_1*$porcentaje/100;
		                          $sueldo_2_tp=$sueldo_2*$porcentaje/100;
		                          $sueldo_3_tp=$sueldo_3*$porcentaje/100;
		                          $promedio_sueldos=$sueldo_1_tp+$sueldo_2_tp+$sueldo_3_tp;
		                          $total_aguinaldo_tp=$total_aguinaldo*$porcentaje/100;

		                          $sum_total_sueldo1+=$sueldo_1_tp;
		                          $sum_total_sueldo2+=$sueldo_2_tp;
		                          $sum_total_sueldo3+=$sueldo_3_tp;
		                          $sum_total_promedio_tp+=$promedio_sueldos;

		                          $sum_total_aguinaldo_tp+=$total_aguinaldo_tp;

		                        ?>
			                	<tr>                                                        
				                    <td class="text-center small"><?=$index;?></td>
				                    <td class="text-left small"><?=$nombre_area_x;?></td>                    
				                    <td class="text-left small"><?=$paterno;?></td>
				                    <td class="text-left small"><?=$materno;?></td>
				                    <td class="text-left small"><?=$personal;?></td>
				                    <td class="text-center small"><?=$doc_id;?>-<?=$lug_emision?><?=$lug_emision_otro?></td>
				                    <td class="text-left small">cargo</td>
				                    <?php if($porcentaje!=100){ ?>
				                    <td class="text-center small"><span class="badge badge-danger"><?=$porcentaje;?></span></td>
				                    <?php }else{?>
				                    <td class="text-center small"><?=$porcentaje;?></td>
				                    <?php }
				                    ?>

				                    <td class="text-center small">fecha ingreso</td>                    
				                    
				                    <td class="small"><?=formatNumberDec($promedio_sueldos);?></td> 
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($sueldo_1_tp);?></td>
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($sueldo_2_tp);?></td>
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($sueldo_3_tp);?></td>
				                    <td  class="text-center small">meses trabajados</td>
				                    <td  class="text-center small">dias trabajados</td>
				                    
				                    <td class="bg-primary text-white small"><?=formatNumberDec($total_aguinaldo_tp);?></td>
				                </tr> 
			                  	<?php 
			                    $index+=1;
		                	}

						}

	                 
						?>                      
	                </tbody>
	                <tfoot>
	                    <tr class="bg-dark text-white">                  
	                    <th colspan="9" class="text-center small">Total</th>

	                    <th class="bg-success text-white small"><?=formatNumberDec($sum_total_promedio_tp);?></th>
	                    <th class="aportesDet bg-success text-white small" style="display:none"><?=formatNumberDec($sum_total_sueldo1);?></th>
	                    <th class="aportesDet bg-success text-white small" style="display:none"><?=formatNumberDec($sum_total_sueldo2);?></th>
	                    <th class="aportesDet bg-success text-white small" style="display:none"><?=formatNumberDec($sum_total_sueldo3);?></th>
	                   
	                    <th class="text-center small">-</th>
	                    <th class="text-center small">-</th>
	                    
	                                                          
	                    <th class="bg-primary text-white small"><?=formatNumberDec($sum_total_aguinaldo_tp);?></th>
	                  </tr>
	                </tfoot>               
              	</table>                                
            </div>                 
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>

<script type="text/javascript">
  
  $("#botonAportes").on("click", function(){
    $(".aportesDet").toggle();
  });
</script>