<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
	Login controller
	================

	This contoller is used to creating login page

*/


class Login extends MY_Controller {

	private $db_result;

	//
	// Form validation callback funcion
	//
	public function username_check($name)
	{
		$name = $this->security->xss_clean($name);

		$this->db_result = $this->db->where('name', $name)->get('yc_users')->row_array();

		if(! empty($this->db_result))
		{
			return TRUE;
		}
		else
		{
			$this->form_validation->set_message('username_check', 'User with this Name does not exist');

			return FALSE;
		}
	}

	//
	// Form validation callback funcion
	//
	public function password_verify($password)
	{
		$password = $this->security->xss_clean($password);

		if(password_verify($password, $this->db_result['password']))
		{
			return TRUE;
		}
		else
		{
			$this->form_validation->set_message('password_verify', 'Wrong password');

			return FALSE;
		}
	}

	public function index()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules(array(
			array(
				'field' => 'username',
				'label' => 'Username',
				'rules' => 'required|callback_username_check'
			),
			array(
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required|callback_password_verify'
			)
		));

		if($this->form_validation->run() == FALSE)
		{
			$this->load->view('login_page');

			return;
		}

		$this->db_result['logged_in'] = TRUE;

		$this->session->set_userdata($this->db_result);

		redirect('/');
	}
}
