<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Db_config
{
	protected $ci;
	protected $db;

	protected $table_name;


	function __construct()
	{
		$this->ci =& get_instance();
		$this->table_name = $this->ci->config->item('db_config_table');
		$this->db =& $this->ci->db;
	}

	public function get($key_name)
	{
		if(! $this->db)
		{
			return FALSE;
		}

		$result = $this->db->select('value')->where('name', $key_name)->get($this->table_name)->row();

		return (isset($result))? $result->value : false;
	}

	public function get_by_id($id)
	{
		$result = $this->db->select('value')->where('name', $key_name)->get($this->table_name)->row();

		return (isset($result))? $result->value : false;
	}

	public function set($key_name, $value)
	{
		return $this->db->set('value', $value)->where('name', $key_name)->update($this->table_name);
	}

	public function add($name, $value)
	{
		$data = array(
			'name' => $name,
			'value' => $value
		);
		return $this->db->insert('yc_config', $data);
	}

	public function set_by_id($id, $value)
	{
		return $this->db->set('value', $value)->where('id', $id)->update($this->table_name);
	}
}