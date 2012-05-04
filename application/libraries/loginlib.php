<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Login Control Library
 *  
 * The login control library is necessary to keep in order
 * users of all types and govern whether or not that they
 * have access privaledges for a page. This class also controls
 * the passwords and such of the users. (although this may be 
 * depreciated later)
 *  
 * @package eMagineCMS
 * @author Sam Duke
 * @copyright Copyright (c) 2011, Sam Duke
 * @since Version 0.1.0
 * @link https://github.com/strik3r2/eMagineCMS
 */


class Loginlib {
  
  /**
   * We are going to need a fair amount of Codeigniter
   * interaction so lets make an object holder
   */
  protected $CI;
  protected $logData;
  
  /**
   * This public contruster loads the CI object 
   * and the session library
   */
  function __construct()
  {
    $this->CI =& get_instance();
    $this->logData = array(
                           'username' => $this->CI->session->userdata('username'),
                           'user_id' => $this->CI->session->userdata('user_id'),
                           'status' => $this->CI->session->userdata('status'),
                           'logged_on' => $this->CI->session->userdata('logged_on'),
                           );
  }
  
  
  /**
   * Is Login 
   * 
   * Determine whether or not the user is logged in.
   * 
   * @access public 
   * @param none
   * @return bool logged in or not
   */
  public function isLoggedOn()
  {
    if( $this->CI->session->userdata('username') != FALSE && $this->CI->session->userdata('logged_on') == TRUE ) {
      return TRUE;
    }
    else {     
      return FALSE;
    } 
  }
  
  
  /**
   * User has access
   * 
   * Work out if you are allowed to access a page or not. 
   * 
   * @access public
   * @param string class name
   * @return bool has page access
   */
  public function hasAccess($class) {

    $list = array(
                  9 => array( 'fb', 'Images', 'users' ),
                  5 => array( 'blog', 'home' ),
                  1 => array( 'theme' ),
                  );

    $status = $this->CI->session->userdata('status');              
    $classStatus = 1;

    foreach($list as $key => $array) {
      if(in_array($class,$array)) {
        $classStatus = $key;
        break;
      }
    }
        
    if($status >= $classStatus) {
      return TRUE;
    } else {
      return FALSE;
    }
    
  } 
  
  
  /**
   * Verbosely login the user
   * 
   * This login method was written to be verbose with 
   * a counter part silent login however this implementation
   * was decided to be done in a usage rather than design
   * 
   * @access public (system only)
   * @param array user data
   * @return bool was login successful or not
   */
  public function verbose_login($data)
  {
    $this->CI->load->model('usermodel');
    $check = $this->CI->usermodel->check_user($data['username'],$data['password']);
    $status = $this->CI->usermodel->getStatus($data['username'],$data['password']);

    if($check) {
      $array = array( "username" => $data['username'],
                      "status" => $status[0]['user_status'],
                      "user_id" => $status[0]['user_id'],
                      "logged_on" => TRUE,
                     );
                     
      $this->CI->session->set_userdata($array);
      return TRUE;
    } 
    return FALSE;
  }
  
  
  /**
   * Logoff
   * 
   * Log the logged in user off
   * 
   * @access public
   * @return void
   */
  public function logoff() {
    $data = array('username' => '',
                  'status' => '',
                  'user_id' => '',
                  'logged_on' => '',
                  );
                  
    $this->CI->session->unset_userdata($data);
  
  }
  
  
  /**
   * Hash password
   * 
   * Takes a password string and runs it through a 
   * password hashing function/s 
   * 
   * @access private
   * @param string password
   * @return string hashed password
   */
  private function hashpass($pass)
  {
    return sha1($pass);
  }  
  
  function getLogData() {
    return $this->logData;
  }
   
}