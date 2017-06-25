<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
	Folder controller
	=================

	This controller is used to managing and showing selected folder. 
	All subsequent arguments passed to controller represent relative (Relative to the directory of the logged user) path to the folder.
*/


class Shared extends Folder_controller {
	protected $partner;

	//
	// Get all objects list action handle
	//
	public function get_all_objects_handle()
	{	
		//die($this->partner->user_id);
		// User is in main sharing folder
		if($this->storage->owner_id == $this->cloud_user->user_id)
		{
			// Show partners as fake folders

			$this->db->where('partner_id', $this->cloud_user->user_id);
			$this->db->select('owner_id')->distinct();
			$result = $this->db->get('yc_shared_objects')->result_array();

			$folder_properties = array(
				'id' => '',
				'pid' => '',
				'owner_id' => '',
				'name' => '',
				'type' => '2'
			);

			foreach ($result as  $parnter) 
			{
				$partner = new Cloud_user($parnter['owner_id']);
				$folder_properties['name'] = $partner->name;
				$folder_properties['id'] = $partner->user_id;

				$partners_as_folders['folders'][$partner->name] = (new Cloud_folder($folder_properties))->summary();
			}

			echo json_encode($partners_as_folders);

			return;
		}

		// If no shared folder is selected
		if($this->storage->pid == '0')
		{
			// Show shared objects
			$partner_id = $this->partner->user_id;

			echo json_encode($this->cloud_user->get_shared_objects(TRUE, $this->partner));

			return;
		}

		// If shared foler is selected
		echo json_encode($this->storage->get_all_objects(TRUE));

		// Show shared objects from user. $this->paths is 
		//echo json_encode($this->cloud_user->get_shared_objects(TRUE));
	}

	//
	// $this->paths generator
	//
	protected function _generate_paths($controller_name, $args = array())
	{
		$realitve_to_folder = '';

		if($controller_name != 'index')
		{
			$this->partner = new Cloud_user($controller_name);

			if(! $this->partner)
			{
				show_404();
			}

			//$this->paths['relative_to_user'] .= urldecode($controller_name);
		}
		else
		{
			$this->partner = $this->cloud_user;
		}

		//Relative
		$this->paths['relative'] = $this->partner->user_id;

		if(! empty($args))
		{
			$shared_folders = $this->cloud_user->get_shared_objects(TRUE, $this->partner)['folders'];

			// Search selected folder in shared folders
			foreach($shared_folders as $folder)
			{
				if($folder['name'] == $args[0])
				{
					$this->paths['relative'] = $folder['relative_path'];
					unset($args[0]);
					break;
				}
			}

			foreach ($args as $folder_name) 
			{
				$realitve_to_folder .= urldecode($folder_name). DIRECTORY_SEPARATOR;
			}
		}

		$this->paths['relative'] .= DIRECTORY_SEPARATOR. $realitve_to_folder;

		$this->paths['relative'] = Path_converter::normalize($this->paths['relative']);

		// Base
		$this->paths['base'] = Path_converter::normalize($this->db_config->get('storage_path'));

		// User Base
		$this->paths['user_base'] = $this->paths['base']. DIRECTORY_SEPARATOR. $this->session->user_id;

		// Absolute
		$this->paths['absolute'] = $this->paths['base']. DIRECTORY_SEPARATOR. $this->paths['relative'];

		$this->paths['relative_to_user'] = substr($this->paths['relative'], strpos($this->paths['relative'], DIRECTORY_SEPARATOR)+1);


	}
}
