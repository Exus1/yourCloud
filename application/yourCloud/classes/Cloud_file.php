<?php

class Cloud_file extends Cloud_object
{

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

		return $this->db->where('id', $this->id)->delete('yc_filecache');
	}
}