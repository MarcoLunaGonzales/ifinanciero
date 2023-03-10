<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
//$dbh = new Conexion();
$globalUser=$_SESSION["globalUser"];

error_reporting(E_ALL);
ini_set('display_errors', '1');

$dbh = new Conexion();
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];
}

$cod_simulacion=0;
$cod_facturacion=null;
$contadorRegistros=0;

$queryPr="SELECT *,DATE_FORMAT(Fecha,'%d/%m/%Y')as Fecha_x from ibnorca.ventanormas where (idSolicitudfactura=0 or idSolicitudfactura is null) order by Fecha desc limit 50";

 //echo $queryPr;

$stmt = $dbh->prepare($queryPr);
$stmt->execute();                    
?>
<script>
  numFilas=<?=$contadorRegistros;?>;
  cantidadItems=<?=$contadorRegistros;?>;
</script>
<div class="content">
    <div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
            <form id="form1" class="form-horizontal" action="<?=$urlRegisterSolicitudfactura;?>" method="post" onsubmit="return valida(this)">
                <?php 
                  if(isset($_GET['q']) || isset($_POST['q'])){
                    ?>
                    <input type="hidden" name="q" id="q" value="<?=$q;?>">
                    <input type="hidden" name="v" id="v" value="<?=$v;?>">
                    <input type="hidden" name="s" id="s" value="<?=$s;?>">
                    <input type="hidden" name="u" id="u" value="<?=$u;?>">
                    <?php
                  }
                  ?>       
                 
                <div class="card">
                    <div class="card-header <?=$colorCard;?> card-header-text">
                        <div class="card-text">
                          <h4 class="card-title">Venta de Normas</h4>                      
                        </div>
                        <!-- <h4 class="card-title" align="center"><b>Venta de Normas</b></h4> -->
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group" align="right">
                                <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscador">
                                    <i class="material-icons" title="Buscador Avanzado">search</i>
                                </button> 
                                <!-- <a href="#" class="btn btn-primary btn-round btn-fab btn-sm" onclick="actualizarRegistroNormas()">
                                   <i class="material-icons" title="Actualizar Normas">update</i>
                                </a>  -->
                            </div>
                        </div>
                    </div>
                    <div class="card-body ">
                        <div id="contenedor_items_normas">                            
                            <table class="table table-bordered table-condensed  table-sm">
                                 <thead>
                                      <tr class="fondo-boton">
                                        <th>#</th>
                                        <!-- <th >Año</th> -->
                                        <th>Oficina</th>
                                        <th>Fecha</th>
                                        <th width="20%">Cliente</th>
                                        <th>Código<br> Norma</th>
                                        <th>Nombre Norma</th>
                                        <th>Idioma</th>
                                        <th>Tipo Venta</th>
                                        <th>Cantidad</th>
                                        <th width="10%">Importe<br>(BOB)</th>                                            
                                        <th class="small">H/D</th>  
                                      </tr>
                                  </thead>
                                  <tbody>                                
                                    <?php 
                                    $iii=1;
                                    while ($rowPre = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $idVentaNormas=$rowPre['IdVentaNormas'];
                                        $idOficina=$rowPre['idOficina'];
                                        $nombre_oficina=trim(abrevUnidad($idOficina),'-');
                                        $NombreCliente=$rowPre['NombreCliente'];
                                        $Fecha=$rowPre['Fecha_x'];
                                        $idNorma=$rowPre['idNorma'];
                                        $Catalogo=$rowPre['Catalogo'];
                                        $Norma_descripcion=abrevNorma($idNorma,$Catalogo);
                                        $Norma=nameNorma($idNorma,$Catalogo);
                                        $Cantidad=$rowPre['Cantidad'];
                                        $idCliente=$rowPre['idCliente'];
                                        $tipoVenta=$rowPre['TipoVenta'];
                                        $idTipoVenta=$rowPre['idTipoVenta'];
                                        $idPromocion=$rowPre['idPromocion'];
                                        $idiomaNorma=$rowPre['Idioma'];

                                        if($Cantidad==null || $Cantidad=='' || $Cantidad==' '){
                                          $Cantidad=1;
                                        }
                                        $Precio=$rowPre['Precio'];
                                       ?>
                                        <!-- guardamos todos los items en inputs -->
                                        <input type="hidden" id="idVentaNormas<?=$iii?>" name="idVentaNormas<?=$iii?>" value="<?=$idVentaNormas?>">
                                        <!-- aqui se captura los items activados -->
                                        <input type="hidden" id="idVentaNormas_a<?=$iii?>" name="idVentaNormas_a<?=$iii?>">
                                        <tr>
                                          <td><?=$iii?></td>
                                          <td class="text-left small"><?=$nombre_oficina?></td>
                                          <td class="text-righ small"><?=$Fecha?></td>
                                          <td class="text-left small"><?=$NombreCliente?></td>
                                          <td class="text-left small"><?=$Norma?></td>
                                          <td class="text-left small"><?=$Norma_descripcion?></td>
                                          <td class="text-left small"><?=$idiomaNorma?></td>
                                          <td class="text-left small"><?=$tipoVenta?></td>
                                          <td class="text-righ smallt"><?=$Cantidad?></td>
                                          <td class="text-righ smallt"><?=number_format($Precio,2,".","")?></td>
                                          <!-- checkbox -->
                                          <td>
                                            <div class="togglebutton">
                                              <label>
                                                <input type="checkbox"  id="modal_check<?=$iii?>" onchange="itemsSeleccionados_ventaNormas()">
                                                <span class="toggle"></span>
                                              </label>
                                            </div>                                                    
                                          </td><!-- fin checkbox -->
                                        </tr>
                                        <?php   
                                        $iii++;
                                        } ?> 
                                        <input type="hidden" id="total_items" name="total_items" value="<?=$iii?>">
                                        <input type="hidden" id="comprobante_auxiliar" name="comprobante_auxiliar"><!-- contador de items seleccioados -->                     
                                  </tbody>
                            </table>                            
                        </div>                        
                    </div>
                    <div class="card-footer fixed-bottom">
                        <button type="submit" class="<?=$buttonNormal;?>">Seleccionar</button>
                        <?php
                          if(isset($_GET['q'])){  ?>
                              <a href='<?=$urlSolicitudfactura;?>&q=<?=$q?>&r=<?=$r?>&s=<?=$s?>&u=<?=$u?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> IR A SF</a>
                          <?php }else{?>
                              <a href='<?=$urlSolicitudfactura?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> IR A SF</a>
                          <?php }
                          ?>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>



