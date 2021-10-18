<?php
require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$query = "SELECT * from depreciaciones order by 3";
$statement = $dbh->query($query);

//asignaciones
// $query2 = "SELECT afs.*,af.activo,(select d.nombre from depreciaciones d where d.codigo=af.cod_depreciaciones) as nombreRubro,(select d.tipo_bien from tiposbienes d where d.codigo=af.cod_tiposbienes) as nombreBien,
// (select CONCAT_WS(' ',p.primer_nombre,p.paterno,p.materno) from personal p where p.codigo=afs.cod_personal) as nombre_personal,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=afs.cod_unidadorganizacional)as nombre_uo FROM activofijos_asignaciones afs, activosfijos af where afs.cod_activosfijos=af.codigo and af.codigo  = ".$codigo_af;
// $statement2 = $dbh->query($query2);
//unidad
$queryUO = "SELECT * from unidades_organizacionales order by 2";
$statementUO = $dbh->query($queryUO);
$statementUO2 = $dbh->query($queryUO);


$fechaDesde=date("Y-m-d");
?>

<div class="content">
  <div class="container-fluid">
    <div class="col-md-12">
      <form id="form1" class="form-horizontal" action="reportes_activosfijos/imp.php" method="post" target="_blank">
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title">Impresión de Constancia</h4>
          </div>
        </div>
        <div class="card-body ">
          <center><h4 class='text-muted font-weight-bold'>DATOS ORIGEN</h4></center>
          <div class="row">
                      <label class="col-sm-2 col-form-label">Oficina Origen</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                            <select id="cod_uodesde" name="cod_uodesde" class="selectpicker form-control form-control-sm" data-style="btn btn-info" onChange="ajaxPersonalUbicacionTrasferDesde(this);" data-show-subtext="true" data-live-search="true" required="true">
                            <option value=""></option>
                            <?php while ($row = $statementUO2->fetch()){ ?>
                              <option  value="<?=$row["codigo"];?>"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>

                                <!-- <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option> -->
                            <?php } ?> 
                            </select>

                        </div>
                      </div>
                    </div><!--fin campo unidad-->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Area Origen</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <div id="div_contenedor_areaDesde">
                            

                          </div>
                            
                        </div>
                      </div>
                    </div><!--fin campo area -->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Responsable Origen</label>
                      <div class="col-sm-7">
                      <div class="form-group">
                          <div id="div_personal_UODesde">
                            
                          </div>
                      </div>
                      </div><!--fin campo cod_responsables_responsable -->
                   </div>
                   <center><h4 class='text-muted font-weight-bold'>DATOS DESTINO</h4></center>
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Oficina Destino</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                            <select id="cod_uo" name="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" onChange="ajaxPersonalUbicacionTrasfer(this);" data-show-subtext="true" data-live-search="true" required="true">
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
                      <label class="col-sm-2 col-form-label">Area Destino</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <div id="div_contenedor_area">
                            

                          </div>
                            
                        </div>
                      </div>
                    </div><!--fin campo area -->
                    <div class="row">
                      <label class="col-sm-2 col-form-label">Responsable Destino</label>
                      <div class="col-sm-7">
                      <div class="form-group"> 
                          <div id="div_personal_UO">
                            
                          </div>
                       </div>
                      </div>
                   </div> 
                    <br>
                   <div class="row">
                      <label class="col-sm-2 col-form-label">Del</label>
                      <div class="col-sm-3">
                      <div class="form-group">
                          <input type="date" name="desde" value='<?=$fechaDesde?>' class="form-control">
                      </div>
                      </div><!--fin campo cod_responsables_responsable -->
                      <label class="col-sm-1 col-form-label">Al</label>
                      <div class="col-sm-3">
                      <div class="form-group">
                          <input type="date" name="hasta" value='<?=$fechaDesde?>' class="form-control">
                      </div>
                      </div><!--fin campo cod_responsables_responsable -->
                   </div>
        </div>
        <div class="card-footer ml-auto mr-auto">
          <button type="submit" class="<?=$buttonNormal;?>">Generar</button>
        </div>
      </div>
      </form>
    </div>
  </div>
</div>