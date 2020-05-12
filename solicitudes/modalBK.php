BACK UP
<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
//$arrayFilesCabecera=json_decode($_POST['archivos_cabecera']);
$arrayFilesDetalle=json_decode($_POST['archivos_detalle']);
$codComprobanteDetalle=obtenerCodigoSolicitudDetalle();
$cantidadFilas=$_POST["cantidad_filas"];
$facturas= json_decode($_POST['facturas']);
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];


if(isset($_POST['numero'])){
    $observaciones=$_POST['observaciones_solicitud'];
    $numero=$_POST['numero'];
    $tipoSol=$_POST['tipo_solicitud'];
  if($tipoSol!=2){
    $codProv=0;
    if($tipoSol==3){
      //datos para solicitud recursos manual manual
      $codSim=0;
      $codSimServ=0;
      $globalArea=$_POST['area_solicitud'];
     $globalUnidad=$_POST['unidad_solicitud'];  
    }else{
      //datos para solicitud recursos SIMULACION (PROPUESTA)
     $simu=explode("$$$",$_POST['simulaciones']);
     if($simu[1]=="TCP"){
      //tcp o tcs
      $codSim=0;
      $codSimServ=$simu[0];
      $areaUnidad=obtenerUnidadAreaPorSimulacionServicio($codSimServ);
     }else{
      // sec
      $codSim=$simu[0];
      $codSimServ=0;
      $areaUnidad=obtenerUnidadAreaPorSimulacionCosto($codSim);
     }
     $globalArea=$areaUnidad[0];
     $globalUnidad=$areaUnidad[1];     
    }
  }else{
    //datos para solicitud recursos proveeedor
    $codProv=$_POST['proveedores'];
    $codSim=0;
    $codSimServ=0;
  }

  $codCont=0;//CODIGO DE CONTRATO
  $fecha= date("Y-m-d h:m:s");
  $codSolicitud=obtenerCodigoSolicitudRecursos();
  $dbh = new Conexion();
  if(isset($_POST['usuario_ibnored_v'])){
       $v=$_POST['usuario_ibnored_v'];
       $sqlInsert="INSERT INTO solicitud_recursos (codigo, cod_personal,cod_unidadorganizacional,cod_area,fecha,numero,cod_simulacion,cod_proveedor,cod_simulacionservicio,cod_contrato,idServicio,observaciones) 
       VALUES ('".$codSolicitud."','".$globalUser."','".$globalUnidad."', '".$globalArea."', '".$fecha."','".$numero."','".$codSim."','".$codProv."','".$codSimServ."','".$codCont."','".$v."','".$observaciones."')";
  }else{
    $v=obtenerIdServicioPorIdSimulacion($codSimServ);
    $sqlInsert="INSERT INTO solicitud_recursos (codigo, cod_personal,cod_unidadorganizacional,cod_area,fecha,numero,cod_simulacion,cod_proveedor,cod_simulacionservicio,cod_contrato,idServicio,observaciones) 
       VALUES ('".$codSolicitud."','".$globalUser."','".$globalUnidad."', '".$globalArea."', '".$fecha."','".$numero."','".$codSim."','".$codProv."','".$codSimServ."','".$codCont."','".$v."','".$observaciones."')";
  }
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();

  //enviar propuestas para la actualizacion de ibnorca
  $fechaHoraActual=date("Y-m-d H:i:s");
  $idTipoObjeto=2708;
  $idObjeto=2721; //regristado
  $obs="Registro de Solicitud";
  if(isset($_POST['usuario_ibnored_u'])){
       $u=$_POST['usuario_ibnored_u'];
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$u,$codSolicitud,$fechaHoraActual,$obs);
  }else{
       actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$globalUser,$codSolicitud,$fechaHoraActual,$obs);
  }



  //insertamos la distribucion
  $sqlDel="DELETE FROM distribucion_gastos_solicitud_recursos where cod_solicitudrecurso=$codSolicitud";
  $stmtDel = $dbh->prepare($sqlDel);
  $stmtDel->execute();

  //borramos los archivos
  $sqlDel="DELETE FROM archivos_adjuntos where cod_objeto=$codSolicitud and cod_tipopadre=2708";
  $stmtDel = $dbh->prepare($sqlDel);
  $stmtDel->execute();
  $sqlDel="DELETE FROM archivos_adjuntos where cod_padre=$codSolicitud and cod_tipopadre=27080";
  $stmtDel = $dbh->prepare($sqlDel);
  $stmtDel->execute();
  
  $valorDist=$_POST['n_distribucion'];
  if($valorDist!=0){
      $array1=json_decode($_POST['d_oficinas']);
      $array2=json_decode($_POST['d_areas']);
      if($valorDist==1){
        guardarDatosDistribucion($array1,0,$codSolicitud); //dist x Oficina
      }else{
        if($valorDist==2){
          guardarDatosDistribucion(0,$array2,$codSolicitud); //dist x Area
        }else{
          guardarDatosDistribucion($array1,$array2,$codSolicitud); //dist x Oficina y Area
        }
      }   
  }
}




