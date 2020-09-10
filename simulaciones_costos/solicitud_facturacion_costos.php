<?php
require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'configModule.php';
require_once 'styles.php';
$codigo_simulacion=0;//codigo de simulacion
$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];
$globalUser=$_SESSION["globalUser"];
$sql="SELECT nombre,cod_area,cod_uo from simulaciones_costos where codigo=$codigo_simulacion";
$stmtSimu = $dbh->prepare($sql);
$stmtSimu->execute();
$resultSimu = $stmtSimu->fetch();
$nombre_simulacion = $resultSimu['nombre'];
$cod_area = $resultSimu['cod_area'];
$cod_uo = $resultSimu['cod_uo'];
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $r=$_GET['r'];
  $u=$r;
  $s=$r;
}
if(isset($_GET['s']))
{
  $s=$_GET['s'];
  $u=$_GET['u'];
}

?>
<div class="content">
    <div class="container-fluid">
          <div class="row">
              <div class="col-md-12">                
                  <div class="card">
                    <div class="card-header card-header-primary card-header-icon">
                      <div class="card-icon">
                        <i class="material-icons">polymer</i>
                      </div>
                      <h4 class="card-title"><b>Solicitud de Facturación para Capacitación</b></h4>                    
                      <h4 class="card-title text-center"><b>Estudiantes</b></h4>
                      <div>
                            <div  align="right">
                             <!--  <button type="button" class="btn btn-warning btn-round btn-fab btn-sm" data-toggle="modal" data-target="#modalBuscador">
                                <i class="material-icons" title="Buscador Avanzado">search</i>
                              </button>  -->                                
                            </div>
                          </div>
                    </div>
                    <div class="row">
                      <?php
                      if(isset($_GET['q'])){?>
                        <input type="hidden" name="q" id="q" value="<?=$q?>">
                        <input type="hidden" name="r" id="r" value="<?=$r?>">
                        <input type="hidden" name="s" id="s" value="<?=$s?>">
                        <input type="hidden" name="u" id="u" value="<?=$u?>">
                      <?php }else{?>
                        <input type="hidden" name="q" id="q" value="0">
                        <input type="hidden" name="r" id="r" value="0">
                        <input type="hidden" name="s" id="s" value="0">
                        <input type="hidden" name="u" id="u" value="0">
                      <?php }
                      ?>
                    </div>
                    <div class="card-body">                      
                      <div class="modal-body ">
                        <div class="row">
                          <label style="color:#000000" class="col-sm-4 col-form-label text-left">Nombre</label>
                          <label style="color:#000000" class="col-sm-4 col-form-label text-left">Paterno</label>   
                          <label style="color:#000000" class="col-sm-4 col-form-label text-left">Materno</label>
                        </div> 
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <input class="form-control" type="text" name="nombreCliente" id="nombreCliente"  >
                            </div>            
                            <div class="form-group col-sm-4">
                                <input class="form-control" type="text" name="paternoCliente" id="paternoCliente"  >
                            </div>            
                            <div class="form-group col-sm-4">
                                <input class="form-control" type="text" name="maternoCliente" id="maternoCliente"  >
                            </div>
                        </div> 
                        <div class="row">
                          <label style="color:#000000" class="col-sm-3 col-form-label text-left">CI</label>
                          <label style="color:#000000" class="col-sm-7 col-form-label text-left">Nombre Curso</label>
                          <label style="color:#000000" class="col-sm-2 col-form-label text-left">Fecha Inscripción</label>
                        </div>
                        <div class="row">
                          <div class="form-group col-sm-3">
                                <input class="form-control" type="text" name="ci" id="ci"  >
                            </div>            
                            <div class="form-group col-sm-7">
                              <input class="form-control" type="text" name="nombre_curso" id="nombre_curso">
                            </div>            
                            <div class="form-group col-sm-2">
                                <input class="form-control" type="date" name="fecha_inscripcion" id="fecha_inscripcion">
                            </div>
                        </div> 
                        <div class="row">
                          <label style="color:#000000" class="col-sm-2 col-form-label text-left">Código Curso</label>
                          <div class="form-group col-sm-5">
                            <input class="form-control" type="text" name="codigo_curso" id="codigo_curso">
                          </div>                            
                        </div> 
                      </div>                     
                    </div>

                    <div class="card-footer fixed-bottom">
                      <button type="button" class="btn btn-primary" id="botonBuscarEstudiantes" name="botonBuscarEstudiantes"  title="Buscar" onclick="botonBuscarEstudiantesCapacitacion()">Buscar</button>
                      <?php                                    
                        if(isset($_GET['q'])){?>
                            <a href='<?=$urlListSol?>&q=<?=$q?>&v=<?=$r?>&u=<?=$u?>&s=<?=$s?>' class="<?=$buttonCancel;?>"><i class="material-icons"  title="Ir A Solicitudes de Facturación">keyboard_return</i> IR A SF</a>
                        <?php }else{?>
                            <a href='<?=$urlListSol?>' class="<?=$buttonCancel;?>"><i class="material-icons"  title="Ir A Solicitudes de Facturación">keyboard_return</i> IR A SF</a>                    
                        <?php }                     
                    ?> 
                    </div>
                  </div>
              </div>
          </div>  
    </div>
</div>


<!-- <div class="cargar">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  



<script type="text/javascript">  
  $('#modalBuscador').modal('show');
</script>
   -->