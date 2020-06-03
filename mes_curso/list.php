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
$stmt->execute();
$stmt->bindColumn('cod_estadomesestrabajo', $codEstadoMeses);
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_mes', $codMes);
$stmt->bindColumn('cod_gestion', $codGestion);


$stmtEstado = $dbh->prepare("SELECT * from estados_mesestrabajo");
$stmtEstado->execute();
$stmtEstado->bindColumn('codigo', $codigo_e);
$stmtEstado->bindColumn('nombre', $nombre_e);


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
                  <h4 class="card-title"><?=$moduleNamePlural?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive" id="data_comprobantes">
                    <table id="tablePaginator50" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Gestión</th>
                          <th >Mes</th>
                          <th class="text-right" width="10%"></th>
                          <th class="text-right" width="10%"></th>
                          <th class="text-right" width="10%"></th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php
						            $index=1;$cont=0;
                      	while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $nombreMes=strToUpper(strftime('%B',strtotime($nombreGestion."-".$codMes."-01")));
                        
                          $valor1="Pendiente";
                          $valor2="Cerrado";
                          $valor3="En Curso";
                          switch ($codEstadoMeses) {
                            case 1:                              
                              $estiloTipo2="btn btn-default";
                              $estiloTipo1="btn btn-warning";
                              $estiloTipo3="btn btn-default";
                              $textStyle="";                              
                              break;
                            case 2:                              
                              $estiloTipo1="btn btn-default";
                              $estiloTipo3="btn btn-default";
                              $estiloTipo2="btn btn-danger";
                              $textStyle="";                              
                              break;
                            case 3:
                              $estiloTipo3="btn btn-info";
                              $estiloTipo1="btn btn-default";
                              $estiloTipo2="btn btn-default";
                              $textStyle="text-info font-weight-bold";                              
                              break;
                            default:                             
                              $estiloTipo1="btn btn-default";
                              $estiloTipo2="btn btn-default";
                              $estiloTipo3="btn btn-default";
                              $textStyle="";                              
                              break;
                          }
                          ?>
                        <tr>
                          <td class="<?=$textStyle?>"align="center"><?=$index;?></td>
                          <td class="<?=$textStyle?>"><?=nameGestion($codGestion);?></td>
                          <td class="<?=$textStyle?>"><?=$nombreMes;?></td>
                          <td class="text-right"><a href="<?=$urlSave?>?codigo=<?=$codigo?>&estado=3" class="<?=$estiloTipo3?> btn-sm"><?=$valor3?></a></td>
                          <td class="text-right"><a href="<?=$urlSave?>?codigo=<?=$codigo?>&estado=1" class="<?=$estiloTipo1?> btn-sm"><?=$valor1?></a></td>
                          <td class="text-right"><a href="<?=$urlSave?>?codigo=<?=$codigo?>&estado=2" class="<?=$estiloTipo2?> btn-sm"><?=$valor2?></a></td>
                        </tr>
                        <?php
            							$index++;
            						               // }
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