$flagSuccess=true;
//subir archivos al servidor
//Como el elemento es un arreglos utilizamos foreach para extraer todos los valores

$nArchivosCabecera=$_POST["cantidad_archivosadjuntos"];
for ($ar=1; $ar <= $nArchivosCabecera ; $ar++) { 
  if(isset($_POST['codigo_archivo'.$ar])){
    if($_FILES['documentos_cabecera'.$ar]["name"]){
      $filename = $_FILES['documentos_cabecera'.$ar]["name"]; //Obtenemos el nombre original del archivos
      $source = $_FILES['documentos_cabecera'.$ar]["tmp_name"]; //Obtenemos un nombre temporal del archivos    
      $directorio = '../assets/archivos-respaldo/archivos_solicitudes/SOL-'.$codSolicitud; //Declaramos una  variable con la ruta donde guardaremos los archivoss
      //Validamos si la ruta de destino existe, en caso de no existir la creamos
      if(!file_exists($directorio)){
                mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
      }
      $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
      //Movemos y validamos que el archivos se haya cargado correctamente
      //El primer campo es el origen y el segundo el destino
      if(move_uploaded_file($source, $target_path)) { 
        echo "ok";
        $tipo=$_POST['codigo_archivo'.$ar];
        $descripcion=$_POST['nombre_archivo'.$ar];
        $tipoPadre=2708;
        $sqlInsert="INSERT INTO archivos_adjuntos (cod_tipoarchivo,descripcion,direccion_archivo,cod_tipopadre,cod_padre,cod_objeto) 
        VALUES ('$tipo','$descripcion','$target_path','$tipoPadre',0,'$codSolicitud')";
        $stmtInsert = $dbh->prepare($sqlInsert);
        $stmtInsert->execute();    
        print_r($sqlInsert);
      } else {    
          echo "error";
      } 
    }
  }
}
  /*foreach($_FILES["archivos"]['tmp_name'] as $key => $tmp_name)
    {
        //Validamos que el archivos exista
        if($_FILES["archivos"]["name"][$key]) {
            $filename = $_FILES["archivos"]["name"][$key]; //Obtenemos el nombre original del archivos
            $source = $_FILES["archivos"]["tmp_name"][$key]; //Obtenemos un nombre temporal del archivos
            
            $directorio = '../assets/archivos-respaldo/archivos_solicitudes/SOL-'.$codSolicitud; //Declaramos una  variable con la ruta donde guardaremos los archivoss
            //Validamos si la ruta de destino existe, en caso de no existir la creamos
            if(!file_exists($directorio)){
                mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
            }
            
            
            $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
            
            //Movemos y validamos que el archivos se haya cargado correctamente
            //El primer campo es el origen y el segundo el destino
            if(move_uploaded_file($source, $target_path)) { 
                echo "ok";
                for ($a=0; $a < count($arrayFilesCabecera); $a++) { 
                  if($arrayFilesCabecera[$a]->nombre==$filename){
                    //insertamos a la tabla de archivos
                    $tipo=$arrayFilesCabecera[$a]->tipo;
                    $descripcion=$arrayFilesCabecera[$a]->nombre_tipo;
                    $tipoPadre=2708;
                    $sqlInsert="INSERT INTO archivos_adjuntos (cod_tipoarchivo,descripcion,direccion_archivo,cod_tipopadre,cod_padre,cod_objeto) 
                    VALUES ('$tipo','$descripcion','$target_path','$tipoPadre',0,'$codSolicitud')";
                    $stmtInsert = $dbh->prepare($sqlInsert);
                    $stmtInsert->execute();    
                    print_r($sqlInsert);
                  }
                }
            } else {    
                echo "error";
            }       
        }
    }*/

//guardar las ediciones
    $fila=0;
