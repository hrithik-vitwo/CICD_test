<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
$pageName =  basename($_SERVER['PHP_SELF'], '.php');


//administratorLocationAuth();
if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
  echo "Session Timeout";
  exit;
}

?>

<style>
  .content-wrapper {
    background: #e8eaed !important;
  }

  .reports-section .row .card {
    box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
    transition-duration: 0.2s;
    height: 10rem;
    min-height: 100%;
    background: #fff;
  }

  .reports-section .row a {
    text-decoration: none;
    color: #000;
  }

  .reports-section .row .card:hover {
    box-shadow: rgba(50, 50, 93, 0.25) 2px 8px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
  }


  .reports-section .row .card .card-body {
    display: flex;
    flex-direction: column;
    justify-content: baseline !important;
    align-items: baseline;
    gap: 35px;
    padding: 0.8rem;
    background-size: contain;
    background-position: top right;
    background-repeat: no-repeat;
    border-radius: 15px;
  }


  .reports-section .row .card .card-footer button {
    float: right;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: #003060;
    color: #fff;
    border: 0;
    font-size: 12px;
  }

  .reports-section .row:nth-child(1) {
    margin: 15px 0;
  }

  .reports-section .row {
    margin: 5px 0 40px;
  }

  .reports-section .row .col {
    max-width: 250px;
  }

  .reports-section .row .card .card-body .icon img {
    width: 1.5rem;
  }

  .reports-section .row .card .card-body .icon {
    background: #003060;
    border-radius: 12px;
    width: 3rem;
    height: 3rem;
    padding: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: #939393 6px 6px 16px -2px, rgba(0, 0, 0, 0.3) 6px 8px 4px -1px;
    border-radius: 50%;
  }

  .reports-section .row .card .card-body .icon ion-icon {
    color: #fff;
    font-size: 35px;
  }

  .disableReport {
    filter: grayscale(1);
  }

  @media (max-width: 768px) {
    .reports-section .row .card.reports-card .card-body {
      gap: 0px !important;
    }
  }

  @media (max-width: 425px) {
    .reports-section .row .col {
      flex: 1 1 100%;
    }
  }
</style>

