<?php
class BaseController extends CoreDatabase{
    protected $Req;
    function __construct()
    {
        $this->Req=new Request();
    }
}