<?php

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

        if (isset($settings['section'])) {
            if ($settings['section'] == 'numbers') {
                $this->exportMap = [
                    'headers' => false,
                    'columns' => [
                        [
                            'name' => 'portNumber',
                            'title' => 'Port Number',
                        ],
                        [
                            'name' => 'number',
                            'title' => 'Number'
                        ]
                    ],
                    'separate' => ' ',
                ];
            }
            $this->exportExtenstion = $settings['exportType'];
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
                $row .= $separate;
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
            return $this->downloadText($this->fileContent, $fileName);
        }
    }

    /**
     * ? Download Text File
     */
    public static function downloadText(String $content, String $fileName)
    {
        $tmpName = tempnam(sys_get_temp_dir(), 'data');
        $file = fopen($tmpName, 'w');

        fwrite($file, $content);
        fclose($file);

        header('Content-Description: File Transfer');
        header('Content-Type: text/txt');
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($tmpName));

        ob_clean();
        flush();
        readfile($tmpName);

        unlink($tmpName);
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
