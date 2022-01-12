<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

setlocale(LC_TIME, "Spanish");
$sqlAreas="";
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  
  //echo "s:".$s."<br>";

  //$numeroServicio=(int)obtenerCantidadSimulacionServicio($q)+1;
  $nombreServicioIbnorca="";
  $nombreInputPropuesta=$nombreServicioIbnorca."PROPUESTA";
  if(isset($_GET['s'])){
    $arraySql=explode("IdArea=",$_GET['s']);

    //echo "arraysql: ".$arraySql[1]."<br>";

    $codigoArea=trim($arraySql[1]);

    $sqlAreas="and p.cod_area=".$codigoArea;
  }
  if(isset($_GET['u'])){
    $u=$_GET['u'];
    ?><input type="hidden" name="idPerfil" id="idPerfil" value="<?=$u?>"/><?php
  }
}else{
  $nombreInputPropuesta="";
}

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

$contadorRegistros=0;

$codSimServ=obtenerCodigoSimServicio();
?>
<script>
  numFilas=<?=$contadorRegistros;?>;
  cantidadItems=<?=$contadorRegistros;?>;
  var itemAtributos=[];
</script>

<?php
$lista= obtenerPaisesServicioIbrnorca();//null 
$fechaActual=date("Y-m-d");
$dbh = new Conexion();
?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde un momento por favor</p>  
  </div>
