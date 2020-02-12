<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];
$dbh = new Conexion();


//SELECT
$stmt = $dbh->prepare("SELECT c.codigo,c.nombre,
(select t.nombre from tipos_cargos_personal t where t.codigo=c.cod_tipo_cargo) as nombre_tipo
from cargos_escala_salarial ce,cargos c 
where ce.cod_estadoreferencial=1 and ce.cod_cargo=c.codigo GROUP BY ce.cod_cargo
");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $cod_cargo);
$stmt->bindColumn('nombre', $nombre_cargo);
$stmt->bindColumn('nombre_tipo', $nombre_tipo);

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
                  <h4 class="card-title">Escala Salarial en General</h4>
                  <h4 class="card-title" align="center"><?=$nombre_cargo;?></h4>                  
                  
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">
                      <thead>
                        <tr >
                          <th>#</th>                          
                          <!-- <th>Cod Categoria</th> -->
                          <th>Nivel</th>
                          <th>Cargo</th>                          
                          <?php
                            $sqlBonos = "SELECT codigo,nombre from niveles_escala_salarial
                            where cod_estadoreferencial=1 ORDER BY nombre";
                            $stmtBonos = $dbh->prepare($sqlBonos);
                            $stmtBonos->execute();                      
                            $stmtBonos->bindColumn('codigo',$codigo_nivel);
                            $stmtBonos->bindColumn('nombre',$nombre_nivel);
                            while ($row = $stmtBonos->fetch()) 
                            { ?>
                              <th><small><?=$nombre_nivel;?></small></th>                      
                              <?php
                              $arrayNivel[] = $codigo_nivel;//guardamos los codigos de nivel
                            }
                          ?>
                          <th></th>                                                   
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;                      
                        // $datos=$cod_cargo;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                         
                          // $datos=$cod_cargo."/".$cod_cargo_funcion."/".$nombre_funcion."/".$peso;
                          ?>
                            <tr>
                                <td><?=$index;?></td>
                                <!-- <td>Categoria</td> -->
                                <td><?=$nombre_tipo;?></td>
                                <td><?=$nombre_cargo;?></td>
                                <?php

                                for ($j=0; $j <count($arrayNivel);$j++){ 
                                    $cod_nivel_aux=$arrayNivel[$j];                          
                                    $sqlMontoNivel = "SELECT cod_nivel_escala_salarial,monto from cargos_escala_salarial
                                    where cod_cargo=$cod_cargo and cod_nivel_escala_salarial=$cod_nivel_aux and cod_estadoreferencial=1";
                                    $stmtMontoNivel = $dbh->prepare($sqlMontoNivel);
                                    $stmtMontoNivel->execute();
                                    $resultMontoNivel=$stmtMontoNivel->fetch();
                                    $cod_nivel_escala_salarialX=$resultMontoNivel['cod_nivel_escala_salarial'];
                                    $montoX=$resultMontoNivel['monto'];

                                    if($cod_nivel_escala_salarialX==$cod_nivel_aux){ ?>
                                      <td  class="small"><?=formatNumberDec($montoX);?></td>  
                                    <?php                            
                                    }else{ $montoAux=0; ?>                                                          
                                      <td  class="small"><?=formatNumberDec($montoAux);?></td>
                                    <?php                            
                                    }
                                }?>

                                
                                <td class="td-actions text-right">
                                  <?php
                                    if($globalAdmin==1){
                                  ?>                                  
                                  <!-- <a href='<?=$urlCargosEscalaSalarial;?>&codigo=<?=$cod_cargo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                    <i class="material-icons"><?=$iconEdit;?></i>
                                  </a>
                                  <button rel="tooltip" class="<?= $buttonDelete; ?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlCargoEscalaSalarialGeneralDelete;?>&codigo=<?=$cod_cargo; ?>')">
                                    <i class="material-icons"><?=$iconDelete; ?></i>
                                  </button> -->
                                <?php } ?>
                                </td>
                            </tr>
                        <?php $index++; } ?>                    
                      </tbody>                      
                    </table>
                  </div>
                </div>
                <div class="card-footer fixed-bottom">
                <a href="<?=$urlListCargos;?>" class="btn btn-danger">
                  <i class="material-icons" title="Volver">keyboard_return</i>Volver
                </a> 
              </div>
              </div>
            </div>
          </div>  
        </div>
    </div>


