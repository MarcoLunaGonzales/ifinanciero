<?php
session_start();
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
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $unidadSol=$row['cod_unidadorganizacional'];
  $areaSol=$row['cod_area'];
}
?>
<div id="comp_row" class="col-md-12">
	<div class="row">
    <div class="col-sm-1">
                                 <div class="form-group">
                                    <select class="selectpicker form-control form-control-sm" name="unidad_fila<?=$idFila;?>" id="unidad_fila<?=$idFila;?>" data-style="btn btn-primary">
                                      <?php
                                   $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
                                   $stmt->execute();
                                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                       ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                    }
                                    ?>
                                   </select>
                                   </div>
                                 </div>
                                 <div class="col-sm-1">
                                       <div class="form-group">
                                       <select class="selectpicker form-control form-control-sm" name="area_fila<?=$idFila;?>" id="area_fila<?=$idFila;?>" data-style="btn btn-primary">
                                               <!--<option value="" disabled selected>Area</option>-->
                                     <?php
                                                             
                                           $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
                                         $stmt->execute();
                                         $cont=0;
                                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                           $codigoX=$row['codigo'];
                                           $nombreX=$row['nombre'];
                                           $abrevX=$row['abreviatura'];
                                            ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                         } 
                                         ?>
                                        </select>
                                      </div>
                                 </div>
		<div class="row col-sm-3">
            <div class="form-group col-sm-2">
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
         <div class="form-group col-sm-10">
             <label for="partida_cuenta<?=$idFila;?>" class="bmd-label-floating">PARTIDA PRES. / Cuenta</label>
             <input class="form-control" type="hidden" name="partida_cuenta_id<?=$idFila?>" id="partida_cuenta_id<?=$idFila?>"/>     
             <input class="form-control" type="text" autofocus name="partida_cuenta<?=$idFila;?>" id="partida_cuenta<?=$idFila;?>" required value=""> 
           </div>
    </div>
    <input type="hidden" id="unidad<?=$idFila;?>" name="unidad<?=$idFila;?>" value="<?=$unidadSol?>">
    <input type="hidden" id="area<?=$idFila;?>" name="area<?=$idFila;?>" value="<?=$areaSol?>">
    <input type="hidden" id="cod_detalleplantilla<?=$idFila;?>" name="cod_detalleplantilla<?=$idFila;?>" value="">
    <input type="hidden" id="cod_servicioauditor<?=$idFila;?>" name="cod_servicioauditor<?=$idFila;?>" value="">    
       
        <div class="col-sm-2">
		    <div class="form-group">
          		<label for="detalle_detalle<?=$idFila;?>" class="bmd-label-static">Detalle</label>
				<textarea rows="1" class="form-control" name="detalle_detalle<?=$idFila;?>" required id="detalle_detalle<?=$idFila;?>" value=""></textarea>
			</div>
		</div>
    <div class="col-sm-1">
            <div class="form-group">
               <label for="importe_presupuesto<?=$idFila;?>" class="bmd-label-floating">Imp Pres</label>      
               <input class="form-control" type="number" required name="importe_presupuesto<?=$idFila;?>" id="importe_presupuesto<?=$idFila;?>" step="0.001" value="0" readonly>  
      </div>
    </div>
		<div class="col-sm-1">
            <div class="form-group">
            	<label for="importe<?=$idFila;?>" class="bmd-label-floating" id="importe_label<?=$idFila;?>">Importe</label>     
          		<input class="form-control" type="number" required name="importe<?=$idFila;?>" id="importe<?=$idFila;?>" step="0.01">	
			</div>
      	</div>
      	<div class="col-sm-2">
            <div class="form-group">
                <select class="selectpicker form-control form-control-sm" name="proveedor<?=$idFila?>" id="proveedor<?=$idFila?>" required data-style="<?=$comboColor;?>">
                    <option disabled selected value="">Proveedor</option>
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
          <div class="btn-group">
            <input type="hidden" name="cod_retencion<?=$idFila?>" id="cod_retencion<?=$idFila?>" value=""/>
            <a href="#" title="Retenciones" id="boton_ret<?=$idFila;?>" onclick="listRetencion(<?=$idFila;?>);" class="btn btn-warning text-dark btn-sm btn-fab">
                    <i class="material-icons">ballot</i>
            </a>
            <a href="#" title="Facturas" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="btn btn-info btn-sm btn-fab">
                    <i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span>
            </a>
            <span id="archivos_fila<?=$idFila?>" class="d-none"><input type="file" name="archivos<?=$idFila?>[]" id="archivos<?=$idFila?>" multiple="multiple"/></span>
            <a href="#" title="Archivos" id="boton_archivos<?=$idFila;?>" onclick="addArchivos(<?=$idFila;?>);" class="btn btn-default btn-sm btn-fab">
              <i class="material-icons"><?=$iconFile?></i><span id="narch<?=$idFila?>" class="bg-warning"></span>
            </a>
            <a  title="Eliminar (alt + q)" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="minusDetalleSolicitud('<?=$idFila;?>');">
                  <i class="material-icons">remove_circle</i>
            </a>
          </div>  
    </div>

	</div>
</div>
<div class="h-divider"></div>
<!--<script>autocompletar("partida_cuenta"+<?=$idFila?>,"partida_cuenta_id"+<?=$idFila?>,array_cuenta);</script>-->