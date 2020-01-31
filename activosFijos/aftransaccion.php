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

$sql="SELECT afa.cod_activosfijos,(select activo from activosfijos where codigo=afa.cod_activosfijos)as activo,afa.fechaasignacion,(select abreviatura from unidades_organizacionales where codigo=afa.cod_unidadorganizacional)as cod_unidadorganizacional,(select abreviatura from areas where codigo=afa.cod_area)as cod_area,(select CONCAT_WS(' ',paterno,materno,primer_nombre) from personal where codigo=afa.cod_personal)as nom_personal,afa.cod_personal,afa.estadobien_asig,(select nombre from estados_asignacionaf where codigo=afa.cod_estadoasignacionaf)as estado_asignacionaf,afa.cod_estadoasignacionaf,afa.fecha_recepcion,afa.observaciones_recepcion,afa.fecha_devolucion,afa.observaciones_devolucion
FROM activofijos_asignaciones afa
order by 2";

$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('cod_activosfijos', $cod_activo);
$stmt->bindColumn('activo', $activo);
$stmt->bindColumn('fechaasignacion', $fecha_asignacion);
$stmt->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('nom_personal', $nom_personal);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('estadobien_asig', $estado_bien_asig);
$stmt->bindColumn('estado_asignacionaf', $estado_asignacionaf);
$stmt->bindColumn('cod_estadoasignacionaf', $cod_estadoasignacionaf);
$stmt->bindColumn('fecha_recepcion', $fecha_recepcion);
$stmt->bindColumn('observaciones_recepcion', $observacion_recepcion);
$stmt->bindColumn('fecha_devolucion', $fecha_devolucion);
$stmt->bindColumn('observaciones_devolucion', $observaciones_devolucion);

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
                  <h4 class="card-title">Activos Fijos Asignados</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                            <th></th>
                            <th>Codigo</th>
                            <th>OF.</th>
                            <th>Area</th>
                            <th>Activo</th>
                            <th>Personal</th>
                            <th>F. Asignación</th>
                            <th>Estado Asignacion AF</th>
                            <th>Fecha Recepción</th>
                            <th>Fecha Devolución</th>
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
                                  <a href='<?=$printAFCustodia;?>?codigo=<?=$cod_activo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>">
                                    <i class="material-icons" title="Imprimir">print</i>
                                  </a>
                                </td>
                                <td><?=$cod_activo;?></td>
                                <td><?=$cod_unidadorganizacional;?></td>
                                <td><?=$cod_area;?></td>
                                <td><?=$activo;?></td>
                                <td><?=$nom_personal;?></td>
                                <td><?=$fecha_asignacion;?></td>
                                <td>
                                    <?=$label.$estado_asignacionaf."</span>";?>
                                </td>
                                <td><?=$fecha_recepcion;?></td>                                
                                <td><?=$fecha_devolucion;?></td>
                                <td class="td-actions text-right">
                                  <?php
                                    if($cod_estadoasignacionaf==5){
                                  ?>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalAceptar" onclick="agregaform('<?=$datos;?>')">
                                      <i class="material-icons" title="Aceptar Devolución">thumb_up</i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalObservar" onclick="agregaform('<?=$datos;?>')" >
                                      <i class="material-icons" title="Rechazar Devolución">thumb_down</i>
                                    </button>
                                    
                                  <?php }?>
                                </td>
                                
                            </tr>
                        <?php $index++; } ?>
                      </tbody>
                    </table>
                  </div>
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
        <h4 class="modal-title" id="myModalLabel">AF Devuelto Sin Observaciones</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_af_aceptar1" id="codigo_af_aceptar1" value="0">
        <input type="hidden" name="codigo_af_aceptar2" id="codigo_af_aceptar2" value="0">
        No podrá revertir el proceso
      </div>       
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="AceptarAFD" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- Modal rechazaR-->
<div class="modal fade" id="modalObservar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Rechazar Activo Fijo Devuelto</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_af_aceptar1" id="codigo_af_aceptar1" value="0">
        <input type="hidden" name="codigo_af_aceptar2" id="codigo_af_aceptar2" value="0">
        No podrá revertir el proceso
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="RechazarAFD"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#RechazarAFD').click(function(){
      
      cod_af=document.getElementById("codigo_af_aceptar1").value;
      cod_personal=document.getElementById("codigo_af_aceptar2").value;
      //observacion=$('#observacion').val();
      rechazarDevolucion(cod_personal,cod_af);
    });

    $('#AceptarAFD').click(function(){
      
      //cod_af='<?php echo $cod_activo;?>';
      cod_af=document.getElementById("codigo_af_aceptar1").value;
      cod_personal=document.getElementById("codigo_af_aceptar2").value;
      //console.log("llega: "+cod_af);

      AceptarDevolucion(cod_personal,cod_af);
    });

  });
</script>