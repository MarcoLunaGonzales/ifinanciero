<?php
	require_once __DIR__.'/../conexion.php';
	require_once __DIR__.'/../functionsGeneral.php';
	require_once '../layouts/bodylogin2.php';
	$dbh = new Conexion();

	$cod_planilla = $_GET["codigo_planilla"];//
	$cod_gestion = $_GET["cod_gestion"];//
	$cod_mes = $_GET["cod_mes"];//
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
                Planilla De Sueldos
            </h4>                  
            <h6 class="card-title"><small>
              Codigo Planilla: <?=$cod_planilla;?><br>
              Gestion: <?=$nombre_gestion; ?> / Mes: <?=$cod_mes; ?><br>              
              UO: <?=$nombre_uo;?>
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
		                    <th><small>Grado Académico</small></th>
		                    <th><small>Porcentaje</small></th>
		                    <th><small>Haber Básico</small></th>
		                    <th><small>Días Trab</small></th>                                        
		                    <th><small>Bono Antiguedad</small></th>
		                    <th class="bg-success text-white"><button id="botonBonos" style="border:none;" class="bg-success text-white small">Otros Bonos</button> </th>
		                    <?php
		                      $sqlBonos = "SELECT cod_bono,(select b.nombre from bonos b where b.codigo=cod_bono) as nombre_bono
		                              from bonos_personal_mes 
		                              where  cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1 GROUP BY (cod_bono)
		                              order by cod_bono ASC";
		                      $stmtBonos = $dbh->prepare($sqlBonos);
		                      $stmtBonos->execute();                      
		                      $stmtBonos->bindColumn('cod_bono',$cod_bono);
		                      $stmtBonos->bindColumn('nombre_bono',$nombre_bono);
		                      while ($row = $stmtBonos->fetch()) 
		                      { ?>
		                        <th class="bonosDet bg-success text-white" style="display:none"><small><?=$nombre_bono;?></small></th>                      
		                        <?php
		                        $arrayBonos[] = $cod_bono;
		                      }
		                    ?>
		                    <th><small>Monto Bonos</small></th>                            
		                    <th class="bg-primary text-white"><small>Total Ganado</small></th>

		                    <th class="bg-success text-white"><button id="botonAportes" style="border:none;" class="bg-success text-white small">Monto Aportes</button></th>
		                    <th class="aportesDet bg-success text-white" style="display:none"><small>AFP.Fut</small></th>
		                    <th class="aportesDet bg-success text-white" style="display:none"><small>AFP.Prev</small></th>
		                    <th class="aportesDet bg-success text-white" style="display:none"><small>A.Solidario(13)</small></th>
		                    <th class="aportesDet bg-success text-white" style="display:none"><small>A.Solidario(25)</small></th>
		                    <th class="aportesDet bg-success text-white" style="display:none"><small>A.Solidario(35)</small></th>
		                    <th class="aportesDet bg-success text-white" style="display:none"><small>RC-IVA</small></th>

		                    
		                    <th><small>Atrasos</small></th>
		                    <th><small>Anticipos</small></th>
		                    <th><small>Dotaciones</small></th>
		                    <th class="bg-success text-white"><button id="botonOtrosDescuentos" style="border:none;" class="bg-success text-white small">Otros Descuentos</button> </th>
		                    <?php  
		                      $swDescuentoOtro=false;                  
		                      $sqlDescuento = "SELECT cod_descuento,(select d.nombre from descuentos d where d.codigo=cod_descuento) as nombre_descuentos
		                              from descuentos_personal_mes 
		                              where  cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1 GROUP BY (cod_descuento)
		                              order by cod_descuento ASC";
		                      $stmtDescuento = $dbh->prepare($sqlDescuento);
		                      $stmtDescuento->execute();                      
		                      $stmtDescuento->bindColumn('cod_descuento',$cod_descuento);
		                      $stmtDescuento->bindColumn('nombre_descuentos',$nombre_descuentos);
		                      while ($row = $stmtDescuento->fetch()) 
		                      { ?>
		                        <th class="DescuentosOtros bg-success text-white" style="display:none"><small><?=$nombre_descuentos;?></small></th>
		                        <?php
		                        $arrayDescuentos[] = $cod_descuento;
		                        $swDescuentoOtro=true;
		                      }
		                    ?>
		                    <th><small>Monto Descuentos</small></th>     
		                    <th class="bg-primary text-white"><small>Liqu Pagable</small></th>                    
		                    <th><small>Seguro De Salud</small></th>
		                    <th><small>Riesgo Profesional</small></th>
		                    <th><small>Provivienda</small></th>
		                    <th><small>Apo Patronal Sol</small></th>
		                    <th><small>Total Apo Patronal</small></th>
		                </tr>                                  
	                </thead>
	                <tbody>
						<?php 
						$index=1;
						$sum_total_basico=0;
						$sum_total_b_antiguedad=0;
						$sum_total_o_bonos=0;
						$sum_total_m_bonos=0;
						$sum_total_t_ganado=0;
						$sum_total_m_aportes=0;
						$sum_total_atrasos=0;
						$sum_total_anticipos=0;
						$sum_total_dotaciones=0;
						$sum_total_o_descuentos=0;
						$sum_total_m_descuentos=0;
						$sum_total_l_pagable=0;
						$sum_total_a_patronal=0;
						while ($row = $stmtArea->fetch(PDO::FETCH_BOUND)) 
						{
							$sql = "SELECT ppm.cod_personalcargo,ppm.cod_gradoacademico,ppm.dias_trabajados,ppm.horas_pagadas,ppm.haber_basico,
									ppm.bono_academico,ppm.bono_antiguedad,ppm.monto_bonos,ppm.total_ganado,ppm.monto_descuentos,
									ppm.liquido_pagable,ppm.afp_1,ppm.afp_2,ppm.dotaciones,pad.porcentaje,
								(SELECT ga.nombre from personal_grado_academico ga where ga.codigo=ppm.cod_gradoacademico) as grado_academico,
						        (select p.primer_nombre from personal p where p.codigo=ppm.cod_personalcargo) as personal,
						        (select pa.paterno from personal pa where pa.codigo=ppm.cod_personalcargo) as paterno,
						        (select pa.materno from personal pa where pa.codigo=ppm.cod_personalcargo) as materno,
						        (select p3.identificacion from personal p3 where p3.codigo=ppm.cod_personalcargo) as doc_id,
						        (select (select pd.abreviatura from personal_departamentos pd where pd.codigo=p3.cod_lugar_emision)
						             from personal p3 where p3.codigo=ppm.cod_personalcargo) as lug_emision,
						  		(select p4.lugar_emision_otro from personal p4 where p4.codigo=ppm.cod_personalcargo) as lug_emision_otro
								from planillas_personal_mes ppm,personal_area_distribucion pad
								where ppm.cod_personalcargo=pad.cod_personal and cod_planilla=$cod_planilla and pad.cod_uo=$cod_uo and pad.cod_area=$cod_area_x order by paterno";

							$stmtPersonal = $dbh->prepare($sql);
							$stmtPersonal->execute();	

							$stmtPersonal->bindColumn('cod_personalcargo', $cod_personalcargo);
							$stmtPersonal->bindColumn('personal', $nombrePersonal);
							$stmtPersonal->bindColumn('paterno', $paterno);
							$stmtPersonal->bindColumn('materno', $materno);
							$stmtPersonal->bindColumn('doc_id', $doc_id);
							$stmtPersonal->bindColumn('lug_emision', $lug_emision);
							$stmtPersonal->bindColumn('lug_emision_otro', $lug_emision_otro);

							$stmtPersonal->bindColumn('cod_gradoacademico', $cod_gradoacademico);
							$stmtPersonal->bindColumn('grado_academico', $grado_academico);
							$stmtPersonal->bindColumn('dias_trabajados', $dias_trabajados);
							$stmtPersonal->bindColumn('horas_pagadas', $horas_pagadas);
							$stmtPersonal->bindColumn('haber_basico', $haber_basico);
							$stmtPersonal->bindColumn('bono_academico', $bono_academico);
							$stmtPersonal->bindColumn('bono_antiguedad', $bono_antiguedad);
							$stmtPersonal->bindColumn('monto_bonos', $monto_bonos);
							$stmtPersonal->bindColumn('total_ganado', $total_ganado);
							$stmtPersonal->bindColumn('monto_descuentos', $monto_descuentos);
							$stmtPersonal->bindColumn('liquido_pagable', $liquido_pagable);
							$stmtPersonal->bindColumn('afp_1', $afp_1);
							$stmtPersonal->bindColumn('afp_2', $afp_2);
							$stmtPersonal->bindColumn('dotaciones', $dotaciones);
							$stmtPersonal->bindColumn('porcentaje', $porcentaje);
							while ($row = $stmtPersonal->fetch()) 
							{  
		                        $sql = "SELECT *                              
		                                from planillas_personal_mes_patronal 
		                                where cod_planilla=$cod_planilla and cod_personal_cargo=$cod_personalcargo";
		                          $stmtPersonalPatronal = $dbh->prepare($sql);
		                          $stmtPersonalPatronal->execute();
		                          $resultPatronal=$stmtPersonalPatronal->fetch();
		                          $codigo_ppm_patronal=$resultPatronal['codigo'];
		                          $cod_planilla_p=$resultPatronal['cod_planilla'];
		                          $cod_personal_cargo_p=$resultPatronal['cod_personal_cargo'];
		                          $a_solidario_13000=$resultPatronal['a_solidario_13000'];
		                          $a_solidario_25000=$resultPatronal['a_solidario_25000'];
		                          $a_solidario_35000=$resultPatronal['a_solidario_35000'];
		                          $rc_iva=$resultPatronal['rc_iva'];
		                          $atrasos=$resultPatronal['atrasos'];
		                          $anticipo=$resultPatronal['anticipo'];
		                          $seguro_de_salud=$resultPatronal['seguro_de_salud'];
		                          $riesgo_profesional=$resultPatronal['riesgo_profesional'];
		                          $provivienda=$resultPatronal['provivienda'];
		                          $a_patronal_sol=$resultPatronal['a_patronal_sol'];
		                          $total_a_patronal=$resultPatronal['total_a_patronal'];


		                          //dividiendo montos a su porcentaje respectivo
		                          $haber_basico_tp=$haber_basico;
		                          $bono_antiguedad_tp=$bono_antiguedad;
		                          $monto_bonos_tp=$monto_bonos;
		                          $total_ganado_tp=$total_ganado;
		                          $atrasos_tp=$atrasos;
		                          $anticipo_tp=$anticipo;
		                          $monto_descuentos_tp=$monto_descuentos;
		                          $dotaciones_tp=$dotaciones;
		                          $seguro_de_salud_tp=$seguro_de_salud;
		                          $riesgo_profesional_tp=$riesgo_profesional;
		                          $provivienda_tp=$provivienda;
		                          $a_patronal_sol_tp=$a_patronal_sol;
		                          $liquido_pagable_tp=$liquido_pagable;
		                          $total_a_patronal_tp=$total_a_patronal;
		                          
		                          $sum_total_basico+=$haber_basico_tp;
		                          $sum_total_b_antiguedad+=$bono_antiguedad_tp;
		                          $sum_total_m_bonos+=$monto_bonos_tp;
		                          $sum_total_t_ganado+=$total_ganado_tp;                          
		                          $sum_total_atrasos+=$atrasos_tp;
		                          $sum_total_anticipos+=$anticipo_tp;
		                          $sum_total_m_descuentos+=$monto_descuentos_tp;
		                          $sum_total_dotaciones+=$dotaciones_tp;
		                          
		                          $sum_total_l_pagable+=$liquido_pagable_tp;
		                          $sum_total_a_patronal+=$total_a_patronal_tp;

		                        ?>
			                	<tr>                                                        
				                    <td class="text-center small"><?=$index;?></td>
				                    <td class="text-left small"><?=$nombre_area_x;?></td>                    
				                    <td class="text-left small"><?=$paterno;?></td>
				                    <td class="text-left small"><?=$materno;?></td>
				                    <td class="text-left small"><?=$nombrePersonal;?></td>
				                    <td class="text-center small"><?=$doc_id;?>-<?=$lug_emision?><?=$lug_emision_otro?></td>                  
				                    <td class="text-left small"><?=$grado_academico;?></td>                    
				                    <?php if($porcentaje!=100){ ?>
				                    <td class="text-center small"><span class="badge badge-danger"><?=$porcentaje;?></span></td>
				                    <?php }else{?>
				                    <td class="text-center small"><?=$porcentaje;?></td>
				                    <?php }
				                    ?>
				                    
				                    <td class="text-center small"><?=formatNumberDec($haber_basico_tp);?></td>
				                    <td class="text-center small"><?=$dias_trabajados;?></td>                
				                    <td class="text-center small"><?=formatNumberDec($bono_antiguedad_tp);?></td>
				                    <?php                    
				                    if(count($arrayBonos)>0)
				                    {
				                      $sqlTotalOtroBonos = "SELECT SUM(monto) as suma_bono
				                              from bonos_personal_mes 
				                              where  cod_personal=$cod_personalcargo and cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1";
				                      $stmtbonoOtros = $dbh->prepare($sqlTotalOtroBonos);
				                      $stmtbonoOtros->execute();
				                      $resultbonoOtros=$stmtbonoOtros->fetch();
				                      $sumaBono_otros=$resultbonoOtros['suma_bono'];

				                      $sumaBono_otros_tp=$sumaBono_otros;
				                      $sum_total_o_bonos+=$sumaBono_otros_tp;
				                

				                      if($sumaBono_otros==null){ $sumaBono_otros_tp=0;}
				                      ?> 
				                      <td class="text-center small"><?=formatNumberDec($sumaBono_otros_tp);?></td>
				                      <?php
				                      set_time_limit(300);
				                      for ($j=0; $j <count($arrayBonos);$j++){ 
				                          $cod_bono_aux=$arrayBonos[$j];                          
				                          $sqlBonosOtrs = "SELECT cod_bono,monto
				                                from bonos_personal_mes 
				                                where  cod_personal=$cod_personalcargo and cod_gestion=$cod_gestion and cod_mes=$cod_mes and  cod_bono=$cod_bono_aux and cod_estadoreferencial=1";
				                          $stmtBonosOtrs = $dbh->prepare($sqlBonosOtrs);
				                          $stmtBonosOtrs->execute();
				                          $resultBonosOtros=$stmtBonosOtrs->fetch();
				                          $cod_bonosX=$resultBonosOtros['cod_bono'];
				                          $montoX=$resultBonosOtros['monto'];

				                          $montoX_tp=$montoX;

				                          if($cod_bonosX==$cod_bono_aux){ ?>
				                            <td  class="bonosDet small" style="display:none"><?=formatNumberDec($montoX_tp);?></td>  
				                          <?php                            
				                          }else{ $montoAux=0; ?>                                                          
				                            <td  class="bonosDet small" style="display:none"><?=formatNumberDec($montoAux);?></td>
				                          <?php                            
				                          }
				                      	}
				                    }else{$sumabonos_otros=0;
				                      ?>
				                      <td class="small"><?=formatNumberDec($sumabonos_otros);?></td>
				                      <?php
				                    }                                          
				                      
				                    	$afp_1_tp=$afp_1;
				                    	$afp_2_tp=$afp_2;
				                    	$a_solidario_13000_tp=$a_solidario_13000;
				                    	$a_solidario_25000_tp=$a_solidario_25000;
				                    	$a_solidario_35000_tp=$a_solidario_35000;
				                    	$rc_iva_tp=$rc_iva;


			                      		$monto_aportes_tp = $afp_1_tp+$afp_2_tp+$a_solidario_13000_tp+$a_solidario_25000_tp+$a_solidario_35000_tp+$rc_iva_tp;

			                      		$sum_total_m_aportes+=$monto_aportes_tp;                    
				                          
				                    ?>  
				                    <td class="small"><?=formatNumberDec($monto_bonos_tp);?></td>
				                    <td class="small"><?=formatNumberDec($total_ganado_tp);?></td>
				                    <td class="small"><?=formatNumberDec($monto_aportes_tp);?></td> 
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($afp_1_tp);?></td>
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($afp_2_tp);?></td>
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($a_solidario_13000_tp);?></td>
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($a_solidario_25000_tp);?></td>
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($a_solidario_35000_tp);?></td>
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($rc_iva_tp);?></td>

				                    <td class="small"><?=formatNumberDec($atrasos_tp);?></td>
				                    <td class="small"><?=formatNumberDec($anticipo_tp);?></td>
				                    <td class="small"><?=formatNumberDec($dotaciones_tp);?></td>
				                    
				                    <?php
				                    if($swDescuentoOtro)
				                    {
				                      $sqlTotalOtroDescuentos = "SELECT SUM(monto) as suma_descuentos
				                              from descuentos_personal_mes 
				                              where  cod_personal=$cod_personalcargo and cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1";
				                      $stmtDescuentosOtros = $dbh->prepare($sqlTotalOtroDescuentos);
				                      $stmtDescuentosOtros->execute();
				                      $resultDescuentosOtros=$stmtDescuentosOtros->fetch();
				                      $sumaDescuentos_otros=$resultDescuentosOtros['suma_descuentos'];

				                      $sumaDescuentos_otros_tp=$sumaDescuentos_otros;

				                      $sum_total_o_descuentos+=$sumaDescuentos_otros_tp;

				                      if($sumaDescuentos_otros_tp==null){ $sumaDescuentos_otros_tp=0;}
				                      ?> 
				                      <td class="small"><?=formatNumberDec($sumaDescuentos_otros_tp);?></td>
				                      <?php                      

				                        for ($j=0; $j <count($arrayDescuentos); $j++) { 
				                          $cod_descuento_aux=$arrayDescuentos[$j];                          
				                          $sqlDescuentos = "SELECT cod_descuento,monto
				                                from descuentos_personal_mes 
				                                where  cod_personal=$cod_personalcargo and cod_gestion=$cod_gestion and cod_mes=$cod_mes and  cod_descuento=$cod_descuento_aux and cod_estadoreferencial=1";
				                          $stmtDescuentos = $dbh->prepare($sqlDescuentos);
				                          $stmtDescuentos->execute();
				                          $resultDescOtros=$stmtDescuentos->fetch();
				                          $cod_descuentosX=$resultDescOtros['cod_descuento'];
				                          $montoX=$resultDescOtros['monto'];
				                          $montoX_tp=$montoX;

				                          if($cod_descuentosX==$cod_descuento_aux){ ?>
				                            <td  class="DescuentosOtros small" style="display:none"><?=formatNumberDec($montoX_tp);?></td>  
				                          <?php                            
				                          }else{ $montoAux=0; ?>                                                          
				                            <td  class="DescuentosOtros small" style="display:none"><?=formatNumberDec($montoAux);?></td>
				                          <?php                            
				                          }
				                        }  	                     
				                        $monto_descuentosX_tp=$monto_descuentos_tp+$sumaDescuentos_otros_tp;                      
				                    }else{
				                      $sumaDescuentos_otros_tp=0;
				                      ?>
				                      <td class="small"><?=formatNumberDec($sumaDescuentos_otros_tp);?></td>
				                      <?php
				                      $monto_descuentosX_tp=$monto_descuentos_tp+$sumaDescuentos_otros_tp;
				                    }
				                    ?>
				                                          
				                    <td class="text-center small"><?=formatNumberDec($monto_descuentosX_tp);?></td>                                                          
				                    <td class="bg-primary text-white small"><?=formatNumberDec($liquido_pagable_tp);?></td>                                  
				                    <td  class="text-center small"><?=formatNumberDec($seguro_de_salud_tp);?></td>
				                    <td class="text-center small"><?=formatNumberDec($riesgo_profesional_tp);?></td>
				                    <td class="text-center small"><?=formatNumberDec($provivienda_tp);?></td>
				                    <td class="text-center small"><?=formatNumberDec($a_patronal_sol_tp);?></td>
				                    <td class="text-center small"><?=formatNumberDec($total_a_patronal_tp);?></td>
				                </tr> 
			                  	<?php 
			                    $index+=1;
		                	}

						}

	                 
						?>                      
	                </tbody>
	                <tfoot>
	                    <tr class="bg-dark text-white">                  
	                    <th colspan="8" class="text-center small">Total</th>
	                    
	                    <th class="text-center small"><?=formatNumberDec($sum_total_basico);?></th>
	                    <th class="text-center small">-</th>                                        
	                    <th class="text-center small"><?=formatNumberDec($sum_total_b_antiguedad);?></th>
	                    <th class="bg-success text-white small"><?=formatNumberDec($sum_total_o_bonos);?> </th>
	                    <?php
	                      $sqlBonos = "SELECT cod_bono
	                              from bonos_personal_mes 
	                              where  cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1 GROUP BY (cod_bono)";
	                      $stmtBonos = $dbh->prepare($sqlBonos);
	                      $stmtBonos->execute();                      
	                      $stmtBonos->bindColumn('cod_bono',$cod_bono);                      
	                      while ($row = $stmtBonos->fetch()) 
	                      { ?>
	                        <th class="bonosDet bg-success text-white small" style="display:none">-</th>                      
	                        <?php                        
	                      }
	                    ?>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_m_bonos);?></th>                            
	                    <th class="bg-primary text-white text-center small"><?=formatNumberDec($sum_total_t_ganado);?></th>

	                    <th class="bg-success text-white small"><?=formatNumberDec($sum_total_m_aportes);?></th>
	                    <th class="aportesDet bg-success text-white small" style="display:none">-</th>
	                    <th class="aportesDet bg-success text-white small" style="display:none">-</th>
	                    <th class="aportesDet bg-success text-white small" style="display:none">-</th>
	                    <th class="aportesDet bg-success text-white small" style="display:none">-</th>
	                    <th class="aportesDet bg-success text-white small" style="display:none">-</th>
	                    <th class="aportesDet bg-success text-white small" style="display:none">-</th>

	                    
	                    <th class="text-center small"><?=formatNumberDec($sum_total_atrasos);?></th>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_anticipos);?></th>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_dotaciones);?></th>
	                    <th class="bg-success text-white small"><?=formatNumberDec($sum_total_o_descuentos);?></th>
	                    <?php  
	                      $swDescuentoOtro=false;                  
	                      $sqlDescuento = "SELECT cod_descuento
	                              from descuentos_personal_mes 
	                              where  cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1 GROUP BY (cod_descuento)";
	                      $stmtDescuento = $dbh->prepare($sqlDescuento);
	                      $stmtDescuento->execute();                      
	                      $stmtDescuento->bindColumn('cod_descuento',$cod_descuento);                      
	                      while ($row = $stmtDescuento->fetch()) 
	                      { ?>
	                        <th class="DescuentosOtros bg-success text-white small" style="display:none">-</th>
	                        <?php
	                      }
	                    ?>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_m_descuentos);?></th>                                        
	                    <th class="bg-primary text-white small"><?=formatNumberDec($sum_total_l_pagable);?></th>                    
	                    <th class="text-center small">-</th>
	                    <th class="text-center small">-</th>
	                    <th class="text-center small">-</th>
	                    <th class="text-center small">-</th>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_a_patronal);?></th>
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
  
  $("#botonBonos").on("click", function(){
    $(".bonosDet").toggle();

  });
  $("#botonDescuetos").on("click", function(){
    $(".descuentosDet").toggle();
  });

  $("#botonAportes").on("click", function(){
    $(".aportesDet").toggle();
  });

  $("#botonOtrosDescuentos").on("click", function(){
    $(".DescuentosOtros").toggle();
  });
</script>