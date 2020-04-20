<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

//$dbh = new Conexion();
$dbh = new Conexion();
$ci_estudiante=$codigo;
$cod_simulacion=$cod_simulacion;
$cod_facturacion=$cod_facturacion;
//sacamos datos para la facturacion
$sql="SELECT sc.nombre,sc.cod_responsable,ps.cod_area,ps.cod_unidadorganizacional
from simulaciones_costos sc,plantillas_costo ps
where sc.cod_plantillacosto=ps.codigo and sc.cod_estadoreferencial=1 and sc.codigo=$cod_simulacion order by sc.codigo";
$stmtSimu = $dbh->prepare($sql);
$stmtSimu->execute();
$resultSimu = $stmtSimu->fetch();
$nombre_simulacion = $resultSimu['nombre'];
$cod_uo = $resultSimu['cod_unidadorganizacional'];
$cod_area = $resultSimu['cod_area'];
$cod_responsable = $resultSimu['cod_responsable'];


//simulacion conexxion con ibnorca
class ConexionIBNORCA extends PDO { 
  private $tipo_de_base = 'mysql';
  private $host = 'localhost';
  private $nombre_de_base = 'ibnorca';
  private $usuario = 'root';
  private $contrasena = '';
  private $port = '3306';   
  public function __construct() {
    //Sobreescribo el método constructor de la clase PDO.
    try{
       parent::__construct($this->tipo_de_base.':host='.$this->host.';dbname='.$this->nombre_de_base.';port='.$this->port, $this->usuario, $this->contrasena,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));//      
    }catch(PDOException $e){
       echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
       exit;
    }
  } 
} 
$dbhIBNO = new ConexionIBNORCA();
//nombre del curso de ibnoca
$stmtIBNOCurso = $dbhIBNO->prepare("SELECT pc.Nombre
FROM asignacionalumno aa, alumnos a, alumnocurso ac, clasificador c, programas_cursos pc, modulos m where aa.IdModulo=4000 and a.CiAlumno=aa.CiAlumno 
and ac.IdCurso=aa.IdCurso and ac.CiAlumno=aa.CiAlumno and ac.IdConceptoPago=c.IdClasificador and pc.IdCurso=pc.IdCurso and pc.IdCurso=aa.IdCurso and 
m.IdCurso=pc.IdCurso and m.IdModulo=aa.IdModulo and aa.CiAlumno=$ci_estudiante");//poner el codigo de curso a buscar
$stmtIBNOCurso->execute();
$resultNombreCurso = $stmtIBNOCurso->fetch();
$nombre_curso = $resultNombreCurso['Nombre'];
//datos del estudiante y el curso que se encuentra
$sqlIBNORCA="SELECT aa.IdModulo, aa.IdCurso, aa.CiAlumno, concat(a.ApPaterno,' ',a.ApMaterno,' ',a.Nombre)as nombreAlumno, c.Abrev, c.Auxiliar,
pc.Costo, pc.CantidadModulos, m.NroModulo, pc.Nombre
FROM asignacionalumno aa, alumnos a, alumnocurso ac, clasificador c, programas_cursos pc, modulos m where aa.IdModulo=4000 and a.CiAlumno=aa.CiAlumno 
and ac.IdCurso=aa.IdCurso and ac.CiAlumno=aa.CiAlumno and ac.IdConceptoPago=c.IdClasificador and pc.IdCurso=pc.IdCurso and pc.IdCurso=aa.IdCurso and 
m.IdCurso=pc.IdCurso and m.IdModulo=aa.IdModulo and aa.CiAlumno=$ci_estudiante";
$stmtIbno = $dbhIBNO->prepare($sqlIBNORCA);
$stmtIbno->execute();
$resultSimu = $stmtIbno->fetch();
$IdModulo = $resultSimu['IdModulo'];
$IdCurso = $resultSimu['IdCurso'];
$nombreAlumno = $resultSimu['nombreAlumno'];
$Abrev = $resultSimu['Abrev'];
$Costo = $resultSimu['Costo'];
$CantidadModulos = $resultSimu['CantidadModulos'];
$NroModulo = $resultSimu['NroModulo'];
$Nombre = $resultSimu['Nombre'];
$monto_pagar=($Costo - ($Costo*$Abrev/100) )/$CantidadModulos; //formula para sacar el monto a pagar del estudiante

if($cod_facturacion>0){//editar
    $sqlFac="SELECT * from solicitudes_facturacion where codigo=$cod_facturacion";
    $stmtSimuFact = $dbh->prepare($sqlFac);
    $stmtSimuFact->execute();
    $resultSimuFact = $stmtSimuFact->fetch();
    $fecha_registro = $resultSimuFact['fecha_registro'];
    $fecha_solicitudfactura = $resultSimuFact['fecha_solicitudfactura'];
    $razon_social = $resultSimuFact['razon_social'];
    $nit = $resultSimuFact['nit'];
    $observaciones = $resultSimuFact['observaciones'];
    $cod_tipopago=$resultSimuFact['cod_tipopago'];
    $cod_tipoobjeto=$resultSimuFact['cod_tipoobjeto'];
}else{//registrat
    $fecha_registro = date('Y-m-d');
    $fecha_solicitudfactura = date('Y-m-d');
    $razon_social= $nombreAlumno;
    $nit = null;
    $observaciones = null;
    $cod_tipopago=null;
    $cod_tipoobjeto=obtenerValorConfiguracion(41);
}

$name_uo=nameUnidad($cod_uo);
$name_area=abrevArea($cod_area);
$contadorRegistros=0;
?>
<script>
  numFilas=<?=$contadorRegistros;?>;
  cantidadItems=<?=$contadorRegistros;?>;
</script>
<div class="content">
    <div class="container-fluid">
        <div style="overflow-y:scroll;">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="<?=$urlSave_solicitud_facturacion_costos;?>" method="post" onsubmit="return valida(this)">                
                <input type="hidden" name="ci_estudiante" id="ci_estudiante" value="<?=$ci_estudiante;?>"/>
                <input type="hidden" name="cod_simulacion" id="cod_simulacion" value="<?=$cod_simulacion;?>"/>
                <input type="hidden" name="cod_facturacion" id="cod_facturacion" value="<?=$cod_facturacion;?>"/>
                <input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
                
                <div class="card">
                    <div class="card-header <?=$colorCard;?> card-header-text">
                        <div class="card-text">
                          <h4 class="card-title"><?php if ($cod_facturacion == 0) echo "Registrar "; else echo "Editar ";?>Solicitud de Facturación</h4>                      
                        </div>
                        <h4 class="card-title" align="center"><b>Propuesta : <?=$nombre_simulacion?></b></h4>
                        <h4 class="card-title" align="center"><b>Módulo : <?=$NroModulo?></b></h4>
                    </div>
                    <div class="card-body ">    
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Oficina</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="hidden" name="cod_uo" id="cod_uo" required="true" value="<?=$cod_uo;?>" required="true" readonly/>
                                    <input class="form-control" type="text" required="true" value="<?=$name_uo;?>" required="true" readonly/>
                               
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Area</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                    <div id="div_contenedor_area_tcc">
                                        <input class="form-control" type="hidden" name="cod_area" id="cod_area" required="true" value="<?=$cod_area;?>" required="true" readonly/>

                                        <input class="form-control" type="text" required="true" value="<?=$name_area;?>" required="true" readonly/>
                                       
                                    </div>                    
                                </div>
                            </div>
                        </div> 
                        <!-- unidad  / area -->                       
                        <div class="row">
                            <label class="col-sm-2 col-form-label">F. Registro</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_registro" id="fecha_registro" required="true" value="<?=$fecha_registro;?>"/>
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">F. A Facturar</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="date" name="fecha_solicitudfactura" id="fecha_solicitudfactura" required="true" value="<?=$fecha_solicitudfactura;?>" required="true"/>
                                </div>
                            </div>
                        </div>
                        <!-- fin fechas -->
                        <div class="row">                           
                            <label class="col-sm-2 col-form-label">Tipo Objeto</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                        <select name="cod_tipoobjeto" id="cod_tipoobjeto" class="selectpicker form-control form-control-sm" data-style="btn btn-info" required="true">
                                            <option value=""></option>
                                            <?php 
                                            $queryTipoObjeto = "SELECT codigo,nombre FROM  tipos_objetofacturacion WHERE cod_estadoreferencial=1 order by nombre";
                                            $statementObjeto = $dbh->query($queryTipoObjeto);
                                            while ($row = $statementObjeto->fetch()){ ?>
                                                <option <?=($cod_tipoobjeto==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>                                
                                </div>
                            </div>

                            <label class="col-sm-2 col-form-label">Tipo Pago</label>
                            <div class="col-sm-4">
                                <div class="form-group" >
                                        <select name="cod_tipopago" id="cod_tipopago" class="selectpicker form-control form-control-sm" data-style="btn btn-info" required="true">
                                            <option value=""></option>
                                            <?php 
                                            $queryTipoPago = "SELECT codigo,nombre FROM  tipos_pago WHERE cod_estadoreferencial=1 order by nombre";
                                            $statementPAgo = $dbh->query($queryTipoPago);
                                            while ($row = $statementPAgo->fetch()){ ?>
                                                <option <?=($cod_tipopago==$row["codigo"])?"selected":"";?>  value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option>
                                            <?php } ?>
                                        </select>                                
                                </div>
                            </div>
                        </div>
                        <!-- fin tipos pago y objeto  -->                                                 
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Estudiante</label>
                            <div class="col-sm-4">
                                <div class="form-group" >                                     
                                        <input class="form-control" type="text" id="nombreAlumno" name="nombreAlumno" value="<?=$nombreAlumno;?>" required="true" readonly style="background-color:#E3CEF6;text-align: left"/>
                                        
                                </div>
                            </div>      
                            <label class="col-sm-2 col-form-label">Responsable</label>
                            <div class="col-sm-4">
                                <div class="form-group">            
                                    <?php  $responsable=namePersonal($cod_responsable); ?>                    
                                    <input type="hidden" name="cod_personal" id="cod_personal" value="<?=$cod_responsable?>" readonly="true" class="form-control">
                                    <input type="text" value="<?=$responsable?>" readonly="true" class="form-control">
                                </div>
                            </div>        
                        </div>
                        <!-- fin cliente y responsable -->                       
                                                                

                        <div class="row">
                            <label class="col-sm-2 col-form-label">Razón Social</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <!-- <div id="contenedor_razonsocial"> -->
                                        <input class="form-control" type="text" name="razon_social" id="razon_social" required="true"  onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?=$razon_social?>" />    
                                    <!-- </div> -->
                                </div>
                            </div>
                            <label class="col-sm-2 col-form-label">Nit</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <input class="form-control" type="number" name="nit" id="nit" value="<?=$nit;?>" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true" />
                                </div>
                            </div>
                        </div>
                        <!-- fin razon social y nit -->
                        <div class="row">
                            <label class="col-sm-2 col-form-label">Observaciones</label>
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <input class="form-control" type="text" name="observaciones" id="observaciones" required="true" value="<?=$observaciones?>" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                                </div>
                            </div>
                        </div>
                        <!-- fin observaciones -->
                        <div class="card">
                            <div class="card-header <?=$colorCard;?> card-header-text">
                                <div class="card-text">
                                  <h6 class="card-title">Detalle Solicitud Facturación</h6>
                                </div>
                            </div>
                            <div class="card-body ">
                                <table class="table table-bordered table-condensed table-striped table-sm">
                                     <thead>
                                          <tr class="fondo-boton">
                                            <th>#</th>
                                            <!-- <th >Año</th> -->
                                            <th>Curso</th>
                                            <th>Cant.</th>
                                            <th>Importe</th>
                                            <th>Total</th>                                            
                                            <th class="small">H/D</th>
                                            <th width="30%">Descripción</th>  
                                          </tr>
                                      </thead>
                                      <tbody>                                
                                        <?php 
                                        $iii=1;                                       
                                        $modal_totalmontopre=0;$modal_totalmontopretotal=0;                                        
                                            $codigoPre=$IdModulo;
                                            $codCS=430;//defecto
                                            $tipoPre=$Nombre;
                                            $cantidadPre=1;
                                            $cantidadEPre=1;
                                            $montoPre=$monto_pagar;
                                            // $montoPreTotal=$montoPre*$cantidadEPre;
                                            $banderaHab=1;
                                            $codTipoUnidad=1;
                                            $cod_anio=1;

                                            if($banderaHab!=0){
                                                // $modal_totalmontopre+=$montoPre;
                                                $montoPre=number_format($montoPre,2,".","");
                                                // $modal_totalmontopretotal+=$montoPreTotal;
                                                ?>
                                                <!-- guardamos las varialbles en un input -->
                                                <input type="hidden" id="cod_serv_tiposerv<?=$iii?>" name="cod_serv_tiposerv<?=$iii?>" value="<?=$codigoPre?>">
                                                <input type="hidden" id="servicio<?=$iii?>" name="servicio<?=$iii?>" value="<?=$codCS?>">
                                                <input type="hidden" id="cantidad<?=$iii?>" name="cantidad<?=$iii?>" value="<?=$cantidadPre?>">
                                                <input type="hidden" id="importe<?=$iii?>" name="importe<?=$iii?>" value="<?=$montoPre?>">

                                                <!-- aqui se captura los servicios activados -->
                                                <input type="hidden" id="cod_serv_tiposerv_a<?=$iii?>" name="cod_serv_tiposerv_a<?=$iii?>">
                                                <input type="hidden" id="servicio_a<?=$iii?>" name="servicio_a<?=$iii?>">
                                                <input type="hidden" id="cantidad_a<?=$iii?>" name="cantidad_a<?=$iii?>">
                                                <input type="hidden" id="importe_a<?=$iii?>" name="importe_a<?=$iii?>">
                                                <tr>
                                                    <td><?=$iii?></td>
                                                    <!-- <td class="text-left"><?=$cod_anio?> </td> -->
                                                    <td class="text-right"><?=$tipoPre?></td>
                                                    <td class="text-right"><?=$cantidadPre?></td>
                                                    <td class="text-right"><?=formatNumberDec($montoPre)?></td>
                                                    <td class="text-right">
                                                    <input type="number" id="modal_importe<?=$iii?>" name="modal_importe<?=$iii?>" class="form-control text-primary text-right"  value="<?=$montoPre?>" step="0.01">
                                                    </td>
                                                 
                                                    <td>
                                                        <div class="togglebutton">
                                                           <label>
                                                             <input type="checkbox"  id="modal_check<?=$iii?>" onchange="activarInputMontoFilaServicio2()">
                                                             <span class="toggle"></span>
                                                           </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="descripcion_alterna<?=$iii?>" id="descripcion_alterna<?=$iii?>" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                    </td>
                                                </tr>

                                              <?php   $iii++;
                                            }
                                                                                                                        
                                              // $montoPreTotal=number_format($montoPreTotal,2,".","");
                                         ?>                        
                                      </tbody>
                                </table>

                                <input type="hidden" id="modal_numeroservicio" name="modal_numeroservicio" value="<?=$iii?>">                    
                                <input type="hidden" id="modal_totalmontos" name="modal_totalmontos">
                                <!-- <script>activarInputMontoFilaServicio2();</script>   -->
                                <input type="hidden" id="comprobante_auxiliar" name="comprobante_auxiliar">
                                <div class="row">
                                    <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">                                        
                                            <input style="background:#ffffff" class="form-control" type="text" value="0" name="modal_totalmontoserv" id="modal_totalmontoserv"/>                                            
                                        </div>
                                    </div>
                                        
                                </div>
                                <!-- <fieldset id="fiel" style="width:100%;border:0;">
                                    <button title="Agregar Servicios" type="button" id="add_boton" name="add" class="btn btn-warning btn-round btn-fab" onClick="AgregarSeviciosFacturacion2(this)">
                                        <i class="material-icons">add</i>
                                    </button><span style="color:#084B8A;"><b> SERVICIOS ADICIONALES</b></span>
                                    <div id="div<?=$index;?>">  
                                        <div class="h-divider">
                                        
                                        </div>
                                    </div>
                                    

                                </fieldset> -->
                                <!-- <div class="row">
                                    <label class="col-sm-5 col-form-label" style="color:#000000">Monto Total + Servicios Adicionales</label>
                                    <div class="col-sm-4">
                                        <div class="form-group">                                            
                                            <input style="background:#ffffff" class="form-control"  name="monto_total" id="monto_total"  readonly="readonly" value="0" />
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>                 
                  </div>
                  <div class="card-footer ml-auto mr-auto">
                    <button type="submit" class="<?=$buttonNormal;?>">Guardar</button>                
                    <a href='<?=$urlSolicitudfactura?>&cod=<?=$cod_simulacion?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a>                    
                  </div>
                </div>
              </form>                  
            </div>
        </div>
    </div>
</div>
<!-- verifica que esté seleccionado al menos un item -->
<script type="text/javascript">
    function valida(f) {
        var ok = true;
        var msg = "Habilite los servicios que se desee facturar...\n";  
        if(f.elements["modal_totalmontoserv"].value == 0 || f.elements["modal_totalmontoserv"].value == '')
        {    
            ok = false;
        }
        
        if(ok == false)
          alert(msg);
        return ok;
    }
</script>