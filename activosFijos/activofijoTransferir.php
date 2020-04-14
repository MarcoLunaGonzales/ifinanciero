<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';



$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$codigo_af=$codigo;
$globalAdmin=$_SESSION["globalAdmin"];

//asignaciones
$query2 = "SELECT afs.*,af.activo,(select d.nombre from depreciaciones d where d.codigo=af.cod_depreciaciones) as nombreRubro,(select d.tipo_bien from tiposbienes d where d.codigo=af.cod_tiposbienes) as nombreBien,
(select CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) from personal p where p.codigo=afs.cod_personal) as nombre_personal,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=afs.cod_unidadorganizacional)as nombre_uo FROM activofijos_asignaciones afs, activosfijos af where afs.cod_activosfijos=af.codigo and af.codigo  = ".$codigo_af;
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
                                <th>CodAF</th>
                                <th>Nombre</th>
                                <th>QR</th>
                                <th>Imagen</th>
                                <th>Fecha Asignación</th>
                                <th>Estado</th>
                                <th>Responsable</th>
                                <th>Oficina</th>
                                 
                              </tr>
                          </thead>
                          <tbody>
                          <?php $index=1;
                              while ($row = $statement2->fetch()) { 
                                  $codigo=$row["codigo"];
                                  $cod_activosfijos=$row["cod_activosfijos"];
                                  $fechaasignacion=$row["fechaasignacion"];
                                  $estadobien_asig=$row["estadobien_asig"];
                                  $nombre_personal=$row["nombre_personal"];
                                  $nombre_uo=$row["nombre_uo"];
                                  $nombreActivo=$row["activo"];
                                  $nombreRubro=$row["nombreRubro"];
                                  $nombreBien=$row["nombreBien"];
                                  // $nombreUO=$row["nombreUO"];
                                  
                                }?>
                             <tr>
                                <td><?=$cod_activosfijos;?></td>
                                <td><small><?=$nombreActivo;?></small></td>
                                <td>
                                  <?php
                                  require 'assets/phpqrcode/qrlib.php';
                                  $dir = 'qr_temp/';
                                  if(!file_exists($dir)){
                                      mkdir ($dir);}
                                  $fileName = $dir.'test.png';
                                  $tamanio = 2.5; //tamaño de imagen que se creará
                                  $level = 'L'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                                  $frameSize = 1; //marco de qr
                                  $contenido = "Cod:".$cod_activosfijos."\nRubro:".$nombreRubro."\nDesc:".$nombreActivo."\nRespo.:".$nombre_uo.' - '.$nombre_personal;
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
                      <a href="?opcion=activosfijosLista" class="<?=$buttonCancel;?>"> <-- Volver </a>
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
                      <label class="col-sm-2 col-form-label">Código Activo</label>
                      <div class="col-sm-4">
                          <div class="form-group">
                              <input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="codigoactivo" id="codigoactivo" required="true"  value="<?=$codigo_af;?>"/>
                          </div>
                      </div>
                    </div>
    
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Oficina</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                            <select id="cod_uo" name="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-info" onChange="ajaxPersonalUbicacionTrasfer(this);" data-show-subtext="true" data-live-search="true" required="true">
                            <option value=""></option>
                            <?php while ($row = $statementUO->fetch()){ ?>
                              <option  value="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>

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
                          <div id="div_contenedor_area">
                            

                          </div>
                            
                        </div>
                      </div>
                    </div><!--fin campo area -->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Responsable</label>
                      <div class="col-sm-7">
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

