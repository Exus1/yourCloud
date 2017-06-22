<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File_downloader
{

	public $key_name = 'file';

	public function files_are_coming()
	{
		return ! empty($_FILES);
	}

	public function get_files_count()
	{
		return count($_FILES[$this->key_name]['name']);
	}

	public function get_files()
	{
		if($this->get_files_count() > 1)
		{
			foreach($_FILES[$this->key_name] as $key_name => &$val)
			{
				foreach($val as $key => $file_data)
				{
					$files_array[$key][$key_name] = $file_data;
				}
			}
		}
		else
		{	
			$files_array[] = $_FILES[$this->key_name];
		}
		
		
		foreach($files_array as &$file_data)
		{
			$file_data = new File_dowloader_file($file_data);
		}

		return $files_array;
	}
}


class File_dowloader_file
{
	protected $file_data;

	function __construct($file_data)
	{
		$this->file_data = $file_data;
	}

	public function save($path, $name = false)
	{
		if(! is_writable($path))
		{
			return false;
		}

		if(! is_uploaded_file($this->file_data['tmp_name']))
		{
			return false;
		}

		if($name === false)
		{
			// Check whether file name is not path
			$name = substr($this->file_data['name'], strrpos($this->file_data['name'], DIRECTORY_SEPARATOR));
		}

		$path = normalize_path($path, true, false) . $name;

		return move_uploaded_file($this->file_data['tmp_name'], $path);
	}

	public function get_size()
	{
		return $this->file_data['size'];
	}

	public function get_name()
	{
		return $this->file_data['name'];
	}

	public function get_type()
	{
		return $this->file_data['type'];
	}

	public function get_error()
	{
		return $this->file_data['error'];
	}
}