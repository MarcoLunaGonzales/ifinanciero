<?php 
//header('Content-Type: text/html; charset=iso-8859-1');
	include("head.php");

	//include("menuService.php");


  include("librerias.php");// se debe cambiar a la parte posterior

 
  // include("functionsGeneral.php");
?>    
    <div class="main-panel">
      <div class="content">

      <?php 
          
          if(!isset($_GET['opcion'])){
            $_SESSION['modulo']=0;
            include("cabecera.php");
            include("home.php");
          }else{
            include("layouts/menu.php");
            include("cabecera.php");
            require_once('routing.php');
          }       
      ?>

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
}
?>