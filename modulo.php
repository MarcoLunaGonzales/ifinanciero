<?php
session_start();
$modulo=$_GET["codigo"];
$_SESSION['modulo']=$modulo;
header("location:index.php?opcion=homeModulo");

?>