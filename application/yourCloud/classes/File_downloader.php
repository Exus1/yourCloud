<?php

class File_downloader
{
	static public function files_count()
	{
		if(empty($_FILES))
		{
			return 0;
		}

		return count($_FILES['file']['name']);
	}

	static public function get_files()
	{
		if(! is_array($_FILES['file']['name']))
		{
			return array(new File_downloader_file($_FILES['file']));
		}

		for($i = 0; $i < count($_FILES['file']['name']); $i++)
		{
			foreach($_FILES['file'] as $key => $value)
			{
				$returned_array[$i][$key] = $value[$i];
			}

			$returned_array[$i] = new File_downloader_file($returned_array[$i]);
		}

		return $returned_array;
	}
}


class File_downloader_file
{
	protected $propeties;

	function __construct($propeties)
	{
		$this->propeties = $propeties;
	}

	function __get($name)
	{
		if(! isset($this->propeties[$name]))
		{
			return FALSE;
		}

		return $this->propeties[$name];
	}

	// $folder can be relative path or Cloud_folder object
	public function move($folder)
	{
		if(get_class($folder) != 'Cloud_folder')
		{
			return FALSE;
		}

		$new_path = $folder->absolute_path. DIRECTORY_SEPARATOR. $this->name;

		if(! rename($this->tmp_name, $new_path))
		{
			return FALSE;
		}

		$rel_path = Path_converter::to_relative($new_path);

		$file = Cloud_object::add($rel_path, $folder->owner_id);

		if(! $file)
		{
			unlink($new_path);
		}

		return $file;
	}
}