for ($i=1;$i<=$cantidadFilas;$i++){ 
    if(isset($_POST["habilitar".$i])){      
    $data[$fila][0]=$_POST["partida_cuenta_id".$i];
    $data[$fila][1]=$_POST["unidad_fila".$i]; 
    $data[$fila][2]=$_POST["area_fila".$i];  
    $data[$fila][3]=$_POST["detalle_detalle".$i]; 
    $data[$fila][4]=$_POST["importe_presupuesto".$i]; 
    $data[$fila][5]=$_POST["importe".$i];           
    $data[$fila][6]=0; 
    $data[$fila][7]="";
    $data[$fila][8]=$_POST["proveedor".$i];
    $data[$fila][9]=$_POST["cod_detalleplantilla".$i];
    $data[$fila][10]=$_POST["cod_servicioauditor".$i];
    $data[$fila][11]=$_POST["cod_retencion".$i];
    $data[$fila][12]=$_POST["cod_tipopago".$i];
    $data[$fila][13]=$_POST["nombre_beneficiario".$i];
    $data[$fila][14]=$_POST["apellido_beneficiario".$i];
    $data[$fila][15]=$_POST["cuenta_beneficiario".$i];
    //$dataInsert  
    $fila++;
      foreach($_FILES["archivos".$i]['tmp_name'] as $key => $tmp_name)
      {
        //Validamos que el archivos exista
        if($_FILES["archivos".$i]["name"][$key]) {
            $filename = $_FILES["archivos".$i]["name"][$key]; //Obtenemos el nombre original del archivos
            $source = $_FILES["archivos".$i]["tmp_name"][$key]; //Obtenemos un nombre temporal del archivos
            
            $directorio = '../assets/archivos-respaldo/archivos_solicitudes/SOL-'.$codSolicitud.'/DET-'.$fila; //Declaramos una  variable con la ruta donde guardaremos los archivoss
            //Validamos si la ruta de destino existe, en caso de no existir la creamos
            if(!file_exists($directorio)){
                mkdir($directorio, 0777,true) or die("No se puede crear el directorio de extracci&oacute;n");    
            }
            
            
            $target_path = $directorio.'/'.$filename; //Indicamos la ruta de destino, así como el nombre del archivos
            
            //Movemos y validamos que el archivos se haya cargado correctamente
            //El primer campo es el origen y el segundo el destino
            if(move_uploaded_file($source, $target_path)) { 
                echo "ok";
                for ($a=0; $a < count($arrayFilesDetalle[$i-1]); $a++) {         
                  if($arrayFilesDetalle[$i-1][$a]->nombre==$filename){
                    
                    //insertamos a la tabla de archivos
                    $tipo=$arrayFilesDetalle[$i-1][$a]->tipo;
                    $descripcion=$arrayFilesDetalle[$i-1][$a]->nombre_tipo;
                    $tipoPadre=27080; //clasificador para detalle de solicitudes
                    $sqlInsert="INSERT INTO archivos_adjuntos (cod_tipoarchivo,descripcion,direccion_archivo,cod_tipopadre,cod_padre,cod_objeto) 
                    VALUES ('$tipo','$descripcion','$target_path','$tipoPadre','$codSolicitud','$codComprobanteDetalle')";
                    $stmtInsert = $dbh->prepare($sqlInsert);
                    $stmtInsert->execute();    
                    print_r($sqlInsert);
                    
                  }
                }
                
            } else {    
                echo "error";
            }
            
        }  
      }
      $codComprobanteDetalle++;   
    }
} 
$cab[0]="cod_plancuenta";
$cab[1]="cod_unidadorganizacional";
$cab[2]="cod_area";
$cab[3]="detalle";
$cab[4]="importe_presupuesto";
$cab[5]="importe";
$cab[6]="numero_factura";
$cab[7]="archivo";
$cab[8]="cod_proveedor";
$cab[9]="cod_detalleplantilla";
$cab[10]="cod_servicioauditor";
$cab[11]="cod_confretencion";
$cab[12]="cod_tipopagoproveedor";
$cab[13]="nombre_beneficiario";
$cab[14]="apellido_beneficiario";
$cab[15]="nro_cuenta_beneficiario";
$solDet=contarSolicitudDetalle($codSolicitud);
$solDet->bindColumn('total', $contador);
while ($row = $solDet->fetch(PDO::FETCH_BOUND)) {
 $cont1=$contador;
}

$stmt1 = obtenerSolicitudesDet($codSolicitud);
editarComprobanteDetalle($codSolicitud,'cod_solicitudrecurso',$cont1,$fila,$stmt1,'solicitud_recursosdetalle',$cab,$data,$facturas);


