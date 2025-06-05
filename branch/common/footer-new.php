<!-- /.content-wrapper -->
<footer class="main-footer text-muted">
  <!-- Modal -->
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
            <img width="100%" class="screenBug" src="" id="screenshotImage" title="Loading...">
            <canvas id="drawingCanvas" class="drawClass" style="display:none"></canvas>
          </div>
          <div class="form-input">
            <label for="">Describe your issue</label>
            <textarea class="form-control remarkText" name="bug_description" id="bug_description" cols="55" rows="2"
              placeholder="Write here ..."></textarea>
          </div>
        </form>
      </div>
      <div class="card-footer">
        <button type="button" id="submitBtn" class="btn btn-primary float-right bug_submit ml-2" disabled>Submit</button>
        <button class=" btn btn-primary float-right undoButn" id="undoBtn" style="visibility:hidden;">Undo</button>
        <button class="popup-close btn btn-danger">Close</button>
      </div>
    </div>
  </div>

  <div class="sticky-icon" id="draggableDiv">
    <a id="openPopup">
      <p><span id="loader" class="spinner-border" role="status" style="display:none"></span></p>
      <i class="fa fa-bug"> </i>
      <p class="report-bug">Report a Bug </p>
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
            <!---------------------Audit History Tab Body End--------------------------->
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- --------------------------Audit Trail single History Modal Endd---------------- -->


</footer>
</div>
<!-- ./wrapper -->
<script src="<?= BASE_URL ?>public/assets/script.js"></script>
<script src="<?= BASE_URL ?>public/assets/plugins/js-barcode/JsBarcode.all.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
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
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>

<script>
  function ExportToExcel(type, tableId, fileName = "my-excel-file", fn, dl) {
    console.log("Exporting to Excel");
    var elt = document.getElementById(tableId);
    var wb = XLSX.utils.table_to_book(elt, {
      sheet: "sheet1"
    });
    return dl ?
      XLSX.write(wb, {
        bookType: type,
        bookSST: true,
        type: 'base64'
      }) :
      XLSX.writeFile(wb, fn || (fileName + '.' + (type || 'xlsx')));
  }

  function getIntValue(value = null) {
    return parseFloat(value) > 0 ? parseFloat(value) : 0;
  }
