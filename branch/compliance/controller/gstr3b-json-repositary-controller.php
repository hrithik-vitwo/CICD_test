<?php
// require_once("../../../app/v1/connection-branch-admin.php");

class Gstr3bJsonRepository
{
    private $periodGstr3b = null;
    private $periodStart = null;
    private $periodEnd = null;
    private $company_id;
    private $branch_id;
    private $location_id;
    private $created_by;
    private $updated_by;
    private $branch_gstin;
    private $branch_gstin_code;
    private $dbObj;


    function __construct($periodGstr3b = null, $periodStart = null, $periodEnd = null)
    {
        $this->periodGstr3b = $periodGstr3b;
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $branch_gstin;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->branch_gstin = $branch_gstin;
        $this->branch_gstin_code = substr($branch_gstin, 0, 2);
        $this->dbObj = new Database();
    }

    function generate()
    {
        $jsonObj = [
            "gstin" => $this->branch_gstin,
            "ret_period" => $this->periodGstr3b,
        ];

        // Decode JSON strings from the database into PHP arrays or objects
        $jsonObj['sup_details'] = ($this->getSupDetails($this->periodGstr3b));

        // Decode the 'inter_sup' value properly
        $interSupDetails = $this->getIntersupDetails($this->periodGstr3b);
        $jsonObj['inter_sup'] = ($interSupDetails);  // Decode if it's a string

        $jsonObj['itc_elg'] = ($this->getItcElg($this->periodGstr3b));
        $jsonObj['inward_sup'] = $this->getInwardSup($this->periodGstr3b);
        $jsonObj['intr_ltfee'] = $this->getIntrltfee($this->periodGstr3b);

        return $jsonObj;
    }


    private function helperPos($pos)
    {
        if (intval($pos) < 10) {
            return "0" . intval($pos);
        } else {
            return intval($pos) . "";
        }
    }

    private function helperAmount($amt, $isRound = false)
    {
        $returnValue = 0;
        if ($isRound) {
            $returnValue = round(floatval($amt), 2);
        } else {
            // $returnValue = (floor(floatval($amt) * 100) / 100);
            $tempVal = floatval($amt) . "";
            $valArr = explode(".", $tempVal);
            $leftVal = $valArr[0];
            $righValTemp = ($valArr[1] ?? "") . "00";
            $rightVal = substr($righValTemp, 0, 2);
            $returnValue = floatval("$leftVal.$rightVal");
        }
        return $returnValue;
    }

    private function getSupDetails($period = null)
    {
        // Retrieve the JSON data from the database
        $getSupDetailssql = $this->dbObj->queryGet("SELECT * FROM `erp_compliance_gstr3b_docs` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `period`= '" . $period . "' AND `table_name`= 'sup_details'")['data'];

        // Decode the JSON data
        $jsonData = json_decode($getSupDetailssql['json_data'], true);

        // Modify the structure to match the desired format
        $formattedData = [
           
                "osup_det" => [
                    "txval" => (float) $jsonData["osup_det"]["txval"],
                    "iamt" => (float) $jsonData["osup_det"]["iamt"],
                    "camt" => (float) $jsonData["osup_det"]["camt"],
                    "samt" => (float) $jsonData["osup_det"]["samt"],
                    "csamt" => (float) $jsonData["osup_det"]["csamt"]
                ],
                "osup_zero" => [
                    "txval" => (float) $jsonData["osup_zero"]["txval"],
                    "iamt" => (float) $jsonData["osup_zero"]["iamt"],
                    "csamt" => (float) $jsonData["osup_zero"]["csamt"]
                ],
                "osup_nil_exmp" => [
                    "txval" => (float) $jsonData["osup_nil_exmp"]["txval"]
                ],
                "isup_rev" => [
                    "txval" => (float) $jsonData["isup_rev"]["txval"],
                    "iamt" => (float) $jsonData["isup_rev"]["iamt"],
                    "camt" => (float) $jsonData["isup_rev"]["camt"],
                    "samt" => (float) $jsonData["isup_rev"]["samt"],
                    "csamt" => (float) $jsonData["isup_rev"]["csamt"]
                ],
                "osup_nongst" => [
                    "txval" => (float) $jsonData["osup_nongst"]["txval"]
                ]
            
        ];

        // Return the modified data in the desired structure
        return $formattedData;
    }

