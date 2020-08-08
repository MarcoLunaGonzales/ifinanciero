<?php

require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'styles.php';

$globalAdmin=$_SESSION["globalAdmin"];

$dbh = new Conexion();
$codigo_tipo_caja_Chica=$codigo;
$stmtTCC = $dbh->prepare("SELECT nombre from tipos_caja_chica where  codigo = $codigo_tipo_caja_Chica");
$stmtTCC->execute();
$resultTCC=$stmtTCC->fetch();
$nombre_tipoCC=$resultTCC['nombre'];

$sql="SELECT *,date_format(fecha,'%d/%m/%Y') as fecha_x,
  (select e.nombre from estados_contrato e where e.codigo=cod_estado) as nombre_estado,
(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_personal) as personal
 from caja_chica where cod_estadoreferencial=1 and cod_tipocajachica = $codigo_tipo_caja_Chica";
//echo $sql;
$stmt = $dbh->prepare($sql);
//ejecutamos
$stmt->execute();
//bindColumn
$stmt->bindColumn('codigo', $cod_cajachica);
$stmt->bindColumn('cod_tipocajachica', $cod_tipocajachica);
$stmt->bindColumn('fecha_x', $fecha);
$stmt->bindColumn('numero', $numero);
$stmt->bindColumn('monto_inicio', $monto_inicio);
// $stmt->bindColumn('monto_reembolso', $monto_reembolso);
$stmt->bindColumn('monto_reembolso_nuevo', $monto_reembolso_nuevo);
$stmt->bindColumn('observaciones', $observaciones);
$stmt->bindColumn('cod_personal', $cod_personal);
$stmt->bindColumn('personal', $personal);
$stmt->bindColumn('cod_estado', $cod_estado);
$stmt->bindColumn('nombre_estado', $nombre_estado);
$stmt->bindColumn('cod_comprobante', $cod_comprobante);


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
                  <h4 class="card-title"><?=$nombrePluralCajaChica?></h4>
                  <h4 class="card-title" align="center"><?=$nombre_tipoCC?></h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" id="tablePaginator">

                      <thead>
                        <tr>
                          <th width="6%"><small><small>Nro. <br>Caja Chica</small></small></th>
                          <th width="6%"><small>Fecha</small></th>
                          
                          <th width="10%"><small>Responsable</small></th>
                          <th width="7%"><small><small>Monto<br>Inicio</small></small></th>
                          <th width="7%"><small>Saldo</small></th>
                          <th width="7%"><small>Reembolso</small></th>
                          <th><small>Detalle</small></th>
                          <th width="5%"><small>estado</small></th>
                          <th width="10%"><small></small></th>
                          <th width="2%"><small></small></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $index=1;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) { 
                          $datos_ComproCajaChica=$cod_cajachica."/".$observaciones."/".$codigo_tipo_caja_Chica;  
                            // $sql_rendicion="SELECT SUM(c.monto)-IFNULL((select SUM(r.monto) from caja_chicareembolsos r where r.cod_cajachica=$cod_cajachica and r.cod_estadoreferencial=1),0) as monto_total from caja_chicadetalle c where c.cod_cajachica=$cod_cajachica and c.cod_estadoreferencial=1";
                            // $stmtSaldo = $dbh->prepare($sql_rendicion);
                            // $stmtSaldo->execute();
                            // $resultSaldo=$stmtSaldo->fetch();
                            // if($resultSaldo['monto_total']!=null || $resultSaldo['monto_total']!='')
                            //   $monto_total=$resultSaldo['monto_total'];
                            // else $monto_total=0;      
                            $monto_total=importe_total_cajachica($cod_cajachica);      
                            $monto_saldo=$monto_inicio-$monto_total;

                             if($cod_estado==1)
                                $label='<span class="badge badge-success">';
                            else
                              $label='<span class="badge badge-danger">';                              
                           
                          ?>
                          <tr>
                              <td class="text-right"><small><?=$numero;?></small></td>    
                              <td><small><?=$fecha;?></small></td>
                              <td class="text-left"><small><?=$personal;?></small></td>        
                              <td class="text-right"><small><?=number_format($monto_inicio, 2, '.', ',');?></small></td>        
                              <td class="text-right"><small><?=number_format($monto_saldo, 2, '.', ',');?></small></td><!-- el saldo -->        
                              <td class="text-right"><small><?=number_format($monto_reembolso_nuevo, 2, '.', ',');?></small></td> <!-- el remmbolso registrado -->
                              <td class="text-left"><small><small><?=$observaciones;?></small></small></td>        
                              <td><small><?=$label.$nombre_estado."</span>";?></small></td>
                                
                              <!-- href='<?=$urlprintFiniquitosOficial;?>?codigo=<?=$codigo;?>' -->
                              <td class="td-actions text-right">
                                <div class="btn-group dropdown">
                                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                                     <i class="material-icons" >list</i><small><small>OPTIONS</small></small>
                                  </button>
                                  <div class="dropdown-menu" >
                                <?php
                                if($globalAdmin==1 and $cod_estado==1){ ?>
                                  <a href='<?=$urlListDetalleCajaChica;?>&codigo=<?=$cod_cajachica;?>&cod_tcc=<?=$codigo_tipo_caja_Chica?>' rel="tooltip" class="dropdown-item" style="background-color:#4a4ea2;">
                                      <i class="material-icons" style="color:#FFF;" title="Agregar Detalle">playlist_add</i>Agregar Gastos
                                  </a>
                                  <!-- <label class="text-danger"> | </label> -->
                                  <a  rel="tooltip" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlDeleteCajaChica;?>&codigo=<?=$cod_cajachica;?>&cod_tcc=<?=$codigo_tipo_caja_Chica?>&cod_a=1')">
                                      <i class="material-icons text-danger"  title="Cerrar Caja Chica">lock</i>Cerrar
                                    </a>
                                <?php }?>
                                <a href='<?=$urlprint_cajachica;?>?codigo=<?=$cod_cajachica;?>' target="_blank" rel="tooltip" class=" dropdown-item">
                                    <i class="material-icons text-primary" title="Imprimir Detalle Gastos">print</i>Imprimir
                                </a>

                                <?php
                                  if($globalAdmin==1 and $cod_estado==1){
                                ?>
                                <a href='<?=$urlFormCajaChica;?>&codigo=<?=$cod_cajachica;?>&cod_tcc=<?=$codigo_tipo_caja_Chica?>' rel="tooltip" class="dropdown-item">
                                  <i class="material-icons text-success" title="Editar"><?=$iconEdit;?></i>Editar
                                </a>
                                <a rel="tooltip" class="dropdown-item" onclick="alerts.showSwal('warning-message-and-confirmation','<?=$urlDeleteCajaChica;?>&codigo=<?=$cod_cajachica;?>&cod_tcc=<?=$codigo_tipo_caja_Chica?>&cod_a=2')">
                                  <i class="material-icons text-danger" title="Borrar"><?=$iconDelete;?></i>Borrar
                                </a>
                                  <?php
                              }
                                  ?>
                              </div></div>
                              </td>
                              <td class="td-actions text-center">
                                <?php
                                //si es mayo a cero, ya se genero el comprobante.
                                  if($globalAdmin==1 and $cod_estado==1 and $cod_comprobante>0){?>
                                    <div class="btn-group dropdown">
                                      <button type="button" class="btn btn-ganger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:red;background-color:#FFFFFF;">
                                         <i class="material-icons" title="Comprobante" >input</i>
                                      </button>
                                      <div class="dropdown-menu" style="background-color: #D8CEF6;">   
                                        <a href="<?=$urlImp;?>?comp=<?=$cod_comprobante;?>&mon=1" class="dropdown-item" type="button" target="_blank">
                                          <i class="material-icons" title="Imprimir Comprobante" style="color:red; ">print</i> Imprimir comprobante
                                        </a>
                                        <button title="Revertir en Comprobante Existente" class="dropdown-item" type="button" data-toggle="modal" data-target="#modalComprobanteCajaChica" onclick="agregaDatosComprCajaChica('<?=$datos_ComproCajaChica;?>')">
                                        <i class="material-icons text-danger">input</i> Revertir en Comprobante Existente
                                        </button>                                          
                                      </div>
                                    </div>
                                  <?php }elseif($globalAdmin==1 and $cod_estado==1){?>
                                    <div class="btn-group dropdown">
                                      <button type="button" class="btn btn-ganger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:red;background-color:#FFFFFF;">
                                         <i class="material-icons" title="Comprobante" >input</i>
                                      </button>
                                      <div class="dropdown-menu" style="background-color: #D8CEF6;">                                    
                                        <button title="Generar en Comprobante Nuevo" class="dropdown-item" type="button" onclick="alerts.showSwal('warning-message-and-confirmationGeneral','<?=$urlprint_contabilizacion_cajachica;?>?cod_cajachica=<?=$cod_cajachica;?>')" target="_blank">
                                        <i class="material-icons text-danger">input</i>En Comprobante Nuevo
                                        </button>
                                        <button title="Generar en Comprobante Existente" class="dropdown-item" type="button" data-toggle="modal" data-target="#modalComprobanteCajaChica" onclick="agregaDatosComprCajaChica('<?=$datos_ComproCajaChica;?>')">
                                        <i class="material-icons text-danger">input</i>En Comprobante Existente
                                        </button>                                          
                                      </div>
                                    </div> 
                                  <?php }else{ ?>
                                    <div class="btn-group dropdown">
                                      <button type="button" class="btn btn-ganger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:red;background-color:#FFFFFF;">
                                         <i class="material-icons" title="Comprobante" >input</i>
                                      </button>
                                      <div class="dropdown-menu" style="background-color: #D8CEF6;">   
                                        <a href="<?=$urlImp;?>?comp=<?=$cod_comprobante;?>&mon=1" class="dropdown-item" type="button" target="_blank">
                                          <i class="material-icons" title="Imprimir Comprobante" style="color:red; ">print</i> Imprimir comprobante
                                        </a>
                                      </div>
                                    </div>                  
                                  <?php }
                                ?>

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
                      <button class="<?=$buttonNormal;?>" onClick="location.href='<?=$urlFormCajaChica;?>&codigo=0&cod_tcc=<?=$codigo_tipo_caja_Chica?>'">Registrar</button>                
                <?php
                }
                ?>
                <button class="btn btn-danger" onClick="location.href='<?=$urlprincipal_CajaChica;?>'"><i class="material-icons" title="Volver">keyboard_return</i>Volver</button>
              </div>
            </div>
          </div>  
        </div>
    </div>

