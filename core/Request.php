<?php
class Request{
    public $company_id;
    public $branch_id;
    public $location_id;
    public $created_by;
    public $updated_by;
    public $body;
    public $post;
    public $get;
    public $files;

    function __construct(){
        global $company_id, $branch_id, $location_id, $created_by, $updated_by;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->post = $_POST;
        $this->get = $_GET;
        $this->files = $_FILES;
        $this->body=[];
        if (($reqBodyData=json_decode(file_get_contents('php://input'), true)) != null) {
            $this->body=$reqBodyData;
        }
    }

    public function file($name){
        return isset($this->files[$name]) ? $this->files[$name] : false;
    }
    public function post($name){
        return isset($this->post[$name]) ? $this->post[$name] : false;
    }
    public function get($name){
        return isset($this->get[$name]) ? $this->get[$name] : false;
    }
    public function body($name){
        return isset($this->body[$name]) ? $this->body[$name] : false;
    }
}
?>