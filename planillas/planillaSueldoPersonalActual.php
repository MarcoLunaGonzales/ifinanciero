<?php //ESTADO FINALIZADO

require_once __DIR__.'/../conexion.php';

require_once __DIR__.'/../functions.php';
require_once __DIR__.'/../functionsGeneral.php';
require_once '../layouts/bodylogin2.php';
$dbh = new Conexion();
$mes_actual=date('F');
$anio_actual=date('Y');
$fecha_actual=date('Y-m-d');

$codigo_planilla = $_GET["codigo_planilla"];//


$sql = "SELECT *,
        (SELECT ga.nombre from personal_grado_academico ga where ga.codigo=cod_gradoacademico) as grado_academico,
        (select p.primer_nombre from personal p where p.codigo=cod_personalcargo) as personal,
        (select pa.paterno from personal pa where pa.codigo=cod_personalcargo) as paterno,
        (select pa.materno from personal pa where pa.codigo=cod_personalcargo) as materno,
        (select p3.identificacion from personal p3 where p3.codigo=cod_personalcargo) as doc_id,
        (select (select pd.abreviatura from personal_departamentos pd where pd.codigo=p3.cod_lugar_emision)
                 from personal p3 where p3.codigo=cod_personalcargo) as lug_emision,
      (select p4.lugar_emision_otro from personal p4 where p4.codigo=cod_personalcargo) as lug_emision_otro
        from planillas_personal_mes where cod_planilla=$codigo_planilla";

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
$stmtPersonal->bindColumn('cod_gradoacademico', $cod_gradoacademico);
$stmtPersonal->bindColumn('grado_academico', $grado_academico);
$stmtPersonal->bindColumn('dias_trabajados', $dias_trabajados);
$stmtPersonal->bindColumn('horas_pagadas', $horas_pagadas);
$stmtPersonal->bindColumn('haber_basico', $haber_basico);
$stmtPersonal->bindColumn('bono_antiguedad', $bono_antiguedad);
$stmtPersonal->bindColumn('horas_extra', $horas_extra);
$stmtPersonal->bindColumn('comisiones', $comisiones);
$stmtPersonal->bindColumn('monto_bonos', $monto_bonos);
$stmtPersonal->bindColumn('total_ganado', $total_ganado);
$stmtPersonal->bindColumn('monto_descuentos', $monto_descuentos);
$stmtPersonal->bindColumn('otros_descuentos', $otros_descuentos);
$stmtPersonal->bindColumn('total_descuentos', $total_descuentos);
$stmtPersonal->bindColumn('liquido_pagable', $liquido_pagable);
$stmtPersonal->bindColumn('afp_1', $afp_1);
$stmtPersonal->bindColumn('afp_2', $afp_2);

?>


<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-icon">            
            <h4 class="card-title"> 
              <img  class="card-img-top"  src="../marca.png" style="widtd:100%; max-width:250px;">
                Planilla De Sueldo
            </h4>                  
            <h6 class="card-title">
              Gestion: <?=$anio_actual; ?><br>
              Mes: <?=$mes_actual; ?><br>
              Codigo Planilla: <?=$codigo_planilla; ?><br>
            </h6>             
          </div>
          <div class="card-body">
            <div class="table-responsive">                  
              <table class="table table-bordered table-condensed" id="tablePaginatorFixedPlanillaSueldo">
                <thead>
                  <tr class="bg-dark text-white">
                    
                    <th>Codigo</th>
                    <th>Paterno</th>
                    <th>Materno</th>
                    <th>Nombres</th>                    
                    <th>Doc. Id</th>

                    
                    <th>Grado Académico</th>
                    <th>Haber Básico</th>
                    <th>Días Trab</th>                  
                    <th>Bono Antig</th>                    
                    <th>Monto Bonos</th> 
                    <th>Total Ganado</th>

                    <th>Afp Fut</th>
                    <th>Afp Prev</th>
                    <th>Apor Solid(13000)</th>
                    <th>Apor Solid(25000)</th>
                    <th>Apor Solid(35000)</th>
                    <th>RC-IVA</th>
                    <th>Atrasos</th>
                    <th>Anticipos</th>
                    <th>Total Descu</th>
                    <th>Liqu Pagable</th>                    
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
                      while ($row = $stmtPersonal->fetch()) 
                      {  
                        $sql = "SELECT *                              
                                from planillas_personal_mes_patronal 
                                where cod_planilla=$cod_planilla and cod_personal_cargo=$cod_personalcargo";
                          $stmtPersonalPatronal = $dbh->prepare($sql);
                          $stmtPersonalPatronal->execute();
                          $result=$stmtPersonalPatronal->fetch();
                          $codigo_ppm_patronal=$result['codigo'];
                          $cod_planilla_p=$result['cod_planilla'];
                          $cod_personal_cargo_p=$result['cod_personal_cargo'];
                          $a_solidario_13000=$result['a_solidario_13000'];
                          $a_solidario_25000=$result['a_solidario_25000'];
                          $a_solidario_35000=$result['a_solidario_35000'];
                          $rc_iva=$result['rc_iva'];

                          $atrasos=$result['atrasos'];
                          $anticipo=$result['anticipo'];
                          $seguro_de_salud=$result['seguro_de_salud'];
                          $riesgo_profesional=$result['riesgo_profesional'];
                          $provivienda=$result['provivienda'];
                          $a_patronal_sol=$result['a_patronal_sol'];
                          $total_a_patronal=$result['total_a_patronal'];                      
                        ?>
                  <tr>                                                        
                    <td><?=$cod_personalcargo;?></td>
                    <td><?=$paterno;?></td>                         
                    <td><?=$materno;?></td>
                    <td><?=$nombrePersonal;?></td>
                    <td><?=$doc_id;?>-<?=$lug_emision?><?=$lug_emision_otro?></td>                  
                    <td><?=$grado_academico;?></td>
                    
                    <td><?=$haber_basico;?></td>
                    <td><?=$dias_trabajados;?></td>                    
                    <td><?=$bono_antiguedad;?></td>                  
                    <td><?=$monto_bonos;?></td>
                    <td><?=$total_ganado;?></td>

                    <td><?=$afp_1;?></td>
                    <td><?=$afp_2;?></td>
                    <td><?=$a_solidario_13000?></td>
                    <td><?=$a_solidario_25000?></td>
                    <td><?=$a_solidario_35000?></td>
                    <td><?=$rc_iva?></td>
                    <td><?=$atrasos?></td>
                    <td><?=$anticipo?></td>
                    <td><?=$total_descuentos;?></td>
                    <td><?=$liquido_pagable;?></td>                                  
                    <td><?=$seguro_de_salud;?></td>
                    <td><?=$riesgo_profesional;?></td>
                    <td><?=$provivienda;?></td>
                    <td><?=$a_patronal_sol;?></td>
                    <td><?=$total_a_patronal;?></td>
                  </tr> 
                  <?php 
                    $index+=1;}?>                      
                </tbody>
              </table>                                
            </div>                 
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>

