<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="col-4">
  <div class="card" id="config-form">
    <div class="card-block">
      <h4 class="card-title">yourCloud - First setup</h4>
      <h6 class="text-danger"><?= $error_msg ?></h6>
      <form action="" method="POST">
        <div class="form-group <?= form_error('login')? 'has-danger' : '' ?>">

          <label for="login" class="form-control-label">
            Login
          </label>

          <input type="text" class="form-control <?= form_error('login')? 'form-control-danger' : '' ?>" id="login" placeholder="Login" name="login" value="<?= isset($_POST['login'])? $_POST['login'] : '' ?>">

          <?= form_error('login', '<div class="form-control-feedback">', '</div>') ?>

          <small class="form-text text-muted">
            Enter login to your new account
            </small>
        </div>

        <div class="form-group <?= form_error('e_mail')? 'has-danger' : '' ?>">
          <label for="e-mail">E-mail</label>

          <input type="email" class="form-control <?= form_error('e_mail')? 'form-control-danger' : '' ?>" id="e-mail" placeholder="E-mail" name="e_mail" value="<?= isset($_POST['e_mail'])? $_POST['e_mail'] : '' ?>">
          <?= form_error('e_mail', '<div class="form-control-feedback">', '</div>') ?>
          <small class="form-text text-muted">Enter e-mail to your new account</small>
        </div>

        <div class="form-group <?= form_error('password')? 'has-danger' : '' ?>">
          <label for="password">Password</label>
          <input type="password" class="form-control <?= form_error('password')? 'form-control-danger' : '' ?>" id="password" placeholder="Password" name="password">
          <?= form_error('password', '<div class="form-control-feedback">', '</div>') ?>
        </div>

        <div class="form-group">
          <label for="repeat_password">Repeat password</label>
          <input type="password" class="form-control" id="repeat_password" placeholder="Password">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>

  </div>
</div>