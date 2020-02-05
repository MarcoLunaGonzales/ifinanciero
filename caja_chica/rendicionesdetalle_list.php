<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();
$codigo_rendicion=$codigo;


$stmt = $dbh->prepare("SELECT cod_cajachicadetalle,monto_a_rendir,observaciones
from rendiciones
where cod_estadoreferencial=1 and codigo=$codigo_rendicion");
//ejecutamos
$stmt->execute();
$result=$stmt->fetch();
$cod_cajachicadetalle=$result['cod_cajachicadetalle'];
$monto_a_rendir=$result['monto_a_rendir'];
$observaciones=$result['observaciones'];
//sacamos el monto sumando de rendicion
$stmtRendicionTotal = $dbh->prepare("SELECT SUM(monto) as monto_total from rendiciones_detalle where cod_estadoreferencial=1 and cod_rendicion=$codigo_rendicion");
//ejecutamos
$stmtRendicionTotal->execute();
$result=$stmtRendicionTotal->fetch();
$monto_rendicion=$result['monto_total'];

$monto_faltante=$monto_a_rendir-$monto_rendicion;
$datose=$codigo_rendicion."/".$monto_rendicion."/".$cod_cajachicadetalle;

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
$statementTipoDocRendicion = $dbh->query("SELECT * from tipos_doc_rendicion order by 2");
$statementTipoDocRendicionE = $dbh->query("SELECT * from tipos_doc_rendicion order by 2");
?>

<div class="cntainer-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Registrar rendición detalle</h4>
                  
                </div>
                <div class="card-body">

                	<div class="row">
                        <label class="col-sm-1 col-form-label">Monto Rendición</label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input class="form-control" name="monto_total" id="monto_total" value="<?=$monto_rendicion;?>" readonly="readonly"/>
                            </div>
                        </div>
                        <label class="col-sm-1 col-form-label">Monto Faltante</label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input class="form-control" name="monto_faltante" id="monto_faltante" value="<?=$monto_faltante;?>" readonly="readonly"/>
                            </div>
                        </div>
                        <label class="col-sm-1 col-form-label">Monto a Rendir</label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input class="form-control"  name="monto_a_rendir" id="monto_a_rendir" value="<?=$monto_a_rendir;?>" readonly="readonly"/>
                            </div>
                        </div>
                    </div><!--montos -->
                    <div class="row">
                        <label class="col-sm-2 col-form-label">Detalle General</label>
                        <div class="col-sm-8">
                            <div class="form-group">
                                <input class="form-control" type="text" name="observaciones" id="observaciones" value="<?=$observaciones;?>" readonly="readonly"/>
                            </div>
                        </div>
                        
                    </div><!--detalle -->
                    <div class="row">
                    	<div class="col-sm-4">
                            <div class="form-group">
                                <button type="button" class="btn btn-warning btn-round btn-fab" data-toggle="modal" data-target="#modalAgregarDR" onclick="agregarRendicionDetalle('<?=$datose;?>')">
		                      <i class="material-icons" title="Agregar Detalle Rendición">add</i>
		  		             </button>
                            </div>
                        </div>
                    	
                    </div>

                   <div class="table-responsive">

	                    <table class="table" id="tablePaginator">

	                      <thead>
	                        <tr>
	                          <th>#</th>                                                  
	                          <th>Tipo Doc.</th>
	                          <th>Nro. Doc.</th>
	                          <th>Fecha Doc.</th>
	                          <th>Monto</th>	                          
	                          <th>Observaciones</th>
	                          <th></th>
	                        </tr>
	                      </thead>
	                      <tbody>
	                        <?php $index=1;
	                        while ($row = $stmtRendicionDetalle->fetch(PDO::FETCH_BOUND)) { 
	                        	$datos=$codigo_rendicion."/".$codigoDR."/".$cod_tipodoccajachica."/".$nro_doc."/".$fecha_doc."/".$monto."/".$observacionesDR;?>
	                          <tr>
	                            <td><?=$index;?></td>                            
	                              <td><?=$cod_tipodoc;?></td>
	                              <td><?=$nro_doc;?></td>        
	                              <td><?=$fecha_doc;?></td>        
	                              <td><?=$monto;?></td>        	                              
	                              <td><?=$observacionesDR;?></td>        

	                              
	                              <td class="td-actions text-right">
	                              
	                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditarDR" onclick="clickEditarRendicionDetalle('<?=$datos;?>')">
                                		<i class="material-icons" title="Editar"><?=$iconEdit;?></i>
                                  </button>
                                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalEliminarDR" onclick="clickBorrarRendicionDetalle('<?=$datos;?>')">
                                    <i class="material-icons" title="Eliminar"><?=$iconDelete;?></i>
                                  </button>
	                                
	                              </td>
	                          </tr>
	                        <?php $index++; } ?>
	                      </tbody>
	                    </table> 
                  </div>
                </div>
              </div>
              <div class="card-footer fixed-bottom">
                <?php
                if($globalAdmin==1){
                ?>
                      <button type="button" class="btn" style="background-color: #3b83bd;" data-toggle="modal" data-target="#modalGuardar" onclick="clickGuardarRendicion('<?=$datose;?>')">Guardar</button>                
                <?php
                }
                ?>
                <button class="btn btn-danger" onClick="location.href='<?=$urlListaRendiciones;?>'">Volver</button>
              </div>
            </div>
          </div>  
        </div>
    </div>



<!-- Modal agregar detalle rendicion-->
<div class="modal fade" id="modalAgregarDR" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Agregar detalle rendición</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_rendicionA" id="codigo_rendicionA" value="0">              
        <div class="row">
            <label class="col-sm-2 col-form-label">Tipo documento</label>
            <div class="col-sm-4">
                <div class="form-group">                   
                    <select name="cod_tipo_documentoA" id="cod_tipo_documentoA" class="selectpicker" data-style="btn btn-primary">
			          <?php while ($row = $statementTipoDocRendicion->fetch()){ ?>
			              <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
			          <?php } ?>
			        </select>
                </div>
            </div>
            <label class="col-sm-2 col-form-label">Numero Doc.</label>
            <div class="col-sm-4">
                <div class="form-group">
                    <input class="form-control"  type="number" name="numero_doc" id="numero_doc" value="" requerid/>
                </div>
            </div>
        </div><!--montos -->
        <div class="row">
            <label class="col-sm-2 col-form-label">Fecha Doc.</label>
            <div class="col-sm-4">
                <div class="form-group">
                    <input class="form-control"  type="date" name="fecha_doc" id="fecha_doc" value="" requerid/>
                </div>
            </div>
            <label class="col-sm-2 col-form-label">Monto</label>
            <div class="col-sm-4">
                <div class="form-group">
                    <input class="form-control" type="text" name="monto_A" id="monto_A" value="0" requerid/>
                </div>
            </div>
        </div><!--montos -->
        <div class="row">
            <label class="col-sm-2 col-form-label">Descripción</label>
            <div class="col-sm-8">
                <div class="form-group">
                    <input class="form-control" type="text" name="DescripciónA" id="observacionesA" value="" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                </div>
            </div>
        </div><!--detalle -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarDetalleRendicionA" name="registrarDetalleRendicionA" data-dismiss="modal">Aceptar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button> -->
      </div>
    </div>
  </div>
</div>

<!-- Modal editar detalle rendicion-->
<div class="modal fade" id="modalEditarDR" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editar detalle rendición</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_rendicionE" id="codigo_rendicionE" value="0">
        <input type="hidden" name="codigo_detRendicionE" id="codigo_detRendicionE" value="0">              
        <div class="row">
            <label class="col-sm-2 col-form-label">Tipo documento</label>
            <div class="col-sm-4">
                <div class="form-group">                   
                    <select name="cod_tipo_documentoE" id="cod_tipo_documentoE" class="selectpicker" data-style="btn btn-primary">
			          <?php while ($rowE = $statementTipoDocRendicionE->fetch()){ ?>
			              <option value="<?=$rowE["codigo"];?>"><?=$rowE["nombre"];?></option>
			          <?php } ?>
			        </select>
                </div>
            </div>
            <label class="col-sm-2 col-form-label">Numero Doc.</label>
            <div class="col-sm-4">
                <div class="form-group">
                    <input class="form-control"  type="number" name="numero_docE" id="numero_docE" value="" requerid/>
                </div>
            </div>
        </div><!--montos -->
        <div class="row">
            <label class="col-sm-2 col-form-label">Fecha Doc.</label>
            <div class="col-sm-4">
                <div class="form-group">
                    <input class="form-control"  type="date" name="fecha_docE" id="fecha_docE" value="" requerid/>
                </div>
            </div>
            <label class="col-sm-2 col-form-label">Monto</label>
            <div class="col-sm-4">
                <div class="form-group">
                    <input class="form-control" type="text" name="monto_E" id="monto_E" value="0" requerid/>
                </div>
            </div>
        </div><!--montos -->
        <div class="row">
            <label class="col-sm-2 col-form-label">Descripción</label>
            <div class="col-sm-8">
                <div class="form-group">
                    <input class="form-control" type="text" name="observacionesE" id="observacionesE" value="" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                </div>
            </div>
        </div><!--detalle -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EditasDetalleRendicionA" name="EditasDetalleRendicionA" data-dismiss="modal">Aceptar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button> -->
      </div>
    </div>
  </div>
</div>

<!--eliminar detalle rendicion-->
<div class="modal fade" id="modalEliminarDR" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_rendicionB" id="codigo_rendicionB" value="0">
        <input type="hidden" name="codigo_detRendicionB" id="codigo_detRendicionB" value="0">              
        Ésta acción eliminará El Detalle De Rendición. ¿Deseas Continuar?
      </div>       
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EliminarDetRendicion" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>
<!-- guardar rendiciones-->
<div class="modal fade" id="modalGuardar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_rendicionG" id="codigo_rendicionG" value="0">
        <input type="hidden" name="monto_rendicionG" id="monto_rendicionG" value="0">
        <input type="hidden" name="cod_cajachicaDetG" id="cod_cajachicaDetG" value="0">
                    
        Ésta acción cerrará el estado de rendición. ¿Deseas Continuar?
      </div>       
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="GuardarRendicion" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> <-- Volver </button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function(){
    $('#registrarDetalleRendicionA').click(function(){    
      codigo_rendicionA=document.getElementById("codigo_rendicionA").value;
      cod_tipo_documentoA=$('#cod_tipo_documentoA').val();
      numero_doc=$('#numero_doc').val();
      fecha_doc=$('#fecha_doc').val();
      monto_A=$('#monto_A').val();
      observacionesA=$('#observacionesA').val();

      RegistrarDetalleRendicion(codigo_rendicionA,cod_tipo_documentoA,numero_doc,fecha_doc,monto_A,observacionesA);
    });
    $('#EditasDetalleRendicionA').click(function(){    
      codigo_rendicionE=document.getElementById("codigo_rendicionE").value;
      codigo_detRendicionE=document.getElementById("codigo_detRendicionE").value;
      cod_tipo_documentoE=$('#cod_tipo_documentoE').val();
      numero_docE=$('#numero_docE').val();
      fecha_docE=$('#fecha_docE').val();
      monto_E=$('#monto_E').val();
      observacionesE=$('#observacionesE').val();
      EditarDetalleRendicion(codigo_detRendicionE,codigo_rendicionE,cod_tipo_documentoE,numero_docE,fecha_docE,monto_E,observacionesE);
    });
    $('#EliminarDetRendicion').click(function(){    
      codigo_rendicionB=document.getElementById("codigo_rendicionB").value;
      codigo_detRendicionB=document.getElementById("codigo_detRendicionB").value;
      EliminarDetalleRendicion(codigo_detRendicionB,codigo_rendicionB);
    });
    $('#GuardarRendicion').click(function(){    
      codigo_rendicionG=document.getElementById("codigo_rendicionG").value;
      monto_rendicionG=document.getElementById("monto_rendicionG").value;
      cod_cajachicaDetG=document.getElementById("cod_cajachicaDetG").value;
      
      
      GuardarRendicion(codigo_rendicionG,monto_rendicionG,cod_cajachicaDetG);
    });


   
  });
</script>