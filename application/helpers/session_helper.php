<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function session_login_check()
{
  if(!function_exists('redirect'))
  {
    get_instance()->load->helper('url');
  }

  if(! get_instance()->session->logged_in)
  {
    redirect('/login');
  }
}

?>
