<?php

date_default_timezone_set('America/La_Paz');

class Conexion extends PDO {      
    public function __construct() {
        //Sobreescribo el método constructor de la clase PDO.
        try{
            $DATABASE_HOST = "lpsit.ibnorca.org";
            $DATABASE_NAME = "bdifinanciero";
            $DATABASE_PORT = "4606";
            $DATABASE_USER = "ingresobd";
            $DATABASE_PASSWORD = "ingresoibno";
            // Oficial
            parent::__construct('mysql:host='.$DATABASE_HOST.';dbname='.$DATABASE_NAME.';port='.$DATABASE_PORT, $DATABASE_USER, $DATABASE_PASSWORD,array(PDO::ATTR_PERSISTENT => 'TRUE',PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            
        }catch(PDOException $e){
            echo 'Ha surgido un error y no se puede conectar a la base de datos. Detalle: ' . $e->getMessage();
            exit;
        }
    } 
} 

// Verificar Tabla Ventas Normas
function buscarVentaNorma($cod_solicitudfacturacion, $cod_facturaventadetalle){
    $dbh = new Conexion();
    $sql = "SELECT vn.IdVentaNormas, vn.idNorma 
            FROM ibnorca.ventanormas vn
            WHERE vn.idSolicitudfactura = '$cod_solicitudfacturacion'
            OR vn.idFacturaDetalle = '$cod_facturaventadetalle'";
    $stmtVentaNorma = $dbh->prepare($sql);
    $stmtVentaNorma->execute();
    $filasEncontradas = $stmtVentaNorma->rowCount();

    if ($filasEncontradas > 0) {
        // Si se encuentran filas, se obtienen los resultados
        $resultado = $stmtVentaNorma->fetch(PDO::FETCH_ASSOC);
        return $resultado['IdVentaNormas']; // Devuelve IdVentaNormas si se encuentra
    } else {
        return false; // Devuelve false si no se encuentra nada
    }
}

// Variables
$fecha_inicio = empty($_GET['fecha_inicio']) ? date('Y-m-d') : $_GET['fecha_inicio']; 
$fecha_fin    = empty($_GET['fecha_fin']) ? date('Y-m-d') : $_GET['fecha_fin']; 
$mostrar      = empty($_GET['mostrar']) ? 1 : $_GET['mostrar']; 
$claServicio  = empty($_GET['cla_servicio']) ? "" : $_GET['cla_servicio'];

$dbh = new Conexion();

// FACTURAS VENTAS en base a SOLICITUDES DE FACTURACIÓN DETALLE
// codfacturadetalle => cod_solicitudfacturaciondetalle
$sql = "SELECT fv.fecha_factura,fv.codigo,fv.nro_factura,f.codigo as codfacturadetalle, ibnorca.d_abrevclasificador(fvd.cod_area) as area,
        ibnorca.d_abrevclasificador(fv.cod_unidadorganizacional) as oficina, 
            f.cantidad, f.descripcion_alterna ,(((f.cantidad*f.precio)-f.descuento_bob)*(fvd.porcentaje/100)) as importe_total,
        ((((f.cantidad*f.precio)-f.descuento_bob)*.87)*(fvd.porcentaje/100)) as importe_neto, fvd.porcentaje, 
            fv.cod_solicitudfacturacion, fv.razon_social, fv.nit, f.cod_claservicio, fv.cod_tipoobjeto, fv.created_by, f.cantidad, f.precio
        from facturas_ventadetalle f 
        left join ibnorca.claservicios c on f.cod_claservicio=c.IdClaServicio
        inner join facturas_venta fv on f.cod_facturaventa=fv.codigo
        inner join facturas_venta_distribucion fvd on fvd.cod_factura=f.cod_facturaventa
        LEFT JOIN ventanormas_facturas vnf ON vnf.cod_facturaventadetalle = f.codigo
        where fvd.cod_area in (12) 
        and fv.cod_estadofactura<>2 ".
        (empty($claServicio) ? '' : "and f.cod_claservicio = $claServicio")
        ." and fv.cod_solicitudfacturacion != '-100'
        AND DATE(fv.fecha_factura) BETWEEN '$fecha_inicio' AND '$fecha_fin'
        order by fv.fecha_factura, f.codigo DESC"; 
// echo $sql;
// exit;
$stmtFactura = $dbh->prepare($sql);
$stmtFactura->execute();

/**
 * OBTIENE SOLO "ID VENTA NORMA"
 * 1: General
 * 2: ID Venta Norma
 */
$vista_norma = 2;
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .filter-form {
            text-align: center;
            margin-top: 20px;
            font-family: 'Arial', sans-serif; /* Utiliza una fuente legible y formal */
            background-color: #f7f7f7; /* Un color de fondo suave */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .filter-form input[type="date"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .filter-form button {
            padding: 10px 20px;
            background-color: #337ab7; /* Un color azul formal */
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .filter-form button:hover {
            background-color: #235a9d; /* Cambia el color al pasar el mouse */
        }

        .resaltado-verde {
            background-color: #c7f4c3;
            color: #000;
        }

        .resaltado-rojo {
            background-color: #f4c3c3;
            color: #000;
        }
        .cards {
            display: flex;
            justify-content: center;
            margin: 20px;
        }

        .card {
            margin-right: 10px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .green {
            background-color: #c7f4c3;
        }

        .red {
            background-color: #f4c3c3;
        }
        .blue{
            background-color: #3498db;
            color: #fff;
        }

        .table {
            width: 100%;
            margin: 0 auto;
            border-collapse: collapse;
            border: 1px solid #ccc;
            max-height: 400px;
        }
        tr:hover {
            background-color: #c0c0c0;
        }

        .sticky-header {
            position: sticky;
            top: 0;
            background-color: white; /* Ajusta el color de fondo según tu diseño */
            z-index: 1;
        }


        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <form method="GET">
        <div class="filter-form">
            <label for="fecha_inicio">Fecha Inicio: </label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" placeholder="Fecha de inicio" value="<?=$fecha_inicio?>">
            <label for="fecha_fin">Fecha Fin: </label>
            <input type="date" name="fecha_fin" id="fecha_fin" placeholder="Fecha de fin" value="<?=$fecha_fin?>">
            <label for="fecha_fin">Tipo: </label>
            <select name="mostrar" id="mostrar">
                <option value="1" <?= $mostrar == 1 ? 'selected' : '' ?>>Todos</option>
                <option value="2" <?= $mostrar == 2 ? 'selected' : '' ?>>Encontrado</option>
                <option value="3" <?= $mostrar == 3 ? 'selected' : '' ?>>No encontrado</option>
            </select>
            <label for="fecha_fin">ClaServicio: </label>
            <select name="cla_servicio" id="cla_servicio">
                <option value="" selected>Todos</option>
                <?php
                    // CLA SERVICIO, se lista en base al rango de fechas
                    $sqlServicio = "SELECT DISTINCT(fvd.cod_claservicio) as cla_servicio
                            FROM facturas_venta fv
                            LEFT JOIN facturas_ventadetalle fvd ON fvd.cod_facturaventa=fv.codigo
                            Inner join facturas_venta_distribucion dist on dist.cod_factura=fvd.cod_facturaventa
                            where dist.cod_area in (12) 
                            AND fv.cod_estadofactura<>2
                            AND DATE(fv.fecha_factura) BETWEEN '$fecha_inicio' AND '$fecha_fin'
                            and fv.cod_solicitudfacturacion != '-100'
                            ORDER BY cla_servicio DESC"; 
                    // echo $sqlServicio;
                    $stmtServicio = $dbh->prepare($sqlServicio);
                    $stmtServicio->execute();
                    while($rowServicio = $stmtServicio->fetch(PDO::FETCH_ASSOC)){
                ?>
                    <option value="<?= $rowServicio['cla_servicio']; ?>" <?= $rowServicio['cla_servicio'] == $claServicio ? 'selected' : '' ?>><?= $rowServicio['cla_servicio']; ?></option>
                <?php
                    } 
                ?>
            </select>
            <button type="submit">Filtrar</button>
        </div>
    </form>


    <div class="cards">
        <div class="card blue" style="text-align:center;">
            <h2>Total <br>Registros</h2>
            <p id=""><?= $stmtFactura->rowCount() ?></p>
        </div>
        <div class="card green" style="text-align:center;">
            <h2>Total <br>Encontradas</h2>
            <p id="nro_encontrado">0</p>
        </div>
        <div class="card red" style="text-align:center;">
            <h2>Suscripciones no<br>Encontradas</h2>
            <p id="nro_no_encontrado">0</p>
        </div>
    </div>


    <table class="table">
        <thead class="sticky-header">
            <tr>
                <th>FV Detalle</th>
                <th>Id Venta Norma</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $ventasEncontradas = 0;
                $ventasNoEncontradas = 0;
                $nro = 0;
                while ($rowFactura = $stmtFactura->fetch(PDO::FETCH_ASSOC)) {
                    $idVentaNorma = buscarVentaNorma($rowFactura['cod_solicitudfacturacion'], $rowFactura['codfacturadetalle']);
                    if ($idVentaNorma !== false) {
                        $ventasEncontradas++;
                    } else {
                        $ventasNoEncontradas++;
                    }
            ?>
            <tr class="<?= $idVentaNorma == false ? 'resaltado-rojo' : ''; ?>">
                <td style="background-color: #c7f4f9;color: #000;"><?= $rowFactura['codfacturadetalle']; ?></td>
                <td class="<?= !empty($idVentaNorma) ? 'resaltado-verde' : 'resaltado-rojo'; ?>"><?= $idVentaNorma; ?></td>
            </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#nro_encontrado').html(<?=$ventasEncontradas;?>);
        $('#nro_no_encontrado').html(<?=$ventasNoEncontradas;?>);
    });
</script>

</body>
</html>
