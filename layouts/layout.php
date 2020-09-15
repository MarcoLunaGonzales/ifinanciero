<?php 
//header('Content-Type: text/html; charset=iso-8859-1');
  require_once 'functions.php';
  //require_once 'functionsGeneral.php';
	include("head.php");
  include("librerias.php");// se debe cambiar a la parte posterior
?>    
    <div class="">
      <div class="wrapper">
      <?php 
          $tipoLogin=obtenerValorConfiguracion(-10);

          if(!isset($_GET['opcion'])){
            $_SESSION['modulo']=0;
            include("cabecera.php");
            include("home.php");
          }else{
            if(isset($_GET['q'])){
              include("cabecera.php");
              require_once('routing.php');
            }else{
              if($tipoLogin==1){
                include("menu.php");
              }else{
                include("menuService.php");
              }
              include("cabecera.php");
              require_once('routing.php');
            }           
          }       
      ?>  
      </div><!-- el div que abre se encuentra dentro de cabecera al principio de NavBar Como en la documentación-->    
    </div>
   </div> 
<?php 
  //poner aqui librerias
if(!isset($_GET['opcion'])){
  ?><script type="text/javascript">
           $(document).ready(function(e) { 
               $("#minimizeSidebar").click()
               $("#minimizeSidebar").addClass("d-none");
             });
    </script><?php
}else{
  if(isset($_GET['q'])){
    ?><script type="text/javascript">
           $(document).ready(function(e) { 
               $("#minimizeSidebar").click()
               $("#minimizeSidebar").addClass("d-none");
             });
    </script><?php
  }
}
?>