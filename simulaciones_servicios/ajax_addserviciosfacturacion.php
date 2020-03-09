<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalAdmin=$_SESSION["globalAdmin"];
//listado de tipo documento rendicion
// $statementTipoDocRendicion = $dbh->query("SELECT td.codigo,td.nombre from tipos_documentocajachica td where td.tipo=2 order by 2");

$idFila=$_GET['idFila'];
$cod_area=$_GET['cod_area'];


?>
<div id="comp_row" class="col-md-12">
	<div class="row">

    <div class="col-sm-4">
      <div class="form-group">
        <?php 
          if($cod_area==39){
              $codigoAreaServ=108;
          }else{
              if($cod_area==38){
                $codigoAreaServ=109;
              }else{
                $codigoAreaServ=0;
              }
          }
          ?>
          <label for="haber<?=$idFila;?>" class="bmd-label-floating">Descripción</label>
          <select class="selectpicker form-control form-control-sm" data-live-search="true" name="modal_editservicio<?=$idFila;?>" id="modal_editservicio<?=$idFila;?>" data-style="fondo-boton" required="true">
              <option disabled selected="selected" value="">--SERVICIOS--</option>
              <?php 
               $stmt3 = $dbh->prepare("SELECT idclaservicio,descripcion,codigo from cla_servicios where (codigo_n1=108 or codigo_n1=109) and vigente=1 and codigo_n1=$codigoAreaServ");
               $stmt3->execute();
               while ($rowServ = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                $codigoServX=$rowServ['idclaservicio'];
                $nombreServX=$rowServ['descripcion'];
                $abrevServX=$rowServ['codigo'];
                ?><option value="<?=$codigoServX;?>"><?=$abrevServX?> - <?=$nombreServX?></option><?php 
               }
              ?>
          </select>
		  </div>
    </div>

		<div class="col-sm-2">
      <div class="form-group">
        <label for="haber<?=$idFila;?>" class="bmd-label-floating">Cantidad</label>			
        <input type="number" min="1" id="cantidad_servicios<?=$idFila;?>" name="cantidad_servicios<?=$idFila;?>" class="form-control text-primary text-right" value="1" required="true">
			</div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
        	<label for="haber<?=$idFila;?>" class="bmd-label-floating">Importe</label>
      		<input type="number" id="modal_montoserv<?=$idFila;?>" name="modal_montoserv<?=$idFila;?>" class="form-control text-primary text-right"   step="0.01" onChange="sumartotalAddServiciosFacturacion(this.id,event);" OnKeyUp="sumartotalAddServiciosFacturacion(this.id,event);" required="true">
			</div>
    </div>
    <div class="col-sm-3">
      <div class="form-group">
        <label for="haber<?=$idFila;?>" class="bmd-label-floating">Descripción</label>     
        <input type="text" id="descripcion<?=$idFila;?>" name="descripcion<?=$idFila;?>" class="form-control text-primary text-right" >
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

