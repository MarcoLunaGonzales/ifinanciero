<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();
$globalUnidad=$_SESSION['globalUnidad'];

?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="utilitarios/saveUnidadOrganizacional.php" method="post">
              <div class="card">
                <div class="card-header card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">assignment</i>
                  </div>
                  <h4 class="card-title">Cambiar Oficina de trabajo</h4>
                </div>
                
                <div class="card-body">
                  <div class="">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Oficina</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td align="center">
                            <select class="selectpicker" title="Seleccione una opcion" name="oficina" id="oficina" data-style="<?=$comboColor;?>" required>
                              <option disabled selected value="">--SELECCIONE UNA OFICINA--</option>
                              <?php
                              $stmt = $dbh->prepare("SELECT g.codigo, g.nombre FROM unidades_organizacionales g where g.cod_estado=1 order by 2 desc");
                              $stmt->execute();
                              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $codigoX=$row['codigo'];
                                $nombreX=$row['nombre'];
                                $abrevX=$row['abreviatura'];
                                if($globalUnidad==$codigoX){
                                  ?>
                              <option value="<?=$codigoX;?>" selected><?=$nombreX;?></option>
                              <?php  
                                }else{
                                  ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?></option>
                                  <?php 
                                }
                              
                              }
                                ?>
                            </select>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="card-body">
                    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
              </div>
               </form>
            </div>
          </div>  
        </div>
    </div>
