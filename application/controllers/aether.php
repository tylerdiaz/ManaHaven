<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Aether Controller
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 **/

/*
 * Aether is a code-name for a global handler. This is mostly for AJAX
 * calls, or for things when an action doesn't deserver a full controller
 *
 * Aether means "The god of the upper air and light"
*/
class Aether extends CI_Controller
{

    public function update_online_status()
    {
        if($this->session->userdata('id')):
            $this->db->where('user_id', $this->system->userdata['id'])->update('user_activity', array('last_activity' => time()));
            $this->load->library('user_agent');

            $log_data = array(
                'time_on_page'     => $this->input->get('time_on_page'),
                'topic_id'         => $this->input->get('topic_id'),
                'window_attention' => $this->input->get('window_attention'),
                'new_posts_loaded' => $this->input->get('new_posts_loaded'),
            );

            $userdata = array(
                'level'        => $this->system->userdata['level'],
                'gender'       => $this->system->userdata['gender'],
                'gold'         => $this->system->userdata['gold'],
                'invites_left' => $this->system->userdata['invites_left'],
                'facebook_id'  => $this->system->userdata['facebook_id']
            );

            $action_logs = array(
                'username' => $this->system->userdata['username'],
                'user_id'  => $this->system->userdata['id'],
                'userdata' => json_encode($userdata),
                'ip'       => $this->input->ip_address(),
                'time'     => time(),
                'type'     => 'data',
                'browser'  => $this->agent->browser().' - '.$this->agent->version(),
                'action'   => 'topic_stick',
                'data'     => json_encode($log_data)
            );

            $this->db->insert('action_logs', $action_logs);

            $this->system->parse_json(array('success' => 1));
        else:
            $this->system->parse_json(array('notice' => 'logged_out'));
        endif;
    }

    public function post_comment($user_id = 0)
    {
        if( ! is_numeric($user_id)) die('Invalid user id');

        if($this->session->userdata('id')):
            $user = $this->db->where(array('id' => $user_id))->get('users');

            if($user->num_rows() > 0):
            	// user was found. Mission accomplished.
            	$user_data = $user->row_array();
                $this->db->set('comment_timestamp', 'NOW()', false)->insert('profile_comments', array(
                    'comment_messsage'   => $this->input->post('comment'),
                    'commenter_username' => $this->system->userdata['username'],
                    'commenter_id'       => $this->session->userdata('id'),
                    'profile_user_id'    => $user_id
                ));

                $this->db->where('id', $user_id)->update('users', array('notifications' => ($user_data['notifications']+1)));
                $this->db->set('notification_timestamp', 'NOW()', false)->insert('user_notifications', array(
                    'notification_type'          => 'comment',
                    'notification_from_user_id'  => $this->system->userdata['id'],
                    'notification_from_username' => $this->system->userdata['username'],
                    'notification_to_user_id'    => $user_id,
                    'notification_to_username'   => $user_data['username'],
                    'notification_message'       => $this->system->userdata['username'].' has commented on your profile!',
                    'notification_active'        => 1
                ));

                $this->system->parse_json(array(
                    'username' => $this->system->userdata['username'],
                    'message'  => nl2br(sanitize($this->input->post('comment'))),
                    'id'       => $this->db->insert_id()
                 ));
            else:
                show_error('User not found');
            endif;
        endif;
    }

    public function delete_comment()
    {
        if( ! is_numeric($this->input->post('id'))) die('Invalid comment id');
        $comment_id = $this->input->post('id');

        $profile_comment = $this->db->select('*')->where(array('comment_id' => $comment_id))->get('profile_comments');

        if($profile_comment->num_rows() > 0){
        	$comment_data = $profile_comment->row_array();
        	if($comment_data['profile_user_id'] == $this->system->userdata['id'] || $comment_data['commenter_id'] == $this->system->userdata['id']){
        	    $this->db->where(array('comment_id' => $comment_id))->delete('profile_comments');

                $profile_comment = $this->db->select('*')->where(array('profile_user_id' => $comment_data['profile_user_id']))->order_by('comment_timestamp', 'desc')->limit(1, 4)->get('profile_comments');

                $new_json_comment = $profile_comment->row_array();
                $new_json_comment['comment_timestamp'] = human_time($new_json_comment['comment_timestamp']);

                if($profile_comment->num_rows() > 0) die(json_encode($new_json_comment));

                $this->system->parse_json(array('no_new_comments'));
        	} else {
                show_error('You enough permissions!');
        	}
        } else {
            show_error('Comment does not exist');
        }

    }

    public function full_heal()
    {
        // outline!
            // If not in a battle && has more than 1 energy
                // deplete 1 energy. set hp as max hp

		if( ! $this->session->userdata('id')) redirect(site_url());

        $battle = $this->db->where(array(
            'character_id'  => $this->system->userdata['character_id'],
            'active_battle' => 1
	    ))->get('battle_characters');

	    if($battle->num_rows() > 0):
	    	// battle_id found!
            show_error('You cannot full-heal while you\'re in a battle.');
	    endif;

	    if($this->system->userdata['energy'] > 0):
	        $this->db->where('character_id', $this->system->userdata['character_id'])->update('characters', array(
                'hp'     => $this->system->userdata['max_hp'],
                'energy' =>  ($this->system->userdata['energy']-1)
	        ));

	        $this->system->parse_json(array('success' => 1, 'hp' => $this->system->userdata['max_hp']));
	    else:
	        show_error('Not enough energy!');
	    endif;
    }

}

/* End of file Aether.php */
/* Location: ./system/application/controllers/Aether.php */