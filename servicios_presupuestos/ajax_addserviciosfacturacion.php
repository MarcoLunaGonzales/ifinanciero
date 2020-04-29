<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalAdmin=$_SESSION["globalAdmin"];
$idFila=$_GET['idFila'];
$IdTipo=$_GET['IdTipo'];


?>
<div id="comp_row" class="col-md-12">
	<div class="row">

    <div class="col-sm-3">
      <div class="form-group">               
          <select class="selectpicker form-control form-control-sm" data-live-search="true" name="modal_editservicio<?=$idFila;?>" id="modal_editservicio<?=$idFila;?>" data-style="fondo-boton" required="true">
              <option disabled selected="selected" value="">--SERVICIOS--</option>
              <?php 
                $sql="SELECT * from cla_servicios where IdTipo=$IdTipo";
                $stmt3 = $dbh->prepare($sql);
                echo $sql; 
                $stmt3->execute();
                while ($rowServ = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                  $codigoServX=$rowServ['IdClaServicio'];
                  $nombreServX=$rowServ['Descripcion'];
                  $abrevServX=$rowServ['Codigo'];
                  ?><option value="<?=$codigoServX;?>"><?=$abrevServX?> - <?=$nombreServX?></option><?php 
                }
              ?>
          </select>
		  </div>
    </div>

		<div class="col-sm-1">
      <div class="form-group">
        <label for="haber<?=$idFila;?>" class="bmd-label-floating">Cantidad</label>			
        <input type="number" min="1" id="cantidad_servicios<?=$idFila;?>" name="cantidad_servicios<?=$idFila;?>" class="form-control text-primary text-right" value="1" required="true">
			</div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
        	<label for="haber<?=$idFila;?>" class="bmd-label-floating">Precio(BOB)</label>
      		<input type="number" id="modal_montoserv<?=$idFila;?>" name="modal_montoserv<?=$idFila;?>" class="form-control text-primary text-right"   step="0.01" onChange="sumartotalAddServiciosFacturacion(this.id,event);" OnKeyUp="sumartotalAddServiciosFacturacion(this.id,event);" required="true">
			</div>
    </div>
    <div class="col-sm-6">
      <div class="form-group">
        <label for="haber<?=$idFila;?>" class="bmd-label-floating">Glosa</label>     
        <textarea id="descripcion<?=$idFila;?>" name="descripcion<?=$idFila;?>" class="form-control text-primary text-right" onkeyup="javascript:this.value=this.value.toUpperCase();"></textarea>
        
      </div>
    </div>
		<div class="col-sm-1">
		    <div class="form-group">
        		<a rel="tooltip" title="Eliminar" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="borrarItemSeriviciosFacturacion('<?=$idFila;?>');">
            		<i class="material-icons">remove_circle</i>
	        	</a>  		
			</div>
		</div>

	</div>
</div>

<div class="h-divider"></div>

