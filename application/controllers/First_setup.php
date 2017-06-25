<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
  First setup controller
  ======================

  Steps:
    1. Databse configuration
    2. Storage configuration
    3. Creating admin account
*/



class First_setup extends MY_Controller {

  function __construct()
  {
    parent::__construct();

    $this->load->library('form_validation');
  }

  function index()
  {
    redirect('first_setup/step_1');
  }

  protected function _load_view($step, $error_msg = '')
  {
    $this->load->view('first_setup/header');
    $this->load->view('first_setup/step_'. $step, array('error_msg' => $error_msg));
    $this->load->view('first_setup/footer');
  }


  //
  // Step 1
  // ======
  //
  public function step_1()
  {
    //
    // Validation rules
    //
    $this->form_validation->set_rules(array(
      array(
        'field' => 'address',
        'label' => 'Ip address',
        'rules' => 'required|valid_ip'
      ),
      array(
        'field' => 'name',
        'label' => 'Database name',
        'rules' => 'required'
      ),
      array(
        'field' => 'user',
        'label' => 'Database user',
        'rules' => 'required'
      ),
      array(
        'field' => 'password',
        'label' => 'Database password',
        'rules' => ''
      )
    ));

    if($this->form_validation->run() == FALSE)
    {
      $this->_load_view(1);

      return;
    }

    $address = $this->input->post('address', TRUE);
    $username =  $this->input->post('user', TRUE);
    $password = $this->input->post('password', TRUE);
    $table_name = $this->input->post('name', TRUE);

    //
    // Checking MySQL connection
    //
    $mysqli = new mysqli($address, $username, $password, $table_name);

    if($mysqli->connect_errno)
    {
      $this->_load_view(1, $mysqli->connect_error);
    }

    //
    // Creating required tables
    //
    $sql = file_get_contents(APPPATH. 'config'. DIRECTORY_SEPARATOR. 'first_setup_tables.sql');

    $comment_patterns = array('/\/\*.*(\n)*.*(\*\/)?/','/\s*--.*\n/','/\s*#.*\n/');
    $sql = preg_replace($comment_patterns, "\n", $sql);
    $statements = explode(";\n", $sql);
    $statements = preg_replace("/\s/", ' ', $statements);

    foreach($statements as $sql)
    {
      $sql = trim($sql);

      if(empty($sql))
      {
        continue;
      }

      if(! $mysqli->query($sql))
      {
        $this->_load_view(1, $mysqli->error);
        
        return;
      }
    }

    //
    // Creating config.php file in APPPATH/config
    //
    $config_file_path = APPPATH. 'config'. DIRECTORY_SEPARATOR. 'database.php';
    copy(APPPATH. 'config'. DIRECTORY_SEPARATOR. 'database_clear.php', $config_file_path);

    $config_file = file_get_contents($config_file_path);

    $config_file = str_replace('hostname_value', $address, $config_file);
    $config_file = str_replace('username_value', $username, $config_file);
    $config_file = str_replace('password_value', $password, $config_file);
    $config_file = str_replace('database_value', $table_name, $config_file);

    if(! file_put_contents($config_file_path, $config_file))
    {
      $this->_load_view(1, 'Can not save config.php in '. APPPATH . '/config');

      return;
    }

    //
    // Creating autoload.php file in APPPATH/config
    //
    $autoload_file_path = APPPATH. 'config'. DIRECTORY_SEPARATOR. 'autoload.php';

    copy(APPPATH. 'config'. DIRECTORY_SEPARATOR. 'autoload_clear.php', $autoload_file_path);

    $config_file = file_get_contents(APPPATH. 'config'. DIRECTORY_SEPARATOR. 'autoload.php');

    $config_file = str_replace('libraries_value', "array('session', 'db_config', 'cloud_user', 'database')", $config_file);

    if(! file_put_contents($autoload_file_path, $config_file))
    {
      $this->_load_view(1, 'Can not save autoload.php in '. APPPATH . '/config');

      return;
    }

    //
    // Redirect to next step
    //
    redirect('first_setup/step_2');
  }

