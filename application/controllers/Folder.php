<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Folder extends MY_Controller {

	private $paths = array(
		'absolute' => '',
		'relative_to_user' => '',
		'relative' => '',
		'base' => '',
		'user_base' => ''
	);

	protected $storage = NULL;



	public function _remap($controller_name, $args)
	{
		 
		$this->_generate_paths($controller_name, $args);

		$this->storage = Cloud_folder::get($this->paths['relative']);


		if(! $this->storage)
		{
			show_404();
		}

		

		// User want do some operation
		if(! empty($_FILES))
		{
			$this->_upload_file_handle();

			return;
		}

		if(isset($_GET['action']))
		{
			$this->_action_handle();

			return;
		}

		$veiw_data = array(
			'folder_name' => $this->storage->name,
		);

		$this->load->view('folder_view', $veiw_data);

		// $script_data = array(
		// 	'folder_name' => $this->this_storage->get_name(),
		// 	'folder_path' => $this->this_storage->get_paths()['relative'],
		// 	'api_address' => base_url() . 'api/',
		// 	'base_url' => base_url(),
		// 	'corner_menu_messages' => array(
		// 		'file' => array(
		// 			'title' => 'File creating test',
		// 			'label' => 'File name',
		// 			'err_msg' => 'Thats file already exists',
		// 			'empty_input' => 'You must input file name'
		// 		),
		// 		'folder' => array(
		// 			'title' => 'Folder creating test',
		// 			'label' => 'Folder name',
		// 			'err_msg' => 'Thats older already exists',
		// 			'empty_input' => 'You must input file name'
		// 		)
		// 	),
		// );

		// $view_data = array(
		// 	'this_folder' => $this->this_storage,
		// 	'script_data' => json_encode($script_data)
		// );

		// $this->load->view('site_header');
		// $this->load->view('folder_view/view', $view_data);
		// $this->load->view('site_footer');
	}

	private function _action_handle($action = FALSE)
	{
		if($action == FALSE)
		{
			$action = $_GET['action'];
		}

		switch($action)
		{
			case 'get_folders':
			{
				// print '<pre>';
				// $folders = $this->storage->get_folders(TRUE);
				// print_r($folders);
				// break;

				echo json_encode($this->storage->get_folders(TRUE));
				break;
			}

			case 'get_files':
			{
				echo json_encode($this->storage->get_files(TRUE));
				break;
			}

			case 'get_all_objects':
			{
				echo json_encode($this->storage->get_all_objects(TRUE));
				break;
			}

			case 'object_properties':
			{
				if(! isset($_GET['id']) || empty($_GET['id']))
				{
					echo 'You must give a object id in id parameter';
					break;
				}

				$this->_object_properties_action();
				break;
			}

			case 'create_folder':
			{
				if(! isset($_GET['name']) || empty($_GET['name']))
				{
					echo 'You must give a folder name in name parameter';
					break;
				}

				$this->_create_folder_action($_GET['name']);
				break;
			}

			case 'create_file':
			{
				if(! isset($_GET['name']) || empty($_GET['name']))
				{
					echo 'You must give a file name in name parameter';
					break;
				}

				$this->_create_file_action($_GET['name']);
				break;
			}

			case 'download_file':
			{
				if(! isset($_GET['id']) || empty($_GET['id']))
				{
					echo 'You must give a file id in id parameter';
					break;
				}

				$this->_download_file_action($_GET['id']);
				break;
			}

			default:
			{
				echo 'This action does not exist';
			}
		}

	}

	private function _upload_file_handle()
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

	private function _download_file_action($file_id)
	{
		$file = Cloud_file::get_by_id($file_id);

		if(! $file)
		{
			echo "Selected file does not exist";

			return;
		}

		$uploader = new File_uploader($file);

		$uploader->send();
	}

	private function _create_file_action($file_name)
	{
		$name = urldecode($file_name);

		$name = $this->security->xss_clean($name);

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

	private function _create_folder_action($folder_name)
	{
		$name = urldecode($folder_name);

		$name = $this->security->xss_clean($name);

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

	private function _object_properties_action()
	{
		if(! is_numeric($_GET['id']))
		{
			echo 'Id must be a id of object';

			return;
		}

		$item = Cloud_object::get_by_id($_GET['id']);

		if($item)
		{
			$info = $item->summary();

			$properties = array(
				'name' => $info['name'],
				'type' => ($info['type'] == 1)? 'File' : 'Folder',
				'size' => $this->_size_converter($info['size']),
				'owner' => $this->db->select('name')->where('user_id', $info['owner_id'])->get('yc_users')->row_array()['name'],
				'shared' => 'No',
				'created' => date('d.m.Y H:i', $info['created']),
				'icon-src' => 'http://yourcloud.dev/application/assets/file_type_icons_png/search.png',
				'icon-alt' => 'Icon'
			);
			
			echo json_encode($properties);
		}
		else
		{
			echo 'File does not exist';
		}
	}



	private function _generate_paths($controller_name, $args = array())
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

	private function _size_converter($bytes)
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
