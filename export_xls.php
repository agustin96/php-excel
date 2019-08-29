<?php

use Slam\Excel\Helper as ExcelHelper;

require __DIR__ . '/vendor/autoload.php';

// Being an Iterator, the data can be any dinamically generated content
// for example a PDOStatement set on unbuffered query

$database = "maricel";

require 'connections/conexion.php';

$registros = $base->query("SELECT

                        xt_cierre1.fecha_real			AS fecha,
                        SUM(xt_cierre1.total)			AS total

                        FROM xt_cierre1
                                        
                        WHERE id_local=1

                        GROUP BY fecha , id_local
                        ORDER BY fecha LIMIT 10")->fetchAll(PDO::FETCH_OBJ);


foreach ($registros as $objeto) :
    $fecha = $objeto->fecha;
    $total = $objeto->total;
    $data[] = [
        'column_1' => $fecha,
        'column_2' => $total,
        'column_3' => '2017-05-08',
    ];
endforeach;

/* $json = json_decode(file_get_contents('php://input'), true);
$arrayArticulos = $json[0];

foreach ($arrayArticulos as $elemento) {
    $lista = $elemento[nombre_lista];
    $codigo = $elemento[codigo];
    $detalle = $elemento[detalle];
    $data[] = [
        'column_1' => $lista,
        'column_2' => $codigo,
        'column_3' => $detalle,
    ];
} */

$users = new ArrayIterator($data);

$columnCollection = new ExcelHelper\ColumnCollection([
    new ExcelHelper\Column('column_1',  'Lista',     10,     new ExcelHelper\CellStyle\Text()),
    new ExcelHelper\Column('column_2',  'Codigo',   15,     new ExcelHelper\CellStyle\Amount()),
    new ExcelHelper\Column('column_3',  'Detalle',     15,     new ExcelHelper\CellStyle\Text()),
]);

$filename = sprintf('%s/my_excel_%s.xls', __DIR__, uniqid());

//echo $filename;

$phpExcel = new ExcelHelper\TableWorkbook($filename);
$worksheet = $phpExcel->addWorksheet('My Users');

$table = new ExcelHelper\Table($worksheet, 0, 0, 'My Heading', $users);
$table->setColumnCollection($columnCollection);

$phpExcel->writeTable($table);
$phpExcel->close();

$respuesta = new \stdClass();
$respuesta->estado = true;
$respuesta->mensaje = $filename;