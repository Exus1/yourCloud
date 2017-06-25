<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_Controller {

	public function index()
	{
		echo 'Welcome in yourCloud API';
	}

	
	public function share_object()
	{
		if(! isset($_GET['object_id']) || empty($_GET['object_id']))
		{
			echo 'You must provide object id in object_id parameter';

			return;
		}

		if(! isset($_GET['user_id']) || empty($_GET['user_id']))
		{
			echo 'You must provide user id or name id in user_id parameter';

			return;
		}

		$object_id = $this->input->get('object_id', TRUE);
		$user_id = $this->input->get('user_id', TRUE);
		$permissions = (! isset($_GET['permissions']) || empty($_GET['permissions']))? 777 : $this->input->get('permissions', TRUE);

		$object = Cloud_object::get_by_id($object_id);

		if(! $object)
		{
			echo 'Selected object does not exits';

			return;
		}

		if($object->owner_id != $this->cloud_user->user_id)
		{
			echo 'You can not share shared objects';

			return;
		}

		$user = new Cloud_user($user_id);

		if(! $user->isset())
		{
			echo 'Selected user does not exits';

			return;
		}

		if($object->share($user->user_id, $permissions))
		{
			echo 'success';
		}
		else
		{
			echo 'error';
		}
	}

	public function delete_sharing()
	{
		if(! isset($_GET['object_id']) || empty($_GET['object_id']))
		{
			echo 'You must provide object id in object_id parameter';

			return;
		}

		if(! isset($_GET['user_id']) || empty($_GET['user_id']))
		{
			echo 'You must provide user id or name in user_id parameter';

			return;
		}

		$object_id = $this->input->get('object_id', TRUE);
		$user_id = $this->input->get('user_id', TRUE);

		$object = Cloud_object::get_by_id($object_id);

		if(! $object)
		{
			echo 'Selected object does not exits';

			return;
		}

		$user = new Cloud_user($user_id);

		if(! $user->isset())
		{
			echo 'Selected user does not exits';

			return;
		}

		if($object->delete_sharing($user->user_id))
		{
			echo 'success';
		}
		else
		{
			echo 'error';
		}

	}
	
}


