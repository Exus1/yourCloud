<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
	Folder controller
	=================

	This controller is used to managing and showing selected folder. 
	All subsequent arguments passed to controller represent relative (Relative to the directory of the logged user) path to the folder.
*/


class Folder_controller extends MY_Controller {

	// Paths leading to selected folder
	protected $paths = array(
		'absolute' => '',
		'relative_to_user' => '',
		'relative' => '',
		'base' => '',
		'user_base' => ''
	);

	protected $storage = NULL;

	//
	//	Public functions
	//	================
	//
	public function _remap($controller_name, $args)
	{
		$this->_generate_paths($controller_name, $args);

		$this->storage = Cloud_folder::get($this->paths['relative']);
		
		if(! $this->storage)
		{
			show_404();
		}

		$this->_load_langs();

		// Checking whether user is sending file
		if(! empty($_FILES))
		{
			$this->_upload_file_handle();

			die();
		}

		// Checking whether user wants to use API
		if(isset($_GET['action']))
		{
			$this->_action_handle();

			die();
		}

		$this->_load_view();
	}

	protected function _load_langs()
	{
		$this->lang->load('folder_view', 'english');
		$this->lang->load('general', 'english');
	}

	protected function _load_view($vars = array())
	{
		// Data passed to view
		$veiw_data = array(
			'folder_name' => $this->storage->name,
		);

		$veiw_data = array_merge($veiw_data, $vars);

		$this->load->view('folder_view', $veiw_data);
	}

	//
	// Method for catching api calls
	//
	protected function _action_handle($action = FALSE)
	{
		if($action == FALSE)
		{
			$action = $_GET['action'];
		}

		$method_name = $action. '_handle';

		if(method_exists($this, $method_name))
		{
			return $this->$method_name();
		}
		else
		{
			echo 'This action does not exist';
		}
	}

	//
	// Get folder list action hanlde
	//
	public function get_folders_handle()
	{
		echo json_encode($this->storage->get_folders(TRUE));
	}

	//
	// Get files list action handle
	//
	public function get_files_handle()
	{
		echo json_encode($this->storage->get_files(TRUE));
	}

	//
	// Get all objects list action handle
	//
	public function get_all_objects_handle()
	{
		echo json_encode($this->storage->get_all_objects(TRUE));
	}

	//
	// object properties action handle
	//
	public function object_properties_handle()
	{
		if(! isset($_GET['id']) || empty($_GET['id']))
		{
			echo 'You must give a object id in id parameter';
			return;
		}

		if(! is_numeric($_GET['id']))
		{
			echo 'Id must be a id of object';
			return;
		}

		$item = Cloud_object::get_by_id($_GET['id']);

		if($item)
		{
			$info = $item->summary();

			$this->lang->load('file_properties');
			$lang =& $this->lang;

			$properties = array(
				$lang->line('name') => $info['name'],
				$lang->line('type') => ($info['type'] == 1)? $lang->line('type_file') : $lang->line('type_folder'),
				$lang->line('size') => ($info['type'] == 1)? $this->_size_converter($info['size']) : '---',
				$lang->line('owner') => $this->db->select('name')->where('user_id', $info['owner_id'])->get('yc_users')->row_array()['name'],
				$lang->line('shared') => ($info['sharing'])? $lang->line('shared_yes') : $lang->line('shared_no'),
				$lang->line('created') => date('d.m.Y H:i', $info['created']),
				'icon-src' => $info['icon_src'], //'http://yourcloud.dev/application/assets/file_type_icons_png/search.png',
				'icon-alt' => $info['icon_alt']
			);
			
			echo json_encode($properties);
		}
		else
		{
			echo 'File does not exist';
		}
	}

	//
	// Create folder action handle
	//
	public function create_folder_handle()
	{
		if(! isset($_GET['name']) || empty($_GET['name']))
		{
			echo 'You must give a folder name in name parameter';
			return;
		}

		$name = $this->input->get('name', TRUE);

		$name = urldecode($name);

		$folder = $this->storage->create_folder($name);
		
		if($folder === 1)
		{
			echo 'Folder with this name already exist';
		}
		else if($folder)
		{
			echo 'success';
		}
		else
		{
			echo 'error';
		}
	}

