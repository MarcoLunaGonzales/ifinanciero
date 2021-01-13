<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();

$stmt = $dbh->prepare("SELECT *,
  (select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal)as personal,(select uo.abreviatura from unidades_organizacionales uo where uo.codigo=cod_uo)as uo,( select a.abreviatura from areas a where a.codigo=cod_area) as area from tipos_caja_chica where cod_estadoreferencial=1 order by cod_estado");
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('cod_uo', $cod_uo);
$stmt->bindColumn('cod_area', $cod_area);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('uo', $uo);
$stmt->bindColumn('area', $area);
$stmt->bindColumn('personal', $personal);
$stmt->bindColumn('cod_estado', $codEstado);
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
                  <h4 class="card-title"><?=$nombrePluralTiposCajaChica?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th>#</th>                        
                          <th>Nombre</th>
                          <th>Oficina</th>
                          <th>Area</th>
                          <th>Responsable</th>
                          <th>Nombre Cuenta</th>
                          <th>Nro Cuenta</th>
                          <th>Estado</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          $cod_cuenta=obtenerCodigoCuentaCajaChica($codigo);
                          $nombre_cuenta=nameCuenta($cod_cuenta);
                          $numero_cuenta=obtieneNumeroCuenta($cod_cuenta);
                          $tituloEstado="<a class='btn btn-sm btn-success'>VIGENTE</a>";
                          if($codEstado==2){
                             $tituloEstado="<a class='btn btn-sm btn-warning'>CERRADO</a>";
                          }
                          ?>
                          <tr>
                            <td><?=$index;?></td>                            
                              <td><?=$nombre;?></td>
                              <td><?=$uo;?></td>
                              <td><?=$area;?></td>        
                              <td><?=$personal;?></td>        
                              <td><?=$nombre_cuenta;?></td> 
                              <td><?=$numero_cuenta;?></td>
                              <td><?=$tituloEstado;?></td>                               
                              <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){
                                  // $cod_cuenta=obtenerCodigoCuentaCajaChica($codigo);
                                  // $datos=$codigo."#####".$nombre."#####".$cod_cuenta;
                              ?>
                                <!-- <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalCuenta" onclick="agregarDatosCuentaCajaChica('<?=$datos;?>')">
                                  <i class="material-icons" title="Editar plan Cuenta">add</i>
                                </button> -->
                                <a href='<?=$urlFormTiposCajaChica;?>&codigo=<?=$codigo;?>' rel="tooltip" class="<?=$buttonEdit;?>">
                                  <i class="material-icons"><?=$iconEdit;?></i>
                                </a>
                                <button rel="tooltip" class="<?=$buttonDelete;?>" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteTiposCajaChica;?>&codigo=<?=$codigo;?>')">
                                  <i class="material-icons"><?=$iconDelete;?></i>
                                </button>
                                <?php
                                  }
                                ?>
                              
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
                    <!--<button class="<?=$buttonNormal;?>" onClick="location.href='index.php?opcion=registerUbicacion'">Registrar</button>-->
                    <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormTiposCajaChica;?>&codigo=0'">Registrar</button>
              </div>
              <?php
              }
              ?>
		  
            </div>
          </div>  
        </div>
    </div>
<!-- Modal -->
<div class="modal fade" id="modalCuenta" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Asociar Cuenta Contable</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_tipocajachica" id="cod_tipocajachica" value="0">   

        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242"> Instancia Caja Chica: </label>
          <div class="col-sm-8">
            <div class="form-group">
              <input class="form-control" type="text" name="tipo_caja_chica" id="tipo_caja_chica"  readonly="true" style="background-color:#E3CEF6;text-align: left"/>
            </div>
          </div>
        </div>

        <div class="row">
          <label class="col-sm-2 col-form-label" style="color:#424242">Cuenta Asociada:</label>
          <div class="col-sm-8">
            <div class="form-group" id="div_cuenta_contable_cajachica">
                
            </div>
          </div>
        </div>

        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="registrarCuentaAsociada" name="registrarCuentaAsociada" data-dismiss="modal">Agregar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function(){
    $('#registrarCuentaAsociada').click(function(){    
      cod_tipocajachica=document.getElementById("cod_tipocajachica").value;
      cod_cuenta=$('#cod_cuenta').val();      
      registrarCuentaAsociadaCajaChica(cod_tipocajachica,cod_cuenta);
    });    

  });
</script>