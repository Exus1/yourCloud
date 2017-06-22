<?php

class Cloud_user
{
	protected $properties;
	protected $ci;

	function __construct($id = FALSE)
	{
		$this->ci =& get_instance();

		if($id === FALSE)
		{
			$this->properties =& $_SESSION;

			return;
		}

		if(is_numeric($id))
		{
			$this->ci->db->where('id', $id);
		}
		else
		{
			$this->ci->db->where('name', $id);
		}

		$this->properties = $this->ci->db->select('user_id, name, email')->get('yc_users')->row_array();

		if($this->ci->session->user_id == $this->user_id)
		{
			$this->properties['logged_in'] = TRUE;
		}
		else
		{
			$this->properties['logged_in'] = FALSE;
		}
	}

	function isset()
	{
		return isset($this->properties['user_id']);
	}

	function __get($name)
	{
		if(isset($this->properties[$name]))
		{
			return $this->properties[$name];
		}
		else
		{
			return FALSE;
		}
	}
}