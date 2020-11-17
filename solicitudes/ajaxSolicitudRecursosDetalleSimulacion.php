<?php
session_start();
require_once '../functions.php';
require_once '../conexion.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$idFila=$_GET['idFila'];
$codigo=$_GET['codigo'];

$stmt = $dbh->prepare("SELECT cod_unidadorganizacional,cod_area from solicitud_recursos where codigo='$codigo'");
$stmt->execute();
$unidadSol="";
$areaSol="";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $unidadSol=$row['cod_unidadorganizacional'];
  $areaSol=$row['cod_area'];
}

if(isset($_GET["unidad"])){
  $globalUnidad = $_GET["unidad"];
}
if(isset($_GET["area"])){
  $globalArea = $_GET["area"];
}
?>

<div class="form-group d-none" id="divNitFactura<?=$idFila;?>">  
  <input class="form-control" type="number" name="nit_fac" id="nit_fac" onkeyup="llenarFacturaAutomaticamente(this.value,'<?=$idFila;?>',0);">
</div>

<div id="comp_row" class="col-md-12">
	<div class="row">
    <div class="col-sm-1 btn-group" style="padding-left:0 !important;padding-right:0 !important;">
                                 <div class="form-group" style="width:100% !important;">
                                  <span style="position:absolute;left:-15px; font-size:20px;font-weight:600; color:#F1C40F;" id="fila_index<?=$idFila?>"><?=$idFila?></span>
                                    <select class="selectpicker form-control form-control-sm col-sm-12" onchange="listarProyectosSisdeUnidades()" name="unidad_fila<?=$idFila;?>" id="unidad_fila<?=$idFila;?>" data-style="btn btn-primary">
                                      <?php
                                   $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
                                   $stmt->execute();
                                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                    if($codigoX==$globalUnidad){
                                       ?><option selected value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                    }else{
                                       ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                    }                                     
                                  }
                                    ?>
                                   </select>
                                   </div>
                                       <div class="form-group" style="width:100% !important;">
                                       <select class="selectpicker form-control form-control-sm col-sm-12" name="area_fila<?=$idFila;?>" id="area_fila<?=$idFila;?>" data-style="btn btn-rose">
                                               <!--<option value="" disabled selected>Area</option>-->
                                     <?php
                                                             
                                           $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and (centro_costos=1 or codigo=1235) order by 2");
                                         $stmt->execute();
                                         $cont=0;
                                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                           $codigoX=$row['codigo'];
                                           $nombreX=$row['nombre'];
                                           $abrevX=$row['abreviatura'];
                                           if($codigoX==$globalArea){
                                             ?><option selected value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                           }else{
                                             ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                           }   
                                         } 
                                         ?>
                                        </select>
                                      </div>
                                 </div>
		<div class="row col-sm-3">
            <div class="form-group col-sm-2 d-none">
               <div class="row">
			           
                <div class="col-sm-12">
          		  <div class="form-check">
                     <label class="form-check-label">
                      <input class="form-check-input" onchange="habilitarFila(<?=$idFila;?>)" type="checkbox" id="habilitar<?=$idFila?>" name="habilitar<?=$idFila?>" checked value="1">
                         <span class="form-check-sign">
                                 <span class="check"></span>
                          </span>
                       </label>
                  </div>
               </div>
              </div>   	
			   </div>
         <div class="form-group col-sm-12">
             <select class="selectpicker form-control form-control-sm"  data-live-search="true" data-size="6" name="partida_cuenta_id<?=$idFila?>" id="partida_cuenta_id<?=$idFila?>" required data-style="btn btn-warning" onchange="verificarDivisionPagoFila(<?=$idFila;?>)">
                    <option disabled selected value="">CUENTAS</option>
                  <?php
                  $cuentaLista=obtenerCuentasListaSolicitud(); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
                while ($rowCuenta = $cuentaLista->fetch(PDO::FETCH_ASSOC)) {
                  $codigoX=$rowCuenta['codigo'];
                  $numeroX=$rowCuenta['numero'];
                  $nombreX=$rowCuenta['nombre'];
                ?>
                <option value="<?=$codigoX;?>">[<?=$numeroX?>] <?=$nombreX;?></option>  
                <?php
                  }
                  ?>
              </select>

             <!--<label for="partida_cuenta<?=$idFila;?>" class="bmd-label-floating d-none">PARTIDA PRES. / Cuenta</label>-->
             <!--<input class="form-control" type="hidden" name="partida_cuenta_id<?=$idFila?>" id="partida_cuenta_id<?=$idFila?>"/>-->     
             <input class="form-control" type="hidden" autofocus name="partida_cuenta<?=$idFila;?>" id="partida_cuenta<?=$idFila;?>" required value=""> 
           </div>
    </div>
    <input type="hidden" id="unidad<?=$idFila;?>" name="unidad<?=$idFila;?>" value="<?=$unidadSol?>">
    <input type="hidden" id="area<?=$idFila;?>" name="area<?=$idFila;?>" value="<?=$areaSol?>">
    <input type="hidden" id="cod_detalleplantilla<?=$idFila;?>" name="cod_detalleplantilla<?=$idFila;?>" value="">
    <input type="hidden" id="cod_servicioauditor<?=$idFila;?>" name="cod_servicioauditor<?=$idFila;?>" value="">    
       
    <input type="hidden" id="cod_actividadproyecto<?=$idFila;?>" name="cod_actividadproyecto<?=$idFila;?>" value="0">
    <input type="hidden" id="cod_accproyecto<?=$idFila;?>" name="cod_accproyecto<?=$idFila;?>" value="0">
    <input type="hidden" id="des_actividadproyecto<?=$idFila;?>" name="des_actividadproyecto<?=$idFila;?>" value="">       
        <div class="col-sm-3">
		    <div class="form-group">
          		<!--<label for="detalle_detalle<?=$idFila;?>" class="bmd-label-static">Detalle</label>-->
				<textarea rows="3" class="form-control" name="detalle_detalle<?=$idFila;?>" required id="detalle_detalle<?=$idFila;?>" value=""></textarea>
			</div>
		</div>
    <div class="col-sm-1">
            <div class="form-group">
               <!--<label for="importe_presupuesto<?=$idFila;?>" class="bmd-label-floating">Imp Pres</label>      -->
               <input class="form-control" type="number" required name="importe_presupuesto<?=$idFila;?>" id="importe_presupuesto<?=$idFila;?>" step="any" value="0" readonly>  
      </div>
    </div>
		<div class="col-sm-1">
            <div class="form-group">
            	<label for="importe<?=$idFila;?>" class="bmd-label-floating d-none" id="importe_label<?=$idFila;?>">Importe</label>     
          		<input class="form-control" value="0" type="number" required name="importe<?=$idFila;?>" id="importe<?=$idFila;?>" step="any" onChange="calcularTotalesSolicitud();" OnKeyUp="calcularTotalesSolicitud();">	
			</div>
      	</div>
      	<div class="col-sm-2">
            <div class="form-group">
                <select class="selectpicker form-control form-control-sm" onchange="quitarFormaPagoProveedor(<?=$idFila?>)" data-live-search="true" data-size="6" name="proveedor<?=$idFila?>" id="proveedor<?=$idFila?>" required data-style="<?=$comboColor;?>">
                    <option disabled selected value="">Proveedor</option>
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
		<div class="col-sm-1">
          <div class="row">
            <input type="hidden" name="cod_cuentaBancaria<?=$idFila?>" id="cod_cuentaBancaria<?=$idFila?>" value="0"/>
            <input type="hidden" name="cod_tipopago<?=$idFila?>" id="cod_tipopago<?=$idFila?>" value="0"/>
            <input type="hidden" name="nombre_beneficiario<?=$idFila?>" id="nombre_beneficiario<?=$idFila?>" value=""/>
            <input type="hidden" name="apellido_beneficiario<?=$idFila?>" id="apellido_beneficiario<?=$idFila?>" value=""/>
            <input type="hidden" name="cuenta_beneficiario<?=$idFila?>" id="cuenta_beneficiario<?=$idFila?>" value=""/>
            
            <span id="archivos_fila<?=$idFila?>" class="d-none">
                                       <?php
                                       //archivos adjuntos detalle
                                        $stmtArchivo = $dbh->prepare("SELECT * from ibnorca.vw_plantillaDocumentos where idTipoServicio=2708"); //2708 //2708 localhost
                                        $stmtArchivo->execute();
                                        $filaA=0;
                                        while ($rowArchivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC)) {
                                           $filaA++;
                                           $codigoX=$rowArchivo['idClaDocumento'];
                                           $nombreX=$rowArchivo['Documento'];
                                           $ObligatorioX=$rowArchivo['Obligatorio'];
                                        ?>
                                        <input type="hidden" name="codigo_archivodetalle<?=$filaA?>FFFF<?=$idFila?>" id="codigo_archivodetalle<?=$filaA?>FFFF<?=$idFila?>" value="<?=$codigoX?>">
                                        <input type="hidden" name="nombre_archivodetalle<?=$filaA?>FFFF<?=$idFila?>" id="nombre_archivodetalle<?=$filaA?>FFFF<?=$idFila?>" value="<?=$nombreX?>">
                                        <input type="file" class="archivo" name="documentos_detalle<?=$filaA?>FFFF<?=$idFila?>" id="documentos_detalle<?=$filaA?>FFFF<?=$idFila?>"/>
                                       <?php
                                        }
                                        ?>
                                      </span>  
                                      <input type="hidden" value="<?=$filaA?>" id="cantidad_archivosadjuntosdetalle<?=$idFila?>" name="cantidad_archivosadjuntosdetalle<?=$idFila?>">
                                        <?php
                                     //fin archivos adjuntos detalle  
            ?>  
           <div class="btn-group col-sm-12">
            <a  title="Forma de Pago" href="#" class="btn btn-success btn-sm btn-fab" id="boton_formapago<?=$idFila;?>" onclick="agregarTipoPagoProveedorDetalle(<?=$idFila;?>)">
                  <i class="material-icons">money</i><span id="nben<?=$idFila?>" class="bg-danger"></span>
            </a>
            <input type="hidden" name="cod_retencion<?=$idFila?>" id="cod_retencion<?=$idFila?>" value=""/>
            <a href="#" title="Retenciones" id="boton_ret<?=$idFila;?>" onclick="listRetencion(<?=$idFila;?>);" class="btn btn-warning text-dark btn-sm btn-fab">
                    <i class="material-icons">ballot</i><span id="nret<?=$idFila?>" class="bg-danger"></span>
            </a>
            <a href="#" title="Facturas" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="btn btn-info btn-sm btn-fab">
                    <i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span>
            </a>
            </div>
            <div class="btn-group col-sm-12" style="top:-15px !important;">
            <span id="archivos_fila<?=$idFila?>" class="d-none"><input type="file" name="archivos<?=$idFila?>[]" id="archivos<?=$idFila?>" multiple="multiple"/></span>
            <a href="#" title="Archivos" id="boton_archivos<?=$idFila;?>" onclick="addArchivos(<?=$idFila;?>);" class="btn btn-default btn-sm btn-fab d-none">
              <i class="material-icons"><?=$iconFile?></i><span id="narch<?=$idFila?>" class="bg-warning"></span>
            </a>
            <input type="hidden" name="cod_servicio<?=$idFila?>" id="cod_servicio<?=$idFila?>" value=""/>
            <a  title="Servicio" href="#" class="btn btn-default btn-sm btn-fab" id="boton_servicio<?=$idFila;?>" onclick="agregarServicioDetalleSR(<?=$idFila;?>)">
                  <i class="material-icons text-dark">flag</i><span id="nserv<?=$idFila?>" class="bg-danger"></span>
            </a>
            <input type="hidden" name="cod_divisionpago<?=$idFila?>" id="cod_divisionpago<?=$idFila?>" value=""/>      
            <a  title="Distribución del Pago" href="#" class="btn btn-info btn-sm btn-fab d-none" id="boton_division<?=$idFila;?>" onclick="agregarDivisionPagoDetalleSR(<?=$idFila;?>)" style="background:#FF8244;">
                  <i class="material-icons text-dark">dashboard</i><span id="ndiv<?=$idFila?>" class="bg-white"></span>
            </a> 
            <a  title="Eliminar (alt + q)" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="minusDetalleSolicitud('<?=$idFila;?>');">
                  <i class="material-icons">remove_circle</i>
            </a>
            </div>                           
          </div>  
    </div>

	</div>
</div>
<div class="h-divider"></div>
<script>//autocompletar("partida_cuenta"+<?=$idFila?>,"partida_cuenta_id"+<?=$idFila?>,array_cuenta);</script>

<script>listarProyectosSisdeUnidades();</script>