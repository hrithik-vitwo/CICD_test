<?php
include("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
include("../common/header.php");
include("../common/navbar.php");
include("../common/sidebar.php");
require_once("../common/pagination.php");
include("../../app/v1/functions/company/func-branches.php");
include("../../app/v1/functions/branch/func-branch-pr-controller.php");


// console($_SESSION);



?>



<!-- <link rel="stylesheet" href="../../public/assets/manage-rfq.css">
<link rel="stylesheet" href="../../public/assets/animate.css"> -->

<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/banking.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">



<div class="content-wrapper">
    <section class="content banking-overview">
        <div class="container-fluid">
            <div class="head">
                <h2 class="text-lg font-bold">Banking Overview</h2>
                <div class="right-buttons d-flex gap-2">
                    <a href="" class="text-xs font-bold" style="text-decoration: none;">Auto-upload bank statements from email</a>
                    <button class="btn bg-light">Import Statement</button>
                    <button class="btn btn-primary">Add Bank</button>
                </div>
            </div>
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="card card-tabs rounded-5 account-details bg-light mt-4">
                        <div class="card-header bg-transparent border-bottom rounded-0">
                            <select name="" id="" class="form-control text-sm w-auto border-0 bg-transparent all-account-select">
                                <option value="">All Accounts</option>
                                <option value="">Tracknerd GPS Pvt Ltd (ICICI)</option>
                                <option value="">Tracknerd GPS Pvt Ltd (ICICI)</option>
                                <option value="">Tracknerd GPS Pvt Ltd (ICICI)</option>
                            </select>

                            <select name="" id="" class="form-control text-sm w-auto border-0 bg-transparent date-selection text-grey">
                                <option value="">Last 30 days</option>
                                <option value="">Last 2 months</option>
                                <option value="">Last 1 year</option>
                            </select>
                        </div>
                        <div class="card-body bg-transparent" style="overflow: hidden;">
                            <div class="row p-4">
                                <div class="col-lg-8 col-md-8 col-sm-8">
                                    <div class="balance-blocks">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <div class="box">
                                                    <div class="icon">
                                                        <img src="../../public/assets/img/cash-hand.png" alt="">
                                                    </div>
                                                    <div class="desc">
                                                        <p>Cash In Hand</p>
                                                        <p class="font-bold">Rs - 36,100</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <div class="box green">
                                                    <div class="icon">
                                                        <img src="../../public/assets/img/bank-balance.png" alt="">
                                                    </div>
                                                    <div class="desc">
                                                        <p>Bank Balance</p>
                                                        <p class="font-bold">Rs - 36,100</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <div class="box pink">
                                                    <div class="icon">
                                                        <img src="../../public/assets/img/card-balance.png" alt="">
                                                    </div>
                                                    <div class="desc">
                                                        <p>Card Balance</p>
                                                        <p class="font-bold">Rs - 36,100</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="redirect-graphic my-4">
                                        <a href="./banking-visualize-chart.php" target="_blank" class="text-xs"><ion-icon name="trending-up-outline"></ion-icon>visualized</a>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="transaction float-right px-3">
                                        <p>Uncategorized Transactions</p>
                                        <a href="" class="text-xs" style="text-decoration: none;">Categorize now</a>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="card-header rounded-0 bg-light py-3">
                            <select name="" id="" class="form-control text-sm bg-transparent w-auto border-0">
                                <option value="">Active Accounts</option>
                                <option value="">Inactive Accounts</option>
                                <option value="">No Accounts</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <table class="table list-table active-accounts">
                                <thead>
                                    <tr>
                                        <th>Account Details</th>
                                        <th>Uncategorized</th>
                                        <th class="text-right">Amount Bank</th>
                                        <th class="text-right">Amount In Vitwo </th>
                                        <th class="text-right">
                                            <ion-icon name="search-outline"></ion-icon>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <a href="">Cash in Hand</a>
                                        </td>
                                        <td></td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">
                                            <ion-icon name="checkbox-outline"></ion-icon>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="">Cradit Card 3380</a>
                                        </td>
                                        <td></td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">
                                            <ion-icon name="checkbox-outline"></ion-icon>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="">Petty cash</a>
                                        </td>
                                        <td></td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">
                                            <ion-icon name="checkbox-outline"></ion-icon>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="">Tracknerd GPs PVT Ltd (ICICI)</a>
                                        </td>
                                        <td><span class="text-danger">3 transactions</span></td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">
                                            <ion-icon name="checkbox-outline"></ion-icon>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <a href="">Undeposited Funds</a>
                                        </td>
                                        <td></td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">
                                            <ion-icon name="checkbox-outline"></ion-icon>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>


<?php
include("../common/footer.php");
?>
