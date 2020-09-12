<?php

require_once __DIR__ . "/../src/Export.php";

use devPackages\dataToFiles\Export;

$data = [
    [
        'name'      => 'Troy',
        'age'       => 18,
        'country'   => 'USA'
    ],
    [
        'name'      => 'Daniel',
        'age'       => 20,
        'country'   => 'Spain'
    ]
];

$columns = [
    [
        'name' => 'name',
        'title' => 'User Name'
    ],
    [
        'name' => 'age',
        'title' => 'Age'
    ],
    [
        'name' => 'country',
        'title' => 'Country'
    ],
];

$exportInstance = new Export();

$exportInstance->setUp([
    // 'exportMap'     => [
    //     'headers'       => true,
    //     'columns'       => $columns,
    //     'separate'      => '   ',
    // ],
    'exportType'    => 'txt',
    'fileName'      => 'Export Test',
    'exportMapFile' => __DIR__ . "/../tests/customMaps.json",
    'customMap'     => 'user'
], $data)->mapData()->generateFileContent()->download();

var_dump($exportInstance);
