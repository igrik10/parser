<?php

class Parser
{

    private $pathToFile;
    private $config;
    private $xml;
    private $csv;
    private $yml;

    /**
     * @param $pathToFile
     * @param $pathToConfig
     * @throws Exception
     */
    public function __construct($pathToFile, $pathToConfig = false)
    {
        if (!file_exists($pathToFile) && !file_exists($pathToConfig)) {
            throw new Exception ("File not found!");
        } else {
            $this->pathToFile = $pathToFile;
            $this->config = $pathToConfig;
        }
        $this->xml = 'XML is not defined';
        $this->csv = 'CSV is not defined';
        $this->yml = 'YML is not defined';
    }

    /**
     * @return bool|null
     */
    private function getLocalData()
    {
        if (self::isXML()) {
            return self::xmlToArray();
        }

        if (self::isYML()) {
            return self::ymlToArray();
        }

        if (self::isCSV()) {
            return self::csvToArray();
        }

        return null;
    }

    /**
     * @return bool
     */
    private function isXML()
    {
        return $this->getExtension($this->pathToFile) == 'xml' ? true : false;
    }

    /**
     * @return bool
     */
    private function isYML()
    {
        return $this->getExtension($this->pathToFile) == 'yml' ? true : false;
    }

    /**
     * @return bool
     */
    private function isCSV()
    {
        return $this->getExtension($this->pathToFile) == 'csv' ? true : false;
    }

    /**
     * @param $filename
     * @return mixed
     */
    private function getExtension($filename)
    {
        $path_info = pathinfo($filename);

        return $path_info['extension'];
    }


    /**
     * @return array|string
     */
    private function xmlToArray() {
        $config = include_once($this->config);
        $config = $config['parser'];
        $bookArray = [];
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($this->pathToFile);
        if (empty(libxml_get_errors())) {
            $i = 1;
            foreach ($config as $key => $book) {
                $xmlValues = $xml->xpath($book['source']);
                foreach ($xmlValues as $node) {
                    $attrArray = [];
                    foreach ($book['attributes'] as $key => $attribute) {
                        $attrArray[$attribute] = current($node->attributes()->$attribute);
                    }
                    $bookArray['book_' . $i++] = $attrArray;
                }
            }
        }
        $this->xml = $bookArray;

        return $this->xml;
    }

    /**
     * @return string
     */
    private function ymlToArray(){
        $yml = yaml_parse_file($this->pathToFile);
        $this->yml = $yml;

        return $this->yml;
    }

    /**
     * @return array|string
     */
    private function csvToArray() {
        $csv = fopen($this->pathToFile,'r');
        $array = [];
        $header = null;
        while ($row = fgetcsv($csv)) {
            if ($header === null) {
                $header = $row;
                continue;
            }
            $array[] = array_combine($header, $row);
        }
        fclose($csv);
        $this->csv = $array;

        return $this->csv;
    }

    /**
     * @return mixed
     */
    public function parse()
    {
        return self::getLocalData();
    }

}