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

/* $users = new ArrayIterator([
    [
        'column_1' => 'John',
        'column_2' => '123.45',
        'column_3' => '2017-05-08',
    ],
    [
        'column_1' => 'Mary',
        'column_2' => '4321.09',
        'column_3' => '2018-05-08',
    ],
]); */

$users = new ArrayIterator([$data]);

$columnCollection = new ExcelHelper\ColumnCollection([
    new ExcelHelper\Column('column_1',  'User',     10,     new ExcelHelper\CellStyle\Text()),
    new ExcelHelper\Column('column_2',  'Amount',   15,     new ExcelHelper\CellStyle\Amount()),
    new ExcelHelper\Column('column_3',  'Date',     15,     new ExcelHelper\CellStyle\Date()),
]);

$filename = sprintf('%s/my_excel_%s.xls', __DIR__, uniqid());

//echo $filename;

$phpExcel = new ExcelHelper\TableWorkbook($filename);
$worksheet = $phpExcel->addWorksheet('My Users');

$table = new ExcelHelper\Table($worksheet, 0, 0, 'My Heading', $users);
$table->setColumnCollection($columnCollection);

$phpExcel->writeTable($table);
$phpExcel->close();

echo $filename;
