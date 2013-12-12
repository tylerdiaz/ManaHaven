<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * World
 *
 * @author(s) 	Tyler Diaz
 * @version 	1.0
 **/

class World extends CI_Controller
{
    function __construct(){
        parent::__construct();
        // Custom constructor code

        // DO NOT PUT = if( ! $this->session->userdata('id')) redirect(site_url());
		// On the constructor, because our Cron job loads the script via the URL so it needs to bypass authentication.
    }

    public function index()
    {
		if( ! $this->session->userdata('id')) redirect(site_url());

        $this->load->model('battle_engine');
        $battle_data = $this->battle_engine->get_battle_data();

        // Are we healing HP or energy?
        if((time()-900) > $this->system->userdata['last_energy_heal']):
            $total_recovery_points = round((time()-$this->system->userdata['last_energy_heal'])/900);
            $new_energy = min(($this->system->userdata['energy']+$total_recovery_points), $this->system->userdata['max_energy']);

            $this->system->userdata['energy'] = $new_energy;

            $this->db->where('character_id', $this->system->userdata['character_id'])
                     ->update('characters', array('last_energy_heal' => time(), 'energy' => $new_energy));
        endif;

        if((time()-300) > $this->system->userdata['last_hp_heal']):
            $total_recovery_points = round((time()-$this->system->userdata['last_hp_heal'])/300);
            $new_hp = min(($this->system->userdata['hp']+$total_recovery_points), $this->system->userdata['max_hp']);

            $this->system->userdata['hp'] = $new_hp;

            $this->db->where('character_id', $this->system->userdata['character_id'])
                     ->update('characters', array('last_hp_heal' => time(), 'hp' => $new_hp));
        endif;

        // Get today's champion - Which was yesterday's warrior
        $champion = apc_fetch('todays_champion');
        if( ! apc_fetch('todays_champion')):
            $todays_champion = $this->db->select('*, SUM(jackpot) as total_jackpot, COUNT(battles.id) as total_fights')
                                        ->join('users', 'creator_id = users.id')
                                        ->where('battle_started_at >', (time()-82800))
                                        ->where('battle_finished', 1)
                                        ->group_by('creator_id')
                                        ->order_by('total_jackpot', 'desc')
                                        ->order_by('total_fights', 'desc')
                                        ->limit(1)
                                        ->get('battles');

            if($todays_champion->num_rows > 0){
                $todays_champion = $todays_champion->row_array();

                apc_store('todays_champion', array('user_id' => $todays_champion['id'], 'username' => $todays_champion['username']), 82800); // Every 23 hours
                $champion = array('user_id' => $todays_champion['id'], 'username' => $todays_champion['username']);

            } else {
                $champion = array('user_id' => 'avatar_default', 'username' => '...unknown');
            }
        endif;

        if(is_array($battle_data)):
            redirect('battle');
        else:
            $this->system->quick_parse('world/update', array('page_title' => 'Home', 'page_body' => 'world', 'champion' => $champion));
        endif;
    }

    public function create_multiplayer()
    {

    }


    public function multiplayer()
    {
		if( ! $this->session->userdata('id')) redirect(site_url());
		if( ! $this->system->is_staff()) return $this->system->quick_parse('general/development');

        $battle_party_member = $this->db->select('*')->join('battle_parties', 'battle_parties.id = battle_party_members.party_id')->where(array('user_id' => $this->session->userdata('id')))->get('battle_party_members');

        if($battle_party_member->num_rows() > 0){
            // You're currently inside a party! Let's redirect you towards it
        	$party_data = $battle_party_member->row_array();
            redirect('multiplayer/party/'.$party_data['id'].'/?key='.$party_data['party_key']);
        } else {
            // Create a new party
            $this->load->helper('string');

            $party_key = random_string('alnum', 10);
            $battle_parties = array(
                'party_key'          => $party_key,
                'author_id'          => $this->session->userdata('id'),
                'public_party'       => 0, // All parties are private at the start
                'invites_to'         => json_encode(array('')),
                'currently_battling' => 0
            );

            $this->db->insert('battle_parties', $battle_parties);
            $party_id = $this->db->insert_id();

            $battle_party_members = array(
                'user_id'      => $this->system->userdata['id'],
                'character_id' => $this->system->userdata['character_id'],
                'username'     => $this->system->userdata['username'],
                'last_update'  => time(),
                'party_id'     => $party_id
            );

            $this->db->insert('battle_party_members', $battle_party_members);

            // Redirect to new party
            redirect('multiplayer/party/'.$party_id.'/?key='.$party_key);
        }
    }

