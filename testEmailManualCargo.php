<?php
	require_once 'functions.php';

    date_default_timezone_set('America/La_Paz');

    $resp = enviarCorreoManualCargo(17);

    echo $resp;
    echo "____holaa";