<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller {

    var $default_items = array(
        // Starter items
        '13996'   => 1,
        '29762'   => 1, # Tunic (Brown)
        '18839'   => 1, # Shoes (Green)
        '22608'   => 1, # Pants (Green)

        '6571'    => 0, # Red Rustle Hair
        '6666'    => 0, # Blonde Rustle Hair
        '6685'    => 0, # Brown Rustle Hair
        '6856'    => 1, # Black Rustle Hair

        '6247'    => 0, # Red Sky Hair
        '6342'    => 0, # Blonde Sky Hair
        '6361'    => 0, # Brown Sky Hair
        '6532'    => 0, # Black Sky Hair

        '415'     => 0, # Red Flow Hair
        '2130'    => 0, # Blonde Flow Hair
        '6668723' => 0, # Brown Flow Hair
        '5560'    => 0, # Black Flow Hair

        # mouths
        '412'     => 1,
        '413'     => 0,
        '414'     => 0,
        # eyebrows
        '375'     => 1,
        '393'     => 0,
        # eyes
        '356'     => 1,
        '353'     => 0,
        '374'     => 0,
        '411'     => 0,
        # bases
        '333'     => 1,
        '334'     => 0,
        '335'     => 0,
        '336'     => 0,
        '337'     => 0,

        # Starter staff!
        '21936'   => 0,
    );


    function __construct(){
        parent::__construct();
        $this->load->library(array('authentication', 'form_validation'));
    }

    public function signup()
	{
        $this->load->helper("form");

        $this->form_validation->set_error_delimiters('', '');

	    $this->form_validation->set_rules('username', 'Username', 'required|max_length[32]|xss_clean|callback_username_check|callback_alnum_verification');

        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');
        $this->form_validation->set_rules('key', 'Beta Key', 'required|alpha_numeric|callback_beta_key_check');

	    if ($this->form_validation->run() == FALSE):
            $this->output->set_content_type('application/json')
	                     ->set_output(json_encode(array('errors' => validation_errors()), JSON_NUMERIC_CHECK));
        else:
            // delete one of their invites
            $this->load->library('encrypt');
            $this->load->helper('string');

        	$this->authentication->create_user(array(
                'username'         => $this->input->post('username'),
                'password'         => $this->input->post('password'),
                'email'            => $this->input->post('email'),
                'beta_key'         => random_string('alnum', 6),
                'invited_by'       => $this->input->post('key'),
                'auto_login_token' => random_string('alnum', 18).$this->encrypt->encode($this->input->post('username'))
    	    ));

    	    $invite = $this->db->select('users.gold, users.id, users.beta_key, users.invites_left, characters.exp')
    	                       ->from('users')
    	                       ->where('invites_left >', 0)
    	                       ->where('users.beta_key', $this->input->post('key'))
    	                       ->join('characters', 'characters.user_id = users.id')
    	                       ->get();

    	    $gold_bonus = 25;
    	    $exp_bonus = 0;

    	    if($invite->num_rows > 0):
    	        $this->load->model('user_engine');
    	        switch($invite->row()->invites_left){
        	        case 1:
            	        $exp_bonus = 50;
        	        break;
        	        case 3:
            	        $exp_bonus = 100;
                	    $gold_bonus = 75;
        	        break;
        	        case 5:
        	            $exp_bonus = 250;
        	        break;
        	        case 7:
    	                $exp_bonus = 300;
                	    $gold_bonus = 125;
        	        break;
        	        case 10:
                        $exp_bonus = 500;
        	        break;
        	    }

        	    $this->db->where('beta_key', $this->input->post('key'))->update('users', array('gold' => $invite->row()->gold+$gold_bonus, 'invites_left' => ($invite->row()->invites_left-1)));

                $this->db->where('user_id', $invite->row()->id)->update('characters', array('exp' => $invite->row()->exp+$exp_bonus));
                $this->user_engine->check_for_levelup($invite->row()->id);
            endif;

    	    $this->db->insert('characters', array(
                'user_id'        => $this->session->userdata('id'),
                'class'          => 'adventurer',
                'hp'             => 25,
                'max_hp'         => 25,
                'exp'            => 5,
                'next_level_exp' => 100,
                'level'          => 1,
                'max_weight'     => 15,
                'attack'         => 1,
                'defense'        => 1,
                'agility'        => 1,
                'energy'         => 6,
                'max_energy'     => 6,
    	    ));

    	    $character_id = $this->db->insert_id();

    	    $this->db->insert('character_skills', array(
                'skill_id'     => 1,
                'character_id' => $this->db->insert_id(),
                'skill_level'  => 1,
                'min_damage'   => 1,
                'max_damage'   => 4
    	    ));

    	    // Email the user!

    	    // Give the user a default avatar
    	    copy('images/avatars/default/male_default.gif', 'images/avatars/'.$this->session->userdata('id').'.gif');
            copy('images/avatars/default/flip/male_default.gif', 'images/avatars/flip/'.$this->session->userdata('id').'.gif');
            copy('images/avatars/default/thumbnails/male_default.gif', 'images/avatars/thumbnails/'.$this->session->userdata('id').'.gif');

            // Give the user a default class
            foreach ($this->default_items as $item => $default):
               $this->db->insert('character_items', array(
                   'item_id' => $item,
                   'character_id' => $character_id,
                   'equipped' => $default
               ));
    	    endforeach;

    	    if ( ! is_ajax()):
                redirect('home/index/new_user');
    	    else:
    	        $this->output->set_content_type('application/json')
    	                     ->set_output(json_encode(array('success' => 1), JSON_NUMERIC_CHECK));
    	    endif;
        endif;
	}

	public function signin()
	{
	    $this->form_validation->set_rules('username', 'Username', 'required|max_length[32]|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'required');

	    $total_attempts = apc_fetch('login_attempts'.$this->input->ip_address());

	    if(apc_fetch('login_attempts'.$this->input->ip_address())):
	    	if($total_attempts > 10):
		    	show_error('You\'ve exceeded your max login attempts. Please try again in 10 minutes!');
	    	else:
		    	apc_inc('login_attempts'.$this->input->ip_address());
	    	endif;
	    else:
	    	apc_store('login_attempts'.$this->input->ip_address(), 1, 720);
	    endif;

	    if ($this->form_validation->run() == FALSE):
	        $this->output->set_content_type('application/json')
	                     ->set_output(json_encode(array('errors' => validation_errors()), JSON_NUMERIC_CHECK));
        else:
        	if($this->authentication->signin($this->input->post('username'), $this->input->post('password'))):
    	        redirect('home/index');
        	else:
        	    $this->session->set_flashdata('errors', 'The username and password did not match');
	            redirect('home/index');
        	endif;
        endif;
	}

	public function signout()
	{
	    $this->authentication->signout();
        redirect('home?logged_out=1');
	}

	function username_check($str)
	{
        // Uh oh, incoming flurry of bad words...
	    $baned_words = array('FUCK', 'GAY', 'CUNT', 'FUCKER', 'BITCH', 'ASS', 'PORN', 'SEX', 'DRUGS', 'PEDO', 'PEDOPHILE', 'BASTARD', 'PUSSY', 'SHIT', 'CLIT', 'SHANK', 'PENIS', 'COCK', 'PRICK', 'CHAV', 'TWAT', 'TITS', 'TIT', 'MOTHER FUCKER', 'NIGGERS', 'SPERM', 'CHINK', 'BUKKAKE', 'JIZZ', 'JIZ', 'HORNY', 'BONDER', 'FUCKED', 'BEANER', 'BANCOCK', 'DILDO', 'DYKE', 'FAG', 'WANKER', 'GOD DAMN', 'GOD', 'P0RN', 'FUCK3R', 'FUCKING', 'WANK', 'WANKER', 'TWAT' );

		$str = trim($str);
		if(in_array(strtoupper($str), $baned_words)):
    		$this->form_validation->set_message('username_check', 'Your username has words that aren\'t allowed');
			return FALSE;
		endif;

		$query = $this->db->limit(1)->where('username', $str)->get('users');

		if($query->num_rows() > 0):
    		$this->form_validation->set_message('username_check', 'That Username is has already been taken');
			return FALSE;
		else:
			return TRUE;
		endif;
	}

	function email_check($str)
	{
		$query = $this->db->limit(1)->where('email', $str)->get('users');

		if($query->num_rows() > 0):
		    $this->form_validation->set_message('email_check', 'That Email is already in use.');
			return FALSE;
		else:
			return TRUE;
		endif;

	}

	public function beta_key_check($key)
	{
        $invite = $this->db->select('*')->from('users')->where('invites_left >', 0)->where('beta_key', $key)->get();

        if($invite->num_rows() > 0):
            return TRUE;
        else:
            $this->form_validation->set_message('beta_key_check', 'That Beta code is invalid');
            return FALSE;
        endif;
	}

	function signup_verify()
	{
		$str = $this->input->post('type');
		$val = $this->input->post('value');

		if($str == "email"):
			// Email regular expression check
			if( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $val)):
				die(json_encode(array('response' => 'error')));
			endif;

			$check = $this->email_check($val);
		elseif($str == "signup_beta_key"):
			$check = $this->beta_key_check($val);
		else:
			// Alphanumeric with space regular expression check
			if( ! preg_match("/^([a-z0-9\s])+$/i", $val)):
				die(json_encode(array('response' => 'error')));
			endif;

			$check = $this->username_check($val);
		endif;

		// Response format is json. It's the new black.
		if($check == TRUE):
		    $this->output->set_content_type('application/json')
		                 ->set_output(json_encode(array('response' => 'success'), JSON_NUMERIC_CHECK));
		else:
    	    $this->output->set_content_type('application/json')
    	                 ->set_output(json_encode(array('response' => 'error'), JSON_NUMERIC_CHECK));
		endif;
	}

	public function create_character()
	{
	    $character_templates = array(
	       'adventurer' => array(
               'class'          => 'adventurer',
               'hp'             => 9,
               'max_hp'         => 9,
               'energy'         => 15,
               'max_energy'     => 15,
               'exp'            => 5,
               'next_level_exp' => 100,
               'level'          => 1,
               'max_weight'     => 40,
               'attack'         => 4,
               'defense'        => 3,
               'agility'        => 1,
               'starter_items'  => array(24312) // item ID's
	       ),
	       'archer' => array(
               'class'          => 'archer',
               'hp'             => 7,
               'max_hp'         => 7,
               'energy'         => 15,
               'max_energy'     => 15,
               'exp'            => 5,
               'next_level_exp' => 100,
               'level'          => 1,
               'max_weight'     => 30,
               'attack'         => 3,
               'defense'        => 2,
               'agility'        => 5,
               'starter_items'  => array(15658) // item ID's
	       ),
	       'wizard' => array(
               'class'          => 'wizard',
               'hp'             => 5,
               'max_hp'         => 5,
               'energy'         => 15,
               'max_energy'     => 15,
               'exp'            => 5,
               'next_level_exp' => 100,
               'level'          => 1,
               'max_weight'     => 25,
               'attack'         => 5,
               'defense'        => 4,
               'agility'        => 5,
               'starter_items'  => array(21936) // item ID's
	       )
	    );

        $default_items = array(
            // Starter items
            '13996'   => 1,
            '29762'   => 1, # Tunic (Brown)
            '18839'   => 1, # Shoes (Green)
            '22608'   => 1, # Pants (Green)

            '6571'    => 0, # Red Rustle Hair
            '6666'    => 0, # Blonde Rustle Hair
            '6685'    => 0, # Brown Rustle Hair
            '6856'    => 1, # Black Rustle Hair

            '6247'    => 0, # Red Sky Hair
            '6342'    => 0, # Blonde Sky Hair
            '6361'    => 0, # Brown Sky Hair
            '6532'    => 0, # Black Sky Hair

            '415'     => 0, # Red Flow Hair
            '2130'    => 0, # Blonde Flow Hair
            '6668723' => 0, # Brown Flow Hair
            '5560'    => 0, # Black Flow Hair

            # mouths
            '412'     => 1,
            '413'     => 0,
            '414'     => 0,
            # eyebrows
            '375'     => 1,
            '393'     => 0,
            # eyes
            '356'     => 1,
            '353'     => 0,
            '374'     => 0,
            '411'     => 0,
            # bases
            '333'     => 1,
            '334'     => 0,
            '335'     => 0,
            '336'     => 0,
            '337'     => 0,
        );

        if(strtolower($this->input->post('gender')) == "female"):
            $default_items['6856'] = 0;
            $default_items['5560'] = 1;
        endif;

        if(strtolower($this->input->post('gender')) == "female"):
            copy('images/avatars/default/female_default.gif', 'images/avatars/'.$this->session->userdata('id').'.gif');
            copy('images/avatars/default/flip/female_default.gif', 'images/avatars/flip/'.$this->session->userdata('id').'.gif');
            copy('images/avatars/default/thumbnails/female_default.gif', 'images/avatars/thumbnails/'.$this->session->userdata('id').'.gif');
        else:
            copy('images/avatars/default/male_default.gif', 'images/avatars/'.$this->session->userdata('id').'.gif');
            copy('images/avatars/default/flip/male_default.gif', 'images/avatars/flip/'.$this->session->userdata('id').'.gif');
            copy('images/avatars/default/thumbnails/male_default.gif', 'images/avatars/thumbnails/'.$this->session->userdata('id').'.gif');
        endif;

	    $class_chosen = $this->input->post('character_class');

	    $character_templates[$class_chosen]['gender'] = $this->input->post('gender');
	    $starter_items = $character_templates[$class_chosen]['starter_items'];

	    foreach ($default_items as $item => $default):
           $this->db->insert('character_items', array(
               'item_id' => $item,
               'character_id' => $this->system->userdata['character_id'],
               'equipped' => $default
           ));
	    endforeach;

	    $this->db->update('characters', array('class' => 'wizard',
            'hp'             => 20,
            'max_hp'         => 20,
            'energy'         => 6,
            'max_energy'     => 6,
            'exp'            => 5,
            'next_level_exp' => 100,
            'level'          => 1,
            'max_weight'     => 15,
            'attack'         => 1,
            'defense'        => 1,
            'agility'        => 1,
        ), array('user_id' => $this->session->userdata('id')));

	    $this->output->set_content_type('application/json')
	                 ->set_output(json_encode(array('success' => 1, 'gender' => strtolower($this->input->post('gender'))), JSON_NUMERIC_CHECK));
	}

	public function alnum_verification($username = "")
	{
        $str_valid = preg_replace('/[^a-zA-Z0-9\s]/', '', htmlentities(strip_tags(mysql_real_escape_string($username))));

        if($str_valid):
            return TRUE;
        else:
    	    $this->form_validation->set_message('alnum_verification', 'Your %s can only be made of letters, numbers, and spaces');
            return FALSE;
        endif;
	}

	public function recover_password()
	{
	    $success = "";
	    $error = "";

        if($_SERVER['REQUEST_METHOD'] == "POST"):
            $user = $this->db->select('*')->where(array('email' => $this->input->post('email')))->get('users');

            if($user->num_rows() > 0):
            	$user_data = $user->row_array();

            	if($user_data['username'] == $this->input->post('username')):
            	    $this->load->helper('string');
            	    $key = random_string('unique');
            	    $this->db->set('created_at', 'NOW()', 'false')->insert('recover_password_keys', array(
                        'username' => $this->input->post('username'),
                        'email'    => $this->input->post('email'),
                        'key'      => $key
            	    ));

  	        	    // Email the user!
              	    $this->load->library('email');

                    $email_configurations = array(
                      'protocol'  => 'smtp',
                      'smtp_host' => 'smtp.mailgun.org',
                      'smtp_user' => 'postmaster@manahaven.mailgun.org',
                      'smtp_pass' => '',
                      'mailtype'  => 'html'
                    );

                    $this->email->initialize($email_configurations);

                    $title = 'ManaHaven: Recover your password';
                    $message = 'Hi '.$this->input->post('username')."!\nSorry to hear you've been having password troubles. Here's a link to help you reset to a *hopefully* more secure, memorizable and fitting for your account.\n\n<a href=\"http://manahaven.com/auth/password_key/".$key."?username=".$this->input->post('username')."\">Reset your password</a>";

                    $this->email->from('tyler@manahaven.com', 'ManaHaven');
                    $this->email->reply_to('tyler@manahaven.com', 'Tyler Diaz');

                    $this->email->to($this->input->post('email'));
                    $this->email->subject($title);
                    $this->email->message($message);
                    $this->email->set_alt_message('Hi '.$this->input->post('username')."!\nSorry to hear you've been having password troubles. Here's a link to help you reset to a *hopefully* more secure, memorizable and fitting for your account.\n\n
                    http://manahaven.com/auth/password_key/".$key."?username=".$this->input->post('username'));

                    $this->email->send();

                    $success = 'An email has been sent to your inbox with the instructions on recovering your account.';
                else:
                    $error = 'That username did not match your email, make sure you typed it correctly please!';
            	endif;
            else:
            	// user not found...
                $error = 'That email combo did not match any users on our side, are you sure you signed up with it? If so, email me at tyler@manahaven.com and I\'ll help you get this sorted out.';
            endif;
        endif;

	    $view_data = array(
           'page_title' => 'Recover your password',
           'page_body'  => 'home',
           'success'    => $success,
           'error'      => $error
	    );

	    $this->system->quick_parse('auth/recover_password', $view_data);
	}

	public function password_key($key = "")
	{
	    $key_check = $this->db->get_where('recover_password_keys', array('key' => $key, 'username' => $this->input->get('username')));
	    $success = "";
	    $error = "";

        if($key_check->num_rows() == 0) die('Key not found');

	    if($_SERVER['REQUEST_METHOD'] == "POST"):
	        if(strlen($this->input->post('password')) > 5):
	            if($this->input->post('password') == $this->input->post('password_confirm')):

                    $this->db->where('key', $key)->delete('recover_password_keys');

	                $this->load->library('authentication');
                    $this->db->where('username', $this->input->get('username'))->update('users', array('password' => $this->authentication->hash_password($this->input->post('password'))));

	                $success = "Your password has been changed! Go ahead, sign in with your new password. :D";
	            else:
	                $error = "Passwords must match!";
	            endif;
	        else:
        	    $error = "Password must be longer than 5 letters";
            endif;
	    endif;

	    $view_data = array(
           'page_title' => 'Recover your password',
           'page_body'  => 'home',
           'success'    => $success,
           'error'      => $error,
           'key'        => $key
	    );

	    $this->system->quick_parse('auth/new_password', $view_data);
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */