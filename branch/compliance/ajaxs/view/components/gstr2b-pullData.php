<style>
    .content-area {
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 7px;
    }
</style>

<div class="container">
    <h4 class="text-sm font-bold my-4">Pull Data From Portal</h4>
    <div class="col-12 pl-auto">
        <div class="content-area" id="content-area">
            <p class="text-sm">Please click below button to pull the data!</p>
            <a class="btn btn-primary waves-effect waves-light" id="pullDataButton">Confirm</a>
        </div>
    </div>
</div>

<script>
    $(document).on("click", "#pullDataButton", function() {

        let apiData= "<?= base64_encode(json_encode($queryParams)) ?>";
        $.ajax({
            type: "POST",
            url: "ajaxs/api/ajax-gstr2b-pull-data.php?action=<?= base64_encode(json_encode($queryParams)) ?>",
            beforeSend: function() {
                console.log("pulling Data");
            },
            success: function(response) {
                console.log(response);
                Swal.fire({
                            icon: response.status,
                            title: response.message,
                            timer: 2000,
                            showConfirmButton: false,
                        })
                        .then(() => {
                            window.location.href = `gstr2b-reconcile.php?action=${apiData}`;
                        });

            }
        })

    })
</script>