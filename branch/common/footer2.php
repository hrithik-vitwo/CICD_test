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
          <span id="screenshot-info" style="display: none;bottom: 20px;right: 20px;background: #e0f7fa;color: #006064; border: 1px solid #4dd0e1;padding: 10px 15px;border-radius: 8px;z-index: 9999;font-family: Arial, sans-serif;">
            <i class="fa fa-info-circle"></i> Please take a manual screenshot and submit.
          </span>
          <div class="form-input">
            <label for="" id="attachmentlevel"></label>
            <input type="file" name="attachment" class="form-control" id="attachment">
          </div>
          <div class="form-input">
            <label for="">Describe your issue</label>
            <textarea class="form-control remarkText" name="bug_description" id="bug_description" cols="55" rows="2" placeholder="Write here ..."></textarea>
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
<div id="loaderModal" class="modal" style="display: none;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center">
        <p>Downloading, please wait...</p>
        <div class="spinner-border text-primary" role="status"></div>
      </div>
    </div>
  </div>
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
<script type="text/javascript" src="<?= BASE_URL ?>public/assets/xlsx.full.min.js"></script>


<!-- stock-report-new  -->

<!-- Resources -->
<script src="<?= BASE_URL ?>public/assets/core.js"></script>
<script src="<?= BASE_URL ?>public/assets/charts.js"></script>
<script src="<?= BASE_URL ?>public/assets/animated.js"></script>
<script src="<?= BASE_URL ?>public/assets/forceDirected.js"></script>
<script src="<?= BASE_URL ?>public/assets/sunburst.js"></script>
<script src="<?= BASE_URL ?>public/assets/padMake.js"></script>
<script src="<?= BASE_URL ?>public/assets/pdfRobotofont.js"></script>
<script src="<?= BASE_URL ?>public/assets/cookies.js"></script>
<style>
  .popup {
    backdrop-filter: blur(15px);
  }
</style>


<!-- script for closing dropout modal view,edir,show etc button -->
<script>
  document.querySelector('table.stock-new-table').onclick = ({
    target
  }) => {
    if (!target.classList.contains('more')) return
    document.querySelectorAll('.dropout.active').forEach(
      (d) => d !== target.parentElement && d.classList.remove('active')
    )
    target.parentElement.classList.toggle('active')
  }

  window.onclick = function(event) {
    if (!event.target.closest('table.stock-new-table')) {
      document.querySelectorAll('.dropout.active').forEach(function(dropout) {
        dropout.classList.remove('active');
      });
    }
  };
</script>

<!-- global list modification JS -->



