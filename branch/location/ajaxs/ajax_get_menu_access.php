<?php
require_once("../../../app/v1/connection-branch-admin.php");
$menufor=$_POST['menufor'];
 foreach (getAdministratorMenuListNew('-0',$menufor)['data'] as $GrandMenuDetails) { ?>
  <div class="col-md-12">
      <div class="card card-danger">
          <div class="card-header bg-info">
              <h3 class="card-title">
                  <input type="checkbox" name="menuGrand<?= $GrandMenuDetails['fldMenuKey'] ?>[]" id="menuGrand<?= $GrandMenuDetails['fldMenuKey'] ?>" class="form-control-default menuGrand" value="<?= $GrandMenuDetails['fldMenuKey'] ?>">
                  <?= $GrandMenuDetails['fldMenuLabel'] ?>
              </h3>
          </div>
          <div class="card-body">
              <table class="table table-bordered">
                  <tr>
                      <th width="15%">Menu</th>
                      <th>Sub Menus & Access</th>
                  </tr>
                  <?php foreach (getAdministratorGrandMenuList('0', $GrandMenuDetails['fldMenuKey'])['data'] as $menuDetails) { ?>
                      <tr>
                          <td><label class="text-muted">
                                  <b>
                                      <input type="checkbox" name="menuGrandParent<?= $GrandMenuDetails['fldMenuKey'] ?>[]" id="menuGrandParent<?= $GrandMenuDetails['fldMenuKey'] ?>" class="form-control-default GrandParent menuGrandParent menuGrandParent<?= $GrandMenuDetails['fldMenuKey'] ?>" value="<?= $menuDetails['fldMenuKey'] ?>">
                                      <?= $menuDetails['fldMenuLabel'] ?>
                                  </b>
                              </label></td>
                          <td>
                              <div class="row p-0 m-0">
                                  <?php foreach (getAdministratorMenuList($menuDetails['fldMenuKey'])['data'] as $subMenuDetails) { ?>
                                      <?php if (!empty($subMenuDetails["menu_accesses"])) {?>
                                      <div class="col-md-3">
                                          <div class="card card-danger">
                                              <div class="card-header bg-info">
                                                  <h3 class="card-title">
                                                      <input type="checkbox" name="menuGrandParentSub<?= $menuDetails['fldMenuKey'] ?>[]" id="menuGrandParentSub<?= $menuDetails['fldMenuKey'] ?>" class="form-control-default GrandParentSub<?= $GrandMenuDetails['fldMenuKey'] ?>  ParentSub<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSub menuGrandParentSub<?= $menuDetails['fldMenuKey'] ?>" value="<?= $subMenuDetails['fldMenuKey'] ?>">
                                                      <?= $subMenuDetails['fldMenuLabel'] ?>
                                                  </h3>
                                              </div>
                                              <div class="card-body">
                                                  <ul class="nav flex-column">
                                                      <?php
                                                      $sql_access = explode(',',$subMenuDetails["menu_accesses"]);
                                                      foreach ($sql_access as $row_access) { ?>
                                                     
                                                          <li class="nav-item">
                                                              <span class="form-group mr-2">
                                                                  <input type="checkbox" name="menuFiles[<?= $GrandMenuDetails['fldMenuKey'] ?>][<?= $menuDetails['fldMenuKey'] ?>][<?= $subMenuDetails['fldMenuKey'] ?>][]" class="form-control-default GrandParentSubAccess<?= $GrandMenuDetails['fldMenuKey'] ?> SubAccess<?= $menuDetails['fldMenuKey'] ?> menuGrandParentSubAccess menuGrandParentSubAccess<?= $subMenuDetails['fldMenuKey'] ?>" value="<?= $row_access ?>" >
                                                                  <label class="text-muted"><?= $row_access ?></label>
                                                              </span>
                                                          </li>

                                                      <?php } ?>
                                                  </ul>
                                              </div>
                                              <!-- /.card-body -->
                                          </div>
                                          <!-- /.card -->
                                      </div>
                                  <?php } } ?>
                              </div>

                          </td>
                      </tr>
                  <?php } ?>
              </table>
          </div>
          <!-- /.card -->
      </div>
  </div>
<?php } ?>
<script>


</script>