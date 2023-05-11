<meta charset="utf-8">
<?php
require_once 'conexion.php';
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$globalUnidad=$_SESSION["globalUnidad"];

$anio=date('Y');

$gestion_porocesada_incremento=obtenerValorConfiguracionPlanillas(29);
// $porcentaje_smn_config=obtenerValorConfiguracionPlanillas(30);//porcentaje de INCREMENTO SALARIAL AL SALARIO MINIMO NACIONAL
// $porcentaje_hb_config=obtenerValorConfiguracionPlanillas(31);//porcentaje de  INCREMENTO SALARIAL AL HABER BASICO

// echo "<br><br>".$gestion_porocesada_incremento."--".$anio;
if($gestion_porocesada_incremento<>$anio){//todavia no se procesó
?>

<div class="content">
  <div class="container-fluid">
   <h2 style="color:#2c3e50;"><b>INCREMENTO SALARIAL <?=$anio?><hr style="background:#dc7633;height:10px;" align="left" width="490px"></b></h2>
   <div id="contenedor_main_incremento_salarial">
      <div class="row">
         <div class="col-md-4">
            <div class="card text-white mx-auto" style="background-color:white; width:25rem;">
               <div class="card-body">
                  <table>
                     <tr>
                        <td width="25%"><center><i class="material-icons" style="color: #a9dfbf;font-size:60px;" >public</i></center></td>
                        <td>
                           <div class="row">
                              <h5 class="card-title" style="color:#7dcea0;"><b>INCREMEMENTO GLOBAL</b></h5>   
                           </div>
                           <div class="row">
                              <div class="col-sm-6">
                                 <div class="form-group has-primary">
                                  <label class="control-label" for="inputSuccess">% SMN</label>
                                  <input type="text" class="form-control" name="incremento_smn_g" id="incremento_smn_g" >
                                </div>
                              </div>
                              <div class="col-sm-6">
                                    <div class="form-group has-primary">
                                     <label class="control-label" for="inputSuccess">% HB</label>
                                     <input type="text" class="form-control" name="incremento_hb_g" id="incremento_hb_g" >
                                   </div>
                              </div>
                           </div>
                        </td>
                     </tr>
                  </table>
                  <center><button  class="btn btn-sm" onclick="boton_incremento_salarial_main(1)">Ingresar</button></center>
               </div>
            </div>
         </div> 
         <div class="col-md-4">
            <div class="card text-white mx-auto" style="background-color:white; width: 25rem;">
               <div class="card-body">
                  <table>
                     <tr>
                        <td width="25%"><center><i class="material-icons" style="color: #f2d7d5;font-size:60px;" >groups</i></center></td>
                        <td>
                           <div class="row">
                              <h5 class="card-title" style="color:#e6b0aa;"><b>INCREMEMENTO POR CARGO</b></h5>
                           </div>
                           <div class="row">
                              <div class="col-sm-6">
                                 <div class="form-group has-primary">
                                  <label class="control-label" for="inputSuccess">% SMN</label>
                                  <input type="text" class="form-control" name="incremento_smn_c" id="incremento_smn_c">
                                </div>
                              </div>
                              <div class="col-sm-6">
                                 <div class="form-group has-primary">
                                  <label class="control-label" for="inputSuccess">% HB</label>
                                 <input type="text" class="form-control" name="incremento_hb_c" id="incremento_hb_c">
                                </div>
                              </div>
                           </div>
                        </td>
                     </tr>
                  </table>
                  <center><button type="button" class="btn btn-sm"  onclick="boton_incremento_salarial_main(2)">Ingresar</button></center>
               </div>
            </div>
         </div> 
         <div class="col-md-4">
            <div class="card text-white mx-auto" style="background-color:white; width: 25rem;">
               <div class="card-body">
                  <table>
                     <tr>
                        <td width="25%"><center><i class="material-icons" style="color: #fad7a0;font-size:60px;" >reduce_capacity</i></center></td>
                        <td>
                           <div class="row">
                              <h5 class="card-title" style="color:#f8c471;"><b>INCREMEMENTO POR PERSONA</b></h5>
                           </div>
                           <div class="row">
                              <div class="col-sm-6">
                                    <div class="form-group has-primary">
                                     <label class="control-label" for="inputSuccess">% SMN</label>
                                     <input type="text" class="form-control" name="incremento_smn_p" id="incremento_smn_p">
                                   </div>
                              </div>
                              <div class="col-sm-6">
                                    <div class="form-group has-primary">
                                     <label class="control-label" for="inputSuccess">% HB</label>
                                     <input type="text" class="form-control" name="incremento_hb_p" id="incremento_hb_p">
                                   </div>
                              </div>
                           </div>
                        </td>
                     </tr>
                  </table>
                  <center><button  class="btn btn-sm" onclick="boton_incremento_salarial_main(3)">Ingresar</button></center>
               </div>
            </div>
         </div> 
      </div>
      <div class="row">
         <div class="col-md-12">
            <div class="card" >
               <div class="card-body">
                  <div class="table-responsive">
                    <div class="">
                      <table class="table table-condensed" id="tablePaginatorHead">
                        <thead>
                          <tr>
                            <th class="text-center"></th>
                            <th class="text-center">Codigo</th>
                            <th class="text-center">Suc/Area</th>
                            <th class="text-center">Nombre Personal</th>
                            <th class="text-center">Haber Basico Anterior</th>
                            <th class="text-center">Haber Basico Nuevo</th>
                            <th class="text-center">Porcentaje Inc</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                  </div>
               </div>
               </div>
            </div> 
         </div>
      </div>
   </div>  
   </div>
</div>

<div class="modal fade" id="modalListCargos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #45b39d;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel" style="background: #45b39d; color:white;"><b>Selección de Cargos</b></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12">
            <div class="form-group">
               <input type="hidden" name="contador_cargos" id="contador_cargos" value="0">
               <div class="table-responsive">
               <table class="table table-condensed" id="tabla_cargos">
                  <thead>
                    <tr>
                      <th class="text-center">Codigo</th>
                      <th class="text-center">Nombre Cargo</th>
                      <th class="text-center">
                           <div class="togglebutton">
                              <label>
                                 <input type="checkbox"  id="cargos_seleccionados_all" name="cargos_seleccionados_all" onchange="activar_input_incremento_salarial_cargos(-100)" checked>
                                 <span class="toggle"></span>
                              </label>
                           </div>
                        </th>
                    </tr>
                  </thead>
                  <tbody>
                     <?php
                     $stmtCargos = $dbh->prepare("SELECT codigo,nombre from cargos where cod_estadoreferencial=1 order by nombre");
                     $stmtCargos->execute();
                     $stmtCargos->bindColumn('codigo', $codigo_cargo);
                     $stmtCargos->bindColumn('nombre', $nombre_cargo);
                     $i=0;
                     while ($rowCargos = $stmtCargos->fetch(PDO::FETCH_ASSOC))
                     { ?>
                        <tr>
                           <td><?=$i+1?></td>
                           <td class="text-left"><?=$nombre_cargo?> ( <?=$codigo_cargo?> )</td>
                           <td class="td-actions text-right">
                              <input type="hidden" id="cargos_activados<?=$i?>" name="cargos_activados<?=$i?>"  value="1">
                              <input type="hidden" id="cod_cargo<?=$i?>" name="cod_cargo<?=$i?>"  value="<?=$codigo_cargo?>">
                              <div class="togglebutton">
                               <label>
                                 <input type="checkbox"  id="cargos_seleccionados<?=$i?>" name="cargos_seleccionados<?=$i?> " onchange="activar_input_incremento_salarial_cargos(<?=$i?>)" checked>
                                 <span class="toggle"></span>
                               </label>
                              </div>

                           </td>
                        </tr>
                     <?php 
                     $i++;
                     }

                     echo "<script type='text/javascript'>
                        $('#contador_cargos').val($i);
                     </script>";
                     ?>
                  </tbody>
               </table>
               </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn" style="background:#45b39d;" id="registrarCargos_modal" name="registrarCargos_modal" data-dismiss="modal">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">  Cerrar </button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function(){
    $('#registrarCargos_modal').click(function(){
      var contador_cargos=document.getElementById("contador_cargos").value;
      var string_cargos="";
      var contador_activados=0;
      for (var ix = 0; ix<contador_cargos; ix++) {
         var cargos_activados=$('#cargos_activados'+ix).val();
         if(cargos_activados==1){//activado
            contador_activados=contador_activados+1;
            var cod_cargo=$('#cod_cargo'+ix).val();
            string_cargos=string_cargos+cod_cargo+",";
         }
      }
      if(contador_activados<=0){
        Swal.fire("Informativo!", "Por favor, seleccione cargos", "warning");
      }else{   
       //alert(string_cargos);     
        guardar_cargos_seleccionados_incremento(string_cargos);
      }      
    });    
  });
</script>

<?php

}else{ // ya se procesó el incremento
 echo "<script type='text/javascript'>
  swal('El incremento salarial de la gestion ".$anio.", YA FUE PROCESADA. ¿Deseas editar?', '', 'warning')
          .then((value) => {
          location.href='index.php?opcion=incremento_salarial_edit';
      });
</script>";
}

?>

