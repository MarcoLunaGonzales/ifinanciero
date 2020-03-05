<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();
$codigo_tipo_caja_Chica=$codigo;
$stmtTCC = $dbh->prepare("SELECT nombre from tipos_caja_chica where  codigo = $codigo_tipo_caja_Chica");
$stmtTCC->execute();
$resultTCC=$stmtTCC->fetch();
$nombre_tipoCC=$resultTCC['nombre'];

$stmt = $dbh->prepare("SELECT *,
  (select e.nombre from estados_contrato e where e.codigo=cod_estado) as nombre_estado,
(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal) as personal
 from caja_chica where cod_estadoreferencial=1 and cod_tipocajachica = $codigo_tipo_caja_Chica");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $cod_cajachica);
$stmt->bindColumn('cod_tipocajachica', $cod_tipocajachica);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('monto_inicio', $monto_inicio);
$stmt->bindColumn('monto_reembolso', $monto_reembolso);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('personal', $personal);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('nombre_estado', $nombre_estado);


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
                  <h4 class="card-title"><?=$nombrePluralCajaChica?></h4>
                  <h4 class="card-title" align="center"><?=$nombre_tipoCC?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th>Nro. Caja Chica</th>
                          <th>Fecha</th>
                          
                          <th>Responsable</th>
                          <th>Monto Inicio</th>
                          <th>Saldo</th>
                          <th>Detalle</th>
                          <th>estado</th>
                          <th></th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                             if($cod_estado==1)
                                $label='<span class="badge badge-success">';
                            else
                              $label='<span class="badge badge-danger">';                              
                           
                          ?>
                          <tr>
                            <td><?=$numero;?></td>    
                              <td><?=$fecha;?></td>
                              <td><?=$personal;?></td>        
                              <td><?=number_format($monto_inicio, 2, '.', ',');?></td>        
                              <td><?=number_format($monto_reembolso, 2, '.', ',');?></td>        
                              <td><?=$observaciones;?></td>        
                              <td><?=$label.$nombre_estado."</span>";?></td>
                                
                              <!-- href='<?=$urlprintFiniquitosOficial;?>?codigo=<?=$codigo;?>' -->


                              <td class="td-actions text-right">
                                <?php
                                if($globalAdmin==1 and $cod_estado==1){
                              ?>
                              <a href='<?=$urlListDetalleCajaChica;?>&codigo=<?=$cod_cajachica;?>&cod_tcc=<?=$codigo_tipo_caja_Chica?>' rel="tooltip" class="btn btn-warning">
                                  <i class="material-icons" title="Agregar Detalle">playlist_add</i>
                              </a>
                              <label class="text-danger"> | </label>
                              <a  rel="tooltip" class="btn btn-danger" style="color:#ffffff;" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlDeleteCajaChica;?>&codigo=<?=$cod_cajachica;?>&cod_tcc=<?=$codigo_tipo_caja_Chica?>&cod_a=1')">
                                  <i class="material-icons" title="Cerrar Caja Chica">lock</i>                                
                                </a>
                              <?php }?>
                                <a href='<?=$urlprint_cajachica;?>?codigo=<?=$cod_cajachica;?>' target="_blank" rel="tooltip" class="btn btn-primary">
                                  <i class="material-icons" title="Imprimir">print</i>
                              </a>

                              <?php
                                if($globalAdmin==1 and $cod_estado==1){
                              ?>
                                
                                <a href='<?=$urlFormCajaChica;?>&codigo=<?=$cod_cajachica;?>&cod_tcc=<?=$codigo_tipo_caja_Chica?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteCajaChica;?>&codigo=<?=$cod_cajachica;?>&cod_tcc=<?=$codigo_tipo_caja_Chica?>&cod_a=2')">
                                  <i class="material-icons" title="Borrar"><?=$iconDelete;?></i>
                                </button>
                                <?php
                                  }
                                ?>
                              
                              </td>
                              <td class="text-center">
                                <a href="<?=$urlprint_contabilizacion_cajachica;?>?cod_cajachica=<?=$cod_cajachica;?>" target="_blank" > 
                                  <i class="material-icons" title="Generar Comprobante" style="color:red">input</i>
                                </a>
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
                      <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormCajaChica;?>&codigo=0&cod_tcc=<?=$codigo_tipo_caja_Chica?>'">Registrar</button>                
                <?php
                }
                ?>
                <button class="btn btn-danger" onClick="location.href='<?=$urlprincipal_CajaChica;?>'"><i class="material-icons" title="Volver">keyboard_return</i>Volver</button>
              </div>
            </div>
          </div>  
        </div>
    </div>
