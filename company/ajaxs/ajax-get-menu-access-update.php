<?php
require_once("../../app/v1/connection-branch-admin.php");
$fldRoleKey = $_POST['fldRoleKey'];
$menuType = $_POST['menuType'];
$menufor = $_POST['menufor'];


$dbObj = new Database();

$roleSql = "SELECT * FROM `tbl_branch_admin_roles_a2` WHERE fldRoleKey=$fldRoleKey";
$role = $dbObj->queryGet($roleSql);
$roleData = $role['data'];

$subChild = unserialize($roleData['subChildMin']);
// console($roleData['subChildMin']);
// console($subChild);
// exit();

?>

<div class="row bg-dark">
    <div class="col-lg-2 col-md-2 col-sm-2 col">
        <p class="text-xs font-bold"><input type="checkbox" class="menuCBX" value="Module" disabled> Module</p>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col">
        <p class="text-xs font-bold"><input type="checkbox" class="menuCBX" value="PageName" disabled> Page Name</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col">
        <p class="text-xs font-bold"><input type="checkbox" class="menuCBX" value="Read"> Read</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col">
        <p class="text-xs font-bold"><input type="checkbox" class="menuCBX" value="Write" <?php echo $menuType == 'Approver' ? 'disabled' : '' ?>> Write</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col">
        <p class="text-xs font-bold"><input type="checkbox" class="menuCBX" value="Update" <?php echo $menuType == 'Approver' ? 'disabled' : '' ?>> Update</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col">
        <p class="text-xs font-bold"><input type="checkbox" class="menuCBX" value="Delete" <?php echo $menuType == 'Approver' ? 'disabled' : '' ?>> Delete</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col">
        <p class="text-xs font-bold"><input type="checkbox" class="menuCBX" value="Approve"> Approve</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col">
        <p class="text-xs font-bold"> <input type="checkbox" class="menuCBX" value="StatusChange" <?php echo $menuType == 'Approver' ? 'disabled' : '' ?>>Status Change</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col">
        <p class="text-xs font-bold"> <input type="checkbox" class="menuCBX" value="Others" <?php echo $menuType == 'Approver' ? 'disabled' : '' ?>>Others</p>
    </div>
</div>
<?php foreach (getAdministratorMenuListNew('-0', $menufor)['data'] as $GrandMenuDetails) { ?>


    <?php foreach (getAdministratorGrandMenuList('0', $GrandMenuDetails['fldMenuKey'])['data'] as $menuDetails) { ?>
        <div class="row bgColorModule">
            <div class="col-lg-12 col-md-12 col-sm-12 col">
                <p class="text-xs font-bold">
                    <input type="checkbox" name="menuGrandParent<?= $GrandMenuDetails['fldMenuKey'] ?>[]" id="menuGrandParent<?= $GrandMenuDetails['fldMenuKey'] ?>" class="form-control-default Module GrandParent menuGrandParent menuGrandParent<?= $GrandMenuDetails['fldMenuKey'] ?>" value="<?= $menuDetails['fldMenuKey'] ?>">
                    <?= $GrandMenuDetails['fldMenuLabel'] ?> >> <?= $menuDetails['fldMenuLabel'] ?>
                </p>
            </div>
        </div>

        <?php foreach (getAdministratorMenuList($menuDetails['fldMenuKey'])['data'] as $subMenuDetails) { ?>
            <?php if (!empty($subMenuDetails["menu_accesses"])) { ?>
                <div class="row">
                    <div class="col-lg-2 col-md-2 col-sm-2 col">
                        <p class="text-xs font-bold"> </p>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col">
                        <p class="text-xs font-bold">
                            <input type="checkbox" name="menuGrandParentSub<?= $menuDetails['fldMenuKey'] ?>[]" id="menuGrandParentSub<?= $menuDetails['fldMenuKey'] ?>" class="form-control-default PageName GrandParentSub<?= $GrandMenuDetails['fldMenuKey'] ?>  ParentSub<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSub menuGrandParentSub<?= $menuDetails['fldMenuKey'] ?>" value="<?= $subMenuDetails['fldMenuKey'] ?>">
                            <?= $subMenuDetails['fldMenuLabel'] ?>
                        </p>
                    </div>
                    <?php
                    $sql_access = explode(',', $subMenuDetails["menu_accesses"]);

                    $searchValues = array('List', 'Add', 'Edit', 'Delete', 'Approve', 'StatusChange');
                    $foundValues = array_intersect($sql_access, $searchValues);
                    $othersAccess = array_diff($sql_access, $foundValues);
                    $checked = '';
                    $submenuId = $subMenuDetails['fldMenuKey'];
                    // console($submnuArry);
                    // console($submnuArry[$subMenuDetails['fldMenuKey']]);
                    
                    // if (in_array("List", $subChild[$submenuId])) {
                    //     echo "Found 'List' in the array.";
                    // } else {
                    //     echo "'List' is not in the array.";
                    // }

                    // exit();

                    ?>

                    <div class="col-lg-1 col-md-1 col-sm-1 col">
                        <input type="checkbox" <?php if (in_array('List', $subChild[$submenuId])) { echo "checked";  } ?> name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default Read GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="List" <?= in_array("List", $sql_access) ? '' : 'disabled'; ?>>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col">

                        <input type="checkbox" <?php if (in_array('Add', $subChild[$submenuId])) { echo "checked";  } ?>  name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default Write GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="Add" <?php echo $menuType == 'Approver' ? 'disabled' : '' ?> <?= in_array("Add", $sql_access) ? '' : 'disabled'; ?>>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col">

                        <input type="checkbox" <?php if (in_array('Edit', $subChild[$submenuId])) { echo "checked";  } ?>  name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default Update GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="Edit" <?php echo $menuType == 'Approver' ? 'disabled' : '' ?> <?= in_array("Edit", $sql_access) ? '' : 'disabled'; ?>>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col">

                        <input type="checkbox" <?php if (in_array('Delete', $subChild[$submenuId])) { echo "checked";  } ?>  name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default Delete GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="Delete" <?php echo $menuType == 'Approver' ? 'disabled' : '' ?> <?= in_array("Delete", $sql_access) ? '' : 'disabled'; ?>>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col">

                        <input type="checkbox" <?php if (in_array('Approve', $subChild[$submenuId])) { echo "checked";  } ?> name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default Approve GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="Approve" <?= in_array("Approve", $sql_access) ? '' : 'disabled'; ?>>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col">

                        <input type="checkbox" <?php if (in_array('StatusChange', $subChild[$submenuId])) { echo "checked";  } ?>  name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default StatusChange GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="StatusChange" <?php echo $menuType == 'Approver' ? 'disabled' : '' ?> <?= in_array("StatusChange", $sql_access) ? '' : 'disabled'; ?>>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col">

                        <?php
                        foreach ($othersAccess as $row_access) { ?>

                            <span class="form-group mr-2">
                                <input type="checkbox" <?php if (in_array($row_access, $subChild[$submenuId])) { echo "checked";  } ?> name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default Others GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="<?= $row_access ?>" <?php echo $menuType == 'Approver' ? 'disabled' : '' ?> <?= in_array($row_access, $othersAccess) ? '' : 'disabled'; ?>>
                                <label class="text-muted"><?= $row_access ?></label><br>
                            </span>

                        <?php } ?>


                    </div>
                </div>
<?php
            }
        }
    }
}
?>