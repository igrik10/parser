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
    public function __construct($pathToFile, $pathToConfig)
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

        if (self::isXML()) {

        }

        return null;
    }

    /**
     * @return bool
     */
    private function isXML()
    {
        return $this->getExtension($this->pathToFile) ? true : false;
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
                        $attrArray[] = $node->attributes()->$attribute;
                    }
                    $bookArray['book_' . $i++] = $attrArray;
                }
            }
        }
        $this->xml = $bookArray;

        return $this->xml;
    }


    private function isYML(){
        $yml = yaml_parse_file($this->pathToFile);
        if (!empty($yml) && is_array($yml)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function parse()
    {
        return self::getLocalData();
    }

}