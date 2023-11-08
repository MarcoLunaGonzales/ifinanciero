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
/**
 * Función para buscar en el array por la propiedad
 * @param ArrayVentaNorma
 * @return Bolean Codigo de Factura Venta Detalle
 */
function buscarArrayFactura($array, $cod_facturaventadetalle) {
    foreach ($array as $elemento) {
        if ($elemento['idFacturaDetalleFinanciero'] == $cod_facturaventadetalle) {
            return $elemento['IdVentaNormas'];
        }
    }
    return '';
}

// Variables
$fecha_inicio = empty($_GET['fecha_inicio']) ? date('Y-m-d') : $_GET['fecha_inicio']; 
$fecha_fin    = empty($_GET['fecha_fin']) ? date('Y-m-d') : $_GET['fecha_fin']; 
$mostrar      = empty($_GET['mostrar']) ? 1 : $_GET['mostrar']; 
$claServicio  = empty($_GET['cla_servicio']) ? "" : $_GET['cla_servicio'];

$dbh = new Conexion();
// FACTURA VENTAS NORMAS
$sql = "SELECT DISTINCT f.codigo AS codigo_factura, f.fecha_factura, f.nro_factura, f.nit, f.razon_social, 
        if(f.cod_solicitudfacturacion = -100, 'Tienda',NULL) AS Origen 
        ,f.cod_solicitudfacturacion
        , v.* 
        , n.abreviatura AS codigoNorma 
        , d.codigo AS idFacturaDetalleFinanciero
        , d.descripcion_alterna 
        FROM bdifinanciero.facturas_venta f 
        INNER JOIN ibnorca.ventanormas v ON f.cod_solicitudfacturacion = v.idSolicitudfactura 
        INNER JOIN bdifinanciero.v_normas n ON n.codigo=v.idNorma 
        LEFT JOIN bdifinanciero.facturas_ventadetalle d ON f.codigo = d.cod_facturaventa 
        and d.descripcion_alterna LIKE (CONCAT('%',n.abreviatura COLLATE utf8_general_ci,'%'))
        WHERE 
        DATE(f.fecha_factura) BETWEEN '$fecha_inicio' AND '$fecha_fin'  
        and f.cod_estadofactura<>2 
        and f.cod_area in (12) 
        and f.cod_solicitudfacturacion <> -100 
        and v.fecha LIKE ('%2023%')
        and v.Catalogo = 'N' 

        GROUP BY n.abreviatura, d.codigo 

        UNION ALL 

        SELECT DISTINCT f.codigo AS codigo_factura, f.fecha_factura, f.nro_factura, f.nit, f.razon_social, 
        if(f.cod_solicitudfacturacion = -100, 'Tienda',NULL) AS Origen 
        ,f.cod_solicitudfacturacion
        , v.* 
        , n.abreviatura AS codigoNorma 
        , d.codigo AS idFacturaDetalleFinanciero
        , d.descripcion_alterna 
        FROM bdifinanciero.facturas_venta f 
        INNER JOIN ibnorca.ventanormas v ON f.cod_solicitudfacturacion = v.idSolicitudfactura 
        INNER JOIN bdifinanciero.v_normas_int n ON n.codigo=v.idNorma 
        LEFT JOIN bdifinanciero.facturas_ventadetalle d ON f.codigo = d.cod_facturaventa 
        and d.descripcion_alterna LIKE (CONCAT('%',n.abreviatura COLLATE utf8_general_ci,'%'))
        WHERE 
        DATE(f.fecha_factura) BETWEEN '$fecha_inicio' AND '$fecha_fin' 
        and f.cod_estadofactura<>2 
        and f.cod_area in (12) 
        and f.cod_solicitudfacturacion <> -100 
        and v.fecha LIKE ('%2023%')
        and v.Catalogo = 'I' 

        GROUP BY n.abreviatura, d.codigo 

        UNION ALL 

        SELECT DISTINCT f.codigo AS codigo_factura, f.fecha_factura, f.nro_factura, f.nit, f.razon_social, 
        if(f.cod_solicitudfacturacion = -100, 'Tienda',NULL) AS Origen 
        ,f.cod_solicitudfacturacion
        , v.* 
        , n.reference AS codigoNorma 
        , d.codigo AS idFacturaDetalleFinanciero
        , d.descripcion_alterna 
        FROM bdifinanciero.facturas_venta f 
        INNER JOIN ibnorca.ventanormas v ON f.cod_solicitudfacturacion = v.idSolicitudfactura 
        INNER JOIN ibnorca_entidades.isos n ON n.iso_id=v.idNorma 
        LEFT JOIN bdifinanciero.facturas_ventadetalle d ON f.codigo = d.cod_facturaventa 
        and d.descripcion_alterna LIKE (CONCAT('%',n.reference,'%'))
        WHERE 
        DATE(f.fecha_factura) BETWEEN '$fecha_inicio' AND '$fecha_fin' 
        and f.cod_estadofactura<>2 
        and f.cod_area in (12) 
        and f.cod_solicitudfacturacion <> -100 
        and v.fecha LIKE ('%2023%')
        and v.Catalogo = 'ISO' 

        GROUP BY n.reference, d.codigo 

        ORDER BY fecha_factura"; 
