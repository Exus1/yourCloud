<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_Controller {

	public function index()
	{
		echo 'APi';
	}

	public function language()
	{
		
	}

	private function _convert_file_weight($weight)
	{
		$counter = 0;

		while($weight > 1024)
		{
			$counter++;

			$weight /= 1024;
		}

		$units = array(
			'B',
			'KB',
			'MB',
			'GB',
			'TB',
			'PB',
			'EB',
			'ZB',
			'YB'
		);

		return round($weight, 2) . ' ' . $units[$counter];
	}

	
}


