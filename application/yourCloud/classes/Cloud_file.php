<?php

class Cloud_file extends Cloud_object
{
	function __construct($properties)
	{
		parent::__construct($properties);

		$this->properties['extension'] = substr($this->name, strrpos($this->name, '.')+1);
	}

	public function get_content()
	{
		return file_get_contents($this->absolute_path);
	}

	public function set_content($content)
	{
		return file_put_contents($this->absolute_path, $content, FILE_APPEND);
	}

	public function delete()
	{
		if(! unlink($this->absolute_path))
		{
			return FALSE;
		}

		return $this->ci->db->where('id', $this->id)->delete('yc_filecache');
	}
}