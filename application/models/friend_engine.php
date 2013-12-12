<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Friend engine Model
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 * @copyright Tyler Diaz - September 21, 2011 
 * @last_update: May 2, 2011 by Tyler Diaz
 **/

class Friend_engine extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
    
    // --------------------------------------------------------------------

    /**
     * Get Friends
     *
     * Get an array of all the users friends
     *
     * @access  public
     * @param   int
     * @param   int
     * @return  bool|array
     */ 
        
    public function get_friends($user_id = 0, $limit = 28)
    {
        $fiften_minutes_ago = time()-(60*15);
        $friends = $this->db->select('users.username, friends.id, users.id as user_id, friends.gifts_exchanged, friends.gift_date, IF(user_activity.last_activity >= '.$fiften_minutes_ago.', true, false) as user_online', false)
							->where('friends.user_id', $this->session->userdata('id'))
							->where('status', 'active')
							->join('users', 'friends.friend_id = users.id')
							->join('user_activity', 'user_activity.user_id = users.id')
							->limit($limit)
							->get('friends');
        
        return ($friends->num_rows() > 0 ? $friends->result_array() : FALSE);
    }
    
    // --------------------------------------------------------------------

    /**
     * Get pending requests
     *
     * List out all the pending friend requests
     *
     * @access  public
     * @param   int
     * @param   int
     * @return  bool|array
     */ 
        
    public function get_pending_requests($user_id = 0, $limit = 12)
    {
		$requests = $this->db->select('username, friends.id, users.id as user_id')
                             ->where('friend_id', $user_id)
                             ->where('status', 'pending')
                             ->join('users', 'friends.user_id = users.id')
                             ->limit($limit)
                             ->get('friends');
        
        return ($requests->num_rows() > 0 ? $requests->result_array() : FALSE);
    }
    
    // --------------------------------------------------------------------

    /**
     * New page
     *
     * New page description
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */ 
        
    public function currently_friends($user_id = 0, $friend_id = 0)
    {
		$friendship = $this->db->query("SELECT *
                                        FROM (`friends`)
                                        WHERE ( user_id = '{$user_id}' AND friend_id = '{$friend_id}' ) 
                                        OR ( friend_id = '{$user_id}' AND user_id = '{$friend_id}' )
                                        AND ( status = 'pending' OR status = 'active' )");

        return ($friendship->num_rows() > 0 ? $friendship->row_array() : FALSE);
    }
    
    // --------------------------------------------------------------------

    /**
     * New page
     *
     * New page description
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */ 
        
    public function remove_friend($user_id = 0, $friend_id = 0)
    {
        $set_inactive = array('status' => 'inactive');
        
        $this->db->where(array('user_id' => $user_id, 'friend_id' => $friend_id))->update('friends', $set_inactive);
        $this->db->where(array('user_id' => $friend_id, 'friend_id' => $user_id))->update('friends', $set_inactive);
    }
    
    // --------------------------------------------------------------------

    /**
     * New page
     *
     * New page description
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */ 
        
    public function accept_friend($user_id = 0, $friend_id = 0)
    {
        $set_active = array('status' => 'active', 'first_time_friendship' => 0);

        $this->db->where(array('user_id' => $user_id, 'friend_id' => $friend_id))->update('friends', $set_active);
        $this->db->where(array('user_id' => $friend_id, 'friend_id' => $user_id))->update('friends', $set_active);
    }
    
    // --------------------------------------------------------------------

    /**
     * New page
     *
     * New page description
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */ 
        
    public function end_friendship($friendship_id = 0)
    {
        $friendship_data = $this->db->get_where('friends', array('id' => $friendship_id));
        
        if($friendship_data->num_rows() > 0):
            $friendship = $friendship_data->row_array();
            $this->remove_friend($friendship['user_id'], $friendship['friend_id']);
        else:
            return FALSE;
        endif;
    }
    
    // --------------------------------------------------------------------

    /**
     * New page
     *
     * New page description
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */ 
        
    public function new_function()
    {
        
    }
    
}


/* End of file friend_engine.php */
/* Location: ./application/models/friend_engine.php */