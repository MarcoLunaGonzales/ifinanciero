<?php
	require_once __DIR__.'/../conexion.php';
	require_once __DIR__.'/../functionsGeneral.php';
	require_once '../functions.php';
	require_once '../layouts/bodylogin2.php';
	$dbh = new Conexion();

	$cod_planilla = $_GET["codigo_planilla"];//
	$cod_gestion = $_GET["cod_gestion"];//
	$cod_mes = $_GET["cod_mes"];//
	$cod_uo = $_GET["codigo_uo"];//
	
	$nombre_gestion=nameGestion($cod_gestion);
	if($cod_uo==-100){		
		$sql="SELECT cod_uo,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo 
			from personal_area_distribucion_planilla 
			where cod_estadoreferencial=1 
			and cod_uo<> 0 
			and cod_uo<> ' ' 
			AND cod_planilla = '$cod_planilla'
			GROUP BY cod_uo";
        // echo $sql;
        $stmtUO=$dbh->prepare($sql);
		$stmtUO->execute();
		$nombre_uo="";
		$string_cod_uo="";
		while ($row = $stmtUO->fetch()) 
		{			
			$nombre_uo.=$row['nombre_uo'].",";
			$string_cod_uo.=$row['cod_uo'].",";
		}
		$nombre_uo=trim($nombre_uo,",");
		$cod_uo=trim($string_cod_uo,",");
	}else{
		$nombre_uo=nameUnidad($cod_uo);
	}
	// $sqlArea="SELECT cod_area,(SELECT a.abreviatura from areas a where a.codigo=cod_area) as nombre_area
	// from personal_area_distribucion_planilla
	// where cod_estadoreferencial=1 and cod_uo in ($cod_uo)
	// GROUP BY cod_area order by nombre_area";
	// // echo $sqlArea;
	// $stmtArea = $dbh->prepare($sqlArea);
	// $stmtArea->execute();
	// $stmtArea->bindColumn('cod_area', $cod_area_x);
	// $stmtArea->bindColumn('nombre_area', $nombre_area_x);
