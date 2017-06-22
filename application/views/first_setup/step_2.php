<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-4">
  <div class="card" id="config-form">
    <div class="card-block">
      <h4 class="card-title">yourCloud - First setup</h4>
      <h6 class="text-danger"><?= $error_msg ?></h6>
      <form action="" method="POST">
        <div class="form-group <?= form_error('storage_path')? 'has-danger' : '' ?>">

          <label for="storage_path">
          	Storage folder path:
          </label>

          <input type="text" class="form-control <?= form_error('storage_path')? 'has-danger' : '' ?>" id="storage_path" placeholder="Storage path" name="storage_path" value="<?= isset($_POST['storage_path'])? $_POST['storage_path'] : '' ?>">

          <?= form_error('storage_path', '<div class="form-control-feedback">', '</div>') ?>

          <small class="form-text text-muted">
          	Enter the path where the files will be stored
          </small>

        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>

  </div>
</div>