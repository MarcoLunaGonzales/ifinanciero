<?php

require_once 'conexion.php';
require_once 'rrhh/configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$cod_personal=$codigo;
$dbh = new Conexion();


$stmtPersonal = $dbh->prepare("SELECT * from personal where codigo=:codigo");
$stmtPersonal->bindParam(':codigo',$cod_personal);
//ejecutamos
$stmtPersonal->execute();
$result=$stmtPersonal->fetch();
$cod_personal_1=$result['codigo'];
$ci=$result['identificacion'];
$primer_nombre=$result['primer_nombre'];
$paterno=$result['paterno'];
$materno=$result['materno'];
//SELECT
$stmt = $dbh->prepare("SELECT *,
(select c.nombre from tipos_contrato_personal c where c.codigo=cod_tipocontrato)as nombre_contrato,
(select ct.duracion_meses from tipos_contrato_personal ct where ct.codigo=cod_tipocontrato)as meses_contrato
from personal_contratos 
where cod_personal=:codigo and cod_estadoreferencial=1
ORDER BY 1" );
$stmt->bindParam(':codigo',$cod_personal);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo_contrato);
$stmt->bindColumn('cod_tipocontrato', $cod_tipocontrato);
$stmt->bindColumn('fecha_iniciocontrato', $fecha_iniciocontrato);
$stmt->bindColumn('fecha_fincontrato', $fecha_fincontrato);

$stmt->bindColumn('nombre_contrato', $nombre_contrato);
$stmt->bindColumn('meses_contrato', $meses_contrato);




