<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

setlocale(LC_TIME, "Spanish");

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

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
    ?>
    <!--input type="hidden" name="idPerfil" id="idPerfil" value="<?=$u?>"/-->
  <?php
  }
}else{
  $nombreInputPropuesta="";
  $q=$globalUser;
}

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

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

$fechaActualInput=date("Y-m-d");

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
                       <label class="col-sm-2 col-form-label"><span class="text-danger">*</span> Personal</label>
                       <div class="col-sm-6">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group" id="lista_personal">
                                <select class="selectpicker form-control" data-size="4" data-live-search-placeholder="Seleccionar usuario que registra..." data-live-search="true" name="codigo_personal" id="codigo_personal" data-style="btn btn-info"  required>
                                <option value="0">-- -- --</option>
                                <?php
                                 $stmt = $dbh->prepare("SELECT p.codigo, concat(p.paterno,' ',p.materno,' ',p.primer_nombre)as nombrepersona FROM personal p where p.cod_estadopersonal=1 order by 2");
                                 $stmt->execute();
                                  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                  $codigoX=$row['codigo'];
                                  $nombreX=$row['nombrepersona'];
                                   ?>
                                  <option value="<?=$codigoX;?>" <?=($codigoX==$q)?"selected":""?> ><?=$nombreX;?></option> 
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
                       <label class="col-sm-2 col-form-label"><span class="text-danger">*</span> Cliente</label>
                       <div class="col-sm-6">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group" id="lista_clientes">
                                <select class="selectpicker form-control" data-size="4" data-live-search-placeholder="Buscar cliente..." data-live-search="true" name="cliente" id="cliente" data-style="btn btn-info"  required>
          
                                <!--<option disabled selected="selected" value="">Cliente</option>-->
                                <?php
                                 $stmt = $dbh->prepare("SELECT c.codigo, c.nombre 
                                                        FROM clientes c 
                                                        WHERE c.cod_estadoreferencial=1 
                                                        AND c.nombre IS NOT NULL AND c.nombre != '' 
                                                        ORDER BY 2");
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
                      
                  <div class="row" hidden>
                    <label class="col-sm-2 col-form-label">Fecha Solicitud Cliente:</label>
                    <div class="col-sm-5">
                     <div class="form-group">
                        <input class="form-control" type="date" id="fecha_solicitud_cliente" name="fecha_solicitud_cliente">  
                     </div>
                    </div>
                  </div>

                  <div class="row">
                       <label class="col-sm-2 col-form-label"><span class="text-danger">*</span> Plantilla de Servicios :</label>
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
                                    <!--option value="1000000">TCP - BPM (Caso Especial) </option--> 
                                </select>
                              </div>
                        </div>
                      </div>
                      
                    <div class="row">
                        <label class="col-sm-2 col-form-label"><span class="text-danger">*</span> Organismo Certificador :</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select class="selectpicker form-control form-control-sm" name="organismo_certificador[]" id="organismo_certificador" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
                                    <?php
                                        $stmt = $dbh->prepare("SELECT oc.codigo, oc.nombre, oc.abreviatura FROM organismo_certificador oc order by 1");
                                        $stmt->execute();
                                        $cont_certificador = 1;
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                          $codigoX=$row['codigo'];
                                          $nombreX=$row['nombre'];
                                          $abreviaturaX=$row['abreviatura'];
                                    ?>
                                        <option value="<?=$codigoX;?>" <?=($cont_certificador)?'selected':'';?>><?=$abreviaturaX?> - <?=$nombreX;?></option> 
                                    <?php
                                          $cont_certificador = 0;
                                        }
                                    ?>
                                </select>
                              </div>
                        </div>
                    </div>

                      <div class="row">
                       <label class="col-sm-2 col-form-label"><span class="text-danger">*</span> Oficina de Servicio</label>
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

                   <!-- <div id="tiposervicio_div" class="d-none"> -->
                    <div class="row">
                      <label class="col-sm-2 col-form-label"><span class="text-danger">*</span> Tipo del Servicio</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <select class="selectpicker form-control" data-size="6" data-live-search="true" name="tipo_servicio" id="tipo_servicio" data-style="btn btn-info"  required onchange="ponerSistemasIntegrados();ponerDescripcionServicio();searchServicio();excepcionTipoServicio(this.value);">
                          </select>
                        </div>
                      </div>
                    </div>
                    <!-- </div> -->
                    
                    <!-- Nuevo campo adicionado: Servicio -->
                    <div class="row">
                      <label class="col-sm-2 col-form-label"><span class="text-danger">*</span> Servicio</label>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <select class="selectpicker form-control" name="cod_servicio[]" id="cod_servicio" multiple data-style="btn btn-success" data-actions-box="true" data-live-search="true" data-size="6" required>
                          </select>
                        </div>
                      </div>
                    </div>

                    <!-- Nuevo campos de NORMAS -->
                    <div class="row seleccion_normas" style="display:none;">
                        <label class="col-sm-2 col-form-label">Normas Nacionales:</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select class="selectpicker form-control" name="normas_nac[]" id="normas_nac" multiple data-style="btn btn-warning" data-actions-box="true" data-live-search="true" data-size="6" required>
                                <?php
                                    $stmt = $dbh->prepare("SELECT vn.codigo, vn.abreviatura, vn.nombre, 'L' as tipo from v_normas vn where vn.cod_estado=1 order by 4,2");
                                    $stmt->execute();
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $codigoX    = $row['codigo'];
                                        $nombreX    = $row['nombre'];
                                        $tipoX      = $row['tipo'];
                                        $abrevX     = $row['abreviatura']." (".$tipoX.")";
                                        $nombreX    = substr($nombreX, 0, 70);
                                ?>
                                <option value="<?=$codigoX;?>" data-subtext="<?=$nombreX;?>"><?=$abrevX;?></option> 
                                <?php
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row seleccion_normas" style="display:none;">
                        <label class="col-sm-2 col-form-label">Normas Internacionales:</label>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select class="selectpicker form-control" name="normas_int[]" id="normas_int" multiple data-style="btn btn-warning" data-actions-box="true" data-live-search="true" data-size="6" required>
                                <?php
                                    $stmt = $dbh->prepare("SELECT vi.codigo, vi.abreviatura, vi.nombre, 'I' as tipo from v_normas_int vi where vi.cod_estado=1 order by 4,2");
                                    $stmt->execute();
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        $codigoX    = $row['codigo'];
                                        $nombreX    = $row['nombre'];
                                        $tipoX      = $row['tipo'];
                                        $abrevX     = $row['abreviatura']." (".$tipoX.")";
                                        $nombreX    = substr($nombreX, 0, 70);
                                ?>
                                <option value="<?=$codigoX;?>" data-subtext="<?=$nombreX;?>"><?=$abrevX;?></option> 
                                <?php
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row seleccion_normas" style="display:none;">
                        <label class="col-sm-2 col-form-label">Otras Normas</label>
                        <div class="col-sm-9">                     
                            <div class="form-group" style="border-bottom: 1px solid #CACFD2">       
                                <input type="text" class="form-control tagsinput" data-role="tagsinput" data-color="info" name="normas_tiposerviciotext" id="normas_tiposerviciotext" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            </div>
                        </div>  
                    </div>
                    <!-- FIN -->



                      <div class="" id="lista_precios">
                      </div>

                    <!-- SE QUITO LOS CAMPOS -->
                      <!-- <div id="productos_div" class="d-none"> -->
                      <div id="productos_div" class="d-none">
                        <div class="row" hidden>
                       <label class="col-sm-2 col-form-label">Regi&oacute;n</label>
                       <div class="col-sm-7">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control" name="region_cliente" id="region_cliente" data-style="btn btn-info"  required>
                                    <option value="">Ninguno</option>
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
                      <div class="row" hidden>
                       <label class="col-sm-2 col-form-label">Tipo Cliente</label>
                       <div class="col-sm-7">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control" name="tipo_cliente" id="tipo_cliente" data-style="btn btn-warning"  required>
                                    <option value="">Ninguno</option>
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
                      </div>
                    <!-- FIN - SE QUITO LOS CAMPOS -->
                      <div class="row">
                       <label class="col-sm-2 col-form-label">IAF</label>
                       <div class="col-sm-3">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                              
                                <select class="selectpicker form-control form-control-sm" name="iaf_primario[]" id="iaf_primario" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
                                  <option value="0" select>NINGUNO</option> 
                                <?php
                                  $sql = "SELECT c.codigo, c.nombre,c.abreviatura 
                                            FROM iaf c 
                                            WHERE c.Auxiliar = 755
                                            order by 1"; // SQL Antiguo
                                  // $sql = "SELECT c.IdClasificador as codigo, CONCAT(c.Abrev,' - ',c.Descripcion) as nombre
                                  //         FROM ibnorca.clasificador c
                                  //         WHERE c.IdPadre=755 AND c.Aprobado=1"; // SQL NUEVO
                                 $stmt = $dbh->prepare($sql);
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
                       <label class="col-sm-1 col-form-label">Categoria Inocuidad</label>
                       <div class="col-sm-3">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control form-control-sm" data-live-search-placeholder="Categoria inocuidad..." name="iaf_secundario[]" id="iaf_secundario" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
                                 <option value="0" select>NINGUNO</option> 
                                <?php
                                 $sql = "SELECT ci.codigo, ci.nombre FROM categorias_inocuidad ci WHERE ci.estado = 1 order by 1"; // SQL Antiguo
                                //  $sql = "SELECT c.IdClasificador as codigo, c.Descripcion as nombre
                                //        FROM ibnorca.clasificador c
                                //        WHERE c.IdPadre=4868 AND c.Aprobado=1"; // SQL NUEVO
                                $stmt = $dbh->prepare($sql);
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
                      </div>
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Descripción del Servicio:</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <input class="form-control" type="text" name="d_servicio_p" id="d_servicio_p" value="<?=strtoupper(obtenerServiciosClaServicioTipoNombre(309))?>">
                        </div>
                        </div>
                      </div>
                      
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Alcance de la Propuesta:</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <textarea class="form-control" type="text" name="alcance_p" id="alcance_p"></textarea>
                        </div>
                        </div>
                      </div>
                      <!-- SECCIÓN DE PRODUCTOS TRASLADADO AL FINAL -->
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
                                    $sql = "SELECT c.codigo, c.nombre FROM objeto_servicio c where c.cod_estadoreferencial=1 order by 1"; // SQL Antiguo
                                    // $sql = "SELECT c.IdClasificador as codigo, CONCAT(c.Abrev,' - ', c.Descripcion) as nombre
                                    //         FROM ibnorca.clasificador c
                                    //         WHERE c.IdPadre=795 AND c.Aprobado=1"; // SQL NUEVO
                                    $stmt = $dbh->prepare($sql);
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
                                <select class="selectpicker form-control form-control-sm" data-size="4" data-live-search-placeholder="Buscar codigo IAF..." name="iaf_primario_tcs[]" id="iaf_primario_tcs" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
                                  <option value="0" select>NINGUNO</option> 
                                <?php
                                  $sql = "SELECT c.codigo, c.nombre,c.abreviatura 
                                            FROM iaf c 
                                            WHERE c.Auxiliar = 755
                                            order by 1"; // SQL Antiguo
                                  // $sql = "SELECT c.IdClasificador as codigo, CONCAT(c.Abrev,' - ',c.Descripcion) as nombre
                                  //         FROM ibnorca.clasificador c
                                  //         WHERE c.IdPadre=755 AND c.Aprobado=1"; // SQL NUEVO
                                 $stmt = $dbh->prepare($sql);
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
                       <label class="col-sm-1 col-form-label">Categoria Inocuidad</label>
                       <div class="col-sm-3">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="form-group">
                                <select class="selectpicker form-control form-control-sm" data-live-search-placeholder="Categoria inocuidad..." name="iaf_secundario_tcs[]" id="iaf_secundario_tcs" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
                                 <option value="0" select>NINGUNO</option> 
                                <?php
                                  $sql = "SELECT ci.codigo, ci.nombre FROM categorias_inocuidad ci WHERE ci.estado = 1 order by 1"; // SQL Antiguo
                                  // $sql = "SELECT c.IdClasificador as codigo, c.Descripcion as nombre
                                  //       FROM ibnorca.clasificador c
                                  //       WHERE c.IdPadre=4868 AND c.Aprobado=1"; // SQL NUEVO
                                 $stmt = $dbh->prepare($sql);
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
                      </div>  
                      <div class="row">
                       <label class="col-sm-2 col-form-label">Descripción del Servicio:</label>
                       <div class="col-sm-7">
                        <div class="form-group">
                          <input class="form-control" type="text" name="d_servicio" id="d_servicio" value="<?=empty($tituloTipoServ)?'':$tituloTipoServ?> - <?=$tituloObjeto?>">
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
                      <!-- SECCIÓN DE SITIOS TRASLADADO AL FINAL -->
                     
                    </div>
                      
        </div>
        
        <br>
        <div class="card-body">
          <!-- SECCIÓN DE PRODUCTOS -->
          <div class="row d-none seccion_productos pt-0">
            <label class="col-sm-1 col-form-label">Productos</label>
            <div class="col-sm-9">
              <div class="form-group">
                <!--<input type="text" value="" class="form-control tagsinput" name="productos" id="productos" data-role="tagsinput" required data-color="warning">-->
                <div>
                  <table class="table table-bordered table-sm table-striped small" style="font-size: 11px;">
                    <thead>
                      <tr class="bg-info text-white">
                        <th>#</th>
                        <th>NOMBRE</th>
                        <th>DIRECCION</th>
                        <th>MARCA</th>
                        <th>NORMA</th>
                        <th>SELLO</th>
                        <td class="text-right" width="18%">OPCION</td>
                      </tr>
                    </thead>
                    <tbody id="listProducto">
                      <tr><td colspan="7">No existen registros.</td></tr>
                    </tbody>
                  </table>
                </div>

              </div>
            </div>
            <div class="col-sm-2">
                <!-- Versión antigua -->
                <!-- <button title="Agregar Sitio" type="button" name="add" class="btn btn-warning btn-round btn-fab btn-sm" onClick="agregarAtributoAjax()"><i class="material-icons">add</i> -->
              <button title="Agregar Sitio" type="button" name="add" class="btn btn-warning btn-round btn-fab btn-sm" onClick="abreModalItem()"><i class="material-icons">add</i>
              </button>
              <!-- EXCEL -->
              <a  title="Pegar Datos Excel" href="#" onclick="modalPegarDatosComprobante()" class="btn btn-primary btn-fab btn-sm" hidden>
                <i class="material-icons">content_paste</i>
              </a>
              <!-- FIN EXCEL -->
              <a  title="Actualizar Datos Cliente" href="#" onclick="modalActualizarDatosCliente('39')" class="btn btn-success btn-fab btn-sm">
                <i class="material-icons">replay_circle_filled</i>
              </a>
            </div>
          </div>
          <!-- FIN SECCIÓN DE PRODUCTOS -->
          <!-- SECCIÓN DE SITIOS -->
          <br>
          <div class="row d-none seccion_sitios pt-0">
            <label class="col-sm-1 col-form-label">Sitios</label>
            <div class="col-sm-9">
              <div class="form-group">
                <div>
                  <table class="table table-bordered table-sm table-striped small" style="font-size: 11px;">
                    <thead>
                      <tr class="bg-info text-white">
                        <th>#</th>
                        <th>NOMBRE</th>
                        <th>DIRECCION</th>
                        <th>PROCESO</th>
                        <td class="text-right" width="18%">OPCION</td>
                      </tr>
                    </thead>
                    <tbody id="listSitio">
                      <tr><td colspan="5">No existen registros.</td></tr>
                    </tbody>
                  </table>
                </div>

              </div>
            </div>
            <div class="col-sm-2">
              <button title="Agregar Sitio" type="button" name="add" class="btn btn-warning btn-round btn-fab btn-sm" onClick="abreModalItem()"><i class="material-icons">add</i>
              </button>
              <!-- EXCEL -->
              <a title="Pegar Datos Excel" href="#" onclick="modalPegarDatosComprobante_tcs()" class="btn btn-primary btn-fab btn-sm" hidden>
                <i class="material-icons">content_paste</i>
              </a>
              <!-- FIN EXCEL -->
              <a title="Actualizar Datos Cliente" href="#" onclick="modalActualizarDatosCliente('38')" class="btn success-success btn-fab btn-sm">
                <i class="material-icons">replay_circle_filled</i>
              </a>
            </div>
          </div>
          <!-- FIN SECCIÓN DE SITIOS -->
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
                        
                      <!-- Nuevo campos de NORMAS -->
                      <div class="row">
                          <label class="col-sm-2 col-form-label">Normas Nacionales:</label>
                          <div class="col-sm-7">
                              <div class="form-group">
                                  <select class="selectpicker form-control" name="atr_normas_nac[]" id="atr_normas_nac" multiple data-style="btn btn-warning" data-actions-box="true" data-live-search="true" data-size="6" required>
                                  <?php
                                      $stmt = $dbh->prepare("SELECT vn.codigo, vn.abreviatura, vn.nombre, 'L' as tipo from v_normas vn where vn.cod_estado=1 order by 4,2");
                                      $stmt->execute();
                                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                          $codigoX    = $row['codigo'];
                                          $nombreX    = $row['nombre'];
                                          $tipoX      = $row['tipo'];
                                          $abrevX     = $row['abreviatura']." (".$tipoX.")";
                                          $nombreX    = substr($nombreX, 0, 70);
                                  ?>
                                  <option value="<?=$codigoX;?>" data-subtext="<?=$nombreX;?>"><?=$abrevX;?></option> 
                                  <?php
                                  }
                                  ?>
                                  </select>
                              </div>
                          </div>
                      </div>
                      <div class="row">
                          <label class="col-sm-2 col-form-label">Normas Internacionales:</label>
                          <div class="col-sm-7">
                              <div class="form-group">
                                  <select class="selectpicker form-control" name="atr_normas_int[]" id="atr_normas_int" multiple data-style="btn btn-warning" data-actions-box="true" data-live-search="true" data-size="6" required>
                                  <?php
                                      $stmt = $dbh->prepare("SELECT vi.codigo, vi.abreviatura, vi.nombre, 'I' as tipo from v_normas_int vi where vi.cod_estado=1 order by 4,2");
                                      $stmt->execute();
                                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                          $codigoX    = $row['codigo'];
                                          $nombreX    = $row['nombre'];
                                          $tipoX      = $row['tipo'];
                                          $abrevX     = $row['abreviatura']." (".$tipoX.")";
                                          $nombreX    = substr($nombreX, 0, 70);
                                  ?>
                                  <option value="<?=$codigoX;?>" data-subtext="<?=$nombreX;?>"><?=$abrevX;?></option> 
                                  <?php
                                  }
                                  ?>
                                  </select>
                              </div>
                          </div>
                      </div>
                      <!-- OTRAS NORMAS NO ESTA SIENDO USADA EN EL MODAL DE PRODUCTOS -->
                       <!-- <div class="row">
                          <label class="col-sm-2 col-form-label">Otra Norma</label>
                           <div class="col-sm-9">                     
                             <div class="form-group" style="border-bottom: 1px solid #CACFD2">       
                                <input type="text" class="form-control tagsinput" data-role="tagsinput" data-color="info" name="modal_norma" id="modal_norma" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                             </div>
                           </div>  
                      </div> -->
                      <!-- FIN --> 
                    </div>
                    
                      <!-- ESCONDIDO -->
                      <div class="row" hidden>
                          <label class="col-sm-2 col-form-label">Norma</label>
                           <div class="col-sm-9">                     
                             <div class="form-group">
                                <!--style="border-bottom: 1px solid #CACFD2"-->          
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
                       <div class="row" hidden>
                          <label class="col-sm-2 col-form-label">Otra Norma</label>
                           <div class="col-sm-9">                     
                             <div class="form-group" style="border-bottom: 1px solid #CACFD2">       
                                <input type="text" class="form-control tagsinput" data-role="tagsinput" data-color="info" name="modal_norma" id="modal_norma" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                             </div>
                           </div>  
                      </div>
                      <!-- ESCONDIDO -->
                      
                     <div class="row col-sm-12" hidden id="div_pais">
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
                      <div class="row col-sm-12" hidden>
                       <label class="col-sm-2 col-form-label">Dep / Est</label>
                       <div class="col-sm-10">
                        <div class="form-group">
                          <select name="departamento_empresa" data-live-search="true" onchange="seleccionarCiudadServicioSitio()" id="departamento_empresa" class="form-control form-control-sm selectpicker" data-style="btn btn-info">
                          </select>
                        </div>
                       </div>
                      </div>
                      <div class="row col-sm-12" hidden>
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




