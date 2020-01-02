<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];


$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura, contabilizacion_vista FROM areas_contabilizacion where cod_estado_referencial=1 order by nombre");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('contabilizacion_vista', $contabilizacion_vista);

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
                  <h4 class="card-title"><?=$nombreSingularAreas_contabilizacion?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-left">#</th>
                          <th>Código</th>
                          <th>Nombre</th>
                          <th>Abreviatura</th>
                          <th>Contabilizacion vista</th>
                          <th class="text-right">Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
              						$index=1;
                        	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                                                   
                        ?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td align="center"><?=$codigo;?></td>
                          <td class="text-left"><?=$nombre;?></td>
                          <td class="text-center"><?=$abreviatura;?></td>
                          <td class="text-left"><?=$contabilizacion_vista;?></td>
                          <td class="td-actions text-right">                                    
                            <a href='<?=$list_areas_contabilizacion_Detalle;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonDetailMin;?>">
                              <i class="material-icons" title="Adicionar">playlist_add</i>
                            </a>
                            <a href='<?=$urlFormAreas_contabilizacion;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteAreas_contabilizacion;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons" title="Eliminar"><?=$iconDelete;?></i>
                            </button>
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
              </div>

              <?php
              if($globalAdmin==1){
              ?>
      				<div class="card-footer fixed-bottom">
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormAreas_contabilizacion;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
              


        </div>
    </div>
