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

$codigoPlan=$_GET['codigo'];

$data=obtenerPlantilla($codigoPlan);
// bindColumn
$data->bindColumn('codigo', $codigo);
$data->bindColumn('titulo', $titulo);
$data->bindColumn('descripcion', $descripcion);
$data->bindColumn('archivo_json', $archivo);
$data->bindColumn('cod_unidadorganizacional', $codUnidad);
$data->bindColumn('cod_personal', $codPersona);


while ($row = $data->fetch(PDO::FETCH_BOUND)) {
    //REALIZAR ALGO AQUI
  $json=json_decode($archivo);
?>
<button type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addCuentaContable(this)" accesskey="a">
    <i class="material-icons">add</i>
</button>
  <div class="col-sm-1 float-right">
      <a href="#modalCopySel" data-toggle="modal" data-target="#modalCopySel" class="<?=$buttonDelete?> btn-fab">
        <i class="material-icons"><?=$iconCopy?></i>
      </a>
  </div>
  <div>
    <div class="h-divider"></div>
  </div> 

<?php
  $totaldebDet=0;$totalhabDet=0;
  for ($i=0; $i < cantidadF($json[1]); $i++) {
    if($json[1][$i]->debe==""){
      $json[1][$i]->debe=0;
    }
    if($json[1][$i]->haber==""){
      $json[1][$i]->haber=0;
    }
    $totaldebDet+=$json[1][$i]->debe;$totalhabDet+=$json[1][$i]->haber;
    $unidadDet=$json[1][$i]->unidad;
    $areaDet=$json[1][$i]->area;
    $debe=$json[1][$i]->debe;
    $haber=$json[1][$i]->haber;
    $glosa=$json[1][$i]->glosa_detalle;
    $cod_cuenta=$json[1][$i]->cuenta;
    $cod_cuenta_aux=$json[1][$i]->cuenta_auxiliar;
    $nombre_cuenta=$json[1][$i]->nom_cuenta;
    $nombre_cuenta_aux=$json[1][$i]->nom_cuenta_auxiliar;
    $numero_cuenta=$json[1][$i]->n_cuenta;
  $idFila=$i+1; 
      ?>      
<div id="div<?=$idFila?>">
 <div class="col-md-12">
  <div class="row">
    <div class="col-sm-1">
          <div class="form-group">
          <select class="selectpicker form-control form-control-sm" name="unidad<?=$idFila;?>" id="unidad<?=$idFila;?>" data-style="<?=$comboColor;?>" >
               <?php
                                   if($unidadDet==0){
                                   ?><option disabled selected="selected" value="">Unidad</option><?php 
                                   }else{
                                    ?><option disabled value="">Unidad</option><?php
                                   }
                                   $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
                                 $stmt->execute();
                                   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
                                    $nombreX=$row['nombre'];
                                    $abrevX=$row['abreviatura'];
                                    if($codigoX==$unidadDet){
                                             ?><option value="<?=$codigoX;?>" selected><?=$abrevX;?></option><?php
                                    }else{
                                              ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
                                    }
                                    }
                                    ?>
      </select>
      </div>
    </div>
    <div class="col-sm-1">
          <div class="form-group">
          <select class="selectpicker form-control form-control-sm" name="area<?=$idFila;?>" id="area<?=$idFila;?>" data-style="<?=$comboColor;?>" >
          <?php
                                  if($areaDet==0){
                                   ?><option disabled selected="selected" value="">Area</option><?php 
                                   }else{
                                    ?><option disabled value="">Area</option><?php
                                   }
                          
                                  $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                    if($codigoX==$areaDet){
                                        ?><option value="<?=$codigoX;?>" selected><?=$abrevX;?></option><?php
                                      }else{
                                       ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php 
                                      }
                                  }
                                   ?>
      </select>
    </div>
  </div>

   <div class="col-sm-4">
        <input type="hidden" name="numero_cuenta<?=$idFila;?>" value="<?=$numero_cuenta?>" id="numero_cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta<?=$idFila;?>" value="<?=$cod_cuenta?>" id="cuenta<?=$idFila;?>">
        <input type="hidden" name="cuenta_auxiliar<?=$idFila;?>" value="<?=$cod_cuenta_aux?>" id="cuenta_auxiliar<?=$idFila;?>">
          <div class="form-group" id="divCuentaDetalle<?=$idFila;?>">
           <span class="text-danger font-weight-bold">[<?=$numero_cuenta?>]-<?=$nombre_cuenta?> </span><br><span class="text-primary font-weight-bold small"><?=$nombre_cuenta_aux?></span>     
          </div>
    </div>
    <div class="col-sm-1">
            <div class="form-group">
              <label class="bmd-label-static">Debe</label>      
              <input class="form-control small" type="number" placeholder="0" value="<?=$debe?>" name="debe<?=$idFila;?>" id="debe<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01"> 
      </div>
    </div>
    <div class="col-sm-1">
            <div class="form-group">
              <label class="bmd-label-static">Haber</label>     
              <input class="form-control small" type="number" placeholder="0" value="<?=$haber?>" name="haber<?=$idFila;?>" id="haber<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01">   
      </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
              <label class="bmd-label-static">GlosaDetalle</label>
        <textarea rows="1" class="form-control" name="glosa_detalle<?=$idFila;?>" id="glosa_detalle<?=$idFila;?>"><?=$glosa?></textarea>
      </div>
    </div>
    <div class="col-sm-1">
      <div class="btn-group">
        <a title="Facturas" href="#" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="btn btn-info btn-sm btn-fab">
               <i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span>
             </a>
      <a title="Eliminar (alt + q)" rel="tooltip" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="minusCuentaContable('<?=$idFila;?>');">
              <i class="material-icons">remove_circle</i>
          </a>
        </div>  
    </div>
   </div>
 </div>
 <div class="h-divider"></div>
</div>
<script>$("#div"+<?=$idFila?>).bootstrapMaterialDesign();</script>
      <?php
  }
  
}
?>
