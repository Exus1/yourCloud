<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-6 col-md-4">
  <div class="card" id="config-form">
    <div class="card-block">
      <h4 class="card-title">yourCloud - First setup</h4>
      <h6 class="text-danger"><?= $error_msg ?></h6>
      <form action="" method="POST">
        <div class="form-group <?= form_error('address')? 'has-danger' : '' ?>">
          <label for="db_address">Database adress:</label>
          <input type="text" class="form-control <?= form_error('address')? 'form-control-danger' : '' ?>" id="db_address" placeholder="Ip address" name="address" value="<?= isset($_POST['address'])? $_POST['address'] : '' ?>">
          <?= form_error('address', '<div class="form-control-feedback">', '</div>') ?>
          <small class="form-text text-muted">Type your MySQL database address</small>
        </div>

        <div class="form-group <?= form_error('name')? 'has-danger' : '' ?>">
          <label for="db_address">Database name:</label>
          <input type="text" class="form-control <?= form_error('name')? 'form-control-danger' : '' ?>" id="db_address" placeholder="Name" name="name" value="<?= isset($_POST['name'])? $_POST['name'] : '' ?>">
          <?= form_error('name', '<div class="form-control-feedback">', '</div>') ?>
          <small class="form-text text-muted">Type your MySQL database name</small>
        </div>

        <div class="form-group <?= form_error('user')? 'has-danger' : '' ?>">
          <label for="db_address">Database user:</label>
          <input type="text" class="form-control <?= form_error('user')? 'form-control-danger' : '' ?>" id="db_address" placeholder="Username" name="user" value="<?= isset($_POST['user'])? $_POST['user'] : '' ?>"> 
          <?= form_error('user', '<div class="form-control-feedback">', '</div>') ?>
          <small class="form-text text-muted">Type your MySQL database user</small>
        </div>

        <div class="form-group <?= form_error('password')? 'has-danger' : '' ?>">
          <label for="db_address">Database password:</label>
          <input type="password" class="form-control <?= form_error('password')? 'form-control-danger' : '' ?>"  id="db_address" placeholder="Password" name="password" value="<?= isset($_POST['password'])? $_POST['password'] : '' ?>">
          <?= form_error('password', '<div class="form-control-feedback">', '</div>') ?>
          <small class="form-text text-muted">Type your MySQL database user password</small>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>

  </div>
</div>