$stmt1 = obtenerSolicitudesDet($codSolicitud);
//PARA registro de facturas
editarComprobanteDetalle($codSolicitud,'cod_solicitudrecurso',$cont1,$fila,$stmt1,'solicitud_recursosdetalle',$cab,$data,$facturas);

if(isset($_POST['usuario_ibnored'])){
    $q=$_POST['usuario_ibnored'];
    $s=$_POST['usuario_ibnored_s'];
    $u=$_POST['usuario_ibnored_u'];
    $v=$_POST['usuario_ibnored_v'];
  if($flagSuccess==true){
    showAlertSuccessError(true,"../".$urlList."&q=".$q."&s=".$s."&u=".$u."&v=".$v); 
  }else{
    showAlertSuccessError(false,"../".$urlList."&q=".$q."&s=".$s."&u=".$u."&v=".$v);
  }
}else{
  if($flagSuccess==true){
    showAlertSuccessError(true,"../".$urlList); 
  }else{
    showAlertSuccessError(false,"../".$urlList);
  }
}


function guardarDatosDistribucion($array1,$array2,$codigoSol){
  $dbh = new Conexion();
 if($array1!=0){
  for ($i=0; $i < count($array1); $i++) { 
    $unidad=$array1[$i]->unidad;
    $porcentaje=$array1[$i]->porcentaje;
    $sqlInsert="INSERT INTO distribucion_gastos_solicitud_recursos (tipo_distribucion,oficina_area,porcentaje,cod_solicitudrecurso) 
    VALUES ('1','$unidad','$porcentaje','$codigoSol')";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $stmtInsert->execute();
  }   
}
if($array2!=0){
  for ($i=0; $i < count($array2); $i++) { 
    $area=$array2[$i]->area;
    $porcentaje=$array2[$i]->porcentaje;
    $sqlInsert="INSERT INTO distribucion_gastos_solicitud_recursos (tipo_distribucion,oficina_area,porcentaje,cod_solicitudrecurso) 
    VALUES ('2','$area','$porcentaje','$codigoSol')";
    $stmtInsert = $dbh->prepare($sqlInsert);
    $stmtInsert->execute();
  }
 } 
}
  
?>




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


<!-- notice modal -->
<div class="modal fade" id="modalTipoPagoSolicitud" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-primary card-header-text">
          <div class="card-text">
            <h5>Forma de Pago</h5> 
          </div>
          <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
            <i class="material-icons">close</i>
          </button>
      </div>
      <input type="hidden" name="fila_pago" id="fila_pago"/>
          <div class="card-body">
            <div class="col-sm-12">
              <div class="row">
                <label class="col-sm-2 col-form-label" style="color: #4a148c;">Tipo Pago</label>
                <div class="col-sm-4">
                  <div class="form-group">
                     <select class="selectpicker form-control form-control-sm" name="tipo_pagoproveedor" id="tipo_pagoproveedor" data-style="btn btn-primary">                                  
                        <?php 
                         $stmt3 = $dbh->prepare("SELECT * from tipos_pagoproveedor where cod_estadoreferencial=1");
                         $stmt3->execute();
                          while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                           $codigoSel=$rowSel['codigo'];
                          $nombreSelX=$rowSel['nombre'];
                          $abrevSelX=$rowSel['abreviaruta'];
                          ?><option value="<?=$codigoSel;?>"><?=$nombreSelX?></option><?php 
                          }
                        ?>
                    </select>
                  </div>
                </div>
                <label class="col-sm-2 col-form-label" style="color: #4a148c;">Cuenta Beneficiario</label>
                <div class="col-sm-4">
                   <div class="form-group" id="">
                        <input class="form-control" type="text" readonly name="cuenta_beneficiario" id="cuenta_beneficiario" required="true"/>
                    </div>
                </div>                          
              </div>
               <div class="row">                      
                    <label class="col-sm-2 col-form-label" style="color: #4a148c;">Nombre Benef.</label>
                    <div class="col-sm-4">
                      <div class="form-group">  
                           <input class="form-control" type="text" readonly name="nombre_beneficiario" id="nombre_beneficiario" required="true">                                                                                                                       
                       </div>
                    </div>
                    <label class="col-sm-2 col-form-label" style="color: #4a148c;">Apellido Benef.</label>
                    <div class="col-sm-4">
                      <div class="form-group">  
                           <input class="form-control" type="text" readonly name="apellido_beneficiario" id="apellido_beneficiario" required="true">                                                                                                                       
                       </div>
                    </div>
                </div>
                <div class="mensaje"></div>
             </div>                     
             <div class="form-group float-right">
                <button type="button" class="btn btn-warning btn-round" onclick="guardarFormaPagoSolicitud()">Guardar</button>
             </div>         
          </div>
    </div>
  </div>
