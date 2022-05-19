<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalAdmin=$_SESSION["globalAdmin"];
$codigoAdministrativos=obtenerValorConfiguracion(46);
//listado de tipo documento rendicion
// $statementTipoDocRendicion = $dbh->query("SELECT td.codigo,td.nombre from tipos_documentocajachica td where td.tipo=2 order by 2");

$idFila=$_GET['idFila'];
$cod_area=$_GET['cod_area'];
$IdTipo=$_GET['IdTipo'];



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
          <!-- <label for="haber<?=$idFila;?>" class="bmd-label-floating">Glosa</label> -->
          <select class="selectpicker form-control form-control-sm" data-live-search="true" name="modal_editservicio<?=$idFila;?>" id="modal_editservicio<?=$idFila;?>" data-style="fondo-boton" required="true">
              <option disabled selected="selected" value="">--SERVICIOS--</option>
              <?php 
                $sql="SELECT IdClaServicio,Descripcion,Codigo from cla_servicios where (codigo_n1=108 or codigo_n1=109) and vigente=1 and codigo_n1=$codigoAreaServ and IdTipo=$IdTipo
                UNION 
                  Select IdClaServicio,Descripcion,Codigo from cla_servicios where codigo_n2 in ($codigoAdministrativos)";
                $stmt3 = $dbh->prepare($sql);
                
                // echo $sql; 
                
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
        
        <input type="number" step="0.01" min="1" id="cantidad_servicios<?=$idFila;?>" name="cantidad_servicios<?=$idFila;?>" class="form-control text-primary text-right" value="1" required="true" onkeyup="cantidad_por_importe_servicio_sf(<?=$idFila?>)">
      </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
          
          <input type="number" id="modal_montoserv<?=$idFila;?>" name="modal_montoserv<?=$idFila;?>" class="form-control text-primary text-right"   step="0.01" onkeyup="cantidad_por_importe_servicio_sf(<?=$idFila?>)"  value="0" required="true">
      </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">          
          
          <input type="text" class="form-control" name="descuento_por_add<?=$idFila?>" id="descuento_por_add<?=$idFila?>" value="0" onkeyup="descuento_convertir_a_bolivianos_add(<?=$idFila?>)">
      </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
          
          <input type="text" class="form-control" name="descuento_bob_add<?=$idFila?>" id="descuento_bob_add<?=$idFila?>" value="0" onkeyup="descuento_convertir_a_porcentaje_add(<?=$idFila?>)">
      </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
          
          <input type="hidden" name="modal_importe_add<?=$idFila?>" id="modal_importe_add<?=$idFila?>">
          <input type="text" class="form-control" name="modal_importe_dos_add<?=$idFila?>" id="modal_importe_dos_add<?=$idFila?>" value="0" style ="background-color: #ffffff;" readonly>
      </div>
    </div>

    <div class="col-sm-2">
      <div class="form-group">
        
        <textarea id="descripcion<?=$idFila;?>" name="descripcion<?=$idFila;?>" class="form-control text-primary" onkeyup="javascript:this.value=this.value.toUpperCase();" required></textarea>
        
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