</div>
<div class="content">
  <div class="container-fluid">

    <div class="col-md-12">
      <div class="card">
        <div class="card-header <?=$colorCard;?> card-header-text">
        <div class="card-text">
          <h4 class="card-title">Registrar Propuesta</h4>
        </div>
        </div>
        <div class="card-body ">
              <?php 
                  if(isset($_GET['q'])){
                    echo "<center><h5><b>".$nombreServicioIbnorca."</b></h5></center>";
                    ?><input class="form-control col-sm-4" type="hidden" name="codigo_servicioibnorca" id="codigo_servicioibnorca" value="<?=$q?>"/>
                    <input type="hidden" name="codigo_servicioibnorca_s" id="codigo_servicioibnorca_s" value="<?=$s?>"/>
                    <input type="hidden" name="codigo_servicioibnorca_u" id="codigo_servicioibnorca_u" value="<?=$u?>"/><?php
                  }
              ?>

                 <div class="row">
                       <label class="col-sm-2 col-form-label">Numero:</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <input class="form-control" type="text" readonly name="nombre" id="nombre" value="-"/>
                        </div>
                        </div>
                      </div>
                      
                   <div class="row">
                       <label class="col-sm-2 col-form-label">Cliente</label>
                       <div class="col-sm-6">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group" id="lista_clientes">
                                <select class="selectpicker form-control" data-size="4" data-live-search-placeholder="Buscar cliente..." data-live-search="true" name="cliente" id="cliente" data-style="btn btn-info"  required>
          
                                <!--<option disabled selected="selected" value="">Cliente</option>-->
                                <?php
                                 $stmt = $dbh->prepare("SELECT c.codigo, c.nombre FROM clientes c where c.cod_estadoreferencial=1 order by 2");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  //$tipoX=$row['tipo'];
                                  //$abrevX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div> 
                        </div>
                       </div>
                       <div class="col-sm-1">
                        <div class="row">
                          <div class="form-group">
                            <a href="#" class="btn btn-warning btn-round btn-fab" onclick="actualizarRegistroSoloClientes()"> <!---->
                             <i class="material-icons" title="Actualizar Clientes">update</i>
                            </a>
                         </div>
                        </div>
                       </div>
                      </div><!--row-->   
                       
                  <div class="row">
                       <label class="col-sm-2 col-form-label">Plantilla de Servicios :</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                                <select class="selectpicker form-control" name="plantilla_servicio" onchange="listarDatosPlantillaSim(this.value)" id="plantilla_servicio" data-style="<?=$comboColor;?>"  data-live-search="true" title="-- Elija una plantilla --" data-style="select-with-transition" data-actions-box="true"required>
                                <?php
                                $sql="SELECT p.*, u.abreviatura as unidad,a.abreviatura as area from plantillas_servicios p,unidades_organizacionales u, areas a where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and p.cod_estadoreferencial!=2 and p.cod_estadoplantilla=3 $sqlAreas order by codigo";
                                // echo $sql;
                                 $stmt = $dbh->prepare($sql);
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                        </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Oficina de Servicio</label>
                       <div class="col-sm-2">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control"  name="oficina_servicio" id="oficina_servicio" data-style="btn btn-warning"  required>
                                <!--<option disabled selected="selected" value="">Cliente</option>-->
                                <?php
                                 $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  //$tipoX=$row['tipo'];
                                  $abrevX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$abrevX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div> 
                        </div>
                       </div>
                      </div><!--row-->
                      <div class="" id="lista_precios">
                      </div>
                      
                      <div id="productos_div" class="d-none">
                        <div class="row">
                       <label class="col-sm-2 col-form-label">Regi&oacute;n</label>
                       <div class="col-sm-7">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control" name="region_cliente" id="region_cliente" data-style="btn btn-info"  required>
                                <?php
                                 $stmt = $dbh->prepare("SELECT c.codigo, c.descripcion FROM tipos_clientenacionalidad c order by 1");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['descripcion'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div> 
                        </div>
                       </div>
                      </div><!--row-->
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Tipo Cliente</label>
                       <div class="col-sm-7">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control" name="tipo_cliente" id="tipo_cliente" data-style="btn btn-warning"  required>
                                <?php
                                 $stmt = $dbh->prepare("SELECT c.codigo, c.nombre FROM tipos_clientes c order by 1");
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
                        </div>
                       </div>
                      </div><!--row-->
                      <div class="row">
                       <label class="col-sm-2 col-form-label">IAF</label>
                       <div class="col-sm-3">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control" data-size="4" data-live-search-placeholder="Buscar codigo IAF..." data-live-search="true" name="iaf_primario" id="iaf_primario" data-style="btn btn-info"  required>
                                  <option value="0" select>NINGUNO</option> 
                                <?php
                                 $stmt = $dbh->prepare("SELECT c.codigo, c.nombre,c.abreviatura FROM iaf c order by 1");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abreviaturaX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$abreviaturaX?> - <?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div> 
                        </div>
                       </div>
                       <label class="col-sm-1 col-form-label">IAF Sec.</label>
                       <div class="col-sm-3">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control" data-size="4" data-live-search-placeholder="Buscar codigo IAF..." data-live-search="true" name="iaf_secundario" id="iaf_secundario" data-style="btn btn-default"  required>
                                 <option value="0" select>NINGUNO</option> 
                                <?php
                                 $stmt = $dbh->prepare("SELECT c.codigo, c.nombre,c.abreviatura FROM iaf c order by 1");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abreviaturaX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$abreviaturaX?> - <?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div> 
                        </div>
                       </div>
                      </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Descripción del Servicio:</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <input class="form-control" type="text" name="d_servicio_p" id="d_servicio_p" value="<?=strtoupper(obtenerServiciosClaServicioTipoNombre(309))?>">
                        </div>
                        </div>
                      </div>
                      <!--row-->
                       <!--<div class="row">
                       <label class="col-sm-2 col-form-label">Alcance de la Propuesta:</label>
                       <div class="col-sm-7">
                        <div class="form-group">-->
                        
                        <!--</div>
                        </div>
                      </div>-->
                      <br>
                      <div class="row">
                       <label class="col-sm-1 col-form-label">Productos</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <!--<input type="text" value="" class="form-control tagsinput" name="productos" id="productos" data-role="tagsinput" required data-color="warning">-->
                          <div id="divResultadoListaAtributosProd">
                            <div class="">
                              <center><h4><b>SIN REGISTROS</b></h4></center>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-2">
                           <button title="Agregar Sitio" type="button" name="add" class="btn btn-warning btn-round btn-fab btn-sm" onClick="agregarAtributoAjax()"><i class="material-icons">add</i>
                            </button>
                            <a  title="Pegar Datos Excel" href="#" onclick="modalPegarDatosComprobante()" class="btn btn-primary btn-fab btn-sm">
                              <i class="material-icons">content_paste</i>
                            </a>
                            <a  title="Actualizar Datos Cliente" href="#" onclick="modalActualizarDatosCliente('39')" class="btn btn-success btn-fab btn-sm">
                              <i class="material-icons">replay_circle_filled</i>
                            </a>
                        </div>
                    </div>
                    
                      </div>
                      <div id="sitios_div" class="d-none">
                      
                     <div class="row">
                       <label class="col-sm-2 col-form-label">Objeto del Servicio</label>
                       <div class="col-sm-7">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control" onchange="ponerDescripcionServicio()" name="objeto_servicio" id="objeto_servicio" data-style="btn btn-info"  required>
                                <?php
                                $tituloObjeto="";
                                 $stmt = $dbh->prepare("SELECT c.codigo, c.nombre FROM objeto_servicio c where c.cod_estadoreferencial=1 order by 1");
                                 $stmt->execute();
                                 $indexOb=0;
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  if($indexOb==0){
                                      $tituloObjeto=obtenerServiciosTipoObjetoNombre($codigoX);
                                  }
                                  $indexOb++;
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div> 
                        </div>
                       </div>
                      </div><!--row-->
                     <div class="row">
                       <label class="col-sm-2 col-form-label">Tipo del Servicio</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <select class="selectpicker form-control" data-size="6" data-live-search="true" name="tipo_servicio" id="tipo_servicio" data-style="btn btn-info"  required onchange="ponerSistemasIntegrados();ponerDescripcionServicio();">       
                                <?php
                                $tituloTipoServ="";
                                $indexOb=0;
                                 $stmt = $dbh->prepare("SELECT DISTINCT codigo_n2,descripcion_n2 from cla_servicios where codigo_n1=109 and vigente=1 order by 2");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo_n2'];
                                  $nombreX=$row['descripcion_n2'];
                                  if($indexOb==0){
                                      $tituloTipoServ=obtenerServiciosClaServicioTipoNombre($codigoX);
                                  }
                                  $indexOb++;
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                            </select>
                        </div>
                        </div>
                     </div>
                     <div class="row d-none" id="div_normastipo">
                       <label class="col-sm-2 col-form-label">Normas</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <select class="selectpicker form-control" data-size="4" data-live-search="true" multiple name="normas_tiposervicio[]" id="normas_tiposervicio" data-style="btn btn-success"  required>       
                                <?php
                                 $stmt = $dbh->prepare("SELECT codigo,abreviatura from normas where cod_estado=1 order by 2");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                            </select>
                        </div>
                        </div>
                     </div>
                     <div class="row d-none" id="div_normastipotexto">
                          <label class="col-sm-2 col-form-label">Otras Normas</label>
                           <div class="col-sm-9">                     
                             <div class="form-group" style="border-bottom: 1px solid #CACFD2">       
                                <input type="text" class="form-control tagsinput" data-role="tagsinput" data-color="info" name="normas_tiposerviciotext" id="normas_tiposerviciotext" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                             </div>
                           </div>  
                      </div>
                     <div class="row">
                     <label class="col-sm-2 col-form-label">AFNOR</label>
                           <div class="col-sm-7">
                        <div class="form-group">
                                <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" id="afnor" name="afnor[]" value="1">
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>  
                             </div>     
                        </div> 
                      <div class="row">
                       <label class="col-sm-2 col-form-label">IAF</label>
                       <div class="col-sm-3">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control" data-size="4" data-live-search-placeholder="Buscar codigo IAF..." data-live-search="true" name="iaf_primario_tcs" id="iaf_primario_tcs" data-style="btn btn-info"  required>
                                  <option value="0" select>NINGUNO</option> 
                                <?php
                                 $stmt = $dbh->prepare("SELECT c.codigo, c.nombre,c.abreviatura FROM iaf c order by 1");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abreviaturaX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$abreviaturaX?> - <?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div> 
                        </div>
                       </div>
                       <label class="col-sm-1 col-form-label">IAF Sec.</label>
                       <div class="col-sm-3">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control" data-size="4" data-live-search-placeholder="Buscar codigo IAF..." data-live-search="true" name="iaf_secundario_tcs" id="iaf_secundario_tcs" data-style="btn btn-default"  required>
                                 <option value="0" select>NINGUNO</option> 
                                <?php
                                 $stmt = $dbh->prepare("SELECT c.codigo, c.nombre,c.abreviatura FROM iaf c order by 1");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abreviaturaX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$abreviaturaX?> - <?=$nombreX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                              </div>
                          </div> 
                        </div>
                       </div>
                      </div>  
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Descripción del Servicio:</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <input class="form-control" type="text" name="d_servicio" id="d_servicio" value="<?=$tituloTipoServ?> - <?=$tituloObjeto?>">
                        </div>
                        </div>
                      </div>  
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Alcance de la Propuesta:</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <textarea class="form-control" type="text" name="alcance" id="alcance"></textarea>
                        </div>
                        </div>
                      </div>
                     <br>
                     <div class="row">
                       <label class="col-sm-1 col-form-label">Sitios</label>
                       <div class="col-sm-9">
                        <div class="form-group">
                          <!--<input type="readonly" value="" class="form-control tagsinput" name="sitios" id="sitios" data-role="tagsinput" required data-color="success">-->
                          <div id="divResultadoListaAtributos">
                            <div class="">
                              <center><h4><b>SIN REGISTROS</b></h4></center>
                            </div>
                          </div>
                        </div>
                        </div>
                        <div class="col-sm-2">
                          <button title="Agregar Sitio" type="button" name="add" class="btn btn-warning btn-round btn-fab btn-sm" onClick="agregarAtributoAjax()"><i class="material-icons">add</i>
                          </button>
                          <a title="Pegar Datos Excel" href="#" onclick="modalPegarDatosComprobante_tcs()" class="btn btn-primary btn-fab btn-sm">
                            <i class="material-icons">content_paste</i>
                          </a>
                          <a title="Actualizar Datos Cliente" href="#" onclick="modalActualizarDatosCliente('38')" class="btn success-success btn-fab btn-sm">
                            <i class="material-icons">replay_circle_filled</i>
                          </a>
                        </div>       
                     </div>
                     
                    </div>
                      
                      

                      
                      <!--<div class="row">
                       <label class="col-sm-2 col-form-label">Norma</label>
                       <div class="col-sm-7">
                        <div class="form-group">-->
                          <input class="form-control" type="hidden" name="norma" id="norma" required value="NINGUNA" autocomplete="off"/>
                        <!--</div>
                        </div>
                      </div>-->
                      <!--<div class="row">
                       <label class="col-sm-2 col-form-label">Regi&oacute;n</label>
                       <div class="col-sm-7">
                         <div class="form-group">
                           <select class="form-control selectpicker" data-style="btn btn-info" name="local_extranjero" id="local_extranjero">
                                 <option value="1" selected>BOLIVIA</option>
                                 <option value="0">EXTRANJERO</option>        
                                </select>
                         </div>
                        </div>
                     </div>-->  
        </div>
        <br>
        <div id="mensaje"></div>
        <div class="card-footer  ml-auto mr-auto">
        <?php 
          if(isset($_GET['q'])){
            ?><button type="button" class="<?=$buttonNormal;?>" onclick="guardarSimulacionServicio()">Guardar</button>
              <a href="<?=$urlList?>&q=<?=$q?>&s=<?=$s?>&u=<?=$u?>" class="<?=$buttonCancel;?>">Volver</a><?php
          }else{
            ?><button type="button" class="<?=$buttonNormal;?>" onclick="guardarSimulacionServicio()">Guardar</button>
              <a href="<?=$urlList?>" class="<?=$buttonCancel;?>">Volver</a><?php
          }
        ?>
        </div>
      </div>
    </div>
      
  </div>
</div> 

<!-- small modal -->
<div class="modal fade modal-primary" id="modal_atributo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
               <div class="card-header card-header-primary card-header-text">
                  <div class="card-text">
                    <h4 id="titulo_modal_atributo"></h4>
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
                <div class="card-body">
                       <input type="hidden" class="form-control" name="modal_fila" id="modal_fila" value="-1">
                      <div class="row">
                          <label class="col-sm-2 col-form-label" id="lbl_nombre_atributo">Nombre</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="text" class="form-control" name="modal_nombre" id="modal_nombre" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                             </div>
                           </div> 
                           <div class="row col-sm-6" id="div_marca">
                             <label class="col-sm-2 col-form-label">Marca</label>
                             <div class="col-sm-10">                     
                              <div class="form-group">
                               <input type="text" class="form-control" name="modal_marca" id="modal_marca" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                               </div>
                             </div>  
                           </div> 
                      </div>
                      <div id="div_norma">
                        <div class="row">
                           <label class="col-sm-2 col-form-label">Nº Sello</label>
                           <div class="col-sm-4">                     
                             <div class="form-group">
                               <input type="number" class="form-control" name="modal_sello" id="modal_sello" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                             </div>
                           </div>  
                      </div>
                      <div class="row">
                          <label class="col-sm-2 col-form-label">Norma</label>
                           <div class="col-sm-9">                     
                             <div class="form-group"><!--style="border-bottom: 1px solid #CACFD2"-->          
                               <!--<input type="text" class="form-control tagsinput" data-role="tagsinput" data-color="info" name="modal_norma" id="modal_norma" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">-->
                               <select class="selectpicker form-control form-control-sm" name="normas[]" id="normas" multiple data-style="btn btn-warning" data-live-search="true" data-size="6" data-actions-box="true" required>
                                <?php
                                 $stmt = $dbh->prepare("SELECT * from normas where cod_estado=1 order by abreviatura");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombre'];
                                  $abrevX=$row['abreviatura'];
                                   ?>
                                  <option value="<?=$codigoX;?>"><?=$abrevX;?></option> 
                                  <?php
                                    }
                                    ?>
                                </select>
                             </div>
                           </div>  
                      </div>
                       <div class="row">
                          <label class="col-sm-2 col-form-label">Otra Norma</label>
                           <div class="col-sm-9">                     
                             <div class="form-group" style="border-bottom: 1px solid #CACFD2">       
                                <input type="text" class="form-control tagsinput" data-role="tagsinput" data-color="info" name="modal_norma" id="modal_norma" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                             </div>
                           </div>  
                      </div>    
                      </div>
                      
                     <div class="row col-sm-12" id="div_pais">
                          <div class="row col-sm-12">
                       <label class="col-sm-2 col-form-label">Pais</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <select name="pais_empresa" id="pais_empresa" data-live-search="true" onchange="seleccionarDepartamentoServicioSitio(0)" class="form-control form-control-sm selectpicker" data-style="btn btn-info">
                            <option disabled selected value="####">--SELECCIONE--</option>
                             <?php
                                  foreach ($lista->lista as $listas) {
                                      echo "<option value='".$listas->idPais."####".strtoupper($listas->paisNombre)."'>".$listas->paisNombre."</opction>";
                                  }?>
                          </select>
                        </div>
                       </div>
                      </div>
                      <div class="row col-sm-12">
                       <label class="col-sm-2 col-form-label">Dep / Est</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <select name="departamento_empresa" data-live-search="true" onchange="seleccionarCiudadServicioSitio()" id="departamento_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-info">
                          </select>
                        </div>
                       </div>
                      </div>
                      <div class="row col-sm-12">
                       <label class="col-sm-2 col-form-label">Ciudad</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <select name="ciudad_empresa" data-live-search="true" onchange="mostrarOtraCiudadServicioSitio()" id="ciudad_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-success">
                          </select>
                        </div>
                       </div>
                      </div>  
                      </div>
                      <div class="row" id="div_direccion">
                          <label class="col-sm-2 col-form-label">Direcci&oacute;n</label>
                           <div class="col-sm-10">                     
                             <div class="form-group">
                               <input type="text" class="form-control" name="modal_direccion" id="modal_direccion" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                             </div>
                           </div>  
                      </div>
                      <hr>
                      <div class="form-group float-right">
                        <button type="button" id="boton_guardarsim" class="btn btn-default" onclick="guardarAtributoItem()">Guardar</button>
                      </div> 
                </div>
      </div>  
    </div>
