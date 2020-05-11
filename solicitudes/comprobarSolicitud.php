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
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r="nn";
  if(isset($_GET['r'])){
   $r=$_GET['r'];
  } 
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];
}
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

//distribucion gastosarea
$distribucionOfi=obtenerDistribucionCentroCostosUnidadActivo(); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
   while ($rowOfi = $distribucionOfi->fetch(PDO::FETCH_ASSOC)) {
    $codigoD=$rowOfi['codigo'];
    $codDistD=$rowOfi['cod_distribucion_gastos'];
    $codUnidadD=$rowOfi['cod_unidadorganizacional'];
    $porcentajeD=$rowOfi['porcentaje'];
    $nombreD=$rowOfi['nombre'];
    $porcentajeD=obtenerPorcentajeDistribucionGastoSolicitud($porcentajeD,1,$codUnidadD,$_GET['cod']);
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
$distribucionArea=obtenerDistribucionCentroCostosAreaActivo(); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
   while ($rowArea = $distribucionArea->fetch(PDO::FETCH_ASSOC)) {
    $codigoD=$rowArea['codigo'];
    $codDistD=$rowArea['cod_distribucionarea'];
    $codAreaD=$rowArea['cod_area'];
    $porcentajeD=$rowArea['porcentaje'];
    $nombreD=$rowArea['nombre'];
    $porcentajeD=obtenerPorcentajeDistribucionGastoSolicitud($porcentajeD,2,$codAreaD,$_GET['cod']);
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

   $valorDistribucion=obtenerSiDistribucionSolicitudRecurso($_GET['cod']);
   $estadoDistribucion="";
   $titDistribucion="Distribución";
   if($valorDistribucion!=0){
     $estadoDistribucion.=" estado";
     if($valorDistribucion==1){
      $titDistribucion="x Oficina";
     }else{
       if($valorDistribucion==2){
        $titDistribucion="x Área";
       }else{
        $titDistribucion="x Oficina y x Área";
       }
     }
   }


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
if(isset($_GET['cod'])){
  $codigo=$_GET['cod'];
  $codigoSolicitud=$_GET['cod'];
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
<form id="formSolDet" class="form-horizontal" action="saveEditAprobado.php" method="post" enctype="multipart/form-data">
<div class="content">
  <div id="contListaGrupos" class="container-fluid">
      <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
      <input type="hidden" name="cod_solicitud" id="cod_solicitud" value="<?=$codigo?>">
      <input type="hidden" name="cod_configuracioniva" id="cod_configuracioniva" value="<?=obtenerValorConfiguracion(35)?>">
      <?php 
      if(isset($_GET['q'])){
        ?><input type="hidden" name="usuario_ibnored" id="usuario_ibnored" value="<?=$q;?>">
          <input type="hidden" name="usuario_ibnored_rol" id="usuario_ibnored_rol" value="<?=$r;?>">
          <input type="hidden" name="usuario_ibnored_s" id="usuario_ibnored_s" value="<?=$s;?>">
        <input type="hidden" name="usuario_ibnored_u" id="usuario_ibnored_u" value="<?=$u;?>">
        <input type="hidden" name="usuario_ibnored_v" id="usuario_ibnored_v" value="<?=$v;?>">
        <?php
      }
      if(isset($_GET['admin'])){
       ?><input type="hidden" name="control_admin" id="control_admin" value="0"><?php 
      }
      ?>
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-text">
          <div class="card-text">
            <h4 class="card-title">Edici&oacute;n <?=$moduleNameSingular;?></h4>
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
                  if($codSimulacionServX==0){
                    $tipoSolicitud=4;
                  }else{
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
                    
                  }
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
              if($tipoSolicitud==1||$tipoSolicitud==3||$tipoSolicitud==4){
              ?><button title="Agregar (alt + n)" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addSolicitudDetalle(this,<?=$tipoSolicitud?>)"><i class="material-icons">add</i>
                  </button><?php
              }else{
              ?><button title="Buscar (alt + s)" type="button" id="boton_solicitudbuscar" name="boton_solicitudbuscar" class="btn btn-warning btn-round btn-fab" onClick="addSolicitudDetalleSearch()"><i class="material-icons">search</i>
                  </button><button title="Agregar (alt + n)" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addSolicitudDetalle(this,<?=$tipoSolicitud?>)"><i class="material-icons">add</i>
                  </button><?php
              } 
              ?>
                 
              <div class="float-right">
                <div class="col-sm-2">
              <input type="hidden" name="n_distribucion" id="n_distribucion" value="<?=$valorDistribucion?>">
              <input type="hidden" name="nueva_distribucion" id="nueva_distribucion" value="<?=$valorDistribucion?>">
              <div class="btn-group dropdown">
                      <button type="button" class="btn btn-sm btn-success dropdown-toggle material-icons text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Distribucion de Gastos">
                      <i class="material-icons">call_split</i> <span id="distrib_icon" class="bg-warning <?=$estadoDistribucion?>"></span> <b id="boton_titulodist"><?=$titDistribucion?></b>
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
            </div> 
              </div>
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
               include "solicitudDetalleSimulacionRegistrado2.php";
            }else{
              if($tipoSolicitud==3){
                include "solicitudDetalleSimulacionRegistrado3.php";
              }else{
                if($tipoSolicitud==2){
                 ?><div id="solicitud_proveedor"></div><?php                
                }else{
                 include "solicitudDetalleSimulacionRegistrado4.php";
                }    
              }
            }
            ?>
            
          </fieldset>
            <div class="card-footer fixed-bottom">
              <button type="submit" class="btn btn-success">Guardar</button>
              <?php 
               if(isset($_GET['q'])){
                if(isset($_GET['admin'])){
                 ?>  
                  <a href="../<?=$urlList2;?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>" class="<?=$buttonCancel;?>">Volver</a> 
                <?php
                }else{
                  ?>  
                  <a href="../<?=$urlList;?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="<?=$buttonCancel;?>">Volver</a> 
                <?php
                }
                
               }else{
                if(isset($_GET['admin'])){
                 ?>
                    <a href="../<?=$urlList2;?>" class="<?=$buttonCancel;?>">Volver</a> 
                <?php
                }else{
                  ?>
                     <a href="../<?=$urlList;?>" class="<?=$buttonCancel;?>">Volver</a> 
                <?php
                }
                
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
        <p>Cargar archivos de respaldo.</p> 
           <div class="fileinput fileinput-new col-md-12" data-provides="fileinput">
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
           </div>
           <p class="text-muted"><small>Los archivos se subir&aacute;n al servidor cuando se GUARDE la solicitud</small></p>
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
<script>calcularTotalesSolicitud()</script> 
