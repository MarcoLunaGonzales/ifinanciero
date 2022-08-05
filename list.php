<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];


$dbh = new Conexion();

// Preparamos
$sqlList="SELECT codigo, nombre, abreviatura, nivel, () FROM $table where cod_estado=1 order by 2";
$stmt = $dbh->prepare($sqlList);
//echo $sqlList;
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('abreviatura', $abreviatura);
$stmt->bindColumn('nivel', $nivel);

?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <h4 class="card-title"><?=$moduleNamePlural?></h4>
                </div>

                <div class="card-body">
                  <div class="table-responsive">
                  <table id="example" class="table table-striped table-bordered" style="width:100%">
                  <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Nombre</th>
                          <th>Abreviatura</th>
                          <th>Nivel</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td><?=$nombre;?></td>
                          <td><?=$abreviatura;?></td>
                          <td><?=$nivel;?></td>
                          <td class="td-actions text-right">
                            <?php
                            if($globalAdmin==1){
                            ?>
                            <a href='<?=$urlEdit;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="bx bx-edit"></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>')">
                              <i class="bx bx-trash"></i>
                            </button>
                            <?php
                            }
                            ?>
                          </td>
                        </tr>
<?php
							$index++;
						}
?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <?php
              if($globalAdmin==1){
              ?>
      				<div class="card-footer ml-auto mr-auto">
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegister;?>'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
