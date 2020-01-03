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


  $sql = "SELECT *,
        (select (select uo.abreviatura from unidades_organizacionales uo where uo.codigo=pad.cod_uo)as nombre_uo from personal_area_distribucion pad where pad.cod_personal=cod_personalcargo) as cod_uo,
        (SELECT ga.nombre from personal_grado_academico ga where ga.codigo=cod_gradoacademico) as grado_academico,
        (select p.primer_nombre from personal p where p.codigo=cod_personalcargo) as personal,
        (select pa.paterno from personal pa where pa.codigo=cod_personalcargo) as paterno,
        (select pa.materno from personal pa where pa.codigo=cod_personalcargo) as materno,
        (select p3.identificacion from personal p3 where p3.codigo=cod_personalcargo) as doc_id,
        (select (select pd.abreviatura from personal_departamentos pd where pd.codigo=p3.cod_lugar_emision)
                 from personal p3 where p3.codigo=cod_personalcargo) as lug_emision,
      (select p4.lugar_emision_otro from personal p4 where p4.codigo=cod_personalcargo) as lug_emision_otro
        from planillas_personal_mes where cod_planilla=$codigo_planilla order by cod_uo asc";

  $stmtPersonal = $dbh->prepare($sql);
  $stmtPersonal->execute();
  $stmtPersonal->bindColumn('codigo', $codigo);
  $stmtPersonal->bindColumn('cod_planilla', $cod_planilla);
  $stmtPersonal->bindColumn('cod_personalcargo', $cod_personalcargo);
  $stmtPersonal->bindColumn('personal', $nombrePersonal);
  $stmtPersonal->bindColumn('paterno', $paterno);
  $stmtPersonal->bindColumn('materno', $materno);
  $stmtPersonal->bindColumn('doc_id', $doc_id);
  $stmtPersonal->bindColumn('lug_emision', $lug_emision);
  $stmtPersonal->bindColumn('lug_emision_otro', $lug_emision_otro);

  $stmtPersonal->bindColumn('cod_uo', $cod_uo);
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
                    <th>#</th>
                    <th>UO</th>                    
                    <th>Paterno</th>
                    <th>Materno</th>
                    <th>Nombres</th>                    
                    <th>Doc. Id</th>                    
                    <th>Grado Académico</th>
                    <th>Haber Básico</th>
                    <th>Días Trab</th>                                        
                    <th>Bono Antiguedad</th>
                    <th class="bg-success text-white"><button id="botonBonos" style="border:none;" class="bg-success text-white">Otros Bonos</button> </th>
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
                        <th class="bonosDet bg-success text-white" style="display:none"><?=$nombre_bono;?></th>                      
                        <?php
                        $arrayBonos[] = $cod_bono;
                      }
                    ?>
                    <th>Monto Bonos</th>                            
                    <th>Total Ganado</th>

                    <th class="bg-success text-white"><button id="botonAportes" style="border:none;" class="bg-success text-white">Monto Aportes</button></th>
                    <th class="aportesDet bg-success text-white" style="display:none">AFP.Fut</th>
                    <th class="aportesDet bg-success text-white" style="display:none">AFP.Prev</th>
                    <th class="aportesDet bg-success text-white" style="display:none">A.Solidario(13)</th>
                    <th class="aportesDet bg-success text-white" style="display:none">A.Solidario(25)</th>
                    <th class="aportesDet bg-success text-white" style="display:none">A.Solidario(35)</th>
                    <th class="aportesDet bg-success text-white" style="display:none">RC-IVA</th>

                    
                    <th>Atrasos</th>
                    <th>Anticipos</th>
                    <th>Dotaciones</th>
                    <th class="bg-success text-white"><button id="botonOtrosDescuentos" style="border:none;" class="bg-success text-white">Otros Descuentos</button> </th>
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
                        <th class="DescuentosOtros bg-success text-white" style="display:none"><?=$nombre_descuentos;?></th>
                        <?php
                        $arrayDescuentos[] = $cod_descuento;
                        $swDescuentoOtro=true;
                      }
                    ?>
                    <th>Monto Descuentos</button></th>                                        
                    <th class="bg-primary text-white">Liqu Pagable</th>                    
                    <th>Seguro De Salud</th>
                    <th>Riesgo Profesional</th>
                    <th>Provivienda</th>
                    <th>Apo Patronal Sol</th>
                    <th>Total Apo Patronal</th>
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
                    <td><?=$index;?></td>
                    <td><?=$cod_uo;?></td>
                    <td class="text-left"><?=$paterno;?></td>
                    <td class="text-left"><?=$materno;?></td>
                    <td class="text-left"><?=$nombrePersonal;?></td>
                    <td><?=$doc_id;?>-<?=$lug_emision?><?=$lug_emision_otro?></td>                  
                    <td class="text-left"><?=$grado_academico;?></td>                    
                    <td><?=formatNumberDec($haber_basico);?></td>
                    <td><?=$dias_trabajados;?></td>                
                    <td><?=formatNumberDec($bono_antiguedad);?></td>
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
                      <td><?=formatNumberDec($sumaBono_otros);?></td>
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
                            <td  class="bonosDet" style="display:none"><?=formatNumberDec($montoX);?></td>  
                          <?php                            
                          }else{ $montoAux=0; ?>                                                          
                            <td  class="bonosDet" style="display:none"><?=formatNumberDec($montoAux);?></td>
                          <?php                            
                          }
                      }
                    }else{$sumabonos_otros=0;
                      ?>
                      <td><?=formatNumberDec($sumabonos_otros);?></td>
                      <?php
                    }                                          
                      $monto_aportes = $afp_1+$afp_2+$a_solidario_13000+$a_solidario_25000+$a_solidario_35000+$rc_iva;

                      $sum_total_m_aportes+=$monto_aportes;                    
                          
                    ?>  
                    <td><?=formatNumberDec($monto_bonos);?></td>
                    <td><?=formatNumberDec($total_ganado);?></td>
                    <td><?=formatNumberDec($monto_aportes);?></td> 
                    <td class="aportesDet" style="display:none"><?=formatNumberDec($afp_1);?></td>
                    <td class="aportesDet" style="display:none"><?=formatNumberDec($afp_2);?></td>
                    <td class="aportesDet" style="display:none"><?=formatNumberDec($a_solidario_13000);?></td>
                    <td class="aportesDet" style="display:none"><?=formatNumberDec($a_solidario_25000);?></td>
                    <td class="aportesDet" style="display:none"><?=formatNumberDec($a_solidario_35000);?></td>
                    <td class="aportesDet" style="display:none"><?=formatNumberDec($rc_iva);?></td>

                    <td><?=formatNumberDec($atrasos);?></td>
                    <td><?=formatNumberDec($anticipo);?></td>
                    <td><?=formatNumberDec($dotaciones);?></td>
                    
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
                      <td><?=formatNumberDec($sumaDescuentos_otros);?></td>
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
                            <td  class="DescuentosOtros" style="display:none"><?=formatNumberDec($montoX);?></td>  
                          <?php                            
                          }else{ $montoAux=0; ?>                                                          
                            <td  class="DescuentosOtros" style="display:none"><?=formatNumberDec($montoAux);?></td>
                          <?php                            
                          }
                        }  
                      

                        $monto_descuentosX=$monto_descuentos+$sumaDescuentos_otros;                      
                    }else{
                      $sumaDescuentos_otros=0;
                      ?>
                      <td><?=formatNumberDec($sumaDescuentos_otros);?></td>
                      <?php
                      $monto_descuentosX=$monto_descuentos+$sumaDescuentos_otros;
                    }
                    ?>
                                          
                    <td ><?=formatNumberDec($monto_descuentosX);?></td>                                                          
                    <td class="bg-primary text-white"><?=formatNumberDec($liquido_pagable);?></td>                                  
                    <td><?=formatNumberDec($seguro_de_salud);?></td>
                    <td><?=formatNumberDec($riesgo_profesional);?></td>
                    <td><?=formatNumberDec($provivienda);?></td>
                    <td><?=formatNumberDec($a_patronal_sol);?></td>
                    <td><?=formatNumberDec($total_a_patronal);?></td>
                  </tr> 
                  <?php 
                    $index+=1;}?>                      
                </tbody>
                <tfoot>
                    <tr class="bg-dark text-white">                  
                    <th colspan="7">Total</th>
                    
                    <th><?=$sum_total_basico?></th>
                    <th>-</th>                                        
                    <th><?=$sum_total_b_antiguedad;?></th>
                    <th class="bg-success text-white"><?=$sum_total_o_bonos;?> </th>
                    <?php
                      $sqlBonos = "SELECT cod_bono
                              from bonos_personal_mes 
                              where  cod_gestion=$cod_gestion and cod_mes=$codigo_mes and cod_estadoreferencial=1 GROUP BY (cod_bono)";
                      $stmtBonos = $dbh->prepare($sqlBonos);
                      $stmtBonos->execute();                      
                      $stmtBonos->bindColumn('cod_bono',$cod_bono);                      
                      while ($row = $stmtBonos->fetch()) 
                      { ?>
                        <th class="bonosDet bg-success text-white" style="display:none">-</th>                      
                        <?php                        
                      }
                    ?>
                    <th><?=$sum_total_m_bonos;?></th>                            
                    <th><?=$sum_total_t_ganado;?></th>

                    <th class="bg-success text-white"><?=$sum_total_m_aportes;?></th>
                    <th class="aportesDet bg-success text-white" style="display:none">AFP.Fut</th>
                    <th class="aportesDet bg-success text-white" style="display:none">AFP.Prev</th>
                    <th class="aportesDet bg-success text-white" style="display:none">A.Solidario(13)</th>
                    <th class="aportesDet bg-success text-white" style="display:none">A.Solidario(25)</th>
                    <th class="aportesDet bg-success text-white" style="display:none">A.Solidario(35)</th>
                    <th class="aportesDet bg-success text-white" style="display:none">RC-IVA</th>

                    
                    <th><?=$sum_total_atrasos;?></th>
                    <th><?=$sum_total_anticipos;?></th>
                    <th><?=$sum_total_dotaciones;?></th>
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
                    <th><?=$sum_total_m_descuentos;?></th>                                        
                    <th class="bg-primary text-white"><?=$sum_total_l_pagable;?></th>                    
                    <th>-</th>
                    <th>-</th>
                    <th>-</th>
                    <th>-</th>
                    <th><?=$sum_total_a_patronal;?></th>
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


