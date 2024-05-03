<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();


$stmt = $dbh->prepare(" SELECT p.codigo,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) as personal,p.identificacion
 from personal p
 where p.cod_estadopersonal <> 1
 order by p.codigo");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $cod_personal);
$stmt->bindColumn('identificacion', $ci);
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
              
              <h4 class="card-title" >Personal para Aprobación</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table" id="tablePaginator">
                  <thead>
                      <tr>
                        
                        <th>Cod Personal</th>
                        <th>Nombre</th>      
                        <th>Ci</cIte></th>
                        <th></th>                        
                      </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                        
                      ?>
                      <tr>
                        <td><?=$cod_personal;?></td>
                        <td><?=$personal;?></td>      
                        <td><?=$ci;?></td>
                          
                        <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1){
                          ?>
                            <a href='<?=$urlFormPersonal;?>&codigo=<?=$cod_personal;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
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
            <div class="card-footer ml-auto mr-auto">
              <a href="<?=$urlListPersonal;?>" class="<?=$buttonCancel;?>">Volver</a>
            </div>
          </div>          
  
        </div>
      </div>  
    </div>
</div>
