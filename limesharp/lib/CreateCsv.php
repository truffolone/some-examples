<?php 

class CreateCsv {

    private $_data = array();
    private $_headers = array();
    private $_folder = "csv";
    private $_basePath = __DIR__ . "/../";
    private $_csvName = "file.csv";

    public function __construct() {

    }

    /*
     * @parameters boid
     * returns $this
     */
    public function createCsv() {
        $completePath = $this->_basePath . $this->_folder;
        $this->_createFolder($completePath);

        if(count($this->_headers) > 0) {
            array_unshift($this->_data, $this->_headers);
        }

        $fp = fopen($completePath . "/" . $this->_csvName, 'w');

        if(!$fp) {
            throw new CustomException("Can't Create CSV File", "It was impossible to create the csv file at " . $completePath . "/" . $this->_csvName);
        }

        foreach ($this->_data as $fields) {
            if(!fputcsv($fp, $fields)) {
                throw new CustomException("Can't Write CSV File", "It was impossible to write into the the csv file");
            }
        }

        return $this;
    }

    /*
     * @parameters array $headers
     * returns $this
     */
    public function setHeaders(array $headers) {
        $this->_headers = $headers;
        return $this;
    }

    /*
     * @parameters array $data
     * returns $this
     */
    public function setData(array $data) {
        $this->_data = $data;
        return $this;
    }

    /* 
     * @parameters String $folder
     * returns $this
     */
    public function setCsvFolder($folder) {
        $this->_folder = $folder;
        return $this;
    }

    /* 
     * @parameters String $csvName
     * returns $this
     */
    public function setCsvName($csvName) {
        if(substr($csvName, -4) !== ".csv") {
            $csvName = $csvName . ".csv";
        }
        $this->_csvName = $csvName;
        return $this;
    }

    /* 
     * @parameters String $folderBasePath
     * returns $this
     */
    public function setCsvBP($folderBasePath) {
        $this->_basePath = $folderBasePath;
        return $this;
    }

    /*
     * @parameters String $folderPath
     * returns void
     */
    private function _createFolder($folderPath) {
        if (!file_exists($folderPath)) {
            if(!mkdir($folderPath, 0777, true)) {
                throw new CustomException("Error Creating CSV Folder", "folder " . $folderPath . " couldn't be created");
            }
        }
    }

}