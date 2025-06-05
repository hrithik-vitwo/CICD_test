<!-- /.content-wrapper -->
<footer class="main-footer text-muted">
  <strong class="text-muted"><a href="<?= COMPANY_URL ?>"><?= getAdministratorSettings("footer"); ?></a></strong>
  <div class="float-right d-none d-sm-inline-block">
    <b>Date :</b> <?= date("d-m-Y"); ?>
  </div>



  <div class="popup" id="bugModal">
    <div class="popup-overlay"></div>
    <div class="popup-content card">
      <div class="card-header p-2">
        <h3 class="bug-title text-center text-white font-bold text-sm mb-2 mt-2">Bug Report</h3>
      </div>
      <div class="card-body">
        <form action="" id="bug_frm">
          <input type="hidden" name="bug_module_name" id="bug_module_name" value="">
          <input type="hidden" name="bug_sub_module_name" id="bug_sub_module_name" value="">
          <input type="hidden" name="bug_image_url" id="bug_image_url" value="">
          <input type="hidden" name="bug_image" id="bug_image" value="">
          <input type="hidden" name="bug_page_url" id="bug_page_url" value="">
          <input type="hidden" name="bug_page_name" id="bug_page_name" value="">
          <div class="url-date-section">
            <p class="mb-2 font-italic text-xs font-bold bug_currentUrl">Loading...</p>
            <p class="mb-2 font-italic text-xs font-bold bug_currentDateclass">Loading...</p>
          </div>
          <div class="bug-screenshot ">
            <img width="100%" class="screenBug" src="" title="Loading...">
          </div>
          <div class="form-input">
            <label for="">Describe your issue</label>
            <textarea class="form-control" name="bug_description" id="bug_description" cols="55" rows="8" placeholder="Write here ..."></textarea>
          </div>
        </form>
      </div>
      <div class="card-footer">
        <button type="button" class="btn btn-primary float-right bug_submit">Submit</button>
        <button class="popup-close btn btn-danger">Close</button>
      </div>
    </div>
  </div>



  <div class="sticky-icon">
    <!-- <button id="openPopup" class="bug-btn-modal" style="z-index: 9999;">
      <img src="<?= BASE_URL ?>public/assets/gif/bug.gif" alt="Bug Img">
      Report a Bug
    </button> -->

    <!-- <a id="openPopup" class="bug-btn-modal" style="z-index: 9999;"> <i class="fa fa-bug"></i> Report a Bug </a> -->
    <a id="openPopup"><i class="fa fa-bug"> </i>
      <p>Report a Bug </p>
    </a>


  </div>


  <!-- --------------------------Audit Trail single History Modal Start---------------- -->
  <div class="modal fade right audit-history-modal" id="innerModal" role="dialog" aria-labelledby="innerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content auditTrailBodyContentLineDiv">
        <div class="modal-header">
          <div class="head-audit">
            <p><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading ...</p>
          </div>
          <div class="head-audit">
            <p>xxxxxxxxxxxxxx</p>
            <p>xxxxxxxxx</p>
          </div>

        </div>
        <div class="modal-body p-0">
          <div class="free-space-bg">
            <div class="color-define-text">
              <p class="update"><span></span> Record Updated </p>
              <p class="all"><span></span> New Added </p>
            </div>
            <ul class="nav nav-tabs pb-0" id="myTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="concise-tab" data-toggle="tab" href="#consize" role="tab" aria-controls="concise" aria-selected="true"><i class="fa fa-th-large mr-2" aria-hidden="true"></i> Concised View</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="false"><i class="fa fa-list mr-2" aria-hidden="true"></i>Detailed View</a>
              </li>
            </ul>
          </div>
          <div class="tab-content pt-0" id="myTabContent">
            <div class="tab-pane fade show active" id="consize" role="tabpanel" aria-labelledby="consize-tab">
              <div class="dotted-box">
                <p class="overlap-title">Loading ...</p>
                <div class="box-content hightlight">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
              </div>
            </div>

            <!-- -------------------Audit History Tab Body Start------------------------- -->
            <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
              <div class="dotted-box">
                <p class="overlap-title">Loading ...</p>
                <div class="box-content hightlight">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
              </div>
              <div class="dotted-box">
                <p class="overlap-title">Loading ...</p>
                <div class="box-content hightlight">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
              </div>
            </div>
            <!-- -------------------Audit History Tab Body End------------------------- -->

          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- --------------------------Audit Trail single History Modal Endd---------------- -->



</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->


