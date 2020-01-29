<?php 
	//carga la plantilla con la header y el footer
set_time_limit(0);

session_start();
if(isset($_SESSION['logueado'])){
	require_once('layouts/layout.php');	
}else{
	header("location:login.html");
}
 ?>
