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

if(isset($_GET['q'])){
  $q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];
}

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$contadorRegistros=0;

?>
<script>
  numFilas=<?=$contadorRegistros;?>;
  cantidadItems=<?=$contadorRegistros;?>;
</script>

<?php
$fechaActual=date("Y-m-d");
$fechaActualFormat=date("d/m/Y");
$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde="01/".$m."/".$y;
$fechaHasta=$d."/".$m."/".$y;

$dbh = new Conexion();
echo "<script>var array_cuenta=[];</script>";
$i=0;
  $cuentaLista=obtenerCuentasListaSolicitud(); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
   while ($rowCuenta = $cuentaLista->fetch(PDO::FETCH_ASSOC)) {
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

$cuentaCombo=obtenerCuentasSimulaciones(5,3,$globalUser);
$i=0;
  while ($rowCombo = $cuentaCombo->fetch(PDO::FETCH_ASSOC)) {
   $codigoX=$rowCombo['codigo'];
   $numeroX=$rowCombo['numero'];
   $nombreX=$rowCombo['nombre'];
   $nivelX=$rowCombo['nivel'];
   $arrayNuevo[$i][0]=$codigoX;
   $arrayNuevo[$i][1]=trim($numeroX);
   $arrayNuevo[$i][2]=trim($nombreX);
   $arrayNuevo[$i][3]=$nivelX;
    $i++;
  }



$codigo=0;

$unidad=$_SESSION['globalUnidad'];
//numero correlativo de la solicitud
$sql="SELECT IFNULL(max(c.codigo)+1,1)as codigo from solicitud_recursos c where c.cod_unidadorganizacional=$unidad";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nroCorrelativo=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $nroCorrelativo=$row['codigo'];
}
?>
<div class="cargar">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<form id="formSolDet" class="form-horizontal" action="saveEdit.php" method="post" enctype="multipart/form-data">
<div class="content">
  <div id="contListaGrupos" class="container-fluid">
      <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
      
      <input type="hidden" name="cod_configuracioniva" id="cod_configuracioniva" value="<?=obtenerValorConfiguracion(35)?>">
      
      <?php 
      if(isset($_GET['q'])){
        ?><input type="hidden" name="usuario_ibnored" id="usuario_ibnored" value="<?=$q;?>">
        <input type="hidden" name="usuario_ibnored_s" id="usuario_ibnored_s" value="<?=$s;?>">
        <input type="hidden" name="usuario_ibnored_u" id="usuario_ibnored_u" value="<?=$u;?>">
        <input type="hidden" name="usuario_ibnored_v" id="usuario_ibnored_v" value="<?=$v;?>">

        <input type="hidden" name="ibnorca_q" id="ibnorca_q" value="<?=$q;?>">
        <input type="hidden" name="ibnorca_s" id="ibnorca_s" value="<?=$s;?>">
        <input type="hidden" name="ibnorca_u" id="ibnorca_u" value="<?=$u;?>">
        <input type="hidden" name="ibnorca_v" id="ibnorca_v" value="<?=$v;?>">
        <?php
      }
      ?> 
      <div class="card">
        <div class="card-header card-header-danger card-header-text">
          <div class="card-text">
            <h4 class="card-title">Nueva <?=$moduleNameSingular;?> <?php 
      if(isset($_GET['v'])){
        $codigoServicioTitulo=obtenerCodigoServicioPorIdServicio($_GET['v']);
        echo " ".$codigoServicioTitulo;
      }
      ?>
       </h4>
          </div>
        </div>
        <div class="card-body ">
          <div class="row"> 
            <div class="col-sm-1">
              <div class="form-group">
                  <label class="bmd-label-static">Numero</label>
                  <input class="form-control" type="text" name="numero" value="<?=$nroCorrelativo?>" id="numero" readonly/>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Fecha Solicitud</label>
                  <input class="form-control" type="text" name="nfecha_solicitudumero" value="<?=$fechaActualFormat?>" id="numero" readonly/>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                  <select class="selectpicker form-control form-control-sm" onchange="listarTipoSolicitud(this.value,'none')" name="tipo_solicitud" id="tipo_solicitud" data-style="btn btn-info" title="-- Tipo de Solicitud --" required>
                      <option value="1">POR PROPUESTA</option> 
                      <option value="2">POR PROVEEDOR</option>
                      <option value="3">MANUAL</option>  
                  </select>
              </div>
            </div>             
            <div class="col-sm-3" id="lista_tipo">
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                  <a href="#" class="btn btn-sm btn-warning d-none" id="buscar_solicitudesdetalle" onclick="filtrarSolicitudRecursosDetalleDatos()"><i class="material-icons">search</i> BUSCAR DETALLES</a>
              </div>
            </div>
            <div class="col-sm-2">
               <div class="btn-group">
                  <a title="Subir Archivos Respaldo (shift+r)" href="#modalFile" data-toggle="modal" data-target="#modalFile" class="btn btn-default btn-sm">Archivos 
                    <i class="material-icons"><?=$iconFile?></i><span id="narch" class="bg-warning"></span>
                  </a>
                </div> 
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
               <div class="form-group">
                  <label class="bmd-label-static">Observaciones</label>
                  <textarea type="text" id="observaciones_solicitud" name="observaciones_solicitud" class="form-control"></textarea>
                </div> 
            </div>
          </div>          
        </div>
      </div>



    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header card-header-info card-header-text">
            <div class="card-text">
              <h6 class="card-title">Detalle de Solicitud</h6>
            </div>
          </div>
          <div class="card-body">
            <fieldset style="width:100%;border:0;">
              <button title="Agregar (alt + n)" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addSolicitudDetalle(this,3)"><i class="material-icons">add</i>
              </button>
              <div class="row col-sm-10 float-right">
            <div class="col-sm-3">
                  <div class="form-group">
                    <select class="selectpicker form-control form-control-sm" data-live-search="true" name="proveedores" id="proveedores" data-style="<?=$comboColor;?>" onChange="cargarDatosCuenta()">
                    <option disabled selected value="">Asignar - Proveedores</option>
                  <?php
                  $stmt = $dbh->prepare("SELECT * FROM af_proveedores order by codigo");
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$row['codigo'];
                  $nombreX=$row['nombre'];
                ?>
                <option value="<?=$codigoX;?>"><?=$nombreX;?></option>  
                <?php
                  }
                  ?>
              </select>
              </div>    
            </div>
            <div class="col-sm-1">
            </div>
            <div class="row col-sm-8 d-none" id="filtros_solicitud">
              <div class="form-group col-sm-4">
                    <select class="selectpicker form-control form-control-sm" data-style="<?=$comboColor;?>" name="anio_solicitud" id="anio_solicitud">
                     <option value="all" selected>TODOS</option>
                    </select>
                </div>
                <div class="form-group col-sm-4">
                    <select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" title="-- Elija el detalle --" name="item_detalle_solicitud" id="item_detalle_solicitud"  data-style="select-with-transition" required>
                     <option value="all" selected>TODOS</option>
                    </select>
                </div>
                <a href="#" class="btn btn-sm btn-warning" onclick="filtrarSolicitudRecursosServiciosItems()">FILTRAR LA SOLICITUD</a>           
            </div>
          </div>
              <div id="div">   
                 <div class="h-divider"></div>     
              </div>
              <div id="fiel">
              </div>
            </fieldset>  
            <div class="card-footer fixed-bottom">
              <button type="submit" class="<?=$buttonMorado;?>">Guardar</button>
              <?php 
               if(isset($_GET['q'])){
                ?>
                 <a href="../<?=$urlList;?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="<?=$buttonCancel;?>">Volver</a> 
                <?php
               }else{
                ?>
                 <a href="../<?=$urlList;?>" class="<?=$buttonCancel;?>">Volver</a> 
                <?php
               }
              ?>  
               <a href="#" onclick="cargarDatosRegistroProveedor()" class="btn btn-warning float-right">Agregar Proveedor</a>
               <a href="#" onclick="actualizarRegistroProveedor()" class="btn btn-success float-right">Actualizar Proveedores</a>
               <div class="row col-sm-12">
                    <div class="col-sm-1">
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="bmd-label-static fondo-boton">Presupuestado</label>  
                          <input class="form-control fondo-boton-active text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="0" id="total_presupuestado" readonly="true"> 
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                          <label class="bmd-label-static fondo-boton">Solicitado</label> 
                          <input class="form-control fondo-boton-active text-center" style="border-radius:10px;" type="number" step=".01" placeholder="0" value="0" id="total_solicitado" readonly="true"> 
                        </div>
                    </div>
              </div>
          </div>
        </div>
      </div>      
    </div>
  </div>
 </div>
