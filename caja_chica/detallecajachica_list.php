<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$cod_cajachica=$codigo;
$cod_tcc=$cod_tcc;
$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT codigo,cod_cuenta,fecha,
  (select td.nombre from tipos_documentocajachica td where td.codigo=cod_tipodoccajachica) as cod_tipodoccajachica,nro_documento,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal)as cod_personal,monto,observaciones,cod_estado,
  (select e.nombre from estados_rendiciones e where e.codigo=cod_estado) as nombre_estado 
from caja_chicadetalle
where cod_cajachica=$cod_cajachica and cod_estadoreferencial=1");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo_detalle_Cajachica);
$stmt->bindColumn('cod_cuenta', $cod_cuenta);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('cod_tipodoccajachica', $cod_tipodoccajachica);
$stmt->bindColumn('nro_documento', $nro_documento);
$stmt->bindColumn('monto', $monto);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('nombre_estado', $nombre_estado);


$stmtb = $dbh->prepare("SELECT (select a.nombre from tipos_caja_chica a where a.codigo=cod_tipocajachica) as nombre_caja_chica FROM caja_chica WHERE codigo=$cod_cajachica");
$stmtb->execute();
$resulttb=$stmtb->fetch();
$nombre_caja_chica=$resulttb['nombre_caja_chica'];


?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">DETALLE</h4>
                  <h4 class="card-title" align="center"><?=$nombre_caja_chica?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th>#</th>                        
                          <th>Cuenta</th>
                          <th>Fecha</th>
                          <th>Tipo</th>
                          <th>N. Doc</th>
                          <th>Entregado a</th>
                          <th>Monto</th>                          
                          <th>Observaciones</th>
                          <th>Estado</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          if($cod_estado==1)
                            $label='<span class="badge badge-danger">';
                          else
                            $label='<span class="badge badge-success">';
                         ?>
                          <tr>
                            <td><?=$index;?></td>                            
                              <td><?=$cod_cuenta;?></td>
                              <td><?=$fecha;?></td>
                              <td><?=$cod_tipodoccajachica;?></td>        
                              <td><?=$nro_documento;?></td>        
                              <td><?=$cod_personal;?></td>        
                              <td><?=$monto;?></td>        
                              <td><?=$observaciones;?></td>
                              <td><?=$label.$nombre_estado."</span>";?></td>

                              
                              <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1 and $cod_estado==1){
                              ?>
                                
                                <a href='<?=$urlFormDetalleCajaChica;?>&codigo=<?=$codigo_detalle_Cajachica;?>&cod_tcc=<?=$cod_tcc?>&cod_cc=<?=$cod_cajachica?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteDetalleCajaChica;?>&codigo=<?=$codigo_detalle_Cajachica;?>&cod_tcc=<?=$cod_tcc?>&cod_cc=<?=$cod_cajachica?>')">
                                  <i class="material-icons" title="Borrar"><?=$iconDelete;?></i>
                                </button> 
                                <?php
                                  }
                                ?>
                              
                              </td>
                          </tr>
                        <?php $index++; } ?>
                      </tbody>
                    
                    </table>
                  </div>
                </div>
              </div>
              
              <div class="card-footer fixed-bottom">
                <?php

              if($globalAdmin==1){
              ?>
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormDetalleCajaChica;?>&codigo=0&cod_tcc=<?=$cod_tcc?>&cod_cc=<?=$cod_cajachica?>'">Registrar</button>
                    <?php
              }
              ?>
              <button class="btn btn-danger" onClick="location.href='<?=$urlListCajaChica;?>&codigo=<?=$cod_tcc?>'">Volver</button>
              </div>              
            </div>
          </div>  
        </div>
    </div>
