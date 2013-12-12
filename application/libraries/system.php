<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * System Library - Used for general purpose functions
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 **/

class System
{
    var $userdata = array();
    var $forge_mode = FALSE;

    // Initiating. Let's get some userdata...
	function __construct(){
		$this->CI =& get_instance();

		if ($this->CI->session->userdata('signed_in')):
	        $this->userdata = $this->CI->db->join('characters', 'users.id = characters.user_id', 'left')
	                                       ->get_where('users', array('users.id' => $this->CI->session->userdata('id')))
	                                       ->row_array();
	    else:
	        $this->CI->load->library('authentication');

	        $auto_login = $this->CI->authentication->auto_login(get_cookie('session_token'));

	        if($auto_login):
	            // Relogged in automatically!
	             $this->userdata = $this->CI->db->join('characters', 'users.id = characters.user_id', 'left')
        	                                       ->get_where('users', array('users.id' => $this->CI->session->userdata('id')))
        	                                       ->row_array();
            endif;
	    endif;
	}

	public function quick_parse($template, $data = array(), $ban_check = TRUE)
	{
	    $this->CI->load->library('parser');
        $banned = FALSE;
	    $ajax_request = is_ajax();

	    if ( ! $this->CI->session->userdata('signed_in')):
	        $this->CI->parser->parse('header_signed_out', $data);
	    else:
	        $data['user'] = $this->userdata;
	        $data['scrty_token'] = substr($this->userdata['auto_login_token'], 20);
	        if( ! $ajax_request) $this->CI->load->view('header', $data);

	        if($ban_check == TRUE && $this->userdata['banned'] == 1){
	            $banned = TRUE;
                $this->CI->parser->parse('banned/ban_template', $data);
    	    }
	    endif;

	    if( ! $banned) $this->CI->parser->parse($template, $data);

	    if( ! $ajax_request) $this->CI->load->view('footer', $data);
	}

    public function parse_json($json_data = array())
    {
        $this->CI->output->set_content_type('application/json')
                         ->set_output(json_encode($json_data, JSON_NUMERIC_CHECK));
    }



	public function is_staff($user_id = 0)
	{
	    // Non-logged in fallback
	    if($user_id == 0) $user_id = (isset($this->userdata['id']) ? $this->userdata['id'] : 0);

	    if( ! isset($this->userdata['user_rank'])) return FALSE;

	    if($user_id == $this->CI->session->userdata('id') && $this->userdata['user_rank'] != "user"):
	        return TRUE;
	    else:
	        $target_userdata = $this->CI->db->get_where('users', array('users.id' => $user_id))->row_array();

	        if($target_userdata['user_rank'] != "user"):
	            return TRUE;
	        else:
	            return FALSE;
	        endif;
	    endif;
	}


	public function notificiation($message = "Oops, system error", $thumbnail = NULL, $target = "friends")
	{
	    if($target != "friends"):
	        $notifications = array(
    	        'user_id' => $target,
    	    	'thumbnail' => (is_null($thumbnail) ? 'avatars/waistup/'.$this->CI->session->userdata('id').'.gif' : $thumbnail),
    	    	'message' => $message
    	    );

    	    $this->CI->db->set('timestamp', 'NOW()', false)->insert('notifications', $notifications);
	    else:
            $my_friends = $this->CI->db->select('friends.friend_id')
    							        ->where('user_id', $this->CI->session->userdata('id'))
    							        ->where('status', 'active')
    							        ->get('friends')
    							        ->result_array();

    		foreach ($my_friends as $friend):
                $notifications = array(
        	        'user_id' => $friend,
        	    	'thumbnail' => (is_null($thumbnail) ? 'avatars/waistup/'.$this->CI->session->userdata('id').'.gif' : $thumbnail),
        	    	'message' => $message
        	    );

        	    $this->CI->db->set('timestamp', 'NOW()', false)->insert('notifications', $notifications);
    		endforeach;
	    endif;

		return $string;
	}

}


/* End of file system.php */
/* Location: ./system/application/library/system.php */