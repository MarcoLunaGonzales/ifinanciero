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

$contadorRegistros=0;
?>
<script>
  numFilas=<?=$contadorRegistros;?>;
  cantidadItems=<?=$contadorRegistros;?>;
</script>

<?php
$fechaActual=date("Y-m-d");
$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde="01/".$m."/".$y;
$fechaHasta=$d."/".$m."/".$y;

$dbh = new Conexion();
echo "<script>var array_cuenta=[];</script>";
$i=0;
  $cuentaLista=obtenerCuentasLista(5,[5,4]); //null para todas las iniciales del numero de cuenta
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

if(isset($_GET['cod'])){
  $codigo=$_GET['cod'];
}else{
  $codigo=0;
}
      $stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_solicitud, u.abreviatura as unidad,a.abreviatura as area 
        from solicitud_recursos p,unidades_organizacionales u, areas a,estados_solicitudrecursos e
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadosolicitudrecurso and p.codigo='$codigo' order by codigo");
      $stmt->execute();
      $stmt->bindColumn('codigo', $codigoX);
            $stmt->bindColumn('cod_personal', $codPersonalX);
            $stmt->bindColumn('fecha', $fechaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);
            $stmt->bindColumn('estado_solicitud', $estadoX);
            $stmt->bindColumn('numero', $numeroX);
            $stmt->bindColumn('cod_simulacion', $codSimulacionX);
            $stmt->bindColumn('cod_simulacionservicio', $codSimulacionServX);
            $stmt->bindColumn('cod_proveedor', $codProveedorX);
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
      <input type="hidden" name="cod_solicitud" id="cod_solicitud" value="<?=$codigo?>">

      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title">Datos <?=$moduleNameSingular;?></h4>
          </div>
        </div>
        <div class="card-body ">
         <div class="row">
          <?php while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
            $solicitante=namePersonal($codPersonalX);
            $fechaSolicitud=strftime('%d/%m/%Y',strtotime($fechaX));
            ?>
          <input class="form-control" type="hidden" name="cod_unidad" value="<?=$codUnidadX?>" id="cod_unidad" readonly/>
          <input class="form-control" type="hidden" name="cod_area" value="<?=$codAreaX?>" id="cod_area" readonly/>
            <div class="col-sm-3">
              <div class="form-group">
                  <label class="bmd-label-static">Solicitante</label>
                  <input class="form-control" type="text" name="nombre" value="<?=$solicitante?>" id="nombre" readonly/>
              </div>
            </div>

            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Fecha</label>
                  <input class="form-control" type="text" name="fecha_solicitud" value="<?=$fechaSolicitud?>" id="fecha_solicitud" readonly/>
              </div>
            </div>
            <div class="col-sm-1">
              <div class="form-group">
                  <label class="bmd-label-static">Numero</label>
                  <input class="form-control" type="number" name="numero_solicitud" value="<?=$numeroX?>" id="numero_solicitud" readonly/>
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Estado</label>
                  <input class="form-control" type="text" name="estado_solicitud" value="<?=$estadoX?>" id="estado_solicitud" readonly/>
              </div>
            </div>
            <div class="col-sm-1">
              <div class="form-group">
                  <label class="bmd-label-static">Unidad</label>
                  <input class="form-control" type="text" name="unidad" value="<?=$unidadX?>" id="unidad" readonly/>
              </div>
            </div>

            <div class="col-sm-1">
                  <div class="form-group">
                  <label class="bmd-label-static">Area</label>
                  <input class="form-control" type="text" name="area" value="<?=$areaX?>" id="area" readonly/>
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
             <?php 
              if($codSimulacionX!=0){
                $tipoSolicitud=1;
                $nombreSimulacion=nameSimulacion($codSimulacionX);
              ?>
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Curso</label>
                  <input class="form-control" type="text" name="simulacion" value="<?=$nombreSimulacion?>" id="simulacion" readonly/>
              </div>
            </div>
              <?php
              }else{
                if($codProveedorX==0){
                  $tipoSolicitud=3;
                $nombreSimulacion=nameSimulacionServicio($codSimulacionServX);
              ?>
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">TCP / TCS</label>
                  <input class="form-control" type="text" name="simulacion" value="<?=$nombreSimulacion?>" id="simulacion" readonly/>
              </div>
            </div>
              <?php
                }else{
               $tipoSolicitud=2;
               ?>
            <div class="col-sm-3">
              <!--<div class="form-group">
                  <label class="bmd-label-static">Partida Pres./ Cuenta</label>
                  <input class="form-control" type="text" name="plan_cuenta" value="" id="plan_cuenta" readonly/>
              </div>-->
              <div class="form-group">
                    <select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" title="-- Elija una cuenta --" name="cuenta_proveedor" id="cuenta_proveedor"  data-style="select-with-transition" required>
                      <?php
                        for ($i=0; $i < count($arrayNuevo); $i++) {
                        $solicitudDet=obtenerSolicitudRecursosDetalleCuenta($codigo,$arrayNuevo[$i][0]);
                        $contExiste=0;
                        while ($rowSolDet = $solicitudDet->fetch(PDO::FETCH_ASSOC)) {
                          $contExiste++;
                        }
                         if($contExiste!=0){
                           ?><option value="<?=$arrayNuevo[$i][0];?>">[<?=$arrayNuevo[$i][1]?>] - <?=$arrayNuevo[$i][2]?> (&#9679; activo)</option>  <?php
                         }else{
                          ?><option value="<?=$arrayNuevo[$i][0];?>">[<?=$arrayNuevo[$i][1]?>] - <?=$arrayNuevo[$i][2]?></option>  <?php
                         }
                        }
                      ?>
                    </select>
                </div>
            </div>   
            <div class="col-sm-3">
                  <div class="form-group">
                    <select class="selectpicker form-control form-control-sm" name="proveedores" id="proveedores" data-style="<?=$comboColor;?>" onChange="cargarDatosCuenta()">
                    <option disabled selected value="">Proveedores</option>
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
            <div class="col-sm-2">
               <p class="text-muted">Se muestran los curso en fechas</p> 
            </div>  
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Desde</label>
                  <input type="text" class="form-control datepicker" name="fecha_desde" id="fecha_desde" value="<?=$fechaDesde?>">
              </div>
            </div>
            <div class="col-sm-2">
              <div class="form-group">
                  <label class="bmd-label-static">Hasta</label>
                  <input type="text" class="form-control datepicker" name="fecha_hasta" id="fecha_hasta" value="<?=$fechaHasta?>">
              </div>
            </div>
              <?php
              }
             }
             ?>
          </div>
          <?php } //fin del while de la cabecera?>
        </div>
      </div>



    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header <?=$colorCard;?> card-header-text">
            <div class="card-text">
              <h6 class="card-title">Detalle</h6>
            </div>
          </div>
          <div class="card-body">
             <fieldset id="fiel" style="width:100%;border:0;">
              <?php 
              if($tipoSolicitud==1||$tipoSolicitud==3){
              ?><button title="Agregar (alt + n)" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addSolicitudDetalle(this,<?=$tipoSolicitud?>)"><i class="material-icons">add</i>
                  </button><?php
              }else{
              ?><button title="Buscar (alt + s)" type="button" id="boton_solicitudbuscar" name="boton_solicitudbuscar" class="btn btn-warning btn-round btn-fab" onClick="addSolicitudDetalleSearch()"><i class="material-icons">search</i>
                  </button><button title="Agregar (alt + n)" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addSolicitudDetalle(this,<?=$tipoSolicitud?>)"><i class="material-icons">add</i>
                  </button><?php
              } 
              ?>
                 

             <div id="div">   
              <div class="h-divider"></div>     
             </div>
            <?php
                       $stmt = $dbh->prepare("SELECT p.* from solicitud_recursosdetalle p where p.cod_solicitudrecurso=$codigo order by p.codigo");
                         $stmt->execute();
                         $idFila=1;
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $codigoCostoX=$row['codigo'];
                          
                        }
            
            if($tipoSolicitud==1){
               include "solicitudDetalleSimulacion2.php";
            }else{
              if($tipoSolicitud==3){
                include "solicitudDetalleSimulacion3.php";
              }else{
              ?><div id="solicitud_proveedor"></div><?php
               //include "solicitudDetalleProveedor.php";     
              }
            }
            ?>
            
          </fieldset>
            <div class="card-footer fixed-bottom">
               <button type="submit" class="<?=$buttonMorado;?>">Guardar</button>
               <a href="../<?=$urlList;?>" class="<?=$buttonCancel;?>">Volver</a>
               <a href="#" onclick="cargarDatosRegistroProveedor()" class="btn btn-warning float-right">Agregar Proveedor</a>
               <a href="#" onclick="actualizarRegistroProveedor()" class="btn btn-success float-right">Actualizar Proveedores</a>
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
?>
