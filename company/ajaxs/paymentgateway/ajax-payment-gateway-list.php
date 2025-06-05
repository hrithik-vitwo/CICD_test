<?php
include_once("../../../app/v1/connection-company-admin.php");

if ($_POST['act'] == 'fetchPayment') {
  // Fetching data from the database
  $sql = "SELECT * FROM `erp_payment_gateway`";
  $data = queryGet($sql, true);
  if (!is_array($data)) {
    echo "Error fetching data.";
    exit;
  }
  foreach ($data['data'] as $row) {
?>
    <tr>
      <td><?= $row['payment_gateway_id'] ?></td>
      <td><?= $row['bank_id'] ?></td>
      <td><?= $row['getway_type'] ?></td>
      <td><?= $row['access_token'] ?></td>
      <td><?= $row['access_key'] ?></td>
      <td><?= $row['url_type'] ?></td>
      <td><?= $row['environment'] ?></td>
      <td><?= $row['status'] ?></td>
      <td><?= $row['created_by'] ?></td>
      <td><?= $row['updated_by'] ?></td>
      <td><?= $row['created_at'] ?></td>
      <td><?= $row['updated_at'] ?></td>
      <td><button class='btn btn-sm' id="editPaymentGatewayInfo_<?= $row['payment_gateway_id'] ?>" data-toggle='modal' data-target="#editPaymentGateway_<?= $row['payment_gateway_id'] ?>"><i class='fa fa-edit po-list-icon'></i></button>
        <div class="modal fade edit-modal edit-payment-gateway" id="editPaymentGateway_<?= $row['payment_gateway_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <form action="" method="POST" id="edit_frm" name="edit_frm">
              <input type="hidden" name="editdata" id="editdata" value="">
              <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

              <div class="modal-content card">
                <div class="modal-header card-header pt-2 pb-2 px-3">
                  <h4 class="text-xs text-white mb-0">Edit Payment Gateway</h4>
                </div>
                <div class="modal-body">
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-6">
                      <div class="form-input">
                        <label for="">Bank ID</label>
                        <input type="text" value="<?= $row['bank_id'] ?>" class="form-control" name="bankID" id="bankID_<?= $row['payment_gateway_id'] ?>">
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-6">
                      <div class="form-input">
                        <label for="">Gateway Type</label>
                        <input type="text" value="<?= $row['getway_type'] ?>" class="form-control" name="gatewaytype" id="gatewaytype_<?= $row['payment_gateway_id'] ?>">
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-6">
                      <div class="form-input">
                        <label for="">Access Token</label>
                        <input type="text" value="<?= $row['access_token'] ?>" class="form-control" name="accessToken" id="accessToken_<?= $row['payment_gateway_id'] ?>">
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-6">
                      <div class="form-input">
                        <label for="">Access Key</label>
                        <input type="text" value="<?= $row['access_key'] ?>" class="form-control" name="accessKey" id="accessKey_<?= $row['payment_gateway_id'] ?>">
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-6">
                      <div class="form-input">
                        <label for="">Url Type</label>
                        <select name="urlType" id="urlTypeid_<?= $row['payment_gateway_id'] ?>" class="form-control">
                          <option value="urlDemo" <?= $row['url_type'] == 'urlDemo' ? 'selected' : '' ?>>Demo Url</option>
                          <option value="urlLive" <?= $row['url_type'] == 'urlLive' ? 'selected' : '' ?>>Live Url</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-6">
                      <div class="form-input">
                        <label for="">Environment Type</label>
                        <select name="environmentType" id="environmentTypeid_<?= $row['payment_gateway_id'] ?>" class="form-control">
                          <option value="entDemo" <?= $row['environment'] == 'entDemo' ? 'selected' : '' ?>>Demo</option>
                          <option value="entzlive" <?= $row['environment'] == 'entzlive' ? 'selected' : '' ?>>Live</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary updatePaymentBtn" data-id="<?= $row['payment_gateway_id'] ?>" id="updatePaymentBtn" value="Update">Submit</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </td>
    </tr>
<?php
  }
}



