<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User Engine Model
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 **/

class User_engine extends CI_Model
{
	function __construct()
	{
        parent::__construct();
	}


	public function get_userdata($username = "")
	{
		return $this->db->select('*')->from('users')->where('username', $username)->get()->row_array();
	}

    function update_userdata($main_key = '', $value = '')
    {
		if(is_array($main_key)):
			$this->db->where('id', $this->session->userdata('id'))->update('users', $main_key);
		else:
			$this->db->where('id', $this->session->userdata('id'))->update('users', array($main_key => $value));
		endif;
    }

    public function add_gold($gold_amount = 0, $users = 0)
    {
        if($users == 0) $users = $this->system->userdata['id'];

        if(is_array($users)):
            $this->db->query('UPDATE users SET gold = (gold+'.$gold_amount.') WHERE id IN ('.implode($users, ',').')');
        else:
            if($users == $this->system->userdata['id']):
                $this->db->where('id', $this->system->userdata['id'])->update('users', array('gold' => ($this->system->userdata['gold']+$gold_amount)));
            else:
                $this->db->query('UPDATE users SET gold = (gold+'.$gold_amount.') WHERE id = '.$users);
            endif;
        endif;

        return $gold_amount;
    }

    public function subtract_gold($gold_amount = 0, $users = 0)
    {
        if($users == 0) $users = $this->system->userdata['id'];

        if(is_array($users)):
            $this->db->query('UPDATE users SET gold = (gold-'.$gold_amount.') WHERE id IN ('.implode($users, ',').')');
        else:
            if($users == $this->system->userdata['id']):
                $this->db->where('id', $this->system->userdata['id'])->update('users', array('gold' => ($this->system->userdata['gold']-$gold_amount)));
            else:
                $this->db->query('UPDATE users SET gold = (gold+'.$gold_amount.') WHERE id = '.$users);
            endif;
        endif;

        return $gold_amount;
    }

    public function check_for_levelup($user_id = 0)
    {
        $character = $this->db->get_where('characters', array('user_id' => $user_id))->row_array();

        if($character['exp'] >= $character['next_level_exp']):
            $new_level = $this->db->limit(1)->order_by('id', 'desc')->get_where('character_templates', array('exp_required <=' => $character['exp']))->row_array();
            $next_level = $this->db->limit(1)->get_where('character_templates', array('level' => $new_level['level']+1))->row_array();

            // DONE: How many level ups did you get?
            $total_level_ups = ($new_level['level']-$character['level']);

            $character_data = array(
                'skill_points'   => ($new_level['points']+$character['skill_points']),
                'next_level_exp' => $next_level['exp_required'],
                'level'          => $new_level['level']
            );

            $new_level_data = array(
                'total_level_ups'  => $total_level_ups,
                'increase_exp_to'  => $character['exp'],
                'new_level'        => $new_level['level'],
                'new_exp_required' => $next_level['exp_required']
            );

            $this->db->where('character_id', $character['character_id'])->update('characters', $character_data);

            return $new_level_data;
        else:
            return FALSE;
        endif;
    }

}


/* End of file system.php */
/* Location: ./system/application/models/user_engine.php */