</div>
<!-- end notice modal -->

<!-- notice modal -->
<div class="modal fade" id="modalDistribucionSol" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
      <div class="card-header card-header-success card-header-text">
          <div class="card-text">
            <h5>Distribución de Gastos <b id="titulo_distribucion"></b> </h5> 
          </div>
          <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
            <i class="material-icons">close</i>
          </button>
      </div>
          <div class="card-body">
            <div class="row col-sm-12">
              <div class="col-sm-6">                                                              
                  <table class="table table-condensed table-bordered">
                    <thead>
                      <tr class="bg-principal text-white">
                        <th>#</th>
                        <th>Oficina</th>
                        <th width="10%">%</th>
                      </tr>
                    </thead>
                    <tbody id="cuerpo_tabladistofi">
                      
                    </tbody>
                  </table>
              </div>
              <div class="col-sm-6">                                                              
                  <table class="table table-condensed table-bordered">
                    <thead>
                      <tr class="bg-principal text-white">
                        <th>#</th>
                        <th>Area</th>
                        <th width="10%">%</th>
                      </tr>
                    </thead>
                    <tbody id="cuerpo_tabladistarea">
                      
                    </tbody>
                  </table>
              </div> 
             </div>                     
             <div class="form-group float-right">
                <button type="button" class="btn btn-success btn-round" onclick="guardarDistribucionSolicitudRecurso()">Guardar</button>
             </div>         
          </div>
    </div>
  </div>
</div>
<!-- end notice modal -->


<!-- notice modal -->
<div class="modal fade" id="modalEditFac" tabindex="-1" role="dialog" style="z-index:99999"aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice modal-xl">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
              <div class="card ">
                <div class="card-header" id="divTituloCuentaDetalle">
                  <h4 class="card-title">Facturas -
                    <small class="description">Edicion</small>
                  </h4>
                </div>
                <div class="card-body ">
                        <input class="form-control" type="hidden" name="fila_fac" id="fila_fac"/>
                        <input class="form-control" type="hidden" name="indice_fac" id="indice_fac"/>
                        <div style="padding: 20px;">
                          <div class="row">                      
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">NIT</label>
                            <div class="col-sm-3">
                              <div class="form-group">  
                                <div id="">
                                  <input class="form-control" type="number" name="nit_fac_edit" id="nit_fac_edit" required="true">                        
                                </div>                                                                                                
                              </div>

                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Nro. Factura</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="">
                                  <!-- <label for="number" class="bmd-label-floating" style="color: #4a148c;">Nro. Factura</label>      -->
                                  <input class="form-control" type="number" name="nro_fac_edit" id="nro_fac_edit" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Fecha</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="fecha_fac" class="bmd-label-floating" style="color: #4a148c;">Fecha</label>      -->
                                <input type="text" class="form-control datepicker" name="fecha_fac_edit" id="fecha_fac_edit" value="<?=$fechaActualModal?>">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Importe</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="">
                                <input class="form-control" type="number" name="imp_fac_edit" id="imp_fac_edit" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Exento</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="exe_fac" class="bmd-label-floating" style="color: #4a148c;">Extento</label>      -->
                                <input class="form-control" type="text" name="exe_fac_edit" id="exe_fac_edit" required="true" value="0" />
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">ICE</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="ice_fac" class="bmd-label-floating" style="color: #4a148c;">ICE</label>      -->
                                <input class="form-control" type="text" name="ice_fac_edit" id="ice_fac_edit" required="true" value="0" />
                              </div>
                             </div>
                          </div>                                                                  
                          <!--No tiene funcion este campo-->
                          <div class="row">                                            
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tasa Cero</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="taza_fac" class="bmd-label-floating" style="color: #4a148c;">Taza Cero</label>      -->
                                <input class="form-control" type="text" name="taza_fac_edit" id="taza_fac_edit" required="true" value="0" />
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Autorizaci&oacute;n</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="">
                                <!-- <label for="aut_fac" class="bmd-label-floating" style="color: #4a148c;">Nro. Autorizaci&oacute;n</label>      -->
                                <input class="form-control" type="text" name="aut_fac_edit" id="aut_fac_edit" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Cod. Control</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="con_fac" class="bmd-label-floating" style="color: #4a148c;">Cod. Control</label>      -->
                                <input class="form-control" type="text" name="con_fac_edit" id="con_fac_edit" required="true"/>
                              </div>
                             </div>
                          </div> 
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tipo</label>
                            <div class="col-sm-2">
                              <div class="form-group">
                                <select class="selectpicker form-control form-control-sm" name="tipo_fac_edit" id="tipo_fac_edit" data-style="btn btn-primary">                                  
                                   <?php
                                         $stmt = $dbh->prepare("SELECT codigo, nombre FROM tipos_compra_facturas where cod_estadoreferencial=1");
                                       $stmt->execute();
                                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $codigoX=$row['codigo'];
                                        $nombreX=$row['nombre'];
                                        ?><option value="<?=$codigoX;?>"><?=$nombreX;?></option><?php
                                         }
                                     ?>
                                </select>
                              </div>
                            </div>                        
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Razón Social</label>
                            <div class="col-sm-8">
                              <div class="form-group" id="">                                
                                <input type="text" class="form-control" name="razon_fac_edit" id="razon_fac_edit">
                                
                              </div>
                            </div>   
                        </div>
                        
                          
                        </div>                     
                        <div class="form-group float-right">
                          <button type="button" class="btn btn-info btn-round" onclick="saveFacturaEdit()">Guardar</button>
                        </div>
                      
                </div>
              </div>
        
        <!--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eaque ullam autem illum, minima doloribus doloremque adipisci dolorem, repellendus debitis animi laboriosam commodi dolores et sint, quod. Pariatur, repudiandae sequi assumenda.</p>-->
      </div>
      <div class="modal-footer justify-content-center">
        
      </div>
    </div>
  </div>
