<?php
class ApiController extends BaseController{
    protected $Request;
    function __construct(){
        $this->Request=new Request();
    }
}
?>