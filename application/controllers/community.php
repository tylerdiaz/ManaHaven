<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Forum
 *
 * @author(s) 	Tyler Diaz
 * @version 	1.0
 **/

class Community extends CI_Controller
{
    public $settings = array(
        'gold' => array(
            'super_bonus_chances' => 5, // % percent
            'max_gold_cap'        => 10,
            'reward_rand_min'     => 1,
            'reward_rand_max'     => 7
        ),
        'post' => array(
            'reward_gold_ratio' => 1, // 0 = Disable, 2 = Double
        ),
    );

    function __construct(){
        parent::__construct();
    }

    public function index()
    {
        $forums = $this->db->get('forums')->result_array();
        $parsed_forums = array();

        foreach ($forums as $forum_data):
            $parsed_forums[$forum_data['id']] = $forum_data;
        endforeach;

        foreach($forums as $forum_key => $forum):
            $topics[$forum_key] = $this->db->limit(10)
                                           ->order_by('last_post_time', 'desc')
                                           ->get_where('topics', array('forum_id' => $forum['id']))
                                           ->result_array();
        endforeach;

        $users_online = $this->db->join('users', 'users.id = user_activity.user_id')
                                 ->where('user_activity.last_activity >', time()-1800)
                                 ->limit(30)
                                 ->order_by('user_activity.last_activity', 'desc')
                                 ->get('user_activity')
                                 ->result_array();

        $this->system->quick_parse('community/index', array(
            'page_title'   => 'The Community',
            'page_body'    => 'community',
            'forums'       => $parsed_forums,
            'topics'       => $topics,
            'users_online' => $users_online,
            'location'     => 'community'
        ));
    }

    public function topic($topic_id = 0)
    {
        if( ! is_numeric($topic_id)) die('Invalid topic ID datatype');

        $topic = $this->db->select('*')->where(array('topic_id' => $topic_id))->get('topics');
        $forums = $this->db->get('forums')->result_array();

        if($topic->num_rows() > 0):
        	$topic_data = $topic->row_array();

        	// [START] Pagination library
    		$this->load->library('pagination');

    		$config['base_url'] = site_url('community/topic/'.$topic_id);
    		$config['total_rows'] = ($topic_data['total_replies']);
    		$config['per_page'] = 12;
    		$config['uri_segment'] = 4;

    		$this->pagination->initialize($config);
    		// [END] Pagination library

    		$load_from = $this->uri->segment(4, 0)+1;
    		$load_to = ($this->uri->segment(4, 0)+$config['per_page']);

    		// Hack around the front page using a reverse index trick
    		if($this->uri->segment(4, 0) == 0):
                $topic_posts = $this->db->select('post_id, author_id, author_username, post_time, text, topic_post_id')
                                        ->where('topic_id', $topic_id)
                                        ->limit($config['per_page'], 0)
                                        ->get('topic_posts')
                                        ->result_array();
    		else:
                $topic_posts = $this->db->select('post_id, author_id, author_username, post_time, text, topic_post_id')
                                        ->where('topic_id', $topic_id)
                                        ->where_in('topic_post_id', range($load_from, $load_to)) // This only keeps the first 12
                                        ->order_by('post_id', 'asc')
                                        ->order_by('topic_post_id', 'asc')
                                        ->limit($config['per_page'])
                                        ->get('topic_posts')
                                        ->result_array();
    		endif;

    		// If the page is empty, redirect you to the old page.
            if(count($topic_posts) < 1) redirect('community/topic/'.$topic_id.'/'.($this->uri->segment(4, 0)-$config['per_page']));

            foreach ($topic_posts as $post):
                $authors[$post['author_id']] = TRUE;
            endforeach;

            $users_online = array();

            $authors_online = $this->db->where_in('user_id', array_keys($authors))->get('user_activity')->result_array();
            foreach ($authors_online as $author):
                if((time()-$author['last_activity']) < 900) array_push($users_online, $author['user_id']);
            endforeach;

        else:
        	die('Topic not found...');
        endif;

        $view_data = array(
            'page_title'   => $topic_data['title'],
            'page_body'    => 'community',
            'topic'        => $topic_data,
            'users_online' => $users_online,
            'posts'        => $topic_posts,
            'forums'       => $forums,
            'location'     => 'topic_'.$topic_id,
            'topic_notice' => ($this->session->flashdata('topic_notice') ? '<div class="notice">'.$this->session->flashdata('topic_notice').'</div>' : '')
        );

   	    if($this->session->userdata('signed_in')):
            $this->system->quick_parse('community/topic', $view_data);
   	    else:
            $this->system->quick_parse('community/topic_signed_out', $view_data);
   	    endif;

        if($this->system->is_staff()):
            $this->load->library('unit_test');

            // We're doing +1 since the topic posts start from one upwards. Unlike regular programming counting.
            $post_count = ($this->db->get_where('topic_posts', array('topic_id' => $topic_id))->num_rows());
            $this->unit->run($post_count, $topic_data['total_replies'], "Real total topic posts", 'Equation: ('.$post_count.' = '.$topic_data['total_replies'].')');
            $this->output->append_output("<div style=\"padding:20px; font-size:14px;\">".$this->unit->report()."</div>");
        endif;
    }

