<?php

    require_once '../layouts/bodylogin.php';
    require_once '../conexion.php';
    require_once '../functionsGeneral.php';
    require_once 'configModule.php';
    require_once '../libs/PHPExcel/Classes/PHPExcel.php';
    
    /*####################################################*/
    
    $dbh = new Conexion();
    // cod refrigerio
    $cod_ref = $_POST['cod_ref'];
    $cod_mes = $_POST['cod_mes'];
    // Opción
    $opcion  = $_POST['cod_opcion'];

    $fechahora   = date("dmy.Hi");
    $archivoName = $fechahora.$_FILES['file']['name'];
    if ($_FILES['file']["error"] > 0){
        echo "Error: " . $_FILES['file']['error'] . "<br>";
    }
    else{
        $path = $archivoName;
        move_uploaded_file($_FILES['file']['tmp_name'], $path);		
        
        // Preparación de Datos
        $inputFileType  = PHPExcel_IOFactory::identify($path);
        $objReader      = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel    = $objReader->load($path);

        // Eliminar Archivo
        unlink($path);
        // Nro de Hoja de calculo
        $sheet          = $objPHPExcel->getSheet(0); 
        $highestRow     = $sheet->getHighestRow(); 
        $highestColumn  = $sheet->getHighestColumn();
        
        $values          = [];

        for ($row = 2; $row <= $highestRow; $row++){
            
            $ci = $sheet->getCell("A".$row)->getValue();
            
            $b  = $sheet->getCell("B".$row)->getValue();
            
            $dias_mes        = $sheet->getCell("C".$row)->getValue();
            $dias_refrigerio = $sheet->getCell("D".$row)->getValue();
            $monto_dia       = $sheet->getCell("E".$row)->getValue();
            /* Obtener cod_personal */
            $sql = "SELECT p.codigo
                    FROM personal p
                    WHERE identificacion = '$ci'
                    LIMIT 1";
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $ci_personal    = 1;
            $cod_personal   = 0;
            while ($row1 = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cod_personal = $row1['codigo'];
            }

            $values[] = "('$cod_ref', '$cod_personal', '$dias_refrigerio', '$monto_dia', 1)";
        }
        
        // Eliminación de Registros de la misma gestión
        if($opcion == 2){
            $sqlDel = "DELETE FROM refrigerios_detalle WHERE cod_refrigerio = $cod_ref";
            $stmt   = $dbh->prepare($sqlDel);
            $stmt->execute();
        }

        // Registro de Planificación de Cursos Alumnos
        $sqlInsert = "INSERT INTO refrigerios_detalle (cod_refrigerio, cod_personal, dias_asistidos, monto, cod_estadoreferencial) VALUES\n" . implode(",\n", $values);
        $stmt      = $dbh->prepare($sqlInsert);
        $flagSuccess = $stmt->execute();
    }
    showAlertSuccessError($flagSuccess,"../index.php?opcion=listRefrigerioDetalle&cod_ref=$cod_ref&cod_mes=$cod_mes");

?>