	// 
	// Create file action handle
	//
	public function create_file_handle()
	{
		if(! isset($_GET['name']) || empty($_GET['name']))
		{
			echo 'You must give a file name in name parameter';
			return;
		}

		$name = $this->input->get('name', true);

		$name = urldecode($name);

		$file = $this->storage->create_file($name);

		if($file === 1)
		{
			echo 'File with this name already exist';
		}
		else if($file)
		{
			echo 'success';
		}
		else
		{
			echo 'error';
		}
	}

	//
	// Dowload file action handle
	//
	public function download_file_handle()
	{
		if(! isset($_GET['id']) || empty($_GET['id']))
		{
			echo 'You must give a file id in id parameter';
			return;
		}

		$id = $this->input->get('id');

		$file = Cloud_file::get_by_id($id);

		if(! $file)
		{
			echo "Selected file does not exist";

			return;
		}

		$uploader = new File_uploader($file);

		$uploader->send();
	}

	//
	// Rename object action handle
	//
	public function rename_object_handle()
	{
		if(! isset($_GET['id']) || empty($_GET['id']))
		{
			echo 'You must give a file id in id parameter';
			return;
		}

		if(! isset($_GET['new_name']) || empty($_GET['new_name']))
		{
			echo 'You must give a file new name in new_name parameter';
			return;
		}

		$obj = Cloud_object::get_by_id($_GET['id']);

		if(! $obj)
		{
			echo "Object does not exist";
			return;
		}

		$name = $this->input->get('new_name', TRUE);

		if($obj->rename($name))
		{
			echo 'success';
		}
		else
		{
			echo 'error';
		}
	}

	//
	// Delete object action handle
	//
	public function delete_object_handle()
	{
		if(! isset($_GET['id']) || empty($_GET['id']))
		{
			echo 'You must give a file id in id parameter';
			return;
		}

		$obj = Cloud_object::get_by_id($_GET['id']);

		if(! $obj)
		{
			echo "Object does not exist";
			return;
		}

		if($obj->delete())
		{
			echo 'success';
		}
		else
		{
			echo 'error';
		}
	}

	//
	// File upload handle
	//
	public function upload_file_handle()
	{
		if(File_downloader::files_count() == 0)
		{
			return 0;
		}

		$files = File_downloader::get_files();

		foreach($files as $file)
		{
			if(! $file->move($this->storage))
			{
				show_404();
			}
		}
	}

	protected function _generate_paths($controller_name, $args = array())
	{
		//Relative
		$this->paths['relative'] = $this->session->user_id;

		if($controller_name != 'index')
		{
			$this->paths['relative_to_user'] .= urldecode($controller_name);
		}

		if(! empty($args))
		{
			foreach ($args as $folder_name) 
			{
				$this->paths['relative_to_user'] .= DIRECTORY_SEPARATOR. urldecode($folder_name);
			}
		}

		$this->paths['relative'] .= DIRECTORY_SEPARATOR. $this->paths['relative_to_user'];

		$this->paths['relative'] = Path_converter::normalize($this->paths['relative']);

		// Base
		$this->paths['base'] = Path_converter::normalize($this->db_config->get('storage_path'));

		// User Base
		$this->paths['user_base'] = $this->paths['base']. DIRECTORY_SEPARATOR. $this->session->user_id;

		// Absolute
		$this->paths['absolute'] = $this->paths['base']. DIRECTORY_SEPARATOR. $this->paths['relative'];
	}

	protected function _size_converter($bytes)
	{
	    $bytes = floatval($bytes);

	    $arBytes = array(
	        0 => array(
	            "UNIT" => "TB",
	            "VALUE" => pow(1024, 4)
	        ),
	        1 => array(
	            "UNIT" => "GB",
	            "VALUE" => pow(1024, 3)
	        ),
	        2 => array(
	            "UNIT" => "MB",
	            "VALUE" => pow(1024, 2)
	        ),
	        3 => array(
	            "UNIT" => "KB",
	            "VALUE" => 1024
	        ),
	        4 => array(
	            "UNIT" => "B",
	            "VALUE" => 1
	        ),
	    );
	
	    foreach($arBytes as $arItem)
	    {
	        if($bytes >= $arItem["VALUE"])
	        {
	            $result = $bytes / $arItem["VALUE"];
	            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
	            break;
	        }
	    }

	    return (isset($result))? $result : '0B';
	}

}
