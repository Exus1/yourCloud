<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function include_css($css_file)
{
  if(!function_exists('base_url'))
  {
    get_instance()->load->helper('url');
  }

  if( ! strpos($css_file, '.css'))
  {
    $css_file .= '.css';
  }

  return base_url() . 'application/assets/css/' . $css_file;
}

function include_js($js_file)
{
  if(!function_exists('base_url'))
  {
    get_instance()->load->helper('url');
  }

  if( ! strpos($js_file, '.js'))
  {
    $js_file .= '.js';
  }

  return base_url() . 'application/assets/js/' . $js_file;
}

function include_img($img_file)
{
  if(!function_exists('base_url'))
  {
    get_instance()->load->helper('url');
  }

  return base_url() . 'application/assets/img/' . $img_file;
}

function include_file_type_icon($img_file)
{
  if(file_exists(FCPATH . 'application/assets/file_type_icons_png/' . $img_file))
  {
    return base_url() . 'application/assets/file_type_icons_png/' . $img_file;
  }
  else
  {
    return base_url() . 'application/assets/file_type_icons_png/file.png';
  }
}

function include_asset($path)
{
  if(!function_exists('base_url'))
  {
    get_instance()->load->helper('url');
  }

  return base_url() . 'application/assets/' . $path;
}

function include_font_icons()
{
  if(!function_exists('base_url'))
  {
    get_instance()->load->helper('url');
  }

  return base_url() . 'application/assets/font_icons/css/fontello.css';
}

function conver_popular_types($type)
{
  $types = array(
    'txt' => 'log',
    'js' => 'json',

  );

  if(($result = array_search($type, $types)))
    return $result;
  else
    return $type;
}

?>
