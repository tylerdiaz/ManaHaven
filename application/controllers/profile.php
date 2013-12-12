<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Profile Controller
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 **/

/*
    TODO: Add load more comments
    TODO: Under Development side of the profiles
    TODO: Quest counter
*/
class Profile extends CI_Controller
{
    public function view($username = "")
    {
        if( ! preg_match('/^[a-z0-9\+]+/i', $username)) show_error('username must be valid');
        $username = urldecode($username);

        $user = $this->db->select('id, username, characters.*, user_activity.last_activity, user_activity.location, last_saved_avatar')
                         ->where(array('username' => $username))
                         ->join('characters', 'characters.user_id = users.id')
                         ->join('user_activity', 'user_activity.user_id = users.id', 'left')
                         ->get('users');

        if($user->num_rows() > 0){
        	$user_data = $user->row_array();

        	$total_items = $this->db->join('avatar_items', 'avatar_items.item_id = character_items.item_id')->join('avatar_layers', 'avatar_layers.id = avatar_items.layer')->get_where('character_items', array('appearance' => 0, 'character_id' => $user_data['character_id']))->num_rows();

        	$total_posts = $this->db->get_where('topic_posts', array('author_id' => $user_data['id']))->num_rows();
        	$total_friends = $this->db->get_where('friends', array('friend_id' => $user_data['id']))->num_rows();
        	$comments = $this->db->limit(5)->order_by('comment_timestamp', 'desc')->get_where('profile_comments', array('profile_user_id' => $user_data['id']))->result_array();
        	$total_comments = $this->db->get_where('profile_comments', array('profile_user_id' => $user_data['id']))->num_rows();

        	// Are we freinds?
	        $friendship = FALSE;

        	if(isset($this->system->userdata['id'])):
        	    $friend = $this->db->select('*')->where(array('user_id' => $this->system->userdata['id'], 'friend_id' => $user_data['id']))->get('friends');

        	    if($friend->num_rows() > 0):
        	        $friendship = TRUE;
        	    endif;

        	    if(strtolower($username) == strtolower($this->system->userdata['username'])):
        	        // Clear my notifications!
        	        if($this->system->userdata['notifications'] > 0):
                        $this->db->where('notification_to_user_id', $this->system->userdata['id'])
            	                 ->where('notification_active', 1)
            	                 ->where('notification_type', 'comment')
            	                 ->update('user_notifications', array('notification_active' => 0));

                        $total_notifications = $this->db->affected_rows();
                        $this->system->userdata['notifications'] -= $total_notifications;

                        $this->db->where('id', $this->system->userdata['id'])->update('users', array('notifications' => ($this->system->userdata['notifications'])));
        	        endif;
        	    endif;
        	endif;

        	$view_data = array(
                'page_title'     => 'Profile - '.$user_data['username'],
                'page_body'      => 'profile',
                'user_data'      => $user_data,
                'total_items'    => number_format($total_items),
                'total_posts'    => number_format($total_posts),
                'total_friends'  => number_format($total_friends),
                'comments'       => $comments,
                'total_comments' => $total_comments,
                'friendship'     => $friendship,
                'user_location'  => ($friendship ? $this->_parse_location($user_data['location']) : FALSE),
                'location'       => $user_data['username'].'\'s profile',
        	);

        	$this->system->quick_parse('profile/view', $view_data);
        } else {
            show_404('User not found!');
        }
    }

    public function _parse_location($location_string = '')
    {
        $this->load->helper('array');
        if(preg_match('/topic_/', $location_string)):
            $topic_id = preg_replace('/topic_/', '', $location_string);
            $topic = $this->db->select('*')->where(array('topic_id' => $topic_id))->get('topics');

            if($topic->num_rows() > 0):
            	$topic_data = $topic->row_array();
            	return "in ".anchor('community/topic/'.$topic_data['topic_id'], $topic_data['title']);
            else:
                return "in a hidden topic";
            endif;
        else:
            switch($location_string):
                case "avatar":
                    $descriptions = array(
                        'spicing up their avatar',
                        'improving their avatar\'s looks',
                        'changing some clothing articles~',
                        'jazzing up their avatar'
                    );
                    return random_element($descriptions);
                break;
                case "dashboard":
                    $descriptions = array(
                        'in the dashboard',
                        'waiting for a comment',
                        'viewing new topics'
                    );
                    return random_element($descriptions);
                break;
                case "shops":
                    $descriptions = array(
                        'window-shopping the local market',
                        'looking for new items to buy'
                    );
                    return random_element($descriptions);
                break;
                case "world":
                    $descriptions = array(
                        'navigating the world',
                        'exploring the world',
                        'discovering the world'
                    );
                    return random_element($descriptions);
                break;
                case "community":
                    $descriptions = array(
                        'browsing the forums',
                    );
                    return random_element($descriptions);
                break;
                default:
                    $descriptions = array(
                        'daydreaming',
                        'jumping around',
                        'star-gazing',
                        'waiting for you to leave a comment',
                        'looking around',
                        'checking their dashboard',
                    );
                    return random_element($descriptions);
                break;
            endswitch;
        endif;
    }

    public function index()
    {
        $this->view($this->session->userdata('username'));
    }

    public function settings()
    {
        /*
         * Note from Tyler:
         * This script is a mess, I could have used CI's form validator but I rushed it and it works.
         * Whoever has the heart and love to clean this code up will be granted tremendous hospitality from Tyler.
        */
        $error = FALSE;
        $success = FALSE;

        if($_SERVER['REQUEST_METHOD'] == "POST"):
            $this->load->library('authentication');

            if($this->input->post('email') == $this->system->userdata['email']):

                if($this->authentication->verify_password($this->input->post('current_password'), $this->session->userdata('id'))):
                    if(strlen($this->input->post('new_password')) > 5):
                        if($this->input->post('new_password') == $this->input->post('new_again_password')):
                            // Save new password.
                            $this->db->where('id', $this->session->userdata('id'))
                                     ->update('users', array('password' => $this->authentication->hash_password($this->input->post('new_password'))));

                            $success = 'Your password has been successfully changed!';
                        else:
                            $error = 'Your new password confirmation did not match!';
                        endif;
                    else:
                        $error = 'Your new password must be 6 characters or longer!';
                    endif;
                else:
                    $error = 'It seems you have mis-spelled your current password, could you type it again carefully?';
                endif;
            else:
                // Save new email!
                $this->load->helper('email');

                if (valid_email($this->input->post('email'))):
                    $this->db->where('id', $this->session->userdata('id'))->update('users', array('email' => $this->input->post('email')));
                    $this->system->userdata['email'] = $this->input->post('email');
                    $success = 'Your email has been successfully changed!';
                else:
                    $error = 'Your new email must be a valid email';
                endif;
            endif;
        endif;

        if($error) $error = '<div class="error">'.$error.'</div>';
        if($success) $success = '<div class="success">'.$success.'</div>';

        $view_data = array(
            'page_title' => 'Your Settings',
            'page_body'  => 'profile',
            'error'      => $error,
            'success'    => $success
        );

        $this->system->quick_parse('profile/settings', $view_data);
    }

}

/* End of file Profile.php */
/* Location: ./system/application/controllers/Profile.php */