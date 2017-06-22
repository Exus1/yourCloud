<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function remove_slashs_from_edges($string)
{
	while(substr($string, 0, 1) == '/')
	{
		$string = substr($string, 1);
	}
	while(substr($string, -1, 1) == '/')
	{
		$string = substr($string, 0, strlen($string)-1);
	}
	return $string;
}

function normalize_path($path, $is_dir = 'auto', $is_relative = 'auto')
{
	$created_path = '';
	$relative_control = false;
	$path_array = explode(DIRECTORY_SEPARATOR, $path);

	foreach($path_array as $folder_name)
	{
		if(empty($folder_name)) continue;

		$created_path .= DIRECTORY_SEPARATOR . $folder_name;
	}

	if(empty($created_path))
	{
		return $created_path;
	}

	if($is_relative)
	{
		if(!empty($path_array[0]) || ($is_relative === true))
		{
			$created_path = substr($created_path, 1);
		}
	}

	if($is_dir)
	{
		if(($is_dir === true) || is_dir($created_path)) 
		{
			$created_path .= DIRECTORY_SEPARATOR;
		}
	}

	return $created_path;
}

function get_parent_path($path)
{
	return substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR));
}

function is_in_storage($path)
{
	$ci =& get_instance();
	$storage_path = normalize_path($ci->config->item('cloud_storage_path'), false, true);
	$path = normalize_path($path, false, true);

	return (strstr($storage_path, $path))? true : false;

}