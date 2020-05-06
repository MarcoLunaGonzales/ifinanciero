<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$globalAdmin=$_SESSION["globalAdmin"];


$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT pcc.cod_cuenta,pc.numero,pc.nombre,pcc.cod_cuentapasivo from solicitud_recursoscuentas pcc,plan_cuentas pc 
where pcc.cod_cuenta=pc.codigo");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('cod_cuenta', $cod_cuenta);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_cuentapasivo', $cod_cuentapasivo);
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
                  <h4 class="card-title">Plan De Cuentas para Solicitud Recursos</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator50">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                          <th>Pasivo</th>                          
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
            						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $tienePasivo=nameCuenta($cod_cuentapasivo);
                          if($tienePasivo=="0"){
                            $tituloCuentaPasivo="Sin Pasivo";
                          }else{
                            $tituloCuentaPasivo="[".obtieneNumeroCuenta($cod_cuentapasivo)."] ".nameCuenta($cod_cuentapasivo);
                          }

                        ?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="font-weight-bold">[<?=$numero;?>]</td>
                          <td><?=$nombre;?></td>
                          <td class="font-weight-bold"><?=$tituloCuentaPasivo?></td>
                          <td class="td-actions text-right">
                            <a title="Pasivo" target="_blank" href='<?=$urlCambiarPasivo?>&cod=<?=$cod_cuenta?>' class="btn btn-warning">
                              <i class="material-icons">playlist_add</i>
                            </a>
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
              <!--div class="card-footer ml-auto mr-auto"-->
              <div class="card-footer fixed-bottom">
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegisterSS;?>'">Registrar</button>
              </div>		  
            </div>
          </div>  
        </div>
    </div>
