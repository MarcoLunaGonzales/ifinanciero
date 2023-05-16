<?php
// require_once '../conexion3.php';


require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsReportes.php';

  $dbh = new Conexion();

  $cod_planilla = $_GET["codigo_planilla"];//
  $cod_gestion = $_GET["cod_gestion"];//
  $cod_mes = $_GET["cod_mes"];//


  $gestion=nameGestion($cod_gestion);

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
             <h4 class="card-title text-center"><?=$cod_mes?> AFP FUTURO RETROACTIVO <?=$gestion?></h4>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-condensed" width="100%" align="center"  id="tablePaginatorFixedPlanillaSueldo_otros">
                <thead>
                  <tr class="table-title small bold text-center">                  
                    <td class="small">No</td> 
                    <td class="small">(13) TIPO</td> 
                    <td class="small">(14) No</td>
                    <td class="small">(14) EXTENSIÓN</td>
                    <td class="small">(15) NUA/CUA</td>
                    <td class="small">(A) 1er. APELLIDO (PATERNO)</td>
                    <td class="small">(B) 2do. APELLIDO (MATERNO)</td>
                    <td class="small">(C) APELLIDO CASADA</td>
                    <td class="small">(D) PRIMER NOMBRE</td>
                    <td class="small">(E) SEGUNDO NOMBRE</td>
                    <td class="small">(F) DEPARTAMENTO</td>
                    <td class="small">(17) NOVEDAD I/R/L/S</td>
                    <td class="small">(18) FECHA NOVEDAD dd/mm/aaaa</td>
                    <td class="small">(19) DIAS COTIZADOS</td>
                    <td class="small">(20) TIPO DE ASEGURADO (M/C/E)</td>
                    <td class="small">(21) TOTAL GANADO DEPENDIENTE < 65 AÑOS O ASEGURADO CON PENSION DEL SIP < 65 AÑOS QUE DECIDE APORTAR AL SIP</td>
                    <td class="small">(22) TOTAL GANADO DEPENDIENTE > 65 AÑOS O ASEGURADO CON PENSION DEL SIP > 65 AÑOS QUE DECIDE APORTAR AL SIP</td>
                    <td class="small">(23) TOTAL GANADO ASEGURADO CON PENSION DEL SIP < 65 AÑOS QUE DECIDE NO APORTAR AL SIP</td>
                    <td class="small">(24) TOTAL GANADO ASEGURADO CON PENSION AL SIP > 65 AÑOS QUE DECIDE NO APORTAR AL SIP</td>
                    <td class="small">(25) COTIZACION ADICIONAL</td>
                    <td class="small">(26) TOTAL GANADO FONDO DE VIVIENDA</td>
                    <td class="small">(27) TOTAL GANADO FONDO SOLIDARIO</td>
                    <td class="small">(28) TOTAL GANADO FONDO SOLIDARIO MINERO</td>
                  </tr>                                  
                </thead>
                <tbody>
                  <?php
                  $index=1;

                  $sql="SELECT prd.correlativo_planilla,(select tip.abreviatura from tipos_identificacion_personal tip where tip.codigo=p.cod_tipo_identificacion) as tipo_identificacion,p.identificacion,(select pd.abreviatura from personal_departamentos pd where pd.codigo=p.cod_lugar_emision)as lugar_emision,p.paterno,p.materno,p.apellido_casada,p.primer_nombre,prd.ing_planilla,prd.retiro_planilla,p.nua_cua_asignado,prd.retroactivo_enero,prd.retroactivo_febrero,prd.retroactivo_marzo,prd.retroactivo_abril,prd.antiguedad_enero,prd.antiguedad_febrero,prd.antiguedad_marzo,prd.antiguedad_abril,prd.dias_trabajados_enero,prd.dias_trabajados_febrero,prd.dias_trabajados_marzo,prd.dias_trabajados_abril
                    from  personal p join planillas_retroactivos_detalle prd on p.codigo=prd.cod_personal join areas a on prd.cod_area=a.codigo
                    where prd.cod_planilla=$cod_planilla and p.cod_tipoafp=1
                    order by correlativo_planilla";
                     //echo $sql."<br><br>";
                  $stmtPersonal = $dbh->prepare($sql);
                  $stmtPersonal->execute(); 
                  $stmtPersonal->bindColumn('tipo_identificacion', $tipo_identificacion);
                  $stmtPersonal->bindColumn('identificacion', $identificacion);
                  $stmtPersonal->bindColumn('paterno', $paterno);
                  $stmtPersonal->bindColumn('materno', $materno);
                  $stmtPersonal->bindColumn('apellido_casada', $apellido_casada);
                  $stmtPersonal->bindColumn('primer_nombre', $primer_nombre);
                  $stmtPersonal->bindColumn('nua_cua_asignado', $nua_cua_asignado);
                  $stmtPersonal->bindColumn('dias_trabajados_enero', $dias_trabajados_enero);
                  $stmtPersonal->bindColumn('dias_trabajados_febrero', $dias_trabajados_febrero);
                  $stmtPersonal->bindColumn('dias_trabajados_marzo', $dias_trabajados_marzo);
                  $stmtPersonal->bindColumn('dias_trabajados_abril', $dias_trabajados_abril);
                  $stmtPersonal->bindColumn('retroactivo_enero', $retroactivo_enero);
                  $stmtPersonal->bindColumn('retroactivo_febrero', $retroactivo_febrero);
                  $stmtPersonal->bindColumn('retroactivo_marzo', $retroactivo_marzo);
                  $stmtPersonal->bindColumn('retroactivo_abril', $retroactivo_abril);
                  $stmtPersonal->bindColumn('antiguedad_enero', $antiguedad_enero);
                  $stmtPersonal->bindColumn('antiguedad_febrero', $antiguedad_febrero);
                  $stmtPersonal->bindColumn('antiguedad_marzo', $antiguedad_marzo);
                  $stmtPersonal->bindColumn('antiguedad_abril', $antiguedad_abril);

                  $stmtPersonal->bindColumn('lugar_emision', $lugar_emision);
                  while ($row = $stmtPersonal->fetch()) 
                  {  
                    
                    $primer_nombre.=" ";
                    $array_nombre=explode(' ', $primer_nombre);
                    $segundo_nombre=$array_nombre[1];
                    if(isset($array_nombre[2])){
                      $segundo_nombre.="  ".$array_nombre[2];
                    }
                    if(isset($array_nombre[3])){
                      $segundo_nombre.="  ".$array_nombre[3];
                    }
                    if(isset($array_nombre[4])){
                      $segundo_nombre.="  ".$array_nombre[4];
                    }

                    switch ($cod_mes) {
                      case 1:
                        $dias_trabajados=$dias_trabajados_enero;
                        $total_ganado=$retroactivo_enero+$antiguedad_enero;
                        break;
                      case 2:
                        $dias_trabajados=$dias_trabajados_enero;
                        $total_ganado=$retroactivo_febrero+$antiguedad_febrero;
                        break;
                      case 3:
                        $dias_trabajados=$dias_trabajados_enero;
                        $total_ganado=$retroactivo_marzo+$antiguedad_marzo;
                        break;
                      case 4:
                        $dias_trabajados=$dias_trabajados_enero;
                        $total_ganado=$retroactivo_abril+$antiguedad_abril;
                        break;
                    }
                    
                    ?>
                    <tr>
                      <td class="text-center small"><?=$index?></td>
                      <td class="text-center small"><?=$tipo_identificacion?></td>
                      <td class="text-left small"><?=$identificacion?></td>
                      <td class="text-left small"><?=$lugar_emision?></td>
                      <td class="text-left small"><?=$nua_cua_asignado?></td>
                      <td class="text-left small"><?=$paterno?></td>
                      <td class="text-left small"><?=$materno?></td>
                      <td class="text-left small"><?=$apellido_casada?></td>
                      <td class="text-left small"><?=$array_nombre[0]?></td>
                      <td class="text-left small"><?=$segundo_nombre?></td>
                      <td class="text-left small"></td>
                      <td class="text-left small"></td>
                      <td class="text-left small"></td>
                      <td class="text-right small"><?=$dias_trabajados?></td>
                      <td class="text-left small"></td>
                      <td class="text-right small"><?=round($total_ganado,2)?></td>
                      <td class="text-left small"></td>
                      <td class="text-left small"></td>
                      <td class="text-left small"></td>
                      <td class="text-left small"></td>
                      <td class="text-right small"><?=round($total_ganado,2)?></td>
                      <td class="text-right small"><?=round($total_ganado,2)?></td>
                      <td class="text-left small"></td>
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

<?php

 // echo $html;

?>