//listado de contratos
$query_contrato = "select * from tipos_contrato_personal order by 3";
$statementTiposContrato = $dbh->query($query_contrato);
//listado de editar areas
$query_contrato = "select * from tipos_contrato_personal order by 3";
$statementTiposContratoE = $dbh->query($query_contrato);
$fecha_actual=date("Y-m-d");
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
                  <h4 class="card-title">Personal Contratos</h4>
                  <h4 class="card-title">Fecha Actual: <?=$fecha_actual?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="tablePaginator">
                      <thead>
                        <tr class="bg-dark text-white">
                        	<th>Codigo C.</th>
                          <th>CI</th>
                        	<th>Personal</th>
              						<th>Tipo Contrato</th>
                          <th>Duración Contrato(Mes)</th>
              						<th>F. Ini. Contrato</th>
                          <th>F. Fin. Contrato</th>
              						<th></th>                                                   
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;                      
                        $datos=$cod_personal_1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {                       	
                        	$datos=$cod_personal_1."/".$codigo_contrato."/".$fecha_iniciocontrato;
                        	?>
                            <tr>
                                <td><?=$codigo_contrato;?></td>
                                <td><?=$ci;?></td>
                                <td><?=$primer_nombre." ".$paterno." ".$materno;?></td>
                                <td><?=$nombre_contrato;?></td>
                                <td><?=$meses_contrato;?></td>
                                <td><?=$fecha_iniciocontrato;?></td>
                                
                                <?php
                                  
                                  $porcionesActual = explode("-", $fecha_actual);
                                  $anioActual= $porcionesActual[0]; // porción1
                                  $mesActual= $porcionesActual[1]; // porción2                                  
                                  $diaActual= $porcionesActual[2]; // porción2 


                                  $porcionesFin = explode("-", $fecha_fincontrato);
                                  $anioFin= $porcionesFin[0]; // porción1
                                  $mesFin= $porcionesFin[1]; // porción2 
                                  $diaFin= $porcionesFin[2]; // porción2 

                                  if($anioActual==$anioFin){
                                    if($mesActual-$mesFin==-1){
                                      $label='<span class="badge badge-warning">';
                                    }elseif ($mesActual-$mesFin==0) {
                                      if ($diaActual<$diaFin) {
                                        $label='<span class="badge badge-warning">';
                                      }else{
                                        $label='<span class="badge badge-danger">';
                                      }
                                    }elseif ($mesActual-$mesFin>0) {
                                      $label='<span class="badge badge-danger">';
                                    }else{
                                      $label='<span class="badge badge-success">';
                                    }
                                  }elseif($anioActual<$anioFin){
                                    $label='<span class="badge badge-success">';
                                  }else{
                                    $label='<span class="badge badge-danger">';
                                  }
                                ?>
                                <td><?=$label.$fecha_fincontrato."</span>";?></td>
                                <td class="td-actions text-right">
                                	<button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEditar" onclick="agregaformPCE('<?=$datos;?>')">
                                		<i class="material-icons" title="Editar"><?=$iconEdit;?></i>                             
                                  </button>
                                  <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalEliminar" onclick="agregaformPCB('<?=$datos;?>')">
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
              <?php

              if($globalAdmin==1){
              ?>
              <div class="card-footer fixed-bottom">
                <button type="button" class="btn btn-warning btn-round btn-fab" data-toggle="modal" data-target="#modalAgregarC" onclick="agregaformPC('<?=$datos;?>')">
                    <i class="material-icons" title="Agregar">add</i>
		             </button>		             
              </div>

              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>


<!-- Modal agregar contrato-->
<div class="modal fade" id="modalAgregarC" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Agregar Contrato a Personal</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personalA" id="codigo_personalA" value="0">              
        <h6> Tipo Contrato : </h6>
        <select name="cod_tipocontrato" id="cod_tipocontratoA" class="selectpicker" data-style="btn btn-primary">
          <?php while ($row = $statementTiposContrato->fetch()){ ?>
              <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?> - <?=$row["duracion_meses"];?> meses</option>
          <?php } ?>
        </select>
        <h6> Fecha Inicio : </h6>
        <input class="form-control" type="date" name="fecha_inicioA" id="fecha_inicioA" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarPC" name="registrarPC" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!--eliminar contrato-->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">¿Estás Seguro?</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personalB" id="codigo_personalB" value="0">
        <input type="hidden" name="codigo_contratoB" id="codigo_contratoB" value="0">
        Esta acción eliminará El contrato. ¿Deseas Continuar?
      </div>       
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EliminarPC" data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
<!-- Editar contrato-->
<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edita Contrato Personal </h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="codigo_personalE" id="codigo_personalE" value="0">
        <input type="hidden" name="codigo_contratoE" id="codigo_contratoE" value="0">        
        <h6> Tipo Contrato : </h6>
        <select name="cod_tipocontratoE" id="cod_tipocontratoE" class="selectpicker" data-style="btn btn-primary">
          <?php while ($row = $statementTiposContratoE->fetch()){ ?>
              <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?> - <?=$row["duracion_meses"];?> meses</option>
          <?php } ?>
        </select>
        <h6> Fecha Inicio : </h6>
        <input class="form-control" type="date" name="fecha_inicioE" id="fecha_inicioE" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="EditarPC"  data-dismiss="modal">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#registrarPC').click(function(){    
      cod_personalA=document.getElementById("codigo_personalA").value;
      cod_tipocontratoA=$('#cod_tipocontratoA').val();
      fecha_inicioA=$('#fecha_inicioA').val();
      RegistrarContratoPersonal(cod_personalA,cod_tipocontratoA,fecha_inicioA);
    });
    $('#EditarPC').click(function(){
      codigo_contratoE=document.getElementById("codigo_contratoE").value;
      codigo_personalE=document.getElementById("codigo_personalE").value;
      cod_tipocontratoE=$('#cod_tipocontratoE').val();
      fecha_inicioE=$('#fecha_inicioE').val();
      EditarContratoPersonal(codigo_contratoE,codigo_personalE,cod_tipocontratoE,fecha_inicioE);
    });
    $('#EliminarPC').click(function(){    
      codigo_contratoB=document.getElementById("codigo_contratoB").value; 
      codigo_personalB=document.getElementById("codigo_personalB").value;
      EliminarContratoPersonal(codigo_contratoB,codigo_personalB);
    }); 
    

  });
</script>