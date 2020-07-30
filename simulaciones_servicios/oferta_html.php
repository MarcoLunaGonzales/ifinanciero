<?php
require_once '../assets/libraries/CifrasEnLetras.php';
$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
if(isset($_GET['cod'])){
  $codigo=$_GET['cod'];
}else{
  $codigo=0;
}
$usd=6.96;

$nombreClienteX=obtenerNombreClienteSimulacion($codigo);

$stmt1 = $dbh->prepare("SELECT sc.*,es.nombre as estado from simulaciones_servicios sc join estados_simulaciones es on sc.cod_estadosimulacion=es.codigo where sc.cod_estadoreferencial=1 and sc.codigo='$codigo'");
      $stmt1->execute();
      $stmt1->bindColumn('codigo', $codigoX);
            $stmt1->bindColumn('nombre', $nombreX);
            $stmt1->bindColumn('fecha', $fechaX);
            $stmt1->bindColumn('cod_responsable', $codResponsableX);
            $stmt1->bindColumn('estado', $estadoX);
            $stmt1->bindColumn('cod_plantillaservicio', $codigoPlan);
            $stmt1->bindColumn('dias_auditoria', $diasSimulacion);
            $stmt1->bindColumn('utilidad_minima', $utilidadIbnorcaX);
            $stmt1->bindColumn('productos', $productosX);
            $stmt1->bindColumn('sitios', $sitiosX);
            $stmt1->bindColumn('anios', $anioX);
            $stmt1->bindColumn('porcentaje_fijo', $porcentajeFijoX);
            $stmt1->bindColumn('afnor', $afnorX);
            $stmt1->bindColumn('porcentaje_afnor', $porcentajeAfnorX);
            $stmt1->bindColumn('id_tiposervicio', $idTipoServicioX);
            $stmt1->bindColumn('alcance_propuesta', $alcanceSimulacionX);
            $stmt1->bindColumn('descripcion_servicio', $descripcionServSimulacionX);

      while ($row1 = $stmt1->fetch(PDO::FETCH_BOUND)) {
        $descripcionServSimulacionX=$descripcionServSimulacionX;
        $anioX=$anioX;
        $anioLetra=strtolower(CifrasEnLetras::convertirNumeroEnLetras($anioX));

        $gestionInicio=(int)strftime('%Y',strtotime($fechaX));
      }
/*                        archivo HTML                      */

?>
<!-- formato cabeza fija para pdf-->
<div class="col-sm-12">
  <center><h3 class="text-primary"><?=$descripcionServSimulacionX?></h3></center>
  <center><h4 class="text-muted"><u><?=obtenerTituloEdicionOferta($codigo)?></u></h4></center>
</div>
<div class="row col-sm-12"> 

 <form method="POST" action="<?='../'.$urlSaveOferta?>" class="row col-sm-11 div-center">
  <input type="hidden" value="<?=$default?>" name="por_defecto">
  <input type="hidden" value="<?=$codOferta?>" name="oferta">
  <input type="hidden" value="<?=$codigo?>" name="simulacion">
  <?php 
  if($default==1){
    $stmt = $dbh->prepare("SELECT oc.*,o.nombre as cabecera from ofertas_complementos oc join tipos_ofertascomplementos o on o.codigo=oc.cod_tipocomplemento where oc.cod_oferta=$codOferta and oc.cod_estadoreferencial=1 and oc.editable=1 order by oc.codigo,oc.orden;");
  }else{
    $stmt = $dbh->prepare("SELECT oc.*,o.nombre as cabecera from simulaciones_servicios_ofertas_complementos oc join tipos_ofertascomplementos o on o.codigo=oc.cod_tipocomplemento join simulaciones_servicios_ofertas so on so.codigo=oc.cod_simulacionoferta where oc.cod_simulacionoferta=$codOferta and oc.cod_estadoreferencial=1 and oc.editable=1 and so.cod_simulacionservicio=$codigo order by oc.codigo,oc.orden;");  
  }
  $stmt->execute();
  $fila=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $fila++;
    $cabecera=$row['cabecera'];
    $codigoItem=$row['cod_tipocomplemento'];
    if($row['orden']>1){
     $cabecera=$row['cabecera']." ".$row['orden'];  
    }
    
    $descripcion=$row['descripcion'];
    $descripcion_alterna=$row['descripcion_alterna'];
        ?>
      <input type="hidden" value="<?=$codigoItem?>" name="codigo<?=$fila?>">
      <input type="hidden" value="<?=$row["orden"]?>" name="orden<?=$fila?>">  
  <div class="form-group row col-sm-12">
    <label class="col-sm-2 col-form-label font-weight-bold" style="color:#5D0185; "><?=$cabecera?></label>
    <div class="col-sm-10">
      <div class="form-group">
        <?php
        if(strlen($descripcion)>110){
          ?>
           <textarea class="form-control" name="descripcion<?=$fila?>" rows="8" style="background-color:#E3CEF6;text-align: left"><?=$descripcion?></textarea>
          <?php
        }else{
          ?><input type="text" name="descripcion<?=$fila?>" class="form-control" style="background-color:#E3CEF6;text-align: left" value="<?=$descripcion?>"><?php
        } 
         ?>
        
      </div>
    </div>
  </div>
        <?php
      }
  ?>
  <input type="hidden" value="<?=$fila?>" name="cantidad_items">

</div>
<div class="row col-sm-12">
  <div class="col-sm-1 div-center">
     <img class="" width="120" height="120" src="../assets/img/ibnorca2.jpg">
  </div>
</div>

<div class="card-footer fixed-bottom">

            <?php 
            if(!(isset($_GET['q']))){
             ?>
              <button type="submit" class="btn btn-primary text-white"><i class="material-icons">save</i> Guardar</button><?php    
             ?>   
            <a href="../<?=$urlList;?>" class="btn btn-danger">Volver</a><?php
            }else{
              $r="";
              if(isset($_GET['r'])){
                $r="&r=".$_GET['r'];
              }
              $urlDatos="&q=".$_GET['q'].$r."&s=".$_GET['s']."&u=".$_GET['u'];
              ?><input type="hidden" name="url" value="<?=$urlDatos?>">
              <button type="submit" class="btn btn-primary text-white"><i class="material-icons">save</i> Guardar</button><?php
            ?>
            <a href="../<?=$urlList;?><?=$urlDatos?>" class="btn btn-danger">Volver</a><?php
            }
            ?>
             
            </div>

</form>
<?php
 if($default==1){
  ?>
  <script>
  $(document).ready(function() {
    notificacionMD('random','top','right',false,'add_alert','IFINANCIERO','Verifique que los datos sean correctos antes de <b>Guardar</b>','<img src="../assets/img/robot.gif" width="100px" height="100px">');
   });
   </script>
  <?php  
 }
 ?>

   