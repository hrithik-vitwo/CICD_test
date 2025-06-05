<?php
include("../app/v1/connection-branch-admin.php");
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");

// administratorAuth();
require_once("common/pagination.php");

?>
<style>
    #mapCanvas {
        width: 100%;
        height: 650px;
    }

    .map-view-card .list-map-tab {
        position: relative;
        top: 0;
        left: 0;
        width: 100%;
        justify-content: center;
        margin: 10px 0;
    }

    .list-map-tab a.active {
        background: #003060;
        color: #fff;
    }
</style>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="card card-tabs map-view-card" style="border-radius: 20px;">
                        <li class="pt-2 px-3 my-2 d-flex justify-content-between align-items-center" style="width:100%">
                            <div class="list-map-tab filter-list">
                                <a href="manage-locations.php" class="btn "><i class="fa fa-clock mr-2 "></i>Location List</a>
                                <a href="locations.php" class="btn active"><i class="fa fa-lock-open mr-2 active"></i>Location Map View</a>
                            </div>
                        </li>

                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">

                                <?php

                                $result = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `branch_id`=$branch_id AND `othersLocation_lat` != 0 AND `othersLocation_lng` != 0", true);
                                //console($result);
                                // Fetch the info-window data from the database 
                                $result2 = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `branch_id`=$branch_id  AND `othersLocation_lat` != 0 AND `othersLocation_lng` != 0", true);

                                $icon = "http://devalpha.vitwo.ai/public/pin-map.png";

                                ?>
                                <div id="mapCanvas"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
<!-- /.row -->
</div>
</section>
<!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<?php
include("common/footer.php");
?>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA3Fvc0bRRWw4hTtD2Sln45D4D7CV1de2I&callback=initMap"></script>
<script>
    function initMap() {
        var map;
        var bounds = new google.maps.LatLngBounds();
        var mapOptions = {
            mapTypeId: 'roadmap'
        };

        // Display a map on the web page
        map = new google.maps.Map(document.getElementById("mapCanvas"), mapOptions);
        map.setTilt(100);

        // Multiple markers location, latitude, and longitude
        var markers = [
            <?php if ($result['numRows'] > 0) {
                foreach ($result['data'] as $row) {
                    echo '["' . $row['othersLocation_name'] . '", ' . $row['othersLocation_lat'] . ', ' . $row['othersLocation_lng'] . ',"' . $icon . '"],';
                }
            }
            ?>
        ];
        // Info window content
        var infoWindowContent = [
            <?php if ($result2['numRows'] > 0) {
                foreach ($result2['data'] as $row) { ?>['<div class="info_content">' +
                        '<h3><?php echo $row['othersLocation_name']; ?></h3>' +
                        '<p><?php echo $row['othersLocation_code']; ?></p>' + '</div>'],
            <?php }
            }
            ?>
        ];

        // Add multiple markers to map
        var infoWindow = new google.maps.InfoWindow(),
            marker, i;

        // Place each marker on the map  
        for (i = 0; i < markers.length; i++) {
            var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
            bounds.extend(position);
            marker = new google.maps.Marker({
                position: position,
                map: map,
                icon: markers[i][3],
                title: markers[i][0]
            });

            // Add info window to marker    
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infoWindow.setContent(infoWindowContent[i][0]);
                    infoWindow.open(map, marker);
                }
            })(marker, i));

            // Center the map to fit all markers on the screen
            map.fitBounds(bounds);
        }

        // Set zoom level
        // var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
        //     this.setZoom(14);
        //     google.maps.event.removeListener(boundsListener);
        // });
    }

    // Load initialize function
    google.maps.event.addDomListener(window, 'load', initMap);
</script>