// echo $sql;
$stmtVentaNorma = $dbh->prepare($sql);
$stmtVentaNorma->execute();

$resultadosVentaNorma = array();
while ($row = $stmtVentaNorma->fetch(PDO::FETCH_ASSOC)) {
    $objeto = array(
        "codigo_factura"  => $row['codigo_factura'],
        "nro_factura"     => $row['nro_factura'],
        "IdVentaNormas"              => $row['IdVentaNormas'],
        "idFacturaDetalleFinanciero" => $row['idFacturaDetalleFinanciero']
    );
    $resultadosVentaNorma[] = $objeto;
}

// FACTURAS VENTAS
$sql = "SELECT fv.fecha_factura,fv.codigo,fv.nro_factura,f.codigo as codfacturadetalle, ibnorca.d_abrevclasificador(fvd.cod_Area) as area,
        ibnorca.d_abrevclasificador(fv.cod_unidadorganizacional) as oficina, 
            f.cantidad, f.descripcion_alterna ,(((f.cantidad*f.precio)-f.descuento_bob)*(fvd.porcentaje/100)) as importe_total,
        ((((f.cantidad*f.precio)-f.descuento_bob)*.87)*(fvd.porcentaje/100)) as importe_neto, fvd.porcentaje, 
            fv.cod_solicitudfacturacion, fv.razon_social, fv.nit, f.cod_claservicio, fv.cod_tipoobjeto, fv.created_by
        from facturas_ventadetalle f 
        left join ibnorca.claservicios c on f.cod_claservicio=c.IdClaServicio
        inner join facturas_venta fv on f.cod_facturaventa=fv.codigo
        inner join facturas_venta_distribucion fvd on fvd.cod_factura=f.cod_facturaventa
        where fvd.cod_area in (12) 
        and fv.cod_estadofactura<>2 ".
        (empty($claServicio) ? '' : "and f.cod_claservicio = $claServicio")
        ." and fv.cod_solicitudfacturacion != '-100'
        AND DATE(fv.fecha_factura) BETWEEN '$fecha_inicio' AND '$fecha_fin'
        order by fv.fecha_factura"; 
