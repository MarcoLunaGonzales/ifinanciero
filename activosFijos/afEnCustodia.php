<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];

//echo "usuario:  ".$globalUser;

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$sql="SELECT afa.cod_activosfijos as cod_activo,(SELECT abreviatura from unidades_organizacionales where codigo=afa.cod_unidadorganizacional) as cod_unidadorganizacional,(Select abreviatura from areas where codigo=afa.cod_area)as cod_area,af.activo,afa.fechaasignacion,afa.cod_estadoasignacionaf,(select nombre from estados_asignacionaf where codigo=afa.cod_estadoasignacionaf) as estado_asignacionaf,afa.cod_personal,afa.fecha_recepcion
from activofijos_asignaciones afa, activosfijos af
where  af.cod_estadoactivofijo=1 and afa.cod_activosfijos=af.codigo and afa.cod_personal=$globalUser";

$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('cod_activo', $cod_activo);
$stmt->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('activo', $activo);

$stmt->bindColumn('fechaasignacion', $fecha_asignacion);
$stmt->bindColumn('estado_asignacionaf', $estado_asignacionaf);
$stmt->bindColumn('cod_estadoasignacionaf', $cod_estadoasignacionaf);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('fecha_recepcion', $fecha_recepcion);


?>

<div class="content" id="tabla1">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Activos Fijos En Custodia</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">
                      <thead>
                        <tr>
                            <th></th>
                            <th>Nro.</th>
                            <th>Código</th>
                            <th>Oficina</th>
                            <th>Area</th>
                            <th>Activo</th>
                            <th>F. Asignación</th>
                            <th>Estado Asignación AF</th>
                            <th>Fecha Recepción</th>
                            <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                            $datos =$cod_activo."-".$cod_personal;
                            if($cod_estadoasignacionaf==1){
                              $label='<span class="badge badge-warning">';
                            }
                            if($cod_estadoasignacionaf==2){
                              $label='<span class="badge badge-success">';
                            }
                            if($cod_estadoasignacionaf==3){
                              $label='<span class="badge badge-danger">';
                            }
                            if($cod_estadoasignacionaf==4){
                              $label='<span class="badge badge-primary">';
                            }
                            if($cod_estadoasignacionaf==5){
                              $label='<span class="badge badge-dark">';
                            }
                          ?>                          
                            <tr>
                                <td  class="td-actions text-right">
                                  <!-- <a href='<?=$printAFCustodia;?>?codigo=<?=$cod_activo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>">
                                    <i class="material-icons" title="Imprimir">print</i>
                                  </a> -->
                                  <a href='<?=$printDepreciacion1;?>?codigo=<?=$cod_activo;?>' target="_blank" rel="tooltip" class="btn btn-info">
                                    <i class="material-icons" title="Ficha Activo Fijo" style="color:black">print</i>
                                  </a>
                                </td>
                                <td><?=$index?></td>
                                <td><?=$cod_activo;?></td>
                                <td><?=$cod_unidadorganizacional;?></td>
                                <td><?=$cod_area;?></td>
                                <td><?=$activo;?></td>
                                <td><?=$fecha_asignacion;?></td>
                                <td><?=$label.$estado_asignacionaf."</span>";?></td>
                                <td><?=$fecha_recepcion;?></td>                                
                                <td class="td-actions text-right">
                                  <?php
                                    if($cod_estadoasignacionaf==1){
                                  ?>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalAceptar" onclick="agregaform('<?=$datos;?>')">
                                      <i class="material-icons" title="Recepcionar AF">thumb_up</i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalRechazar" onclick="agregaform('<?=$datos;?>')">
                                      <i class="material-icons" title="Rechazar Af">thumb_down</i>
                                    </button>
                                  <?php }elseif($cod_estadoasignacionaf==2){?>

                                    <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#modalDevolver" onclick="agregaform('<?=$datos;?>')">
                                      <i class="material-icons" title="Devolver AF">reply</i>
                                    </button>
                                    
                                  <?php }?>
                                </td>
                            </tr>
                        <?php $index++; } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                
                <div class="card-footer fixed-bottom">
                  <a href="#" type="button" class="btn btn-primary" onClick="impirmir_acta_de_entrega_all(<?=$globalUser?>)">Acta De Entrega</a>
                  <?php if($cod_estadoasignacionaf==2){?>
                  <button class="<?=$buttonNormal;?>" data-toggle="modal" data-target="#modalDevolverAll" >Devolver todos los AF</button>
                  <?php }?>
                </div>
                
              </div>

            </div>
          </div>  
        </div>
    </div>


