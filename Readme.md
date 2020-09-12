# Convert Your Array Into Files

## Install The Library
```sh
composer require dev-packages/data-to-files
```
## How To Use It
1. By Using External Maps Files  
    1. Your PHP file

        ```php
        <?php

        // require composer autoloader
        require __DIR__ . '/vendor/autoload.php';

        // use the class
        use devPackages\dataToFiles\Export;

        // the data that would be converted into a file
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
        $exportInstance = new Export();

        $exportInstance->setUp([
            'exportType'    => 'txt', // export file type
            'fileName'      => 'Export Test', // file name
            // the location of the custom maps file
            'exportMapFile' => __DIR__ . "/path/to/map/file/customMaps.json", 
            // the map name in the custom maps file
            'customMap'     => 'user'
        ], $data)->mapData()->generateFileContent()->download();

        ```
    2. Your Map File. **Map File Must Be JSON**

        ```json
        {
            "user": {
                "headers": true,
                "columns": [
                    {
                        "name": "name",
                        "title": "Name"
                    },
                    {
                        "name": "age",
                        "title": "Age"
                    },
                    {
                        "name": "country",
                        "title": "Country"
                    }
                ],
                "separate": "   "
            }
        }
        ```
2. Without Map Files
    ```php
    <?php
    // require composer autoloader
    require __DIR__ . '/vendor/autoload.php';

    // use the class
    use devPackages\dataToFiles\Export;

    // the data that would be converted into a file
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

    // export map columns
    $columns = [
        [
            // column key
            'name' => 'name',
            // column title for headers
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
        // the full export map
        'exportMap'     => [
            // use headers or no
            'headers'       => true,
            // available columns
            'columns'       => $columns,
            // separate for csv or txt files
            'separate'      => '   ',
        ],
        // export file type
        'exportType'    => 'txt',
        // exported file title
        'fileName'      => 'Export Test',
    ], $data)->mapData()->generateFileContent()->download();

    ```