<script>
  document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('dataTable_detailed_view');

    let draggingEle;
    let draggingColumnIndex;
    let placeholder;
    let list;
    let isDraggingStarted = false;

    let x = 0;
    let y = 0;

    const swap = function(nodeA, nodeB) {
      const parentA = nodeA.parentNode;
      const siblingA = nodeA.nextSibling === nodeB ? nodeA : nodeA.nextSibling;


      nodeB.parentNode.insertBefore(nodeA, nodeB);

      parentA.insertBefore(nodeB, siblingA);
    };

    const isOnLeft = function(nodeA, nodeB) {
      const rectA = nodeA.getBoundingClientRect();
      const rectB = nodeB.getBoundingClientRect();

      return rectA.left + rectA.width / 2 < rectB.left + rectB.width / 2;
    };

    const cloneTable = function() {
      const rect = table.getBoundingClientRect();

      list = document.createElement('div');
      list.classList.add('clone-list');
      list.style.position = 'absolute';
      list.style.left = rect.left + 'px';
      list.style.top = rect.top + 'px';
      table.parentNode.insertBefore(list, table);

      table.style.visibility = 'hidden';

      const originalCells = [].slice.call(table.querySelectorAll('tbody td'));

      const originalHeaderCells = [].slice.call(table.querySelectorAll('th'));
      const numColumns = originalHeaderCells.length;

      originalHeaderCells.forEach(function(headerCell, headerIndex) {
        const width = parseInt(window.getComputedStyle(headerCell).width);

        const item = document.createElement('div');
        item.classList.add('draggable');

        const newTable = document.createElement('table');
        newTable.setAttribute('class', 'clone-table');
        newTable.style.width = width + 'px';

        const th = headerCell.cloneNode(true);
        let newRow = document.createElement('tr');
        newRow.appendChild(th);
        newTable.appendChild(newRow);

        const cells = originalCells.filter(function(c, idx) {
          return (idx - headerIndex) % numColumns === 0;
        });
        cells.forEach(function(cell) {
          const newCell = cell.cloneNode(true);
          newCell.style.width = width + 'px';
          newRow = document.createElement('tr');
          newRow.appendChild(newCell);
          newTable.appendChild(newRow);
        });

        item.appendChild(newTable);
        list.appendChild(item);
      });
    };

    const mouseDownHandler = function(e) {
      draggingColumnIndex = [].slice.call(table.querySelectorAll('th')).indexOf(e.target);

      x = e.clientX - e.target.offsetLeft;
      y = e.clientY - e.target.offsetTop;

      document.addEventListener('mousemove', mouseMoveHandler);
      document.addEventListener('mouseup', mouseUpHandler);
    };

    const mouseMoveHandler = function(e) {
      if (!isDraggingStarted) {
        isDraggingStarted = true;

        cloneTable();

        draggingEle = [].slice.call(list.children)[draggingColumnIndex];
        draggingEle.classList.add('dragging');

        placeholder = document.createElement('div');
        placeholder.classList.add('placeholder');
        draggingEle.parentNode.insertBefore(placeholder, draggingEle.nextSibling);
        placeholder.style.width = draggingEle.offsetWidth + 'px';
      }

      draggingEle.style.position = 'absolute';
      draggingEle.style.top = (draggingEle.offsetTop + e.clientY - y) + 'px';
      draggingEle.style.left = (draggingEle.offsetLeft + e.clientX - x) + 'px';

      x = e.clientX;
      y = e.clientY;

      const prevEle = draggingEle.previousElementSibling;
      const nextEle = placeholder.nextElementSibling;

      if (prevEle && isOnLeft(draggingEle, prevEle)) {

        swap(placeholder, draggingEle);
        swap(placeholder, prevEle);
        return;
      }

      if (nextEle && isOnLeft(nextEle, draggingEle)) {

        swap(nextEle, placeholder);
        swap(nextEle, draggingEle);
      }
    };

    const mouseUpHandler = function() {

      placeholder && placeholder.parentNode.removeChild(placeholder);

      draggingEle.classList.remove('dragging');
      draggingEle.style.removeProperty('top');
      draggingEle.style.removeProperty('left');
      draggingEle.style.removeProperty('position');

      const endColumnIndex = [].slice.call(list.children).indexOf(draggingEle);

      isDraggingStarted = false;

      list.parentNode.removeChild(list);

      table.querySelectorAll('tr').forEach(function(row) {
        const cells = [].slice.call(row.querySelectorAll('th, td'));
        draggingColumnIndex > endColumnIndex ?
          cells[endColumnIndex].parentNode.insertBefore(
            cells[draggingColumnIndex],
            cells[endColumnIndex]
          ) :
          cells[endColumnIndex].parentNode.insertBefore(
            cells[draggingColumnIndex],
            cells[endColumnIndex].nextSibling
          );
      });


      table.style.removeProperty('visibility');


      document.removeEventListener('mousemove', mouseMoveHandler);
      document.removeEventListener('mouseup', mouseUpHandler);
    };

    table.querySelectorAll('th').forEach(function(headerCell) {
      headerCell.classList.add('draggable');
      headerCell.addEventListener('mousedown', mouseDownHandler);
    });
  });
