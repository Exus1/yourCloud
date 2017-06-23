<?php

class Cloud_folder extends Cloud_object
{

	public function delete()
	{
		$objects = $this->get_all_objects();

		foreach ($objects['folders'] as $object) 
		{
			if(! $object->delete())
			{
				return FALSE;
			}
		}

		foreach ($objects['files'] as $object) 
		{
			if(! $object->delete())
			{
				return FALSE;
			}
		}

		if(! rmdir($this->absolute_path))
		{
			return FALSE;
		}

		return $this->ci->db->where('id', $this->id)->delete('yc_filecache');
	}

	public function create_folder($name)
	{
		$path = $this->absolute_path. DIRECTORY_SEPARATOR. $name;

		if(file_exists($path))
		{
			return 1;
		}

		if(! mkdir($path))
		{
			return FALSE;
		}

		if(Cloud_object::add($this->relative_path. DIRECTORY_SEPARATOR. $name))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function create_file($name, $content = '')
	{
		$path = $this->absolute_path. DIRECTORY_SEPARATOR. $name;

		if(file_exists($path))
		{
			return 1;
		}

		
		$file = fopen($path, 'w');

		if(! $file)
		{
			return FALSE;
		}

		fwrite($file, $content);
		fclose($file);

		if(Cloud_object::add($this->relative_path. DIRECTORY_SEPARATOR. $name))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function get_files($summary = FALSE)
	{
		$result = $this->ci->db->where('pid', $this->id)->where('type', '1')->get('yc_filecache')->result_array();

		$files = array();

		foreach($result as &$object)
		{
			$object = new Cloud_file($object);

			if($summary)
			{
				$files[$object->name] = $object->summary();
			}
			else
			{
				$files[$object->name] = $object;
			}
		}

		return $files;
	}

	public function get_folders($summary = FALSE)
	{
		$result = $this->ci->db->where('pid', $this->id)->where('type', '2')->get('yc_filecache')->result_array();

		$folders = array();

		foreach($result as &$object)
		{
			$object = new Cloud_folder($object);

			if($summary)
			{
				$folders[$object->name] = $object->summary();
			}
			else
			{
				$folders[$object->name] = $object;
			}
		}

		return $folders;
	}

	public function get_all_objects($summary = FALSE)
	{
		$returned = array(
			'files' => $this->get_files($summary),
			'folders' => $this->get_folders($summary)
		);

		return $returned;
	}

	public function append_file($file)
	{
		if(get_class($file) != 'Cloud_file')
		{
			return FALSE;
		}

		if($file->move($this))
		{
			return $file;
		}
		else
		{
			return FALSE;
		}
	}
}