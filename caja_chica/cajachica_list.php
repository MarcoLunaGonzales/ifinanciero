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
                          <th>#</th>                                                  
                          <th>Fecha</th>
                          <th>Número</th>
                          <th>Responsable</th>
                          <th>Monto Inicio</th>
                          <th>Reembolso</th>
                          <th>Observaciones</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                          <tr>
                            <td><?=$index;?></td>                            
                              <td><?=$fecha;?></td>
                              <td><?=$numero;?></td>        
                              <td><?=$personal;?></td>        
                              <td><?=$monto_inicio;?></td>        
                              <td><?=$monto_reembolso;?></td>        
                              <td><?=$observaciones;?></td>        

                              
                              <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                              ?>
                                <a href='<?=$urlListDetalleCajaChica;?>&codigo=<?=$cod_cajachica;?>&cod_tcc=<?=$codigo_tipo_caja_Chica?>' rel="tooltip" class="btn btn-warning">
                                  <i class="material-icons" title="Agregar detalle">playlist_add</i>
                                </a>
                                <a href='<?=$urlFormCajaChica;?>&codigo=<?=$codigo;?>&cod_tcc=<?=$codigo_tipo_caja_Chica?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteCajaChica;?>&codigo=<?=$codigo;?>&cod_tcc=<?=$codigo_tipo_caja_Chica?>')">
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
                      <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormCajaChica;?>&codigo=0&cod_tcc=<?=$codigo_tipo_caja_Chica?>'">Registrar</button>                
                <?php
                }
                ?>
                <button class="btn btn-danger" onClick="location.href='<?=$urlprincipal_CajaChica;?>'">Volver</button>
              </div>
            </div>
          </div>  
        </div>
    </div>