<!-- jQuery UI 1.11.4 -->
<script src="<?= BASE_URL ?>public/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?= BASE_URL ?>public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- sweetalert2 -->
<script src="<?= BASE_URL ?>public/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- ChartJS -->
<script src="<?= BASE_URL ?>public/assets/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="<?= BASE_URL ?>public/assets/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="<?= BASE_URL ?>public/assets/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?= BASE_URL ?>public/assets/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?= BASE_URL ?>public/assets/plugins/moment/moment.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= BASE_URL ?>public/assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?= BASE_URL ?>public/assets/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?= BASE_URL ?>public/assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

<!-- DataTables  & Plugins -->
<script src="<?= BASE_URL ?>public/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/jszip/jszip.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<!-- BS-Stepper -->
<script src="<?= BASE_URL ?>public/assets/plugins/bs-stepper/js/bs-stepper.min.js"></script>

<!-- AdminLTE App -->
<script src="<?= BASE_URL ?>public/assets/AdminLTE/dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?= BASE_URL ?>public/assets/AdminLTE/dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?= BASE_URL ?>public/assets/AdminLTE/dist/js/pages/dashboard.js"></script>

<script src="<?= BASE_URL ?>public/assets/html2canvas.min.js"></script>

<script>
  // ------------------ Audit Trail script Start----------------------------

  $(document).on("click", ".auditTrail", function() {
    var ccode = $(this).data('ccode');
    // alert(ccode);
    $.ajax({
      url: 'ajaxs/audittrail/ajax-audit-trail.php?auditTrailBodyContent', // <-- point to server-side PHP script 
      type: 'POST',
      data: {
        ccode
      },
      beforeSend: function() {
        // $('.Ckecked_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
        // $(".Ckecked_loder").toggleClass("disabled");
      },
      success: function(responseData) {
        $(`.auditTrailBodyContent${ccode}`).html(responseData);
      }
    });


  });

  $(document).on("click", ".auditTrailBodyContentLine", function() {
    $(`.auditTrailBodyContentLineDiv`).html(`<div class="modal-header">
          <div class="head-audit">
            <p><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading ...</p>
          </div>
          <div class="head-audit">
            <p>xxxxxxxxxxxxxx</p>
            <p>xxxxxxxxx</p>
          </div>

        </div>
        <div class="modal-body p-0">
          <div class="free-space-bg">
            <div class="color-define-text">
              <p class="update"><span></span> Record Updated </p>
              <p class="all"><span></span> New Added </p>
            </div>
            <ul class="nav nav-tabs pb-0" id="myTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="concise-tab" data-toggle="tab" href="#consize" role="tab" aria-controls="concise" aria-selected="true"><i class="fa fa-th-large mr-2" aria-hidden="true"></i> Concised View</a>
              </li>

              <li class="nav-item">
                <a class="nav-link" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="false"><i class="fa fa-list mr-2" aria-hidden="true"></i>Detailed View</a>
              </li>
            </ul>
          </div>
          <div class="tab-content pt-0" id="myTabContent">
            <div class="tab-pane fade show active" id="consize" role="tabpanel" aria-labelledby="consize-tab">
              <div class="dotted-box">
                <p class="overlap-title">Loading ...</p>
                <div class="box-content hightlight">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
              </div>
            </div>

            <!-- -------------------Audit History Tab Body Start------------------------- -->
            <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
              <div class="dotted-box">
                <p class="overlap-title">Loading ...</p>
                <div class="box-content hightlight">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
              </div>
              <div class="dotted-box">
                <p class="overlap-title">Loading ...</p>
                <div class="box-content hightlight">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
                <div class="box-content">
                  <p>xxxxxxxxxxxxx</p>
                  <p>xxxxxxx</p>
                </div>
              </div>
            </div>
            <!-- -------------------Audit History Tab Body End------------------------- -->

          </div>
        </div>`);
    var ccode = $(this).data('ccode');
    var id = $(this).data('id');
    // alert(ccode);
    $.ajax({
      url: 'ajaxs/audittrail/ajax-audit-trail.php?auditTrailBodyContentLine', // <-- point to server-side PHP script 
      type: 'POST',
      data: {
        ccode,
        id
      },
      beforeSend: function() {
        // $('.Ckecked_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
        // $(".Ckecked_loder").toggleClass("disabled");
      },
      success: function(responseData) {
        $(`.auditTrailBodyContentLineDiv`).html(responseData);
      }
    });


  });


  // ------------------ Audit Trail script End----------------------------


  $(document).ready(function() {

    // Get the close button
    var closeButton = $('.popup-close');

    // Function to close the popup
    function closePopup() {
      $('.popup').hide();
    }

    // Event listener for the close button
    closeButton.on('click', closePopup);
  });



  $('.bug_submit').on('click', function() {
    // Serialize the form data
    var formData = $('#bug_frm').serialize();
    $('.bug_submit').prop('disabled', true);

    $.ajax({
      url: 'https://one.vitwo.ai/save_screenshot.php?act=bug_submit', // Replace with your server-side script URL
      type: 'POST',
      data: formData,
      success: function(response) {
        // Handle the server response if needed
        console.log(response);
        var jsonObject = JSON.parse(response);
        // let Toast = Swal.mixin({
        //   toast: true,
        //   position: 'top-end',
        //   showConfirmButton: false,
        //   timer: 3000
        // });
        // Toast.fire({
        //   icon: jsonObject['status'],
        //   title: jsonObject['message']
        // });
        Swal.fire({
          icon: jsonObject['status'],
          title: jsonObject['bug_code'],
          text: jsonObject['message'],
        });
        $('#bug_frm')[0].reset();
        let bug_img = document.querySelector(".screenBug");
        bug_img.src = '';
        $('.bug_submit').prop('disabled', false);
        $('#bugModal').hide();

      },
      error: function(xhr, status, error) {
        // Handle errors if any
        console.error('error');
      }
    });

  });

  $('#openPopup').on('click', function() {
    $("#bug_description").focus();
    // alert('Please');
    bug_report();

  });


  function bug_report() {
    let bug_region = document.querySelector("body"); // whole screen
    html2canvas(bug_region, {
      onrendered: function(canvas) {
        let bug_pngUrl = canvas.toDataURL(); // png in dataURL format
        let bug_img = document.querySelector(".screenBug");
        bug_img.src = bug_pngUrl;

        $('#bugModal').show();

        var bug_fullURL = window.location.href;
        console.log(bug_fullURL);

        var bug_timestamp = $.now();
        var bug_currentDateTime = new Date(bug_timestamp);
        var bug_dateTime = bug_currentDateTime.toLocaleString();
        console.log(bug_dateTime);


        var bug_currentDate = new Date();
        var bug_dateString = bug_currentDate.getFullYear() + '_' + (bug_currentDate.getMonth() + 1) + '_' + bug_currentDate.getDate();
        var bug_uniqueId = Math.floor(Math.random() * 1042014252545596);
        var bug_filename = 'bug_' + bug_dateString + '_' + bug_uniqueId + '.png';
        console.log(bug_filename);
        $(".bug_currentDateclass").html(bug_dateTime);
        $(".bug_currentUrl").html(bug_fullURL);
        $("#bug_page_url").val(bug_fullURL);
        $("#bug_image_url").val(bug_filename);
        $("#bug_image").val(bug_pngUrl);

        var bug_page_name = $(document).prop('title');
        console.log(bug_page_name);
        $("#bug_page_name").val(bug_page_name);
      },
    });
  }


  // ***//

  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
  $(function() {
    $(".defaultDataTable").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#defaultDataTable_wrapper .col-md-6:eq(0)');

    /*$('#defaultDataTable').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });*/
  });

  // BS-Stepper Init
  document.addEventListener('DOMContentLoaded', function() {
    window.stepper = new Stepper(document.querySelector('.bs-stepper'))
  });
  $("input[type='checkbox']").change(function() {
    var val = $(this).val();
    $("#mytable tr:first").find("th:eq(" + val + ")").toggle();
    $("#mytable tr").each(function() {
      $(this).find("td:eq(" + val + ")").toggle();
    });
    if ($("#mytable tr:first").find("th:visible").length > 0) {
      $("#mytable").removeClass("noborder");
    } else {
      $("#mytable").addClass("noborder");
    }
  });
  $("#selector5").click(function() {
    $("#main2").toggle();

  });

  // *****  //

  $(function() {
    $(".checkbox").click(function() {
      if ($(this).is(":checked")) {
        $("#multistepform").show();
        $("#gstform").hide();
      } else {
        $("#multistepform").hide();
        $("#gstform").show();
      }
    });
  });

  $(function() {
    $("#checkbox2").click(function() {
      if ($(this).is(":checked")) {
        $(".gstfield").show();
        $("#multistepform").hide();
      }
    });
  });

  // *** multi step form *** //

  //DOM elements
  const DOMstrings = {
    stepsBtnClass: 'multisteps-form__progress-btn',
    stepsBtns: document.querySelectorAll(`.multisteps-form__progress-btn`),
    stepsBar: document.querySelector('.multisteps-form__progress'),
    stepsForm: document.querySelector('.multisteps-form__form'),
    stepsFormTextareas: document.querySelectorAll('.multisteps-form__textarea'),
    stepFormPanelClass: 'multisteps-form__panel',
    stepFormPanels: document.querySelectorAll('.multisteps-form__panel'),
    stepPrevBtnClass: 'js-btn-prev',
    stepNextBtnClass: 'js-btn-next'
  };


  //remove class from a set of items
  const removeClasses = (elemSet, className) => {

    elemSet.forEach(elem => {

      elem.classList.remove(className);

    });

  };

  //return exect parent node of the element
  const findParent = (elem, parentClass) => {

    let currentNode = elem;

    while (!currentNode.classList.contains(parentClass)) {
      currentNode = currentNode.parentNode;
    }

    return currentNode;

  };

  //get active button step number
  const getActiveStep = elem => {
    return Array.from(DOMstrings.stepsBtns).indexOf(elem);
  };

  //set all steps before clicked (and clicked too) to active
  const setActiveStep = activeStepNum => {

    //remove active state from all the state
    removeClasses(DOMstrings.stepsBtns, 'js-active');

    //set picked items to active
    DOMstrings.stepsBtns.forEach((elem, index) => {

      if (index <= activeStepNum) {
        elem.classList.add('js-active');
      }

    });
  };

  //get active panel
  const getActivePanel = () => {

    let activePanel;

    DOMstrings.stepFormPanels.forEach(elem => {

      if (elem.classList.contains('js-active')) {

        activePanel = elem;

      }

    });

    return activePanel;

  };

  //open active panel (and close unactive panels)
  const setActivePanel = activePanelNum => {

    //remove active class from all the panels
    removeClasses(DOMstrings.stepFormPanels, 'js-active');

    //show active panel
    DOMstrings.stepFormPanels.forEach((elem, index) => {
      if (index === activePanelNum) {

        elem.classList.add('js-active');

        setFormHeight(elem);

      }
    });

  };

  //set form height equal to current panel height
  const formHeight = activePanel => {

    const activePanelHeight = activePanel.offsetHeight;

    DOMstrings.stepsForm.style.height = `${activePanelHeight}px`;

  };

  const setFormHeight = () => {
    const activePanel = getActivePanel();

    formHeight(activePanel);
  };

  //STEPS BAR CLICK FUNCTION
  DOMstrings.stepsBar.addEventListener('click', e => {

    //check if click target is a step button
    const eventTarget = e.target;

    if (!eventTarget.classList.contains(`${DOMstrings.stepsBtnClass}`)) {
      return;
    }

    //get active button step number
    const activeStep = getActiveStep(eventTarget);

    //set all steps before clicked (and clicked too) to active
    setActiveStep(activeStep);

    //open active panel
    setActivePanel(activeStep);
  });

  //PREV/NEXT BTNS CLICK
  DOMstrings.stepsForm.addEventListener('click', e => {

    const eventTarget = e.target;

    //check if we clicked on `PREV` or NEXT` buttons
    if (!(eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`) || eventTarget.classList.contains(`${DOMstrings.stepNextBtnClass}`))) {
      return;
    }

    //find active panel
    const activePanel = findParent(eventTarget, `${DOMstrings.stepFormPanelClass}`);

    let activePanelNum = Array.from(DOMstrings.stepFormPanels).indexOf(activePanel);

    //set active step and active panel onclick
    if (eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`)) {
      activePanelNum--;

    } else {

      activePanelNum++;

    }

    setActiveStep(activePanelNum);
    setActivePanel(activePanelNum);

  });

  //SETTING PROPER FORM HEIGHT ONLOAD
  window.addEventListener('load', setFormHeight, false);

  //SETTING PROPER FORM HEIGHT ONRESIZE
  window.addEventListener('resize', setFormHeight, false);

  //changing animation via animation select !!!YOU DON'T NEED THIS CODE (if you want to change animation type, just change form panels data-attr)

  const setAnimationType = newType => {
    DOMstrings.stepFormPanels.forEach(elem => {
      elem.dataset.animation = newType;
    });
  };

  //selector onchange - changing animation
  const animationSelect = document.querySelector('.pick-animation__select');

  animationSelect.addEventListener('change', () => {
    const newAnimationType = animationSelect.value;

    setAnimationType(newAnimationType);
  });

  // ** add remove *** //
</script>

<script src="<?= BASE_URL ?>public/assets/plugins/select2/js/select2.min.js"></script>
</body>

</html>