<!-- MODAL PARA PRODUCTOS -->
<div class="modal fade modal-primary" id="modal_atributo_producto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card">
            <div class="card-header card-header-primary card-header-text">
                <div class="card-text">
                    <h4 class="card-title">Agregar Producto</h4>
                </div>
                <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <div class="card-body">
                <!-- Nro de registro para edición, caso contrario nuevo registro -->
                <input type="hidden" id="row_producto" value="0">
                <div class="row">
                    <div class="row col-sm-12">
                        <label class="col-sm-2 col-form-label">Producto</label>
                        <div class="col-sm-9">                     
                            <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" name="map_producto" id="map_producto" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            </div>
                        </div>
                    </div> 
                </div>
                <div class="row">
                    <div class="row col-sm-12">
                        <label class="col-sm-2 col-form-label">Marca</label>
                        <div class="col-sm-9">                     
                            <div class="form-group bmd-form-group">
                                <input type="text" class="form-control" name="map_marca" id="map_marca" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            </div>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="row col-sm-12">
                        <label class="col-sm-2 col-form-label">Nº Sello</label>
                        <div class="col-sm-9">                     
                            <div class="form-group bmd-form-group">
                                <input type="number" class="form-control" name="map_sello" id="map_sello" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            </div>
                        </div> 
                    </div>
                </div>
                <!-- NORMAS -->
                <div class="row">
                    <label class="col-sm-2 col-form-label">Normas Nacionales:</label>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <select class="selectpicker form-control" name="map_normas_nac[]" id="map_normas_nac" multiple data-style="btn btn-warning" data-actions-box="true" data-live-search="true" data-size="6" required>
                            <?php
                                $stmt = $dbh->prepare("SELECT vn.codigo, vn.abreviatura, vn.nombre, 'L' as tipo from v_normas vn where vn.cod_estado=1 order by 4,2");
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX    = $row['codigo'];
                                    $nombreX    = $row['nombre'];
                                    $tipoX      = $row['tipo'];
                                    $abrevX     = $row['abreviatura']." (".$tipoX.")";
                                    $nombreX    = substr($nombreX, 0, 70);
                            ?>
                            <option value="<?=$codigoX;?>" data-subtext="<?=$nombreX;?>"><?=$abrevX;?></option> 
                            <?php
                            }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-2 col-form-label">Normas Internacionales:</label>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <select class="selectpicker form-control" name="map_normas_int[]" id="map_normas_int" multiple data-style="btn btn-warning" data-actions-box="true" data-live-search="true" data-size="6" required>
                            <?php
                                $stmt = $dbh->prepare("SELECT vi.codigo, vi.abreviatura, vi.nombre, 'I' as tipo from v_normas_int vi where vi.cod_estado=1 order by 4,2");
                                $stmt->execute();
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX    = $row['codigo'];
                                    $nombreX    = $row['nombre'];
                                    $tipoX      = $row['tipo'];
                                    $abrevX     = $row['abreviatura']." (".$tipoX.")";
                                    $nombreX    = substr($nombreX, 0, 70);
                            ?>
                            <option value="<?=$codigoX;?>" data-subtext="<?=$nombreX;?>"><?=$abrevX;?></option> 
                            <?php
                            }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-2 col-form-label">Dirección</label>
                    <div class="col-sm-9">                     
                        <div class="form-group">
                            <input type="text" class="form-control" name="map_direccion" id="map_direccion" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                    </div>  
                </div>
                <hr>
                <div class="form-group float-right">
                    <button type="button" class="btn btn-default" onclick="agregarProductoPropuesta()">Guardar</button>
                </div> 
            </div>
        </div>
    </div>
