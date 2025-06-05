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
    <section class="content banking-import-statement">
        <div class="container-fluid">
            <div class="head">
                <h2 class="text-lg font-bold">Import Statements</h2>
            </div>
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="body-container mt-4">
                        <div class="progress-container">
                            <div class="progress" id="progress"></div>
                            <div class="circle active">1
                                <span>Configure</span>
                            </div>
                            <div class="circle">2
                                <span>Preview</span>
                            </div>
                        </div>
                        <div class="form-step mt-4" id="step1">
                            <h2 class="text-sm font-bold my-3">Configure</h2>
                            <p class="text-xs mt-2 mb-3">Upload your documents</p>
                            <form>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-input">
                                            <label for="">Select Account</label>
                                            <select name="" id="" class="form-control">
                                                <option value="">Choose your account to import</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="form-input">
                                            <label for="">Character Encoding</label>
                                            <select name="" id="" class="form-control">
                                                <option value="">UTF-8 (unicode)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-xs d-flex gap-2 my-4">Ensure that the import files is in the correct format by comparing it with our sample file. <a href="">Download sample file</a></p>

                                <div class="row upload-tips-block">
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="card upload-card">
                                            <div class="card-body bg-white">
                                                <div class="file-upload">
                                                    <ion-icon name="cloud-upload-outline" class="po-list-icon"></ion-icon>
                                                    <span class="text-xs">Drag and drop file to import</span>
                                                    <div class="upload-btn-wrapper">
                                                        <div class="icon">
                                                            <ion-icon name="attach-outline"></ion-icon>
                                                        </div>
                                                        <button class="btn">Upload a file</button>
                                                        <input type="file" name="myfile" />
                                                    </div>
                                                    <span class="text-xs text-center">Maximum File Size: 1 MB for CSV, TSV, XLS, OFX, QFX, QIF,<br><br>CAMT.053 and CAMT.054 5 MB for PDF files</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6">
                                        <div class="card tips-card">
                                            <div class="card-body bg-white">
                                                <h5 class="font-bold text-sm d-flex gap-3">
                                                    <img src="../../public/assets/img/tips.png" alt="">
                                                    General tips
                                                </h5>
                                                <ul>
                                                    <li class="text-xs my-2">If you have files in other formats, you can convert it to an accepted file format using any online/offline converter.</li>
                                                    <li class="text-xs my-2">You can configure your import settings and save them for future tool.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="form-step" id="step2">
                            <h2 class="text-sm font-bold my-3">Preview</h2>
                            <p class="text-xs mt-2 mb-3">Bank statement preview</p>
                            <form>
                                <div class="row">
                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                        <div class="form-input">
                                            <label for="">Search</label>
                                            <input type="search" class="form-control" placeholder="Find Customer">
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                        <div class="form-input">
                                            <label for="">Email Address</label>
                                            <input type="search" class="form-control" placeholder="Find Email Address">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2">
                                        <div class="form-input">
                                            <label for="">Balance</label>
                                            <div class="d-flex gap-2">
                                                <input type="checkbox">
                                                <span class="text-xs">Show $0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <table class="table list-table preview-table mt-4">
                                    <thead>
                                        <tr>
                                            <th width="5%"><input type="checkbox"></th>
                                            <th>Business</th>
                                            <th>Contact</th>
                                            <th>Phone</th>
                                            <th>Email ID</th>
                                            <th class="text-right">Current Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <tr>
                                        <td><input type="checkbox"></td>
                                        <td>Coffee Company PVT LTD.</td>
                                        <td>John Robert</td>
                                        <td>98 xxx xxxxx</td>
                                        <td>coffee@gmail.com</td>
                                        <td class="text-right">Rs. 385,247.25</td>
                                       </tr>
                                       <tr>
                                        <td><input type="checkbox"></td>
                                        <td>Coffee Company PVT LTD.</td>
                                        <td>John Robert</td>
                                        <td>98 xxx xxxxx</td>
                                        <td>coffee@gmail.com</td>
                                        <td class="text-right">Rs. 385,247.25</td>
                                       </tr>
                                       <tr>
                                        <td><input type="checkbox"></td>
                                        <td>Coffee Company PVT LTD.</td>
                                        <td>John Robert</td>
                                        <td>98 xxx xxxxx</td>
                                        <td>coffee@gmail.com</td>
                                        <td class="text-right">Rs. 385,247.25</td>
                                       </tr>
                                       <tr>
                                        <td><input type="checkbox"></td>
                                        <td>Coffee Company PVT LTD.</td>
                                        <td>John Robert</td>
                                        <td>98 xxx xxxxx</td>
                                        <td>coffee@gmail.com</td>
                                        <td class="text-right">Rs. 385,247.25</td>
                                       </tr>
                                       <tr>
                                        <td><input type="checkbox"></td>
                                        <td>Coffee Company PVT LTD.</td>
                                        <td>John Robert</td>
                                        <td>98 xxx xxxxx</td>
                                        <td>coffee@gmail.com</td>
                                        <td class="text-right">Rs. 385,247.25</td>
                                       </tr>
                                       <tr>
                                        <td><input type="checkbox"></td>
                                        <td>Coffee Company PVT LTD.</td>
                                        <td>John Robert</td>
                                        <td>98 xxx xxxxx</td>
                                        <td>coffee@gmail.com</td>
                                        <td class="text-right">Rs. 385,247.25</td>
                                       </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="btns-grp d-flex w-100 justify-content-between">
                            <button class="btn bg-light" id="prev" disabled>Prev</button>
                            <button class="btn btn-primary" id="next">Next</button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card card-tabs rounded-5 account-details bg-light">


            </div>
        </div>
</div>
</div>
</div>


<?php
include("../common/footer.php");
?>


<script>
    const progress = document.getElementById('progress');
    const prev = document.getElementById('prev');
    const next = document.getElementById('next');
    const circles = document.querySelectorAll('.circle');
    const formSteps = document.querySelectorAll('.form-step');

    let currentActive = 1;

    next.addEventListener('click', () => {
        currentActive++;

        if (currentActive > circles.length) {
            currentActive = circles.length;
        }
        progress.style.width = '100%';
        update();
    });

    prev.addEventListener('click', () => {
        currentActive--;

        if (currentActive < 1) {
            currentActive = 1;
        }
        progress.style.width = '0%';
        update();
    });

    function update() {
        formSteps.forEach((step, idx) => {
            if (idx + 1 === currentActive) {
                step.style.display = 'block';
            } else {
                step.style.display = 'none';
            }
        });

        circles.forEach((circle, idx) => {
            if (idx < currentActive) {
                circle.classList.add('active');
            } else {
                circle.classList.remove('active');
            }
        });

        const actives = document.querySelectorAll('.active');



        if (currentActive === circles.length) {
            next.disabled = false;
        } else {
            next.disabled = false;
        }

        if (currentActive === 1) {
            prev.disabled = true;
        } else {
            prev.disabled = false;
        }
    }

    update(); // Initialize the progress and buttons
</script>