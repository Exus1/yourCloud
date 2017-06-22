<!DocType html>
<html lang="pl">
<head>
	<title>yourCloud - <?= $folder_name ?></title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" href="<?= include_css('dropzone') ?>">
	<link rel="stylesheet" href="<?= include_css('bootstrap/bootstrap.min') ?>">
	<link rel="stylesheet" href="<?= include_css('folder_view') ?>">
	<link rel="stylesheet" type="text/css" href="<?= include_asset('font_icons/css/fontello.css') ?>">
	<link rel="stylesheet" type="text/css" href="<?= include_css('colourful_menu') ?>">

	<link rel="stylesheet" type="text/css" href="<?= include_css('drive_content') ?>">

</head>
<body>


	<nav class="navbar navbar-light bg-faded fixed-top" id="navbar">

		<div class="row">


			<div class="col">
				<div class="breadcrumb mb-0">
					<div>
						

						<?php
							$uri = site_url(). '/folder';

							$segments = get_instance()->uri->segment_array();

							if(count($segments) == 1)
							{
								echo '<a class="breadcrumb-item active" href="'. site_url() .'"><i class="icon-home home-link"></i></a>';
							}
							else
							{
								echo '<a class="breadcrumb-item" href="'. site_url() .'"><i class="icon-home home-link"></i></a>';
							}

							for($i = 2; $i <= count($segments); $i++)
							{
								$uri .= '/'. $segments[$i];

								if($i == count($segments))
								{
									echo '<a class="breadcrumb-item active" href="'. $uri .'">'. urldecode($segments[$i]) .'</a>';
									break;
								}

								echo '<a class="breadcrumb-item" href="'. $uri .'">'. urldecode($segments[$i]) .'</a>';
							}
						?>

						<!-- <a class="breadcrumb-item" href="#">2</a>
						<a class="breadcrumb-item active" href="#">3</a> -->
					</div>

					<div>
						<div id="view-button" class="dropdown">
							<a class="btn btn-secondary dropdown-toggle" href="https://example.com" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    							<i class="icon-th-list"></i>
  							</a>
  							<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
    							<a class="dropdown-item" href="#"><i class="icon-th-list"></i></a>
    							<a class="dropdown-item" href="#"><i class="icon-th-large"></i></a>
  							</div>
						</div>
						<div id="sort-button" class="dropdown">
							<a class="btn btn-secondary dropdown-toggle" href="https://example.com" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    							<i class="icon-sort-name-up"></i>
  							</a>
  							<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
    							<a class="dropdown-item" href="#"><i class="icon-sort-name-up"></i></a>
    							<a class="dropdown-item" href="#"><i class="icon-sort-name-down"></i></a>
  							</div>
						</div>
					</div>
				</div>
			</div>


			<div class="" id="right-menu-categories">

				<ul class="nav row" role="tablist">
					<li class="nav-item col">
    					<a class="active" data-toggle="tab" href="#item-properties" role="tab"><i class="icon-info"></i></a>
  					</li>
					<li class="nav-item col">
						<a data-toggle="tab" href="#shared-items" role="tab"><i class="icon-share"></i></a>
					</li>
					<li class="nav-item col">
						<a data-toggle="tab" href="#profile" role="tab"><i class="icon-cog-alt"></i></a>
					</li>
				</ul>

			</div>
		</div>

	</nav>

	<div class="container-fluid" id="content">

		<div class="row">
			<div class="col pt-3" id="left-content-container" style=" min-width: 500px">
				<div class="" id="floating-text">
					Drag to upload
				</div>

				<div class="row" id="drive-content" data-id='' data-name='' data-path='' data-url='<?= get_instance()->uri->uri_string() ?>'>

				<!-- Szablon obiektu -->
					<!-- <div class="col-3 col-sm-2 col-md-2 col-lg-2 col-xl-1 mb-3">
						<div class="drive-object" data-id='' data-name='' data-type=''>
							<img src="<?= include_asset('file_type_icons_png/search.png') ?>" alt="" class="icon">

							<p>Nazwa pliku</p>
						</div>
					</div> -->

				</div>
			</div>

			<nav class="colourful-menu" id="corner-menu">
	   			<input type="checkbox" href="#" class="colourful-menu-open" name="colourful-menu-open" id="colourful-menu-open" />
	   			<label class="colourful-menu-open-button" for="colourful-menu-open">
	   				<span class="colourful-lines colourful-line-1"></span>
	    			<span class="colourful-lines colourful-line-2"></span>
	    			<span class="colourful-lines colourful-line-3"></span>
	  			</label>

	   			<a class="colourful-menu-item colourful-blue" data-toggle="modal" data-action-create="file" data-target="#corner-menu-modal-file">
	   				Fi
	   			</a>
	   			<a class="colourful-menu-item colourful-green" data-toggle="modal" data-target="#corner-menu-modal-folder">
	   				Fo
	   			</a>
			</nav>

			<!-- Modal -->
			<div class="modal fade corner-menu-modal" id="corner-menu-modal-folder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  			<div class="modal-dialog" role="document">
	    			<div class="modal-content">
	      				<div class="modal-header">
	        				<h5 class="modal-title">File creating</h5>
	        				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          					<span aria-hidden="true">&times;</span>
	        				</button>
	      				</div>
	      				<div class="modal-body">
	       					<div class="form-group">
	            				<label for="recipient-name" class="form-control-label">File name: <span class="text-danger message"></span></label>
	            				<input type="text" class="form-control" id="recipient-name">
	            				<div class="form-control-feedback"></div>
	          				</div>
	      				</div>
	      				<div class="modal-footer">
	        				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	        				<button type="button" class="btn btn-primary" data-create="folder">Create</button>
	      				</div>
	    			</div>
	  			</div>
			</div>

			<!-- Modal -->
			<div class="modal fade corner-menu-modal" id="corner-menu-modal-file" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  			<div class="modal-dialog" role="document">
	    			<div class="modal-content">
	      				<div class="modal-header">
	        				<h5 class="modal-title">File creating</h5>
	        				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          					<span aria-hidden="true">&times;</span>
	        				</button>
	      				</div>
	      				<div class="modal-body">
	       					<div class="form-group">
	            				<label for="recipient-name" class="form-control-label">File name: <span class="text-danger message"></span></label>
	            				<input type="text" class="form-control" id="recipient-name">
	            				<div class="form-control-feedback"></div>
	          				</div>
	      				</div>
	      				<div class="modal-footer">
	        				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	        				<button type="button" class="btn btn-primary" data-create="file">Create</button>
	      				</div>
	    			</div>
	  			</div>
			</div>


			<div class="" id="right-menu">
				<div class="row tab-content" id="right-menu-top-content">

	  				<div id="item-properties" class="tab-pane active">
	  					<div class="col-12 text-center mb-5 preview-img">
							<img src="<?= include_asset('file_type_icons_png/search.png') ?>" alt="">
						</div>
	  					<div class="col-12 preview-properties">
	  						<p>Select a file</p>
						</div>
	  				</div>

	  				<div id="shared-items" class="tab-pane w-100">
	  					<div class="col-12 text-center">
	  						<h5>Your shared items</h5>
	  					</div>
	  				</div>

				</div>

				<div class="row" id="upload-section">
					<div class="col-12 mb-2" id="upload-box">

						<!-- <div class="row upload-item">
							<div class="col-12 action text-right">
								<button data-dz-remove class="btn btn-cancel">Anuluj</button>
							</div>
							<div class="col-2 icon">
								<img src="<?= include_asset('file_type_icons_png/search.png') ?>" alt="">
							</div>
							<div class="col-10 text-right file-details">
								<div class="row"><div data-dz-name class="col-12 text-right">Nazwa pliku</div>
									<div data-dz-size class="col-12 text-right">6.5MB</div>
								</div>
							</div>
							<div class="w-100">
								<div class="progress">
									<div data-dz-uploadprogress class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
						</div> -->

					</div>

					<div class="col-12 pb-2 text-right">
						<button class="btn btn-primary" id="upload-button"><i class="icon-upload-cloud"></i>Upload file</button>
					</div>
				</div>
			</div>
		</div>
	</div>


	<script src="<?= include_js('jquery/jquery.min') ?>"></script>
	<script src="<?= include_js('bootstrap/bootstrap.min') ?>"></script>
	<script src="<?= include_js('dropzone') ?>"></script>
  	<script type="text/javascript" src="<?= include_js('yourCloud') ?>"></script>
  	<script type="text/javascript" src="<?= include_js('script') ?>"></script>

</body>
</html>
