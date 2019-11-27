<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

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

//$codigoPlan=$_GET['codigo'];

  $json=json_decode($_GET["listDist"]);
  $idFila=$_GET['filas'];
  $areaDet=$_GET['area'];
  $valor=$_GET['valor'];
  $glosa=$_GET['glosa'];
  $cuenta=$_GET['cuenta'];
  $cuenta_aux=$_GET['cuenta_aux'];
  if($cuenta_aux==0){
   $nom_cuenta_auxiliar="";
   $n_cuenta_auxiliar=0;
  }else{
   $nom_cuenta_auxiliar=nameCuentaAux($cuenta_aux);
   $n_cuenta_auxiliar=obtieneNumeroCuenta($cuenta_aux);
  }

  $nom_cuenta=nameCuenta($cuenta);
  $n_cuenta=obtieneNumeroCuenta($cuenta);
  
  $cond=$_GET['cond'];


  for ($i=0; $i < cantidadF($json); $i++) {

    $unidadDet=$json[$i]->cod_unidad;
    //$areaDet=$json[1][$i]->area;
    $porcent=$json[$i]->porcent;
    if($cond==0){
    $debe=$valor*($porcent/100);
    $haber=0;
    $estadohaber="readonly";
    $estadodebe="";
   }else{
    $debe=0;
    $haber=$valor*($porcent/100);
    $estadodebe="readonly";
    $estadohaber="";
   }
    

   // $glosa=$json[$i]->glosa_detalle;
    //$cod_cuenta=$json[$i]->cuenta;
    //$cod_cuenta_aux=$json[$i]->cuenta_auxiliar;
    //$nombre_cuenta=$json[$i]->nom_cuenta;
    //$nombre_cuenta_aux=$json[$i]->nom_cuenta_auxiliar;
    //$numero_cuenta=$json[$i]->n_cuenta;
    $idFila=$idFila+1; 
      ?>   
      <script>
      numFilas++;
      cantidadItems++;
      filaActiva=numFilas;
      //aumentar un itemfactura
      var nfac=[];
      itemFacturas.push(nfac);
      document.getElementById("cantidad_filas").value=numFilas;
      </script>   
<div id="div<?=$idFila?>">
 <div class="col-md-12">
  <div class="row">
    <div class="col-sm-1">
          <div class="form-group">
          <select class="selectpicker form-control form-control-sm" name="unidad<?=$idFila;?>" id="unidad<?=$idFila;?>" data-style="btn btn-primary" >
               <?php
                                   $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
                                   $stmt->execute();
                                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                    if($codigoX==$unidadDet){
                                             ?><option value="<?=$codigoX;?>" selected><?=$abrevX;?></option><?php
                                    }
                                    }
                                    ?>
      </select>
      </div>
    </div>
    <div class="col-sm-1">
          <div class="form-group">
          <select class="selectpicker form-control form-control-sm" name="area<?=$idFila;?>" id="area<?=$idFila;?>" data-style="btn btn-primary">
          <?php
                                  
                                  $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
                                $stmt->execute();
                                $cont=0;
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                  if($codigoX==$areaDet){
                                    $cont=1;     
                                    }
                                }
                                if($cont==0) {
                                  ?><option value="<?=$areaDet;?>" selected>SA</option><?php
                                }else{
                                $stmt2 = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
                                $stmt2->execute();
                                  while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row2['codigo'];
                                  $nombreX=$row2['nombre'];
                                  $abrevX=$row2['abreviatura'];
                                  if($codigoX==$areaDet){
                                      ?><option value="<?=$codigoX;?>" selected><?=$abrevX;?></option><?php
                                    }
                                   }  

                                }  
                                   ?>
      </select>
    </div>
  </div>

   <div class="col-sm-3">
    <input type="hidden" name="numero_cuenta<?=$idFila;?>" value="<?=$numero_cuenta?>" id="numero_cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta<?=$idFila;?>" value="<?=$cuenta?>" id="cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta_auxiliar<?=$idFila;?>" value="<?=$cuenta_aux?>" id="cuenta_auxiliar<?=$idFila;?>">
          <div class="form-group" id="divCuentaDetalle<?=$idFila;?>">
           <span class="text-info font-weight-bold">[<?=$n_cuenta?>]-<?=$nom_cuenta?> </span><br><span class="text-info font-weight-bold small"><?=$nom_cuenta_auxiliar?></span>     
            <p class="text-muted"><?=$porcent?> <span>%</span> de <?=$valor?></p>   
          </div>
    </div>
    <div class="col-sm-2">
            <div class="form-group">
              <label class="bmd-label-static">Debe</label>      
              <input class="form-control small" type="number" placeholder="0" value="<?=$debe?>" <?=$estadodebe?> name="debe<?=$idFila;?>" id="debe<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01"> 
      </div>
    </div>
    <div class="col-sm-2">
            <div class="form-group">
              <label class="bmd-label-static">Haber</label>     
              <input class="form-control small" type="number" placeholder="0" value="<?=$haber?>" <?=$estadohaber?> name="haber<?=$idFila;?>" id="haber<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01">   
      </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
              <label class="bmd-label-static">GlosaDetalle</label>
        <textarea class="form-control" name="glosa_detalle<?=$idFila;?>" id="glosa_detalle<?=$idFila;?>"><?=$glosa?></textarea>
      </div>
    </div>
    <div class="col-sm-1">
      <div class="btn-group">
        <a href="#" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="btn btn-just-icon btn-info btn-link">
               <i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span>
             </a>
      <button rel="tooltip" class="btn btn-just-icon btn-danger btn-link" id="boton_remove<?=$idFila;?>" onclick="minusCuentaContable('<?=$idFila;?>');">
              <i class="material-icons">remove_circle</i>
          </button>
        </div>  
    </div>
   </div>
 </div>
 <div class="h-divider"></div>
</div>
      <?php

  }

?>
<script>calcularTotalesComprobante("null");</script>