  //
  // Step 2
  // ======
  //
  public function step_2()
  {
    //
    // Form validation rules
    //
    $this->form_validation->set_rules(array(
      array(
        'field' => 'storage_path',
        'label' => 'Storage path',
        'rules' => 'required'
      ),
    ));

    if($this->form_validation->run() == FALSE)
    {
      $this->_load_view(2);

      return;
    }

    //
    // Checking if selected folder is exist
    //
    $path = Path_converter::normalize($this->input->post('storage_path', TRUE));

    if(! is_dir($path))
    {
      $this->_load_view(2, 'Selected folder does not exist');

      return;
    }

    if(! is_writable($path))
    {
      $this->_load_view(2, 'Selected folder is not writable');

      return;
    }

    //
    // Adding selected folder to database configuration
    //
    $this->db_config->add('storage_path', $path);

    if(! $this->db_config->get('storage_path'))
    {
      $this->_load_view(2, 'Can not write to databse');

      return;
    }

    //
    // Adding stroage folder to database file index
    //
    $filecache_storage = array(
      'pid' => '0',
      'owner_id' => '0',
      'name' => substr($path, strrpos($path, DIRECTORY_SEPARATOR)+1),
      'type' => '2',
      'absolute_path' => $path,
      'absolute_path_hash' => hash('md5', $path),
      'relative_path' => '',
      'relative_path_hash' => hash('md5', ''),
      'SHA1' => sha1('')
    );

    if(! $this->db->insert('yc_filecache', $filecache_storage))
    {
      $this->_load_view(2, 'Can not write to databse filecache');

      return;
    }

    //
    // Setting storage folder id to 0
    //
    $this->db->where('id', '1')->set('id', '0')->update('yc_filecache');

    //
    // Redirect to next step
    //
    redirect('first_setup/step_3');
  }

  //
  // Step 3
  // ======
  //
  public function step_3()
  {
    //
    // Form validaton rules
    //
    $this->form_validation->set_rules(array(
      array(
        'field' => 'login',
        'label' => 'Login',
        'rules' => 'required|min_length[3]'
      ),
      array(
        'field' => 'e_mail',
        'label' => 'E-mail',
        'rules' => 'required|valid_email'
      ),
      array(
        'field' => 'password',
        'label' => 'Password',
        'rules' => 'required'
      ),

    ));

    if($this->form_validation->run() == FALSE)
    {
      $this->_load_view(3);

      return;
    }

    //
    // Creating user
    //
    $login = $this->input->post('login', TRUE);   
    $password = password_hash($this->input->post('password', TRUE), PASSWORD_DEFAULT);
    $email = $this->input->post('e_mail', TRUE);

    $data = array(
      'name' => $login,
      'password' => $password,
      'email' => $email
    );

    // Checking if that user already exist
    $query = $this->db->where('name', $login)->get('yc_users')->result();

    if($query)
    {
      $this->_load_view(3, 'User with this name already exist');

      return;
    }

    // Checking if user with this email already exist
    $query = $this->db->where('email', $email)->get('yc_users')->result();

    if($query)
    {
      $this->_load_view(3, 'User with this E-mail already exist');

      return;
    }

    if(! $this->db->insert('yc_users', $data))
    {
      $this->_load_view(3, 'Can not add user to database');

      return;
    }

    $user = new Cloud_user($login);

    //
    // Creating user storage folder
    //
    $storage_path = $this->db_config->get('storage_path'). DIRECTORY_SEPARATOR. $user->user_id;

    if(! mkdir($storage_path))
    {
      $this->_load_view(3, 'Can not create storage folder');

      $this->db->delete('yc_users', array('user_id' => $user->user_id));

      return;
    }

    $storage_rel_path = Path_converter::to_relative($storage_path);

    Cloud_object::add($storage_rel_path);

    //
    // Redirect to login page
    //
    redirect('/login');
  }

}