// echo $sql;
$stmtFactura = $dbh->prepare($sql);
$stmtFactura->execute();

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
            margin-right: 10px;
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
            <h2>Total no<br>Encontradas</h2>
            <p id="nro_no_encontrado">0</p>
        </div>
    </div>


    <table class="table">
        <thead class="sticky-header">
            <tr hidden>
                <th>#</th>
                <th>Código FV</th>
                <th>Número de Factura</th>
                <th>Código de FV Detalle</th>
                <th>Tipo Objeto</th>
                <th>Usuario</th>
                <th>NIT</th>
                <th>Razón Social</th>
                <th>ClaServicio</th>
                <th>Fecha de Factura</th>
                <th>Área</th>
                <th>Oficina</th>
                <th>Cantidad</th>
                <th>Descripción Alterna</th>
                <th>Importe Total</th>
                <th>Importe Neto</th>
                <th>Porcentaje</th>
                <th>Código de SF</th>
                <th>Verificación<br> Factura / Norma</th>
                <th>IdVentaNorma</th>
            </tr>
            <tr>
                <!-- <th>#</th> -->
                <th>Código de FV Detalle</th>
                <th>IdVentaNorma</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nro_encontrado = 0;
                $nro_no_encontrado = 0;
                $nro = 0;
                while ($rowFactura = $stmtFactura->fetch(PDO::FETCH_ASSOC)) {
                    $idVentaNorma      = buscarArrayFactura($resultadosVentaNorma, $rowFactura['codfacturadetalle']);
                    $facturaValida     = empty($idVentaNorma) ? false : true;
                    $nro_encontrado    = $facturaValida ? ($nro_encontrado + 1) : $nro_encontrado;
                    $nro_no_encontrado = !$facturaValida ? ($nro_no_encontrado + 1) : $nro_no_encontrado;
                    if(($mostrar == 1) || ($mostrar == 2 && $facturaValida) || ($mostrar == 3 && !$facturaValida)){
                        $nro++;
            ?>
            <tr class="<?= !$facturaValida ? 'resaltado-rojo' : ''; ?>" hidden>
                <td><?=$nro;?></td>
                <td style="background-color: #c7f4f9;color: #000;"><?= $rowFactura['codigo']; ?></td>
                <td><?= $rowFactura['nro_factura']; ?></td>
                <td style="background-color: #c7f4f9;color: #000;"><?= $rowFactura['codfacturadetalle']; ?></td>
                <td><?= $rowFactura['cod_tipoobjeto']; ?></td>
                <td><?= $rowFactura['created_by']; ?></td>
                <td><?= $rowFactura['nit']; ?></td>
                <td><?= $rowFactura['razon_social']; ?></td>
                <td style="background-color: #B0B0B0;color: #000;"><?= $rowFactura['cod_claservicio']; ?></td>
                <td><?= $rowFactura['fecha_factura']; ?></td>
                <td><?= $rowFactura['area']; ?></td>
                <td><?= $rowFactura['oficina']; ?></td>
                <td><?= $rowFactura['cantidad']; ?></td>
                <td><?= $rowFactura['descripcion_alterna']; ?></td>
                <td><?= $rowFactura['importe_total']; ?></td>
                <td><?= $rowFactura['importe_neto']; ?></td>
                <td><?= $rowFactura['porcentaje']; ?></td>
                <td style="background-color: #FFD699;;color: #000;"><?= $rowFactura['cod_solicitudfacturacion']; ?></td>
                <td class="<?= $facturaValida ? 'resaltado-verde' : 'resaltado-rojo'; ?>"><b><?=$facturaValida ? 'Encontrado' : 'No Encontrado';?></b></td>
                <td><?= $idVentaNorma; ?></td>
            </tr>
            <tr class="<?= !$facturaValida ? 'resaltado-rojo' : ''; ?>">
                <!-- <td><?=$nro;?></td> -->
                <td style="background-color: #c7f4f9;color: #000;"><?= $rowFactura['codfacturadetalle']; ?></td>
                <td><?= empty($idVentaNorma) ? 0 : $idVentaNorma; ?></td>
            </tr>
            <?php 
                    }
                }
            ?>
        </tbody>
    </table>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#nro_encontrado').html(<?=$nro_encontrado;?>);
        $('#nro_no_encontrado').html(<?=$nro_no_encontrado;?>);
    });
</script>

</body>
</html>
