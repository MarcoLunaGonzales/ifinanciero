<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';
require_once 'functionsDepreciacion.php';

$globalAdmin=$_SESSION["globalAdmin"];


$dbh = new Conexion();


$stmt = $dbh->prepare("select * from mesdepreciaciones");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('mes', $mes);
$stmt->bindColumn('gestion', $gestion);
$stmt->bindColumn('ufvinicio', $ufvinicio);
$stmt->bindColumn('ufvfinal', $ufvfinal);
$stmt->bindColumn('estado', $estado);

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
                  <h4 class="card-title"><?=$moduleNamePlural7?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table"  id="tablePaginator">
                      <thead>
                        <tr>
                          <th>Mes</th>
                          <th>Gestion</th>
                          <!-- <th>Ufv Inicio</th>
                          <th>Ufv Final</th> -->
                          <th class="text-center">Detalle</th>
                          <th class="text-center">Generar Comprobante</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                          $sw_depreciacion=verificarContabilizacion($codigo);
                          ?>
                          <tr>
                              <td><?=nombreMes($mes);?></td>
                              <td><?=$gestion;?></td>                              
                              <td class="text-center">
                                <a target="_blank" href="<?=$printDepreciacionMes;?>?codigo=<?=$codigo;?>">
                                  <i class="material-icons" title='Ver Detalle' style="color:blue">assignment</i></a>
                              </td>
                              <?php
                              if($sw_depreciacion>0){?>
                                <td class="text-center">
                                  <span style="color: #00afaa"><b>Comprobante Generado</b></span>
                                </td>
                              <?php }else{?>
                                <td class="text-center">
                                  <a href="<?=$urlGenerarCompDepreciacion;?>&codigo=<?=$codigo;?>">
                                    <i class="material-icons" title="Generar Comprobante" style="color:red">input</i>
                                  </a>
                                </td>
                              <?php }
                              ?>
                          </tr><?php 
                          $index++; 
                        } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <?php
              // if($globalAdmin==1){
              ?>
      				<div class="card-footer ml-auto mr-auto">
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegistrar7;?>'">Registrar</button>

              </div>
              <?php
              //}
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
