<?php

/**
 * This Class Takes An Array Of Settings, And Other Array Of Data,
 * Settings Array Should Contain
 * @param bool headers      | The File Should Have Headers
 * @param array columns     | List Of Allowed Columns, ['name' => The Key In Array, 'title' => Title Of The Column For Headers]
 * @param string separate   | Required For Text Files
 */

namespace devPackages\dataToFiles;

class Export
{
    /**
     * ? Download File Extenstion
     */
    private $exportExtenstion = 'csv';

    /**
     * ? File Name
     */
    private $fileName = 'defaultName';

    /**
     * ? Export Map Files Path & Name
     */
    private $exportFilePathName;

    /**
     * ? Download Map Settings
     */
    private $exportMap = [
        'headers' => true,
        'columns' => [],
        'separate' => ' ',
    ];
    /**
     * ? Download Data, The User Input
     */
    private $data;
    /**
     * ? The File Content, That'd Be Downloaded
     */
    private $fileContent;

    /**
     * ? Prepare The Class Settings
     */
    public function setUp(array $settings, array $data): Export
    {
        if (isset($settings['exportType'])) {
            $this->exportExtenstion = $settings['exportType'];
        }

        if (isset($settings['fileName']) && !empty($settings['fileName'])) {
            $this->fileName = $settings['fileName'];
        }

        $this->exportFilePathName = $settings['exportMapFile'] ?? __DIR__ . "/../tests/customMaps.json";

        if (isset($settings['customMap'])) {
            $map = json_decode(file_get_contents($this->exportFilePathName), true)[$settings['customMap']] ?? false;
            if ($map) {
                $this->exportMap = $map;
            } else {
                throw new \Exception("Custom Map Not Found In " . $this->exportFilePathName . " File");
            }
        } else {
            $this->exportMap = array_merge($this->exportMap, $settings['exportMap']);
        }

        $this->data = $data;

        return $this;
    }

    /**
     * ? Remap The Data, To Be Validate Them, And Making Them Have The Same Format
     */
    public function mapData(): Export
    {
        $mapedData = [];
        if ($this->exportMap['headers']) {
            $headers = [];
            foreach ($this->exportMap['columns'] as $column) {
                $headers[$column['name']] = $column['title'];
            }

            $mapedData[] = $headers;
        }


        foreach ($this->data as $data) {
            $row = [];
            $data = (array)$data;
            foreach ($this->exportMap['columns'] as $column) {
                $row[$column['name']] = $data[$column['name']];
            }
            $mapedData[] = $row;
        }

        $this->data = $mapedData;

        return $this;
    }

    /**
     * ? Generate The File Content Action Start
     */
    public function generateFileContent(): Export
    {
        $x = 0;
        foreach ($this->data as $data) {
            if ($this->exportExtenstion == 'txt') {
                $this->fileContent .= $this->generateTxtFileContent($data, $x, $this->exportMap['separate']);
            } elseif ($this->exportExtenstion == 'csv') {
                $this->fileContent .= $this->generateCSVFileContent($data, $x, $this->exportMap['separate']);
            }

            $x++;
        }
        return $this;
    }

    /**
     * ? Create The Download Text For Text Files Download
     */
    private static function generateTxtFileContent(array $data, Int $currentRecord, String $separate): String
    {
        $row = "";
        $totalRows = count($data);
        $x = 0;
        if ($currentRecord) {
            $row .= "\n";
        }
        foreach ($data as $d) {
            if ($x !== 0 && $x <= $totalRows) {
                $row .= ',';
            }

            $row .= $d;

            $x++;
        }

        return $row;
    }

    /**
     * ? Create The Download Text For CSV Files Download
     */
    private static function generateCSVFileContent(array $data, Int $currentRecord, String $separate): String
    {
        $row = "";
        $totalRows = count($data);
        $x = 0;
        if ($currentRecord) {
            $row .= "\n";
        }
        foreach ($data as $d) {
            if ($x !== 0 && $x <= $totalRows) {
                $row .= ',' . $separate;
            }

            $row .= $d;

            $x++;
        }

        return $row;
    }

    /**
     * ? Start Downloading Action
     */
    public function download()
    {
        $fileName = $this->fileName . '.' . $this->exportExtenstion;
        if ($this->exportExtenstion == 'txt') {
            $file = $this->downloadText($this->fileContent);
        } elseif ($this->exportExtenstion == 'csv') {
            $file = $this->downloadCSV($this->fileContent);
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $file['contentType']);
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file['file']));

        ob_clean();
        flush();
        readfile($file['file']);

        unlink($file['file']);
    }

    /**
     * ? Download Text File
     */
    public static function downloadText(String $content): array
    {
        $tmpName = tempnam(sys_get_temp_dir(), 'data');
        $file = fopen($tmpName, 'w');

        fwrite($file, $content);
        fclose($file);

        return [
            'contentType' => 'text/txt',
            'file'        => $tmpName,
        ];
    }

    /**
     * ? Download CSV
     */
    public static function downloadCSV(String $content): array
    {
        $tmpName = tempnam(sys_get_temp_dir(), 'data');
        $file = fopen($tmpName, 'w');

        fwrite($file, $content);
        fclose($file);

        return [
            'contentType' => 'application/csv',
            'file'        => $tmpName,
        ];
    }

    /**
     * ? Return The File Contents
     */
    public function returnFileContent(): String
    {
        return $this->fileContent;
    }

    /**
     * ? Get File Extenstion
     */
    public function fileExtenstion(): String
    {
        return $this->exportExtenstion;
    }

    /**
     * ? Get File Name
     */
    public function fileName(): String
    {
        return $this->fileName;
    }
}
