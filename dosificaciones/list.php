<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT *,DATE_FORMAT(fecha_limite_emision,'%d/%m/%Y')as fecha_limite from dosificaciones_facturas where cod_estado<>2 order by fecha desc");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('fecha', $fecha);
$stmt->bindColumn('cod_sucursal', $cod_sucursal);
$stmt->bindColumn('nro_autorizacion', $nro_autorizacion);
$stmt->bindColumn('llave_dosificacion', $llave_dosificacion);
$stmt->bindColumn('fecha_limite', $fecha_limite);
$stmt->bindColumn('leyenda', $leyenda);
$stmt->bindColumn('cod_estado', $cod_estado);

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
                  <h4 class="card-title">Dosificaciones</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th>Sucursal</th>                        
                          <th>Nro. Autorizacióon</th>
                          <th>Llave</th>
                          <th>Fecha Límite<br>Emisión</th>
                          <th>Leyenda</th>
                          <th>Estado</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                          $nombre_sucursal=obtenerValorConfiguracionFactura(2);
                          switch ($cod_estado) {                            
                            case 1:
                              $estado="Activo";
                              $labelEstado='<span class="badge badge-success">';
                            break;
                            case 0:
                              $estado="Cerrado";
                              $labelEstado='<span class="badge badge-danger">';                                
                            break;                              
                          }
                          ?>
                          <tr>
                            <td><small><?=$nombre_sucursal;?></small></td>
                              <td><small><?=$nro_autorizacion;?><small></td>
                              <td><small><?=$llave_dosificacion;?></small></td>
                              <td><small><?=$fecha_limite;?></small></td>
                              <td><small><?=$leyenda;?></small></td>
                              <td><small><?=$labelEstado.$estado."</span>";?></small></td>
                              <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                              ?>
                                <a href='<?=$urlDeleteDosificacion;?>&codigo=<?=$codigo;?>&sw=0&cod_sucursal=<?=$cod_sucursal?>' rel="tooltip" class="btn btn-info">
                                  <i class="material-icons" title="Activar Dosificación">check</i>
                                </a>
                                <a href='<?=$urlRegisterDosificacion;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteDosificacion;?>&codigo=<?=$codigo;?>&sw=1&cod_sucursal=0')">
                                  <i class="material-icons"><?=$iconDelete;?></i>
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
              <?php

              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">                    
                <a href="<?=$urlRegisterDosificacion;?>&codigo=0" target="_self" class="<?=$buttonNormal;?>">Registrar</a>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