<!-- Modal Aeptar-->
<div class="modal fade" id="modalAceptar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_af_aceptar1" id="codigo_af_aceptar1" value="0">
        <input type="hidden" name="codigo_af_aceptar2" id="codigo_af_aceptar2" value="0">
        Esta acción recepcionará el Activo Fijo. ¿Deseas Continuar?
      </div>       
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="RecepcionarAF" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- Modal rechazaR-->
<div class="modal fade" id="modalRechazar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Rechazar Activo Fijo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_af_aceptar1" id="codigo_af_aceptar1" value="0">
        <input type="hidden" name="codigo_af_aceptar2" id="codigo_af_aceptar2" value="0">
        <h6> Observaciones : </h6><br>
        <input type="text" name="observacion" id="observacion" class="form-control input-sm">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="RechazarAF"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- Modal devolver-->
<div class="modal fade" id="modalDevolver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Se devolverá el Activo fijo</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_af_aceptar1" id="codigo_af_aceptar1" value="0">
        <input type="hidden" name="codigo_af_aceptar2" id="codigo_af_aceptar2" value="0">
        <h6> Observaciones : </h6><br>
        <input type="text" name="observacionD" id="observacionD" class="form-control input-sm" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
      </div>
      <div class="modal-footer">
        
        <button type="button" class="btn btn-success" id="DevolverAF"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- Modal devolver all-->
<div class="modal fade" id="modalDevolverAll" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"> </b>Devolver todos los Activos Fijos</b></h4>
      </div>
      <form id="form2" class="form-horizontal" action="activosFijos/saveAsignacionAll.php" method="post">
        <div class="modal-body">
              <?php

                $stmt = $dbh->prepare("SELECT cod_activosfijos,cod_personal
                 FROM activofijos_asignaciones
                  where cod_estadoasignacionaf=2 and cod_personal=:cod_personal ");
                // Bind
                $stmt->bindParam(':cod_personal', $cod_personal);
                $stmt->execute();
                $stmt->bindColumn('cod_activosfijos', $cod_activosfijos);
                $stmt->bindColumn('cod_personal', $cod_personal);
                $cont_aux=1;
                while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                  ?>
                        <h6>Observaciones Para El AF Con Código: <?=$cod_activosfijos?> </h6>
                        <input type="text" name="observacionD<?=$cont_aux;?>" id="observacionD<?=$cont_aux;?>" class="form-control input-sm" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">
                <?php
                  $cont_aux=$cont_aux+1;
                  }
                  $cantidad_items=$cont_aux-1;?>
              <input type="hidden" name="cod_personal" id="cod_personal" value="<?=$cod_personal;?>">    
              <input type="hidden" name="cantidad_items" id="cantidad_items" value="<?=$cantidad_items;?>">
        </div>       
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-success" id="DevolverAFAll" data-dismiss="modal">Aceptar</button> -->
          <button type="submit" class="btn btn-success" >Guardar</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#RechazarAF').click(function(){
      cod_af=document.getElementById("codigo_af_aceptar1").value;
      cod_personal=document.getElementById("codigo_af_aceptar2").value;
      observacion=$('#observacion').val();
      rechazarRecepcion(cod_personal,cod_af,observacion);
    });

    $('#RecepcionarAF').click(function(){
      //cod_af='<?php echo $cod_activo;?>';
      cod_af=document.getElementById("codigo_af_aceptar1").value;
      cod_personal=document.getElementById("codigo_af_aceptar2").value;

      RecepcionarAF(cod_personal,cod_af);
    });

    $('#DevolverAF').click(function(){
      //cod_af='<?php echo $cod_activo;?>';
      cod_af=document.getElementById("codigo_af_aceptar1").value;
      cod_personal=document.getElementById("codigo_af_aceptar2").value;
      observacionD=$('#observacionD').val();
      DevolverAF(cod_personal,cod_af,observacionD);
    });   

  });
</script>
