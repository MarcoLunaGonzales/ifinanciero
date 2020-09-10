<?php
require_once 'conexion.php';
require_once 'conexion_externa.php';
require_once 'configModule.php';
require_once 'styles.php';
$codigo_simulacion=0;//codigo de simulacion
$dbh = new Conexion();
$globalAdmin=$_SESSION["globalAdmin"];
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
// $sql="SELECT nombre,cod_area,cod_uo from simulaciones_costos where codigo=$codigo_simulacion";
// $stmtSimu = $dbh->prepare($sql);
// $stmtSimu->execute();
// $resultSimu = $stmtSimu->fetch();
// $nombre_simulacion = $resultSimu['nombre'];
// $cod_area = $resultSimu['cod_area'];
// $cod_uo = $resultSimu['cod_uo'];
//simulamos conexion con ibnorca

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
            <h4 class="card-title text-center"><b>Empresas</b></h4>
            <span style="color: #ff0000;"><center>Para listar todas las empresas, simplemente presione en Buscar</center></span>
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
              <input type="hidden" name="u" id="u" value="0">
              <input type="hidden" name="s" id="s" value="0">
              <input type="hidden" name="r" id="r" value="0">
            <?php }
            ?>              
            </div>
          <div class="card-body">
            <div class="row">
                <label style="color:#000000" class="col-sm-12 col-form-label text-center">Empresa</label>
            </div> 
            <div class="row">
              <div class="form-group col-sm-3">            
              </div>
              <div class="form-group col-sm-6">            
                    <select name="cod_empresa[]" id="cod_empresa" class="selectpicker form-control form-control-sm" data-style="btn btn-info select-with-transition" data-show-subtext="true" data-live-search="true" data-actions-box="true" multiple>
                    <!-- <option value="0">SELECCIONE UNA EMPRESA</option> -->
                    <?php 
                    $query1 = "SELECT codigo,nombre from clientes where cod_estadoreferencial=1 order by nombre";
                    $statement = $dbh->query($query1);
                    while ($row = $statement->fetch()){ ?>
                        <option value="<?=$row["codigo"];?>"><?=$row["nombre"];?> </option>
                    <?php } ?>
                    </select>
              </div>
            </div> 
            <div class="row">                                                  
              <label style="color:#000000" class="col-sm-12 col-form-label text-center">Nombre Curso</label>
            </div> 
            <div class="row">
              <div class="form-group col-sm-3">            
                </div>
              <div class="form-group col-sm-6">
                  <input class="form-control input-sm" type="text" name="glosa" id="glosa"  >
              </div>                    
            </div>
            <div class="row">                                                  
              <label style="color:#000000" class="col-sm-12 col-form-label text-center">Código Curso</label>
            </div> 
            <div class="row">
              <div class="form-group col-sm-3">            
                </div>
              <div class="form-group col-sm-6">
                  <input class="form-control input-sm" type="text" name="codigo_curso" id="codigo_curso"  >
              </div>                    
            </div>
          </div>
          <div class="card-footer fixed-bottom"> 
          <button type="button" class="btn btn-primary" id="botonBuscarEmpresas" name="botonBuscarEmpresas" title="Buscar" onclick="botonBuscarEmpresasCapacitacion()">Buscar</button>             
            <?php                                    
                if(isset($_GET['q'])){?>
                    <a href='<?=$urlListSol?>&q=<?=$q?>&v=<?=$r?>&u=<?=$u?>&s=<?=$s?>' class="<?=$buttonCancel;?>" title="Ir A Solicitudes de Facturación"><i class="material-icons">keyboard_return</i> IR A SF</a>
                <?php }else{?>
                    <a href='<?=$urlListSol?>' class="<?=$buttonCancel;?>" title="Ir A Solicitudes de Facturación"><i class="material-icons">keyboard_return</i> IR A SF</a>                    
                <?php }                     
            ?> 
          </div>      
      </div>
    </div>  
  </div>
</div>

