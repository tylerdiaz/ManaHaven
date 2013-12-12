<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Friends
 *
 * @author(s) 	Tyler Diaz
 * @version 	1.0
 **/

class Friends extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

        if( ! $this->session->userdata('id')) redirect(site_url());
    }

    public function index()
    {
        $this->load->model('friend_engine');

        $friends = $this->friend_engine->get_friends($this->session->userdata('id'), 42);
        $fb_friends = array('friend' => array(), 'invite' => array());

        if ($this->system->userdata['facebook_id'] > 0):
            $fb_friends = $this->db->query("SELECT *
                                            FROM `facebook_friends`
                                            WHERE user_id = ".$this->session->userdata('id')."
                                            AND hide = 0
                                            ORDER BY RAND()
                                            LIMIT 5")->result_array();
        endif;

        $friend_requests = $this->db->select('username, friends.id, users.id as user_id')
		                       ->where('friend_id', $this->session->userdata('id'))
							   ->where('status', 'pending')
							   ->join('users', 'friends.user_id = users.id')
							   ->limit(12)
							   ->get('friends')
							   ->result_array();

		if($friend_requests > 0 && $this->system->userdata['notifications'] > 0):
		    $this->db->where('notification_to_user_id', $this->system->userdata['id'])
	                 ->where('notification_active', 1)
	                 ->where('notification_type', 'friend_request')
	                 ->update('user_notifications', array('notification_active' => 0));

            $total_notifications = $this->db->affected_rows();
            $this->system->userdata['notifications'] -= $total_notifications;

            $this->db->where('id', $this->system->userdata['id'])->update('users', array('notifications' => ($this->system->userdata['notifications'])));
		endif;

		$view_data = array(
			'page_title' => 'My Friends',
			'page_body' => 'friends',
			'friend_requests' => $friend_requests,
			'friends' => $friends,
			'facebook_friends' => $fb_friends,
		);

        $this->system->quick_parse('friends/home', $view_data);
    }

    public function hide_fb_friend($fb_id = 0)
    {
        $hide_data = array('hide' => 1, 'user_id' => $this->session->userdata('id'));
        $this->db->where('fb_id', $fb_id)
                 ->update('facebook_friends', $hide_data);

        $new_friend = $this->db->query("SELECT *
                                     FROM `facebook_friends`
                                     WHERE user_id = ".$this->session->userdata('id')."
                                     AND hide = 0
                                     ORDER BY RAND()
                                     LIMIT 1")->row_array();

        echo json_encode($new_friend);
    }

    public function invite()
    {
		$leaderboards = $this->db->select('username, id, invites_left')->from('users')->order_by('invites_left', 'ASC')->limit(8)->get()->result_array();

        $view_data = array(
            'page_title' => 'Invite your friends',
            'page_body' => 'friends',
            'leaderboards' => $leaderboards
        );

        $this->system->quick_parse('friends/invite', $view_data);
    }

    public function facebook()
    {
        $cookie = $this->get_facebook_cookie("credentials_hidden", "credentials_hidden");

        $friends = json_decode(file_get_contents('https://graph.facebook.com/me/friends?access_token='.$cookie['access_token']));

        $me = json_decode(file_get_contents('https://graph.facebook.com/me?access_token='.$cookie['access_token']));

        $facebook_friends = array('friend' => array());
         foreach($friends->data as $fb_friend):
            if(count($facebook_friends['friend']) <= 50):
                $user = $this->db->select('*')
                                 ->where(array('facebook_id' => $fb_friend->id))
                                 ->get('users');

                $fb_friend_cache[] = array(
                    'user_id'      => $this->session->userdata('id'),
                    'name'         => $fb_friend->name,
                    'fb_id'        => $fb_friend->id,
                    'local_user'   => ($user->num_rows > 0 ? 1 : 0),
                    'invited'      => 0,
                    'hide'         => 0,
                    'request_sent' => 0
                );
            endif;
        endforeach;

        $this->db->delete('facebook_friends', array('user_id' => $this->session->userdata('id')));
        foreach ($fb_friend_cache as $fb_friend):
            $this->db->insert('facebook_friends', $fb_friend);
        endforeach;

        $facebook_data = array(
            'facebook_id'   => $me->id,
            'facebook_name' => $me->name
        );

        $this->db->where('id', $this->session->userdata('id'))->update('users', $facebook_data);

        echo json_encode(array('success'));
    }

    public function get_facebook_cookie($app_id, $app_secret)
    {
        $args = array();
        parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
        ksort($args);
        $payload = '';

        foreach ($args as $key => $value):
            if ($key != 'sig') $payload .= $key.'='.$value;
        endforeach;

        if (md5($payload . $app_secret) != $args['sig']) return null;
        return $args;
    }

	public function send_request()
	{
	    //User of $_SERVER['REQUEST_METHOD'] is not needed, using $variable = $this->input->post('da') is better
	    if($requested_user = $this->input->post('username')):
	        $this->load->model(array('user_engine', 'friend_engine'));
			$friend_data = $this->user_engine->get_userdata($requested_user);

			// Make sure you're not adding yourself
			if(count($friend_data) < 1) die(json_encode(array('error' => 'We couldn\'t find this user anywhere, could you please make sure you wrote it right?')));

			// Make sure you're not adding yourself
			if(strtolower($this->input->post('username')) == strtolower($this->session->userdata('username'))) die(json_encode(array('error' => 'Sorry, but you cannot add yourself as a friend.')));

			// Are these users already friends?
			$existing_friendship = $this->friend_engine->currently_friends($this->session->userdata('id'), $friend_data['id']);

			if($existing_friendship != FALSE) {
			    if($existing_friendship['status'] == "pending"){
			        if($existing_friendship['friend_id'] == $this->session->userdata('id')){
			            die(json_encode(array('response' => $this->input->post('username').' has already sent you a friend request, you should accept it! :D')));
			        } else {
			            die(json_encode(array('response' => 'You\'ve already sent '.$this->input->post('username').' a friend request, but we and cleared the old one and sent them a new one.')));
			        }
			    } else {
			         die(json_encode(array('response' => 'You and '.$this->input->post('username').' are already friends!')));
			    }
			}

			$first_time_friendship = TRUE;

			// Are these users already friends?
			$recent_friendship = $this->db->where('user_id', $this->session->userdata('id'))
   					 						->where('friend_id', $friend_data['id'])
											->where('status', 'inactive')
					 						->get('friends');

			if($recent_friendship->num_rows() > 0){
			    $recent_friendship = $recent_friendship->row_array();

                if((time()-(86400*2)) > strtotime($recent_friendship['friendship_end_date'])):
    				$first_time_friendship = FALSE;
    			endif;
			}

			$this->db->where('id', $friend_data['id'])->update('users', array('notifications' => ($friend_data['notifications']+1)));
            $this->db->set('notification_timestamp', 'NOW()', false)->insert('user_notifications', array(
                'notification_type'          => 'friend_request',
                'notification_from_user_id'  => $this->system->userdata['id'],
                'notification_from_username' => $this->system->userdata['username'],
                'notification_to_user_id'    => $friend_data['id'],
                'notification_to_username'   => $friend_data['username'],
                'notification_message'       => $this->system->userdata['username'].' has sent you a friend request!',
                'notification_active'        => 1
            ));

			if($first_time_friendship === TRUE):
				// create a new row and give gold to both parties
				$new_friend_data = array(
                    'user_id'               => $this->session->userdata('id'),
                    'friend_id'             => $friend_data['id'],
                    'status'                => 'pending',
                    'first_time_friendship' => 1
				);

				$this->db->set('friendship_start_date', 'NOW()', FALSE)->insert('friends', $new_friend_data);

				echo json_encode(array('response' => 'Your request was successfully sent!'));
			else:
                // update the old one to become a fresh request.
			    $this->db->where('id', $recent_friendship['id'])
			             ->set('friendship_start_date', 'NOW()', FALSE)
			             ->update('friends', array('status' => 'pending', 'first_time_friendship' => 0));

				echo json_encode(array('response' => 'Yay, your becoming friends again! Your request has been sent! :D'));
			endif;
	    endif;
	}


	public function remove_friend()
	{
	    if($this->input->post('friendship_id') && is_ajax()):
	        if( ! is_numeric($this->input->post('friendship_id'))) die(json_encode(array('error' => 'Invalid friendship_id')));
	        $this->friend_engine->end_friendship($this->input->post('friendship_id'));
	        echo json_encode(array('response' => 'Friend has been removed.'));
	    endif;
	}


	public function send_gift()
	{
        // make sure they're not abusing the more than one gift a day thing
        // make sure they are friends
        // get the reward they picked, and do some updates to make sure they can't do it again for the rest of the day
	}

	public function accept($friendship_id = 0)
	{
	    $this->load->model('user_engine');
	    $friendship = $this->db->where(array('id' => $friendship_id, 'status' => 'pending', 'friend_id' => $this->session->userdata('id')))->get('friends');

	    if($friendship->num_rows() < 1) die(json_encode(array('error' => 'Friendship not found!')));

	    $friendship = $friendship->row_array();

	    if($friendship['first_time_friendship'] == 1){
	        // Give users a small reward for their friendy-ness!
    	    $this->user_engine->add_gold(5, array($this->session->userdata('id'), $friendship['user_id']));
	    }

	    // Update request to comfirmed friendship
	    $this->db->where('id', $friendship['id'])->update('friends', array('status' => 'active', 'first_time_friendship' => 0));

	    // create the new friendship row for the person who accepted it
	    $new_friend_data = array(
           'user_id'               => $this->session->userdata('id'),
           'friend_id'             => $friendship['user_id'],
           'status'                => 'active',
           'first_time_friendship' => 0
	    );

	    $this->db->set('friendship_start_date', 'NOW()', FALSE)->insert('friends', $new_friend_data);

	    $this->session->set_flashdata('notice', 'You have successfully accepted your friend request!');

	    // [x] make sure the non-accepted friendship exists
	    // [x] make sure your part of the friendship
	    // [x] create the row in the friends as in your part of the friendship, and give both users 10gold. :D
	    redirect('friends');
	}


	public function decline($friendship_id = 0)
	{
	    $friendship = $this->db->where(array('id' => $friendship_id, 'status' => 'pending', 'friend_id' => $this->session->userdata('id')))->get('friends');

	    if($friendship->num_rows() < 1) die(json_encode(array('error' => 'Friendship not found!')));
	    $friendship = $friendship->row_array();

        $this->db->where('id', $friendship['id'])->update('friends', array('status' => 'inactive'));

	    $this->session->set_flashdata('notice', 'You have successfully declined the friend request.');

	    redirect('friends');
	}

}

/* End of file Friends.php */
/* Location: ./system/application/controllers/Friends.php */