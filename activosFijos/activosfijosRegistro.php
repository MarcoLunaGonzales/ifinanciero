<?php


require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';


$dbh = new Conexion();


$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$globalAdmin=$_SESSION["globalAdmin"];

//------------------------------------------- para combos
$querydepre = "select * from depreciaciones where cod_estado=1 order by 3"; //los rubros
$statementDepre = $dbh->query($querydepre);

// $queryUO = "SELECT u.*, uo.nombre as xuo FROM ubicaciones u, unidades_organizacionales uo WHERE u.cod_unidades_organizacionales = uo.codigo
// order by 3";
// $statementUO = $dbh->query($queryUO);//uo

$query = "select codigo,paterno,materno,primer_nombre from personal where cod_estadoreferencial=1 order by paterno ";
// $statementPersonal = $dbh->query($query);
$statementPersonal2 = $dbh->query($query);

$query_prov = "select * from af_proveedores where cod_estado=1 order by 2";
$statementprov = $dbh->query($query_prov);

$query_tiposbienes = "select * from tiposbienes where cod_estado=1 order by 3";
$statementTIPOSBIENES = $dbh->query($query_tiposbienes);

$query_unidadesOrganizacionales = "select * from unidades_organizacionales where cod_estado=1 order by nombre";
$statementUNIDADESORGANIZACIONALES = $dbh->query($query_unidadesOrganizacionales);

$query_areas = "select * from areas where cod_estado=1 order by 2";
$statementAREAS = $dbh->query($query_areas);

$query_proy_financiacion = "select * from proyectos_financiacionexterna where cod_estadoreferencial=1 order by 2";
$statementProyFinanciacion = $dbh->query($query_proy_financiacion);

$statementTIPOSAF = $dbh->query("select * from tipos_activos_fijos where cod_estadoreferencial=1 order by 2");

//------------------------------------------------------------------- principal
if ($codigo > 0){
    $stmt = $dbh->prepare("SELECT * ,(select d.nombre from depreciaciones d where d.codigo=cod_depreciaciones) as nombreRubro,(select d.tipo_bien from tiposbienes d where d.codigo=cod_tiposbienes) as nombreBien,(select d.abreviatura from unidades_organizacionales d where d.codigo=cod_unidadorganizacional) as nombreUO,(select CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre) from personal p where p.codigo=cod_responsables_responsable) as nombreResponsable FROM activosfijos where codigo =:codigo");
    //Ejecutamos;
    $stmt->bindParam(':codigo',$codigo);
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = $result['codigo'];
    $codigoactivo = $result['codigoactivo'];
    $tipoalta = $result['tipoalta'];
    $fechalta = $result['fechalta'];
    $indiceufv = $result['indiceufv'];
    $tipocambio = $result['tipocambio'];
    $moneda = $result['moneda'];
    $valorinicial = $result['valorinicial'];
    $depreciacionacumulada = $result['depreciacionacumulada'];
    $valorresidual = $result['valorresidual'];
    $cod_depreciaciones = $result['cod_depreciaciones'];
    $cod_tiposbienes = $result['cod_tiposbienes'];
    $vidautilmeses = $result['vidautilmeses'];
    $estadobien = $result['estadobien'];
    $otrodato = $result['otrodato'];
    $cod_ubicaciones = $result['cod_ubicaciones'];
    $cod_empresa = $result['cod_empresa'];
    $activo = $result['activo'];
    $cod_responsables_responsable = $result['cod_responsables_responsable'];
    $cod_responsables_autorizadopor = $result['cod_responsables_autorizadopor'];
    $created_at = $result['created_at'];
    $created_by = $result['created_by'];
    $modified_at = $result['modified_at'];
    $modified_by = $result['modified_by'];
    $vidautilmeses_restante = $result['vidautilmeses_restante'];
    $cod_af_proveedores = $result['cod_af_proveedores'];
    $numerofactura = $result['numerofactura'];

    $cod_unidadorganizacional = $result['cod_unidadorganizacional'];
    $cod_area = $result['cod_area'];
    $tipo_af = $result['tipo_af'];
    $nombreRubro = $result['nombreRubro'];
    $nombreUO = $result['nombreUO'];
    $nombreResponsable = $result['nombreResponsable'];
    $nombreBien = $result['nombreBien'];


    //IMAGEN
    $stmtIMG = $dbh->prepare("SELECT * FROM activosfijosimagen where codigo =:codigo");
    //Ejecutamos;
    $stmtIMG->bindParam(':codigo',$codigo);
    $stmtIMG->execute();
    $resultIMG = $stmtIMG->fetch();
    $imagen = $resultIMG['imagen'];
    //$archivo = __DIR__.DIRECTORY_SEPARATOR."imagenes".DIRECTORY_SEPARATOR.$imagen;//sale mal
    $archivo = "activosfijos/imagenes/".$imagen;//sale mal

    //asignaciones
    $query2 = "SELECT * FROM v_activosfijos_asignaciones where codigo = ".$codigo;
    $statement2 = $dbh->query($query2);

    $variableDisabled="true";
} else {
    //consulta para sacar el codigo del ultimo activoFijo
    $stmt = $dbh->prepare("SELECT max(codigo) AS id FROM activosfijos");
    //Ejecutamos;
    $stmt->execute();
    $result = $stmt->fetch();
    $codigo = 0;

    $codigo_aux = $result['id']+1; 
    
    //$codigoactivo = "AF-".$codigo_aux;
    $codigoactivo = "-";

    $tipoalta = '';
    $fechalta = '';
    $indiceufv = '';
    $tipocambio = '';
    $moneda = '';
    $valorinicial =  '';
    $depreciacionacumulada = '';
    $valorresidual = '';
    $cod_depreciaciones = '';
    $cod_tiposbienes = '';
    $vidautilmeses = '';
    $estadobien  = '';
    $otrodato  = '';
    $cod_ubicaciones  = '';
    $cod_empresa = '';
    $activo = '';
    $cod_responsables_responsable  = '';
    $cod_responsables_autorizadopor  = '';
    $created_at  = '';
    $created_by  = '';
    $modified_at  = '';
    $modified_by  = '';
    $vidautilmeses_restante  = '';
    $cod_af_proveedores  = '';
    $numerofactura = '';

    $archivo = '';

    $cod_unidadorganizacional = '';
    $cod_area = '';
    $variableDisabled="false";
    $tipo_af='';
    $nombreRubro='';
    $nombreUO='';
    $nombreResponsable='';
}
//echo $variableDisabled;
?>
<div class="content">
	<div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
                <form id="form1" class="form-horizontal" action="<?=$urlSave6;?>" method="post"  enctype="multipart/form-data">
        			<div class="card">
                        <div class="card-header <?=$colorCard;?> card-header-text">
                            <div class="card-text">
                              <h4 class="card-title"><?php if ($codigo == 0) echo "Registrar"; else echo "Editar";?> <?=$moduleNameSingular6;?></h4>
                            </div>
                        </div>
                        <div class="card-body ">
                            <input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Codigo Activo</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <div id="divCodigoAF">
                                            <input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="codigoactivo" id="codigoactivo" required="true"  value="<?=$codigoactivo;?>"/>
                                        </div>
                                    </div>
                                </div>
                                <!-- imagen qr-->
                                <div class='col-sm-6'>
                                        <?php
                                            require 'assets/phpqrcode/qrlib.php';
                                            $dir = 'qr_temp/';
                                            if(!file_exists($dir)){
                                                mkdir ($dir);}
                                            $fileName = $dir.'test.png';
                                            $tamanio = 3; //tamaño de imagen que se creará
                                            $level = 'L'; //tipo de precicion Baja L, mediana M, alta Q, maxima H
                                            $frameSize = 1; //marco de qr
                                            $contenido = "Cod:".$codigoactivo."\nRubro:".$nombreRubro."\nDesc:".$activo."\nRespo.:".$nombreUO.' - '.$nombreResponsable;

                                            QRcode::png($contenido, $fileName, $level,$tamanio,$frameSize);
                                            echo '<img src="'.$fileName.'"/>';
                                        ?>
                                </div>

                            </div><!--fin campo codigoactivo -->

                          
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Oficina</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select name="cod_unidadorganizacional" id="cod_unidadorganizacional" onChange="ajaxAFunidadorganizacionalArea(this);" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                                        
                                            <option value=""></option>
                                            <?php 
                                            $queryUO1 = "SELECT codigo,nombre,abreviatura from unidades_organizacionales where cod_estado=1 order by nombre";
                                            $statementUO1 = $dbh->query($queryUO1);
                                            while ($row = $statementUO1->fetch()){ ?>
                                                <option <?=($cod_unidadorganizacional==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>" data-subtext="(<?=$row['codigo']?>)"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <label class="col-sm-2 col-form-label">Area</label>
                                  <div class="col-sm-4">
                                    <div class="form-group" >
                                        <div id="div_contenedor_area">
                                            <?php
                                                if($codigo>0){?>

                                                <?php }else{

                                                }
                                            ?>
                                            <select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" >

                                                <option value=""></option>
                                                <?php 
                                                $queryArea = "SELECT codigo,nombre,abreviatura FROM  areas WHERE cod_estado=1 order by nombre";
                                                $statementArea = $dbh->query($queryArea);
                                                while ($row = $statementArea->fetch()){ ?>
                                                    <option <?=($cod_area==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>" data-subtext="(<?=$row['codigo']?>)"><?=$row["abreviatura"];?> - <?=$row["nombre"];?></option>
                                                <?php } ?>
                                            </select>
                                        </div>                    
                                    </div>
                                </div>
                            </div>   

                            <div class="row">
                                <label class="col-sm-2 col-form-label">Tipo Alta</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                    				<select name="tipoalta" id="tipoalta" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required="true">
                                    <!--<select name="tipoalta" id="tipoalta" class="selectpicker " data-style="select-with-transition">-->
                    					<option <?php if("NUEVO" == $tipoalta) echo "selected"; ?> value="NUEVO">NUEVO</option>
                    					<option <?php if("USADO" == $tipoalta) echo "selected"; ?> value="USADO">USADO</option>
                    				</select>
                                </div>
                                </div><!--fin campo tipoalta -->

                                <label class="col-sm-2 col-form-label">Rubro</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                            		<select name="cod_depreciaciones" id="cod_depreciaciones" onchange="ajaxCodigoActivo(this);" required="true" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true">
                            			<option disabled selected value=""></option>
                                		<?php while ($row = $statementDepre->fetch()){ ?>
                            				<option <?php if($cod_depreciaciones == $row["codigo"]) echo "selected"; ?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                            			<?php } ?>
                            		</select>				
                                    </div>
                                </div>
                            </div><!--fin campo cod_depreciaciones -->

                            <div class="row">
                                <label class="col-sm-2 col-form-label">Tipo Bien</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <div id="cod_tiposbienes_containers">
                            		<select name="cod_tiposbienes" id="cod_tiposbienes" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" required="true">
                                        <option disabled selected value=""></option>
                            			<?php while ($row = $statementTIPOSBIENES->fetch()){ ?>
                            				<option <?php if($cod_tiposbienes == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["tipo_bien"];?></option>
                            			<?php } ?>
                            		</select>
                                    </div>
                                </div>
                                </div>

                                <label class="col-sm-2 col-form-label">Fecha Alta</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fechalta" id="fechalta" required="true" value="<?=$fechalta;?>" required="true"/>
                                </div>
                                </div><!--fin campo fechalta -->
                            </div>
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Valor Inicial</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="number" step="0.01" name="valorinicial" id="valorinicial" required="true" value="<?=$valorinicial;?>" required="true"/>
                                </div>
                                </div>
                                <!--fin campo valorinicial -->
                                <label class="col-sm-2 col-form-label">Depr. Acumulada (Nuevo es 0)</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="depreciacionacumulada" id="depreciacionacumulada" required="true" value="<?=($row["codigo"]==0)?"0":$depreciacionacumulada;?>"/>
                                </div>
                                </div>
                            </div><!--fin campo depreciacionacumulada --> 
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Valor Residual</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" readonly type="text" name="valorresidual" id="valorresidual" required="true" value="<?=$valorresidual;?>"/>
                                </div>
                                </div><!--fin campo valorresidual -->

                                <label class="col-sm-2 col-form-label">Vida Util Meses</label><!-- sel automaticamente -->
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <div id="div_contenedor_valorR">
                                        <input class="form-control" type="text" name="vidautilmeses" id="vidautilmeses" required="true" value="<?=$vidautilmeses;?>" readonly="true"/>
                                    </div>
                                </div>
                                </div>
                            </div><!--fin campo vidautilmeses -->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Estado Bien</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                            				<select name="estadobien" name="estadobien" class="selectpicker form-control form-control-sm" data-style="btn btn-primary">
                            					<option <?php if("NUEVO" == $estadobien) echo "selected"; ?> value="NUEVO">NUEVO</option>
                            					<option <?php if("BUENO" == $estadobien) echo "selected"; ?> value="BUENO">BUENO</option>
                            					<option <?php if("REGULAR" == $estadobien) echo "selected"; ?> value="REGULAR">REGULAR</option>
                            					<option <?php if("MALO" == $estadobien) echo "selected"; ?> value="MALO">MALO</option>
                            					<option <?php if("OBSOLETO" == $estadobien) echo "selected"; ?> value="OBSOLETO">OBSOLETO</option>
                            					<option <?php if("PESIMO" == $estadobien) echo "selected"; ?> value="PESIMO">PESIMO</option>
                            				</select>
                                </div>
                                </div><!--fin campo estadobien -->
                                <label class="col-sm-2 col-form-label">Tipo Activo</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <select name="cod_tiposactivos" id="cod_tiposactivos" class="selectpicker form-control form-control-sm" data-style="btn btn-primary">
                                        <?php while ($row = $statementTIPOSAF->fetch()){ ?>
                                            <option <?php if($tipo_af == $row["codigo"]) echo "selected"; ?> value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                </div>

                                
                            </div><!--fin campo cod_ubicaciones -->

                            <div class="row">
                                <label class="col-sm-2 col-form-label">Nombre Activo</label>
                                <div class="col-sm-7">
                                <div class="form-group">
                                    <textarea rows="2" class="form-control" name="activo" id="activo" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"><?=$activo;?></textarea>
                                </div>
                                </div>
                            </div><!--fin campo activo -->

                            <div class="row">
                                <label class="col-sm-2 col-form-label">Datos Complementarios</label>
                                <div class="col-sm-7">
                                <div class="form-group">
                                    <textarea rows="2" class="form-control" name="otrodato" id="otrodato" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"><?=$otrodato;?></textarea>
                                </div>
                                </div>
                            </div><!--fin campo activo -->

                            <div class="row">
                                <label class="col-sm-2 col-form-label">Responsable</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <div id="div_personal_UO">
                                        <?php
                                        $stmtRR = $dbh->prepare("SELECT p.codigo, p.paterno,p.materno,p.primer_nombre
                                        from personal p, ubicaciones u, unidades_organizacionales uo 
                                        where u.cod_unidades_organizacionales=uo.codigo and uo.codigo=p.cod_unidadorganizacional and uo.codigo=$cod_unidadorganizacional order by 2");
                                        $stmtRR->execute();
                                        ?>
                                        <select id="cod_responsables_responsable" name="cod_responsables_responsable" class="selectpicker form-control form-control-sm" 
                                        data-style="btn btn-primary" data-size="5">
                                            <?php while ($row = $stmtRR->fetch()){ ?>
                                                <option <?=($cod_responsables_responsable==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>">
                                                    <?=$row["paterno"].' '.$row["materno"].' '.$row["primer_nombre"];?>
                                                </option>
                                            <?php } ?>

                                        </select>

                                    </div>
                                </div>
                                </div><!--fin campo cod_responsables_responsable -->

                                <label class="col-sm-2 col-form-label">Autorizado Por</label>
                                <div class="col-sm-4">
                                <div class="form-group">
                                    <select id="cod_responsables_autorizadopor" name="cod_responsables_autorizadopor" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" required="true">
                                        <?php while ($row = $statementPersonal2->fetch()){ ?>
                                            <option <?=($cod_responsables_autorizadopor==$row["codigo"])?"selected":"";?> value="<?=$row["codigo"];?>">
                                                <?=$row["paterno"].' '.$row["materno"].' '.$row["primer_nombre"];?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                </div>
                            </div><!--fin campo cod_responsables_autorizadopor -->
                            


                            <!-- proveedor -->
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Proveedor :</label>
                                <div class="col-sm-4">
                                <div class="form-group">                        
                                    <div id="div_contenedor_proveedor">
                                        <select class="selectpicker form-control form-control-sm" name="proveedores" id="proveedores" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" title="Seleccione Proveedor" required="true">
                                          <option value=""></option>
                                          <?php 
                                          $query="SELECT * FROM af_proveedores order by nombre";
                                          $stmt = $dbh->prepare($query);
                                          $stmt->execute();
                                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            $codigoProv=$row['codigo'];    
                                            ?><option <?=($cod_af_proveedores==$codigoProv)?"selected":"";?> value="<?=$codigoProv?>" class="text-right"><?=$row['nombre']?></option>
                                           <?php 
                                           } ?> 
                                        </select>
                                    </div>
                                </div>
                                </div>      
                                <div class="col-sm-2">
                                    <div class="form-group">                                
                                      <a href="#" class="btn btn-warning btn-round btn-fab btn-sm" onclick="cargarDatosRegistroProveedorActivoFijo(<?=$codigo?>)">
                                        <i class="material-icons" title="Add Proveedor">add</i>
                                      </a>
                                      <a href="#" class="btn btn-success btn-round btn-fab btn-sm" onclick="actualizarRegistroProveedorActivoFijo(<?=$codigo?>)">
                                        <i class="material-icons" title="Actualizar Proveedor">update</i>
                                      </a> 
                                    </div>
                                </div>                          
                            </div>


                            <div class="row">
                                <label class="col-sm-2 col-form-label">Nombre Proyecto Financiación</label>
                                <div class="col-sm-4">
                                    <div class="form-group">                            
                                        <select name="cod_proy_finan" id="cod_proy_finan" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" >                                    
                                            <?php while ($row = $statementProyFinanciacion->fetch()){ ?>
                                                <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <label class="col-sm-2 col-form-label">Factura o Numero de Documento</label>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="numerofactura" id="numerofactura" required="true" value="<?=$numerofactura;?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                                        <div class="fileinput-new img-raised">
                                            <img src="<?=$archivo;?>" alt="..." style="width:250px;">
                                        </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail img-raised">
                                        </div>
                                        <div>
                                            <span class="btn btn-raised btn-round <?=$buttonNormal;?> btn-file">
                                            <span class="fileinput-new">Seleccionar Imagen</span>
                                            <span class="fileinput-exists">Cambiar</span>
                                            <input type="file" name="image" /><!-- ARCHHIVO -->
                                            </span>
                                            <a href="#" class="btn <?=$buttonNormal;?> btn-round fileinput-exists" data-dismiss="fileinput">
                                            <i class="fa fa-times"></i> Quitar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer fixed-bottom">
                            <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
                            <a href="?opcion=activosfijosLista" class="<?=$buttonCancel;?>">Volver</a>
                        </div>
        			</div>
                </form>
    		</div>
        </div>
	
	</div>
</div>

<!-- carga de proveedores -->
<div class="cargar">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<div class="modal fade modal-arriba modal-primary" id="modalAgregarProveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content card">
            <div class="card-header card-header-warning card-header-icon">
                <div class="card-icon">
                    <i class="material-icons text-dark">ballot</i>
                 </div>
                  <h4 class="card-title">Proveedor</h4>
            </div>
            <div class="card-body">
                 <div id="datosProveedorNuevo">
                   
                 </div> 
                <div class="form-group float-right">
                        <button type="button" onclick="guardarDatosProveedorActivosFijos()" class="btn btn-info btn-round">Agregar</button>
                </div>
          </div>
      </div>  
    </div>
  </div>

  <script>$('.selectpicker').selectpicker("refresh");</script>
  <?php 
  // require_once 'modal.php';
?>
