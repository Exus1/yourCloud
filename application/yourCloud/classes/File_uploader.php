<?php

class File_uploader
{

	protected $file;

	/*
	$file - Cloud_file object or relative path
	*/
	function __construct($file)
	{
		$type = get_class($file);

		if($type != 'Cloud_file')
		{
			throw new Exception('file must be object of Cloud_file');
		}

		$this->file = $file;
	}

	public function send()
	{
		$this->_set_headers();

		echo $this->file->get_content();
	}

	protected function _set_headers()
	{
		header('Content-Type:application/force-download');
		header('Content-Disposition: attachment; filename="'. $this->file->name .'";');
		header('Content-Length:'. @filesize($this->file->absolute_path));

		//@readfile($file_path);

		header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");// HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");// HTTP/1.0
	}
}