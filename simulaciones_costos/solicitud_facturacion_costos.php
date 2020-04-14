<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
$codigo_simulacion=$cod;//codigo de simulacion
$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];

$sql="SELECT nombre from simulaciones_costos where codigo=$codigo_simulacion";
$stmtSimu = $dbh->prepare($sql);
$stmtSimu->execute();
$resultSimu = $stmtSimu->fetch();
$nombre_simulacion = $resultSimu['nombre'];
// $cod_area_simulacion = $resultSimu['cod_area'];
// $name_area_simulacion=abrevArea($cod_area_simulacion);
//simulamos conexion con ibnorca
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
  //datos registrado de la simulacion en curso
  $stmtIBNO = $dbhIBNO->prepare("SELECT aa.IdModulo, aa.IdCurso, aa.CiAlumno, concat(a.ApPaterno,' ',a.ApMaterno,' ',a.Nombre)as nombreAlumno, c.Abrev, c.Auxiliar,
pc.Costo, pc.CantidadModulos, m.NroModulo, pc.Nombre
FROM asignacionalumno aa, alumnos a, alumnocurso ac, clasificador c, programas_cursos pc, modulos m where aa.IdModulo=4000 and a.CiAlumno=aa.CiAlumno 
and ac.IdCurso=aa.IdCurso and ac.CiAlumno=aa.CiAlumno and ac.IdConceptoPago=c.IdClasificador and pc.IdCurso=pc.IdCurso and pc.IdCurso=aa.IdCurso and 
m.IdCurso=pc.IdCurso and m.IdModulo=aa.IdModulo");
  $stmtIBNO->execute();
  $stmtIBNO->bindColumn('IdModulo', $IdModulo);
  $stmtIBNO->bindColumn('IdCurso', $IdCurso);
  $stmtIBNO->bindColumn('CiAlumno', $CiAlumno);
  $stmtIBNO->bindColumn('nombreAlumno', $nombreAlumno);
  $stmtIBNO->bindColumn('Abrev', $Abrev);
  $stmtIBNO->bindColumn('Auxiliar', $Auxiliar);
  $stmtIBNO->bindColumn('Costo', $Costo);
  $stmtIBNO->bindColumn('CantidadModulos', $CantidadModulos);
  $stmtIBNO->bindColumn('NroModulo', $NroModulo);
  $stmtIBNO->bindColumn('Nombre', $nombre_mod);
  ?>
  <div class="content">
    <div class="container-fluid">
          <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header card-header-warning card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">polymer</i>
                    </div>
                    <h4 class="card-title"><b>Solicitud de Facturación Capacitación</b></h4>
                    <h4 class="card-title" align="center"><b><?=$nombre_simulacion?></b></h4>
                  </div>
                  <div class="card-body">
                      <table class="table" id="tablePaginator">
                        <thead>
                          <tr>
                            <th class="text-center">#</th>                          
                            <th>CI Alumno</th>
                            <th>Nombre</th>
                            <th>Costo</th>
                            <th>Canti. Mod</th>
                            <th>Nro Módulo</th>
                            <th>Nombre Mod.</th>
                            <th class="text-right">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                          $index=1;
                          while ($row = $stmtIBNO->fetch(PDO::FETCH_BOUND)) {                            
                            //los registros de la factura
                           
                            ?>
                          <tr>
                            <td align="center"><?=$index;?></td>
                            <td><?=$CiAlumno;?></td>
                            <td><?=$nombreAlumno;?></td>
                            <td><?=$Costo;?></td>
                            <td><?=$CantidadModulos;?></td>
                            <td><?=$NroModulo;?></td>
                            <td><?=$nombre_mod;?></td>

                            <td class="td-actions text-right">
                              <?php
                                if($globalAdmin==1){                            
                                ?>                              
                              <a href='<?=$urlregistro_solicitud_facturacion?>&codigo=<?=$CiAlumno?>' rel="tooltip" class="btn" style="background-color: #0489B1;">
                              <i class="material-icons" title="Solicitar Facturación">receipt</i>
                            </a>                                                  
                                <?php  
                                }
                              ?>
                            </td>
                          </tr>
                          <?php
                              $index++;
                            }
                          ?>
                        </tbody>
                      </table>
                  </div>
                </div>
                <div class="card-footer fixed-bottom">
                 <?php 
                if($globalAdmin==1){              
                    ?>
                    <!-- <a href="<?=$urlRegisterSolicitudfactura;?>&cod_s=<?=$codigo_simulacion?>&cod_f=0&cod_sw=1" target="_self" class="<?=$buttonNormal;?>">Registrar</a>
                    <a href='<?=$urlList;?>' class="<?=$buttonCancel;?>"><i class="material-icons" title="Volver">keyboard_return</i> Volver </a> -->
                    <?php                
                } 
                 ?>
                </div>      
              </div>
          </div>  
    </div>
  </div>



  