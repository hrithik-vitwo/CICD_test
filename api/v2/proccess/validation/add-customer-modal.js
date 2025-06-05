// DOM elements
const DOMstrings = {
  stepsBtnClass: "multisteps-form__progress-btn",
  stepsBtns: document.querySelectorAll(`.multisteps-form__progress-btn`),
  stepsBar: document.querySelector(".multisteps-form__progress"),
  stepsForm: document.querySelector(".multisteps-form__form"),
  stepsFormTextareas: document.querySelectorAll(".multisteps-form__textarea"),
  stepFormPanelClass: "multisteps-form__panel",
  stepFormPanels: document.querySelectorAll(".multisteps-form__panel"),
  stepPrevBtnClass: "js-btn-prev",
  stepNextBtnClass: "js-btn-next",
};

//remove class from a set of items
const removeClasses = (elemSet, className) => {
  elemSet.forEach((elem) => {
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
const getActiveStep = (elem) => {
  return Array.from(DOMstrings.stepsBtns).indexOf(elem);
};

//set all steps before clicked (and clicked too) to active
const setActiveStep = (activeStepNum) => {
  //remove active state from all the state
  removeClasses(DOMstrings.stepsBtns, "js-active");

  //set picked items to active
  DOMstrings.stepsBtns.forEach((elem, index) => {
    if (index <= activeStepNum) {
      elem.classList.add("js-active");
    }
  });
};

//get active panel
const getActivePanel = () => {
  let activePanel;

  DOMstrings.stepFormPanels.forEach((elem) => {
    if (elem.classList.contains("js-active")) {
      activePanel = elem;
    }
  });

  return activePanel;
};

//open active panel (and close unactive panels)
const setActivePanel = (activePanelNum) => {
  //remove active class from all the panels
  removeClasses(DOMstrings.stepFormPanels, "js-active");

  //show active panel
  DOMstrings.stepFormPanels.forEach((elem, index) => {
    if (index === activePanelNum) {
      elem.classList.add("js-active");
      setFormHeight(elem);
    }
  });
};

//set form height equal to current panel height
const formHeight = (activePanel) => {
  const activePanelHeight = activePanel.offsetHeight;
  DOMstrings.stepsForm.style.height = `${activePanelHeight}px`;
};

const setFormHeight = () => {
  const activePanel = getActivePanel();
  formHeight(activePanel);
};

//STEPS BAR CLICK FUNCTION
DOMstrings.stepsBar.addEventListener("click", (e) => {
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
DOMstrings.stepsForm.addEventListener("click", (e) => {
  const eventTarget = e.target;

  //check if we clicked on `PREV` or NEXT` buttons
  if (
    !(
      eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`) ||
      eventTarget.classList.contains(`${DOMstrings.stepNextBtnClass}`)
    )
  ) {
    return;
  }

  //find active panel
  const activePanel = findParent(
    eventTarget,
    `${DOMstrings.stepFormPanelClass}`
  );

  let activePanelNum = Array.from(DOMstrings.stepFormPanels).indexOf(
    activePanel
  );

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
window.addEventListener("load", setFormHeight, false);

//SETTING PROPER FORM HEIGHT ONRESIZE
window.addEventListener("resize", setFormHeight, false);

//changing animation via animation select !!!YOU DON'T NEED THIS CODE (if you want to change animation type, just change form panels data-attr)

const setAnimationType = (newType) => {
  DOMstrings.stepFormPanels.forEach((elem) => {
    elem.dataset.animation = newType;
  });
};

// selector onchange - changing animation
const animationSelect = document.querySelector(".pick-animation__select");

animationSelect.addEventListener("change", () => {
  const newAnimationType = animationSelect.value;

  setAnimationType(newAnimationType);
});

$(document).on("click", ".add_data", function () {
  var data = this.value;
  $("#createdatamultiform").val(data);
  // confirm('Are you sure to Submit?')
  $("#add_frm").submit();
});

// <!-- script for adding customer with GSTIN  -->

$(document).on("focusout", "#customer_gstin", function () {
  alert("hiii")
  let customerGstNo = $(this).val();
  $.ajax({
    type: "GET",
    dataType: "json",
    url: `<?= COMPANY_URL ?>ajaxs/ajax-gst-details.php?gstin=${customerGstNo}`,
    success: function (response) {
      let data = response.data;
      let city;
      // console.log(response)
      if (response.status == "success") {
        $("#customer_pan").prop("readonly", true);

        if (data.pradr.addr.city) {
          city = data.pradr.addr.city;
        } else {
          city = data.pradr.addr.loc;
        }
        $("#customer_pan").val(data.gstin.substring(2, 12));

        $("#trade_name").val(data.lgnm);
        $("#con_business").val(data.ctb);
        $(`.selDiv  option:eq(${data.gstin.slice(0, 2) - 1})`).prop(
          "selected",
          true
        );
        $("#city").val(city);
        $("#district").val(data.pradr.addr.dst);
        $("#location").val(data.pradr.addr.loc);
        $("#build_no").val(data.pradr.addr.bno);
        $("#flat_no").val(data.pradr.addr.flno);
        $("#street_name").val(data.pradr.addr.st);
        $("#pincode").val(data.pradr.addr.pncd);
      } else {
        $("#customer_pan").prop("readonly", false);
      }
    },
  });
});

// tcs hide show function
$("#tcsAmtshowhidediv").hide();
$(document).on("change", ".tcscheckbox", function () {
  if (this.checked) {
    $("#tcsAmtshowhidediv").show();
  } else {
    $("#tcsAmtshowhidediv").hide();
  }
});

// On click of the Preview button
$(".previewBtn").click(function () {
  // Get the selected value from the <select> element
  var selectedValue = $("#terms-and-condition").val();

  // Perform AJAX request based on the selected value
  $.ajax({
    url: "ajaxs/so/ajax-tc.php", // Replace with your API endpoint or server URL
    type: "GET",
    data: {
      value: selectedValue, // Send the selected value to the server
      act: "tc",
    },

    success: function (response) {
      // console.log(response);
      let obj = JSON.parse(response);

      $(".tc-modal-title").html(obj["termHead"]);
      $(".tc-modal-body").html(obj["termscond"]);
      // Assuming the response contains the content you want to show in the modal
      // You can adjust this depending on your response structure
      // $('#modalBody').html(response.data); // Populate the modal with the response data
    },
    error: function (error) {
      // Handle any error that occurs during the AJAX request
      console.log("Error:", error);
      $("#modalBody").html("An error occurred while fetching the data.");
    },
  });
});
