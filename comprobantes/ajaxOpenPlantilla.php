<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

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

$codigoPlan=$_GET['codigo'];

$data=obtenerPlantilla($codigoPlan);
// bindColumn
$data->bindColumn('codigo', $codigo);
$data->bindColumn('titulo', $titulo);
$data->bindColumn('descripcion', $descripcion);
$data->bindColumn('archivo_json', $archivo);
$data->bindColumn('cod_unidadorganizacional', $codUnidad);
$data->bindColumn('cod_personal', $codPersona);

//cuentas auxliliares
 $un=0;
 $sqlCuentasAux="SELECT codigo, nombre,cod_cuenta FROM cuentas_auxiliares order by 2";
 $stmtAux = $dbh->prepare($sqlCuentasAux);
 $stmtAux->execute();
 $stmtAux->bindColumn('codigo', $codigoCuentaAux);
 $stmtAux->bindColumn('nombre', $nombreCuentaAux);
 $stmtAux->bindColumn('cod_cuenta', $codigoCuentaPlan);
 while ($rowAux = $stmtAux->fetch(PDO::FETCH_BOUND)) {  
      $arrayCuentasAux[$un]['cod_cuenta']=$codigoCuentaPlan;
      $arrayCuentasAux[$un]['codigo']=$codigoCuentaAux;
      $arrayCuentasAux[$un]['nombre']=$nombreCuentaAux;
      $un++;
  }

