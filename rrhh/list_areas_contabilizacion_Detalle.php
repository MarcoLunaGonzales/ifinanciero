<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$codigo_area_contabilizacion=$codigo;
//echo "test cod bono: ".$codigoBono;

$globalAdmin=$_SESSION["globalAdmin"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$codGestionActiva=$_SESSION['globalGestion'];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT codigo,
  (SELECT uo.nombre from unidades_organizacionales uo where uo.codigo=cod_unidadorganizacional) as nombre_unidadorganizacional,
  (SELECT a.nombre from areas a where a.codigo=cod_area) as nombre_area
from areas_contabilizacion_detalle
where cod_areacontabilizacion = $codigo_area_contabilizacion and cod_estadoreferencial=1");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre_unidadorganizacional', $nombre_unidadorganizacional);
$stmt->bindColumn('nombre_area', $nombre_area);


//Mostrar tipo contabilizacion
$stmtb = $dbh->prepare("SELECT nombre FROM areas_contabilizacion WHERE codigo=$codigo_area_contabilizacion");
// Ejecutamos
$stmtb->execute();
// bindColumn
$stmtb->bindColumn('nombre', $nombreContabilizacion);
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
                  <h4 class="card-title"><?=$nombreSingularAreas_contabilizacion_detalle?></h4>                                                  
                  <?php
                  while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
                    ?>
                  <h4 class="card-title" align="center"><?=$nombreContabilizacion?></h4>
                  <?php
                  }
                  ?>                
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Unidad Organizacional</th>
                          <th>Area</th>                          
                          <th>Delete</th> 
                        </tr>
                      </thead>
                      <tbody>
                        <?php
              						$index=1;
                        	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                                                  
                        ?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="text-left"><?=$nombre_unidadorganizacional;?></td>
                          <td class="text-left"><?=$nombre_area?></td>                          
                          <td class="td-actions text-right">
                              <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteAreaContabilizacionDetalle;?>&codigo=<?=$codigo;?>&codigox=<?=$codigo_area_contabilizacion;?>')">
                                  <i class="material-icons"><?=$iconDelete;?></i>
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

                <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormAreas_contabilizacion_detalle;?>&codigo=<?=$codigo_area_contabilizacion?>'">Registrar</button>

                <button class="<?=$buttonCancel;?>" onClick="location.href='<?=$urlListAreas_contabilizacion;?>'">Cancelar</button>
              </div>
              

              <?php
              }
              ?>
		  
            </div>
          </div>  
              


        </div>
    </div>
