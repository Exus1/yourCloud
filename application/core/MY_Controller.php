<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
	function __construct()
	{
		parent::__construct();

		if($this->db_config->get('storage_path') == false)
		{
			if(get_class($this) != 'First_setup')
			{
				redirect('/first_setup');
			}
			else
			{
				return;
			}
		}

		if(! $this->session->logged_in)
		{
			if(get_class($this) != 'Login')
			{
				redirect('/login');
			}
		}

		if(! $this->uri->segment(1, FALSE))
		{
			redirect('/folder');
		}

	}
}