<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functionsGeneral.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT codigo, nombre, observaciones FROM $table where cod_estadoreferencial=1");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('observaciones', $observaciones);

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
                  <h4 class="card-title"><?=$moduleNamePlural?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th class="text-left">Nombre</th>
                          <th>Descripcion</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
                      		$index=1;$cont= array();
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          // Preparamos
                            $dbh1 = new Conexion();
                            $sql="SELECT pc.numero,pc.nombre from partidaspresupuestarias_cuentas p join plan_cuentas pc on p.cod_cuenta=pc.codigo where cod_partidapresupuestaria=$codigo";
                                   $stmt2 = $dbh1->prepare($sql);
                                   $stmt2->execute(); 
                                   $nc=0;
                                   
                                   while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                                      $dato =new stdClass();
                                      $nombreX=trim($row2['nombre']);
                                      $numeroX=trim($row2['numero']);
                                      $dato->codigo=($nc+1);
                                      $dato->nombre=$nombreX;
                                      $dato->numero=$numeroX;
                                      $datos[$index-1][$nc]=$dato;                           
                                      $nc++;
                                    }
                                $cont[$index-1]=$nc;  
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="text-left"><?=$nombre;?></td>
                          <td><?=$observaciones;?></td>
                          <td class="td-actions text-right">
                            <a href='#' rel="tooltip" class="btn btn-warning" onclick="filaTablaGeneral($('#tablas_registradas'),<?=$index?>)">
                              <i class="material-icons" title="Ver Cuentas">settings_applications</i>
                            </a>
                            
                            <a href='<?=$urlRegisterCuentas;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-success">
                              <i class="material-icons" title="Registrar Cuentas">playlist_add</i>
                            </a>

                            <a href='<?=$urlEdit;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
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
      				<div class="card-footer fixed-bottom">
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegister;?>'">Registrar</button>
              </div>
            </div>
          </div>  
        </div>
    </div>
<?php 
$lan=sizeof($cont);
for ($i=0; $i < $lan; $i++) {
  ?><script>var presupuestos=[];</script><?php
     for ($j=0; $j < $cont[$i]; $j++) { 
         if($cont[$i]>0){
          ?><script>presupuestos.push({codigo:<?=$datos[$i][$j]->codigo?>,nombre:'<?=$datos[$i][$j]->nombre?>',numero:'<?=$datos[$i][$j]->numero?>'});</script><?php         
          }          
        }
    ?><script>cuentas_tabla_general.push(presupuestos);</script><?php                    
}
require_once 'partidas_presupuestarias/modal.php';
?>