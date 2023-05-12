<?php //ESTADO FINALIZADO

session_start();
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../layouts/bodylogin2.php';

$dbh = new Conexion();

$globalAdmin=$_SESSION["globalAdmin"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$codGestionActiva=$_SESSION['globalGestion'];
$cod_mes=$_SESSION['globalMes'];
$nombre_mes=nombreMes($cod_mes);


$estado_planilla=0;
$sql="SELECT cod_estadoplanilla from planillas where cod_mes=$cod_mes and cod_gestion=$codGestionActiva";
// echo $sql; 
$stmtVerifPlani=$dbh->prepare($sql);
$stmtVerifPlani->execute();
while ($rowVerifPlani = $stmtVerifPlani->fetch(PDO::FETCH_ASSOC)) {
  $estado_planilla=$rowVerifPlani['cod_estadoplanilla'];
}
if($estado_planilla==0){ // registrar plaanilla mes 
  ?>
  <script type="text/javascript">
    Swal.fire({
        title: 'A ocurrido un error :(',
        text: "Registre la PLANILLA del mes en curso, Gracias.",
        type: 'warning',
        confirmButtonClass: 'btn btn-warning',
        confirmButtonText: 'Aceptar',
        buttonsStyling: false
        }).then((result) => {
          if (result.value) {
            window.close();
            return(false);
          } 
        });
   </script>
<?php
}else{
  if($estado_planilla==3){//planilla cerrada 
    ?>
    <script type="text/javascript">
      Swal.fire({
        title: 'LO SIENTO :("',
        text: "La Planilla No se encuentra disponible.",
        type: 'error',
        confirmButtonClass: 'btn btn-danger',
        confirmButtonText: 'Aceptar',
        buttonsStyling: false
        }).then((result) => {
          if (result.value) {
            window.close();
            return(false);
          } 
      });
     </script>
    <?php
  }else{ ?>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-icon">
            <div class="card-icon bg-blanco">
              <img class="" width="40" height="40" src="../assets/img/favicon.png">
            </div>
            <h3 class="card-title text-center"><b>Plantilla Sueldos<br>Mes : <?=$nombre_mes?> - <?=$nombreGestion?></b></h3>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="tablePaginatorHeaderFooter" class="table table-bordered table-condensed table-striped  table-sm table-secondary" style="width:100%">
                <thead>
                  <tr>
                    <th><small><b>Codigo</b></small></th>
                    <th><small><b>CI</b></small></th>
                    <th width="13%"><small><b>Apellidos y Nombres</b></small></th>
                    <th><small><b>Area</b></small></th>
                    <th><small><b>Dias Traba jados L_V</b></small></th>
                    <th><small><b>OTROS BONOS</b></small></th>
                    <th><small><b>OTROS</b></small></th>
                    <th><small><b>RC-IVA</b></small></th>
                    <th><small><b>ATRASOS</b></small></th>
                    <th><small><b>OTROS DESCUENTOS</b></small></th>
                    <th><small><b>ANTICIPOS</b></small></th>
                    <th><small><b></b></small></th>
                  </tr>                                  
                </thead>
                <tbody>
                  <?php
                  $index=0;
                   $sql="SELECT p.codigo,a.nombre as areas,p.identificacion,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as personal,p.turno,p.cod_unidadorganizacional,k.cod_gestion,k.cod_mes,k.faltas,k.faltas_sin_descuento,k.dias_vacacion,k.dias_trabajados,k.domingos_trabajados_normal,k.feriado_normal,k.noche_normal,k.domingo_reemplazo,k.feriado_reemplazo,k.ordianrio_reemplazo,k.hxdomingo_extras,k.hxferiado_extras,k.hxdnnormal_extras,k.reintegro,k.obs_reintegro
                  from personal_kardex_mes k join personal p on p.codigo=k.cod_personal join areas a on p.cod_area=a.codigo
                  where k.cod_gestion=$codGestionActiva and k.cod_mes=$cod_mes and k.cod_estadoreferencial=1 
                  order by p.cod_unidadorganizacional,a.nombre,p.turno,p.paterno";
                  // echo $sql;
                  $stmtDet = $dbh->prepare($sql);
                  $stmtDet->execute();
                  while ($row = $stmtDet->fetch(PDO::FETCH_ASSOC)) { 
                    $codigo=$row['codigo'];
                    $areas=$row['areas'];
                    $identificacion=$row['identificacion'];
                    $personal=$row['personal'];
                    $turno=$row['turno'];
                    $cod_unidadorganizacional=$row['cod_unidadorganizacional'];
                    $cod_gestion=$row['cod_gestion'];
                    $cod_mes=$row['cod_mes'];
                    $faltas=$row['faltas'];
                    $faltas_sin_descuento=$row['faltas_sin_descuento'];
                    $dias_vacacion=$row['dias_vacacion'];
                    $dias_trabajados=$row['dias_trabajados'];
                    $domingos_trabajados_normal=$row['domingos_trabajados_normal'];
                    $feriado_normal=$row['feriado_normal'];
                    $noche_normal=$row['noche_normal'];
                    $domingo_reemplazo=$row['domingo_reemplazo'];
                    $feriado_reemplazo=$row['feriado_reemplazo'];
                    $ordianrio_reemplazo=$row['ordianrio_reemplazo'];
                    $hxdomingo_extras=$row['hxdomingo_extras'];
                    $hxferiado_extras=$row['hxferiado_extras'];
                    $hxdnnormal_extras=$row['hxdnnormal_extras'];
                    $reintegro=$row['reintegro'];
                    $obs_reintegro=$row['obs_reintegro'];
                    //descuentos
                    $monto_anticipo=0;
                    $monto_prestamos=0;
                    $inventarios=0;
                    $vencidos=0;
                    $atrasos=0;
                    $faltante_caja=0;
                    $otros_descuentos=0;
                    $aporte_sindicato=0;
                    $datos_descuentos=obtenerDescuentosGestionActiva($codigo,$cod_gestion,$cod_mes);
                    if(isset($datos_descuentos[1000])){
                      $monto_anticipo=$datos_descuentos[1000];
                    }
                    if(isset($datos_descuentos[1])){
                      $monto_prestamos=$datos_descuentos[1];//prestamos
                    }
                    if(isset($datos_descuentos[2])){
                      $inventarios=$datos_descuentos[2];//inventarios
                    }
                    if(isset($datos_descuentos[3])){
                      $vencidos=$datos_descuentos[3];//vencidos;
                    }
                    if(isset($datos_descuentos[4])){
                      $atrasos=$datos_descuentos[4];//atrasos;
                    }
                    if(isset($datos_descuentos[5])){
                      $faltante_caja=$datos_descuentos[5];//faltante caja;
                    }
                    if(isset($datos_descuentos[6])){
                      $otros_descuentos=$datos_descuentos[6];//otros descuentos;
                    }
                    if(isset($datos_descuentos[100])){
                      $aporte_sindicato=$datos_descuentos[100];//aporte al sindicato;
                    }
                    if($cod_unidadorganizacional!=1){
                      if($turno==1){
                        $areas=$areas." TM";
                      }else{
                        $areas=$areas." TT";
                      }
                    }
                    $datos_modificacion=$codigo."/".$personal."/".$cod_gestion."/".$cod_mes."/".$faltas."/".$faltas_sin_descuento."/".$dias_vacacion."/".$dias_trabajados."/".$domingos_trabajados_normal."/".$feriado_normal."/".$noche_normal."/".$domingo_reemplazo."/".$feriado_reemplazo."/".$ordianrio_reemplazo."/".$hxdomingo_extras."/".$hxferiado_extras."/".$hxdnnormal_extras."/".$reintegro."/".$obs_reintegro."/".$monto_anticipo."/".$monto_prestamos."/".$inventarios."/".$vencidos."/".$atrasos."/".$faltante_caja."/".$otros_descuentos."/".$aporte_sindicato;
                    $index++;
                      ?>
                    <tr>
                      <td class="text text-left"><small><?=$identificacion?></small></td>
                      <td class="text text-left"><small><?=$personal?></small></td>
                      <td class="text text-left"><small><?=$areas?></small></td>
                      <td class="text text-left"><small><?=$faltas?></small></td>
                      <td class="text text-left"><small><?=$faltas_sin_descuento?></small></td>
                      <td class="text text-left"><small><?=$dias_vacacion?></small></td>
                      <td class="text text-left"><small><?=$dias_trabajados?></small></td>
                      <td class="text text-left"><small><?=$domingos_trabajados_normal?></small></td>
                      <td class="text text-left"><small><?=$feriado_normal?></small></td>
                      <td class="text text-left"><small><?=$noche_normal?></small></td>
                      <td class="text text-left"><small><?=$domingo_reemplazo?></small></td>
                      <td class="text text-left"><small><?=$feriado_reemplazo?></small></td>
                      <td class="text text-left"><small><?=$ordianrio_reemplazo?></small></td>
                      <td class="text text-left"><small><?=$hxdomingo_extras?></small></td>
                      <td class="text text-left"><small><?=$hxferiado_extras?></small></td>
                      <td class="text text-left"><small><?=$hxdnnormal_extras?></small></td>
                      <td class="text text-left"><small><?=$reintegro?></small></td>
                      <td class="text text-left"><small><?=$obs_reintegro?></small></td>
                      <td class="text text-left"><small><?=$monto_anticipo?></small></td>
                      <td class="text text-left"><small><?=$monto_prestamos?></small></td>
                      <td class="text text-left"><small><?=$inventarios?></small></td>
                      <td class="text text-left"><small><?=$vencidos?></small></td>
                      <td class="text text-left"><small><?=$atrasos?></small></td>
                      <td class="text text-left"><small><?=$faltante_caja?></small></td>
                      <td class="text text-left"><small><?=$otros_descuentos?></small></td>
                      <td class="text text-left"><small><?=$aporte_sindicato?></small></td>
                      <td><button title="Editar Ingreso" class="btn btn-success btn-sm" style="padding: 0;font-size:5px;width:18px;height:18px;" type="button" data-toggle="modal" data-target="#modalEditar" onclick="agregardatosModalEdicionPlantillaSueldos('<?=$datos_modificacion;?>')">
                          <i class="material-icons">edit</i>
                        </button></td>
                    </tr>
                    <?php   
                    
                  }?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>
<!-- modal editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel" style="background:#2e4053;color:white;"><b>Editar Plantilla</b></h4>
      </div>
      <div class="modal-body">        
        <input type="hidden" name="codigo_e" id="codigo_e">
        <input type="hidden" name="cod_gestion_e" id="cod_gestion_e">
        <input type="hidden" name="cod_mes_e" id="cod_mes_e">
        
        
        
        <div class="row">
          <label class="col-sm-2 col-form-label text-dark  text-right">Nombre : </label>
          <div class="col-sm-5">
            <div class="form-group" >
              <input type="text" class="form-control"  name="nombre_e" id="nombre_e" readonly="true" style="background-color:white;color:blue;size: 20px;">              
            </div>
          </div>
        </div>           
        <div class="row">
          <label class="col-sm-2 col-form-label text-dark  text-right">Días Trabajados : </label>
          <div class="col-sm-1">
            <div class="form-group" >              
              <input type="text" class="form-control" name="dias_trabajados_e" id="dias_trabajados_e" style="background-color:white">
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Faltas : </label>
          <div class="col-sm-1">
            <div class="form-group" >              
              <input type="number" class="form-control" name="faltas_e" id="faltas_e" style="background-color:white">
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Faltas sin Desc : </label>
          <div class="col-sm-1">
            <div class="form-group" >              
              <input type="text" class="form-control" name="faltas_sin_descuento_e" id="faltas_sin_descuento_e" >
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Vacaciones : </label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="dias_vacacion_e" id="dias_vacacion_e" style="background-color:white">              
            </div>
          </div>
        </div>
        <center><h5 style="color: black;background:#d5d8dc;"><b>Días Trabajados Normal</b></h5></center>        
        <div class="row">
          <label class="col-sm-2 col-form-label text-dark  text-right">Domingos : </label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="domingos_e" id="domingos_e" style="background-color:white">              
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Feriados : </label>
          <div class="col-sm-1">
            <div class="form-group" >              
              <input type="text" class="form-control" name="feriados_e" id="feriados_e" >
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Noches : </label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="noches_e" id="noches_e" style="background-color:white">              
            </div>
          </div>
        </div>
        <center><h5 style="color: black;background:#d5d8dc;"><b>Días Reemplazos</b></h5></center>
        <div class="row">
          <label class="col-sm-2 col-form-label text-dark  text-right">Domingo : </label>
          <div class="col-sm-1">
            <div class="form-group" >              
              <input type="text" class="form-control" name="domingo_reemp_e" id="domingo_reemp_e" style="background-color:white">
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Feriado : </label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="feriado_reemp_e" id="feriado_reemp_e" style="background-color:white">              
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Ordinario : </label>
          <div class="col-sm-1">
            <div class="form-group" >              
              <input type="text" class="form-control" name="ordinario_reemp_e" id="ordinario_reemp_e" >
            </div>
          </div>
        </div>
        <center><h5 style="color: black;background:#d5d8dc;"><b>Horas Extras</b></h5></center>
        
        
        <div class="row">
          <label class="col-sm-2 col-form-label text-dark  text-right">H x Domingo : </label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="hxdomingo_e" id="hxdomingo_e" style="background-color:white">              
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">H x Veriado : </label>
          <div class="col-sm-1">
            <div class="form-group" >              
              <input type="text" class="form-control" name="hxferiado_e" id="hxferiado_e" style="background-color:white">
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">H x Dia Normal : </label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="hxdianormal_e" id="hxdianormal_e" style="background-color:white">              
            </div>
          </div>
        </div>
        <center><h5 style="color: black;background:#d5d8dc;"><b>Reintegros</b></h5></center>
        
        
        <div class="row">
          <label class="col-sm-2 col-form-label text-dark  text-right">Reintegro : </label>
          <div class="col-sm-1">
            <div class="form-group" >              
              <input type="text" class="form-control" name="reintegro_e" id="reintegro_e" >
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Observaciones R. : </label>
          <div class="col-sm-5">
            <div class="form-group" >
              <input type="text" class="form-control" name="obs_reintegro_e" id="obs_reintegro_e" style="background-color:white">              
            </div>
          </div>
        </div>
        <center><h5 style="color: black;background:#d5d8dc;"><b>DESCUENTOS</b></h5></center>
        
        
        <div class="row">
          <label class="col-sm-2 col-form-label text-dark  text-right">Anticipo : </label>
          <div class="col-sm-1">
            <div class="form-group" >              
              <input type="text" class="form-control" name="anticipo_e" id="anticipo_e" readonly="true" style="background:#d98880;">
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Prestamos : </label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="prestamos_e" id="prestamos_e" readonly="true" style="background:#d98880;">              
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Inventarios : </label>
          <div class="col-sm-1">
            <div class="form-group" >              
              <input type="text" class="form-control" name="inventarios_e" id="inventarios_e" readonly="true" style="background:#d98880;" >
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Vencidos : </label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="vencidos_e" id="vencidos_e"  readonly="true" style="background:#d98880;">              
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-2 col-form-label text-dark  text-right">Atrasos : </label>
          <div class="col-sm-1">
            <div class="form-group" >              
              <input type="text" class="form-control" name="atrasos_e" id="atrasos_e" readonly="true" style="background:#d98880;">
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Faltante en Caja : </label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="faltante_caja_e" id="faltante_caja_e"  readonly="true" style="background:#d98880;">              
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Otros Descuentos : </label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="otros_descuentos_e" id="otros_descuentos_e" readonly="true" style="background:#d98880;">
            </div>
          </div>
          <label class="col-sm-2 col-form-label text-dark  text-right">Aporte Sindicato : </label>
          <div class="col-sm-1">
            <div class="form-group" >
              <input type="text" class="form-control" name="aporte_sindicato_e" id="aporte_sindicato_e"  >              
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-warning" id="guardar_edit_plantilla" name="guardar_edit_plantilla" data-dismiss="modal">Confirmar Actualización</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#guardar_edit_plantilla').click(function(){
      
      var codigo_e=document.getElementById("codigo_e").value;
      var cod_gestion_e=document.getElementById("cod_gestion_e").value;
      var cod_mes_e=document.getElementById("cod_mes_e").value;

      var dias_trabajados_e=$('#dias_trabajados_e').val();
      var faltas_e=$('#faltas_e').val();
      var faltas_sin_descuento_e=$('#faltas_sin_descuento_e').val();
      var dias_vacacion_e=$('#dias_vacacion_e').val();

      var domingos_e=$('#domingos_e').val();
      var feriados_e=$('#feriados_e').val();
      var noches_e=$('#noches_e').val();
      var domingo_reemp_e=$('#domingo_reemp_e').val();
      var feriado_reemp_e=$('#feriado_reemp_e').val();
      var ordinario_reemp_e=$('#ordinario_reemp_e').val();
      var hxdomingo_e=$('#hxdomingo_e').val();
      var hxferiado_e=$('#hxferiado_e').val();
      var hxdianormal_e=$('#hxdianormal_e').val();
      var reintegro_e=$('#reintegro_e').val();
      var obs_reintegro_e=$('#obs_reintegro_e').val();

      var anticipo_e=$('#anticipo_e').val();
      var prestamos_e=$('#prestamos_e').val();
      var inventarios_e=$('#inventarios_e').val();
      var vencidos_e=$('#vencidos_e').val();

      var atrasos_e=$('#atrasos_e').val();
      var faltante_caja_e=$('#faltante_caja_e').val();
      var otros_descuentos_e=$('#otros_descuentos_e').val();
      var aporte_sindicato_e=$('#aporte_sindicato_e').val();
      

      if(dias_trabajados_e=='' || faltas_e=='' || faltas_sin_descuento_e=='' || dias_vacacion_e==''|| domingos_e==''|| feriados_e==''|| noches_e==''|| domingo_reemp_e==''
        || feriado_reemp_e=='' || ordinario_reemp_e=='' || hxdomingo_e=='' || hxferiado_e==''|| hxdianormal_e==''|| reintegro_e==''|| anticipo_e==''|| prestamos_e==''
        || inventarios_e=='' || vencidos_e=='' || atrasos_e==''|| faltante_caja_e==''|| otros_descuentos_e==''|| aporte_sindicato_e==''){
        Swal.fire("Informativo!", "No se permiten Campos Vacíos (Excepto Observacion Reintegro).", "warning");
       }else{        
        guardar_edit_plantilla_sueldos(codigo_e,cod_gestion_e,cod_mes_e,dias_trabajados_e,faltas_e,faltas_sin_descuento_e,dias_vacacion_e,domingos_e,feriados_e,noches_e,domingo_reemp_e,feriado_reemp_e,ordinario_reemp_e,hxdomingo_e,hxferiado_e,hxdianormal_e,reintegro_e,obs_reintegro_e,anticipo_e,prestamos_e,inventarios_e,vencidos_e,atrasos_e,faltante_caja_e,otros_descuentos_e,aporte_sindicato_e);
       }      
    });    
  });
</script>

<?php


  }
}


?>