</div>
<!-- end notice modal -->
<!-- small modal -->
<div class="modal fade modal-primary" id="modalFileDet" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
        <input type="hidden" id="codigo_fila" value=""/>
           <div class="fileinput fileinput-new col-md-12" data-provides="fileinput">
           <div class="row">
              <div class="col-md-12">
                <div class="border" id="lista_archivosdetalle">Ningun archivo seleccionado</div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <span class="btn btn-info btn-file btn-sm">
                      <span class="fileinput-new">Buscar</span>
                      <span class="fileinput-exists">Cambiar</span>
                      <input type="file" name="archivosDetalle[]" id="archivosDetalle" multiple="multiple"/>
                   </span>
                <a href="#" id="boton_quitararchivos" class="btn btn-danger btn-sm fileinput-exists" onclick="archivosPreviewDetalle(1)" data-dismiss="fileinput"><i class="material-icons">clear</i> Quitar</a>
              </div>
            </div>
           </div>
           <p class="text-danger">Lista de archivos</p>
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

<!-- small modal -->
<div class="modal fade modal-mini modal-primary" id="modalCopy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div class="modal-content bg-info text-white">
      <div class="modal-header">
        <i class="material-icons" data-notify="icon"><?=$iconCopy?></i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <p>¿Desea copiar la glosa a todos los detalles?.</p> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link" data-dismiss="modal"> <-- Volver </button>
        <button type="button" onclick="copiarGlosa()" class="btn btn-white btn-link" data-dismiss="modal">Aceptar
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->
<!-- small modal -->
<div class="modal fade modal-primary" id="modalAbrirPlantilla" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content bg-secondary text-white">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
              <div class="card ">
                <div class="card-header">
                  <h4 class="card-title">Plantilla -
                    <small class="description">Abrir :</small>
                  </h4>
                </div>
                <div class="card-body ">
                 <div id="listaPlan"></div>
                 <div id="mensaje"></div>
                </div>
              </div>
      </div>  
    </div>
  </div>
</div>
<!--    end small modal -->


<!-- small modal -->
<div class="modal fade modal-mini modal-primary" id="modalAlert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-small">
    <div id="modalAlertStyle" class="modal-content bg-danger text-white">
      <div class="modal-header">
        <i class="material-icons" data-notify="icon">notifications_active</i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <div id="msgError"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-white btn-link" data-dismiss="modal">Aceptar
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->

