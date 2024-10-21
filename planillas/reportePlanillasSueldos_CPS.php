<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

require_once '../layouts/bodylogin2.php';
  $dbh = new Conexion();

  $cod_planilla = $_GET["codigo_planilla"];//
  $cod_gestion = $_GET["cod_gestion"];//
  $cod_mes = $_GET["cod_mes"];//
  $tipo = $_GET["tipo"];//

  // if($tipo==1){//CNS
  //   $sqlTipo=" and p.cod_cajasalud=1";
  // }elseif($tipo==2){//CPS
  //   $sqlTipo=" and p.cod_cajasalud=2";
  // }

  $mes=strtoupper(nombreMes($cod_mes));
  $gestion=nameGestion($cod_gestion);



$porcentaje_aport_afp=obtenerValorConfiguracionPlanillas(12);
$porcentaje_aport_sol=obtenerValorConfiguracionPlanillas(15);
//html del reporte
?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="50" height="40" src="../assets/img/favicon.png">
            </div>
            <h4 class="card-title text-center">
              PLANILLA DE SUELDOS Y SALARIOS<br>CORRESPONDIENTE AL MES DE <?=$mes?> DE <?=$gestion?><br>EXPRESADO EN BOLIVIANOS
            </h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
<table class="table table-bordered table-condensed" width="100%" align="center"  id="tablePaginatorFixedPlanillaSueldo_otros">
    <thead>
      <tr class="table-title small bold text-center">                  
        <td >N°</td> 
        <td>DES</td> 
        <!-- <td>TURNO</td>  -->
        <td>CI</td>
        <td>EXT</td>
        <td>PATERNO</td>
        <td>MATERNO</td>
        <td>NOMBRE</td>
        <td>FECHA NAC.</td>
        <td>CARGO</td>
        <td>FEC INGRESO</td>
        <td>DIAS TRAB</td>
        <td>HABER BASICO</td>
        <td>HABER BASICO DIAS TRAB</td>
        <td>BONO ANT</td>
        <td>COMS VENTAS</td>
        <td>FALLO DE CAJA</td>
        <td>NOCHES</td>
        <td>DOMINGOS</td>
        <td>FERIADOS</td>
        <td>DIAS ORD</td>
        <td>REINTEGRO</td>
        <td>MOVILIDAD</td>
        <td>REFRIGERIO</td>
        <td>TOTAL GANADO</td>
        <td>AP VEJEZ 10%</td>
        <td>RIESGO PROF 1.71%</td>
        <td>COM AFP 0.5%</td>
        <td>APO SOL 0.5%</td>
        <td>APO SOL 13</td>
        <td>RC IVA</td>
        <td>ANTICIPOS</td>
        <td>PRESTAMOS</td>
        <td>INVENTARIOS</td>
        <td>VENCIDOS</td>
        <td>ATRASOS</td>
        <td>APO SIND</td>
        <td>FALTANTE CAJA</td>
        <td>OTROS DESC</td>
        <td>TOTAL DESCTO</td>
        <td>LIQUIDO PAG</td>
        <td>FIRMA</td>
      </tr>                                  
    </thead>
    <tbody>
      <?php 
      $index=1;
      

      $sql = "SELECT a.nombre,p.identificacion,( select pd.abreviatura from personal_departamentos pd where pd.codigo=p.cod_lugar_emision)as lugar_emision,p.fecha_nacimiento,p.paterno,p.materno,p.primer_nombre,
      (select c.nombre from cargos c where c.codigo=p.cod_cargo)as cargo,p.ing_planilla,ppm.dias_trabajados,ppm.haber_basico,ppm.haber_basico_pactado,ppm.bono_antiguedad,ppm.total_ganado,ppm.afp_1,ppm.afp_2,pp.a_solidario_13000,pp.a_solidario_25000,pp.a_solidario_35000,pp.anticipo,pp.rc_iva,ppm.liquido_pagable,pp.riesgo_profesional,ppm.monto_descuentos,pp.seguro_de_salud,
      (select bm.monto from bonos_personal_mes bm where bm.cod_bono=11 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bnoches,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=12 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bdomingos,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=13 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bferiados,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=14 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bmovilidad,(select sum(bm.monto) from bonos_personal_mes bm where bm.cod_bono in (15,16) and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as brefrig,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=17 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as breintegro,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=18 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bventas,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=19 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bfallo,(select bm.monto from bonos_personal_mes bm where bm.cod_bono=20 and bm.cod_estadoreferencial=1 and bm.cod_personal=p.codigo and bm.cod_gestion=$cod_gestion and bm.cod_mes=$cod_mes) as bextras,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=1 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as dprestamos,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=2 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as dinventarios,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=3 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as dvencidos,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=4 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as datrasos,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=5 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as dfaltante,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=6 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as dotros,(select dm.monto from descuentos_personal_mes dm where dm.cod_descuento=100 and dm.cod_estadoreferencial=1 and dm.cod_personal=p.codigo and dm.cod_gestion=$cod_gestion and dm.cod_mes=$cod_mes)as daportesind

      from personal p
      join planillas_personal_mes ppm on ppm.cod_personalcargo=p.codigo
      join planillas_personal_mes_patronal pp on pp.cod_planilla=ppm.cod_planilla and pp.cod_personal_cargo=ppm.cod_personalcargo
      join areas a on p.cod_area=a.codigo
      where  ppm.cod_planilla=$cod_planilla
      order by p.cod_unidadorganizacional,a.nombre,p.paterno";
      // echo $sql."<br><br>";

        $stmtPersonal = $dbh->prepare($sql);
        $stmtPersonal->execute(); 
        $stmtPersonal->bindColumn('nombre', $nombre);
        $stmtPersonal->bindColumn('identificacion', $identificacion);
        $stmtPersonal->bindColumn('lugar_emision', $lugar_emision);
        $stmtPersonal->bindColumn('fecha_nacimiento', $fecha_nacimiento);
        $stmtPersonal->bindColumn('paterno', $paterno);
        $stmtPersonal->bindColumn('materno', $materno);
        $stmtPersonal->bindColumn('primer_nombre', $primer_nombre);
        $stmtPersonal->bindColumn('ing_planilla', $ing_planilla);
        $stmtPersonal->bindColumn('cargo', $cargo);
        $stmtPersonal->bindColumn('dias_trabajados', $dias_trabajados);
        $stmtPersonal->bindColumn('haber_basico_pactado', $haber_basico_pactado);
        $stmtPersonal->bindColumn('haber_basico', $haber_basico);
        $stmtPersonal->bindColumn('bono_antiguedad', $bono_antiguedad);
        $stmtPersonal->bindColumn('total_ganado', $total_ganado);
        $stmtPersonal->bindColumn('anticipo', $anticipo);
        $stmtPersonal->bindColumn('liquido_pagable', $liquido_pagable);
        $stmtPersonal->bindColumn('afp_1', $afp_1);
        $stmtPersonal->bindColumn('afp_2', $afp_2);
        $stmtPersonal->bindColumn('a_solidario_13000', $a_solidario_13000);
        $stmtPersonal->bindColumn('a_solidario_25000', $a_solidario_25000);
        $stmtPersonal->bindColumn('a_solidario_35000', $a_solidario_35000);
        $stmtPersonal->bindColumn('riesgo_profesional', $riesgo_profesional);

        $stmtPersonal->bindColumn('seguro_de_salud', $seguro_de_salud);

        
        $stmtPersonal->bindColumn('rc_iva', $rc_iva);
        $stmtPersonal->bindColumn('bventas', $bventas);
        $stmtPersonal->bindColumn('bfallo', $bfallo);
        $stmtPersonal->bindColumn('bnoches', $bnoches);
        $stmtPersonal->bindColumn('bdomingos', $bdomingos);
        $stmtPersonal->bindColumn('bferiados', $bferiados);
        $stmtPersonal->bindColumn('bextras', $bextras);
        $stmtPersonal->bindColumn('breintegro', $breintegro);
        $stmtPersonal->bindColumn('bmovilidad', $bmovilidad);
        $stmtPersonal->bindColumn('brefrig', $brefrig);
        $stmtPersonal->bindColumn('dprestamos', $dprestamos);
        $stmtPersonal->bindColumn('dinventarios', $dinventarios);
        $stmtPersonal->bindColumn('dvencidos', $dvencidos);
        $stmtPersonal->bindColumn('datrasos', $datrasos);

        $stmtPersonal->bindColumn('dfaltante', $dfaltante);
        $stmtPersonal->bindColumn('dotros', $dotros);
        $stmtPersonal->bindColumn('daportesind', $daportesind);


        // $stmtPersonal->bindColumn('descuentos_otros', $descuentos_otros);
        // $stmtPersonal->bindColumn('bonos_otros', $bonos_otros);
        $stmtPersonal->bindColumn('monto_descuentos', $monto_descuentos);
      
        while ($row = $stmtPersonal->fetch()) 
        {  
          // $aporte_caja=$afp_1+$afp_2;

          $aporte_caja=$seguro_de_salud/100;//10%
          $aporte_sol13=$a_solidario_13000+$a_solidario_25000+$a_solidario_35000;
          $ComAFP=$total_ganado*$porcentaje_aport_afp/100;//0.5%
          $aposol=$total_ganado*$porcentaje_aport_sol/100;//0.5%

          $aportes=$aporte_caja+$aporte_sol13+$riesgo_profesional+$ComAFP+$aposol;

          // $total_descuentos=$aportes+$anticipo+$dprestamos+ $dinventarios+ $dvencidos+ $datrasos+ $dfaltante+ $dotros+ $daportesind;
          
          $total_descuentos=$monto_descuentos;

          
          ?>
              <tr>
                <td class="small"><small><?=$index?></small></td> 
                <td class="small"><small><?=$nombre?></small></td> 
                
                <td class="small"><small><?=$identificacion?></small></td>
                <td class="small"><small><?=$lugar_emision?></small></td>
                <td class="small"><small><?=$paterno?></small></td>
                <td class="small"><small><?=$materno?></small></td>
                <td class="small"><small><?=$primer_nombre?></small></td>
                <td class="small"><small><?=$fecha_nacimiento?></small></td>
                <td class="small"><small><?=$cargo?></small></td>
                <td class="small"><small><?=$ing_planilla?></small></td>
                <td class="small"><small><?=formatNumberDec($dias_trabajados)?></small></td>
                <td class="small"><small><?=formatNumberDec($haber_basico_pactado)?></small></td>
                <td class="small"><small><?=formatNumberDec($haber_basico)?></small></td>
                <td class="small"><small><?=formatNumberDec($bono_antiguedad)?></small></td>
                <td class="small"><small><?=formatNumberDec($bventas)?></small></td>
                <td class="small"><small><?=formatNumberDec($bfallo)?></small></td>
                <td class="small"><small><?=formatNumberDec($bnoches)?></small></td>
                <td class="small"><small><?=formatNumberDec($bdomingos)?></small></td>
                <td class="small"><small><?=formatNumberDec($bferiados)?></small></td>
                <td class="small"><small><?=formatNumberDec($bextras)?></small></td>
                <td class="small"><small><?=formatNumberDec($breintegro)?></small></td>
                <td class="small"><small><?=formatNumberDec($bmovilidad)?></small></td>
                <td class="small"><small><?=formatNumberDec($brefrig)?></small></td>
                <td class="small"><small><?=formatNumberDec($total_ganado)?></small></td>
                <td class="small"><small><?=formatNumberDec($aporte_caja)?></small></td>
                <td class="small"><small><?=formatNumberDec($riesgo_profesional)?></small></td>
                <td class="small"><small><?=formatNumberDec($ComAFP)?></small></td>
                <td class="small"><small><?=formatNumberDec($aposol)?></small></td>
                <td class="small"><small><?=formatNumberDec($aporte_sol13)?></small></td>
                <td class="small"><small><?=formatNumberDec($rc_iva)?></small></td>
                <td class="small"><small><?=formatNumberDec($anticipo)?></small></td>
                <td class="small"><small><?=formatNumberDec($dprestamos)?></small></td>
                <td class="small"><small><?=formatNumberDec($dinventarios)?></small></td>
                <td class="small"><small><?=formatNumberDec($dvencidos)?></small></td>
                <td class="small"><small><?=formatNumberDec($datrasos)?></small></td>
                <td class="small"><small><?=formatNumberDec($daportesind)?></small></td>
                <td class="small"><small><?=formatNumberDec($dfaltante)?></small></td>
                <td class="small"><small><?=formatNumberDec($dotros)?></small></td>
                <td class="small"><small><?=formatNumberDec($total_descuentos)?></small></td>
                <td class="small"><small><?=formatNumberDec($liquido_pagable)?></small></td>
                <td class="small"><small></small></td>
                </tr><?php 
                $index+=1;
            }
             ?>
    </tbody>
  </table>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>