<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde un momento por favor</p>  
  </div>
</div>

<div class="modal fade" id="modalComprobanteCajaChica" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel"><b>Generar Comprobante Existente</b></h3>
      </div>
      <div class="modal-body">
        <input type="hidden" name="cod_cajachica" id="cod_cajachica" value="0">
        <input type="hidden" name="cod_tipocajachica" id="cod_tipocajachica" value="0">
        <div class="row">
          <!-- <label class="col-sm-3 text-right col-form-label" style="color:#424242">Importe De Solicitud de Facturación</label> -->
          <div class="col-sm-12">
            <div class="form-group text-center">
              <input type="text" name="detalle_cajachica" id="detalle_cajachica" value="0" readonly="true" class="form-control text-center" style="background-color:#E3CEF6;text-align: left">            
            </div>
          </div>       
        </div>
        <div class="row">
          <label class="col-sm-4 text-right col-form-label" style="color:#424242">Gestión</label>
          <div class="col-sm-6">
            <div class="form-group">            
              <select class="selectpicker form-control form-control-sm" name="gestion" id="gestion" data-style="<?=$comboColor;?>">
                    <option disabled selected value="">Gestión</option>
                  <?php
                  $stmt = $dbh->prepare("SELECT nombre from gestiones where cod_estado=1 order by nombre desc");
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$row['nombre'];
                  $nombreX=$row['nombre'];                  
                ?>
                <option value="<?=$codigoX;?>"><?=$nombreX;?></option>  
                <?php
                  }
                  ?>
              </select>
            </div>
          </div>
        </div>  
        <div class="row">
          <label class="col-sm-4 text-right col-form-label" style="color:#424242">Mes del comprobante</label>
          <div class="col-sm-6">
            <div class="form-group">              
              <select class="selectpicker form-control form-control-sm" name="mes_comprobante" id="mes_comprobante" data-style="<?=$comboColor;?>">
                  <option disabled selected value=""></option>                
                  <option value="1">ENERO</option>
                  <option value="2">FEBRERO</option>
                  <option value="3">MARZO</option>
                  <option value="4">ABRIL</option>
                  <option value="5">MAYO</option>
                  <option value="6">JUNIO</option>
                  <option value="7">JULIO</option>
                  <option value="8">AGOSTO</option>
                  <option value="9">SEPTIEMBRE</option>
                  <option value="10">OCTUBRE</option>
                  <option value="11">NOVIEMBRE</option>
                  <option value="12">DICIEMBRE</option>                  
              </select>
            </div>
          </div>
        </div>      
        <div class="row">
          <label class="col-sm-4 text-right col-form-label" style="color:#424242">Tipo de comprobante</label>
          <div class="col-sm-6">
            <div class="form-group">            
              <select class="selectpicker form-control form-control-sm" name="tipo_comprobante" id="tipo_comprobante" data-style="<?=$comboColor;?>">
                    <option disabled selected value="">Tipo</option>
                  <?php
                  $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM tipos_comprobante where cod_estadoreferencial=1 order by 1");
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$row['codigo'];
                  $nombreX=$row['nombre'];
                  $abrevX=$row['abreviatura'];
                ?>
                <option value="<?=$codigoX;?>"><?=$nombreX;?> - <?=$abrevX;?></option>  
                <?php
                  }
                  ?>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-4 text-right col-form-label" style="color:#424242">Unidad</label>
          <div class="col-sm-6">
            <div class="form-group">            
              <select class="selectpicker form-control form-control-sm" name="unidad" id="unidad" data-style="<?=$comboColor;?>">
                    <option disabled selected value="">Tipo</option>
                  <?php
                  $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 order by 1");
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$row['codigo'];
                  $nombreX=$row['nombre'];
                  $abrevX=$row['abreviatura'];
                ?>
                <option value="<?=$codigoX;?>"><?=$nombreX;?> - <?=$abrevX;?></option>  
                <?php
                  }
                  ?>
              </select>
            </div>
          </div>
        </div>  

        <div class="row">
          <label class="col-sm-4 text-right col-form-label" style="color:#424242">Número de Comprobante</label>
          <div class="col-sm-6">
            <div class="form-group">
              <input type="number"name="nro_comprobante" id="nro_comprobante" class="form-control" onchange="ajaxBuscarComprobanteCajaChica()">
            </div>
          </div>        
        </div>    
        <div class="row" id="contenedor_detalle_comprobante">
          
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success d-none" id="guardarDatosModalComprobante" name="guardarDatosModalComprobante">Generar Comprobante</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Volver </button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  $(document).ready(function(){
    $('#guardarDatosModalComprobante').click(function(){    
      cod_cajachica=document.getElementById("cod_cajachica").value;
      detalle_cajachica=document.getElementById("detalle_cajachica").value;
      cod_tipocajachica=document.getElementById("cod_tipocajachica").value;
      nro_comprobante=$('#nro_comprobante').val();
      mes_comprobante=$('#mes_comprobante').val();
      tipo_comprobante=$('#tipo_comprobante').val();      
      gestion=$('#gestion').val();
      unidad=$('#unidad').val();  
      if(nro_comprobante==null || nro_comprobante<=0){
        Swal.fire("Informativo!", "Por favor introduzca el Número de Comprobante.", "warning");
      }else{
        if(mes_comprobante==null){
          Swal.fire("Informativo!", "Por favor introduzca el Mes Del Comprobante.", "warning");
        }else{
            if(tipo_comprobante==null){
              Swal.fire("Informativo!", "Por favor introduzca el Tipo De Comprobante.", "warning");
            }else{              
                RegistrarComprobanteCajaChica(cod_cajachica,cod_tipocajachica,nro_comprobante,mes_comprobante,tipo_comprobante,gestion,unidad);
            }          
        }
      }      
    });   
  });
</script>