<!-- notice modal -->
<div class="modal fade modal-arriba" id="modalFac" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-notice modal-xl">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
              <div class="card ">
                <div class="card-header" id="divTituloCuentaDetalle">
                  <h4 class="card-title">Facturas -
                    <small class="description">Cuenta :</small>
                  </h4>
                </div>
                <div class="card-body ">
                  <ul class="nav nav-pills nav-pills-warning" role="tablist">
                    <li class="nav-item">
                          <a id="nav_boton1"class="nav-link active" data-toggle="tab" href="#link110" role="tablist">
                            <span class="material-icons">view_list</span> Lista
                          </a>
                        </li>
                        <li class="nav-item">
                          <a id="nav_boton2"class="nav-link" data-toggle="tab" href="#link111" role="tablist">
                            <span class="material-icons">add</span> Nuevo
                          </a>
                        </li>
                        <li class="nav-item">
                          <a id="nav_boton3" class="nav-link" data-toggle="tab" href="#link112" role="tablist">
                            <span class="material-icons">filter_center_focus</span> QR quincho
                          </a>
                        </li>
                  </ul>
                  <div class="tab-content tab-space">
                    <div class="tab-pane active" id="link110" style="background: #e0e0e0">
                      <div id="divResultadoListaFac">
            
                       </div>
                    </div>
                    <div class="tab-pane" id="link111" style="background: #e0e0e0">
                      <form name="form2">
                        <input class="form-control" type="hidden" name="codCuenta" id="codCuenta"/>
                        <div style="padding: 20px;">
                          <div class="row">                      
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">NIT</label>
                            <div class="col-sm-3">
                              <div class="form-group">  
                                <div id="divNitFacturaDetalle">
                                  <input class="form-control" type="number" name="nit_fac" id="nit_fac" required="true">                        
                                </div>                                
                                <div id="divNit2FacturaDetalle">
                                  
                                </div>                                
                                  
                              </div>

                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Nro. Factura</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="divNroFacFacturaDetalle">
                                  <!-- <label for="number" class="bmd-label-floating" style="color: #4a148c;">Nro. Factura</label>      -->
                                  <input class="form-control" type="number" name="nro_fac" id="nro_fac" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Fecha</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="fecha_fac" class="bmd-label-floating" style="color: #4a148c;">Fecha</label>      -->
                                <input type="text" class="form-control datepicker" name="fecha_fac" id="fecha_fac" value="<?=$fechaActualModal?>">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Importe</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="divImporteFacturaDetalle">
                                <input class="form-control" type="number" name="imp_fac" id="imp_fac" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Exento</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="exe_fac" class="bmd-label-floating" style="color: #4a148c;">Extento</label>      -->
                                <input class="form-control" type="text" name="exe_fac" id="exe_fac" required="true" value="0" />
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">ICE</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="ice_fac" class="bmd-label-floating" style="color: #4a148c;">ICE</label>      -->
                                <input class="form-control" type="text" name="ice_fac" id="ice_fac" required="true" value="0" />
                              </div>
                             </div>
                          </div>                                                                  
                          <!--No tiene funcion este campo-->
                          <div class="row">                                            
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tasa Cero</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="taza_fac" class="bmd-label-floating" style="color: #4a148c;">Taza Cero</label>      -->
                                <input class="form-control" type="text" name="taza_fac" id="taza_fac" required="true" value="0" />
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Autorizaci&oacute;n</label>
                            <div class="col-sm-3">
                              <div class="form-group" id="divNroAutoFacturaDetalle">
                                <!-- <label for="aut_fac" class="bmd-label-floating" style="color: #4a148c;">Nro. Autorizaci&oacute;n</label>      -->
                                <input class="form-control" type="text" name="aut_fac" id="aut_fac" required="true"/>
                              </div>
                            </div>
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Cod. Control</label>
                            <div class="col-sm-3">
                              <div class="form-group">
                                <!-- <label for="con_fac" class="bmd-label-floating" style="color: #4a148c;">Cod. Control</label>      -->
                                <input class="form-control" type="text" name="con_fac" id="con_fac" required="true"/>
                              </div>
                             </div>
                          </div> 
                          <div class="row">
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Tipo</label>
                            <div class="col-sm-2">
                              <div class="form-group">
                                <select class="selectpicker form-control form-control-sm" name="tipo_fac" id="tipo_fac" data-style="btn btn-primary">                                  
                                   <?php
                                         $stmt = $dbh->prepare("SELECT codigo, nombre FROM tipos_compra_facturas where cod_estadoreferencial=1");
                                       $stmt->execute();
                                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $codigoX=$row['codigo'];
                                        $nombreX=$row['nombre'];
                                        ?><option value="<?=$codigoX;?>"><?=$nombreX;?></option><?php
                                         }
                                     ?>
                                </select>
                              </div>
                            </div>                        
                            <label class="col-sm-1 col-form-label" style="color: #4a148c;">Razón Social</label>
                            <div class="col-sm-8">
                              <div class="form-group" id="divRazonFacturaDetalle">                                
                                <input type="text" class="form-control" name="razon_fac" id="razon_fac">
                                
                              </div>
                            </div>   
                        </div>
                        
                          
                        </div>                     
                        <div class="form-group float-right">
                          <button type="button" class="btn btn-info btn-round" onclick="saveFactura()">Guardar</button>
                        </div>
                      </form>
                    </div>
                    <div class="tab-pane" id="link112">
                     <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                          <div class="fileinput-preview fileinput-exists thumbnail"></div>
                         <div>
                         <span class="btn btn-rose btn-round btn-file">
                           <span class="fileinput-new">Subir archivo .txt</span>
                           <span class="fileinput-exists">Subir archivo .txt</span>
                           <input type="file" name="qrquincho" id="qrquincho" accept=".txt"/>
                         </span>
                
                        </div>
                       </div>
                       <p>Los archivos cargados se adjuntaran a la lista de facturas existente</p>
                    </div>
                  </div>
                </div>
              </div>
        
        <!--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eaque ullam autem illum, minima doloribus doloremque adipisci dolorem, repellendus debitis animi laboriosam commodi dolores et sint, quod. Pariatur, repudiandae sequi assumenda.</p>-->
      </div>
      <div class="modal-footer justify-content-center">
        
      </div>
    </div>
  </div>
