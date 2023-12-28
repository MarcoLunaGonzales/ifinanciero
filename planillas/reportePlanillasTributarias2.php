<?php
	require_once __DIR__.'/../conexion.php';
	require_once __DIR__.'/../functionsGeneral.php';
	require_once '../functions.php';
	require_once '../layouts/bodylogin2.php';
	$dbh = new Conexion();
	set_time_limit(300);

	$total_1=0;$total_2=0;$total_3=0;$total_4=0;$total_5=0;$total_6=0;$total_7=0;$total_8=0;$total_9=0;
	$total_10=0;$total_11=0;$total_12=0;$total_13=0;$total_14=0;$total_15=0;
	$total_monto_retroactivo=0;

	$codPlanilla=$_GET['codigo_trib'];
	$cod_gestion = $_GET["cod_gestion"];//
	$cod_mes = $_GET["cod_mes"];//
	// $nombre_mes=strtoupper(nameMes($_GET['cod_mes']));


	 //modificacion Para el retroactivo de gestion, solo apliuca a mes de mayo
	  // $sqlPlanillaS="SELECT cod_mes from planillas where codigo=$codPlanilla";
	  // $stmtPlanillaS=$dbh->prepare($sqlPlanillaS);
	  // $stmtPlanillaS->execute();
	  // $resultPlanillaS=$stmtPlanillaS->fetch();
	  // $cod_mes=$resultPlanillaS['cod_mes'];
	  // echo $cod_mes."**";

	$nombre_gestion=nameGestion($cod_gestion);	
	$sql = "SELECT pt.*,p.primer_nombre,p.paterno,p.materno,
		(Select i.abreviatura from tipos_identificacion_personal i where i.codigo=p.cod_tipo_identificacion) as tipo_identificacion,p.tipo_identificacion_otro,
		p.identificacion, p.codigo_dependiente
		from planillas_tributarias_personal_mes_2 pt
		join personal p on p.codigo=pt.cod_personal
		where pt.cod_planillatributaria=$codPlanilla";

	$stmtPersonal = $dbh->prepare($sql);
	$stmtPersonal->execute();		
