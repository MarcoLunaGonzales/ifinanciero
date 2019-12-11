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
$query2 = "SELECT * FROM v_activosfijos_asignaciones where codigo = ".$codigo;
$statement2 = $dbh->query($query2);
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
              <form id="form1" class="form-horizontal" action="<?=$urlSaveTransfer;?>" method="post"  enctype="multipart/form-data">
                <div class="card">
                  <div class="card-header <?=$colorCard;?> card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons"><?=$iconCard;?></i>
                    </div>
                    <h4 class="card-title">Transferencia De Activos Fijos</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table">
                          <thead>
                              <tr>
                                <th>CódigoAF</th>
                                <th>Nombre</th>
                                <th>QR</th>
                                <th>Imagen</th>
                                <th>Fecha Asignación</th>
                                <th>Estado</th>
                                <th>Responsable</th>
                                <th>UO</th>
                                 
                              </tr>
                          </thead>
                          <tbody>
                          <?php $index=1;
                              while ($row = $statement2->fetch()) { 
                                  $codigo=$row["codigo"];
                                  $fechaasignacion=$row["fechaasignacion"];
                                  $estadobien_asig=$row["estadobien_asig"];
                                  $nombre_personal=$row["nombre_personal"];
                                  $nombre_uo=$row["nombre_uo"];
                                  $nombreActivo=$row["activo"];
                                }?>
                             <tr>
                                <td><?=$codigo;?></td>
                                <td><?=$nombreActivo;?></td>
                                <td>
                                  <?php
                                  require 'assets/phpqrcode/qrlib.php';
                                  $dir = 'qr_temp/';
                                  if(!file_exists($dir)){
                                      mkdir ($dir);}
                                  $fileName = $dir.'test.png';
                                  $tamanio = 4; //tamaño de imagen que se creará
                                  $level = 'Q'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                                  $frameSize = 1; //marco de qr
                                  $contenido = $codigo;
                                  QRcode::png($contenido, $fileName, $level,$tamanio,$frameSize);
                                  echo '<img src="'.$fileName.'"/>';
                                  ?>
                                </td>
                                <td class="text-right small">
                                  <img src="<?=$archivo;?>" alt="..." style="width:200px;">
                                </td>
                                <td><?=$fechaasignacion;?></td>
                                <td><?=$estadobien_asig;?></td>
                     
                                <td><?=$nombre_personal;?></td>
                                <td><?=$nombre_uo;?></td>
                                  
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
                      <h4 class="card-title">Transferir A:</h4>
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
                      <label class="col-sm-2 col-form-label">Unidad Organizacional</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                            <select id="cod_uo" name="cod_uo" class="selectpicker " data-style="btn btn-info" onChange="ajaxPersonalUbicacionTrasfer(this);">
                            <?php while ($row = $statementUO->fetch()){ ?>
                              <option  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>

                                <!-- <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option> -->
                            <?php } ?> 
                            </select>

                        </div>
                      </div>
                    </div><!--fin campo unidad-->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Area</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                            <select id="cod_area" name="cod_area" class="selectpicker " data-style="btn btn-info">
                            <?php while ($row = $statementArea->fetch()){ ?>
                                <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                            <?php } ?> 
                            </select>
                        </div>
                      </div>
                    </div><!--fin campo area -->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Responsable</label>
                      <div class="col-sm-4">
                      <div class="form-group">
                          <div id="div_personal_UO">

                          </div>
                      </div>
                      </div><!--fin campo cod_responsables_responsable -->
                    
                  </div>
                </div>

              </form>
            </div>

        </div>  
    </div>
</div>