</script>
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
  //bug-icon draggable
  // Get the draggable div element
  const draggableDiv = document.getElementById("draggableDiv");
  let isDragging = false;
  let initialX;
  let initialY;
  // Function to handle the start of the touch
  function startDrag(event) {
    isDragging = true;
    // Store the initial touch position
    initialX = event.touches[0].clientX;
    initialY = event.touches[0].clientY;
    // Prevent any default browser behavior during dragging
    event.preventDefault();
  }
  // Function to handle the touch movement
  function drag(event) {
    if (isDragging) {
      // Calculate the distance moved by the touch
      const deltaX = event.touches[0].clientX - initialX;
      const deltaY = event.touches[0].clientY - initialY;
      // Update the position of the div
      draggableDiv.style.left = draggableDiv.offsetLeft + deltaX + "px";
      draggableDiv.style.top = draggableDiv.offsetTop + deltaY + "px";
      // Update the initial touch position for the next drag movement
      initialX = event.touches[0].clientX;
      initialY = event.touches[0].clientY;
    }
  }
  // Function to handle the end of the touch
  function endDrag() {
    isDragging = false;
  }
  // Attach event listeners to enable drag functionality on touch devices
  draggableDiv.addEventListener("touchstart", startDrag);
  draggableDiv.addEventListener("touchmove", drag);
  draggableDiv.addEventListener("touchend", endDrag);
  // ------------------ Audit Trail script End----------------------------

  $(document).ready(function () {
    // Get the close button
    var closeButton = $('.popup-close');
    // Function to close the popup
    function closePopup() {
      $('.popup').hide();
      const canvasElement = document.getElementById("drawingCanvas");
      const sourceImage = document.getElementById("screenshotImage");
      canvasElement.style.display = "none";
      sourceImage.style.display = "block";
      document.getElementById('undoBtn').style.visibility = 'hidden';
      document.getElementById("bug_page_url").value = "";
      document.getElementById("bug_image").value = "";
      // $('#bug_frm')[0].reset();
      let bug_img = document.querySelector(".screenBug");
      bug_img.src = '';
    }
    // Event listener for the close button
    closeButton.on('click', closePopup);
  });

  //submit bug image
  $('body').on("click", '.bug_submit', function () {
    // Serialize the form data
    var formData = $('#bug_frm').serialize();
    $('.bug_submit').prop('disabled', true);
    console.log(formData);
    $.ajax({
      url: '<?= BASE_URL ?>save_screenshot.php?act=bug_submit', // Replace with your server-side script URL
      type: 'POST',
      data: formData,
      success: function (response) {
        // Handle the server response if needed
        console.log(response);
        var jsonObject = JSON.parse(response);
        Swal.fire({
          icon: jsonObject['status'],
          title: jsonObject['bug_code'],
          text: jsonObject['message'],
        });

        document.getElementById("bug_page_url").value = "";
        document.getElementById("bug_image").value = "";
        let bug_img = document.querySelector(".screenBug");
        bug_img.src = '';
        $('.bug_submit').prop('disabled', false);
        $('#bugModal').hide();
        const canvasElement = document.getElementById("drawingCanvas");
        const sourceImage = document.getElementById("screenshotImage");
        canvasElement.style.display = "none";
        sourceImage.style.display = "block";
        document.getElementById('undoBtn').style.visibility = 'hidden';

      },
      error: function (xhr, status, error) {
        // Handle errors if any
        console.error('error');
      }
    });
  });

  //open modal
  $('#openPopup').on('click', function () {
    $("#loader").show();
    screenshot();
    resetGlobalModal();
    $('.fa-bug').hide();
    $('.report-bug').hide();
   
  });
  // Function to reset the global modal
  function resetGlobalModal() {
    $('#bug_frm')[0].reset(); // Reset form fields
    document.getElementById("bug_page_url").value = "";
    document.getElementById("bug_image").value = "";
    $('.screenBug').attr('src', ''); // Clear the image src
    if ($('#bugModal').hasClass('show')) {
      $('#bugModal').modal('hide');
    }
  }

  function screenshot() {
    var scrollY = window.scrollY;
    let config = {
      scrollY: -scrollY,
      height: window.innerHeight,
    };

    html2canvas(document.body,
      config).then(function (canvas) {
        // Create a modal element 
        $('#bugModal').show();
        // Convert the canvas to a data URL
        let bug_pngUrl = canvas.toDataURL("image/png"); // png in dataURL format
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
        $('#bug_image').val(bug_pngUrl);
        $("#bug_image_url").val(bug_filename);
        var bug_page_name = $(document).prop('title');
        console.log(bug_page_name);
        $("#bug_page_name").val(bug_page_name);
        $("#loader").hide();
        $('.fa-bug').show();
        $('.report-bug').show();

      });

  }


  // let markButtonClicked = false;
  let mouseDown = false;

  $('body').on("keyup", '.remarkText', function () {
    checkTextArea();
    if (document.getElementById("screenshotImage").style.display === "block") {
        const sourceImage = document.getElementById("screenshotImage").src;
        $("#bug_image").val(sourceImage);
    }
});

  function checkTextArea() {
    var textarea = document.getElementById('bug_description');
    var submitBtnId = document.getElementById('submitBtn');
    if (textarea.value.trim() !== "") {
      submitBtnId.disabled = false;
    } else {
      submitBtnId.disabled = true;

    }
  }

  //function for draw on canavs
  function drawCanvas() {
    const canvasElement = document.getElementById("drawingCanvas");
    const context = canvasElement.getContext("2d");
    const sourceImage = document.getElementById("screenshotImage");
    const sourceImageRect = sourceImage.getBoundingClientRect();
    canvasElement.width = sourceImageRect.width;
    canvasElement.height = sourceImageRect.height;
    context.drawImage(sourceImage, 0, 0, sourceImage.width, sourceImage.height);
    const editedImageURL = canvasElement.toDataURL("image/png");
    $("#bug_image").val(editedImageURL);
  }

  // // mark function
  function markButton() {
    const canvasElement = document.getElementById("drawingCanvas");
    const context = canvasElement.getContext("2d");
    const sourceImage = document.getElementById("screenshotImage");
    const sourceImageRect = sourceImage.getBoundingClientRect();
    let drawingHistory = [];
    let index = -1;
    canvasElement.style.display = "block";
    sourceImage.style.display = "none";
    canvasElement.width = sourceImageRect.width;
    canvasElement.height = sourceImageRect.height;
    context.drawImage(sourceImage, 0, 0, sourceImageRect.width, sourceImageRect.height);
    let isDrawing = false;
    canvasElement.onmousedown = (e) => {
      isDrawing = true;
      context.beginPath();
      context.strokeStyle = "red";
      context.lineWidth = 1;
      const rect = canvasElement.getBoundingClientRect();
      const scaleX = canvasElement.width / rect.width;
      const scaleY = canvasElement.height / rect.height;
      const x = (e.clientX - rect.left) * scaleX;
      const y = (e.clientY - rect.top) * scaleY;
      context.moveTo(x, y);
      mouseDown = true;
      document.getElementById('undoBtn').style.visibility = 'visible';
      const editedImageURL = canvasElement.toDataURL("image/png");
      $("#bug_image").val(editedImageURL);
    };
    canvasElement.onmousemove = (e) => {

      if (isDrawing) {
        const rect = canvasElement.getBoundingClientRect();
        const scaleX = canvasElement.width / rect.width;
        const scaleY = canvasElement.height / rect.height;
        const x = (e.clientX - rect.left) * scaleX;
        const y = (e.clientY - rect.top) * scaleY;
        context.lineTo(x, y);
        context.strokeStyle = '#ff0000';
        context.stroke();
      }
    };
    canvasElement.onmouseup = function () {
      isDrawing = false;
      context.closePath();
      const editedImageURL = canvasElement.toDataURL("image/png");
      $("#bug_image").val(editedImageURL);
      drawingHistory.push(context.getImageData(0, 0, sourceImageRect.width, sourceImageRect.height));
      index += 1;
    };
    function clear_Canvas() {
      context.clearRect(0, 0, canvasElement.width, canvasElement.height);
      context.drawImage(sourceImage, 0, 0, sourceImageRect.width, sourceImageRect.height);
      drawingHistory = [];
      index -= 1;
    }
    function undoCanvas() {
      if (index <= 0) {
        clear_Canvas();
        const editedImageURL = canvasElement.toDataURL("image/png");
        $("#bug_image").val(editedImageURL);
      } else {
        index -= 1;
        drawingHistory.pop();
        context.putImageData(drawingHistory[index], 0, 0);
        const editedImageURL = canvasElement.toDataURL("image/png");
        $("#bug_image").val(editedImageURL);
      }
    }
    if (!mouseDown) {
      context.drawImage(sourceImage, 0, 0, sourceImageRect.width, sourceImageRect.height);
      const editedImageURL = canvasElement.toDataURL("image/png");
      $("#bug_image").val(editedImageURL);
    }

    $('body').on("click", '.undoButn', function () {
      undoCanvas();
    });

  }



  //mark fucntion call
  const sourceImage = document.getElementById("screenshotImage");
  sourceImage.onmousemove = (e) => {
    markButton();
    const canvasElement = document.getElementById("drawingCanvas");
    const editedImageURL = canvasElement.toDataURL("image/png");
    $("#bug_image").val(editedImageURL);
  };


  //-------------------------------

  function load_js() {
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.4.1/js/mdb.min.js';
    head.appendChild(script);
  }
  load_js();
  // ***//
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
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
</script>


<script src="<?= BASE_URL ?>public/assets/plugins/select2/js/select2.min.js"></script>
</body>

</html>