    public function singleplayer()
    {
		if( ! $this->session->userdata('id')) redirect(site_url());

		redirect('battle/start');
        // $this->system->quick_parse('world/singleplayer', array('page_title' => 'Home', 'page_body' => 'world'));
    }


    public function shop()
    {
		if( ! $this->session->userdata('id')) redirect(site_url());
        $shop_items = $this->db->query("SELECT shop_items.*, shop_items.id as shop_item_id
                                        FROM shop_items
                                        WHERE display_item = 1
                                        ORDER BY price DESC
                                        LIMIT 20")->result_array();

        $item_data = array();

        foreach ($shop_items as $key => $item):
            if($item['item_type'] == 'usable'):
                $item_data[$item['permenent_item']][$key] = $this->db->select('*')
                         ->from('utility_items')
                         ->where('id', $item['item_id'])
                         ->get()
                         ->row_array();

                $item_data[$item['permenent_item']][$key]['price'] = $item['price'];
                $item_data[$item['permenent_item']][$key]['shop_item_id'] = $item['shop_item_id'];
            else:
                $item_data[$item['permenent_item']][$key] = $this->db->select('*')
                         ->from('avatar_items')
                         ->where('item_id', $item['item_id'])
                         ->get()
                         ->row_array();

                $item_data[$item['permenent_item']][$key]['price'] = $item['price'];
                $item_data[$item['permenent_item']][$key]['shop_item_id'] = $item['shop_item_id'];
            endif;
        endforeach;

        //
        $item_data[0] = array_reverse($item_data[0]);

        $view_data = array('page_title' => 'Home', 'page_body' => 'world', 'location' => 'shops', 'items' => $item_data);

        if(is_ajax()):
            $this->load->view('world/shop', $view_data);
        else:
            $this->system->quick_parse('world/shop', $view_data);
        endif;
    }

    public function purchase_item()
    {
		if( ! $this->session->userdata('id')) redirect(site_url());
        $shop_item_id = $this->input->post('item_id');
        $shop_item = $this->db->select('*')->where(array('id' => $shop_item_id))->get('shop_items');

        if($shop_item->num_rows() > 0){
        	$shop_item_data = $shop_item->row_array();

        	if($this->system->userdata['gold'] >= $shop_item_data['price']){
            	$this->db->where('id', $this->session->userdata('id'))
            	         ->update('users', array('gold' => ($this->system->userdata['gold']-$shop_item_data['price'])));

                $character_items = array(
                	'item_id' => $shop_item_data['item_id'],
                	'character_id' => $this->system->userdata['character_id'],
                	'type' => $shop_item_data['item_type']
                );

                $this->db->insert('character_items', $character_items);

                echo json_encode(array('success' => 1, 'type' => $shop_item_data['item_type'], 'reduction' => $shop_item_data['price']));
        	}
        } else {

        }

    }

    public function learn_technique()
    {
        if( ! is_ajax()) show_error('You are not allowed to access this page');
        if( ! is_numeric($this->input->post('id'))) die('Not a valid skill id');

        $skill_id = $this->input->post('id');

        $skill = $this->db->where(array('public' => 1, 'id' => $skill_id, 'min_level_required <=' => $this->system->userdata['level']))->get('skills');

        if($skill->num_rows() > 0){
            $this->load->model('user_engine');
        	$skill_data = $skill->row_array();

            $character_skill_id = 0;
            $character_skill = $this->db->where(array('skill_id' => $skill_id, 'character_id' => $this->system->userdata['character_id']))->get('character_skills');

            if($character_skill->num_rows() > 0){
            	$character_skill_data = $character_skill->row_array();

                if($this->system->userdata['gold'] > $this->_algorithm('price', $character_skill_data['skill_level']+1, $skill_data['scale'])) {
                	// upgrading skill
                    $this->db->insert('character_learning_skills', array(
                        'user_id'      => $this->session->userdata('id'),
                        'skill_id'     => $skill_id,
                        'skill_level'  => $character_skill_data['skill_level']+1,
                        'time_started' => time(),
                        'finish_at'    => time()+$this->_algorithm('time', $character_skill_data['skill_level']+1, $skill_data['scale']),
                    ));

                    $this->user_engine->subtract_gold($this->_algorithm('price', $character_skill_data['skill_level']+1, $skill_data['scale']));

                    echo json_encode(array(
                        'response' => 'upgrading current skill'
                    ));
                } else {
                    // not enough gold!
                    show_error('Not enough gold to update this skill!');
                }
            } else {
                if($this->system->userdata['gold'] > $this->_algorithm('price', 1, $skill_data['scale'])) {
                	// learning new skill
                    $this->db->insert('character_learning_skills', array(
                        'user_id'      => $this->session->userdata('id'),
                        'skill_id'     => $skill_id,
                        'skill_level'  => 1,
                        'time_started' => time(),
                        'finish_at'    => time()+$this->_algorithm('time', 1, $skill_data['scale']),
                    ));

                    $this->user_engine->subtract_gold($this->_algorithm('price', 1, $skill_data['scale']));
                } else {
                    // not enough gold!
                    show_error('Not enough gold to learn this skill!');
                }
            }

        } else {
            // not enough gold!
            show_error('This skill could not be loaded!');
        }

    }

    public function finished_skill_learn()
    {
        $learning_skill = $this->db->select('finish_at, character_learning_skills.id as learning_id, skill_level, skills.target, scale, base_points, skills.id as skill_id')->join('skills', 'skills.id = character_learning_skills.skill_id')->get_where('character_learning_skills', array('user_id' => $this->system->userdata['id']));

		if($learning_skill->num_rows() > 0):
		    $skill = $learning_skill->row_array();

            if(time() > $skill['finish_at']):
                // Learn new skill
                $this->db->where('id', $skill['learning_id'])->limit(1)->delete('character_learning_skills');

                if($skill['skill_level'] == 1):
                    // Create a new skill!
                    $damage = $this->_algorithm('damage', 1, $skill['scale'], array('base' => $skill['base_points']));

                    $character_skills = array(
                        'skill_id'     => $skill['skill_id'],
                        'character_id' => $this->system->userdata['character_id'],
                        'skill_level'  => 1,
                        'min_damage'   => $damage['min'],
                        'max_damage'   => $damage['max']
                    );

                    $this->db->insert('character_skills', $character_skills);
                else:
                    if($skill['target'] == 'monster' || $skill['target'] == 'all_monsters'){
                        $damage = $this->_algorithm('damage', $skill['skill_level'], $skill['scale'], array('base' => $skill['base_points']));
                    } else {
                        $damage = $this->_algorithm('heal', $skill['skill_level'], $skill['scale'], array('base' => $skill['base_points']));
                    }

                    $character_skills = array(
                        'skill_id'     => $skill['skill_id'],
                        'character_id' => $this->system->userdata['character_id'],
                        'skill_level'  => $skill['skill_level'],
                        'min_damage'   => $damage['min'],
                        'max_damage'   => $damage['max']
                    );

                    $this->db->where(array('skill_id' => $skill['skill_id'], 'character_id' => $this->system->userdata['character_id']))->update('character_skills', $character_skills);
                endif;
                return TRUE;
            endif;
		else:
    		return FALSE;
        endif;

    }


    public function techniques()
    {
		if( ! $this->session->userdata('id')) redirect(site_url());

		$learning_skill = $this->db->join('skills', 'skills.id = character_learning_skills.skill_id')->get_where('character_learning_skills', array('public' => 1, 'user_id' => $this->system->userdata['id']));

		if($learning_skill->num_rows() > 0 && $this->finished_skill_learn() == FALSE):
		    $skill = $learning_skill->row_array();

		    $view_data = array('page_title' => 'Techniques', 'location' => 'technique', 'page_body' => 'world', 'skill' => $skill);

		    if(is_ajax()):
                $this->load->view('world/learning_technique', $view_data);
            else:
                $this->system->quick_parse('world/learning_technique', $view_data);
            endif;
		else:
    		$techniques = $this->db->where('public', 1)->get('skills')->result_array();
            $list_character_skills = $this->db->get_where('character_skills', array('character_id' => $this->system->userdata['character_id']))->result_array();

            $character_skills = array();
            foreach ($list_character_skills as $skill):
                $character_skills[$skill['skill_id']] = $skill;
            endforeach;

            foreach ($techniques as $key => $technique):
                if (isset($character_skills[$technique['id']])) {
                    $techniques[$key]['time'] = $this->_algorithm('time', $character_skills[$technique['id']]['skill_level']+1, $technique['scale']);
                    $techniques[$key]['price'] = $this->_algorithm('price', $character_skills[$technique['id']]['skill_level']+1, $technique['scale']);
                } else {
                    $techniques[$key]['time'] = $this->_algorithm('time', 1, $technique['scale']);
                    $techniques[$key]['price'] = $this->_algorithm('price', 1, $technique['scale']);
                }
            endforeach;

            $view_data = array('page_title' => 'Techniques', 'page_body' => 'world', 'location' => 'technique', 'character_skills' => $character_skills, 'techniques' => $techniques);

            if(is_ajax()):
                $this->load->view('world/techniques', $view_data);
            else:
                $this->system->quick_parse('world/techniques', $view_data);
            endif;
        endif;
    }

    private function _algorithm($algorithm, $skill_level, $scale, $extra_data = array())
    {
        switch ($algorithm) {
            case 'time':
                return (pow(5, ($scale))+($skill_level+1)*15)*35; // {level:10, scale:1} = (ceil(1 ^ 5)+(11*15))*35
            break;
            case 'price':
                return (ceil(pow(6, $scale)+($skill_level*30)/5)*5)+($skill_level*50); // {level:10, scale:1} = (ceil((1 ^ 6)+(10*30)/5)*5)+(10*15)
            break;
            case 'damage':
                $odd_level = ($skill_level % 2);
                $max = floor($skill_level/2)+($extra_data['base']+($odd_level ? 0 : 2));
                $min = floor($skill_level/2)+($extra_data['base']+($odd_level ? 2 : 0));
                if($max > $min):
                    return array('min' => $min, 'max' => $max);
                else:
                    return array('max' => $min, 'min' => $max);
                endif;
            break;
            case 'heal':
                $odd_level = ($skill_level % 2);
                $max = floor($skill_level/2)+($extra_data['base']+($odd_level ? 0 : 2));
                $min = floor($skill_level/2)+($extra_data['base']+($odd_level ? 2 : 0));
                if($max > $min):
                    return array('min' => $min, 'max' => $max);
                else:
                    return array('max' => $min, 'min' => $max);
                endif;
            break;
        }
    }


    public function flush_shop_items()
    {
        // Reset items
        $this->db->where(array('display_item' => 1, 'permenent_item' => 0))->update('shop_items', array('display_item' => 0));

        $basic_items = $this->db->query("UPDATE shop_items
                                         SET display_item = 1
                                         WHERE display_item = 0
                                         AND permenent_item = 0
                                         AND price < 300
                                         ORDER BY RAND()
                                         LIMIT 6");

        $advanced_items = $this->db->query("UPDATE shop_items
                                            SET display_item = 1
                                            WHERE display_item = 0
                                            AND permenent_item = 0
                                            AND price < 900
                                            AND price > 300
                                            ORDER BY RAND()
                                            LIMIT 4");

        $advanced_items = $this->db->query("UPDATE shop_items
                                            SET display_item = 1
                                            WHERE display_item = 0
                                            AND permenent_item = 0
                                            AND price > 900
                                            ORDER BY RAND()
                                            LIMIT 2");

    }

    public function character()
    {
        $view_data = array('location' => 'character_sheet');

        if(is_ajax()):
            $this->load->view('world/character', $view_data);
        else:
            $this->system->quick_parse('world/character', $view_data);
        endif;
    }

    public function save_character_changes()
    {
        $total_points = 0;

        foreach ($this->input->post('upgrade_skills') as $attribute => $amount):
            $total_points = ($total_points+$amount);
            if( ! is_numeric($amount)) die(json_encode(array('error', 'Not a valid skill point amount')));

            $update_data[$attribute] = $this->system->userdata[$attribute]+$amount;
            $this->system->userdata[$attribute] = $update_data[$attribute];

            if($attribute == "strength" && $amount > 0):
                $update_data['max_weight'] = ($this->system->userdata['max_weight']+($amount*4));
                $update_data['max_hp'] = ($this->system->userdata['max_hp']+($amount*2));
                $update_data['hp'] = $update_data['max_hp'];
            endif;
        endforeach;

        if($total_points > $this->system->userdata['skill_points']) die(json_encode(array('error', 'Not enough skill points')));

        $update_data['skill_points'] = ($this->system->userdata['skill_points']-$total_points);
        $this->db->where('character_id', $this->system->userdata['character_id'])->update('characters', $update_data);

        echo json_encode(array('success' => 1));
    }

}

/* End of file World.php */
/* Location: ./system/application/controllers/World.php */