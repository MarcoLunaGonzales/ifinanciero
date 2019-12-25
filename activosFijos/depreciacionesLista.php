<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];


$dbh = new Conexion();

//combo
$_POST["cod_empresa"] = 1;


// Preparamos
//echo $table;
//$stmt = $dbh->prepare("select * from depreciaciones where cod_empresa = ".$_POST["cod_empresa"].";");
$stmt = $dbh->prepare("SELECT d.codigo, d.cod_empresa, d.nombre, d.abreviatura,d.vida_util, d.cod_estado, 
  (SELECT v.codigocuenta from v_af_cuentacontablepararubros v where v.codigo=d.cod_cuentacontable)as codigocuenta,
  (SELECT v.cuentacontable from v_af_cuentacontablepararubros v where v.codigo=d.cod_cuentacontable)as cuentacontable
   from depreciaciones d where d.cod_estado=1 ");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_empresa', $cod_empresa);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('vida_util', $vida_util);
$stmt->bindColumn('cod_estado', $cod_estado);

$stmt->bindColumn('codigocuenta', $codigocuenta);
$stmt->bindColumn('cuentacontable', $cuentacontable);
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
                  <h4 class="card-title"><?=$moduleNamePlural4?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table"  id="tablePaginator">
                    <thead>
                            <tr>
                              
                                <th>NOMBRE</th>
                                <th>ABREVIATURA</th>
                                <th>VIDA UTIL (Meses)</th>
                                <th>CUENTA CONTABLE</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { ?>
                            <tr>
                                <td><?=$nombre;?></td>
                                <td class="text-center"><?=$abreviatura;?></td>
                                <td class="text-center"><?=$vida_util;?></td>
                                <td><?=$codigocuenta;?> - <?=$cuentacontable;?></td>                                
                                <td  class="td-actions text-right">
                                <?php
                                  if($globalAdmin==1){
                                      ?>
                                      <a href='<?=$urlRegistrar_depreciacion;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                      <i class="material-icons"><?=$iconEdit;?></i>
                                      </a>
                                      <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteDepr;?>&codigo=<?=$codigo;?>')">
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
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegistrar_depreciacion;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
