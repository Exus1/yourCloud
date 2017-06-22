<?php

class Path_converter
{
	private static function get_storage_path()
	{
		return get_instance()->db_config->get('storage_path');
	}

	public static function normalize($path)
	{
		$path = trim($path);

		$path_array = explode(DIRECTORY_SEPARATOR, $path);

		$path = '';

		foreach($path_array as $name)
		{
			$name = trim($name);

			if(empty($name)) continue;

			$path .= DIRECTORY_SEPARATOR . $name;
		}

		if(! empty($path_array[0]))
		{
			return substr($path, 1);
		}

		return $path;
	}

	public static function convert_to_relative($path)
	{
		$path = trim($path);

		$path = self::normalize($path);

		if($path[0] == DIRECTORY_SEPARATOR)
			return substr($path, 1);

		return $path;
	}

	public static function convert_to_absolute($path)
	{
		$path = trim($path);

		$path = self::normalize($path);

		if($path[0] != DIRECTORY_SEPARATOR)
			return DIRECTORY_SEPARATOR . $path;

		return $path;
	}

	public static function to_relative($path)
	{
		$path = trim($path);

		$storage_path = self::get_storage_path();

		if(strpos($path, $storage_path) !== FALSE)
		{
			$path = substr($path, strlen($storage_path));

			return self::convert_to_relative($path);
		}

		return FALSE;
	}

	public static function to_absolute($path)
	{
		$path = trim($path);

		$storage_path = self::get_storage_path();

		$path = $storage_path . DIRECTORY_SEPARATOR . $path;
		$path = self::normalize($path);

		return $path;
	}

	public static function get_parent_path($path)
	{
		if(empty(trim($path))) return '';

		$path = self::normalize($path);

		return substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR));
	}

	public static function get_absolute_parent_path($path)
	{
		$path = self::to_absolute($path);

		return substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR));
	}
}