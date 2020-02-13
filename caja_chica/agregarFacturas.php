<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();
setlocale(LC_TIME, "Spanish");
set_time_limit(0);
//$codigo_rendicion=$codigo;


$cod_cc=$cod_cc;
$cod_tcc=$cod_tcc;
$cod_dcc=$codigo;

$stmt = $dbh->prepare("SELECT monto,observaciones,fecha from caja_chicadetalle where codigo=$cod_dcc");
//ejecutamos
$stmt->execute();
$result=$stmt->fetch();
$monto_a_rendir=$result['monto'];
$observaciones=$result['observaciones'];
$fecha_ccd=$result['fecha'];
$monto_rendicion=0;

$contadorRegistros=0;
?>

<script>
  numFilas=<?=$contadorRegistros;?>;
  cantidadItems=<?=$contadorRegistros;?>;
  var distribucionPor=[];
  var configuracionCentro=[];
  var configuraciones=[];
  var estado_cuentas=[];
</script>

<form id="formRegComp" class="form-horizontal" action="caja_chica/agregarFacturas_save.php" method="post" enctype="multipart/form-data">
<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
            
              <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
              <input type="hidden" name="cod_cajachica" id="cod_cajachica" value="<?=$cod_cc;?>">
              <input type="hidden" name="cod_cajachicadetalle" id="cod_cajachicadetalle" value="<?=$cod_dcc;?>">


              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Registrar Facturas</h4>
                  
                </div>
                <div class="card-body">

                	<div class="row">
                        <label class="col-sm-1 col-form-label">Monto Rendición</label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <!-- <input class="form-control" type="number" step="any" name="monto_total" id="monto_total" value="<?=number_format($monto_rendicion, 2, '.', ',');?>" readonly="readonly"/> -->

                                <input class="form-control" type="number" name="monto_total" placeholder="0" id="monto_total" readonly="true">

                            </div>
                        </div>
                        <label class="col-sm-1 col-form-label">Monto Faltante</label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input class="form-control" name="monto_faltante" id="monto_faltante" readonly="readonly"/>
                            </div>
                        </div>
                        <label class="col-sm-1 col-form-label">Monto a Rendir</label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input class="form-control"  name="monto_a_rendir" id="monto_a_rendir" value="<?=number_format($monto_a_rendir, 2, '.', ',');?>" readonly="readonly"/>
                            </div>
                        </div>
                    </div><!--montos -->
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Detalle General</label>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input class="form-control" type="text" name="observaciones" id="observaciones" value="<?=$observaciones;?>" readonly="readonly"/>
                            </div>
                        </div>
                        <label class="col-sm-2 col-form-label">Fecha Solicitud</label>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input class="form-control" type="text" name="fecha" id="fecha" value="<?=$fecha_ccd;?>" readonly="readonly"/>
                            </div>
                        </div>
                        
                    </div><!--detalle -->
                    <div class="row">
                    	<div class="col-sm-4">
                            <div class="form-group">
                                <!-- <button type="button" class="btn btn-warning btn-round btn-fab" data-toggle="modal" data-target="#modalAgregarDR" onclick="agregarRendicionDetalle('<?=$datose;?>')">
          		                      <i class="material-icons" title="Agregar Detalle Rendición">add</i>
          		  		             </button> -->

                            </div>
                        </div>
                    	
                    </div>


              <div class="card">
              <div class="card-header <?=$colorCard;?> card-header-text">
                <div class="card-text">
                  <h6 class="card-title">Detalle</h6>
                </div>
              </div>
              <div class="card-body ">
                <fieldset id="fiel" style="width:100%;border:0;">
                  <button title="Agregar" type="button" id="add_boton" name="add" class="btn btn-warning btn-round btn-fab" onClick="addCajaChicaDetalleADD(this)">
                              <i class="material-icons">add</i>
                  </button>
                  <div id="div<?=$index;?>">  
                    <div class="h-divider">
                        
                    </div>
                  </div>          
                </fieldset>
                                       
                  <div class="card-footer fixed-bottom">
                  <button type="submit" class="<?=$buttonMorado;?>">Guardar</button>
                  <a href="<?=$urlListDetalleCajaChica;?>&codigo=<?=$cod_cc;?>&cod_tcc=<?=$cod_tcc?>" class="<?=$buttonCancel;?>"> <i class="material-icons" title="Volver">keyboard_return</i> Volver </a>
                  <!-- <button class="btn btn-danger" onClick="location.href='<?=$urlListaRendiciones;?>'">Volver</button> -->

                  </div>
              </div>
            </div>

            <!--aqui termina el detalle-->
            </div>
          </div>  
        </div>
    </div>

</form>              

