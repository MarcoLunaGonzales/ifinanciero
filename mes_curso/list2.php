<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';
$dbh = new Conexion();
$codGestionGlobal=$_SESSION["globalGestion"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$mes=$_SESSION['globalMes'];
?>

<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="mes_curso/save2.php" method="post">
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
                          <th>Mes</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td align="center">                            
                            <select class="selectpicker" title="Seleccione una opcion" name="mes" id="mes" data-style="<?=$comboColor;?>" required>
                              <option disabled selected value=""></option>
                              <?php
                              $stmt = $dbh->prepare("SELECT cod_mes,(select m.nombre from meses m where m.codigo=cod_mes)as nombre_mes from meses_trabajo where cod_gestion=$codGestionGlobal and cod_estadomesestrabajo<>2 order by cod_mes");
                              $stmt->execute();
                              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {                                
                                $cod_mes=$row['cod_mes'];                                
                                $nombre_mes=$row['nombre_mes'];     
                              ?>
                              <option <?=($mes==$cod_mes)?"selected":"";?> value="<?=$cod_mes;?>"><?=$nombre_mes;?></option>
                              <?php 
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
