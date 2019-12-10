<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';



$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalAdmin=$_SESSION["globalAdmin"];

//asignaciones
$query2 = "SELECT * FROM activosfijos where codigo = ".$codigo;
$statementAF = $dbh->query($query2);
//unidad
$queryUO = "SELECT * from unidades_organizacionales order by 2";
$statementUO = $dbh->query($queryUO);

//unidad
$queryAREA = "SELECT * from areas order by 2";
$statementArea = $dbh->query($queryAREA);


$responsable='';
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="<?=$urlSaveReevaluoAF;?>" method="post"  enctype="multipart/form-data">
                <div class="card">
                  <div class="card-header <?=$colorCard;?> card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons"><?=$iconCard;?></i>
                    </div>
                    <h4 class="card-title">Revalorizar Activos Fijos</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table">
                          <thead>
                              <tr>
                                <th>CódigoAF</th>
                                <th>Nombre</th>
                                <th>Fecha Revaluo</th>
                                <th>V. Neto</th>
                                <th>V. Residual</th>
                                <th>Vida Util.</th>
                                <th>Tiempo Vida Res.</th>
                                 
                              </tr>
                          </thead>
                          <tbody>
                          <?php $index=1;
                              while ($row = $statementAF->fetch()) { 
                                  $codigo=$row["codigo"];
                                  $activo=$row["activo"];
                                  $fecha_reevaluo=$row["fecha_reevaluo"];
                                  $valorinicial=$row["valorinicial"];
                                  $valorresidual=$row["valorresidual"];
                                  $vida_util=$row["vidautilmeses"];
                                  $vidautilmeses_restante=$row["vidautilmeses_restante"];
                                }?>
                             <tr>
                                <td><?=$codigo;?></td>
                                <td><?=$activo;?></td>
                                  <td><?=$fecha_reevaluo;?></td>
                                  <td><?=$valorinicial;?></td>                       
                                  <td><?=$valorresidual;?></td>
                                  <td><?=$vida_util;?></td>
                                  <td><?=$vidautilmeses_restante;?></td>
                                  
                              </tr>
                          
                          </tbody>
                      </table>
                    </div>
                  </div><!--card body-->
                  <div class="card-footer fixed-bottom">
                      <button type="submit" class="<?=$buttonNormal;?>">guardar</button>
                      <a href="?opcion=activosfijosLista" class="<?=$buttonCancel;?>">Cancelar</a>
                  </div>
                </div> 

                <div class="card">
                  <div class="card-header <?=$colorCard;?> card-header-text">
                    <div class="card-text">
                      <h4 class="card-title">Revalorizar:</h4>
                    </div>
                  </div>
                  <div class="card-body ">                    
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Codigo Activo</label>
                      <div class="col-sm-4">
                          <div class="form-group">
                              <input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="codigoactivo" id="codigoactivo" required="true"  value="<?=$codigo;?>"/>
                          </div>
                      </div>
                    </div>
    
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Monto Del Reevaluo</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <input type="text" style="padding-left:20px" class="form-control" name="monto_reevaluo" id="monto_reevaluo" required="true"  />
                            

                        </div>
                      </div>
                    </div><!--fin monto reevaluo-->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Cantidad De Meses Restante</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <input type="text"  style="padding-left:20px" class="form-control" name="meses_restantes" id="meses_restantes" required="true"/>
                            
                        </div>
                      </div>
                    </div><!--fin meses restantes -->                    
                  </div>
                </div>

              </form>
            </div>

        </div>  
    </div>
</div>

