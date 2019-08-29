<?php
use Slam\Excel\Helper as ExcelHelper;

require __DIR__ . '/vendor/autoload.php';

// Being an Iterator, the data can be any dinamically generated content
// for example a PDOStatement set on unbuffered query
$users = new ArrayIterator([
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
]);

$columnCollection = new ExcelHelper\ColumnCollection([
    new ExcelHelper\Column('column_1',  'User',     10,     new ExcelHelper\CellStyle\Text()),
    new ExcelHelper\Column('column_2',  'Amount',   15,     new ExcelHelper\CellStyle\Amount()),
    new ExcelHelper\Column('column_3',  'Date',     15,     new ExcelHelper\CellStyle\Date()),
]);

$filename = sprintf('%s/my_excel_%s.xls', __DIR__, uniqid());

$phpExcel = new ExcelHelper\TableWorkbook($filename);
$worksheet = $phpExcel->addWorksheet('My Users');

$table = new ExcelHelper\Table($worksheet, 0, 0, 'My Heading', $users);
$table->setColumnCollection($columnCollection);

$phpExcel->writeTable($table);
$phpExcel->close();