</div>
<!-- end notice modal -->

<!-- small modal -->
<div class="modal fade modal-primary" id="modalRetencion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons text-dark">ballot</i>
                  </div>
                  <h4 class="card-title">Retenciones</h4>
                </div>
                <div class="card-body">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  <i class="material-icons">close</i>
                </button>
                <input class="form-control" type="hidden" name="retencion_codcuenta" id="retencion_codcuenta"/>
                <input class="form-control" type="hidden" name="retFila" id="retFila"/>
                <div class="row" id="retencion_cuenta">
                  </div>
                  <div class="row">
                       <label class="col-sm-2 col-form-label">Importe</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <input class="form-control" type="number" readonly step="0.001" name="retencion_montoimporte" id="retencion_montoimporte"/>
                        </div>
                        </div>
                  </div>
                  <div class="card-title"><center><h6>Retenciones</h6></center></div>
                 <table class="table table-condensed table-striped">
                   <thead>
                     <tr>
                       <th>Opcion</th>
                       <th class="text-left">Descripci&oacute;n</th>
                     </tr>
                   </thead>
                   <tbody>
                     <?php 
                        $stmtRetencion = $dbh->prepare("SELECT * from configuracion_retenciones where cod_estadoreferencial=1 order BY nombre");
                        $stmtRetencion->execute();
                        $contRetencion=0;
                        while ($row = $stmtRetencion->fetch(PDO::FETCH_ASSOC)) {
                           $nombreX=$row['nombre'];
                           $codigoX=$row['codigo'];
?>
                        <tr>
                          <td align="center" width="20%">
                          <div class="form-check">
                            <label class="form-check-label">
                              <input class="form-check-input" type="radio" id="retencion<?=$codigoX?>" name="retenciones" <?=($contRetencion==0)?"checked":"";?> value="<?=$codigoX?>@<?=$nombreX?>">
                              <span class="form-check-sign">
                                <span class="check"></span>
                              </span>
                            </label>
                          </div>
                          </td>
                          <td class="text-left"><?=$nombreX;?></td>
                        </tr>

                      <?php
                      $contRetencion++;
                        }
                     ?>
                   </tbody>  
                 </table>
                 <div id="mensaje_retencion"></div>
                 <div class="form-group float-right">
                        <button type="button" class="btn btn-info btn-round" onclick="agregarRetencionSolicitud()">Agregar</button>
                  </div>
                </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->
<!-- small modal -->
<div class="modal fade modal-arriba modal-primary" id="modalAgregarProveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
            <div class="card-header card-header-warning card-header-icon">
                <div class="card-icon">
                    <i class="material-icons text-dark">ballot</i>
                 </div>
                  <h4 class="card-title">Proveedor</h4>
            </div>
            <div class="card-body">
                 <div id="datosProveedorNuevo">
                   
                 </div> 
                <div class="form-group float-right">
                        <button type="button" onclick="guardarDatosProveedor()" class="btn btn-info btn-round">Agregar</button>
                </div>
          </div>
      </div>  
    </div>
  </div>
<!--    end small modal -->
<script>$('.selectpicker').selectpicker("refresh");</script>