<!-- 
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css"> -->
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<div class="content-wrapper">
  <section class="content">
    <div class="container-fluid">
      <div class="reports-section">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <h4 class="text-sm font-bold border-bottom pb-2 mb-3"> Failed Accounting Modules</h4>
          </div>
        </div>
        <div class="row">
          <!-- Invoice -->
          <div class="col mb-5">
            <a href="failed-accounting-invoices.php">
              <div class="card reports-card">
                <div class="card-body">
                  <div class="icon text-center">
                    <img width="20" src="../../public/storage/icons/trial-balance.png" alt="icons">
                  </div>
                  <div class="report-name">
                    <p class="font-bold text-sm mt-3"> Invoice</p>
                  </div>
                </div>
                <div class="card-footer">
                  <button>
                    <i class="fa fa-arrow-right"></i>
                  </button>
                </div>
              </div>
            </a>
          </div>
          <!-- GRN/SRN  -->
          <div class="col mb-5">
            <a href="failed-accounting-grn-srn.php">
              <div class="card reports-card">
                <div class="card-body">
                  <div class="icon text-center">
                    <img width="20" src="../../public/storage/icons/trial-balance.png" alt="icons">
                  </div>
                  <div class="report-name">
                    <p class="font-bold text-sm mt-3"> GRN/SRN </p>
                  </div>
                </div>
                <div class="card-footer">
                  <button>
                    <i class="fa fa-arrow-right"></i>
                  </button>
                </div>
              </div>
            </a>
          </div>
          <!-- GRNIV/SRNIV -->
          <div class="col mb-5">
            <a href="failed-accounting-grnIv-srnIv.php">
              <div class="card reports-card">
                <div class="card-body">
                  <div class="icon text-center">
                    <img width="20" src="   ../../public/storage/icons/trial-balance.png" alt="icons">

                  </div>
                  <div class="report-name">
                    <p class="font-bold text-sm mt-3"> GRNIV/SRNIV</p>
                  </div>
                </div>
                <div class="card-footer">
                  <button>
                    <i class="fa fa-arrow-right"></i>
                  </button>
                </div>
              </div>
            </a>
          </div>
          <!-- Collection -->
          <div class="col mb-5">
            <a href="failed-accounting-collectPayment.php">
              <div class="card reports-card">
                <div class="card-body">
                  <div class="icon text-center">
                    <img width="20" src="../../public/storage/icons/trial-balance.png" alt="icons">
                  </div>
                  <div class="report-name">
                    <p class="font-bold text-sm mt-3"> Collection</p>
                  </div>
                </div>
                <div class="card-footer">
                  <button>
                    <i class="fa fa-arrow-right"></i>
                  </button>
                </div>
              </div>
            </a>
          </div>
        </div>
        <div class="row">
          <!-- Payment -->
          <div class="col mb-5">
            <a href="failed-accounting-payment.php">
              <div class="card reports-card">
                <div class="card-body">
                  <div class="icon text-center">
                    <img width="20" src="../../public/storage/icons/trial-balance.png" alt="icons">
                  </div>
                  <div class="report-name">
                    <p class="font-bold text-sm mt-3"> Payment</p>
                  </div>
                </div>
                <div class="card-footer">
                  <button>
                    <i class="fa fa-arrow-right"></i>
                  </button>
                </div>
              </div>
            </a>
          </div>
          <!-- Credit Note -->
          <div class="col mb-5">
            <a href="failed-accounting-credit-note.php">
              <div class="card reports-card">
                <div class="card-body">
                  <div class="icon text-center">
                    <img width="20" src="../../public/storage/icons/trial-balance.png" alt="icons">
                  </div>
                  <div class="report-name">
                    <p class="font-bold text-sm mt-3"> Credit Note</p>
                  </div>
                </div>
                <div class="card-footer">
                  <button>
                    <i class="fa fa-arrow-right"></i>
                  </button>
                </div>
              </div>
            </a>
          </div>
          <!-- Debit Note -->
          <div class="col mb-5">
            <a href="failed-accounting-debit-note.php">
              <div class="card reports-card">
                <div class="card-body">
                  <div class="icon text-center">
                    <img width="20" src="../../public/storage/icons/trial-balance.png" alt="icons">
                  </div>
                  <div class="report-name">
                    <p class="font-bold text-sm mt-3"> Debit Note</p>
                  </div>
                </div>
                <div class="card-footer">
                  <button>
                    <i class="fa fa-arrow-right"></i>
                  </button>
                </div>
              </div>
            </a>
          </div>
          <!-- Production -->
          <div class="col mb-5">
            <a href="failed-accounting-production-declaration.php">
              <div class="card reports-card">
                <div class="card-body">
                  <div class="icon text-center">
                    <img width="20" src="../../public/storage/icons/trial-balance.png" alt="icons">
                  </div>
                  <div class="report-name">
                    <p class="font-bold text-sm mt-3">Production</p>
                  </div>
                </div>
                <div class="card-footer">
                  <button>
                    <i class="fa fa-arrow-right"></i>
                  </button>
                </div>
              </div>
            </a>
          </div>

        </div>
        <div class="row">
          <!-- Payroll -->
          <div class="col mb-5">
            <a href="failed-accounting-payroll.php">
              <div class="card reports-card">
                <div class="card-body">
                  <div class="icon text-center">
                    <img width="20" src="../../public/storage/icons/trial-balance.png" alt="icons">
                  </div>
                  <div class="report-name">
                    <p class="font-bold text-sm mt-3"> Payroll</p>
                  </div>
                </div>
                <div class="card-footer">
                  <button>
                    <i class="fa fa-arrow-right"></i>
                  </button>
                </div>
              </div>
            </a>
          </div>
          <!-- pgi -->
          <div class="col mb-5">
            <a href="failed-accounting-pgi-invoice.php">
              <div class="card reports-card">
                <div class="card-body">
                  <div class="icon text-center">
                    <img width="20" src="../../public/storage/icons/trial-balance.png" alt="icons">
                  </div>
                  <div class="report-name">
                    <p class="font-bold text-sm mt-3"> PGI</p>
                  </div>
                </div>
                <div class="card-footer">
                  <button>
                    <i class="fa fa-arrow-right"></i>
                  </button>
                </div>
              </div>
            </a>
          </div>

        </div>

      </div>
  </section>
</div>


<?php
require_once("../common/footer2.php");
?>