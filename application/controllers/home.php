<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Home
 *
 * @author(s) 	Tyler Diaz
 * @version 	1.0
 **/

class Home extends CI_Controller
{

    public function index()
    {
        if ( ! $this->session->userdata('signed_in')):
            $this->system->quick_parse('home/signed_out', array('page_title' => 'A chat-based role playing adventure', 'page_body' => 'home'));
        else:
            $my_friends = $this->db->select('users.username, users.id as user_id, friends.id, user_activity.last_activity')
    							   ->where('friends.user_id', $this->session->userdata('id'))
    							   ->where('status', 'active')
    							   ->join('users', 'friends.friend_id = users.id')
    							   ->join('user_activity', 'users.id = user_activity.user_id', 'left')
    							   ->limit(10)
    							   ->order_by('user_activity.last_activity', 'desc')
    							   ->get('friends')
    							   ->result_array();

            $latest_topics = $this->db->select('*')
                                      ->from('topics')
                                      ->order_by('last_post_time', 'desc')
                                      ->limit(4)
                                      ->get()
                                      ->result_array();

            $cached_statistics = apc_fetch('statistics');
            if($cached_statistics){
                $total_battles = $cached_statistics['total_battles'];
                $total_posts = $cached_statistics['total_posts'];
            } else {
                $total_battles = ceil($this->db->where(array('battle_started_at >=' => (time()-86400)))->get('battles')->num_rows()/5)*5;
                $total_posts = $this->db->select('COUNT(*) as total_posts')
                                        ->from('topic_posts')
                                        ->where('post_time >=', 'DATE_SUB(NOW(), INTERVAL 24 HOUR)', FALSE)
                                        ->get()->row()->total_posts;

                apc_store('statistics', array('total_posts' => $total_posts, 'total_battles' => $total_battles), 120);
            }

            $notifications = array();

            if($this->system->userdata['notifications'] > 0):
                $user_notifications = $this->db->select('COUNT(notification_id) as total, notification_type')->where(array(
                    'notification_active' => 1,
                    'notification_to_user_id' => $this->system->userdata['id']
                ))->limit(30)->group_by('notification_type')->get('user_notifications');

                if($user_notifications->num_rows() > 0):
                	// user_notification was found. Mission accomplished.
                	$user_notifications = $user_notifications->result_array();
	                foreach ($user_notifications as $key => $user_notification):
                		// Looping through notifications
                		switch($user_notification['notification_type']):
                            case "comment":
                    	        $notifications[] = 'You have '.$user_notification['total'].' new profile comment'.($user_notification['total'] > 1 ? 's' : '').'! &bull;  '.anchor('profile', 'View my profile &raquo;');
                            break;
                            case "friend_request":
                    	        $notifications[] = 'You have '.$user_notification['total'].' new friend request'.($user_notification['total'] > 1 ? 's' : '').'! &bull;  '.anchor('friends', 'View my friend requests &raquo;');
                            break;
                		endswitch;
                	endforeach;
                endif;
            endif;

            $this->system->quick_parse('home/new_signed_in', array(
                'page_title' => 'Dashboard',
                'page_body' => 'home',
                'location' => 'dashboard',
                'my_friends' => $my_friends,
                'latest_topics' => $latest_topics,
                'notifications' => $notifications,
                'total_battles' => number_format($total_battles+500),
                'total_posts' => number_format($total_posts+50)
            ));
        endif;
    }

    public function get_latest_topics($limit = 4)
    {
        if($limit > 10) die(json_encode(array('error' => 'Limit can only be less than 10')));

        $latest_topics = $this->db->select('author_username, last_post_time, title, topic_id, total_replies, last_post_by, last_post')
                                  ->from('topics')
                                  ->order_by('last_post_time', 'desc')
                                  ->limit(4)
                                  ->get()
                                  ->result_array();

        $this->db->close();

        $formatted_topics = array();

        foreach ($latest_topics as $topic):
            $formatted_topics[] = array(
                'topic_title' => $topic['title'],
                'link_location' => '/community/topic/'.$topic['topic_id'].'/'.(floor(($topic['total_replies']-1)/12)*12).'#'.$topic['last_post'],
                'timestamp' => human_time($topic['last_post_time']),
                'last_poster' => $topic['last_post_by'],
                'total_replies' => $topic['total_replies'],
                'raw_timestamp' => strtotime($topic['last_post_time']),
            );
        endforeach;

        if($this->input->get('json')):
            echo json_encode($formatted_topics);
        else:
            return $formatted_topics;
        endif;
    }

}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */