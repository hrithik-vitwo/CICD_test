<!-- /.content-wrapper -->
<footer class="main-footer text-muted">
  <strong class="text-muted"><a href="<?= ADMIN_URL ?>"><?= getAdministratorSettings("footer"); ?></a></strong>
  <div class="float-right d-none d-sm-inline-block">
    <b>Date :</b> <?= date("d-m-Y"); ?>
  </div>
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->


<!-- jQuery UI 1.11.4 -->
<script src="../public/assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="../public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- sweetalert2 -->
<script src="../public/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- ChartJS -->
<script src="../public/assets/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="../public/assets/plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="../public/assets/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="../public/assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="../public/assets/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../public/assets/plugins/moment/moment.min.js"></script>
<script src="../public/assets/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../public/assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="../public/assets/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../public/assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

<!-- DataTables  & Plugins -->
<script src="../public/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../public/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../public/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../public/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../public/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../public/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../public/assets/plugins/jszip/jszip.min.js"></script>
<script src="../public/assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../public/assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../public/assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../public/assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<!-- BS-Stepper -->
<script src="../public/assets/plugins/bs-stepper/js/bs-stepper.min.js"></script>

<!-- AdminLTE App -->
<script src="../public/assets/AdminLTE/dist/js/adminlte.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../public/assets/AdminLTE/dist/js/demo.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../public/assets/AdminLTE/dist/js/jquery.validate.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../public/assets/AdminLTE/dist/js/pages/dashboard.js"></script>

<script>

  // ***//

  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
  $(function () {
    $(".defaultDataTable").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
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
  document.addEventListener('DOMContentLoaded', function () {
    window.stepper = new Stepper(document.querySelector('.bs-stepper'))
  });
  $("input[type='checkbox']").change(function () {
    var val = $(this).val();
    $("#mytable tr:first").find("th:eq(" + val + ")").toggle();
    $("#mytable tr").each(function () {
      $(this).find("td:eq(" + val + ")").toggle();
    });
    if ($("#mytable tr:first").find("th:visible").length > 0) {
      $("#mytable").removeClass("noborder");
    }
    else {
      $("#mytable").addClass("noborder");
    }
  });
  $("#selector5").click(function () {
    $("#main2").toggle();

  });

  // *****  //

  $(function () {
    $(".checkbox").click(function () {
      if ($(this).is(":checked")) {
        $("#multistepform").show();
        $("#gstform").hide();
      } else {
        $("#multistepform").hide();
        $("#gstform").show();
      }
    });
  });

  $(function () {
    $("#checkbox2").click(function () {
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
<!-- add decimal function -->
<script>
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
    if (number != null || number != "") {
      number = number ?? 0;
      let num = parseFloat(number);
      if (isNaN(num)) {
        return number;
      }
      let base = <?= $decimalQuantity ?>;
      let res = num.toFixed(base);
      return res.replace(/,/g, '');
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
      let res = num.toFixed(base);
      return res.replace(/,/g, ''); // Ensure no commas
    }
    return "";
  }

  function helperAmount(amt, isRound = false) {
    let returnValue = 0;
    if (isRound) {
      returnValue = Math.round(parseFloat(amt) * 100) / 100;
    } else {
      let tempVal = String(amt);
      let valArr = tempVal.split(".");
      let leftVal = valArr[0];
      let rightValTemp = (valArr[1] || "") + "00";
      let base = <?= $decimalValue ?>;
      let rightVal = rightValTemp.substring(0, base);
      returnValue = parseFloat(`${leftVal}.${rightVal}`);
    }
    return returnValue;
  }

  function helperQuantity(val, isRound = false) {
    let returnValue = 0;
    if (isRound) {
      returnValue = Math.round(parseFloat(val) * 100) / 100;
    } else {
      let tempVal = String(val);
      let valArr = tempVal.split(".");
      let leftVal = valArr[0];
      let rightValTemp = (valArr[1] || "") + "00";
      let base = <?= $decimalQuantity ?>;
      let rightVal = rightValTemp.substring(0, base);
      returnValue = parseFloat(`${leftVal}.${rightVal}`);
    }
    return returnValue;
  }


</script>
<script src="../public/assets/plugins/select2/js/select2.min.js"></script>
</body>

</html>