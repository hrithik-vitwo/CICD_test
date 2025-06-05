<?php
include("../../../app/v1/functions/common/func-common.php");
require_once("api-common-func.php");


function othersInfo($country, $type)
{

    if ($type == 'so') {
        $formJson['input'] = [
            "customerOrderNo" => "<input type='text'>",
            "date" => "<input type='date'>"
        ];
    } else {
        $formJson['field'] = [
            "dropdown" => '<select name="companyConfigId" class="form-control" id="config">
                            <option value="">Select One</option>
                            <option value="41">test /test@mail.com / 9876543210</option>
                             </select>',

        ];
    }
}
