<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();
setlocale(LC_TIME, "Spanish");
set_time_limit(0);
$codigo_rendicion=$codigo;


$stmt = $dbh->prepare("SELECT cod_cajachicadetalle,monto_a_rendir,observaciones,
(select ccd.fecha from caja_chicadetalle ccd where ccd.codigo=cod_cajachicadetalle) as fecha_ccd
from rendiciones
where cod_estadoreferencial=1 and codigo=$codigo_rendicion");
//ejecutamos
$stmt->execute();
$result=$stmt->fetch();
$cod_cajachicadetalle=$result['cod_cajachicadetalle'];
$monto_a_rendir=$result['monto_a_rendir'];
$observaciones=$result['observaciones'];
$fecha_ccd=$result['fecha_ccd'];
//sacamos el monto sumando de rendicion
// $stmtRendicionTotal = $dbh->prepare("SELECT SUM(monto) as monto_total from rendiciones_detalle where cod_estadoreferencial=1 and cod_rendicion=$codigo_rendicion");
// //ejecutamos
// $stmtRendicionTotal->execute();
// $result=$stmtRendicionTotal->fetch();
// $monto_rendicion=$result['monto_total'];
$monto_rendicion=0;

//$monto_faltante=$monto_a_rendir-$monto_rendicion;
//$datose=$codigo_rendicion."/".$monto_rendicion."/".$cod_cajachicadetalle;

//redinciones detalle
$stmtRendicionDetalle = $dbh->prepare("
SELECT *,
(select t.nombre from tipos_doc_rendicion t where t.codigo=cod_tipodoccajachica)as cod_tipodoc 
from rendiciones_detalle 
where cod_estadoreferencial=1 and cod_rendicion=$codigo_rendicion");
//ejecutamos
$stmtRendicionDetalle->execute();
$stmtRendicionDetalle->bindColumn('codigo', $codigoDR); 
$stmtRendicionDetalle->bindColumn('cod_rendicion', $cod_rendicion);
$stmtRendicionDetalle->bindColumn('cod_tipodoc', $cod_tipodoc);
$stmtRendicionDetalle->bindColumn('cod_tipodoccajachica', $cod_tipodoccajachica);
$stmtRendicionDetalle->bindColumn('fecha_doc', $fecha_doc);
$stmtRendicionDetalle->bindColumn('nro_doc', $nro_doc);
$stmtRendicionDetalle->bindColumn('monto', $monto);
$stmtRendicionDetalle->bindColumn('observaciones', $observacionesDR);

//listado de tipo documento rendicion
$statementTipoDocRendicion = $dbh->query("SELECT td.codigo,td.nombre from tipos_documentocajachica td where td.tipo=2 order by 2");
$statementTipoDocRendicionE = $dbh->query("SELECT td.codigo,td.nombre from tipos_documentocajachica td where td.tipo=2 order by 2");


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

<form id="formRegComp" class="form-horizontal" action="caja_chica/rendicionesdetalle_save.php" method="post" enctype="multipart/form-data">
<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
            
              <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
              <input type="hidden" name="cod_rendicion" id="cod_rendicion" value="<?=$codigo_rendicion;?>">
              <input type="hidden" name="cod_cajachicadetalle" id="cod_cajachicadetalle" value="<?=$cod_cajachicadetalle;?>">


              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Registrar Rendición Detalle</h4>
                  
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
                  <button title="Agregar" type="button" id="add_boton" name="add" class="btn btn-warning btn-round btn-fab" onClick="addRendicionDetalle(this)">
                              <i class="material-icons">add</i>
                  </button>
                  <div id="div<?=$index;?>">  
                    <div class="h-divider">
                        
                    </div>
                  </div>          
                </fieldset>
                                       
                  <div class="card-footer fixed-bottom">
                  <button type="submit" class="<?=$buttonMorado;?>">Guardar</button>
                  <a href="<?=$urlListaRendiciones;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
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

