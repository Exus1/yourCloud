<?php
defined('BASEPATH') OR exit('No direct script access allowed');

abstract class Cloud_object
{
	protected $properties;
	protected $ci;

	static public function get($rel_path)
	{
		$path = get_instance()->db_config->get('storage_path') . DIRECTORY_SEPARATOR . $rel_path;

		if(! file_exists($path))
		{
			return FALSE;
		}

		$result = get_instance()->db->where('relative_path_hash', hash('md5', $rel_path))->get('yc_filecache')->row_array();

		if(empty($result))
		{
			return FALSE;
		}

		$class_name = get_called_class();

		if($class_name == "Cloud_object")
		{
			if($result['type'] == 1)
			{
				$class_name = 'Cloud_file';
			}
			else
			{
				$class_name = 'Cloud_folder';
			}
		}

		return new $class_name($result);
	}

	static public function get_by_id($id)
	{

		$result = get_instance()->db->where('id', $id)->get('yc_filecache')->row_array();

		if(empty($result))
		{
			return FALSE;
		}

		$class_name = get_called_class();

		if($class_name == "Cloud_object")
		{
			if($result['type'] == 1)
			{
				$class_name = 'Cloud_file';
			}
			else
			{
				$class_name = 'Cloud_folder';
			}
		}

		return new $class_name($result);
	}

	static public function add($rel_path, $owner = FALSE)
	{
		$rel_path = Path_converter::normalize($rel_path);

		$abs_path = Path_converter::normalize(get_instance()->db_config->get('storage_path') . DIRECTORY_SEPARATOR . $rel_path);

		if(! file_exists($abs_path))
		{
			return FALSE;
		}

		$pid = get_instance()->db->where('absolute_path_hash', hash('md5', Path_converter::get_parent_path($abs_path)))->get('yc_filecache')->row_array();

		if(empty($pid))
		{
			return FALSE;
		}

		$pid = $pid['id'];

		$name = substr($abs_path, strrpos($abs_path, DIRECTORY_SEPARATOR)+1);

		$type = (is_dir($abs_path))? 2 : 1;

		if($owner === FALSE)
		{
			$owner_id = $_SESSION['user_id'];
		}
		else if(is_object($owner) && (get_class($owner) == "Cloud_user"))
		{
			$owner_id = $owner->user_id;
		}
		else
		{
			if(! get_instance()->db->where('user_id', $owner)->get('yc_users')->result())
			{
				return FALSE;
			}

			$owner_id = $owner;
		}

		$file_data = array(
			'pid' => $pid,
			'owner_id' => $owner_id,
			'name' => $name,
			'type' => $type,
			'size' => filesize($abs_path),
			'created' => time(NULL),
			'absolute_path' => $abs_path,
			'absolute_path_hash' => hash('md5', $abs_path),
			'relative_path' => $rel_path,
			'relative_path_hash' => hash('md5', $rel_path),
			'SHA1' => sha1($abs_path)
		);

		if(! get_instance()->db->insert('yc_filecache', $file_data))
		{
			return FALSE;
		}

		if($type == 1)
		{
			return Cloud_file::get($rel_path);
		}
		else
		{
			return Cloud_folder::get($rel_path);
		}
	}

	function __construct($properties)
	{
		$this->ci =& get_instance();

		$this->properties = $properties;
	}

	public function __get($name)
	{
		return (isset($this->properties[$name]))? $this->properties[$name] : false;
	}

	public function good()
	{
		$path = $this->ci->db_config->get('storage_path') . DIRECTORY_SEPARATOR . $this->relative_path;

		return file_exists($path);
	}

	public function move($folder)
	{
		if(get_class($folder) != 'Cloud_folder')
		{
			return FALSE;
		}

		$new_path = $folder->absolute_path. DIRECTORY_SEPARATOR. $this->name;

		$rel_path = Path_converter::to_relative($new_path);

		$db_data = array(
			'absolute_path' => $new_path,
			'absolute_path_hash' => hash('md5', $new_path),
			'relative_path' => $rel_path,
			'relative_path_hash' => hash('md5', $rel_path),
			'name' => $this->name
		);

		if(rename($this->absolute_path, $new_path))
		{
			if($this->update_data($db_data))
			{
				return TRUE;
			}
			else
			{
				rename($new_path, $this->absolute_path);
			}
		}


		return FALSE;
	}

	public function parent()
	{
		$parent_path = Path_converter::get_parent_path($this->relative_path);

		if(is_dir($parent_path))
		{
			return new Cloud_folder($parent_path);
		}
		else
		{
			return FALSE;
		}
	}

	public function reload()
	{
		$this->properties = $this->db->where('id', $this->id)->get('yc_filecache')->row_array();
	}

	public function rename($name)
	{
		$path = $this->absolute_path;
		$path_to = substr($this->absolute_path, 0, strrpos($this->absolute_path, DIRECTORY_SEPARATOR));

		$path_to .= DIRECTORY_SEPARATOR . $name;

		$path_to = Path_converter::normalize($path_to);

		$rel_path = Path_converter::to_relative($path_to);

		$db_data = array(
			'absolute_path' => $path_to,
			'absolute_path_hash' => hash('md5', $path_to),
			'relative_path' => $rel_path,
			'relative_path_hash' => hash('md5', $rel_path),
			'name' => $name
		);

		if(rename($path, $path_to))
		{
			if($this->update_data($db_data))
			{
				return TRUE;
			}

			rename($path_to, $this->absolute_path);
		}

		return FALSE;
	}

	public function summary()
	{
		$remove_from_props = array(
			'absolute_path' => '',
			'absolute_path_hash' => '',
			'relative_path_hash' => ''
		);

		$summary = array_diff_key($this->properties, $remove_from_props);

		if($summary['type'] == 1) // Files
		{
			$summary['icon_src'] = include_file_type_icon('file.png');
			$summary['icon_alt'] = 'File icon';
		}
		else
		{
			$summary['icon_src'] = include_file_type_icon('search.png');
			$summary['icon_alt'] = 'File icon';
		}

		return $summary;
	}

	public abstract function delete();

	protected function update_data($data)
	{
		if($this->ci->db->where('id', $this->id)->update('yc_filecache', $data))
		{
			{
				$this->properties = array_merge($this->properties, $data);

				return TRUE;
			}
		}
	
		return FALSE;
	}
}