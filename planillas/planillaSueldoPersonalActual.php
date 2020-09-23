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
  $codigo_mes = $_GET["cod_mes"];//

  $sqlGestion="SELECT nombre from gestiones where codigo=$cod_gestion";
  $stmtGestion=$dbh->prepare($sqlGestion);
  $stmtGestion->execute();
  $resultGestion=$stmtGestion->fetch();
  $nombre_gestion=$resultGestion['nombre'];


  $stmtUO = $dbh->prepare("SELECT cod_uo,(SELECT uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo) as nombre_uo
   from personal_area_distribucion where cod_estadoreferencial=1
  GROUP BY cod_uo");
  $stmtUO->execute();
  $stmtUO->bindColumn('cod_uo', $cod_uo_x);
  $stmtUO->bindColumn('nombre_uo', $nombre_uo_x);
                         

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
            <h6 class="card-title">
              Gestion: <?=$nombre_gestion; ?><br>
              Mes: <?=$codigo_mes; ?><br>
              Codigo Planilla: <?=$codigo_planilla; ?><br>
                     
            </h6>             
          </div>
          <div class="card-body">
            <div class="table-responsive">                  
              <table class="table table-bordered table-condensed table-hover" id="tablePaginator">
                <thead>
                  <tr class="bg-dark text-white">                  
                    <th><small>#</small></th>
                    <th><small>UO</small></th>                    
                    <th><small>Area</small></th>
                    <th><small>Paterno</small></th>
                    <th><small>Materno</small></th>
                    <th><small>Nombres</small></th>                    
                    <th><small>Doc. Id</small></th>                    
                    <th><small>Grado Académico</small></th>
                    <th><small>Haber Básico</small></th>
                    <th><small>Días Trab</small></th>                                        
                    <th><small>Bono Antiguedad</small></th>
                    <th class="bg-success text-white"><button id="botonBonos" style="border:none;" class="bg-success text-white small">Otros Bonos</button> </th>
                    <?php
                      $sqlBonos = "SELECT cod_bono,(select b.nombre from bonos b where b.codigo=cod_bono) as nombre_bono
                              from bonos_personal_mes 
                              where  cod_gestion=$cod_gestion and cod_mes=$codigo_mes and cod_estadoreferencial=1 GROUP BY (cod_bono)
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
                    <th><small>Total Ganado</small></th>

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
                              where  cod_gestion=$cod_gestion and cod_mes=$codigo_mes and cod_estadoreferencial=1 GROUP BY (cod_descuento)
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

                  while ($row = $stmtUO->fetch(PDO::FETCH_BOUND)) {
                    if($cod_uo_x>0){

                      $stmtArea = $dbh->prepare("SELECT cod_area,(SELECT a.abreviatura from areas a where a.codigo=cod_area) as nombre_area from personal_area_distribucion
                      where cod_estadoreferencial=1 and cod_uo=$cod_uo_x
                      GROUP BY cod_area");
                      $stmtArea->execute();
                      $stmtArea->bindColumn('cod_area', $cod_area_x);
                      $stmtArea->bindColumn('nombre_area', $nombre_area_x);
                      while ($row = $stmtArea->fetch(PDO::FETCH_BOUND)) {

                        $sql = "SELECT ppm.cod_planilla,ppm.cod_personalcargo,ppm.cod_gradoacademico,ppm.dias_trabajados,ppm.horas_pagadas,ppm.haber_basico,
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
                          where ppm.cod_personalcargo=pad.cod_personal and cod_planilla=$codigo_planilla and pad.cod_uo=$cod_uo_x and pad.cod_estadoreferencial=1 and pad.cod_area=$cod_area_x";

                        $stmtPersonal = $dbh->prepare($sql);
                        $stmtPersonal->execute();
                        
                        $stmtPersonal->bindColumn('cod_planilla', $cod_planilla);
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


                              $sum_total_basico+=$haber_basico;
                              $sum_total_b_antiguedad+=$bono_antiguedad;                          
                              $sum_total_m_bonos+=$monto_bonos;
                              $sum_total_t_ganado+=$total_ganado;                          
                              $sum_total_atrasos+=$atrasos;
                              $sum_total_anticipos+=$anticipo;                        
                              $sum_total_m_descuentos+=$monto_descuentos;
                              $sum_total_dotaciones+=$dotaciones;
                              
                              $sum_total_l_pagable+=$liquido_pagable;
                              $sum_total_a_patronal+=$total_a_patronal;
                            ?>
                            <tr>                                                        
                              <td class="text-center small"><?=$index;?></td>
                              <td class="text-left small"><?=$nombre_uo_x;?></td>
                              <td class="text-left small"><?=$nombre_area_x;?></td>
                              <td class="text-left small"><?=$paterno;?></td>
                              <td class="text-left small"><?=$materno;?></td>
                              <td class="text-left small"><?=$nombrePersonal;?></td>
                              <td class="text-center small"><?=$doc_id;?>-<?=$lug_emision?><?=$lug_emision_otro?></td>                  
                              <td class="text-left small"><?=$grado_academico;?></td>                    
                              <td class="text-center small"><?=formatNumberDec($haber_basico);?></td>
                              <td class="text-center small"><?=$dias_trabajados;?></td>                
                              <td class="text-center small"><?=formatNumberDec($bono_antiguedad);?></td>
                              <?php                    
                              if(count($arrayBonos)>0)
                              {
                                $sqlTotalOtroBonos = "SELECT SUM(monto) as suma_bono
                                        from bonos_personal_mes 
                                        where  cod_personal=$cod_personalcargo and cod_gestion=$cod_gestion and cod_mes=$codigo_mes and cod_estadoreferencial=1";
                                $stmtbonoOtros = $dbh->prepare($sqlTotalOtroBonos);
                                $stmtbonoOtros->execute();
                                $resultbonoOtros=$stmtbonoOtros->fetch();
                                $sumaBono_otros=$resultbonoOtros['suma_bono'];

                                $sum_total_o_bonos+=$sumaBono_otros;
                          

                                if($sumaBono_otros==null){ $sumaBono_otros=0;}
                                ?> 
                                <td class="text-center small"><?=formatNumberDec($sumaBono_otros);?></td>
                                <?php
                                set_time_limit(300);
                                for ($j=0; $j <count($arrayBonos);$j++){ 
                                    $cod_bono_aux=$arrayBonos[$j];                          
                                    $sqlBonosOtrs = "SELECT cod_bono,monto
                                          from bonos_personal_mes 
                                          where  cod_personal=$cod_personalcargo and cod_gestion=$cod_gestion and cod_mes=$codigo_mes and  cod_bono=$cod_bono_aux and cod_estadoreferencial=1";
                                    $stmtBonosOtrs = $dbh->prepare($sqlBonosOtrs);
                                    $stmtBonosOtrs->execute();
                                    $resultBonosOtros=$stmtBonosOtrs->fetch();
                                    $cod_bonosX=$resultBonosOtros['cod_bono'];
                                    $montoX=$resultBonosOtros['monto'];

                                    if($cod_bonosX==$cod_bono_aux){ ?>
                                      <td  class="bonosDet small" style="display:none"><?=formatNumberDec($montoX);?></td>  
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
                                $monto_aportes = $afp_1+$afp_2+$a_solidario_13000+$a_solidario_25000+$a_solidario_35000+$rc_iva;

                                $sum_total_m_aportes+=$monto_aportes;                    
                                    
                              ?>  
                              <td class="small"><?=formatNumberDec($monto_bonos);?></td>
                              <td class="small"><?=formatNumberDec($total_ganado);?></td>
                              <td class="small"><?=formatNumberDec($monto_aportes);?></td> 
                              <td class="aportesDet small" style="display:none"><?=formatNumberDec($afp_1);?></td>
                              <td class="aportesDet small" style="display:none"><?=formatNumberDec($afp_2);?></td>
                              <td class="aportesDet small" style="display:none"><?=formatNumberDec($a_solidario_13000);?></td>
                              <td class="aportesDet small" style="display:none"><?=formatNumberDec($a_solidario_25000);?></td>
                              <td class="aportesDet small" style="display:none"><?=formatNumberDec($a_solidario_35000);?></td>
                              <td class="aportesDet small" style="display:none"><?=formatNumberDec($rc_iva);?></td>

                              <td class="small"><?=formatNumberDec($atrasos);?></td>
                              <td class="small"><?=formatNumberDec($anticipo);?></td>
                              <td class="small"><?=formatNumberDec($dotaciones);?></td>
                              
                              <?php
                              if($swDescuentoOtro)
                              {
                                $sqlTotalOtroDescuentos = "SELECT SUM(monto) as suma_descuentos
                                        from descuentos_personal_mes 
                                        where  cod_personal=$cod_personalcargo and cod_gestion=$cod_gestion and cod_mes=$codigo_mes and cod_estadoreferencial=1";
                                $stmtDescuentosOtros = $dbh->prepare($sqlTotalOtroDescuentos);
                                $stmtDescuentosOtros->execute();
                                $resultDescuentosOtros=$stmtDescuentosOtros->fetch();
                                $sumaDescuentos_otros=$resultDescuentosOtros['suma_descuentos'];

                                $sum_total_o_descuentos+=$sumaDescuentos_otros;

                                if($sumaDescuentos_otros==null){ $sumaDescuentos_otros=0;}
                                ?> 
                                <td class="small"><?=formatNumberDec($sumaDescuentos_otros);?></td>
                                <?php                      

                                  for ($j=0; $j <count($arrayDescuentos); $j++) { 
                                    $cod_descuento_aux=$arrayDescuentos[$j];                          
                                    $sqlDescuentos = "SELECT cod_descuento,monto
                                          from descuentos_personal_mes 
                                          where  cod_personal=$cod_personalcargo and cod_gestion=$cod_gestion and cod_mes=$codigo_mes and  cod_descuento=$cod_descuento_aux and cod_estadoreferencial=1";
                                    $stmtDescuentos = $dbh->prepare($sqlDescuentos);
                                    $stmtDescuentos->execute();
                                    $resultDescOtros=$stmtDescuentos->fetch();
                                    $cod_descuentosX=$resultDescOtros['cod_descuento'];
                                    $montoX=$resultDescOtros['monto'];
                                    if($cod_descuentosX==$cod_descuento_aux){ ?>
                                      <td  class="DescuentosOtros small" style="display:none"><?=formatNumberDec($montoX);?></td>  
                                    <?php                            
                                    }else{ $montoAux=0; ?>                                                          
                                      <td  class="DescuentosOtros small" style="display:none"><?=formatNumberDec($montoAux);?></td>
                                    <?php                            
                                    }
                                  }  
                                

                                  $monto_descuentosX=$monto_descuentos+$sumaDescuentos_otros;                      
                              }else{
                                $sumaDescuentos_otros=0;
                                ?>
                                <td class="small"><?=formatNumberDec($sumaDescuentos_otros);?></td>
                                <?php
                                $monto_descuentosX=$monto_descuentos+$sumaDescuentos_otros;
                              }
                              ?>
                                                    
                              <td class="text-center small"><?=formatNumberDec($monto_descuentosX);?></td>                                                          
                              <td class="bg-primary text-white small"><?=formatNumberDec($liquido_pagable);?></td>                                  
                              <td  class="text-center small"><?=formatNumberDec($seguro_de_salud);?></td>
                              <td class="text-center small"><?=formatNumberDec($riesgo_profesional);?></td>
                              <td class="text-center small"><?=formatNumberDec($provivienda);?></td>
                              <td class="text-center small"><?=formatNumberDec($a_patronal_sol);?></td>
                              <td class="text-center small"><?=formatNumberDec($total_a_patronal);?></td>
                            </tr> 
                          <?php 
                            $index+=1;
                        }

                      }



                      
                    }
                  }
                  ?>                      
                </tbody>
                <tfoot>
                  <tr class="bg-dark text-white">                  
                    <th colspan="8" class="text-center small">Total</th>
                    
                    <th class="text-center small"><?=$sum_total_basico?></th>
                    <th class="text-center small">-</th>                                        
                    <th class="text-center small"><?=$sum_total_b_antiguedad;?></th>
                    <th class="bg-success text-white small"><?=$sum_total_o_bonos;?> </th>
                    <?php
                      $sqlBonos = "SELECT cod_bono
                              from bonos_personal_mes 
                              where  cod_gestion=$cod_gestion and cod_mes=$codigo_mes and cod_estadoreferencial=1 GROUP BY (cod_bono)";
                      $stmtBonos = $dbh->prepare($sqlBonos);
                      $stmtBonos->execute();                      
                      $stmtBonos->bindColumn('cod_bono',$cod_bono);                      
                      while ($row = $stmtBonos->fetch()) 
                      { ?>
                        <th class="bonosDet bg-success text-white small" style="display:none">-</th>                      
                        <?php                        
                      }
                    ?>
                    <th class="text-center small"><?=$sum_total_m_bonos;?></th>                            
                    <th class="text-center small"><?=$sum_total_t_ganado;?></th>

                    <th class="bg-success text-white small"><?=$sum_total_m_aportes;?></th>
                    <th class="aportesDet bg-success text-white small" style="display:none">AFP.Fut</th>
                    <th class="aportesDet bg-success text-white" style="display:none">AFP.Prev</th>
                    <th class="aportesDet bg-success text-white" style="display:none">A.Solidario(13)</th>
                    <th class="aportesDet bg-success text-white" style="display:none">A.Solidario(25)</th>
                    <th class="aportesDet bg-success text-white" style="display:none">A.Solidario(35)</th>
                    <th class="aportesDet bg-success text-white" style="display:none">RC-IVA</th>

                    
                    <th class="text-center small"><?=$sum_total_atrasos;?></th>
                    <th class="text-center small"><?=$sum_total_anticipos;?></th>
                    <th class="text-center small"><?=$sum_total_dotaciones;?></th>
                    <th class="bg-success text-white"><?=$sum_total_o_descuentos;?></th>
                    <?php  
                      $swDescuentoOtro=false;                  
                      $sqlDescuento = "SELECT cod_descuento
                              from descuentos_personal_mes 
                              where  cod_gestion=$cod_gestion and cod_mes=$codigo_mes and cod_estadoreferencial=1 GROUP BY (cod_descuento)";
                      $stmtDescuento = $dbh->prepare($sqlDescuento);
                      $stmtDescuento->execute();                      
                      $stmtDescuento->bindColumn('cod_descuento',$cod_descuento);                      
                      while ($row = $stmtDescuento->fetch()) 
                      { ?>
                        <th class="DescuentosOtros bg-success text-white" style="display:none">-</th>
                        <?php
                      }
                    ?>
                    <th class="text-center small"><?=$sum_total_m_descuentos;?></th>                                        
                    <th class="bg-primary text-white"><?=$sum_total_l_pagable;?></th>                    
                    <th >-</th>
                    <th >-</th>
                    <th >-</th>
                    <th >-</th>
                    <th class="text-center small"><?=$sum_total_a_patronal;?></th>
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