    private function getIntersupDetails($period = null)
    {
        // Retrieve the JSON data from the database
        $getIntersupDetails = $this->dbObj->queryGet("SELECT * FROM `erp_compliance_gstr3b_docs` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `period`= '" . $period . "' AND `table_name`= 'inter_sup'")['data'];

        // Decode the JSON data into an associative array
        $jsonData = json_decode($getIntersupDetails['json_data'], true);

        // Format the data as needed, checking for the presence of the keys
        $formattedData = [
            "unreg_details" => isset($jsonData["inter_sup"]["unreg_details"])
                ? array_map(function ($item) {
                    return [
                        "pos" => $item["pos"],
                        "txval" => (float) $item["txval"],
                        "iamt" => (float) $item["iamt"]
                    ];
                }, $jsonData["inter_sup"]["unreg_details"])
                : [], // Return empty array if no data

            "comp_details" => isset($jsonData["inter_sup"]["comp_details"])
                ? array_map(function ($item) {
                    return [
                        "pos" => $item["pos"],
                        "txval" => (float) $item["txval"],
                        "iamt" => (float) $item["iamt"]
                    ];
                }, $jsonData["inter_sup"]["comp_details"])
                : [], // Return empty array if no data

            "uin_details" => isset($jsonData["inter_sup"]["uin_details"])
                ? array_map(function ($item) {
                    return [
                        "pos" => $item["pos"],
                        "txval" => (float) $item["txval"],
                        "iamt" => (float) $item["iamt"]
                    ];
                }, $jsonData["inter_sup"]["uin_details"])
                : [] // Return empty array if no data
        ];

        // Return the formatted data
        return $formattedData;
    }


    private function getItcElg($period = null)
    {
        // Retrieve the JSON data from the database
        $getItcElg = $this->dbObj->queryGet("SELECT * FROM `erp_compliance_gstr3b_docs` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `period`= '" . $period . "' AND `table_name`= 'itc_elg'")['data'];

        // Decode the JSON data into an associative array
        $jsonData = json_decode($getItcElg['json_data'], true);

        // Format the data as needed
        $formattedData = [
            "itc_avl" => array_map(function ($item) {
                return [
                    "ty" => $item["ty"],
                    "iamt" => (float) $item["iamt"],
                    "camt" => (float) $item["camt"],
                    "samt" => (float) $item["samt"],
                    "csamt" => (float) $item["csamt"]
                ];
            }, $jsonData["itc_avl"]),

            "itc_rev" => array_map(function ($item) {
                return [
                    "ty" => $item["ty"],
                    "iamt" => (float) $item["iamt"],
                    "camt" => (float) $item["camt"],
                    "samt" => (float) $item["samt"],
                    "csamt" => (float) $item["csamt"]
                ];
            }, $jsonData["itc_rev"]),

            "itc_net" => [
                "iamt" => (float) $jsonData["itc_net"]["iamt"],
                "camt" => (float) $jsonData["itc_net"]["camt"],
                "samt" => (float) $jsonData["itc_net"]["samt"],
                "csamt" => (float) $jsonData["itc_net"]["csamt"]
            ],

            "itc_inelg" => array_map(function ($item) {
                return [
                    "ty" => $item["ty"],
                    "iamt" => (float) $item["iamt"],
                    "camt" => (float) $item["camt"],
                    "samt" => (float) $item["samt"],
                    "csamt" => (float) $item["csamt"]
                ];
            }, $jsonData["itc_inelg"])
        ];

        // Return the formatted data
        return $formattedData;
    }

    private function getInwardSup($period = null)
    {
        // Retrieve the JSON data from the database
        $getInwardSup = $this->dbObj->queryGet("SELECT * FROM `erp_compliance_gstr3b_docs` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `period`= '" . $period . "' AND `table_name`= 'inward_sup'")['data'];

        // Decode the JSON data into an associative array
        $jsonData = json_decode($getInwardSup['json_data'], true);

        // Check if 'isup_details' exists and return data, otherwise return an empty object
        if (isset($jsonData["isup_details"]) && !empty($jsonData["isup_details"])) {
            $formattedData = [
                "isup_details" => array_map(function ($item) {
                    return [
                        "ty" => $item["ty"],
                        "inter" => (float) $item["inter"],
                        "intra" => (float) $item["intra"]
                    ];
                }, $jsonData["isup_details"])
            ];
            return $formattedData;
        }
    }
    private function getIntrltfee($period = null)
    {
        // Retrieve the JSON data from the database
        $getIntrltfee = $this->dbObj->queryGet("SELECT * FROM `erp_compliance_gstr3b_docs` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `period`= '" . $period . "' AND `table_name`= 'intr_ltfee'")['data'];

        // Decode the JSON data into an associative array
        $jsonData = json_decode($getIntrltfee['json_data'], true);

        // Check if 'intr_details' exists and return the formatted data, otherwise return an empty object
        if (isset($jsonData["intr_details"]) && !empty($jsonData["intr_details"])) {
            $formattedData = [
                "intr_details" => [
                    "iamt" => (float) $jsonData["intr_details"]["iamt"],
                    "camt" => (float) $jsonData["intr_details"]["camt"],
                    "samt" => (float) $jsonData["intr_details"]["samt"],
                    "csamt" => (float) $jsonData["intr_details"]["csamt"]
                ]
            ];
            return $formattedData;
        } else {
            // Return an empty object instead of null or an empty array
            return (object) [];
        }
    }
}
