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
			$this->ci->db->where('user_id', $id);
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

	public function get_shared_objects($summary = FALSE, $user = FALSE)
	{
		if($user !== false)
		{

			if(get_class($user) != 'Cloud_user')
			{
				return FALSE;
			}

			get_instance()->db->where('owner_id', $user->user_id);
		}

		$result = get_instance()->db->where('partner_id', $this->user_id)->get('yc_shared_objects')->result_array();

		if(empty($result))
		{
			return FALSE;
		}

		$returned_array = array(
			'files' => array(),
			'folders' => array()
		);

		foreach($result as $object)
		{
			$tmp_obj = Cloud_object::get_by_id($object['fid']);

			if($tmp_obj->type == '2')
			{
				$type = 'folders';
			}
			else
			{
				$type = 'files';
			}

			if($summary)
			{
				$returned_array[$type][$tmp_obj->name] = $tmp_obj->summary();
			}
			else
			{
				$returned_array[$type][$tmp_obj->name] = $tmp_obj;
			}
		}

		return $returned_array;
	}

	public function get_my_shared_objects($summary = FALSE)
	{
		$result = get_instance()->db->where('owner_id', $this->user_id)->get('yc_shared_objects')->result_array();

		if(empty($result))
		{
			return FALSE;
		}

		foreach($result as $object)
		{
			$tmp_obj = Cloud_object::get_by_id($object['fid']);

			if($tmp_obj->type == '2')
			{
				$type = 'folders';
			}
			else
			{
				$type = 'files';
			}

			if($summary)
			{
				$returned_array[$type][$tmp_obj->name] = $tmp_obj->summary();
			}
			else
			{
				$returned_array[$type][$tmp_obj->name] = $tmp_obj;
			}
		}

		return $returned_array;
	}
}