<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde un momento por favor</p>  
  </div>
</div>
<div class="modal fade modal-arriba modal-primary" id="modalAgregarProveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
            <div class="card-header card-header-warning card-header-icon">
                <div class="card-icon">
                    <i class="material-icons text-dark">ballot</i>
                 </div>
                  <h4 class="card-title">Contacto</h4>
            </div>
            <div class="card-body">
                 <div id="datosProveedorNuevo">
                   
                 </div> 
                <div class="form-group float-right">
                        <button type="button" onclick="guardarDatoscontacto()" class="btn btn-info btn-round">Agregar</button>
                </div>
          </div>
      </div>  
    </div>
  </div>
<!-- Modal busqueda de items-->
<div class="modal fade" id="modalBuscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Buscador de Normas</h4>
      </div>
      <div class="modal-body ">
        <div class="row">
            <label class="col-sm-3 col-form-label text-center">Cliente</label> 
            <label class="col-sm-6 col-form-label text-center">Fechas</label>                  
            <label class="col-sm-3 col-form-label text-center">Normas</label>                                
        </div> 
        <div class="row">
            <div class="form-group col-sm-3">
                <input class="form-control input-sm" type="text" name="glosaCliente" id="glosaCliente"  >
            </div>
            <div class="form-group col-sm-3">
                <input class="form-control input-sm" type="date" name="fechaBusquedaInicio" id="fechaBusquedaInicio">
            </div>
            <div class="form-group col-sm-3">
                <input class="form-control input-sm" type="date" name="fechaBusquedaFin" id="fechaBusquedaFin" >
            </div>
            <div class="form-group col-sm-3">            
                <select name="normas[]" id="normas" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple> 
                <option value="0"></option>
                <?php 
                $query1 = "SELECT codigo,nombre,abreviatura from normas where cod_estado=1 order by abreviatura";
                $statementUO1 = $dbh->query($query1);
                while ($row = $statementUO1->fetch()){ ?>
                    <option value="<?=$row["codigo"];?>" data-subtext="(<?=$row['codigo']?>)"><?=$row["abreviatura"];?> </option>
                <?php } ?>

                </select>
            
            </div>              
        </div> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="botonBuscarComprobante" name="botonBuscarComprobante" onclick="botonBuscarNormasSolfac()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
    function valida(f) {
      var ok = true;
      var msg = "Habilite los Items que desee Solicitar la factura...\n";  
      if(f.elements["comprobante_auxiliar"].value == 0 || f.elements["comprobante_auxiliar"].value < 0 || f.elements["comprobante_auxiliar"].value == '')
      {    
        ok = false;
      }
      if(ok == false)    
        Swal.fire("Informativo!",msg, "warning");
      return ok;
    }
</script>