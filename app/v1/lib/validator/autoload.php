<?php

require("source/autoload.php");

use Rakit\Validation\Validator;


function validate($inputs = [], $rules = [], $curtomErrorMessages=[])
{
    /*
    $data = [
        'name'                  => 'wshuhyweb',
        'email'                 => 'gduywu',
        'password'              => 'uygy',
        'confirm_password'      => 'dwgsduyhy',
        'avatar'                => 'fdhefhjiuw.jpg',
        'skills'                => [78778,3232,33,"huiwh"]
    ];
    $rules = [
        'name'                  => 'required',
        'email'                 => 'required|email',
        'password'              => 'required|min:6',
        'confirm_password'      => 'required|same:password',
        'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
        'skills'                => 'array',
        'skills.*.id'           => 'required'
    ];

    //optional
    $customErrors=[
        'required' => ':attribute harus diisi',
        'email' => ':email tidak valid'
    ];

    validate($data, $rules);
    */
    
    $returnData["status"] = null;
    $returnData["errors"] = null;
    $validator = new Validator;
    
    if(!empty($errorMessages)){
        $validation = $validator->validate($inputs, $rules);
    }else{
        $validation = $validator->validate($inputs, $rules, $curtomErrorMessages);
    }
    if ($validation->fails()) {
        $errors = $validation->errors();
        $returnData["status"] = "error";
        $returnData["errors"] = $errors->firstOfAll();
    } else {
        $returnData["status"] = "success";
        $returnData["errors"] = [];
    }
    return $returnData;
}



?>