    public function create_topic_template()
    {
        if(is_ajax() == TRUE):
            $staff_only = 0;
            if($this->system->is_staff()) $staff_only = 1;

            $forums = $this->db->get_where('forums', array('staff_only <=' => $staff_only))->result_array();

            $this->load->view('community/popup/new_topic', array('forums' => $forums));
        endif;
    }

    public function create_topic()
    {
        if(is_ajax() == FALSE) die('Oops, we could not process your request. Please contact a developer!');

        $forum_id = $this->input->post('category');
        $topic_message = $this->input->post('message');
        $topic_title = $this->input->post('title');

        if($forum_id == 1 && ! $this->system->is_staff()) show_error('You are not allowed to post here!');

        // Add a default category
        if(strlen($forum_id) == 0):
            $forum_id = 3;
        endif;

        if($this->input->post('forgotten_category') == TRUE):
            $this->session->set_flashdata('topic_notice', 'Psst... We noticed you forgot to set a category on this new topic, so we went ahead and placed it in the "Gameplay Chat" for you, we hope that\'s okay.');
        endif;

        $topic_data = array(
            'forum_id'        => $forum_id,
            'title'           => replace_foul_words($topic_title),
            'author_id'       => $this->session->userdata('id'),
            'author_username' => $this->session->userdata['username'],
            'total_replies'   => 1,
            'last_post'       => 0,
            'topic_views'     => 0,
            'last_post_by'    => $this->session->userdata['username']
        );

        $this->db->set('date_created', 'NOW()', false)
                 ->set('last_post_time', 'NOW()', false)
                 ->insert('topics', $topic_data);

        $topic_id = $this->db->insert_id();

        $topic_post = array(
            'topic_id'        => $topic_id,
            'author_ip'       => $this->input->ip_address(),
            'author_id'       => $this->session->userdata('id'),
            'author_username' => $this->session->userdata['username'],
            'text'            => $topic_message,
        );

        $post_id = $this->db->insert_id();
        $this->db->set('post_time', 'NOW()', false)->insert('topic_posts', $topic_post);
        $this->db->where('topic_id', $topic_id)->update('topics', array('last_post' => $post_id));

        $this->system->parse_json(array('topic_id' => $topic_id));
    }

