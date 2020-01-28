<?php
require_once 'conexion.php';
require_once 'functionsGeneral.php';
require_once 'functions.php';
require_once 'configModule.php';
require_once 'styles.php';
$globalAdmin=$_SESSION["globalAdmin"];
$mes=$_SESSION["globalMes"];
$codGestionGlobal=$_SESSION["globalGestion"];
$nombreGestion=$_SESSION['globalNombreGestion'];
$fechaActual=date("Y-m-d");
setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare('SELECT esp.codigo,es.nombre as evento,es.observaciones,concat(p.primer_nombre," ",p.otros_nombres," ",p.paterno," ",p.materno) as personal,email_empresa from eventos_sistemapersonal esp join eventos_sistema es on esp.cod_eventosistema=es.codigo 
  join personal p on p.codigo=esp.cod_personal where esp.cod_estadoreferencial=1');
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigoX);
$stmt->bindColumn('evento', $eventoX);
$stmt->bindColumn('personal', $personalX);
$stmt->bindColumn('email_empresa', $emailX);
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">mail</i>
                  </div>
                  <h4 class="card-title"><?=$moduleNameSingular?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive" id="data_comprobantes">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Evento</th>
                          <th>Personal</th>
                          <th>Correo</th>
                          <th class="text-right" width="10%">Quitar</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						     $index=1;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $valor="Eliminar";
?>
                        <tr>
                          <td class=""align="center"><?=$index;?></td>
                          <td class=""><?=$eventoX;?></td>
                          <td class=""><?=$personalX;?></td>
                          <td class=""><?=$emailX;?></td>
                          <td class="text-right"><a onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDelete?>&cod=<?=$codigoX?>')" href="#" title="Eliminar" class="btn btn-danger btn-fab btn-sm"><i class="material-icons">delete</i></a></td>
                        </tr>
<?php
							$index++;
                      }
?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <?php
              if($globalAdmin==1){
              ?>
              <div class="card-footer fixed-bottom">
                    <a class="<?=$buttonNormal;?>" href="<?=$urlRegister;?>" target="_blank">Registrar</a>
                    <a class="btn btn-warning" href="<?=$urlRegister2;?>" target="_blank">Notificacion Particular</a>
              </div>
              <?php
              }
              ?>
              </div>
            </div>
          </div>  
        </div>
    </div>
