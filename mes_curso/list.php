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
$stmt = $dbh->prepare("SELECT * from meses_trabajo where cod_gestion='$codGestionGlobal'");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('cod_estadomesestrabajo', $codEstadoMeses);
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_mes', $codMes);
$stmt->bindColumn('cod_gestion', $codGestion);
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
                  <h4 class="card-title"><?=$moduleNameSingular?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive" id="data_comprobantes">
                    <table class="table">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Gestion</th>
                          <th>Mes</th>
                          <th>Estado</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						     $index=1;$cont=0;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $nombreMes=strToUpper(strftime('%B',strtotime($nombreGestion."-".$codMes."-01")));
                        if($codEstadoMeses!=2){
                          if($codEstadoMeses==3){
                           $valor="Mes en Curso"; 
                           $estiloTipo="btn btn-success";
                           $actionButton="";
                          }else{
                            $valor="Habilitar";
                            $estiloTipo="btn btn-default";
                            $actionButton=$urlSave."?codigo=".$codigo;
                          }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td><?=nameGestion($codGestion);?></td>
                          <td><?=$nombreMes;?></td>
                          <td class=""><a href="<?=$actionButton?>" class="<?=$estiloTipo?> btn-sm"><?=$valor?></a></td>
                        </tr>
<?php
							$index++;
						               }
                      }
?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <?php 
              if($cont>0){
      				?><div class="card-footer fixed-bottom">
                <input type="hidden" value="<?=$cont?>" id="numeroMoneda">
                <a href="#" onclick="guardarValoresMoneda()" class="btn btn-info">Guardar Valores</a>
              </div><?php		  
              }
              ?>
            </div>
          </div>  
        </div>
    </div>