</script>

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
    var ccode = this.getAttribute('data-ccode');
    var cid = this.getAttribute('data-cid')
    // console.log(ccode);
    // alert(ccode);

    $.ajax({
      url: 'ajaxs/audittrail/ajax-audit-trail.php?auditTrailBodyContent', // <-- point to server-side PHP script 
      type: 'POST',
      data: {
        ccode,
        cid
      },
      beforeSend: function() {
        // $('.Ckecked_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
        // $(".Ckecked_loder").toggleClass("disabled");
      },
      success: function(responseData) {
        // console.log(responseData);
        $(`.auditTrailBodyContent`).html(responseData);
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
  let show = false;
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

  $(document).ready(function() {
    // Get the close button
    var closeButton = $('.popup-close');
    // Function to close the popup
    function closePopup() {
      //alert(1);
      $('.popup').hide();
      if (show == true) {
        // alert(show);
        $("#viewGlobalModal").modal("show");
        show == false;
      }
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
  $('body').on("click", '.bug_submit', function() {
    if (show == true) {
      if ($('#attachment').get(0).files.length === 0) {
        alert('Please select an attachment file.');
        return; // stop submission
      }
    }
    // Serialize the form data
    $('.bug_submit').prop('disabled', true);
    var formData = new FormData();

    // Append form data to the FormData object using native JavaScript
    $('#bug_frm').find('input, select, textarea').each(function() {
      var name = $(this).attr('name');

      if (!name) return;

      // If show is true, skip adding bug_image and bug_image_url
      if (show === true && (name === 'bug_image' || name === 'bug_image_url')) return;

      formData.append(name, $(this).val());
    });

    // Add file if selected
    var fileInput = $('#attachment')[0];
    if (fileInput.files.length > 0) {
      formData.append('attachment', fileInput.files[0]);
    }

    // Debug output
    for (var pair of formData.entries()) {
      console.log(pair[0] + ':', pair[1]);
    }


    $.ajax({
      url: '<?= BASE_URL ?>save_screenshot.php?act=bug_submit', // Replace with your upload URL
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        // Handle the server response if needed
        var jsonObject = JSON.parse(response);
        console.log(jsonObject);
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
        if (show == true) {
          // alert(show);
          $("#viewGlobalModal").modal("show");
        }
        const canvasElement = document.getElementById("drawingCanvas");
        const sourceImage = document.getElementById("screenshotImage");
        canvasElement.style.display = "none";
        sourceImage.style.display = "block";
        document.getElementById('undoBtn').style.visibility = 'hidden';

      },
      error: function(xhr, status, error) {
        // Handle errors if any
        console.error('error');
      }
    });
  });

  //open modal
  $('#openPopup').on('click touchstart', function() {
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
    // Only block screenshot if #viewGlobalModal has the .show class


    // Show the loader
    $("#loader").show();

    const scrollY = window.scrollY;
    const config = {
      scrollY: -scrollY,
      height: window.innerHeight
    };

    const isGlobalModalVisible = $('#viewGlobalModal').hasClass('show');
    //alert(isGlobalModalVisible);
    if (isGlobalModalVisible == true) {
      //alert(1);
      $(".bug-screenshot").hide();
      $("#screenshot-info").css("display", "block");
      $("#attachmentlevel").text("Attachment");

      show = true;
    } else {
      //alert(0);
      $(".bug-screenshot").show();
      $("#screenshot-info").css("display", "none");
      $("#attachmentlevel").text("Additional Attachment(optional)");

      show = false;
    }
    //alert(show);
    html2canvas(document.body, config).then(function(canvas) {
      const bug_pngUrl = canvas.toDataURL("image/png");
      const bug_fullURL = window.location.href;
      const now = new Date();

      const bug_dateTime = now.toLocaleString();
      const bug_dateString = `${now.getFullYear()}_${now.getMonth() + 1}_${now.getDate()}`;
      const bug_uniqueId = Math.floor(Math.random() * 1e15);
      const bug_filename = `bug_${bug_dateString}_${bug_uniqueId}.png`;
      const bug_page_name = document.title;

      $(".screenBug").attr("src", bug_pngUrl);
      $(".bug_currentDateclass").text(bug_dateTime);
      $(".bug_currentUrl").text(bug_fullURL);
      $("#bug_page_url").val(bug_fullURL);
      $("#bug_image").val(bug_pngUrl);
      $("#bug_image_url").val(bug_filename);
      $("#bug_page_name").val(bug_page_name);

      $("#bugModal").show();
      $(".fa-bug, .report-bug").show();

    }).catch(function(error) {
      console.error("Screenshot error:", error);
      //alert("Failed to capture screenshot.");
    }).finally(function() {
      $("#viewGlobalModal").modal("hide");
      $("#loader").hide();
    });
  }


  // let markButtonClicked = false;
  let mouseDown = false;

  $('body').on("keyup", '.remarkText', function() {
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
    canvasElement.onmouseup = function() {
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

    $('body').on("click", '.undoButn', function() {
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


  $(document).ready(function() {

    // add class to advance filter modal
    let advFilterElement = document.getElementById('btnSearchCollpase_modal');
    advFilterElement.classList.add('advanced-search-modal');

    // add class to bill Address modal
    let billElement = document.getElementById('billAddress');
    billElement.classList.add('bill-address');


    // add class to bill Address modal
    let shipElement = document.getElementById('shipAddress');
    shipElement.classList.add('ship-address');


    // Form reset button
    $(document).on("click", "#serach_reset", function(e) {
      e.preventDefault();
      $("#myForm")[0].reset();
      $("#serach_submit").click();
    });

    // Enter to search
    $(document).on("keypress", "#myForm input", function(e) {
      if (e.key === "Enter") {
        $("#serach_submit").click();
        e.preventDefault();
      }
    });

  });

  // **********************  FUNCTIONS uses in new TEMPLATE list *******************************

  // Function to convert date to DD/MM/YY format
  const formatDate = (date) => {
    if (date != null && date != "" && date != undefined) {
      let showDate = new Date(date);
      let dd = showDate.getDate();
      let mm = showDate.getMonth() + 1;
      let yyyy = showDate.getFullYear();
      dd = dd < 10 ? '0' + dd : dd;
      mm = mm < 10 ? '0' + mm : mm;
      return dd + '-' + mm + '-' + yyyy;
    }
    return "";
  }
  const formatDateTime = (datetime) => {
    if (datetime != null && datetime !== "" && datetime !== undefined) {
      let showDate = new Date(datetime);

      let dd = showDate.getDate();
      let mm = showDate.getMonth() + 1;
      let yyyy = showDate.getFullYear();

      dd = dd < 10 ? '0' + dd : dd;
      mm = mm < 10 ? '0' + mm : mm;

      let hours = showDate.getHours();
      let minutes = showDate.getMinutes();
      let ampm = hours >= 12 ? 'PM' : 'AM';
      hours = hours % 12;
      hours = hours ? hours : 12; // the hour '0' should be '12'
      minutes = minutes < 10 ? '0' + minutes : minutes;

      let timeStr = hours + ':' + minutes + ' ' + ampm;

      return `${dd}-${mm}-${yyyy} ${timeStr}`;
    }
    return "";
  }


  //  caps the first character of the string
  function capFirstLetter(string) {
    if (string != null) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    } else {
      return "";
    }
  }

  // function to number format Quantity  
  function decimalQuantity(number) {
    if (number != null || number != "") {
      number = number ?? 0;
      let num = parseFloat(number);
      if (isNaN(num)) {
        return number;
      }
      let base = <?= $decimalQuantity ?>;
      let res = num.toFixed(base);
      return res;
    }
    return "";
  }

  function inputQuantity(number) {
    if (number !== null && number !== "") {
      number = number ?? 0;
      let num = parseFloat(number);
      if (isNaN(num)) {
        return number;
      }
      let base = <?= $decimalQuantity ?>;
      let factor = Math.pow(10, base);
      let truncated = Math.trunc(num * factor) / factor;
      return truncated.toString().replace(/,/g, '');
    }
    return "";
  }

  // function to number format Amount
  function decimalAmount(number) {
    if (number != null || number != "") {
      number = number ?? 0;
      let num = parseFloat(number);
      if (isNaN(num)) {
        return number;
      }
      let base = <?= $decimalValue ?>;
      let res = num.toFixed(base);
      return res;
    }
    return "";
  }

  function inputValue(number) {
    if (number !== null && number !== "") {
      number = number ?? 0;
      let num = parseFloat(number);
      if (isNaN(num)) {
        return number;
      }
      let base = <?= $decimalValue ?>;
      let factor = Math.pow(10, base);
      let truncated = Math.trunc(num * factor) / factor;
      return truncated.toFixed(base);
    }
    return "";
  }

  function helperAmount(amt) {
    let returnValue = 0;
    let tempVal = String(amt);
    let valArr = tempVal.split(".");
    let leftVal = valArr[0];
    let rightValTemp = (valArr[1] || "") + "00";
    let base = <?= $decimalValue ?? 2 ?>;
    let rightVal = rightValTemp.substring(0, base);
    returnValue = parseFloat(`${leftVal}.${rightVal}`);
    return returnValue;
  }

  function helperQuantity(qty) {
    let returnValue = 0;
    let tempVal = String(qty);
    let valArr = tempVal.split(".");
    let leftVal = valArr[0];
    let rightValTemp = (valArr[1] || "") + "00";
    let base = <?= $decimalQuantity ?? 2 ?>;
    let rightVal = rightValTemp.substring(0, base);
    returnValue = parseFloat(`${leftVal}.${rightVal}`);
    return returnValue;
  }



  // convert large string into short AND ... End of it
  function trimString(str, num) {
    if (typeof str !== 'string' || !Number.isInteger(num)) {
      return str;
    }

    if (str === "") {
      return str;
    }

    if (num >= str.length) {
      return str;
    } else {
      const trimmedStr = str.slice(0, num) + "...";
      return trimmedStr;
    }
  }

  // set title to the given id
  function setTitleAttributeById(elementId, titleText) {
    let element = document.getElementById(elementId);
    if (element) {
      element.setAttribute('title', titleText);
    } else {
      console.error('Element with id ' + elementId + ' not found.');
    }
  }

  // function for download all data
  function downloadCSV() {
    var blob = new Blob([csvContent], {
      type: 'text/csv'
    });

    var url = URL.createObjectURL(blob);
    var link = document.createElement('a');
    link.href = url;
    link.download = '<?= $newFileNameDownloadall ?>';
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  $(document).on("click", ".ion-fulllist", function() {
    downloadCSV();
  });

  // function for downloadCSV data by pagination
  function downloadCSVBypagin() {
    var blob = new Blob([csvContentBypagination], {
      type: 'text/csv'
    });

    var url = URL.createObjectURL(blob);
    var link = document.createElement('a');
    link.href = url;
    link.download = '<?= $newFileName ?>';
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  $(document).on("click", ".ion-paginationlist", function() {
    downloadCSVBypagin();
  });

  // export tag open js

  document.querySelector('table.stock-new-table').onclick = ({
    target
  }) => {
    if (!target.classList.contains('more')) return
    document.querySelectorAll('.dropout.active').forEach(
      (d) => d !== target.parentElement && d.classList.remove('active')
    )
    target.parentElement.classList.toggle('active')
  }

  window.onclick = function(event) {
    if (!event.target.closest('table.stock-new-table')) {
      document.querySelectorAll('.dropout.active').forEach(function(dropout) {
        dropout.classList.remove('active');
      });
    }
  };

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.exportgroup').forEach(function(exportgroup) {
      exportgroup.querySelector('.exceltype').addEventListener('click', function() {
        exportgroup.classList.toggle('active');
      });
    });

    window.addEventListener('click', function(event) {
      if (!event.target.closest('.exportgroup')) {
        document.querySelectorAll('.exportgroup.active').forEach(function(exportgroup) {
          exportgroup.classList.remove('active');
        });
      }
    });
  });

  $(document).on("input keyup paste blur", ".inputQuantityClass", function() {
    let val = $(this).val();
    let base = <?= $decimalQuantity ?>;
    // Allow only numbers and one decimal point
    if (val.includes(".")) {
      let parts = val.split(".");
      if (parts[1].length > base) {
        $(this).val(parts[0] + "." + parts[1].substring(0, base)); // Restrict extra decimals
      }
    }
  });

  $(document).on("input keyup paste blur", ".inputAmountClass", function() {
    let val = $(this).val();
    let base = <?= $decimalValue ?>;
    // Allow only numbers and one decimal point
    if (val.includes(".")) {
      let parts = val.split(".");
      if (parts[1].length > base) {
        $(this).val(parts[0] + "." + parts[1].substring(0, base)); // Restrict extra decimals
      }
    }
  });
</script>



<script src="<?= BASE_URL ?>public/assets/plugins/select2/js/select2.min.js"></script>
</body>

</html>