<meta charset="utf-8">
<?php
require_once 'conexion.php';
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalUnidad=$_SESSION["globalUnidad"];

$anio=date('Y');

// $gestion_porocesada_incremento=obtenerValorConfiguracionPlanillas(29);
$porcentaje_smn_config=obtenerValorConfiguracionPlanillas(32);//porcentaje de INCREMENTO SALARIAL AL SALARIO MINIMO NACIONAL
$porcentaje_hb_config=obtenerValorConfiguracionPlanillas(33);//porcentaje de  INCREMENTO SALARIAL AL HABER BASICO
$porcentaje_smn=$porcentaje_smn_config;
$porcentaje_hb=$porcentaje_hb_config;



$minimo_salarial_config=obtenerValorConfiguracionPlanillas(31);
$minimo_salarial_nuevo=$minimo_salarial_config+$minimo_salarial_config*$porcentaje_smn/100;

// echo  $minimo_salarial_nuevo;

// $cod_cargo_gerencia="2,3";
$anio=date('Y');
$fecha_inicio=$anio."-01-01";
$fecha_fin=$anio."-12-31";



// echo "<br><br>".$gestion_porocesada_incremento."--".$anio;
?>

<div class="content">
  <div class="container-fluid">
   <h2 style="color:#2c3e50;"><b>INCREMENTO SALARIAL <?=$anio?><hr style="background:#dc7633;height:10px;" align="left" width="490px"></b></h2>
   <div id="contenedor_main_incremento_salarial">

      <form id="form_incrementosalarialglobal" class="form-horizontal" action="incremento_salarial/save_incremento_porpersona.php" method="POST">
  <div class="row">
     <div class="col-md-4">
        <div class="card text-white mx-auto" style="background-color:white; width:25rem;">
         <input type="hidden" name="bandera_edit" id="bandera_edit" value="1">
           <div class="card-body">
              <table>
                 <tr>
                    <td width="25%"><center><i class="material-icons" style="color: #fad7a0;font-size:60px;" >reduce_capacity</i></center></td>
                    <td>
                       <div class="row">
                          <h5 class="card-title" style="color:#f8c471;"><b>INCREMEMENTO POR PERSONA</b></h5>
                       </div>
                       <div class="row">
                          <div class="col-sm-1">
                             <i class="material-icons" style="color:#d4e6f1;">label</i>
                          </div>
                          <div class="col-sm-4">
                            <div class="form-group has-primary">
                              <label class="control-label" for="inputSuccess">% SMN</label>
                              <input type="text" class="form-control" name="incremento_smn_g" id="incremento_smn_g"  style="background:white;font-size: 18px;color:#566573;" readonly="true" value="<?=$porcentaje_smn?>">
                              <input type="hidden" name="incremento_smn_monto" id="incremento_smn_monto" value="<?=$minimo_salarial_nuevo?>">
                              <input type="text" class="form-control" style="background:white;font-size: 18px;color:#dc7633;" readonly="true" value="<?=formatNumberDec($minimo_salarial_nuevo)?>">
                            </div>
                          </div>
                          <div class="col-sm-3">
                             <div class="form-group has-primary">
                              <label class="control-label" for="inputSuccess">% HB</label>
                              <input type="text" class="form-control" name="incremento_hb_g" id="incremento_hb_g"   style="background:white;font-size: 18px;color:#566573;" readonly="true" value="<?=$porcentaje_hb?>" >
                            </div>
                          </div>
                       </div>
                    </td>
                 </tr>
              </table>
           </div>
        </div>
     </div> 

  </div>
  <div class="row">
     <div class="col-md-12">
        <div class="card" >
           <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-condensed table-sm " id="tablePaginatorHead">
                  <thead>
                    <tr>
                      <th class="text-center"></th>
                      <th class="text-center" width="15%">Suc/Area</th>
                      <th class="text-center">Nombre Personal</th>
                      <th class="text-center">Cargo</th>
                      <th class="text-center">Haber Basico Anterior</th>
                      <th class="text-center">Haber Basico Nuevo</th>
                      <th class="text-center">Inc</th>
                      <th class="text-center">A/I</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td align="center" colspan="8" class="bg-dark text-white">PERSONAL ACTIVA</td>
                    </tr>
                    <?php
                    $index = 0;
                    //personal Activo
                    $query="SELECT p.codigo,p.cod_cargo,p.primer_nombre,p.paterno,p.materno,(select c.nombre from cargos c where c.codigo=p.cod_cargo)as cargo,a.nombre as area,p.haber_basico,p.haber_basico_anterior
                      from personal p join areas a on p.cod_area=a.codigo 
                      where p.cod_estadopersonal=1 and p.cod_estadoreferencial=1 
                      order by p.cod_unidadorganizacional,a.nombre,p.paterno";
                    $stmt = $dbh->prepare($query);
                    $stmt->execute();
                    $stmt->bindColumn('codigo', $codigo);
                    $stmt->bindColumn('cod_cargo', $cod_cargo);
                    $stmt->bindColumn('primer_nombre', $primer_nombre);
                    $stmt->bindColumn('paterno', $paterno);
                    $stmt->bindColumn('materno', $materno);
                    $stmt->bindColumn('cargo', $cargo);
                    $stmt->bindColumn('area', $area);
                    $stmt->bindColumn('haber_basico', $haber_basico);
                    $stmt->bindColumn('haber_basico_anterior', $haber_basico_ant);
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                     if($haber_basico_ant==null){
                        $haber_basico_ant=$haber_basico;
                     }
                        if($haber_basico_ant<=$minimo_salarial_config){//para salario por debajo o igual al salario minimo nacional
                          //$haber_basico_nuevo=floor($haber_basico_ant+$haber_basico_ant*$porcentaje_smn/100);
                           $haber_basico_nuevo=$minimo_salarial_nuevo;
                           $porcentaje_inc=$porcentaje_smn;
                           $estilo="style='background:#d4e6f1;'";
                        }else{
                          $haber_basico_nuevo=$haber_basico_ant+$haber_basico_ant*$porcentaje_hb/100;
                          $porcentaje_inc=$porcentaje_hb;
                          $estilo="";  
                        }
                      ?>
                      <tr <?=$estilo?>>
                        <td align="center"><?=$index+1;?>
                           <input type="hidden" name="codigo_persona[]" value="<?=$codigo?>"/>
                           <input type="hidden" name="hba[]" value="<?=$haber_basico_ant?>"/>
                           <input type="hidden" name="hbn[]" value="<?=$haber_basico_nuevo?>"/>
                           <input type="hidden" name="personal_seleccionado<?=$index?>" id="personal_seleccionado<?=$index?>"  value="1"/></td>
                        <td class="text-left"><?=$area;?></td>
                        <td class="text-left"><?=$primer_nombre;?> <?=$paterno;?> <?=$materno;?></td>
                        <td class="text-left"><?=$cargo;?></td>
                        <td class="text-right"><?=formatNumberDec($haber_basico_ant);?></td>
                        <td class="text-right"><?=formatNumberDec($haber_basico_nuevo);?></td>
                        <td class="text-right"><?=$porcentaje_inc;?> %</td>
                        <td class="text-right">
                           <div class="togglebutton">
                              <label>
                                 <input type="checkbox"  id="personal_seleccionado_x<?=$index?>" name="personal_seleccionado_x<?=$index?> " onchange="activar_input_incremento_salarial_personal(<?=$index?>)" checked >
                                 <span class="toggle"></span>
                              </label>
                           </div>
                        </td>
                      </tr>
                    <?php
                      $index++;
                    }
                    ?>
                    <tr>
                      <td align="center" colspan="8" class="bg-dark text-white">PERSONAL RETIRADO GESTIÓN <?=$anio?></td>
                    </tr>
                    <?php
                     //personal no activo
                     $queryRetirados="SELECT p.codigo,p.identificacion,p.primer_nombre,p.paterno,p.materno,(select c.nombre from cargos c where c.codigo=p.cod_cargo)as cargo,a.nombre as area,p.haber_basico,pr.fecha_retiro
                     from personal p join personal_retiros pr on p.codigo=pr.cod_personal join areas a on p.cod_area=a.codigo
                     where p.cod_estadopersonal=3 and pr.fecha_retiro BETWEEN '$fecha_inicio' and '$fecha_fin'
                     order by p.cod_unidadorganizacional,a.nombre";
                     $stmtRetirados = $dbh->prepare($queryRetirados);
                     $stmtRetirados->execute();
                     $stmtRetirados->bindColumn('codigo', $codigo_retirado);
                     // $stmtRetirados->bindColumn('cod_cargo', $cod_cargo);
                     $stmtRetirados->bindColumn('primer_nombre', $primer_nombre_retirado);
                     $stmtRetirados->bindColumn('paterno', $paterno_retirado);
                     $stmtRetirados->bindColumn('materno', $materno_retirado);
                     $stmtRetirados->bindColumn('cargo', $cargo_retirado);
                     $stmtRetirados->bindColumn('area', $area_retirado);
                     $stmtRetirados->bindColumn('haber_basico', $haber_basico_ant_retirado);
                     while ($rowRetirados = $stmtRetirados->fetch(PDO::FETCH_BOUND)) {
                        if($haber_basico_ant_retirado<=$minimo_salarial_config){//para salario por debajo o igual al salario minimo nacional
                          //$haber_basico_nuevo_retirado=floor($haber_basico_ant_retirado+$haber_basico_ant_retirado*$porcentaje_smn/100);
                           $haber_basico_nuevo_retirado=$minimo_salarial_nuevo;
                           $porcentaje_inc=$porcentaje_smn;
                           $estilo="style='background:#d4e6f1;'";
                        }else{
                          $haber_basico_nuevo_retirado=$haber_basico_ant_retirado+$haber_basico_ant_retirado*$porcentaje_hb/100;
                          $porcentaje_inc=$porcentaje_hb;
                          $estilo="";  
                        }
                        ?>
                        <tr <?=$estilo?>>
                           <td align="center"><?=$index+1;?>
                              <input type="hidden" name="codigo_persona[]" value="<?=$codigo_retirado?>"/>
                              <input type="hidden" name="hba[]" value="<?=$haber_basico_ant_retirado?>"/>
                              <input type="hidden" name="hbn[]" value="<?=$haber_basico_nuevo_retirado?>"/>
                              <input type="hidden" name="personal_seleccionado<?=$index?>" id="personal_seleccionado<?=$index?>" value="1"/></td>
                           <td class="text-left"><?=$area_retirado;?></td>
                           <td class="text-left"><?=$primer_nombre_retirado;?> <?=$paterno_retirado;?> <?=$materno_retirado;?></td>
                           <td class="text-left"><?=$cargo_retirado;?></td>
                           <td class="text-right"><?=formatNumberDec($haber_basico_ant_retirado);?></td>
                           <td class="text-right"><?=formatNumberDec($haber_basico_nuevo_retirado);?></td>
                           <td class="text-right"><?=$porcentaje_inc;?> %</td>
                            <td class="text-right">
                              <div class="togglebutton">
                                 <label>
                                    <input type="checkbox"  id="personal_seleccionado_x<?=$index?>" name="personal_seleccionado_x<?=$index?> " onchange="activar_input_incremento_salarial_personal(<?=$index?>)" checked>
                                    <span class="toggle"></span>
                                 </label>
                              </div>
                           </td>
                        </tr><?php
                        $index++;
                     }
                     ?>
                  </tbody>
                </table>    
              </div>
           </div>
        </div> 
     </div>
  </div>

<div class=" fixed-bottom">
<button type="submit" class="<?=$buttonCeleste;?>">Guardar</button>
</div>
</form>



   </div>  
</div>
</div>


