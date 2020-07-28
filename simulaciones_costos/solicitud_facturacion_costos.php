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
if(isset($_GET['u']))
{
  $u=$_GET['u'];
  $s=$_GET['s'];
}

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
                          <label class="col-sm-4 col-form-label text-center">Nombre</label>
                          <label class="col-sm-4 col-form-label text-center">Paterno</label>   
                          <label class="col-sm-4 col-form-label text-center">Materno</label>
                        </div> 
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <input class="form-control input-sm" type="text" name="nombreCliente" id="nombreCliente"  >
                            </div>            
                            <div class="form-group col-sm-4">
                                <input class="form-control input-sm" type="text" name="paternoCliente" id="paternoCliente"  >
                            </div>            
                            <div class="form-group col-sm-4">
                                <input class="form-control input-sm" type="text" name="maternoCliente" id="maternoCliente"  >
                            </div>
                        </div> 
                        <div class="row">
                          <label class="col-sm-3 col-form-label text-center">CI</label>
                          <label class="col-sm-7 col-form-label text-center">Nombre Curso</label>
                          <label class="col-sm-2 col-form-label text-center">Fecha Inscripción</label>
                        </div>
                        <div class="row">
                          <div class="form-group col-sm-3">
                                <input class="form-control input-sm" type="text" name="ci" id="ci"  >
                            </div>            
                            <div class="form-group col-sm-7">
                              <input class="form-control input-sm" type="text" name="nombre_curso" id="nombre_curso">
                            </div>            
                            <div class="form-group col-sm-2">
                                <input class="form-control input-sm" type="date" name="fecha_inscripcion" id="fecha_inscripcion">
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
  </div>
</div> -->
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     <p class="text-white">Aguarde un momento por favor</p>  
  </div>
</div>
<!-- Modal busqueda de items-->
<div class="modal fade" id="modalBuscador" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Buscador de Estudiantes</h4>
      </div>
      <div class="row">
        <div class="form-group col-sm-12">
          <h5 style="color:#FF0000;" class="text-center">* Para Filtrar toda la lista, simplemente presione "buscar"</h5>  
        </div>
      </div>
      <div class="modal-body ">
        <div class="row">
          <label class="col-sm-4 col-form-label text-center">Nombre</label>
          <label class="col-sm-4 col-form-label text-center">Paterno</label>   
          <label class="col-sm-4 col-form-label text-center">Materno</label>
        </div> 
        <div class="row">
            <div class="form-group col-sm-4">
                <input class="form-control input-sm" type="text" name="nombreCliente" id="nombreCliente"  >
            </div>            
            <div class="form-group col-sm-4">
                <input class="form-control input-sm" type="text" name="paternoCliente" id="paternoCliente"  >
            </div>            
            <div class="form-group col-sm-4">
                <input class="form-control input-sm" type="text" name="maternoCliente" id="maternoCliente"  >
            </div>
        </div> 
        <div class="row">
          <label class="col-sm-4 col-form-label text-center">CI</label>
          <label class="col-sm-4 col-form-label text-center">Nombre Curso</label>
          <label class="col-sm-4 col-form-label text-center">Fecha Inscripción</label>
        </div>
        <div class="row">
          <div class="form-group col-sm-4">
                <input class="form-control input-sm" type="text" name="ci" id="ci"  >
            </div>            
            <div class="form-group col-sm-4">
              <input class="form-control input-sm" type="text" name="nombre_curso" id="nombre_curso">
            </div>            
            <div class="form-group col-sm-4">
                <input class="form-control input-sm" type="date" name="fecha_inscripcion" id="fecha_inscripcion">
            </div>
        </div> 
      </div>

      <div class="modal-footer">
         <?php                                    
            if(isset($_GET['q'])){?>
                <a href='<?=$urlListSol?>&q=<?=$q?>&v=<?=$r?>&u=<?=$r?>&s=<?=$r?>' class="<?=$buttonCancel;?>"><i class="material-icons"  title="Ir A Solicitudes de Facturación">keyboard_return</i> IR A SF</a>                    
            <?php }else{?>
                <a href='<?=$urlListSol?>' class="<?=$buttonCancel;?>"><i class="material-icons"  title="Ir A Solicitudes de Facturación">keyboard_return</i> IR A SF</a>                    
            <?php }                     
        ?> 
        <button type="button" class="btn btn-success" id="botonBuscarEstudiantes" name="botonBuscarEstudiantes"  title="Buscar" onclick="botonBuscarEstudiantesCapacitacion()">Buscar</button>
        <!-- <button type="button" class="btn btn-danger" data-dismiss="modal"> Cerrar </button> -->
      </div>
    </div>
  </div>
</div>



<!-- 
<script type="text/javascript">  
  $('#modalBuscador').modal('show');
</script>
   -->