while ($rowData = $data->fetch(PDO::FETCH_BOUND)) {
    //REALIZAR ALGO AQUI
  $json=json_decode($archivo);

  $totaldebDet=0;$totalhabDet=0;
  for ($i=0; $i < count($json[1]); $i++) {
    if($json[1][$i]->debe==""){
      $json[1][$i]->debe=0;
    }
    if($json[1][$i]->haber==""){
      $json[1][$i]->haber=0;
    }
    $totaldebDet+=$json[1][$i]->debe;
    $totalhabDet+=$json[1][$i]->haber;
    $unidadDet=$json[1][$i]->unidad;
    $areaDet=$json[1][$i]->area;
    $debe=$json[1][$i]->debe;
    $haber=$json[1][$i]->haber;
    $glosa=$json[1][$i]->glosa_detalle;
    $cod_cuenta=$json[1][$i]->cuenta;
    $cod_cuenta_aux=$json[1][$i]->cuenta_auxiliar;
    $nombre_cuenta=$json[1][$i]->nom_cuenta;
    $nombre_cuenta_aux=$json[1][$i]->nom_cuenta_auxiliar;
    $numero_cuenta=$json[1][$i]->n_cuenta;

    $codigoCuenta=$json[1][$i]->cuenta;
    $codCuentaAuxDet=$json[1][$i]->cuenta_auxiliar;
    $numeroDet=$json[1][$i]->n_cuenta;
    $nombreDet=$json[1][$i]->nom_cuenta;

    
  $idFila=($i+1); 
      ?>      
<div id="div<?=$idFila?>">
 <div class="col-md-12">
  <div class="row">
    <div class="col-sm-1">
          <div class="form-group">
            <span id="numero_fila<?=$idFila?>" style="position:absolute;left:-15px; font-size:16px;font-weight:600; color:#386D93;"><?=$idFila?></span>
          <select class="selectpicker form-control form-control-sm" name="unidad<?=$idFila;?>" id="unidad<?=$idFila;?>" data-style="<?=$comboColor;?>" onChange="relacionSolicitudesSIS(<?=$idFila;?>)">
               <?php
                                   if($unidadDet==0){
                                   ?><option disabled selected="selected" value="">Unidad</option><?php 
                                   }else{
                                    ?><option disabled value="">Unidad</option><?php
                                   }
                                   $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
                                 $stmt->execute();
                                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                    if($codigoX==$unidadDet){
                                             ?><option value="<?=$codigoX;?>" selected><?=$abrevX;?></option><?php
                                    }else{
                                              ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                    }
                                    }
                                    ?>
      </select>
      </div>
    </div>
    <div class="col-sm-1">
          <div class="form-group">
          <select class="selectpicker form-control form-control-sm" name="area<?=$idFila;?>" id="area<?=$idFila;?>" data-style="<?=$comboColor;?>" >
          <?php
                                  if($areaDet==0){
                                   ?><option disabled selected="selected" value="">Area</option><?php 
                                   }else{
                                    ?><option disabled value="">Area</option><?php
                                   }
                          
                                  $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                    if($codigoX==$areaDet){
                                        ?><option value="<?=$codigoX;?>" selected><?=$abrevX;?></option><?php
                                      }else{
                                       ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php 
                                      }
                                  }
                                   ?>
      </select>
    </div>
  </div>

   <div class="col-sm-4">
        <input type="hidden" name="numero_cuenta<?=$idFila;?>" value="<?=$numero_cuenta?>" id="numero_cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta<?=$idFila;?>" value="<?=$cod_cuenta?>" id="cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta_auxiliar<?=$idFila;?>" value="<?=$cod_cuenta_aux?>" id="cuenta_auxiliar<?=$idFila;?>">
          <div class="row"> 
                                  <div class="col-sm-8">
                                    <div class="form-group" id="divCuentaDetalle<?=$idFila;?>">
                                  
                                          </div>
                                  </div>
                                  <div class="col-sm-4">
                                    <div class="btn-group">
                                     <div class="btn-group dropdown">
                      <button type="button" class="btn btn-sm btn-info btn-fab dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="MAYORES">
                      <i class="material-icons">list</i>
                        </button>
                        <div class="dropdown-menu">
                        <a title="Mayores" href="#" id="mayor<?=$idFila?>" onclick="mayorReporteComprobante(<?=$idFila?>)" class="dropdown-item"><span class="material-icons text-info">list</span> Ver Reporte Mayor</a>         
                        <a title="Cerrar Comprobante" id="cerrar_detalles<?=$idFila?>" href="#" onclick="verMayoresCierre(<?=$idFila;?>);" class="dropdown-item"><span class="material-icons text-danger">ballot</span> Cerrar Comprobantes</a>       
                        </div>
                    </div>

                                     <a title="Cambiar cuenta" href="#" id="cambiar_cuenta<?=$idFila?>" onclick="editarCuentaComprobante(<?=$idFila?>)" class="btn btn-sm btn-warning btn-fab"><span class="material-icons text-dark">edit</span></a>   
                                        <div class="btn-group dropdown">
                              <button type="button" class="btn btn-sm btn-success btn-fab dropdown-toggle material-icons text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Distribucion de Gastos">
                                <i class="material-icons">call_split</i>
                              </button>
                              <div class="dropdown-menu">   
                                <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucionX<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>,1);" class="dropdown-item">
                                  <i class="material-icons">bubble_chart</i> x Oficina
                                </a>
                                <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucionY<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>,2);" class="dropdown-item">
                                  <i class="material-icons">bubble_chart</i> x Área
                                </a>
                              </div>
                            </div>  
                                       <input type="hidden" id="tipo_estadocuentas<?=$idFila?>">
                                       <input type="hidden" id="tipo_proveedorcliente<?=$idFila?>">
                                       <input type="hidden" id="proveedorcliente<?=$idFila?>">
                                       <input type="hidden" id="tipo_estadocuentas_casoespecial<?=$idFila?>">
                           
                                       <a title="Estado de Cuentas" id="estados_cuentas<?=$idFila?>" href="#" onclick="verEstadosCuentas(<?=$idFila;?>,0);" class="btn btn-sm btn-danger btn-fab d-none"><span class="material-icons text-dark">ballot</span><span id="nestado<?=$idFila?>" class="bg-warning"></span></a>
                                       <!--LIBRETAS BANCARIAS DETALLE-->
               <a title="Libretas Bancarias" id="libretas_bancarias<?=$idFila?>" href="#" onclick="verLibretasBancarias(<?=$idFila;?>);" class="btn btn-sm btn-primary btn-fab d-none"><span class="material-icons text-dark">ballot</span><span id="nestadolib<?=$idFila?>" class="bg-warning"></span></a>       
               <input type="hidden" id="cod_detallelibreta<?=$idFila?>" name="cod_detallelibreta<?=$idFila?>" value="0">
               <input type="hidden" id="descripcion_detallelibreta<?=$idFila?>" value=""> 
               <input type="hidden" id="tipo_libretabancaria<?=$idFila?>" value=""> 
                                          <!--SOLICITUD DE RECURSOS SIS-->
                                          <input type="hidden" id="cod_detallesolicitudsis<?=$idFila?>" name="cod_detallesolicitudsis<?=$idFila?>" value="0">
                                          <input type="hidden" id="cod_actividadproyecto<?=$idFila?>" name="cod_actividadproyecto<?=$idFila?>" value="0">
                                          <input type="hidden" id="cod_accnum<?=$idFila?>" name="cod_accnum<?=$idFila?>" value="0">
                                          <!---->       
                                      </div>  
                                  </div>
                                </div>
    </div>
    <?php
                                $numeroCuenta=trim($numeroDet);
                                $nombreCuenta=trim($nombreDet);
                                $existeAux=0;
                              ?><script>var nfac=[];
      itemFacturas.push(nfac);var nest=[];
      itemEstadosCuentas.push(nest);itemFacturas[<?=$idFila?>]=[];filaActiva=<?=$idFila?>;</script><?php
                                    for ($ff=0; $ff < count($arrayCuentasAux) ; $ff++) {
                                  $codigoCuentaAux=$arrayCuentasAux[$ff]['codigo'];
                                  $codX=$arrayCuentasAux[$ff]['cod_cuenta'];
                                  $nombreCuentaAux=$arrayCuentasAux[$ff]['nombre'];
                                  if($codCuentaAuxDet==$codigoCuentaAux){
                                    $existeAux=1;
                                    break;                         
                                  }
                               }
                               if($existeAux==0){
                                  ?><script>setBusquedaCuenta('<?=$codigoCuenta;?>','<?=$numeroCuenta;?>','<?=$nombreCuenta;?>','0','');</script><?php   
                               }else{
                                 ?><script>setBusquedaCuenta('<?=$codigoCuenta?>','<?=$numeroCuenta?>','<?=$nombreCuenta?>','<?=$codigoCuentaAux?>','<?=$nombreCuentaAux?>');</script><?php   
                               }
                                  ?>

    <div class="col-sm-1">
            <div class="form-group">      
              <input class="form-control small" type="number" placeholder="0" value="<?=$debe?>" name="debe<?=$idFila;?>" id="debe<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01"> 
      </div>
    </div>
    <div class="col-sm-1">
            <div class="form-group">     
              <input class="form-control small" type="number" placeholder="0" value="<?=$haber?>" name="haber<?=$idFila;?>" id="haber<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01">   
      </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">  
        <textarea rows="1" class="form-control" name="glosa_detalle<?=$idFila;?>" id="glosa_detalle<?=$idFila;?>"><?=$glosa?></textarea>
      </div>
    </div>
    <div class="col-sm-1">
      <div class="btn-group">
      <a href="#" title="Retenciones" id="boton_ret<?=$idFila;?>" onclick="listRetencion(<?=$idFila;?>);" class="btn btn-warning text-dark btn-sm btn-fab">
             <i class="material-icons">ballot</i>
           </a>
      <a title="Facturas" href="#" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="btn btn-info btn-sm btn-fab <?=($cod_cuenta_configuracion_iva==$codigoCuenta)?'':'d-none';?>" >
              <i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span>
            </a>
            <a title="Actividad Proyecto SIS" id="boton_actividad_proyecto<?=$idFila?>" href="#" onclick="verActividadesProyectosSis(<?=$idFila;?>);" class="btn btn-sm btn-orange btn-fab d-none"><span class="material-icons">assignment</span><span id="nestadoactproy<?=$idFila?>" class="bg-warning"></span></a>
      <a title="Solicitudes de Recursos SIS" id="boton_solicitud_recurso<?=$idFila?>" href="#" onclick="verSolicitudesDeRecursosSis(<?=$idFila;?>);" class="btn btn-sm btn-default btn-fab d-none"><span class="material-icons text-dark">view_sidebar</span><span id="nestadosol<?=$idFila?>" class="bg-warning"></span></a>      
      <a title="Agregar Fila" id="boton_agregar_fila<?=$idFila?>" href="#" onclick="agregarFilaComprobante(<?=$idFila;?>);return false;" class="btn btn-sm btn-primary btn-fab"><span class="material-icons">add</span></a>              
            <a title="Eliminar (alt + q)" rel="tooltip" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="quitarFilaComprobante('<?=$idFila;?>');return false;">
                   <i class="material-icons">disabled_by_default</i>
            </a>
       </div>  
    </div>
   </div>
 </div>
 <div class="h-divider"></div>
</div>
<script>$("#div"+<?=$idFila?>).bootstrapMaterialDesign();</script>
      <?php
  }
  
}
?>
