<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin = $_SESSION["globalAdmin"];

$dbh = new Conexion();


// $stmtDistrGastos = $dbh->prepare("SELECT codigo from distribucion_gastosporcentaje where estado=1");
// $stmtDistrGastos->execute();
// $result=$stmtDistrGastos->fetch();
// $codDistribucionGastos=$result['codigo'];
$codigo=$codigo;
if($codigo==0)
{
  $stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura from unidades_organizacionales where cod_estado=1");

  $stmt->execute();

  // $stmt->bindColumn('codDistribucion', $cod_distribucionDetalle);
  $stmt->bindColumn('codigo', $cod_unidad);
  $stmt->bindColumn('nombre', $unidad_nombre);
  $stmt->bindColumn('abreviatura', $unidad_abreviatura);

  $porcentaje=0;
  $nombre_d='';  
  ?>
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
        <form name="form1" id="form1" action="<?=$urlSaveRegisterDistribucionGastos;?>" method="POST">
        <input class="form-control" type="hidden" name="codigo" id="codigo" value="<?=$codigo?>"/>        
          <div class="card">
            <div class="card-header <?= $colorCard; ?> card-header-icon">
              <div class="card-icon">
                <i class="material-icons"><?= $iconCard; ?></i>
              </div>
              <h4 class="card-title">Registrar Nueva Distribución</h4>
              <!-- <h6 class="card-title">Por favor active la casilla para registrar el Area</h6> -->            
            </div>
            <div class="card-body">
              <div class="row">
                <label class="col-sm-2 col-form-label" style="color:#0B2161;font-size: 15px">Nombre Ditribución:</label>
                <div class="col-sm-7">
                <div class="form-group">
                  <input class="form-control" type="text" name="nombre_d" id="nombre_d" value="<?=$nombre_d?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                </div>
                </div>
              </div>
              <h6 class="card-title">Por favor ingrese los porcentajes</h6>

              <div class="table-responsive">

                
                <table class="table table-condensed">
                  <thead>
                    <tr>
                      <th class="text-left">#</th>
                      <th class="text-center">Unidad</th>
                      <th class="text-center">Abreviatura</th>
                      <th class="text-center">Porcentaje</th>
                      <!--th class="text-right">Actions</th-->
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                       $index = 1;
                       $sum=0;

                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          
                    ?>
                      <tr>
                        <td class="text-center"><?= $index; ?></td>
                        <td class="text-left"><?= $unidad_nombre; ?></td>
                        <td class="text-center"><?= $unidad_abreviatura; ?></td>
                        <td class="text-center">
                        <input type="hidden" name="cod_unidad[]"  value="<?=$cod_unidad;?>"/>
                        <input class="form-control" type="number" onchange="sumarPorcentaje()" id="porcentaje<?= $index; ?>" name="porcentaje[]"  required="true" value="<?= $porcentaje; ?>" onkeyup="sumarPorcentaje(); javascript:this.value=this.value.toUpperCase();"/>  

                        </td>
                      </tr>
                    <?php
                            $index++;
                           }
                    ?>
                  </tbody>
                  <tfoot>
                    <tr></tr>
                    <tr>
                    <td colspan="2"></td>
                    <th >Porcentaje Total (100 %) : </th>
                    <th><input type="text" required="true" id="total" name="total" value="<?= $sum; ?>" readonly="readonly" ></th>
                    </tr>
                  </tfoot>
                       </table>
                
              </div>
            </div>
          </div>
          <?php
                    if ($globalAdmin == 1) {
          ?>
            <div class="card-footer fixed-bottom">
            <button type="submit" id="botonGuardar" class="<?=$buttonCeleste;?>">Guardar</button>
            <a href='<?= $urlList; ?>'  rel="tooltip" class="<?=$buttonCancel;?>">Volver
                
            </a>
            </div>
          <?php
          }
          ?>
  </form>

        </div>
      </div>
    </div>
  </div>

  
<?php }else{
  $stmtDist = $dbh->prepare("SELECT nombre from distribucion_gastosporcentaje 
  where codigo=$codigo");
  $stmtDist->execute();
  $resultDist=$stmtDist->fetch();
  $nombre_d=$resultDist['nombre'];


  // $stmt = $dbh->prepare("SELECT dgd.codigo as codDistribucion,dgd.cod_unidadorganizacional as codUnidad,
  // (SELECT uo.nombre FROM unidades_organizacionales uo WHERE uo.codigo=dgd.cod_unidadorganizacional) as unidad_nomb,
  // (SELECT uo.abreviatura FROM unidades_organizacionales uo WHERE uo.codigo=dgd.cod_unidadorganizacional) as unidad_abrev,dgd.porcentaje
  // from distribucion_gastosporcentaje_detalle dgd,distribucion_gastosporcentaje dg
  // where dgd.cod_distribucion_gastos=dg.codigo and dg.codigo=$codigo");
  // $stmt->execute();

  // // $stmt->bindColumn('codDistribucion', $cod_distribucionDetalle);
  // $stmt->bindColumn('porcentaje', $porcentaje);
  // $stmt->bindColumn('codUnidad', $cod_unidad);
  // $stmt->bindColumn('unidad_nomb', $unidad_nombre);
  // $stmt->bindColumn('unidad_abrev', $unidad_abreviatura);

  ?>
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
          <form name="form1" id="form1" action="<?=$urlSaveRegisterDistribucionGastos;?>" method="POST">
          <input class="form-control" type="hidden" name="codigo" id="codigo" value="<?=$codigo?>"/>       
            <div class="card">
              <div class="card-header <?= $colorCard; ?> card-header-icon">
                <div class="card-icon">
                  <i class="material-icons"><?= $iconCard; ?></i>
                </div>
                <h4 class="card-title">Registrar Nueva Distribución</h4>
                <!-- <h6 class="card-title">Por favor active la casilla para registrar el Area</h6> -->            
              </div>
              <div class="card-body">
                <div class="row">
                  <label class="col-sm-2 col-form-label" >Nombre Ditribución:</label>
                  <div class="col-sm-7">
                  <div class="form-group">
                    <input class="form-control" type="text" name="nombre_d" id="nombre_d" value="<?=$nombre_d?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                  </div>
                  </div>
                </div>

              </div>
            </div>
            <?php
                      if ($globalAdmin == 1) {
            ?>
              <div class="card-footer fixed-bottom">
              <button type="submit" id="botonGuardar" class="<?=$buttonCeleste;?>">Guardar</button>
              <a href='<?= $urlList; ?>'  rel="tooltip" class="<?=$buttonCancel;?>">Volver
                  
              </a>
              </div>
            <?php
            }
            ?>
            </form>

          </div>
        </div>
      </div>
    </div>

  
<?php }


?>



