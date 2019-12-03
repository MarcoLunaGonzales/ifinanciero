<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';


setlocale(LC_TIME, "Spanish");
$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$codigo=$_GET['cod'];

$nombreRetencion=nameRetencion($codigo);
$porRetencion=porcentRetencion($codigo);
$contadorRegistros=0;
?>
<script>
	numFilas=<?=$contadorRegistros;?>;
	cantidadItems=<?=$contadorRegistros;?>;
</script>

<?php
$fechaActual=date("Y-m-d");
$dbh = new Conexion();

  $i=0;
  echo "<script>var array_cuenta=[],imagen_cuenta=[];</script>";
   $stmtCuenta = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre from plan_cuentas p where p.nivel=5 order by p.numero");
   $stmtCuenta->execute();
   while ($rowCuenta = $stmtCuenta->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$rowCuenta['codigo'];
    $numeroX=$rowCuenta['numero'];
    $nombreX=$rowCuenta['nombre'];
    ?>
    <script>
     var obtejoLista={
       label:'[<?=trim($numeroX)?>] - <?=trim($nombreX)?>',
       value:'<?=$codigoX?>'};
       array_cuenta[<?=$i?>]=obtejoLista;
    </script> 
    <?php
    $i=$i+1;
  }

?>
<div class="content">
	<div id="">
		<div class="fondo-imagen2"></div>
    <div class="container-fluid">
       <div class="row">
        <div class="card col-sm-6">
                <div class="card-header card-header-info card-header-text">
                  <div class="card-text">
                    <h6 class="card-title">Configuracion</h6>
                  </div>
                </div>
                <div class="card-body ">
                  <form action="saveEdit.php" autocomplete="off" method="post">
                    <div class="row">
                       <label class="col-sm-2 col-form-label">Nombre:</label>
                       <div class="col-sm-4">
                        <div class="form-group has-success">
                          <input type="hidden" name="codigo" id="codigo" value="<?=$codigo?>">
                          <input class="form-control" rel="tooltip" readonly title="Edite el nombre de la Retención Padre (No tocar si no desea editar el nombre)" value="<?=$nombreRetencion?>" type="text" name="nombre_retencion" id="nombre_retencion" required/>
                        </div>
                        </div>
                        <label class="col-sm-4 col-form-label">Cuenta Origen:</label>
                       <div class="col-sm-2">
                        <div class="form-group has-success">
                          <input class="form-control" rel="tooltip" readonly value="<?=$porRetencion?>" type="number" name="cuenta_origen" id="cuenta_origen" required/>
                          <span class="form-control-feedback">
                            %
                          </span>
                        </div>
                        </div>
                      </div>
                      <hr>
                      <div class="card-title text-info"><center><h3><b>Agregar detalle</b></h3></center></div>
                      <br>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Valor Porcentaje:</label>
                       <div class="col-sm-3">
                         <div class="form-group has-success">
                          <input class="form-control" type="number" step="0.01" value="" name="porcentaje" id="porcentaje" step="0.0001" required autofocus/>
                          <span class="form-control-feedback">
                            %
                          </span>
                         </div>
                        </div>
                        <label class="col-sm-2 col-form-label">Tipo:</label>
                         <div class="col-sm-4">
                           <div class="form-group">
                                <select class="selectpicker form-control form-control-sm" name="credito" id="credito" data-style="<?=$comboColor;?>" required>
                                  <option value="">--seleccione--</option>
                                  <option value="1">Debe</option>
                                  <option value="2">Haber</option> 
                                </select>
                              </div>
                            </div>  
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Glosa:</label>
                       <div class="col-sm-9">
                        <div class="form-group has-success">
                          <input class="form-control"  type="text" name="glosa_retencion" id="glosa_retencion"/>
                        </div>
                        </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-3 col-form-label">Cuenta</label>
                       <div class="col-sm-9">
                         <div class="form-group has-success">
                           <input class="form-control" type="text" name="cuenta_auto" id="cuenta_auto" placeholder="[numero] y nombre de cuenta (defecto:Cuenta vacía)"/>
                           <input class="form-control" type="hidden" name="cuenta_auto_id" id="cuenta_auto_id"/>
                         </div>
                        </div>
                     </div>
                      <hr>
                      <!--<div class="form-group float-left">
                        <a href="#" class="btn btn-info btn-round" onclick="editarRetencionNombre()">Editar Retencion</a>                        
                      </div>-->
                      <div class="form-group float-right">
                        <a href="../index.php?opcion=configuracionDeRetenciones" class="btn btn-default btn-round">Atras</a>      
                        <button type="submit" class="btn btn-info btn-round" onclick="">Guardar</button>
                      </div>
             </form>    
        </div>  
      </div>
      <div class="col-sm-6">
        <div class="card">
               <div class="card-header card-header-default card-header-text">
                  <div class="card-text">
                    <h6 class="card-title">Listas de registros</h6>
                  </div>
                </div>
                <div class="card-body" id="tabla_detalle_retencion">
                  <table class="table table-striped table-condensed table-bordered">
                     <thead>
                       <tr>
                         <th>#</th>
                         <th>Cuenta</th>
                         <th>%</th>
                         <th>Debe</th>
                         <th>Haber</th>
                         <th>Glosa</th>
                         <th>Action</th>
                       </tr>
                     </thead>
                     <tbody>
                   <?php 
                   $index=1;
                   $stmt = $dbh->prepare("SELECT cd.* from configuracion_retenciones c join configuracion_retencionesdetalle cd on cd.cod_configuracionretenciones=c.codigo where cd.cod_configuracionretenciones=$codigo order by cd.codigo");
                         $stmt->execute();
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if($row['cod_cuenta']==0){
                              $cuenta="Sin cuenta";
                            }else{
                              $numeroX=obtieneNumeroCuenta($row['cod_cuenta']);
                              $cuentaX=nameCuenta($row['cod_cuenta']);
                              $cuenta="[".$numeroX."] ".$cuentaX;
                            }                          

                          $codigoX=$row['codigo'];
                          
                          $porcentajeX=$row['porcentaje'];
                          
                          $glosaX=$row['glosa'];
                          $debehaberX=$row['debe_haber'];
                          if($debehaberX==1){
                            $debe="<i class='material-icons text-info'>check_circle_outline</i>";
                            $haber="";
                          }else{
                            $debe="";
                            $haber="<i class='material-icons text-info'>check_circle_outline</i>";
                          }
                        ?>
                         <tr>
                           <td><?=$index?></td>
                           <td><?=$cuenta?></td>
                           <td class="font-weight-bold"><?=$porcentajeX?></td>
                           <td><?=$debe?></td>
                           <td><?=$haber?></td>
                           <td><?=$glosaX?></td>
                           <td><a href="#" onclick="borrarRetencionDetalle(<?=$codigoX?>)" class="btn btn-danger btn-link"><i class='material-icons'>clear</i></a></td>
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
  </div><!--fin div container-->
   </div><!--div nueva plantilla-->
</div>

<!-- small modal -->
<div class="modal fade modal-mini modal-primary" id="modalAlert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div id="modalAlertStyle" class="modal-content">
      <div class="modal-header">
        <i class="material-icons" data-notify="icon">notifications_active</i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <div id="msgError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-white btn-link" data-dismiss="modal">Aceptar
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->

