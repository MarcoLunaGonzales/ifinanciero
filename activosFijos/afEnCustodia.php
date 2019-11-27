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
                  <h4 class="card-title">AF En Custodia</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                            <th></th>
                            <th>Codigo</th>
                            <th>Unidad O</th>
                            <th>Area</th>
                            <th>Activo</th>
                            <th>F. Asignación</th>
                            <th>Estado Asignacion AF</th>
                            <th>Fecha Recepcion</th>
                            <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                            $datos =$cod_activo;
                          ?>
                            <tr>
                                <td  class="td-actions text-right">
                                  <a href='<?=$printAFCustodia;?>?codigo=<?=$cod_activo;?>' target="_blank" rel="tooltip" class="<?=$buttonEdit;?>">
                                    <i class="material-icons">print</i>
                                  </a>
                                </td>
                                <td><?=$cod_activo;?></td>
                                <td><?=$cod_unidadorganizacional;?></td>
                                <td><?=$cod_area;?></td>
                                <td><?=$activo;?></td>
                                <td><?=$fecha_asignacion;?></td>
                                <td><?=$estado_asignacionaf;?></td>
                                <td><?=$fecha_recepcion;?></td>                                
                                <td class="td-actions text-right">
                                  <?php
                                    if($cod_estadoasignacionaf==1){
                                  ?>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalAceptar" onclick="agregaform(<?php echo $datos;?>)">
                                      <i class="material-icons" title="Recepcionar">thumb_up</i>
                                    </button>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalRechazar" onclick="agregaform(<?php echo $datos;?>)">
                                      <i class="material-icons" title="Rechazar">thumb_down</i>
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
        <h4 class="modal-title" id="myModalLabel">Se recepcionará el Activo fijo</h4>
      </div>
      <div class="modal-body">
        No podrá revertir el proceso
      </div>       
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="RecepcionarAF" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
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
        <label> Observaciones</label><br>
        <input type="text" name="observacion" id="observacion" class="form-control input-sm">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="RechazarAF"  data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#RechazarAF').click(function(){
    
      //alert(“Has escrito: ”);
      cod_personal='<?php echo $cod_personal;?>';
      observacion=$('#observacion').val();
      rechazarRecepcion(cod_personal,observacion);
    });

    $('#RecepcionarAF').click(function(){
    
      //alert(“Has escrito: ”);
      //cod_af='<?php echo $cod_activo;?>';
      cod_personal='<?php echo $cod_personal;?>';
      RecepcionarAF(cod_personal);
    });

  });
</script>