    public function create_post()
    {
        if(is_ajax() == FALSE) die('Oops, we could not process your request. Please contact a developer!');
        if ( ! $this->session->userdata('id')) die(json_encode(array('error' => 'You must be signed in to post a reply')));

        $topic_id = $this->input->post('topic_id');
        $message = rawurldecode($this->input->post('message'));

        if( ! is_numeric($topic_id)) die('Invalid topic ID datatype');

        $topic = $this->db->select('topic_id, total_replies')->where(array('topic_id' => $topic_id))->get('topics');

        if($topic->num_rows() > 0):
        	$topic_data = $topic->row_array();

            $topic_post = array(
            	'topic_id' => $topic_id,
            	'author_ip' => $this->input->ip_address(),
            	'author_id' => $this->session->userdata('id'),
            	'author_username' => $this->system->userdata['username'],
            	'text' => $message,
                'topic_post_id' => ($topic_data['total_replies']+1),
            );

            $this->db->set('post_time', 'NOW()', false)->insert('topic_posts', $topic_post);
        	$post_id = $this->db->insert_id();

        	$topic_data = array(
            	'total_replies' => $topic_data['total_replies']+1,
            	'last_post' => $this->db->insert_id(),
            	'last_post_by' => $this->session->userdata['username']
            );

            $this->db->set('last_post_time', 'NOW()', false)->where(array('topic_id' => $topic_id))->update('topics', $topic_data);

            apc_store('t'.$topic_id.'_post_key', ($topic_data['total_replies']+1), 150);

            $cache_data = array(
                'message'       => $this->_javascript_filter($message),
                'username'      => $this->system->userdata['username'],
                'user_id'       => $this->session->userdata('id'),
                'post_id'       => $post_id,
                'timestamp'     => date("o-m-d\TH:i:s"),
                'topic_post_id' => ($topic_data['total_replies']+1),
            );

            apc_add('t'.$topic_id.'p'.($topic_data['total_replies']+1), $cache_data, 5); // Store for 5 seconds

            $this->load->model('user_engine');

            $total_active_users = apc_fetch('active_topic_'.$topic_id.'_users');

            if( ! $total_active_users):
                $total_active_users = $this->_get_latest_posters($topic_id);
                apc_store('active_topic_'.$topic_id.'_users', $this->_get_latest_posters($topic_id), 600); // 10 Minute cache
            endif;

            $gold_limit = mt_rand($this->settings['gold']['reward_rand_min'], $this->settings['gold']['reward_rand_max']);
            if($total_active_users > 2) $gold_limit += min(floor($total_active_users/2), $this->settings['gold']['max_gold_cap']);

            $gold_bonus = mt_rand(1, $gold_limit);
            $super_bonus = (mt_rand(1, 100) >= (100-$this->settings['gold']['super_bonus_chances']) ? TRUE : FALSE);

            if($super_bonus === TRUE):
                $gold_bonus = ($gold_bonus*2)+3;
                $this->db->where('character_id', $this->system->userdata['character_id'])->update('characters', array('energy' => min(($this->system->userdata['energy']+1), $this->system->userdata['max_energy'])));
            endif;

            $gold_bonus *= $this->settings['post']['reward_gold_ratio'];

            $this->system->parse_json(array(
                 'message' => $this->_javascript_filter($message),
                 'user_id' => $this->session->userdata('id'),
                 'username' => $this->system->userdata['username'],
                 'timestamp' => date("o-m-d\TH:i:s"),
                 'post_id' => $post_id,
                 'reward' => $this->user_engine->add_gold(min($gold_bonus, 12)), // Capped at 12 gold, just incase. :)
                 'topic_post_id' => ($topic_data['total_replies']+1),
                 'super_bonus' => $super_bonus
             ));
        else:
        	show_error('Topic not found');
        endif;
    }

    public function get_post_data($post_id = 0, $format = FALSE)
    {
        $this->output->set_header('Content-type: application/json; charset=utf8');
        $this->output->set_header('Cache-Control: private, no-cache, no-store, must-revalidate');

        $topic_post = $this->db->select('*')->where(array('post_id' => $post_id, 'author_id' => $this->session->userdata('id')))->get('topic_posts');

        if($topic_post->num_rows() > 0):
        	$topic_post_data = $topic_post->row_array();

        	if($format == FALSE):
        	    $this->system->parse_json(array('text' => $topic_post_data['text']));
        	else:
        	    $this->system->parse_json(array('text' => $this->_javascript_filter($topic_post_data['text'])));
        	endif;
        else:
	        $this->system->parse_json(array('error' => 'Error: Post data could not be loaded.'));
        endif;
    }