</div>
<!--    end small modal -->

<div class="modal fade modal-arriba" id="modalPegarDatosComprobante" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-primary card-header-text">
        <div class="card-text">
          <h4>TCP > Pegar Datos - Excel</h4>      
        </div>
        
        <button title="Cerrar" type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">         
        <div class="row">
          <label class="col-sm-2 col-form-label">Excel Formato .xlsx</label>
          <div class="col-sm-7">
            <input class="form-control" type="file" name="archivo_tcp" id="archivo_tcp" accept=".xls,.xlsx" required="true" />
            <div id="contenedor_oculto" class="d-none">
              
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
            <a href="#" class="btn btn-primary btn-round" id="boton_cargar_datos" onclick="cargarDatosExel_tcp();return false;"><span id='texto_boton'>CARGAR DATOS</span></a>

            <!-- <a href="#" class="btn btn-default btn-round" onclick="limpiarComprobanteExcel()">Limpiar Datos</a> -->
      </div>
      <hr>
      <div id="div_datos_excel"></div>
    </div>
  </div>
</div>


<div class="modal fade modal-arriba" id="modalPegarDatosComprobante_tcs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-primary card-header-text">
        <div class="card-text">
          <h4>TCS > Pegar Datos - Excel</h4>      
        </div>
        <button title="Cerrar" type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>
      <div class="card-body">         
        <div id="contenedor_oculto_tcs" class="d-none"></div>
        <div class="row">
        <label class="col-sm-2 col-form-label">Excel Formato .xlsx</label>
        <div class="col-sm-7">
          <input class="form-control" type="file" name="archivo_tcs" id="archivo_tcs" accept=".xls,.xlsx" required="true" />
          <div id="contenedor_oculto" class="d-none">
          </div>
        </div>
        </div>
      </div>

      <div class="modal-footer">
            <a href="#" class="btn btn-primary btn-round" id="boton_cargar_datos_tcs" onclick="cargarDatosExel_tcs()">Cargar Datos</a>
            <!-- <a href="#" class="btn btn-success btn-round d-none" id="boton_generar_filas_tcs" onclick="generarComprobanteExcel_TCS()">Generar Filas</a>
            <a href="#" class="btn btn-default btn-round" onclick="limpiarComprobanteExcel()">Limpiar Datos</a> -->
      </div>
      <hr>
      <div id="div_datos_excel_tcs"></div>
    </div>
  </div>
