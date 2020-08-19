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


   $indexArea=0;
     $stmtAreas = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
      $stmtAreas->execute();
      while ($row = $stmtAreas->fetch(PDO::FETCH_ASSOC)) {
       $codigoX=$row['codigo'];
       $nombreX=$row['nombre'];
       $abrevX=$row['abreviatura'];
       $porcentajeA=obtenerPorcentajeDistribucionGastoSolicitudGeneral(0,2,$codigoX,$_GET['cod'],0);
      ?>
      <script>
        var distri = {
          fila:<?=$codigoX?>,
          codigo:1,
          cod_dis:2,
          area:<?=$codigoX?>,
          nombre:'<?=$nombreX?> - <?=$abrevX?>',
          porcentaje:<?=$porcentajeA?>
        }
          itemDistAreaGlobal.push(distri);
        
      </script> 
        <?php
        $distribucionOfi=obtenerDistribucionCentroCostosUnidadActivo(); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
        while ($rowOfi = $distribucionOfi->fetch(PDO::FETCH_ASSOC)) {
          $codigoD=$rowOfi['codigo'];
          $codDistD=$rowOfi['cod_distribucion_gastos'];
          $codUnidadD=$rowOfi['cod_unidadorganizacional'];
          $porcentajeD=$rowOfi['porcentaje'];
          $nombreD=$rowOfi['nombre'];
          $porcentajeD=obtenerPorcentajeDistribucionGastoSolicitudGeneral(0,1,$codUnidadD,$_GET['cod'],$codigoX);
        ?>
         <script>
         var ofi = {
              cod_fila:<?=$codigoX?>,
              codigo:<?=$codigoD?>,
              cod_dis:<?=$codDistD?>,
              unidad:<?=$codUnidadD?>,
              nombre:'<?=$nombreD?>',
              porcentaje:<?=$porcentajeD?>
         }
        itemDistOficinaGeneral.push(ofi);
         </script>  
      <?php
        }

        $indexArea++;
      }

   $valorDistribucion=obtenerSiDistribucionSolicitudRecurso($_GET['cod']);
   $estadoDistribucion="";
   $titDistribucion="Distribución";
   if($valorDistribucion!=0){
     $estadoDistribucion.=" estado";
     if($valorDistribucion==1){
      $titDistribucion="x Of";
     }else{
       if($valorDistribucion==2){
        $titDistribucion="x Área";
       }else{
        if($valorDistribucion==3){
          $titDistribucion="x Of y x Área";  
        }else{
          $titDistribucion="x Área y x Of";
        }
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
  <input type="hidden" value="-100" id="tipo_documento_otro" name="tipo_documento_otro">
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
        if($_GET['admin']==2){
          ?><input type="hidden" name="control_admin" id="control_admin" value="1"><?php 
        }else{
          ?><input type="hidden" name="control_admin" id="control_admin" value="0"><?php 
        }
       
      }
      if(isset($_GET['reg'])){
        if($_GET['reg']==2){
          ?><input type="hidden" name="control_adminreg" id="control_adminreg" value="2"><?php  
        }else{
          ?><input type="hidden" name="control_adminreg" id="control_adminreg" value="0"><?php  
        }
       
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
            $distribucionArea=obtenerDistribucionCentroCostosAreaActivo($unidadX); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
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
                  <!--<label class="bmd-label-static">Unidad</label>-->
                  <select class="selectpicker form-control form-control-sm" name="unidad_solicitud" onchange="cargarArrayAreaDistribucion(-1)" id="unidad_solicitud" data-style="btn btn-primary">
                       <option disabled value="">--OFICINA--</option>               
                                      <?php
                                   $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
                                   $stmt->execute();
                                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                    if($codigoX==$codUnidadX){
                                     ?><option selected value="<?=$codigoX;?>"><?=$abrevX;?></option><?php 
                                   }else{
                                    //if($v==0){
                                      ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                    //}      
                                   }
                                       
                                      }
                                    ?>
                  </select>
                  <!--<input class="form-control" type="text" name="unidad" value="<?=$unidadX?>" id="unidad" readonly/>-->
              </div>
            </div>

            <div class="col-sm-1">
                  <div class="form-group">
                  <!--<label class="bmd-label-static">Area</label>
                  <input class="form-control" type="text" name="area" value="<?=$areaX?>" id="area" readonly/>-->
                  <select class="selectpicker form-control form-control-sm" name="area_solicitud" id="area_solicitud" data-style="btn btn-rose">
                                   <option disabled value="">--AREA--</option>                 
                                     <?php
                                                             
                                           $stmt = $dbh->prepare("SELECT a.codigo, a.nombre, a.abreviatura FROM areas a join areas_activas aa on aa.cod_area=a.codigo where a.cod_estado=1 order by 2");
                                         $stmt->execute();
                                         $cont=0;
                                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                           $codigoX=$row['codigo'];
                                           $nombreX=$row['nombre'];
                                           $abrevX=$row['abreviatura'];
                                           if($codigoX==$codAreaX){
                                             ?><option selected value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                           }else{
                                            //if($v==0){
                                              ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                            //}          
                                           }
                                            
                                         } 
                                         ?>
                                        </select>
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
               }
             }
             ?>
             <div class="col-sm-3 d-none">
                  <div class="form-group">
                    <select class="selectpicker form-control form-control-sm" data-live-search="true" data-size="6" name="proveedores" id="proveedores" data-style="<?=$comboColor;?>" onChange="cargarDatosCuenta()">
                    <option disabled selected value="">Proveedores</option>
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
          </div>
          <?php } //fin del while de la cabecera?>
          <div class="row">
            
            <div class="col-sm-3">
              <label class="bmd-label-static">Persona que Procesará el Pago</label>
              <?php
              if(verificarEdicionComprobanteUsuario($globalUser)!=0){
               ?>
               <!--    end small modal -->
<?php 
 $arrayEnc=obtenerPersonalEncargadoSolicitud($codigoSolicitud);
 $arrayEncargados=implode(",",$arrayEnc[0]);
 $queryEncargado="";
 $queryEncargadoNot="";
 if(count($arrayEnc[0])>0){
  $queryEncargado="where p.codigo in ($arrayEncargados)";
  $queryEncargadoNot="where p.codigo not in ($arrayEncargados)";
 }
?>
                    <select class="selectpicker form-control form-control-sm" name="personal_encargado[]" multiple id="personal_encargado" data-live-search="true" data-size="6"data-style="btn btn-default text-dark" data-actions-box="true">
                         <?php 
                         if(count($arrayEnc[0])>0){
                         $stmt3 = $dbh->prepare("SELECT p.codigo,CONCAT_WS(' ',p.primer_nombre,p.materno,p.paterno)as nombre from personal p join configuracion_encargado c on c.cod_personal=p.codigo $queryEncargado");
                         $stmt3->execute();
                          while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                           $codigoSel=$rowSel['codigo'];
                          $nombreSelX=$rowSel['nombre'];
                          ?><option value="<?=$codigoSel;?>" selected><?=$nombreSelX?></option><?php 
                          }
                        }
                        ?>

                        <?php 
                         $stmt3 = $dbh->prepare("SELECT p.codigo,CONCAT_WS(' ',p.primer_nombre,p.materno,p.paterno)as nombre from personal p join configuracion_encargado c on c.cod_personal=p.codigo $queryEncargadoNot");
                         $stmt3->execute();
                          while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                           $codigoSel=$rowSel['codigo'];
                          $nombreSelX=$rowSel['nombre'];
                          ?><option value="<?=$codigoSel;?>"><?=$nombreSelX?></option><?php 
                          }
                        ?>
                    </select>
               
               <?php 
             } ?>
            </div>
          </div>
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
              if($tipoSolicitud==1||$tipoSolicitud==3||$tipoSolicitud==4||$tipoSolicitud==2){
              ?><button title="Agregar (alt + n)" id="add_boton" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addSolicitudDetalle(this,<?=$tipoSolicitud?>)"><i class="material-icons">add</i>
                  </button><?php
              }else{
              ?><button title="Agregar (alt + n)" id="add_boton" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addSolicitudDetalle(this,<?=$tipoSolicitud?>)"><i class="material-icons">add</i>
                  </button><?php
              } 
              ?>
                 
              <div class="float-right">
              </div>
              <div class="row">
                <label class="col-sm-1 col-form-label" style="text-align: center;">Of / Area</label>
                <label class="col-sm-3 col-form-label" style="text-align: center;">Cuenta Relacionada</label>                
                <label class="col-sm-3 col-form-label" style="text-align: left;">Detalle / Glosa</label>
                <label class="col-sm-1 col-form-label" style="text-align: left; left:-25px !important;">Presupuestado</label>
                <label class="col-sm-1 col-form-label" style="text-align: left;">Importe</label>
                <label class="col-sm-2 col-form-label" style="text-align: left;">Proveedor</label>
                <label class="col-sm-1 col-form-label" style="text-align: center;">Opciones</label>
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
                 include "solicitudDetalleSimulacionRegistrado4.php";
                }else{
                 include "solicitudDetalleSimulacionRegistrado4.php";
                }    
              }
            }
            ?>
            
          </fieldset>
            <div class="card-footer fixed-bottom">
              <a id="buttonSubmitFalse" title="El Monto Solicitado es Mayor al Presupuestado" class="btn btn-warning text-dark d-none">Guardar <span class="material-icons text-dark">warning</span></a>
              <button id="buttonSubmit" type="submit" class="btn btn-success">Guardar</button>
              <?php 
               if(isset($_GET['q'])){
                if(isset($_GET['admin'])){
                  if($_GET['admin']==2){
                    $urlList2=$urlList4;
                  }
                 ?>  
                  <a href="../<?=$urlList2;?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>" class="<?=$buttonCancel;?>">Volver</a> 
                <?php
                }else{
                  if(isset($_GET['reg'])){
                    if($_GET['reg']==2){
                      ?>  
                    <a href="../<?=$urlList5;?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="<?=$buttonCancel;?>">Volver</a> 
                   <?php
                    }else{
                      ?>  
                    <a href="../<?=$urlList3;?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="<?=$buttonCancel;?>">Volver</a> 
                   <?php
                    }
                    
                  }else{
                    ?>  
                    <a href="../<?=$urlList;?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>&v=<?=$v?>" class="<?=$buttonCancel;?>">Volver</a> 
                   <?php
                  }
                   
                }
                
               }else{
                if(isset($_GET['admin'])){
                  if($_GET['admin']==2){
                    $urlList2=$urlList4;
                  }
                 ?>
                    <a href="../<?=$urlList2;?>" class="<?=$buttonCancel;?>">Volver</a> 
                <?php
                }else{
                  if(isset($_GET['reg'])){
                    if($_GET['reg']==2){
                      ?>
                     <a href="../<?=$urlList5;?>" class="<?=$buttonCancel;?>">Volver</a> 
                  <?php
                    }else{
                      ?>
                     <a href="../<?=$urlList3;?>" class="<?=$buttonCancel;?>">Volver</a> 
                  <?php
                    }
                   
                  }else{
                   ?>
                     <a href="../<?=$urlList;?>" class="<?=$buttonCancel;?>">Volver</a> 
                  <?php
                  }
                }
                
               }
              ?>
               
               <a href="#" onclick="cargarDatosRegistroProveedor()" title="Agregar Proveedor" class="btn btn-warning float-right"><i class="material-icons">group_add</i></a>
               <a href="#" onclick="actualizarRegistroProveedor()" title="Actualizar Lista Proveedores" class="btn btn-info float-right"><i class="material-icons">find_replace</i></a>
               <!--DISTRIBUCION-->
               <input type="hidden" id="cantidad_filas_proyecto" value="0"> 
                 <input type="hidden" name="n_distribucion" id="n_distribucion" value="<?=$valorDistribucion?>">
                 <input type="hidden" name="nueva_distribucion" id="nueva_distribucion" value="<?=$valorDistribucion?>">
                  <div class="btn-group dropdown">
                      <button type="button" class="btn btn-success dropdown-toggle material-icons text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Distribucion de Gastos">
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
                        <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion" onclick="cargarDistribucionSol(4)" class="dropdown-item">
                          <i class="material-icons">bubble_chart</i> x Área y Oficina
                        </a>
                        <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion" onclick="cargarDistribucionSol(0)" class="dropdown-item">
                          <i class="material-icons">bubble_chart</i> Nínguna
                        </a>
                        </div>
                    </div>
                    <div id="array_distribucion"></div>
                <!--FIN DISTRIBUCION-->  
               <a href="#" onclick="mostrarActividadesDeSolicitud()" title="Actividades Proyecto SIS" class="btn btn-primary float-right"><i class="material-icons">assignment</i><span id="nproyectos" class="count bg-danger">0</span> ACTIVIDADES</a>
               <?php 
               $estiloBoton="";
              $datosEncargadoSolicitud=verificarPersonalEncargadoSolicitud($codigoSolicitud);
              if($datosEncargadoSolicitud>0){
                $estiloBoton="estado";
              }
               ?>
               <input type="hidden" id="cantidad_filas_proyecto" value="0">
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
                      $Obli='<i class="material-icons text-success">done</i> SI<input type="hidden" id="obligatorio_file'.$filaA.'" value="1">';
                     }
                     //2708 cabecera //27080 detalle
                     $verificarArchivo=verificarArchivoAdjuntoExistente(2708,$codigoSolicitud,0,$codigoX);
                     //$nombreX=$verificarArchivo[1];
                     $urlArchivo=$verificarArchivo[2];
                     $codigoArchivoX=$verificarArchivo[3];
                  ?>
                  <tr>
                    <td class="text-left"><input type="hidden" name="codigo_archivo<?=$filaA?>" id="codigo_archivo<?=$filaA?>" value="<?=$codigoX;?>"><input type="hidden" name="nombre_archivo<?=$filaA?>" id="nombre_archivo<?=$filaA?>" value="<?=$nombreX;?>"><?=$nombreX;?></td>
                    <td class="text-center"><?=$Obli?></td>
                    <td class="text-right">
                      <?php
                      if($verificarArchivo[0]==0){
                       ?>
                      <small id="label_txt_documentos_cabecera<?=$filaA?>"></small> 
                      <span class="input-archivo">
                        <input type="file" class="archivo" name="documentos_cabecera<?=$filaA?>" id="documentos_cabecera<?=$filaA?>"/>
                      </span>
                      <label title="Ningún archivo" for="documentos_cabecera<?=$filaA?>" id="label_documentos_cabecera<?=$filaA?>" class="label-archivo btn btn-warning btn-sm"><i class="material-icons">publish</i> Subir Archivo
                      </label>
                       <?php
                      }else{
                        ?>
                        <small id="existe_archivo_cabecera<?=$filaA?>"></small>

                        <small id="label_txt_documentos_cabecera<?=$filaA?>"></small> 
                        <span class="input-archivo">
                          <input type="file" class="archivo" name="documentos_cabecera<?=$filaA?>" id="documentos_cabecera<?=$filaA?>"/>
                        </span>
                        <label title="Ningún archivo - Click para Cambiar el Archivo" for="documentos_cabecera<?=$filaA?>" id="label_documentos_cabecera<?=$filaA?>" class="label-archivo btn btn-success btn-sm btn-fab"><i class="material-icons">publish</i>
                        </label>
                        <div class="btn-group" id="existe_div_archivo_cabecera<?=$filaA?>">
                        <a href="#" class="btn btn-button btn-sm">Registrado</a>
                        <a class="btn btn-button btn-info btn-sm" href="<?=$urlArchivo?>" title="Descargar: Doc - IFINANCIERO (<?=$nombreX?>)" download="Doc - IFINANCIERO (<?=$nombreX?>)"><i class="material-icons">get_app</i></a>  
                        <a href="#" title="Quitar" class="btn btn-danger btn-sm" onClick="quitarArchivoSistemaAdjunto(<?=$filaA?>,<?=$codigoArchivoX;?>,0)"><i class="material-icons">delete_outline</i></a>
                        </div> 
                        <?php
                      }
                    ?>  
                    </td>    
                    <td><?=$nombreX;?></td>
                  </tr> 
                  <?php
                   }
                  $stmtArchivo = $dbh->prepare("SELECT * from archivos_adjuntos where cod_tipoarchivo=-100 and cod_tipopadre=2708 and cod_objeto=$codigoSolicitud and cod_padre=0"); //2708 //2708 localhost
                  $stmtArchivo->execute();
                  $filaE=0;
                  while ($rowArchivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC)) {
                     $filaE++;
                     $filaA++;
                     $codigoArchivoX=$rowArchivo['codigo'];
                     $codigoX=$rowArchivo['cod_tipoarchivo'];
                     $nombreX=$rowArchivo['descripcion'];
                     $urlArchivo=$rowArchivo['direccion_archivo'];
                     $ObligatorioX=0;
                     $Obli='<i class="material-icons text-danger">clear</i> NO';
                     if($ObligatorioX==1){
                      $Obli='<i class="material-icons text-success">done</i> SI';
                     }

                  ?>
                  <tr id="fila_archivo<?=$filaA?>">
                    <td class="text-left"><input type="hidden" name="codigo_archivoregistrado<?=$filaE?>" id="codigo_archivoregistrado<?=$filaE?>" value="<?=$codigoArchivoX;?>">Otros Documentos</td>
                    <td class="text-center"><?=$Obli?></td>
                    <td class="text-right">
                      <small id="existe_archivo_cabecera<?=$filaA?>"></small>

                        <small id="label_txt_documentos_cabecera<?=$filaA?>"></small> 
                        <span class="input-archivo">
                          <input type="file" class="archivo" name="documentos_cabecera<?=$filaA?>" id="documentos_cabecera<?=$filaA?>"/>
                        </span>
                        <label title="Ningún archivo" for="documentos_cabecera<?=$filaA?>" id="label_documentos_cabecera<?=$filaA?>" class="label-archivo btn btn-success btn-sm"><i class="material-icons">publish</i> Cambiar Archivo
                        </label>
                      <div class="btn-group">
                        <a href="#" class="btn btn-button btn-sm" >Registrado</a>  
                        <a class="btn btn-button btn-info btn-sm" href="<?=$urlArchivo?>" title="Descargar: Doc - IFINANCIERO (<?=$nombreX?>)" download="Doc - IFINANCIERO (<?=$nombreX?>)"><i class="material-icons">get_app</i></a>  
                        <a href="#" title="Quitar" class="btn btn-danger btn-sm" onClick="quitarArchivoSistemaAdjunto(<?=$filaA?>,<?=$codigoArchivoX;?>,1)"><i class="material-icons">delete_outline</i></a>
                      </div>     
                    </td>    
                    <td><?=$nombreX;?></td>
                  </tr> 
                  <?php
                   }
                      ?>     
                </tbody>
              </table>
              <input type="hidden" value="<?=$filaA?>" id="cantidad_archivosadjuntos" name="cantidad_archivosadjuntos">
              <input type="hidden" value="<?=$filaA?>" id="cantidad_archivosadjuntosexistentes" name="cantidad_archivosadjuntosexistentes">
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

</form>
<?php
require_once 'modal.php';
?>
<script>calcularTotalesSolicitud();listarProyectosSisdeUnidades();ponerConfirmacionDeArchivosSolRec();</script> 