    public function edit_post_data($post_id = 0)
    {
        $this->output->set_header('Content-type: application/json; charset=utf8');

        $topic_post = $this->db->select('*')
                               ->where(array('post_id' => $post_id, 'author_id' => $this->session->userdata('id')))
                               ->get('topic_posts');

        if($topic_post->num_rows() > 0):
        	$topic_post_data = $topic_post->row_array();
        	$message = rawurldecode($this->input->post('text'));

        	$this->db->where('post_id', $post_id)->update('topic_posts', array('text' => $message));

        	$this->system->parse_json(array('text' => $this->_javascript_filter($message)));
        else:
        	$this->system->parse_json(array('error' => 'Error: Post data could not be loaded.'));
        endif;
    }

    public function load_more_posts($forum_id = 0, $total_posts = 0)
    {
        if( ! is_numeric($total_posts) || ! is_numeric($total_posts)) show_error('Params must be numerical');
        $this->output->set_header('Content-type: application/json; charset=utf8');

        $ajax_topics = $this->db->limit(10, $total_posts)
                                ->order_by('last_post_time', 'desc')
                                ->get_where('topics', array('forum_id' => $forum_id))
                                ->result_array();

        $json_response = array();
        foreach ($ajax_topics as $topic):
            $json_response[] = array(
                'topic_link' => anchor('community/topic/'.$topic['topic_id'], $topic['title'], ' class="topic_title"'),
                'author_link' => anchor('profile/'.urlencode($topic['last_post_by']), $topic['last_post_by']),
                'last_post_link' => anchor('community/topic/'.$topic['topic_id'].'/'.(floor(($topic['total_replies']-1)/12)*12), $topic['total_replies'].' &rsaquo;'),
                'creator_username' => $topic['author_username'],
                'timestamp' => human_time($topic['last_post_time'])
            );
        endforeach;

        $this->system->parse_json($json_response);
    }

    public function change_topic_title()
    {
        $topic_id = $this->input->post('topic_id');
        $topic_title = urldecode(htmlentities($this->input->post('new_topic_title')));

        if( ! is_numeric($topic_id)) show_error('Invalid topic id');

        $topic = $this->db->select('*')->where(array('topic_id' => $topic_id))->get('topics');

        if($topic->num_rows() > 0):
        	$topic_data = $topic->row_array();

        	if ($topic_data['author_id'] == $this->session->userdata('id')):
                $this->db->where('topic_id', $topic_id)->update('topics', array('title' => replace_foul_words($topic_title)));
                $this->system->parse_json(array('success' => 'Post title edited!'));
            else:
                show_error('Permission denied');
        	endif;
        else:
            show_error('Topic not found!');
        endif;
    }

    private function _get_latest_posters($topic_id = 0)
    {
        return $this->db->select('COUNT(DISTINCT author_username) as total_users')
                        ->from('topic_posts')
                        ->where('topic_id', $topic_id)
                        ->where('post_time >=', 'DATE_SUB(NOW(), INTERVAL 0.5 HOUR)', FALSE)
                        ->order_by('topic_post_id', 'desc')
                        ->get()
                        ->row()
                        ->total_users;
    }


    private function _javascript_filter($text = "", $post_format = TRUE)
    {
        if($post_format === TRUE):
            return rawurlencode(nl2br(simple_bbcode(htmlspecialchars($text))));
        elseif($post_format === FALSE):
            return rawurlencode(htmlspecialchars($text));
        endif;
    }

}

/* End of file Forum.php */
/* Location: ./system/application/controllers/Forum.php */