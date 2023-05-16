<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../layouts/bodylogin2.php';

  $dbh = new Conexion();

  $cod_planilla = $_GET["codigo_planilla"];//
  $cod_gestion = $_GET["cod_gestion"];//
  // $cod_mes = $_GET["cod_mes"];//
  // $mes=strtoupper(nombreMes($cod_mes));
  $gestion=nameGestion($cod_gestion);
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
             <!--<div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div>-->
             <h4 class="card-title text-center">PLANILLA RETROACTIVO <?=$gestion?> - FORMATO OVT</h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-condensed" width="100%" align="center"  id="tablePaginatorFixedPlanillaSueldo_otros">
                <thead>
                  <tr class="table-title small bold text-center">                  
                    <td>Nro</td> 
                    <td>Tipo de documento de identidad</td> 
                    <td>Número de documento de identidad</td>
                    <td>Lugar de expedición</td>
                    <td>Fecha de nacimiento</td>
                    <td>Apellido Paterno</td>
                    <td>Apellido Materno</td>
                    <td>Nombres</td>
                    <td>País de nacionalidad</td>
                    <td>Sexo</td>
                    <td>Jubilado</td>
                    <td>¿Aporta a la AFP?</td>
                    <td>¿Persona con discapacidad?</td>
                    <td>Tutor de persona con discapacidad</td>
                    <td>Fecha de ingreso</td>
                    <td>Fecha de retiro</td>
                    <td>Motivo retiro</td>
                    <td>Caja de salud</td>
                    <td>AFP a la que aporta</td>
                    <td>NUA/CUA</td>
                    <td>Sucursal o ubicación adicional</td>
                    <td>Clasificacion laboral</td>
                    <td>Cargo</td>
                    <td>Modalidad de contrato</td>
                    <td>Tipo contrato</td>
                    <td>Horas pagadas</td>
                    <td>Haber Básico</td>
                    <td>Salario Mínimo Nacional con incremento (si correspondiere)</td>
                    <td>Monto Retroactivo mes de Enero</td>
                    <td>Monto Retroactivo mes de Febrero</td>
                    <td>Monto Retroactivo mes de Marzo</td>
                    <td>Monto Retroactivo mes de Abril</td>
                    <td>Monto Retroactivo mes de Mayo</td>
                    <td>Monto Retroactivo mes de Junio</td>
                    <td>Pago total de retroactivo de otros bonos y pagos</td>
                    <td>RC-IVA</td>
                    <td>Aporte AFP</td>
                    <td>Otros descuentos</td>
                  </tr>                                  
                </thead>
                <tbody>
                  <?php 
                  $index=1;
                  $Aporta_AFP=1;
                  $Fecha_retiro='';
                  $Motivo_retiro='';
                  $Sucursal_adicional=1;
                  $Modalidad_contrato=1;
                  $Tipo_contrato=1;
                  $Horas_pagadas=8;
                  $sql="SELECT prd.correlativo_planilla,(select tip.abreviatura from tipos_identificacion_personal tip where tip.codigo=p.cod_tipo_identificacion) as tipo_identificacion,p.identificacion,(select pd.abreviatura from personal_departamentos pd where pd.codigo=p.cod_lugar_emision)as lugar_emision,p.fecha_nacimiento,p.paterno,p.materno,p.primer_nombre,(select n.nombre from personal_pais n where n.codigo=p.cod_nacionalidad)as nacionalidad,(select g.abreviatura from tipos_genero g where g.codigo=p.cod_genero) as genero,p.jubilado,(select pd.tipo_persona_discapacitado from personal_discapacitado pd where pd.codigo=p.codigo limit 1)as tipo_personal_discapacidad,prd.ing_planilla,prd.retiro_planilla,1 as cod_cajasalud,p.cod_tipoafp,p.nua_cua_asignado,(select c.nombre from cargos c where c.codigo=p.cod_cargo) as cargo,p.cod_unidadorganizacional,

                    prd.haber_basico_anterior,prd.haber_basico_nuevo,prd.bono_antiguedad_anterior,prd.bono_antiguedad_nuevo,prd.retroactivo_enero,prd.retroactivo_febrero,prd.retroactivo_marzo,prd.retroactivo_abril,prd.antiguedad_enero,prd.antiguedad_febrero,prd.antiguedad_marzo,prd.antiguedad_abril,prd.total_ganado,prd.ap_vejez,prd.riesgo_prof,prd.com_afp,prd.aporte_sol,prd.total_descuentos,prd.liquido_pagable
                      from  personal p join planillas_retroactivos_detalle prd on p.codigo=prd.cod_personal join areas a on prd.cod_area=a.codigo
                      where prd.cod_planilla=$cod_planilla
                      order by correlativo_planilla";
                      // echo $sql."<br><br>";
                    $stmtPersonal = $dbh->prepare($sql);
                    $stmtPersonal->execute(); 
                    $stmtPersonal->bindColumn('tipo_identificacion', $tipo_identificacion);
                    $stmtPersonal->bindColumn('identificacion', $identificacion);
                    $stmtPersonal->bindColumn('lugar_emision', $lugar_emision);
                    $stmtPersonal->bindColumn('fecha_nacimiento', $fecha_nacimiento);
                    $stmtPersonal->bindColumn('paterno', $paterno);
                    $stmtPersonal->bindColumn('materno', $materno);
                    $stmtPersonal->bindColumn('primer_nombre', $primer_nombre);
                    $stmtPersonal->bindColumn('nacionalidad', $nacionalidad);
                    $stmtPersonal->bindColumn('genero', $genero);
                    $stmtPersonal->bindColumn('jubilado', $jubilado);
                    $stmtPersonal->bindColumn('tipo_personal_discapacidad', $tipo_personal_discapacidad);
                    $stmtPersonal->bindColumn('ing_planilla', $ing_planilla);
                    $stmtPersonal->bindColumn('retiro_planilla', $retiro_planilla);
                    
                    $stmtPersonal->bindColumn('cod_cajasalud', $cod_cajasalud);
                    $stmtPersonal->bindColumn('cod_tipoafp', $cod_tipoafp);
                    $stmtPersonal->bindColumn('nua_cua_asignado', $nua_cua_asignado);
                    $stmtPersonal->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
                    $stmtPersonal->bindColumn('cargo', $cargo);
                    $stmtPersonal->bindColumn('haber_basico_anterior', $haber_basico_anterior);
                    $stmtPersonal->bindColumn('haber_basico_nuevo', $haber_basico_nuevo);
                    $stmtPersonal->bindColumn('retroactivo_enero', $retroactivo_enero);
                    $stmtPersonal->bindColumn('retroactivo_febrero', $retroactivo_febrero);
                    $stmtPersonal->bindColumn('retroactivo_marzo', $retroactivo_marzo);
                    $stmtPersonal->bindColumn('retroactivo_abril', $retroactivo_abril);
                    $stmtPersonal->bindColumn('antiguedad_enero', $antiguedad_enero);
                    $stmtPersonal->bindColumn('antiguedad_febrero', $antiguedad_febrero);
                    $stmtPersonal->bindColumn('antiguedad_marzo', $antiguedad_marzo);
                    $stmtPersonal->bindColumn('antiguedad_abril', $antiguedad_abril);
                    $stmtPersonal->bindColumn('ap_vejez', $ap_vejez);
                    $stmtPersonal->bindColumn('riesgo_prof', $riesgo_prof);
                    $stmtPersonal->bindColumn('com_afp', $com_afp);
                    $stmtPersonal->bindColumn('aporte_sol', $aporte_sol);
                    $stmtPersonal->bindColumn('total_descuentos', $total_descuentos);
                    
                    while ($row = $stmtPersonal->fetch()) 
                    {
                      $Persona_discapacidad=0;
                      $Tutordiscapacidad=0;
                      switch ($tipo_personal_discapacidad) {
                        case 1:
                          $Persona_discapacidad=1;
                          $Tutordiscapacidad=0;
                        break;
                        case 2:
                          $Persona_discapacidad=0;
                          $Tutordiscapacidad=1;
                        break;
                      }
                      switch ($cod_tipoafp) {
                        case 1:
                            $cod_tipoafp=2;//el codigo que se envía al ministerio es volteado.
                        break;
                        case 2:
                          $cod_tipoafp=1;//solo para estos dos
                        break;
                      }
                      
                      // if($cod_tipoafp==1){//el codigo que se envía al ministerio es volteado.
                      //   $cod_tipoafp=2;
                      // }else{
                      //   $cod_tipoafp=1;
                      // }

                      if($cod_unidadorganizacional==1)
                        $Clasificacion_laboral=4;
                      else
                        $Clasificacion_laboral=5;
                      // $aporte_AFP=$ap_vejez+$riesgo_prof+$com_afp+$aporte_sol;
                      $aporte_AFP=$total_descuentos;
                      
                      if($haber_basico_anterior==$haber_basico_nuevo){
                        $salario_incremento=0;  
                      }else{
                        $salario_incremento=$haber_basico_nuevo;
                      }
                      if($retiro_planilla>"2022-01-01"){//fecha VALIDA
                        $Motivo_retiro=1;
                      }
                      ?>
                          <tr>
                              <td class="text-center small"><?=$index?></td> 
                                <td><?=$tipo_identificacion?></td> 
                                <td><?=$identificacion?></td>
                                <td><?=$lugar_emision?></td>
                                <td><?=strftime('%d/%m/%Y',strtotime($fecha_nacimiento))?></td>
                                <td class="text-left"><?=$paterno?></td>
                                <td class="text-left"><?=$materno?></td>
                                <td class="text-left"><?=$primer_nombre?></td>
                                <td><?=$nacionalidad?></td>
                                <td><?=$genero?></td>
                                <td><?=$jubilado?></td>
                                <td><?=$Aporta_AFP?></td>
                                <td><?=$Persona_discapacidad?></td>
                                <td><?=$Tutordiscapacidad?></td>
                                <td><?=strftime('%d/%m/%Y',strtotime($ing_planilla))?></td>
                                <td><?=strftime('%d/%m/%Y',strtotime($retiro_planilla))?></td>
                                <td><?=$Motivo_retiro?></td>
                                <td><?=$cod_cajasalud?></td>
                                <td><?=$cod_tipoafp?></td>
                                <td><?=$nua_cua_asignado?></td>
                                <td><?=$Sucursal_adicional?></td>
                                <td><?=$Clasificacion_laboral?></td>
                                <td class="text-left"><?=$cargo?></td>
                                <td><?=$Modalidad_contrato?></td>
                                <td><?=$Tipo_contrato?></td>
                                <td><?=$Horas_pagadas?></td>
                                <td><?=$haber_basico_anterior?></td>
                                <td><?=$salario_incremento?></td>
                                <td><?=round($retroactivo_enero+$antiguedad_enero,2)?></td>
                                <td><?=round($retroactivo_febrero+$antiguedad_febrero,2)?></td>
                                <td><?=round($retroactivo_marzo+$antiguedad_marzo,2)?></td>
                                <td><?=round($retroactivo_abril+$antiguedad_abril,2)?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><?=round($aporte_AFP,2) ?></td>
                                <td></td>
                            </tr> <?php 
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

<?php
  $dbh=null;
  $stmtPersonal=null;
?>

