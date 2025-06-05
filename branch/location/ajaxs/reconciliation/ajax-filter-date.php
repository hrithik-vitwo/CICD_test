<?php

require_once("../../../../app/v1/connection-branch-admin.php");

require_once("../../../../app/v1/functions/branch/bankReconciliationStatement.controller.php");

if(isset($_GET['startdate']) && $_GET['startdate'] != "" && isset($_GET['enddate']) && $_GET['enddate'] != ""){

     $start_date = $_GET['startdate'];
     $end_date = $_GET['enddate'];

     $brsObj = new BankReconciliationStatement($bankId, $tnxType);
     $table = "";

     $bank_sum = 0;
     $book_sum = 0;
     $graph = [];
     foreach ($brsObj->getBankList()["data"] as $key => $listItem) {
        $bank_id = $listItem["id"];
        $bank_amt = $brsObj->getBankAmountDateFilter($listItem["id"],$start_date,$end_date)["data"]["balance_amt"];
        $bank_amt_count = $brsObj->getBankAmountDateFilter($listItem["id"],$start_date,$end_date)["numRows"];
        $book_amt = $brsObj->getBooksAmountDateFilter($listItem["parent_gl"],$listItem["acc_code"],$start_date,$end_date);
        $bank_name = $listItem["bank_name"]." (".$listItem["account_no"].")";
        $pgl = $listItem["parent_gl"];
        $subgl = $listItem["acc_code"];

        if($bank_amt_count == 0)
        {
         $bank_amt = 0;
        }
        else
        {
         $bank_amt = $bank_amt;
        }

        if($book_amt == "")
        {
         $book_amt = 0;
        }
        else
        {
         $book_amt = $book_amt;
        }

             $table .= '<tr>
                  <td>
                     <a href="'. LOCATION_URL .'banking-transaction.php?act=all&bank='.base64_encode(base64_encode(base64_encode($bank_id))).'">'. $listItem["bank_name"] .'</a>
                  </td>
                  <td><a href="#focus" class="report_link" data-pgl = "'.$pgl.'" data-subgl = "'.$subgl.'" data-bankid= "'.$bank_id.'" data-bank= "'.$bank_amt.'" data-book="'.$book_amt.'" data-bankname="'.$bank_name.'"><i class="fa fa-chart-line cursor_pointer"></i></a></td>';

                  $count =  $brsObj->getUncategorizedCountDateFilter($listItem["id"],$start_date,$end_date)["numRows"];

                  if($count != 0)
                  {
                  $table .= '<td><span class="text-danger">'. $count .' transactions</span></td>';
                  }
                  else
                  {
                     $table .= '<td></td>';
                  }

                  $table .= '<td class="text-right">';

                     if($bank_amt_count == 0)
                     {
                        $bank_sum += 0;
                        $table .= '0.00';
                        $bank_amt_graph = 0;
                     }
                     else
                     {
                        $bank_sum += $bank_amt;
                        $bank_amt_graph = $bank_amt;
                        $table .= $bank_amt;
                     }

                     $table .= '</td>
                  <td class="text-right">';

                  if($book_amt == "")
                  {
                     $book_sum += 0;
                     $table .= '0.00';
                     $book_amt_graph = 0;
                  }
                  else
                  {
                     $book_sum += $book_amt;
                     $table .= $book_amt;
                     $book_amt_graph = $book_amt;
                  }

                  $table .= '</td>
                  <td>
                     <ion-icon name="cloud-upload-outline" data-bs-toggle="modal" data-bs-target="#bankStatement'.$bank_id.'" style="cursor: pointer;"></ion-icon>
                                                <div class="modal fade bankstatement-modal" id="bankStatement'.$bank_id.'" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="" method="post" id="statement-upload-form_'.$bank_id.'" class="statement-upload-form" enctype="multipart/form-data">
                                                                    <input type="hidden" name="uploadFile" value="submitStatementFileBtn">
                                                                    <input type="hidden" name="bank_id" value="'.$bank_id.'">
                                                                    <div class="upload-section">
                                                                        <div class="wrapper">
                                                                            <div class="upload-wrapper">
                                                                                <div class="upload drop-area">
                                                                                    <div class="upload-button">
                                                                                        <div class="upload-info">
                                                                                            <ion-icon name="attach-outline"></ion-icon>
                                                                                            <span class="upload-filename inactive drop-text" id="upload_filename_'.$bank_id.'">No file selected</span>
                                                                                        </div>
                                                                                        <input type="file" name="file" id="fileInput_'.$bank_id.'" class="form-control statement-file-input">
                                                                                        <button id="uploadButton_'.$bank_id.'" type="submit" class="btn btn-primary statement-upload-btn">Upload File</button>
                                                                                    </div>
                                                                                    <div class="upload-hint">Uploading...</div>
                                                                                    <div class="upload-progress"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="upload-vit-animation">
                                                                                <img width="150" src="../../public/assets/img/VitNew 1.png" alt="">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                <div class="bank-statement-list uploadStatementResponseDiv" id="uploadStatementResponseDiv_'.$bank_id.'">
                                                                </div>
                                                            </div>
                                                            <!-- <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary" name = "submitOcrStatementBtn">Save changes</button>
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                </div>
                  </td>
            </tr>';
            $graph[] = array("bank_name"=>$listItem["bank_name"]." (".$listItem["account_no"].")","book"=>$book_amt_graph,"bank"=>$bank_amt_graph);
     }

     $date_text = date("d/m/Y",strtotime($date))."-".date("d/m/Y");

     $array = array("table"=>$table,"bank_sum"=>$bank_sum,"book_sum"=>$book_sum,"dates"=>$date_text,"graph"=>$graph);

     echo json_encode($array);

}
?>