</div>
<!-- small modal -->
<div class="modal fade modal-primary" id="modalFile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <i class="material-icons" data-notify="icon"><?=$iconFile?></i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <p>Cargar archivos de respaldo.</p> 
           <div class="fileinput fileinput-new col-md-12" data-provides="fileinput">
            <div class="row">
              <div class="col-md-9">
                <div class="border" id="lista_archivos">Ningun archivo seleccionado</div>
              </div>
              <div class="col-md-3">
                <span class="btn btn-info btn-round btn-file">
                      <span class="fileinput-new">Buscar</span>
                      <span class="fileinput-exists">Cambiar</span>
                      <input type="file" name="archivos[]" id="archivos" multiple="multiple"/>
                   </span>
                <a href="#" class="btn btn-danger btn-round fileinput-exists" onclick="archivosPreview(1)" data-dismiss="fileinput"><i class="material-icons">clear</i> Quitar</a>
              </div>
            </div>
           </div>
           <p class="text-danger">Los archivos se subir&aacute;n al servidor cuando se GUARDE la solicitud</p>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="" class="btn btn-link" data-dismiss="modal">Aceptar
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->
</form>
<?php
require_once 'modal.php';

if(isset($_GET['sim'])){
  $sim=$_GET['sim'];
  $det=$_GET['det'];
  if($det!=1){
    $detalle="TCP";
  }else{
    $detalle="SIM";
  }
  ?>
  <script>
  $(document).ready(function() {
    $("#tipo_solicitud").html("");
    $('.selectpicker').selectpicker("refresh");
    $("#tipo_solicitud").append("<option selected value='1'>POR PROPUESTA</option>");
    $('.selectpicker').selectpicker("refresh");

    listarTipoSolicitud(1,'<?=$sim?>$$$<?=$detalle?>');
    //se eliminan las demas solicitudes
  });
  </script>
  <?php
}else{
  if(isset($_GET['v'])){
    $idPropuesta=obtenerIdPropuestaServicioIbnorca($v);
    $areaServicio=obtenerIdAreaServicioIbnorca($v);
     if($areaServicio==39||$areaServicio==38){
      $detalle="TCP";
     }else{
       $detalle="SIM";
     }
    if($idPropuesta!="NONE"){
    ?>
  <script>
  $(document).ready(function() {
    $("#tipo_solicitud").html("");
    $('.selectpicker').selectpicker("refresh");
    $("#tipo_solicitud").append("<option selected value='1'>POR PROPUESTA</option>");
    $('.selectpicker').selectpicker("refresh");
    listarTipoSolicitud(1,'<?=$idPropuesta?>$$$<?=$detalle?>');
    //se eliminan las demas solicitudes
   });
   </script>
   <?php    
    }else{
      //servicio SIN PROPUESTA "OI" 
      ?>
  <script>
  $(document).ready(function() {
    $("#tipo_solicitud").html("");
    $('.selectpicker').selectpicker("refresh");
    $("#tipo_solicitud").append("<option selected value='3'>MANUAL</option>");
    $('.selectpicker').selectpicker("refresh");
    listarTipoSolicitud(3,'none');
    //se eliminan las demas solicitudes
   });
   </script>
   <?php 
    }
  }
}

?>
