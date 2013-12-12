<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Authentication Library - Manages sesions & accounts
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 **/

require('extentions/passwordhash.php');

class Authentication
{
	var $CI;
	var $user_table = 'users';

	function __construct(){
		$this->CI =& get_instance();
	}

	function signin($username_or_email = '', $user_pass = '')
	{
		if($username_or_email == '' OR $user_pass == '') return false;
		if($this->CI->session->userdata('username') == $username_or_email) return true;

		if(preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $username_or_email)):
		    $query = $this->CI->db->where('email', $username_or_email)->get($this->user_table);
		else:
		    $query = $this->CI->db->where('username', $username_or_email)->get($this->user_table);
		endif;

		if ($query->num_rows() > 0):
			$user_data = $query->row_array();

			if( ! $this->verify_password($user_pass, $user_data['id'])) return false;

			$this->CI->session->sess_destroy();
			$this->CI->session->sess_create();

            $this->CI->db->set('last_login', 'NOW()', false)
                         ->where('id', $user_data['id'])
                         ->update($this->user_table);

			$session_data = array(
                'username'  => $user_data['username'],
                'id'        => $user_data['id'],
                'user_rank' => $user_data['user_rank'],
                'signed_in' => true
			);

            $this->CI->load->library('encrypt');
            $this->CI->load->helper('string');

            $new_token = random_string('alnum', 18).$this->CI->encrypt->encode($username_or_email);

            $this->CI->db->where('id', $user_data['id'])->update('users', array('auto_login_token' => $new_token));
            $this->CI->db->where('user_id', $user_data['id'])->delete('user_activity');
            $this->CI->db->insert('user_activity', array('user_id' =>  $user_data['id'], 'last_activity' => time()));

            set_cookie(array(
                'name'   => 'session_token',
                'value'  => $new_token,
                'expire' => '605500'
            ));

			$this->CI->session->set_userdata($session_data);
			return true;
		else:
			return false;
		endif;
	}

	function signout(){
        $this->CI->load->helper('string');

	    $this->CI->db->where('id', $this->CI->session->userdata('id'))->update('users', array('auto_login_token' => 'LOGGED_OUT_'.random_string('alnum', 24)));

	    delete_cookie("session_token");
		$this->CI->session->sess_destroy();
	}

	function verify_password($password, $user_id){
	    $query = $this->CI->db->where(array('id' => $user_id))->get($this->user_table);

		if ($query->num_rows() > 0):
			$user_data = $query->row_array();
			$hasher = new PasswordHash();
			if($hasher->check_password($password, $user_data['password'])):
			    return TRUE;
			else:
			    return FALSE;
		    endif;
		else:
		    return FALSE;
		endif;
	}


	public function hash_password($password = '')
	{
		$hasher = new PasswordHash();
	    return $hasher->hash_password($password);
	}



	function create_user($userdata = array())
	{
	    //Hash user_pass using phpass
		$hasher = new PasswordHash();

        $text_password = $userdata['password'];
		$userdata['password'] = $hasher->hash_password($userdata['password']);
		$userdata['last_activity'] = time();
		$userdata['ip'] = $this->CI->input->ip_address();

        set_cookie(array(
            'name'   => 'session_token',
            'value'  => $userdata['auto_login_token'],
            'expire' => '605500'
        ));

		$this->CI->db->set('register_date', 'NOW()', false)
		             ->set('last_login', 'NOW()', false)
		             ->insert('users', $userdata);

        $this->signin($userdata['username'], $text_password);

		return $this->CI->db->insert_id();
	}


	public function auto_login($key = '')
	{
	    if(strlen($key) < 24 || ctype_alnum($key) == FALSE) return FALSE;

	    $user = $this->CI->db->get_where('users', array('auto_login_token' => $key));

	    if($user->num_rows() > 0){
	        $user_data = $user->row_array();
	        $this->CI->session->sess_destroy();
			$this->CI->session->sess_create();

            $this->CI->db->set('last_login', 'NOW()', false)
                         ->where('id', $user_data['id'])
                         ->update($this->user_table);

			$session_data = array(
                'username'  => $user_data['username'],
                'id'        => $user_data['id'],
                'user_rank' => $user_data['user_rank'],
                'signed_in' => true
			);

            $this->CI->load->helper('string');

            $new_token = random_string('alnum', 32);

            $this->CI->db->where('id', $user_data['id'])->update('users', array('last_activity' => time(), 'auto_login_token' => $new_token));

            set_cookie(array(
                'name'   => 'session_token',
                'value'  => $new_token,
                'expire' => 2422000 // One month in seconds
            ));

			$this->CI->session->set_userdata($session_data);

			return TRUE;
	    } else {
	        return FALSE;
	    }
	}

}
?>