?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">            
            <h4 class="card-title"> 
              <img  class="card-img-top"  src="../marca.png" style="widtd:100%; max-width:250px;">
                <b>PLANILLA TRIBUTARIA V2</b>
            </h4>                  
            <h6 class="card-title"><small>
              <b>NIT:</b><?=obtenerValorConfiguracionEmpresa(4)?><br>
              Mes de declaración: <?=$cod_mes;?><br>
              Año de declaración: <?=$nombre_gestion; ?><br>
              SMN Actual: <?=obtenerValorConfiguracionEmpresa(5)?><br>
              </small>                    
            </h6>             
          </div>
          <div class="card-body">
            <div class="table-responsive">                  
				<table class="table table-bordered table-condensed table-hover" id="tablePaginatorFixedTributaria">
                	<thead>
		                <tr class="bg-dark text-white">                  
		                    <td><small>Año</td> 
		                    <td><small>Periodo</small></td>                   
		                    <td><small>Cod. Dependiente RC-IVA</small></td>
		                    <td><small>Nombres</small></td>
		                    <td><small>Primer Apellido</small></td>                    
		                    <td><small>Segundo Apellido</small></td>                    
		                    <td><small># Doc Identidad</small></td>
		                    <td><small>Tipo De Doumento</small></td>
		                    <td><small>Novedades</small></td>
		                    <td><small>Monto De Ingreso Neto</small></td>
		                    <?php
								//Solo en mayo se adiciona el retroactivo
								if($cod_mes==5){ ?>
									<td><small>Retroactivo</small></td>
								<?php 
								}
								//Para Diciembre se adiciona el aguinaldo
								if($cod_mes==12){ ?>
									<td><small>Bono</small></td>
								<?php 
								}
		                    ?>
		                    <td><small>Minimo No Imponible</small></td>
		                    <td><small>Importe Sujeto a Impuesto</small></td>
		                    <td><small>Impuesto RC-IVA</small></td>                            
		                    <td><small>(13%)Salario Mínimo</small></td>
		                    <td><small>Impuesto Neto RC-IVA</small></td>
		                    <td><small>F-110</small></td>
		                    <td><small>Saldo Físico</small></td>     
		                    <td><small>Saldo Del Dep</small></td>                    
		                    <td><small>Saldo Del Dep Anterior</small></td>
		                    <td><small>Mant Saldo Del Dep Anterior</small></td>
		                    <td><small>Saldo Ant Actu</small></td>
		                    <td><small>Saldo Utilizado</small></td>
		                    <td><small>RC-IVA retenido</small></td>
		                    <td><small>Saldo C.Fiscal Sig.</small></td>
		                </tr>                                  
	                </thead>
	                <tbody>
						<?php 
							$index=1;
							while ($row = $stmtPersonal->fetch()) 
							{

		                        $total_1+=round($row['monto_ingreso_neto'],2);
		                        $total_2+=round($row['minimo_no_imponble'],0);
		                        $total_3+=round($row['importe_sujeto_impuesto_i'],0);
		                        $total_4+=round($row['impuesto_rc_iva'],0);
		                        $total_5+=round($row['minimo_13'],0);
		                        $total_6+=round($row['impuesto_neto_rc_iva'],0);
		                        $total_7+=round($row['formulario_110_13'],0);
		                        $total_8+=round($row['saldo_favor_fisico'],0);
		                        $total_9+=round($row['saldo_favor_dependiente'],0);
		                        $total_10+=round($row['saldo_mes_anterior'],0);
		                        $total_11+=round($row['mantenimiento_saldo_mes_anterior'],0);
		                        $total_12+=round($row['saldo_anterior_actualizado'],0);
		                        $total_13+=round($row['saldo_utilizado'],0);
		                        $total_14+=round($row['impuesto_rc_iva_retenido'],0);
		                        $total_15+=round($row['saldo_credito_fiscal_mes_siguiente'],0);

		                        $monto_ingreso_neto=$row['monto_ingreso_neto'];

								/**
								 * La variable de "monto_retroactivo" hace referencia a:
								 * Retroactivo(mes:5) y/o Aguinaldo(mes:12)
								 */
		                        $monto_retroactivo=$row['monto_retroactivo'];
		                        $total_monto_retroactivo+=round($monto_retroactivo,2);
		                        if($monto_retroactivo > 0){
		                        	$monto_ingreso_neto = $monto_ingreso_neto - $monto_retroactivo;
									$total_1 			= $total_1 - $monto_retroactivo;
		                        }
		                        ?>
			                	<tr>			                		
				                    <td class="text-center small"><?=$nombre_gestion;?></td>
				                    <td class="text-left small"><?=$cod_mes;?></td>
				                    <td class="text-left small"><?=$row['codigo_dependiente']?></td>
				                    <td class="text-left small"><?=$row['primer_nombre'];?></td>
				                    <td class="text-left small"><?=$row['paterno'];?></td>
				                    <td class="text-left small"><?=$row['materno'];?></td>
				                    <td class="text-center small"><?=$row['identificacion'];?></td>
				                    <td class="text-center small"><?=$row['tipo_identificacion'].$row['tipo_identificacion_otro'];?></td>
				                    <td class="text-center small">V</td>
				                    <td class="text-center small"><?=number_format($monto_ingreso_neto,2);?></td>
				                    <?php
									//Solo en mayo se adiciona el retroactivo
								    if($cod_mes==5){ ?>
								    	<td class="text-center small"><?=number_format($monto_retroactivo,2);?></td>
								    <?php }
				                    ?>
									<?php
									// Solo en Diciembre se adiciona el Aguinaldo
								    if($cod_mes==12){ ?>
								    	<td class="text-center small"><?=number_format($monto_retroactivo,2);?></td>
								    <?php }
				                    ?>


				                    <td class="text-center small"><?=number_format($row['minimo_no_imponble'],0);?></td>
				                    <td class="small"><?=number_format($row['importe_sujeto_impuesto_i'],0);?></td>
				                    <td class="small"><?=number_format($row['impuesto_rc_iva'],0);?></td>
				                    <td class="small"><?=number_format($row['minimo_13'],0);?></td> 
				                    <td class="small"><?=number_format($row['impuesto_neto_rc_iva'],0);?></td>
				                    <td class="small"><?=number_format($row['formulario_110_13'],0);?></td>
				                    <td class="small"><?=number_format($row['saldo_favor_fisico'],0);?></td>
				                    <td class="text-center small"><?=number_format($row['saldo_favor_dependiente'],0);?></td>
				                    <td class="text-white small" style="background:#e59866;"><?=number_format($row['saldo_mes_anterior'],0);?></td>
				                    <td  class="text-center small"><?=number_format($row['mantenimiento_saldo_mes_anterior'],0);?></td>
				                    <td class="text-center small"><?=number_format($row['saldo_anterior_actualizado'],0);?></td>
				                    <td class="text-center small"><?=number_format($row['saldo_utilizado'],0);?></td>				                    
				                    <td class="text-white text-center small" style="background:red;"><?=number_format($row['impuesto_rc_iva_retenido'],0);?></td>
				                    <td class="text-center small"><?=number_format($row['saldo_credito_fiscal_mes_siguiente'],0);?></td>
				                </tr> 
			                  	<?php 
			                    $index+=1;
		                	}
						?>                      
	                </tbody>
	                <tfoot>
	                    <tr class="bg-dark text-white">                  
		                    <th colspan="9" class="text-center small">Total</th>
		                    
		                    <th class="text-center small"><?=number_format($total_1,0);?></th>
		                    <?php
							//Solo en mayo se adiciona el retroactivo
						    if($cod_mes==5){ ?>
						    	<th class="text-center small"><?=number_format($total_monto_retroactivo,0);?></th>
						    <?php }
		                    ?>
		                    <?php
							//Solo en mayo se adiciona el retroactivo
						    if($cod_mes==12){ ?>
						    	<th class="text-center small"><?=number_format($total_monto_retroactivo,0);?></th>
						    <?php }
		                    ?>
		                    <th class="text-center small"><?=number_format($total_2,0);?></th>
		                    <th class="text-center small"><?=number_format($total_3,0);?></th>                            
		                    <th class="text-center small"><?=number_format($total_4,0);?></th>
		                    <th class="text-center small"><?=number_format($total_5,0);?></th>
		                    <th class="text-center small"><?=number_format($total_6,0);?></th>
		                    <th class="text-center small"><?=number_format($total_7,0);?></th>
		                    <th class="text-center small"><?=number_format($total_8,0);?></th>	                    
		                    <th class="text-center small"><?=number_format($total_9,0);?></th>
		                    <th class="text-center small"><?=number_format($total_10,0);?></th>                            
		                    <th class="text-center small"><?=number_format($total_11,0);?></th>
		                    <th class="text-center small"><?=number_format($total_12,0);?></th>
		                    <th class="text-center small"><?=number_format($total_13,0);?></th>
		                    <th class="text-center small"><?=number_format($total_14,0);?></th>
		                    <th class="text-center small"><?=number_format($total_15,0);?></th>		                    
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
