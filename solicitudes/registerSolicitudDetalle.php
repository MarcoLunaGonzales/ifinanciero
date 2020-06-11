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


//distribucion gastosarea
$distribucionOfi=obtenerDistribucionCentroCostosUnidadActivo(); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
   while ($rowOfi = $distribucionOfi->fetch(PDO::FETCH_ASSOC)) {
    $codigoD=$rowOfi['codigo'];
    $codDistD=$rowOfi['cod_distribucion_gastos'];
    $codUnidadD=$rowOfi['cod_unidadorganizacional'];
    $porcentajeD=$rowOfi['porcentaje'];
    $nombreD=$rowOfi['nombre'];
     ?>
      <script>
        var distri = {
          codigo:<?=$codigoD?>,
          cod_dis:<?=$codDistD?>,
          unidad:<?=$codUnidadD?>,
          nombre:'<?=$nombreD?>',
          porcentaje:<?=$porcentajeD?>
        }
        itemDistOficina.push(distri);
      </script>  
      <?php
   }
$distribucionArea=obtenerDistribucionCentroCostosAreaActivo($globalUnidad); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
   while ($rowArea = $distribucionArea->fetch(PDO::FETCH_ASSOC)) {
    $codigoD=$rowArea['codigo'];
    $codDistD=$rowArea['cod_distribucionarea'];
    $codAreaD=$rowArea['cod_area'];
    $porcentajeD=$rowArea['porcentaje'];
    $nombreD=$rowArea['nombre'];
     ?>
      <script>
        var distri = {
          codigo:<?=$codigoD?>,
          cod_dis:<?=$codDistD?>,
          area:<?=$codAreaD?>,
          nombre:'<?=$nombreD?>',
          porcentaje:<?=$porcentajeD?>
        }
        itemDistArea.push(distri);
      </script>  
      <?php
   }


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
$sql="SELECT IFNULL(max(c.numero)+1,1)as codigo from solicitud_recursos c";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$nroCorrelativo=0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $nroCorrelativo=$row['codigo'];
}

