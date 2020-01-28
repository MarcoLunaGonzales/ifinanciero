<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();


$stmt = $dbh->prepare(" SELECT pr.codigo,p.codigo as cod_personal,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) as personal,p.identificacion,p.cod_lugar_emision,
 (select r.nombre from tipos_retiro_personal r where r.codigo=pr.cod_tiporetiro) as cod_tiporetiro,p.ing_contr,pr.fecha_retiro,pr.observaciones
 from personal_retiros pr,personal p
 where pr.cod_personal=p.codigo and pr.cod_estadoreferencial=1
 order by personal");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo_retiro);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('identificacion', $ci);
$stmt->bindColumn('cod_lugar_emision', $ci_lugar_emision);
$stmt->bindColumn('personal', $personal);
$stmt->bindColumn('ing_contr', $fecha_ingreso);
$stmt->bindColumn('fecha_retiro', $fecha_retiro);
$stmt->bindColumn('cod_tiporetiro', $cod_tiporetiro);
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
              
              <h4 class="card-title" >Personal Fuera de planillas</h4>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table" id="tablePaginator">
                  <thead>
                      <tr>
                        
                        <th>Cod Personal</th>
                        <th>Nombre</th>      
                        <th>Ci</cIte></th>
                        <th>F.Ingreso</th>
                        <th>F.Retiro</th>
                        <th>Tipo Retiro</th>
                        <th>Observaciones</th>                        
                        <th></th>
                        <th></th>                        
                      </tr>
                  </thead>
                  <tbody>
                    <?php $index=1;
                    while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                        
                      ?>
                      <tr>
                        <td><?=$cod_personal?></td>
                        <td><?=$personal;?></td>      
                        <td><?=$ci;?>-<?=$ci_lugar_emision;?></td>
                        <td><?=$fecha_ingreso;?></td>
                        <td><?=$fecha_retiro;?></td>
                        <td><?=$cod_tiporetiro;?></td>
                        <td><?=$observaciones;?></td>                        
                        <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1 ){
                          ?>
                            <!-- <a href='<?=$urlFormPersonalContratos;?>&codigo=<?=$codigo;?>' rel="tooltip" class="btn btn-info">
                              <i class="material-icons" title="Contratos">assignment</i>
                            </a> -->
                            <?php
                              }?>
                        </td>
                          
                        <td class="td-actions text-right">
                          <?php
                            if($globalAdmin==1){
                          ?>
                            <a href='<?=$urlFormPersonalRetiros;?>&codigo=<?=$cod_personal;?>' rel="tooltip" class="<?=$buttonEdit;?>">
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
