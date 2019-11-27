<?php

require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

//RECIBIMOS LA VARIABLE DE LA CUENTA 
$codigoCuentaPadre=$codigo;

$numeroCuentaPadre=obtieneNumeroCuenta($codigoCuentaPadre);
$nombreCuentaPadre=nameCuenta($codigoCuentaPadre);

// Preparamos
$sql="SELECT p.codigo, p.nombre, p.nro_cuenta, p.direccion, p.telefono, p.referencia1, p.referencia2, 
  (select b.nombre from bancos b where b.codigo=p.cod_banco)cod_banco FROM $table p where cod_estadoreferencial=1 and p.cod_cuenta='$codigoCuentaPadre' order by p.nombre";
//echo $sql;
$stmt = $dbh->prepare($sql);
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('nro_cuenta', $nroCuenta);
$stmt->bindColumn('direccion', $direccion);
$stmt->bindColumn('telefono', $telefono);
$stmt->bindColumn('referencia1', $referencia1);
$stmt->bindColumn('referencia2', $referencia2);
$stmt->bindColumn('cod_banco', $codBanco);

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
                  <h4 class="card-title"><?=$moduleNamePlural?></h4>
                  <h6 class="card-title"><?=$numeroCuentaPadre;?> <?=$nombreCuentaPadre;?></h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator50">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Codigo</th>
                          <th>Nombre</th>
                          <th>Banco</th>
                          <th>Nro. Cuenta</th>
                          <th>Direccion</th>
                          <th>Referencia 1</th>
                          <th>Referencia 2</th>
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
                          <td><?=$codigo;?></td>
                          <td><?=$nombre;?></td>
                          <td><?=$codBanco;?></td>
                          <td><?=$nroCuenta;?></td>
                          <td><?=$direccion;?></td>
                          <td><?=$referencia1;?></td>
                          <td><?=$referencia2;?></td>
                          <td class="td-actions text-right">
                            <?php
                            if($globalAdmin==1){
                            ?>
                            <a href='<?=$urlEdit;?>&codigo=<?=$codigo;?>&codigo_padre=<?=$codigoCuentaPadre?>' rel="tooltip" class="<?=$buttonEdit;?>">
                              <i class="material-icons"><?=$iconEdit;?></i>
                            </a>
                            <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete;?>&codigo=<?=$codigo;?>&codigo_padre=<?=$codigoCuentaPadre?>')">
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
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlRegister;?>&codigo=<?=$codigoCuentaPadre;?>'">Registrar</button>
              </div>		  
            </div>
          </div>  
        </div>
    </div>