?>
<style>
	  table ,tr td{
    border:1px solid red
}
tbody {
    display:block;
    height:500px;
    overflow:auto;
}
thead, tbody tr {
    display:table;
    width:100%;
    table-layout:fixed;/* even columns width , fix width of table too*/
}
thead {
    width: calc( 100% - 1em )/* scrollbar is average 1em/16px width, remove it from thead width */
}
tfoot, tbody tr {
    display:table;
    width:100%;
    table-layout:fixed;/* even columns width , fix width of table too*/
}
tfoot {
    width: calc( 100% - 1em )/* scrollbar is average 1em/16px width, remove it from thead width */
}
table {
    width:2000px !important;
}
</style>
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
              Oficina: <?=$nombre_uo;?>
              </small>                    
            </h6>             
          </div>
          <div class="card-body">
            <div class="table-responsive">                  
				<!-- <table class="table table-bordered table-condensed table-hover" id="tablePaginatorHeaderFooter"> -->
					<table width="2000px !important" class="table table-condensed table-bordered table-sm table-striped mb-0" id="tablePaginatorHeaderFooter123">
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
		                    <th><small>Bono de Antig</small></th>
		                    <th class="bg-success text-white"><a id="botonBonos" style="border:none;" class="bg-success text-white small">Otros Bonos</a> </th>
		                    <?php
								$swBonosOtro=false;
								$sqlBonos = "SELECT cod_bono,(select b.nombre from bonos b where b.codigo=cod_bono) as nombre_bono
								  from bonos_personal_mes 
								  where  cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1 GROUP BY (cod_bono)
								  order by cod_bono ASC";
								// echo $sqlBonos;
								$stmtBonos = $dbh->prepare($sqlBonos);
								$stmtBonos->execute();                      
								$stmtBonos->bindColumn('cod_bono',$cod_bono);
								$stmtBonos->bindColumn('nombre_bono',$nombre_bono);
								$x=0;
								while ($row = $stmtBonos->fetch()) 
								{ ?>
									<th class="bonosDet bg-success text-white" style="display:none"><small><?=$nombre_bono;?></small></th>                      
									<?php
									$arrayBonos[] = $cod_bono;
									$arrayBonos_aux[$x]=0;
									$swBonosOtro=true;
									$x++;
								}
		                    ?>
		                    <th><small>Monto Bonos</small></th>                            
		                    <th class="bg-primary text-white"><small>Tot Gan</small></th>
		                    <th class="bg-success text-white"><a id="botonAportes" style="border:none;" class="bg-success text-white small">+Aport</a></th>
		                    <th class="aportesDet bg-success text-white" style="display:none"><small>Gestora</small></th>
		                    <th class="aportesDet bg-success text-white" style="display:none"><small>-</small></th>
		                    <th class="aportesDet bg-success text-white" style="display:none"><small>A.Sol(13)</small></th>
		                    <th class="aportesDet bg-success text-white" style="display:none"><small>A.Sol(25)</small></th>
		                    <th class="aportesDet bg-success text-white" style="display:none"><small>A.Sol(35)</small></th>
		                    <th ><small>RC-IVA</small></th>
		                    <!-- <th><small>Atrasos</small></th> -->
		                    <th><small>Anticipos</small></th>
		                    <th><small>Dotaciones</small></th>
		                    <th class="bg-success text-white"><a id="botonOtrosDescuentos" style="border:none;" class="bg-success text-white small">Otros Desc</a> </th>
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
		                      $x=0;
		                      while ($row = $stmtDescuento->fetch()) 
		                      { ?>
		                        <th class="DescuentosOtros bg-success text-white" style="display:none"><small><?=$nombre_descuentos;?></small></th>
		                        <?php
		                        $arrayDescuentos[] = $cod_descuento;
		                        $arrayDescuentos_aux[$x]=0;
		                        $swDescuentoOtro=true;
		                        $x++;
		                      }
		                    ?>
		                    <th><small>Monto Desc</small></th>     
		                    <th class="bg-primary text-white"><small>Liqu Pag</small></th>                    
		                    <th><small>Seg De Sal</small></th>
		                    <th><small>Ries Prof</small></th>
		                    <th><small>Proviv</small></th>
		                    <th><small>Apo Patr Sol</small></th>
		                    <th><small>Tot Apo Patr</small></th>
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

						$suma_total_afp_1=0;
                  		$suma_total_afp_2=0;
                  		$suma_total_a_solidario_13000=0;
                  		$suma_total_a_solidario_25000=0;
                  		$suma_total_a_solidario_35000=0;
                  		$suma_total_rc_iva=0;

						$sum_total_atrasos=0;
						$sum_total_anticipos=0;
						$sum_total_dotaciones=0;
						$sum_total_o_descuentos=0;
						$sum_total_m_descuentos=0;
						$sum_total_l_pagable=0;
						$sum_total_a_patronal=0;

						$sum_total_seguro_sal=0;
						$sum_total_riesgo_profesional=0;
						$sum_total_provivienda=0;
						$sum_total_patronal=0;

						$dias_trabajados_asistencia=30;//ver datos
						// while ($row = $stmtArea->fetch(PDO::FETCH_BOUND)) 
						// {
							$sql="SELECT ppm.cod_personalcargo,ppm.cod_gradoacademico,ppm.dias_trabajados,ppm.horas_pagadas,ppm.haber_basico,
									ppm.bono_academico,ppm.bono_antiguedad,ppm.monto_bonos,ppm.total_ganado,ppm.monto_descuentos,
									ppm.liquido_pagable,ppm.afp_1,ppm.afp_2,ppm.dotaciones,100 as porcentaje,
								(SELECT ga.nombre from personal_grado_academico ga where ga.codigo=ppm.cod_gradoacademico) as grado_academico,
						        p.primer_nombre  as personal,
						        p.paterno,
						        p.materno,
						        p.identificacion as doc_id,
						        (select (select pd.abreviatura from personal_departamentos pd where pd.codigo=p3.cod_lugar_emision)
						             from personal p3 where p3.codigo=ppm.cod_personalcargo) as lug_emision,
						  		p.lugar_emision_otro as lug_emision_otro,
								ppm.cod_uo as cod_uo,
								ppm.cod_area,
								(select a.nombre from areas a where a.codigo=p.cod_area)as areas
								from planillas_personal_mes ppm,personal p
								where ppm.cod_personalcargo=p.codigo and cod_planilla='$cod_planilla'  order by paterno";
									// echo $sql;
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
							$stmtPersonal->bindColumn('cod_uo', $cod_uo_xy);
							$stmtPersonal->bindColumn('cod_area', $cod_area_xy);
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
		                          // $atrasos=$resultPatronal['atrasos'];
		                          $anticipo=$resultPatronal['anticipo'];
		                          $seguro_de_salud=$resultPatronal['seguro_de_salud'];
		                          $riesgo_profesional=$resultPatronal['riesgo_profesional'];
		                          $provivienda=$resultPatronal['provivienda'];
		                          $a_patronal_sol=$resultPatronal['a_patronal_sol'];
		                          $total_a_patronal=$resultPatronal['total_a_patronal'];
		                          //dividiendo montos a su porcentaje respectivo
		                          $haber_basico_tp=$haber_basico*$porcentaje/100;
		                          $bono_antiguedad_tp=$bono_antiguedad*$porcentaje/100;
		                          $monto_bonos_tp=$monto_bonos*$porcentaje/100;
		                          $total_ganado_tp=$total_ganado*$porcentaje/100;
		                          // $atrasos_tp=$atrasos*$porcentaje/100;
		                          $anticipo_tp=$anticipo*$porcentaje/100;
		                          $monto_descuentos_tp=$monto_descuentos*$porcentaje/100;
		                          $dotaciones_tp=$dotaciones*$porcentaje/100;
		                          $seguro_de_salud_tp=$seguro_de_salud*$porcentaje/100;
		                          $riesgo_profesional_tp=$riesgo_profesional*$porcentaje/100;
		                          $provivienda_tp=$provivienda*$porcentaje/100;
		                          $a_patronal_sol_tp=$a_patronal_sol*$porcentaje/100;

		                          $liquido_pagable_tp=$liquido_pagable*$porcentaje/100;
		                          $total_a_patronal_tp=$total_a_patronal*$porcentaje/100;
		                          $sum_total_basico+=$haber_basico_tp;
		                          $sum_total_b_antiguedad+=$bono_antiguedad_tp;
		                          $sum_total_m_bonos+=$monto_bonos_tp;
		                          $sum_total_t_ganado+=$total_ganado_tp;                          
		                          // $sum_total_atrasos+=$atrasos_tp;
		                          $sum_total_anticipos+=$anticipo_tp;
		                          $sum_total_m_descuentos+=$monto_descuentos_tp;
		                          $sum_total_dotaciones+=$dotaciones_tp;
		                          
		                          $sum_total_l_pagable+=$liquido_pagable_tp;
		                          $sum_total_seguro_sal+=$seguro_de_salud_tp;
		                          $sum_total_riesgo_profesional+=$riesgo_profesional_tp;
		                          $sum_total_provivienda+=$provivienda_tp;
		                          $sum_total_patronal+=$a_patronal_sol_tp;
		                          $sum_total_a_patronal+=$total_a_patronal_tp;

		                          $nombreAreaxy=trim(abrevArea($cod_area_xy),",");
		                          $nombreuoxy=trim(abrevUnidad($cod_uo_xy),",");

		                        ?>
			                	<tr>                                                        
				                    <td class="text-center small"><?=$index;?></td>
				                    <td class="text-left small"><?=$nombreAreaxy;?>/<?=$nombreuoxy?></td>                    
				                    <td class="text-left small"><?=$paterno;?></td>
				                    <td class="text-left small"><?=$materno;?></td>
				                    <td class="text-left small"><?=$nombrePersonal;?></td>
				                    <td class="text-center small"><?=$doc_id;?>-<small><?=$lug_emision?><?=$lug_emision_otro?></small></td>                  
				                    <td class="text-left small"><small><?=$grado_academico;?></small></td>                    
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
				                    if($swBonosOtro)
				                    {
				                        $total_bonos1=0;
										  $total_bonos2=0;
										  $sqlBonos1 = "SELECT bpm.monto
										  from bonos_personal_mes bpm,bonos b
										  where bpm.cod_bono=b.codigo and bpm.cod_personal=$cod_personalcargo and bpm.cod_gestion=$cod_gestion and bpm.cod_mes=$cod_mes and bpm.cod_estadoreferencial=1 and b.cod_tipocalculobono=1";
										  $stmtBonos1 = $dbh->prepare($sqlBonos1);
										  $stmtBonos1->execute();
										  $stmtBonos1->bindColumn('monto',$monto1);
										  while ($row = $stmtBonos1->fetch()) 
										  {
										    $total_bonos1=$total_bonos1+$monto1;
										  }
										    $sqlBonos2 = "SELECT bpm.monto
										  from bonos_personal_mes bpm,bonos b
										  where bpm.cod_bono=b.codigo and bpm.cod_personal=$cod_personalcargo and bpm.cod_gestion=$cod_gestion and bpm.cod_mes=$cod_mes and bpm.cod_estadoreferencial=1 and b.cod_tipocalculobono=2";
										  $stmtBonos2 = $dbh->prepare($sqlBonos2);
										  $stmtBonos2->execute();
										  $stmtBonos2->bindColumn('monto',$monto2);
										  while ($row = $stmtBonos2->fetch()) 
										  {
										    $porcen_monto=$dias_trabajados_asistencia*100/$dias_trabajados;
										    $monto2_aux=$porcen_monto*$monto2/100;
										    $total_bonos2=$total_bonos2+$monto2_aux;
										  }
										$sumaBono_otros=$total_bonos1+$total_bonos2;
				                      $sumaBono_otros_tp=$sumaBono_otros*$porcentaje/100;
				                      $sum_total_o_bonos+=$sumaBono_otros_tp;
				                      if($sumaBono_otros==null){ $sumaBono_otros_tp=0;}
				                      ?> 
				                      <td class="text-center small"><?=formatNumberDec($sumaBono_otros_tp);?></td>
				                      <?php
				                      set_time_limit(300);
				                      for ($j=0; $j <count($arrayBonos);$j++){ 
				                          $cod_bono_aux=$arrayBonos[$j];                          
				                          $sqlBonosOtrs = "SELECT bpm.cod_bono,bpm.monto,b.cod_tipocalculobono
				                                from bonos_personal_mes bpm,bonos b 
				                                where   bpm.cod_bono=b.codigo and bpm.cod_personal=$cod_personalcargo and bpm.cod_gestion=$cod_gestion and bpm.cod_mes=$cod_mes and  bpm.cod_bono=$cod_bono_aux and bpm.cod_estadoreferencial=1";
				                          $stmtBonosOtrs = $dbh->prepare($sqlBonosOtrs);
				                          $stmtBonosOtrs->execute();
				                          $resultBonosOtros=$stmtBonosOtrs->fetch();
				                          $cod_bonosX=$resultBonosOtros['cod_bono'] ?? '';
				                          $montoX=$resultBonosOtros['monto'] ?? 0;
				                          $tipoBonoX=$resultBonosOtros['cod_tipocalculobono'] ?? '';
				                          if($tipoBonoX==2){
				                          	$porcen_monto=30*100/$dias_trabajados;
										    $montoX_aux=$porcen_monto*$montoX/100;
				                          }else $montoX_aux=$montoX;

				                          $montoX_tp=$montoX_aux*$porcentaje/100;
				                          if($cod_bonosX==$cod_bono_aux){ ?>
				                            <td  class="bonosDet small" style="display:none"><?=formatNumberDec($montoX_tp);?></td>  
				                          <?php                            
				                          }else{ $montoAux=0; ?>                                                          
				                            <td  class="bonosDet small" style="display:none"><?=formatNumberDec($montoAux);?></td>
				                          <?php                            
				                          }
				                          $arrayBonos_aux[$j]+=$montoX_tp;
				                      	}
				                    }else{
				                      $sumabonos_otros=0;
				                      ?>
				                      <td class="small"><?=formatNumberDec($sumabonos_otros);?></td>
				                      <?php
				                    }                                          
				                      
			                    	$afp_1_tp=$afp_1*$porcentaje/100;
			                    	$afp_2_tp=$afp_2*$porcentaje/100;
			                    	$a_solidario_13000_tp=$a_solidario_13000*$porcentaje/100;
			                    	$a_solidario_25000_tp=$a_solidario_25000*$porcentaje/100;
			                    	$a_solidario_35000_tp=$a_solidario_35000*$porcentaje/100;
			                    	$rc_iva_tp=$rc_iva*$porcentaje/100;
		                      		//$monto_aportes_tp = $afp_1_tp+$afp_2_tp+$a_solidario_13000_tp+$a_solidario_25000_tp+$a_solidario_35000_tp+$rc_iva_tp;
			                    	/*Corregimos los aportes porque no deben tomar en cuenta el iva*/
			                    	$monto_aportes_tp = $afp_1_tp+$afp_2_tp+$a_solidario_13000_tp+$a_solidario_25000_tp+$a_solidario_35000_tp;

		                      		$sum_total_m_aportes+=$monto_aportes_tp;
		                      		
		                      		$suma_total_afp_1+=$afp_1_tp;
		                      		$suma_total_afp_2+=$afp_2_tp;
		                      		$suma_total_a_solidario_13000+=$a_solidario_13000_tp;
		                      		$suma_total_a_solidario_25000+=$a_solidario_25000_tp;
		                      		$suma_total_a_solidario_35000+=$a_solidario_35000_tp;
		                      		$suma_total_rc_iva+=$rc_iva_tp;

				                          
				                    ?>  
				                    <td class="small"><?=formatNumberDec($monto_bonos_tp);?></td>
				                    <td class="small"><?=formatNumberDec($total_ganado_tp);?></td>
				                    <td class="small"><?=formatNumberDec($monto_aportes_tp);?></td> 
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($afp_1_tp);?></td>
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($afp_2_tp);?></td>
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($a_solidario_13000_tp);?></td>
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($a_solidario_25000_tp);?></td>
				                    <td class="aportesDet small" style="display:none"><?=formatNumberDec($a_solidario_35000_tp);?></td>
				                    <td class="small"><?=formatNumberDec($rc_iva_tp);?></td>
				                    <td class="small"><?=formatNumberDec($anticipo_tp);?></td>
				                    <td class="small"><?=formatNumberDec($dotaciones_tp);?></td>
				                    
				                    <?php
				                    if($swDescuentoOtro){
				                      $sqlTotalOtroDescuentos = "SELECT SUM(monto) as suma_descuentos
				                              from descuentos_personal_mes 
				                              where  cod_personal=$cod_personalcargo and cod_gestion=$cod_gestion and cod_mes=$cod_mes and cod_estadoreferencial=1";
				                      $stmtDescuentosOtros = $dbh->prepare($sqlTotalOtroDescuentos);
				                      $stmtDescuentosOtros->execute();
				                      $resultDescuentosOtros=$stmtDescuentosOtros->fetch();
				                      $sumaDescuentos_otros=$resultDescuentosOtros['suma_descuentos'];

				                      $sumaDescuentos_otros_tp=$sumaDescuentos_otros*$porcentaje/100;

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
				                          $montoX_tp=$montoX*$porcentaje/100;				                          
				                          if($cod_descuentosX==$cod_descuento_aux){ ?>
				                            <td  class="DescuentosOtros small" style="display:none"><?=formatNumberDec($montoX_tp);?></td>  
				                          <?php                            
				                          }else{ $montoAux=0; ?>                                                          
				                            <td  class="DescuentosOtros small" style="display:none"><?=formatNumberDec($montoAux);?></td>
				                          <?php                            
				                          }
				                          $arrayDescuentos_aux[$j]+=$montoX_tp;
				                        }  	                     
				                        //$monto_descuentosX_tp=$monto_descuentos_tp+$sumaDescuentos_otros_tp;
				                        $monto_descuentosX_tp=$monto_descuentos_tp;
				                    }else{
				                      $sumaDescuentos_otros_tp=0;
				                      ?>
				                      <td class="small"><?=formatNumberDec($sumaDescuentos_otros_tp);?></td>
				                      <?php
				                      //$monto_descuentosX_tp=$monto_descuentos_tp+$sumaDescuentos_otros_tp;
				                      $monto_descuentosX_tp=$monto_descuentos_tp;
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

						// }

	                 
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
	                      for ($i=0; $i <count($arrayBonos) ; $i++) { ?>
	                        <th class="bonosDet bg-success text-white small" style="display:none"><?=formatNumberDec($arrayBonos_aux[$i])?></th>
	                        <?php                        
	                      }
	                    ?>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_m_bonos);?></th>                            
	                    <th class="bg-primary text-white text-center small"><?=formatNumberDec($sum_total_t_ganado);?></th>

	                    <th class="bg-success text-white small"><?=formatNumberDec($sum_total_m_aportes);?></th>
	                    <th class="aportesDet bg-success text-white small" style="display:none"><?=formatNumberDec($suma_total_afp_1)?></th>
	                    <th class="aportesDet bg-success text-white small" style="display:none"><?=formatNumberDec($suma_total_afp_2)?></th>
	                    <th class="aportesDet bg-success text-white small" style="display:none"><?=formatNumberDec($suma_total_a_solidario_13000)?></th>
	                    <th class="aportesDet bg-success text-white small" style="display:none"><?=formatNumberDec($suma_total_a_solidario_25000)?></th>
	                    <th class="aportesDet bg-success text-white small" style="display:none"><?=formatNumberDec($suma_total_a_solidario_35000)?></th>
	                    <th class="small"><?=formatNumberDec($suma_total_rc_iva)?></th>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_anticipos);?></th>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_dotaciones);?></th>
	                    <th class="bg-success text-white small"><?=formatNumberDec($sum_total_o_descuentos);?></th>
	                    <?php  
		                	for ($i=0; $i <count($arrayDescuentos) ; $i++) { ?>
		                    <th class="DescuentosOtros small" style="display:none;background:#d98880;"><small><?=formatNumberDec($arrayDescuentos_aux[$i]);?></small></th><?php
		                	}
		                ?>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_m_descuentos);?></th>
	                    <th class="bg-primary text-white small"><?=formatNumberDec($sum_total_l_pagable);?></th>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_seguro_sal)?></th>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_riesgo_profesional)?></th>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_provivienda)?></th>
	                    <th class="text-center small"><?=formatNumberDec($sum_total_patronal)?></th>
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