?>
<input type="hidden" value="-100" id="tipo_documento_otro" name="tipo_documento_otro">
             <div id="combo_tipodocumento" class="d-none">
                <select class="selectpicker form-control form-control-sm" name="tipo_documento" id="tipo_documento" data-style="<?=$comboColor;?>" onChange="asignarTipoDocumento()">
                    <option disabled selected value="">TIPO DOCUMENTO</option>
                  <?php
                  $stmt = $dbh->prepare("SELECT * from ibnorca.vw_plantillaDocumentos where idTipoServicio=2708"); //2708 //2708 localhost
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$row['idClaDocumento'];
                  $nombreX=$row['Documento'];
                ?>
                <option value="<?=$codigoX;?>"><?=$nombreX;?></option>  
                <?php
                  }
                  ?>
                  <option value="-100">OTROS DOCUMENTOS</option>
               </select>
              </div>


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
          <div id="listaPro" class="row d-none">
            <div class="col-sm-2">
              <div class="form-group">
                  <select class="selectpicker form-control form-control-sm" name="tipo_solicitudproveedor" id="tipo_solicitudproveedor" data-style="btn btn-info">
                      <option value="1">CAPACITACIÓN</option> 
                      <option value="2">E. CONFORMIDAD</option> 
                  </select>
              </div>
            </div>
              <div class="col-sm-4">
              <div class="form-group">
                    <select class="selectpicker form-control form-control-sm" data-style="select-with-transition" data-live-search="true" title="-- Elija una cuenta --" name="cuenta_proveedor" id="cuenta_proveedor"  data-style="select-with-transition">
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
            <div class="col-sm-2">
              <button title="Buscar (alt + s)" type="button" id="boton_solicitudbuscar" name="boton_solicitudbuscar" class="btn btn-warning btn-round btn-fab" onClick="addSolicitudDetalleSearch()"><i class="material-icons">search</i></button>
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
              <div class="row col-sm-11 float-right">
            <div class="col-sm-2">
              <input type="hidden" name="n_distribucion" id="n_distribucion" value="0">
              <input type="hidden" name="nueva_distribucion" id="nueva_distribucion" value="0">
              <div class="btn-group dropdown">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle material-icons text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Distribucion de Gastos">
                      <i class="material-icons">call_split</i> <span id="distrib_icon" class="bg-warning"></span> <b id="boton_titulodist">Distribución</b>
                        </button>
                        <div class="dropdown-menu">   
                        <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion" onclick="cargarDistribucionSol(1)" class="dropdown-item">
                          <i class="material-icons">bubble_chart</i> x Oficina
                        </a>
                        <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion" onclick="cargarDistribucionSol(2)" class="dropdown-item">
                          <i class="material-icons">bubble_chart</i> x Área
                        </a>
                        <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion" onclick="cargarDistribucionSol(3)" class="dropdown-item">
                          <i class="material-icons">bubble_chart</i> x Oficina y x Área
                        </a>
                        <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion" onclick="cargarDistribucionSol(0)" class="dropdown-item">
                          <i class="material-icons">bubble_chart</i> Nínguna
                        </a>
                        </div>
                    </div>
                <div id="array_distribucion"></div>    
            </div>    
            <div class="col-sm-3">
                  <div class="form-group">
                    <select class="selectpicker form-control form-control-sm" data-live-search="true" name="proveedores" id="proveedores" data-style="<?=$comboColor;?>" onChange="cargarDatosCuenta()">
                    <option disabled selected value="">Asignar - Proveedores</option>
                  <?php
                  $stmt = $dbh->prepare("SELECT * FROM af_proveedores order by nombre");
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
            
            <div class="row col-sm-7 d-none" id="filtros_solicitud">
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
              <a id="buttonSubmitFalse" title="El Monto Solicitado es Mayor al Presupuestado" class="btn btn-warning text-dark d-none">Guardar <span class="material-icons text-dark">warning</span></a>
              <button id="buttonSubmit" type="submit" class="btn btn-success">Guardar</button>
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
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-info card-header-text">
                  <div class="card-text">
                    <h5>DOCUMENTOS DE RESPALDO</h5>      
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
      <div class="card-body">
           <!--<div class="fileinput fileinput-new col-md-12" data-provides="fileinput">
            <div class="row">
              <div class="col-md-12">
                <div class="border" id="lista_archivos">Ningun archivo seleccionado</div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <span class="btn btn-info btn-info btn-file btn-sm">
                      <span class="fileinput-new">Buscar</span>
                      <span class="fileinput-exists">Cambiar</span>
                      <input type="file" name="archivos[]" id="archivos" multiple="multiple"/>
                   </span>
                <a href="#" class="btn btn-danger btn-sm fileinput-exists" onclick="archivosPreview(1)" data-dismiss="fileinput"><i class="material-icons">clear</i> Quitar</a>
              </div>
            </div>
           </div>-->
           <p class="text-muted"><small>Los archivos se subir&aacute;n al servidor cuando se GUARDE la Solicitud de Recurso</small></p>
            <div class="row col-sm-11 div-center">
              <table class="table table-warning table-bordered table-condensed">
                <thead>
                  <tr>
                    <th class="small" width="30%">Tipo de Documento <a href="#" title="Otro Documento" class="btn btn-primary btn-round btn-sm btn-fab float-left" onClick="agregarFilaArchivosAdjuntosCabecera()"><i class="material-icons">add</i></a></th>
                    <th class="small">Obligatorio</th>
                    <th class="small" width="35%">Archivo</th>
                    <th class="small">Descripción</th>                  
                  </tr>
                </thead>
                <tbody id="tabla_archivos">
                  <?php
                  $stmtArchivo = $dbh->prepare("SELECT * from ibnorca.vw_plantillaDocumentos where idTipoServicio=2708"); //2708 //2708 localhost
                  $stmtArchivo->execute();
                  $filaA=0;
                  while ($rowArchivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC)) {
                     $filaA++;
                     $codigoX=$rowArchivo['idClaDocumento'];
                     $nombreX=$rowArchivo['Documento'];
                     $ObligatorioX=$rowArchivo['Obligatorio'];
                     $Obli='<i class="material-icons text-danger">clear</i> NO';
                     if($ObligatorioX==1){
                      $Obli='<i class="material-icons text-success">done</i> SI';
                     }
                  ?>
                  <tr>
                    <td class="text-left"><input type="hidden" name="codigo_archivo<?=$filaA?>" id="codigo_archivo<?=$filaA?>" value="<?=$codigoX;?>"><input type="hidden" name="nombre_archivo<?=$filaA?>" id="nombre_archivo<?=$filaA?>" value="<?=$nombreX;?>"><?=$nombreX;?></td>
                    <td class="text-center"><?=$Obli?></td>
                    <td class="text-right">
                      <small id="label_txt_documentos_cabecera<?=$filaA?>"></small> 
                      <span class="input-archivo">
                        <input type="file" class="archivo" name="documentos_cabecera<?=$filaA?>" id="documentos_cabecera<?=$filaA?>"/>
                      </span>
                      <label title="Ningún archivo" for="documentos_cabecera<?=$filaA?>" id="label_documentos_cabecera<?=$filaA?>" class="label-archivo btn btn-warning btn-sm"><i class="material-icons">publish</i> Subir Archivo
                      </label>
                    </td>    
                    <td><?=$nombreX;?></td>
                  </tr> 
                  <?php
                   }
                  ?>       
                </tbody>
              </table>
              <input type="hidden" value="<?=$filaA?>" id="cantidad_archivosadjuntos" name="cantidad_archivosadjuntos">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="" class="btn btn-success" data-dismiss="modal">Aceptar
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
