const months = {
    "0" : "January",
    "1" : "February",
    "2" : "March",
    "3" : "April",
    "4" : "May",
    "5" : "June",
    "6" : "July",
    "7" : "August",
    "8" : "September",
    "9" : "October",
    "10" : "November",
    "11" : "December",
}

function getDateString (selectedDate) {

    let month = `${selectedDate.getMonth() + 1}`
    
    if (month.length == 1){
        month = '0' + month
    }

    let date = `${selectedDate.getDate()}`

    if (date.length == 1){
        date = '0' + date
    }

    let year = `${selectedDate.getFullYear()}`

    let dateString = date + '/' + month + '/' + year

    return dateString
};

function changeDateString (dateString) {

    let dateList = dateString.split("/");

    let date = dateList[0];
    let month = dateList[1];
    let year = dateList[2];

    let updatedDateString = year + '-' + month + '-' + date

    return updatedDateString
};

$(document).ready(function () {

    $(document).on("change", "#start_date", function(e){

        let startDate = new Date($("#start_date").val());
        let dateObject = new Date($("#start_date").val());
        let endDate = new Date(startDate.setFullYear(startDate.getFullYear() + 1));
        
        endDate.setDate(endDate.getDate() - 1);

        $("#end_date_year").val(months[endDate.getMonth()] + ' ' + endDate.getFullYear());

        let monthCounter = 1
        let firstDay = new Date(dateObject.getFullYear(), dateObject.getMonth(), 1);

        for (let i = 0; i < 12; i++) {
            let lastDay = new Date(firstDay.getFullYear(), firstDay.getMonth() + 1, 0);
            $(`[name="month[${monthCounter}][name]"]`).val(months[firstDay.getMonth()] + ' - ' + firstDay.getFullYear());
            $(`[name="month[${monthCounter}][start_date]"]`).val(getDateString(firstDay));
            $(`[name="month[${monthCounter}][end_date]"]`).val(getDateString(lastDay));
            monthCounter++;
            firstDay.setDate(lastDay.getDate() + 1);
        };

        let specialStartDate = changeDateString($('[name="month[12][start_date]"]').val());
        let specialEndDate = changeDateString($('[name="month[12][end_date]"]').val());

        $('[name="month[13][start_date]"]').val(specialStartDate);
        $('[name="month[13][end_date]"]').val(specialEndDate);

        $('[name="month[14][start_date]"]').val(specialStartDate);
        $('[name="month[14][end_date]"]').val(specialEndDate);

        $('[name="month[15][start_date]"]').val(specialStartDate);
        $('[name="month[15][end_date]"]').val(specialEndDate);
    });

    let monthNO = 15;

    $(document).on("click", "#minusVariant", function () {
        monthNO--;
    });

    $(document).on("click", "#addVariant", function () {
        let monthPrefix = "";
        monthNO++;

        if (monthNO.toString()[1] == 1) {
            monthPrefix = "st";
        } else if (monthNO.toString()[1] == 2) {
            monthPrefix = "nd";
        } else if (monthNO.toString()[1] == 3) {
            monthPrefix = "rd";
        } else {
            monthPrefix = "th";
        };

        let addressRandNo = Math.ceil(Math.random() * 100000);
        $(`.varient-card-body`).append(`<div class="row goods-info-form-view customer-info-form-view">

        <div class="col-lg-4 col-md-4 col-sm-4">

            <div class="form-input">

                <label for="">Special Variant Name (${monthNO}${monthPrefix} Variant)</label>

                <input type="text" id="start2" name="month[${addressRandNo}][name]" class="form-control" id="start_date" value="">

                <span class="error start_date"></span>

            </div>

        </div>
        <div class="col-lg-4 col-md-4 col-sm-4">

            <div class="form-input"> 

                <label for="">Start</label>

                <input type="date" id="start2" name="month[${addressRandNo}][start_date]" class="form-control" id="start_date" >

                <span class="error start_date"></span>

            </div>

        </div>

        <div class="col-lg-3 col-md-3 col-sm-3">

            <div class="form-input">

                <label for="">End</label>

                <input type="date" name="month[${addressRandNo}][end_date]" class="form-control" id="end_date" >

            </div>

        </div>


        <div class="col-lg-1 col-md-1 col-sm-1 btn-minus">

                    <a id="minusVariant" class="btn btn-danger float-right">
                    
                        <i class="fa fa-minus"></i>

                    </a>

        </div>

        </div>`);
    });
});