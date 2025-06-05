<?php
require_once("../../app/v1/connection-branch-admin.php");
$menuType = $_POST['menuType']; 
$menufor = $_POST['menufor']; 

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
        <p class="text-xs font-bold"><input type="checkbox" class="menuCBX" value="Write" <?php echo $menuType == 'Approver' ? 'disabled':'' ?>> Write</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col">
        <p class="text-xs font-bold"><input type="checkbox" class="menuCBX" value="Update" <?php echo $menuType == 'Approver' ? 'disabled':'' ?>> Update</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col">
        <p class="text-xs font-bold"><input type="checkbox" class="menuCBX" value="Delete" <?php echo $menuType == 'Approver' ? 'disabled':'' ?>> Delete</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col">
        <p class="text-xs font-bold"><input type="checkbox" class="menuCBX" value="Approve"> Approve</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col">
        <p class="text-xs font-bold"> <input type="checkbox" class="menuCBX" value="StatusChange" <?php echo $menuType == 'Approver' ? 'disabled':'' ?>>Status Change</p>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 col">
        <p class="text-xs font-bold"> <input type="checkbox" class="menuCBX" value="Others" <?php echo $menuType == 'Approver' ? 'disabled':'' ?>>Others</p>
    </div>
</div>
<?php foreach (getAdministratorMenuListNew('-0', $menufor)['data'] as $GrandMenuDetails) { ?>


    <?php foreach (getAdministratorGrandMenuList('0', $GrandMenuDetails['fldMenuKey'])['data'] as $menuDetails) { ?>
        <div class="row bgColorModule">
            <div class="col-lg-12 col-md-12 col-sm-12 col">
                <p class="text-xs font-bold">
                <input type="checkbox" name="menuGrandParent<?= $GrandMenuDetails['fldMenuKey'] ?>[]" id="menuGrandParent<?= $GrandMenuDetails['fldMenuKey'] ?>" class="form-control-default Module GrandParent menuGrandParent menuGrandParent<?= $GrandMenuDetails['fldMenuKey'] ?>" value="<?= $menuDetails['fldMenuKey'] ?>">
                    <?= $GrandMenuDetails['fldMenuLabel'] ?> >> <?= $menuDetails['fldMenuLabel'] ?></p>
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
                    
                    $searchValues = array('List','Add', 'Edit', 'Delete', 'Approve','StatusChange');
                    $foundValues = array_intersect($sql_access, $searchValues);
                    $othersAccess = array_diff($sql_access, $foundValues);
                   ?>
                    
                    <div class="col-lg-1 col-md-1 col-sm-1 col">
                        <input type="checkbox" name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default Read GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="List" <?= in_array("List", $sql_access) ? '' : 'disabled'; ?>>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col">

                        <input type="checkbox" name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default Write GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="Add" <?php echo $menuType == 'Approver' ? 'disabled':'' ?> <?= in_array("Add", $sql_access) ? '' : 'disabled'; ?>>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col">

                        <input type="checkbox" name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default Update GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="Edit" <?php echo $menuType == 'Approver' ? 'disabled':'' ?> <?= in_array("Edit", $sql_access) ? '' : 'disabled'; ?>>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col">

                        <input type="checkbox" name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default Delete GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="Delete" <?php echo $menuType == 'Approver' ? 'disabled':'' ?> <?= in_array("Delete", $sql_access) ? '' : 'disabled'; ?>>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col">

                        <input type="checkbox" name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default Approve GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="Approve" <?= in_array("Approve", $sql_access) ? '' : 'disabled'; ?>>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col">

                        <input type="checkbox" name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default StatusChange GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="StatusChange" <?php echo $menuType == 'Approver' ? 'disabled':'' ?> <?= in_array("StatusChange", $sql_access) ? '' : 'disabled'; ?>>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col">

                    <?php
                    foreach ($othersAccess as $row_access) { ?>
                    
                            <span class="form-group mr-2">
                                <input type="checkbox" name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default Others GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="<?= $row_access ?>" <?php echo $menuType == 'Approver' ? 'disabled':'' ?> <?= in_array($row_access, $othersAccess) ? '' : 'disabled'; ?> >
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