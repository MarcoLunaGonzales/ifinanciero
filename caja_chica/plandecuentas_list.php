<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$globalAdmin=$_SESSION["globalAdmin"];


$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
  (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas_cajachica p where cod_estadoreferencial=1 order by p.numero");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_padre', $codPadre);
$stmt->bindColumn('nivel', $nivel);
$stmt->bindColumn('cod_tipocuenta', $codTipoCuenta);
$stmt->bindColumn('cuenta_auxiliar', $cuentaAuxiliar);

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
                  <h4 class="card-title">Plan De Cuentas Caja Chica</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator50">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                          <th>Padre</th>
                          <th>Nivel</th>
                          <th>Tipo</th>
                          <th>Cuentas Auxiliares</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
            						$index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $nombre=formateaPlanCuenta($nombre, $nivel);
                          $linkAdd="";
                          if($nivel<5){
                            $linkAdd="<a href='".$urlRegisterCC2.$codigo."'><i class='material-icons' style='color:orange;' title='Registrar Hijo'>add_circle_outline</i></a>";
                          }

                          $imgCuentaAuxiliar="";
                          if($cuentaAuxiliar==1){
                            $imgCuentaAuxiliar="<a href='index.php?opcion=listCuentasAuxCC&codigo=$codigo' rel='tooltip' class='<?=$buttonCeleste;?>'><i class='material-icons' style='color:blue'>check_circle_outline</i></a>";
                          }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td><?=$numero;?></td>
                          <td><?=$nombre;?><?=$linkAdd;?></td>
                          <td><?=$codPadre;?></td>
                          <td><?=$nivel;?></td>
                          <td><?=$codTipoCuenta;?></td>
                          <td class="td-actions text-center"><?=$imgCuentaAuxiliar;?></td>
                          <td class="td-actions text-right">
                            <?php
                            if($globalAdmin==1){
                            ?>
                            <a href='<?=$urlEditCC;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteCC;?>&codigo=<?=$codigo;?>')">
                              <i class="material-icons"><?=$iconDelete;?></i>
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
              <!--div class="card-footer ml-auto mr-auto"-->
              <div class="card-footer fixed-bottom">
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegisterCC;?>'">Registrar</button>
              </div>		  
            </div>
          </div>  
        </div>
    </div>
