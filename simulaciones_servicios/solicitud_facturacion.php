<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$codigo_simulacion=$cod;
$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Preparamos

$stmt = $dbh->prepare("SELECT * FROM solicitudes_facturacion where cod_simulacion_servicio=$codigo_simulacion");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo_facturacion);
$stmt->bindColumn('cod_simulacion_servicio', $cod_simulacion_servicio);
$stmt->bindColumn('cod_unidadorganizacional', $cod_unidadorganizacional);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('fecha_registro', $fecha_registro);
$stmt->bindColumn('fecha_solicitudfactura', $fecha_solicitudfactura);
$stmt->bindColumn('cod_tipoobjeto', $cod_tipoobjeto);
$stmt->bindColumn('cod_tipopago', $cod_tipopago);
$stmt->bindColumn('cod_cliente', $cod_cliente);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('razon_social', $razon_social);
$stmt->bindColumn('nit', $nit);
$stmt->bindColumn('observaciones', $observaciones);

?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">polymer</i>
                  </div>
                  <h4 class="card-title"><b>Solicitud de Facturación</b></h4>
                </div>
                <div class="card-body">
                    <table class="table" id="tablePaginator">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>                          
                          <th>Oficina</th>
                          <th>Area</th>
                          <th>Fecha R</th>
                          <th>Fecha Solicitud</th>
                          <th>Cliente</th>
                          <th>Personal</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                        $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $responsable=namePersonal($cod_personal);                          
                          ?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td><?=$cod_unidadorganizacional;?></td>
                          <td><?=$cod_area;?></td>
                          <td><?=$fecha_registro;?></td>
                          <td><?=$fecha_solicitudfactura;?></td>
                          <td><?=$cod_cliente;?></td>
                          <td><?=$responsable;?></td>

                          <td class="td-actions text-right">
                            <?php
                              if($globalAdmin==1){                            
                              ?>
                            <a title="Editar Simulación - Detalle" href='<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=<?=$codigo_facturacion?>' class="btn btn-info">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <!-- <button type="button" onclick="SolicitudFacturacionDetalle()" class="btn btn-success ">
                               <i class="material-icons" title="Facturación Detalle">description</i>
                            </button> -->

                           <!--  <button title="Eliminar Simulación" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
                            </button> -->                            
                              <?php  
                              }
                            ?>
                          </td>
                        </tr>
                        <?php
                            $index++;
                          }
                        ?>
                      </tbody>
                    </table>
                </div>
              </div>
              <div class="card-footer fixed-bottom">
               <?php 
              if($globalAdmin==1){              
                  ?><a href="<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=0" target="_self" class="<?=$buttonNormal;?>">Registrar</a>
                  <a href='<?=$urlList;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                  <?php                
              } 
               ?>
              </div>      
            </div>
          </div>  
        </div>
    </div>