</div>



<!-- small modal -->
<div class="modal fade modal-primary" id="modal_actualizar_cliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
     <div class="card-header card-header-primary card-header-text">
        <div class="card-text">
          <div id="contenedor_oculto_actualizar" class="d-none">
          <h4>Actualizar Datos Cliente</h4>
        </div>
        <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
          <i class="material-icons">close</i>
        </button>
      </div>

      <div class="card-body">                       
          <div class="row">
            <label class="col-sm-6 "><center><span style="text-align: center;color:red;">Datos Actuales</span></center></label>             
            <label class="col-sm-6 "><center><span style="text-align: center;color:green;">Datos a Actualizar</span></center></label>             
            <input type="hidden" name="cod_area_contacto" id="cod_area_contacto" value="0">
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Nit</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="nit_cliente" id="nit_cliente" value="">
               </div>
             </div>                
             <label class="col-sm-2 col-form-label" >Nit</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="nit_cliente_actualizar" id="nit_cliente_actualizar" value="">
               </div>
             </div>                
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Razón Social</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="razon_social_cliente" id="razon_social_cliente" value="">
               </div>
            </div>
            <label class="col-sm-2 col-form-label" >Razón Social</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="razon_social_cliente_actualizar" id="razon_social_cliente_actualizar" value="">
               </div>
            </div>                
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Dirección</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="direccion_cliente" id="direccion_cliente" value="">
               </div>
             </div>
             <label class="col-sm-2 col-form-label" >Dirección</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="direccion_cliente_actualizar" id="direccion_cliente_actualizar" value="">
               </div>
             </div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Pais</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="pais_cliente" id="pais_cliente" value="">
               </div>
             </div>               
             <label class="col-sm-2 col-form-label" >Pais</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="pais_cliente_actualizar" id="pais_cliente_actualizar" value="">
               </div>
             </div>                
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Ciudad</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="ciudad_cliente" id="ciudad_cliente" value="">
               </div>
             </div>               
             <label class="col-sm-2 col-form-label" >Ciudad</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="ciudad_cliente_actualizar" id="ciudad_cliente_actualizar" value="">
               </div>
             </div>                
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Departamento</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="departamento_cliente" id="departamento_cliente" value="">
               </div>
             </div>                
             <label class="col-sm-2 col-form-label" >Departamento</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="departamento_cliente_actualizar" id="departamento_cliente_actualizar" value="">
               </div>
             </div>                
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Telefono</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="telefono_cliente" id="telefono_cliente" value="">
               </div>
             </div>                
             <label class="col-sm-2 col-form-label" >Telefono</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="telefono_cliente_actualizar" id="telefono_cliente_actualizar" value="">
               </div>
             </div>                
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Fax</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="fax_cliente" id="fax_cliente" value="">
               </div>
             </div>                
             <label class="col-sm-2 col-form-label" >Fax</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="fax_cliente_actualizar" id="fax_cliente_actualizar" value="">
               </div>
             </div>                
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >E-mail</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="email_cliente" id="email_cliente" value="">
               </div>
             </div>                
             <label class="col-sm-2 col-form-label" >E-mail</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="email_cliente_actualizar" id="email_cliente_actualizar" value="">
               </div>
             </div>                
          </div>

          <div class="row">
            <label class="col-sm-2 col-form-label" >Pagina Web</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="web_cliente" id="web_cliente" value="">
               </div>
             </div>                
             <label class="col-sm-2 col-form-label" >Pagina Web</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="web_cliente_actualizar" id="web_cliente_actualizar" value="">
               </div>
             </div>                
          </div>

           <div class="row">
             <div class="col-sm-8">                     
               <div class="form-group">
               </div>
             </div>                
             <div class="col-sm-4">                     
               <div class="form-group">
                 <button type="button" id="boton_guardarDatosCliente" class="btn btn-success btn-sm" onclick="modalActualizarDatosCliente_enviar('1')">Actualizar Datos Cliente</button>
               </div>
             </div>                
          </div>

          
          <div class="row">
            <input type="hidden" name="id_contacto_mae" id="id_contacto_mae" value="0">
            <label class="col-sm-12 col-form-label"><center><span style="text-align: center;color:blue;">Máxima Autoridad Ejecutiva</span></center></label>
          </div>

          <div class="row">
            <label class="col-sm-2 col-form-label" >Nombre</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="mae_nombre" id="mae_nombre" value="">
               </div>
             </div>
             <label class="col-sm-2 col-form-label" >Nombre</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="mae_nombre_actualizar" id="mae_nombre_actualizar" value="">
               </div>
             </div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Cargo</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="mae_cargo" id="mae_cargo" value="">
               </div>
             </div>
             <label class="col-sm-2 col-form-label" >Cargo</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="mae_cargo_actualizar" id="mae_cargo_actualizar" value="">
               </div>
             </div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Telefono</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="mae_telefono" id="mae_telefono" value="">
               </div>
             </div>
             <label class="col-sm-2 col-form-label" >Telefono</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="mae_telefono_actualizar" id="mae_telefono_actualizar" value="">
               </div>
             </div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label">E-mail</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="mae_email" id="mae_email" value="">
               </div>
             </div>
             <label class="col-sm-2 col-form-label">E-mail</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="mae_email_actualizar" id="mae_email_actualizar" value="">
               </div>
             </div>
          </div>
          <div class="row">
             <div class="col-sm-8">                     
               <div class="form-group">
               </div>
             </div>                
             <div class="col-sm-4">                     
               <div class="form-group">
                 <button type="button" id="boton_guardarDatosCliente" class="btn btn-success btn-sm" onclick="modalActualizarDatosCliente_enviar('2')">Actualizar Datos MAE</button>
               </div>
             </div>                
          </div>
          <div class="row">
            <label class="col-sm-12 col-form-label"><center><span style="text-align: center;color:blue;">Contacto</span></center></label>
          </div>


          <div class="row">
            <label class="col-sm-2 col-form-label" >Contactos</label>
             <div class="col-sm-4">
              <input type="text" hidden="true" class="form-control" name="id_contacto" id="id_contacto" value="0">
              <div class="form-group" id="contenedor_contactos_cliente">
                
               </div>
             </div>
            
        </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Nombre</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="contacto_nombre" id="contacto_nombre" value="">
               </div>
             </div>
             <label class="col-sm-2 col-form-label" >Nombre</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="contacto_nombre_actualizar" id="contacto_nombre_actualizar" value="">
               </div>
             </div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Cargo</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="contacto_cargo" id="contacto_cargo" value="">
               </div>
             </div>
             <label class="col-sm-2 col-form-label" >Cargo</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="contacto_cargo_actualizar" id="contacto_cargo_actualizar" value="">
               </div>
             </div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label" >Telefono</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="contacto_telefono" id="contacto_telefono" value="">
               </div>
             </div>
             <label class="col-sm-2 col-form-label" >Telefono</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="contacto_telefono_actualizar" id="contacto_telefono_actualizar" value="">
               </div>
             </div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label">E-mail</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="contacto_email" id="contacto_email" value="">
               </div>
             </div>
             <label class="col-sm-2 col-form-label">E-mail</label>
             <div class="col-sm-4">                     
               <div class="form-group">
                 <input type="text" readonly class="form-control" name="contacto_email_actualizar" id="contacto_email_actualizar" value="">
               </div>
             </div>
          </div>
            <div class="row">
             <div class="col-sm-8">                     
               <div class="form-group">
               </div>
             </div>                
             <div class="col-sm-4">                     
               <div class="form-group">
                 <button type="button" id="boton_guardarDatosCliente" class="btn btn-success btn-sm" onclick="modalActualizarDatosCliente_enviar('3')">Actualizar Datos Contacto</button>
               </div>
             </div>                
          </div>
          <hr>
          <div class="form-group float-right">
            <button type="button" id="boton_guardarsim" class="btn btn-warning btn-sm" onclick="modalActualizarDatosCliente_enviar('0')">Actualizar TODO</button>
          </div> 
      </div>
      <div id="contenedor_oculto_actualizarCliente"></div>
    </div>  
  </div>
</div>
<!--    end small modal -->