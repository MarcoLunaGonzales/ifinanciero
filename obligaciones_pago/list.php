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
$lista=listaObligacionesPagoDetalleSolicitudRecursos();
?>

<div class="content">
	<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons text-dark">history</i>
                  </div>
                  <h4 class="card-title"><?=$moduleNameSingular?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive" id="data_comprobantes">
                    <table id="tablePaginator" class="table table-condensed">
                      <thead>
                        <tr>
                          <th class="text-center">#</th>
                          <th>Unidad</th>
                          <th>Area</th>
                          <th>Responsable</th>
                          <th>Fecha Solicitud</th>
                          <th>Detalle</th>
                          <th class="bg-warning text-dark">Importe</th>
                          <th>Proveedor</th>
                          <th class="bg-info text-dark">D&iacute;as Cr&eacute;dito</th>
                          <th class="text-right">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
<?php
						     $index=1;$cont=0;

                      	while ($row = $lista->fetch(PDO::FETCH_ASSOC)) {
                          $unidad=$row['unidad'];
                          $area=$row['area'];
                          $solicitante=namePersonal($row['cod_personal']);
                          $fecha=$row['fecha'];
                          $detalle=$row['detalle'];
                          $importe=$row['importe'];
                          $proveedor=$row['proveedor'];

                          $dias=obtenerCantidadDiasCredito($row['cod_proveedor']);
                          if($dias==0){
                            $tituloDias="Sin Registro";
                          }else{
                            $tituloDias="".$dias;
                          }
?>
                        <tr>
                          <td align="center"><?=$index;?></td>                          
                          <td><?=$unidad;?></td>
                          <td><?=$area;?></td>
                          <td>
                                 <img src="assets/img/faces/persona1.png" width="20" height="20"/><?=$solicitante;?>
                          </td>
                          <td><?=strftime('%d/%m/%Y',strtotime($fecha));?></td>
                          <td class="text-left"><?=$detalle;?></td>
                          <td class="bg-warning text-dark text-right font-weight-bold" style="font-size:20px"><?=number_format($importe,2,",",".")?></td>
                          <td class="text-left"><?=$proveedor;?></td>
                          <td class="text-right bg-info text-dark font-weight-bold"><?=$tituloDias;?></td>
                          <td class="text-right"><a href="#" class="btn btn-success btn-sm"><i class="material-icons">attach_money</i> Pagar</a></td>
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
            </div>
          </div>  
        </div>
    </div>
