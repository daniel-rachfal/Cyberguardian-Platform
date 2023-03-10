<?php
/**
 * uploadDB endpoint
 * 
 * work in progress
 * 
 * @author Jack Wilde w20022384
 */ 
include 'database.php';
include 'endpoint.php';

Class UploadDB extends Endpoint
{
    private $fileName;
    private $visibility;
    private $createdBy;

    protected function initialiseSQL() {
        //get current unix time
        date_default_timezone_set('Europe/London');
        $date = time();
        $this->setFileName($_POST['fileName']);

        //query for test purposes
        $query="SELECT * FROM files";

        $query2= 
        (
        "INSERT INTO files 
        (fileName, visibility, createdBy, createdAt) 
        VALUES (:fileName, :visibility, :createdBy, ".$date.")"
        );

        //set PDO params
        $params = [];
        //$params[':fileName'] = $this->getFileName();
        //$params[':visibility'] = 1;
        //$params[':createdBy'] = 1;
        $this->setSQL($query);
        $this->setSQLParams($params);
    }

    private function validateRequestMethod($method) {
        if ($_SERVER['REQUEST_METHOD'] != $method){
            throw new ClientErrorException("Invalid request method", 405);
        }
    }

    protected function setFileName($fileName){
        $this->fileName = $fileName;
    }

    protected function getFileName(){
        return $this->fileName;
    }

    protected function setVisibility($visibility){
        $this->visibility = $visibility;
    }

    protected function getVisibility(){
        return $this->visibility;
    }

    protected function setCreatedBy($createdBy){
        $this->createdBy = $createdBy;
    }

    protected function getCreatedBy(){
        return $this->createdBy;
    }
}
