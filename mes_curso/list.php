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
                <div class="card-header card-header-info card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">build</i>
                  </div>
                  <h4 class="card-title"><?=$moduleNameSingular?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive" id="data_comprobantes">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Gestion</th>
                          <th>Mes</th>
                          <th class="text-right" width="10%">Estado</th>
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
                           $estiloTipo="btn btn-info";$textStyle="text-info font-weight-bold";
                           $actionButton="";
                          }else{
                            $valor="Habilitar";
                            $estiloTipo="btn btn-default";$textStyle="";
                            $actionButton=$urlSave."?codigo=".$codigo;
                          }
?>
                        <tr>
                          <td class="<?=$textStyle?>"align="center"><?=$index;?></td>
                          <td class="<?=$textStyle?>"><?=nameGestion($codGestion);?></td>
                          <td class="<?=$textStyle?>"><?=$nombreMes;?></td>
                          <td class="text-right"><a href="<?=$actionButton?>" class="<?=$estiloTipo?> btn-sm"><?=$valor?></a></td>
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
