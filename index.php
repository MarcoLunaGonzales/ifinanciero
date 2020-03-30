<?php 
	//carga la plantilla con la header y el footer
set_time_limit(0);

session_start();
if(isset($_SESSION['logueado'])){
	require_once('layouts/layout.php');	
}else{
	if(isset($_GET['q'])){
	 $q=$_GET['q'];	 
	 header("location:login.php?q=".$q);	
	}else{
     header("location:login.html");  
	}
   
}
 ?>