</div>

<!-- MODAL PARA SITIOS -->
<div class="modal fade modal-primary" id="modal_atributo_sitio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card">
            <div class="card-header card-header-primary card-header-text">
                <div class="card-text">
                    <h4 id="card-title">Agregar Sitio</h4>
                </div>
                <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                </button>
            </div>
            <div class="card-body">
                <!-- Nro de registro para edición, caso contrario nuevo registro -->
                <input type="hidden" id="row_sitio" value="0">
                <div class="row">
                    <label class="col-sm-2 col-form-label">Nombre</label>
                    <div class="col-sm-9">                     
                        <div class="form-group bmd-form-group">
                            <input type="text" class="form-control" name="mas_nombre" id="mas_nombre" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                    </div>  
                </div>
                <div class="row">
                    <label class="col-sm-2 col-form-label">Dirección</label>
                    <div class="col-sm-9">                     
                        <div class="form-group bmd-form-group">
                            <input type="text" class="form-control" name="mas_direccion" id="mas_direccion" value="" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                    </div>  
                </div>
                <div class="row">
                    <label class="col-sm-2 col-form-label">Proceso</label>
                    <div class="col-sm-9">                     
                        <div class="form-group bmd-form-group">
                            <textarea class="form-control" name="mas_procesos" id="mas_procesos" row="2"></textarea>
                        </div>
                    </div>  
                </div>
                <hr>
                <div class="form-group float-right">
                    <button type="button" id="boton_guardarsim" class="btn btn-default" onclick="agregarSitioPropuesta()">Guardar</button>
                </div> 
            </div>
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
                 <input type="text"  class="form-control" name="nit_cliente_actualizar" id="nit_cliente_actualizar" value="">
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
                 <input type="text"  class="form-control" name="razon_social_cliente_actualizar" id="razon_social_cliente_actualizar" value="" readonly>
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
                 <input type="text"  class="form-control" name="direccion_cliente_actualizar" id="direccion_cliente_actualizar" value="">
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
                 <input type="text"  class="form-control" name="telefono_cliente_actualizar" id="telefono_cliente_actualizar" value="">
               </div>
             </div>                
          </div>
          <input type="hidden" readonly class="form-control" name="fax_cliente" id="fax_cliente" value="">
          <input type="hidden" readonly class="form-control" name="fax_cliente_actualizar" id="fax_cliente_actualizar" value="">
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
                 <input type="text"  class="form-control" name="email_cliente_actualizar" id="email_cliente_actualizar" value="">
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
                 <input type="text"  class="form-control" name="web_cliente_actualizar" id="web_cliente_actualizar" value="">
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
                 <input type="text"  class="form-control" name="mae_nombre_actualizar" id="mae_nombre_actualizar" value="">
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
                 <input type="text"  class="form-control" name="mae_cargo_actualizar" id="mae_cargo_actualizar" value="">
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
                 <input type="text"  class="form-control" name="mae_telefono_actualizar" id="mae_telefono_actualizar" value="">
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
                 <input type="text"  class="form-control" name="mae_email_actualizar" id="mae_email_actualizar" value="">
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
                 <input type="text"  class="form-control" name="contacto_nombre_actualizar" id="contacto_nombre_actualizar" value="">
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
                 <input type="text"  class="form-control" name="contacto_cargo_actualizar" id="contacto_cargo_actualizar" value="">
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
                 <input type="text"  class="form-control" name="contacto_telefono_actualizar" id="contacto_telefono_actualizar" value="">
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
                 <input type="text"  class="form-control" name="contacto_email_actualizar" id="contacto_email_actualizar" value="">
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
