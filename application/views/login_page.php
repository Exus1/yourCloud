<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<DocType html>
<!DOCTYPE html>
<html>
<head>
	<title>yourCloud - Sign in</title>

	<link rel="stylesheet" type="text/css" href="<?= include_css('login_page') ?>">
	<link rel="stylesheet" type="text/css" href="<?= include_asset('font_icons/css/fontello.css') ?>">
	<link rel="stylesheet" href="<?= include_css('bootstrap/bootstrap.min') ?>">
</head>
<body>
	<div class="container-fluid">
		<div class="row justify-content-center align-items-center">
			<div class="col" >

				<div class="card" id="login-container" style="width: 400px">
					<div class="card-block">
						<h3 class="card-title">yourCloud</h3>
						<h6 class="card-subtitle mb-2 text-muted">Sign in</h6>

						<form class="mt-4 <?= form_error('username')? 'has-danger' : '' ?>"" action="" method="POST">
							<div class="form-group">
  							  <label for="username">Username</label>
  							  <input class="form-control <?= form_error('username')? 'form-control-danger' : '' ?>" id="username" placeholder="Username" name="username" value="<?= isset($_POST['username'])? $_POST['username'] : '' ?>">
  							  <?= form_error('username', '<div class="form-control-feedback">', '</div>') ?>
  							</div>

  							<div class="form-group <?= form_error('password')? 'has-danger' : '' ?>"">
  							  <label for="password">Password</label>
  							  <input type="password" class="form-control <?= form_error('password')? 'form-control-danger' : '' ?>" id="password" placeholder="Password" name="password">
  							  <?= form_error('password', '<div class="form-control-feedback">', '</div>') ?>
  							</div>
  							<button type="submit" class="btn btn-primary">Sign In</button>
						</form>
					</div>

				</div>

			</div>
		</div>
	</div>
</body>
</html>
