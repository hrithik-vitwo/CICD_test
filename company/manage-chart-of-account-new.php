<?php
include("../app/v1/connection-company-admin.php");
// administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
include("../app/v1/functions/company/func-ChartOfAccounts.php");
include("../app/v1/functions/admin/func-company.php");
$company_data = getCompanyDataDetails($company_id);
$queryGetNumRows = queryGetNumRows("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id");
// console($queryGetNumRows);
if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusChartOfAccounts($_POST, "customer_id", "customer_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


if (isset($_POST["createdata"])) {
  $addNewObj = createDataChartOfAccounts($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}

if (isset($_GET["import"]) && $_GET["import"] == "importDefault") {
  if ($queryGetNumRows['numRows'] == 4) {
    $importNewObj = importDefaltChartOfAccounts();
    // console($importNewObj);
    ///swalToast($addNewObj["status"], $addNewObj["message"]);
    redirect($_SERVER['PHP_SELF']);
    exit;
  }
}

if (isset($_POST["editdata"])) {
  $editDataObj = updateDataChartOfAccounts($_POST);
  // console($editDataObj);

  swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>
<style>
  .btn-transparent {
    position: absolute;
    top: 23px;
    left: 9px;
    height: 35px;
    z-index: 9;
    width: 92%;
    background: transparent !important;
  }

  body.sidebar-mini.layout-fixed.sidebar-collapse p.gl-code {
    position: absolute;
    right: 32em;
    top: 5px;
    z-index: 999;
    width: 100px;
    transition-delay: 0.2s;
  }

  body.sidebar-mini.layout-fixed p.gl-code {
    position: absolute;
    right: 22em;
    top: 5px;
    z-index: 999;
    width: 100px;
    transition-delay: 0.2s;
  }

  .gl-filter-list.filter-list {
    display: flex;
    gap: 7px;
    justify-content: flex-end;
    position: relative;
    top: 53px;
    left: -20px;
  }

  .gl-filter-list.filter-list a.active {
    background-color: #003060;
    color: #fff;
  }

  .preview-default-coa-modal .modal-dialog {
    max-width: 85%;
    min-height: 80%;
  }

  .preview-default-coa-modal .modal-content {
    min-height: 80vh;
  }

  .preview-default-coa-modal .modal-content .modal-body {
    height: 400px;
  }
</style>

<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../public/assets/accordion.css">
<link rel="stylesheet" href="../public/assets/tree-table-2/tree-table-2.css">
<?php
if (isset($_GET['create']) && ($_GET['create'] == 'group' || $_GET['create'] == 'account')) {
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <?php if (isset($msg)) { ?>
        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="container-fluid">

        <ol class="breadcrumb">

          <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

          <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Chart of Account</a></li>

          <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add Chart of Account</a></li>

          <li class="back-button">

            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

              <i class="fa fa-reply po-list-icon"></i>

            </a>

          </li>

        </ol>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">

          <div class="col-lg-6 col-md-12 col-sm-12">

            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

              <div class="card-header">

                <h4>Add Group</h4>

              </div>

              <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">
                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_group_frm" name="add_group_frm">
                  <input type="hidden" name="createdata" id="createdata" value="group">
                  <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>">
                  <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12">

                      <div class="row goods-info-form-view customer-info-form-view">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="form-input">

                            <label for="">Select Parent*</label>

                            <!-- <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#accountDropdown1"></button> -->

                            <select id="gp_id" name="p_id" class="form-control form-control-border borderColor" required>
                              <option value="">Select Parent*</option>
                              <?php
                              $listResult = getAllChartOfAccounts_listGroup($company_id);
                              if ($listResult["status"] == "success") {
                                foreach ($listResult["data"] as $listRow) {
                              ?>

                                  <option value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label']; ?>
                                  </option>
                              <?php }
                              } ?>
                            </select>
                            <input type="hidden" name="gl_code" id="ggl_code" value="">
                            <input type="hidden" name="personal_glcode_lvl" id="gpersonal_glcode_lvl" value="">
                            <input type="hidden" name="typeAcc" id="gpersonal_typeAcc" value="">
                            <input type="hidden" class="form-control" id="ggl_code_preview" name="gl_code_preview" readonly required>
                          </div>

                          <span class="error " id="gp_id_error"></span>

                          <!-- <div class="modal fade" id="accountDropdown1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content" style="max-height: 600px;">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel">Select Parent*</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body p-2">

                                  <?php echo createGlTreeAddform(); ?>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  <button type="button" class="btn btn-primary">Save changes</button>
                                </div>
                              </div>
                            </div>
                          </div> -->

                        </div>


                        <div class="col-lg-6 col-md-6 col-sm-6">

                          <div class="form-input">

                            <label for="">Group Name</label>

                            <input type="text" class="form-control" id="gl_label" name="gl_label" required>
                            <span class="error"></span>

                          </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">

                          <div class="form-input">

                            <label for="">Group Description</label>

                            <input type="text" class="form-control" id="remark" name="remark">

                            <span class="error"></span>

                          </div>

                        </div>
                        <div class="btn-section mt-2 mb-2 ml-auto">
                          <button class="btn btn-primary save-close-btn float-right add_data waves-effect waves-light" value="add_post">Create Group</button>
                        </div>



                      </div>

                    </div>

                  </div>
                </form>

              </div>

            </div>

          </div>

          <div class="col-lg-6 col-md-12 col-sm-12">

            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

              <div class="card-header">

                <h4>Add Account</h4>

              </div>

              <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_account_frm" name="add_account_frm">
                  <input type="hidden" name="createdata" id="createdata" value="account">
                  <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>">
                  <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12">

                      <div class="row goods-info-form-view customer-info-form-view">

                        <div class="col-lg-6 col-md-6 col-sm-12">

                          <div class="form-input">

                            <label for="">Select Parent*</label>

                            <!-- <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#accountDropdown"></button> -->

                            <select id="ap_id" name="p_id" class="form-control form-control-border borderColor mt-0" required>
                              <option value="">Select Parent*</option>
                              <?php
                              $coaSql = "SELECT coaTable.* FROM
                              " . ERP_ACC_CHART_OF_ACCOUNTS . " coaTable
                                WHERE
                                    coaTable.company_id = $company_id 
                                    AND coaTable.glStType = 'group'
                                    AND coaTable.id NOT IN (
                                        SELECT
                                          innerTable.`p_id`
                                        FROM
                                        " . ERP_ACC_CHART_OF_ACCOUNTS . " innerTable
                                        WHERE innerTable.glStType = 'group' AND innerTable.`p_id`=coaTable.`id` 
                                        ) 
                                  AND coaTable.`status` != 'deleted'
                                ORDER BY coaTable.gl_code";
                              $listResult = queryGet($coaSql, true);

                              if ($listResult["status"] == "success") {
                                foreach ($listResult["data"] as $listRow) {
                              ?>
                                  <option value="<?php echo $listRow['id']; ?>"><?php echo $listRow['gl_label']; ?>
                                  </option>
                              <?php }
                              } ?>
                            </select>

                            <!-- <div class="modal fade" id="accountDropdown" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content" style="max-height: 600px;">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Select Parent*</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body p-2">

                                    <?php echo createGlTreeAddform(); ?>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">Save changes</button>
                                  </div>
                                </div>
                              </div>
                            </div> -->

                            <input type="hidden" name="gl_code" id="agl_code" value="">
                            <input type="hidden" name="personal_glcode_lvl" id="apersonal_glcode_lvl" value="">
                            <input type="hidden" name="typeAcc" id="apersonal_typeAcc" value="">
                          </div>

                          <span class="error " id="ap_id_error"></span>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">

                          <div class="form-input">

                            <label for="">A/C Code</label>

                            <input type="text" class="form-control" id="agl_code_preview" name="gl_code_preview" readonly required>

                            <span class="error"></span>

                          </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">

                          <div class="form-input">

                            <label for="">Account Name</label>

                            <input type="text" class="form-control" id="gl_label" name="gl_label" required>
                            <span class="error"></span>

                          </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">

                          <div class="form-input">

                            <label for="">Account Description</label>

                            <input type="text" class="form-control" id="remark" name="remark">

                            <span class="error"></span>

                          </div>

                        </div>

                        <div class="btn-section mt-2 mb-2 ml-auto">
                          <button type="submit" class="btn btn-primary save-close-btn float-right add_data waves-effect waves-light" value="add_post">Create Account</button>
                        </div>

                      </div>

                    </div>

                  </div>

              </div>
              </form>

            </div>

          </div>

        </div>

      </div>
  </div>
  </section>
  <!-- /.content -->
  </div>

<?php
} else {

  $length_bkup = explode('-', $company_data['data']['gl_length_bkup']);
  $format = [];
  foreach ($length_bkup as $key => $bkdta) {
    $format[] = str_pad(0, $bkdta, 0, STR_PAD_LEFT);
  }
  $gl_format = implode('-', $format);
  ?>

  <style>
    .content-wrapper {
      padding: 30px 5px 10px 5px !important;
    }



    .form {
      display: flex;
    }

    .form>div {
      display: inline-block;
      vertical-align: top;
    }

    #treeviewDriveC,
    #treeviewDriveD {
      margin-top: 10px;
    }

    .drive-header {
      min-height: auto;
      padding: 0;
      cursor: default;
    }

    .drive-panel {
      padding: 20px 30px;
      font-size: 115%;
      font-weight: bold;
      border-right: 1px solid rgba(165, 165, 165, 0.4);
      height: 100%;
    }

    .drive-panel:last-of-type {
      border-right: none;
    }
  </style>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->


    <div class="gl-filter-list filter-list">
      <div class="btns-group d-flex justify-content-end">
        <?php
        if ($queryGetNumRows['numRows'] == 4) { ?>
          <a class="btn btn-primary text-white" data-toggle="modal" data-target="#importAcc" id="import-acc">Import Default <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
          <div class="modal fade zoom-in preview-default-coa-modal" id="importAcc" tabindex="-1" aria-labelledby="importAccLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Preview Default COA</h5>
                </div>
                <div class="modal-body">
                  <?php echo previewGlTreeDefult(); ?>
                </div>

                <div class="modal-footer">
                  <button onclick="window.location.href='<?php echo basename($_SERVER['PHP_SELF']) ?>?import=importDefault'" type="button" class="btn btn-success">Import Default</button>
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
        <?php  } else { ?>
          <a href="manage-chart-of-account.php" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active"></i>Tree View</a>
          <a href="sub-gl-view.php" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2 "></i>Sub GL View</a>
          <a href="gl-view.php" class="btn waves-effect waves-light"><i class="fa fa-list mr-2"></i>GL View</a>
          <a class="btn btn-primary text-white" href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create=account" type="button" id="add-acc">Add Account <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
        <?php } ?>
      </div>
    </div>

    <section class="tree-table-2">
      <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
        <li class="pt-2 px-3 my-2 d-flex justify-content-between align-items-center" style="width:100%">
          <h3 class="card-title">Chart of Account (<?= $gl_format; ?>)</h3>

        </li>
      </ul>

      <?php //echo createGlTree(); 
      ?>

      <div class="demo-container">
        <div class="form">
          <div class="drive-panel">
            <div class="drive-header dx-treeview-item">
              <div class="dx-treeview-item-content"><i class="dx-icon dx-icon-activefolder"></i><span>Drive C:</span></div>
            </div>
            <div id="treeviewDriveC"></div>
          </div>
          <div class="drive-panel">
            <div class="drive-header dx-treeview-item">
              <div class="dx-treeview-item-content"><i class="dx-icon dx-icon-activefolder"></i><span>Drive D:</span></div>
            </div>
            <div id="treeviewDriveD"></div>
          </div>
        </div>
      </div>

    </section>

    <div class="modal fade right" id="GLedit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog w-50">
        <div class="modal-content">
          <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_group_frm" name="add_group_frm">
            <div class="modal-header">
              <h5 class="modal-title text-white" id="staticBackdropLabel">Edit Form</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row ReturnsDataDiv" style="row-gap: 7px;">
                <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Loding...
              </div>
              <div class="row ReturnsDataDivoriginal" style="row-gap: 7px;">

              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>



  </div>
<?php
}
include("common/footer.php");
?>

<link rel="stylesheet" type="text/css" href="https://cdn3.devexpress.com/jslib/22.2.6/css/dx.dark.css" />
<script src="../../public/assets-2/tree/js/dx.all.js"></script>
<script>
  // Data List

  const itemsDriveD = [];
  const itemsDriveC = [{
    id: '1',
    name: 'Documents',
    icon: 'activefolder',
    isDirectory: true,
    expanded: true,
  }, {
    id: '2',
    parentId: '1',
    name: 'Projects',
    icon: 'activefolder',
    isDirectory: true,
    expanded: true,
  }, {
    id: '3',
    parentId: '2',
    name: 'About.rtf',
    icon: 'file',
    isDirectory: false,
    expanded: true,
  }, {
    id: '4',
    parentId: '2',
    name: 'Passwords.rtf',
    icon: 'file',
    isDirectory: false,
    expanded: true,
  }, {
    id: '5',
    parentId: '2',
    name: 'About.xml',
    icon: 'file',
    isDirectory: false,
    expanded: true,
  }, {
    id: '6',
    parentId: '2',
    name: 'Managers.rtf',
    icon: 'file',
    isDirectory: false,
    expanded: true,
  }, {
    id: '7',
    parentId: '2',
    name: 'ToDo.txt',
    icon: 'file',
    isDirectory: false,
    expanded: true,
  }, {
    id: '8',
    name: 'Images',
    icon: 'activefolder',
    isDirectory: true,
    expanded: true,
  }, {
    id: '9',
    parentId: '8',
    name: 'logo.png',
    icon: 'file',
    isDirectory: false,
    expanded: true,
  }, {
    id: '10',
    parentId: '8',
    name: 'banner.gif',
    icon: 'file',
    isDirectory: false,
    expanded: true,
  }, {
    id: '11',
    name: 'System',
    icon: 'activefolder',
    isDirectory: true,
    expanded: true,
  }, {
    id: '12',
    parentId: '11',
    name: 'Employees.txt',
    icon: 'file',
    isDirectory: false,
    expanded: true,
  }, {
    id: '13',
    parentId: '11',
    name: 'PasswordList.txt',
    icon: 'file',
    isDirectory: false,
    expanded: true,
  }, {
    id: '14',
    name: 'Description.rtf',
    icon: 'file',
    isDirectory: false,
    expanded: true,
  }, {
    id: '15',
    icon: 'file',
    name: 'Description.txt',
    isDirectory: false,
    expanded: true,
  }];






  /// JS


  $(() => {
    createTreeView('#treeviewDriveC', itemsDriveC);
    createTreeView('#treeviewDriveD', itemsDriveD);

    createSortable('#treeviewDriveC', 'driveC');
    createSortable('#treeviewDriveD', 'driveD');
  });

  function createTreeView(selector, items) {
    $(selector).dxTreeView({
      items,
      expandNodesRecursive: false,
      dataStructure: 'plain',
      width: 250,
      height: 380,
      displayExpr: 'name',
    });
  }

  function createSortable(selector, driveName) {
    $(selector).dxSortable({
      filter: '.dx-treeview-item',
      group: 'shared',
      data: driveName,
      allowDropInsideItem: true,
      allowReordering: true,
      onDragChange(e) {
        if (e.fromComponent === e.toComponent) {
          const $nodes = e.element.find('.dx-treeview-node');
          const isDragIntoChild = $nodes.eq(e.fromIndex).find($nodes.eq(e.toIndex)).length > 0;
          if (isDragIntoChild) {
            e.cancel = true;
          }
        }
      },
      onDragEnd(e) {
        if (e.fromComponent === e.toComponent && e.fromIndex === e.toIndex) {
          return;
        }

        const fromTreeView = getTreeView(e.fromData);
        const toTreeView = getTreeView(e.toData);

        const fromNode = findNode(fromTreeView, e.fromIndex);
        const toNode = findNode(toTreeView, calculateToIndex(e));

        if (e.dropInsideItem && toNode !== null && !toNode.itemData.isDirectory) {
          return;
        }

        const fromTopVisibleNode = getTopVisibleNode(fromTreeView);
        const toTopVisibleNode = getTopVisibleNode(toTreeView);

        const fromItems = fromTreeView.option('items');
        const toItems = toTreeView.option('items');
        moveNode(fromNode, toNode, fromItems, toItems, e.dropInsideItem);

        fromTreeView.option('items', fromItems);
        toTreeView.option('items', toItems);
        fromTreeView.scrollToItem(fromTopVisibleNode);
        toTreeView.scrollToItem(toTopVisibleNode);
      },
    });
  }

  function getTreeView(driveName) {
    return driveName === 'driveC' ?
      $('#treeviewDriveC').dxTreeView('instance') :
      $('#treeviewDriveD').dxTreeView('instance');
  }

  function calculateToIndex(e) {
    if (e.fromComponent !== e.toComponent || e.dropInsideItem) {
      return e.toIndex;
    }

    return e.fromIndex >= e.toIndex ?
      e.toIndex :
      e.toIndex + 1;
  }

  function findNode(treeView, index) {
    const nodeElement = treeView.element().find('.dx-treeview-node')[index];
    if (nodeElement) {
      return findNodeById(treeView.getNodes(), nodeElement.getAttribute('data-item-id'));
    }
    return null;
  }

  function findNodeById(nodes, id) {
    for (let i = 0; i < nodes.length; i += 1) {
      if (nodes[i].itemData.id === id) {
        return nodes[i];
      }
      if (nodes[i].children) {
        const node = findNodeById(nodes[i].children, id);
        if (node != null) {
          return node;
        }
      }
    }
    return null;
  }

  function moveNode(fromNode, toNode, fromItems, toItems, isDropInsideItem) {
    const fromIndex = findIndex(fromItems, fromNode.itemData.id);
    fromItems.splice(fromIndex, 1);

    const toIndex = toNode === null || isDropInsideItem ?
      toItems.length :
      findIndex(toItems, toNode.itemData.id);
    toItems.splice(toIndex, 0, fromNode.itemData);

    moveChildren(fromNode, fromItems, toItems);
    if (isDropInsideItem) {
      fromNode.itemData.parentId = toNode.itemData.id;
    } else {
      fromNode.itemData.parentId = toNode != null ?
        toNode.itemData.parentId :
        undefined;
    }
  }

  function moveChildren(node, fromItems, toItems) {
    if (!node.itemData.isDirectory) {
      return;
    }

    node.children.forEach((child) => {
      if (child.itemData.isDirectory) {
        moveChildren(child, fromItems, toItems);
      }

      const fromIndex = findIndex(fromItems, child.itemData.id);
      fromItems.splice(fromIndex, 1);
      toItems.splice(toItems.length, 0, child.itemData);
    });
  }

  function findIndex(array, id) {
    const idsArray = array.map((elem) => elem.id);
    return idsArray.indexOf(id);
  }

  function getTopVisibleNode(component) {
    const treeViewElement = component.element().get(0);
    const treeViewTopPosition = treeViewElement.getBoundingClientRect().top;
    const nodes = treeViewElement.querySelectorAll('.dx-treeview-node');
    for (let i = 0; i < nodes.length; i += 1) {
      const nodeTopPosition = nodes[i].getBoundingClientRect().top;
      if (nodeTopPosition >= treeViewTopPosition) {
        return nodes[i];
      }
    }

    return null;
  }
  
</script>