<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Battle
 *
 * @author(s) 	Tyler Diaz
 * @version 	1.0
 **/

class Battle extends CI_Controller
{
    function __construct(){
        parent::__construct();
        $this->load->model('battle_engine');
    }

    public function index()
    {
		if( ! $this->session->userdata('id')) redirect(site_url());

        $battle_data = $this->battle_engine->get_battle_data();
        if(is_array($battle_data)):

            if(apc_fetch('battle_cap_'.$battle_data['id']) === TRUE):
                apc_store('battles_initiated_'.$battle_data['id'], time(), 750); // Reset the security lock as well!
                foreach ($battle_data['monsters'] as $key => $monster):
                    $monster['monster_data']['hp'] = $monster['monster_data']['max_hp'];
                    $monster['ready_at'] = 0;
                    $battle_data['monsters'][$key] = $monster;
                    $monster['monster_data'] = serialize($monster['monster_data']);
                    $this->db->where('id', $monster['id'])->update('battle_monsters', $monster);
                endforeach;
            else:
                apc_store('battle_cap_'.$battle_data['id'], TRUE, (60*60));
            endif;

            if(isset($battle_data['monsters'])):
                // Battle has been won, what are you doing here?
            endif;

            $this->load->library('user_agent'); // Good for checking compatibility

            $offset = 300;
            foreach ($battle_data['monsters'] as $key => $monster):
                $offset += 50;
                $monster_data = $monster['monster_data'];
                $js_monsters[$key] = array(
                    'id'                => $monster['id'],
                    'battle_monster_id' => $monster['id'],
                    'monster_id'        => $monster_data['id'],
                    'name'              => $monster_data['name'],
                    'hp'                => $monster_data['hp'],
                    'max_hp'            => $monster_data['max_hp'],
                    'image'             => $monster_data['image'],
                    'offset'            => $offset,
                    'animation_frame'   => $monster_data['animation_frame'],
                    'hp_percent'        => percent($monster_data['hp'], $monster_data['max_hp'])
                );
            endforeach;

            $formatted_skills = array();

            foreach ($battle_data['character']['character_data']['skills'] as $key => $skill):
                $formatted_skills[$key] = array(
                    'id'          => $skill['id'],
                    'icon'        => $skill['icon'],
                    'name'        => $skill['name'],
                    'target_type' => $skill['target'],
                );

                switch($skill['target']):
                    case 'character':
                        $formatted_skills[$key]['description'] = $skill['min_damage'].'-'.$skill['max_damage'].' heal';
                    break;
                    case 'monster':
                        $formatted_skills[$key]['description'] = $skill['min_damage'].'-'.$skill['max_damage'].' damage';
                    break;
                    case 'all_character':
                        $formatted_skills[$key]['description'] = $skill['min_damage'].'-'.$skill['max_damage'].' +heal';
                    break;
                    case 'all_monsters':
                        $formatted_skills[$key]['description'] = $skill['min_damage'].'-'.$skill['max_damage'].' +damage';
                    break;
                endswitch;
            endforeach;

            $view_data = array(
                'page_title'      => 'Battling',
                'page_body'       => 'world',
                'js_monsters'     => $js_monsters,
                'monsters'        => $battle_data['monsters'],
                'my_skills'       => $formatted_skills,
                'my_items'        => $battle_data['characters'][$this->system->userdata['character_id']]['character_data']['items'],
                'my_character_id' => $this->system->userdata['character_id'],
                'battle_logs'     => unserialize($battle_data['battle_logs']),
                'battle'          => $battle_data
            );

            $this->system->quick_parse('battle/index', $view_data);
        else:
            redirect('world/index');
        endif;
    }

    public function start()
    {
		if( ! $this->session->userdata('id')) redirect(site_url());

        $battle_data = $this->battle_engine->get_battle_data();

        if($battle_data !== FALSE) show_error('You are currently in a battle!');

        $character = $this->db->get_where('characters', array('user_id' => $this->session->userdata('id')));

        if($character->num_rows() > 0):
            $character_data = $this->battle_engine->format_character_data($character->row_array());
        else:
        	die('Oops, we couldn\'t find your character, report this to a developer so we can get this attended right away!');
        endif;

        // Cancel all other battles!
        $this->db->where('creator_id', $this->session->userdata('id'))
                 ->where('progress_lock', 1)
                 ->update('battles', array('progress_lock' => 1));

        $start_wave = 1;

        if($this->system->userdata['energy'] < 1) show_error('You do not have enough energy to battle!');
        if($this->system->userdata['hp'] < 1) show_error('You do not have enough health to battle!');

        $battle_wave = $this->db->select('*')->where(array('wave_number' => $start_wave))->get('battle_waves');

        if($battle_wave->num_rows() > 0):
        	$battle_wave_data = $battle_wave->row_array();
        else:
        	// battle_wave not found...
        	show_error('This battle template has not been built yet!');
        endif;

        // Let's get this battle started!
        $battles = array(
        	'creator_id' => $this->session->userdata('id'),
        	'battle_finished' => 0,
        	'battle_type' => 'singleplayer',
        	'wave_level' => $start_wave,
        	'jackpot' => 0,
        	'battle_started_at' => time()
        );

        $this->db->insert('battles', $battles);
        $battle_id = $this->db->insert_id();

        // Some security patches!
        apc_store('total_attacks_'.$battle_id, 0, 3600);
        apc_store('battles_initiated_'.$battle_id, time(), 750);

        // Reduce and energy when you start a new wave set!
        $this->db->where('character_id', $this->system->userdata['character_id'])
                 ->update('characters', array('energy' => ($this->system->userdata['energy']-1)));

        // Alright, battle is ready to go, let's dive in to the character(s)
        $battle_characters = array(
            'character_id'       => $this->system->userdata['character_id'],
            'character_data'     => serialize($character_data),
            'battle_id'          => $battle_id,
            'battle_team'        => 'left',
            'character_username' => $this->system->userdata['username'],
            'character_user_id'  => $this->system->userdata['id'],
            'active_battle'      => 1,
        );

        $this->db->insert('battle_characters', $battle_characters);

        // Characters are set. Let's go for the monsters now!
        $load_monsters = unserialize($battle_wave_data['monsters']);

        foreach($load_monsters as $monster_id):
            $monster = $this->db->select('*')->where(array('id' => $monster_id))->get('monsters');
            $total_exp = 0;

            if($monster->num_rows() > 0):
            	$monster_data = $monster->row_array();
                $monster_data['max_hp'] = $monster_data['hp'];
                $total_exp += $monster_data['exp'];

            	$battle_monsters = array(
                    'monster_id'   => $monster_id,
                    'monster_data' => serialize($monster_data),
                    'battle_id'    => $battle_id,
                    'battle_team'  => 'right',
                    'reload_time'  => $monster_data['reload_time']
            	);

            	$this->db->insert('battle_monsters', $battle_monsters);
            else:
            	// monster not found...
            endif;
        endforeach;

        redirect('battle');
    }

    public function use_skill()
    {
		if( ! $this->session->userdata('id')) die(json_encode(array('error' => 1)));

        if( ! $this->battle_engine->battle_data_loaded) $battle = $this->battle_engine->get_battle_data();

        $skill_id = $this->input->post('skill_id');
        $target_id = $this->input->post('target_id');
        $char_lock_key = 'character_'.$this->system->userdata['character_id'].'_lock_time';

        $battles_restarted_time = apc_fetch('battles_initiated_'.$battle['id']);

        if( ! $battles_restarted_time):
            apc_store('battles_initiated_'.$battle['id'], time(), 750);
            $battles_restarted_time = time();
        endif;

        $total_attacks = apc_fetch('total_attacks_'.$battle['id']);
        $security_second_cap = floor((time()-$battles_restarted_time)/14); // Make sure there has been attacks every 15 seconds!

        if($total_attacks < $security_second_cap):
            $this->load->library('user_agent');

            $action_logs = array(
                'username' => $this->system->userdata['username'],
                'user_id'  => $this->system->userdata['id'],
                'userdata' => serialize($this->system->userdata),
                'ip'       => $this->input->ip_address(),
                'time'     => time(),
                'type'     => 'data',
                'browser'  => $this->agent->browser().' - '.$this->agent->version(),
                'action'   => 'red_flag_battle',
                'data'     => json_encode($battle)
            );

            $this->db->insert('action_logs', $action_logs);

            $battle['characters'][$this->system->userdata['character_id']]['character_data']['hp'] -= 10;

            if($battle['characters'][$this->system->userdata['character_id']]['character_data']['hp'] <= 0):
                $this->battle_engine->set_winner('monster');
            endif;

            $this->db->where('character_id', $this->system->userdata['character_id'])
                     ->where('battle_id', $battle['id'])
                     ->update('battle_characters', array('character_data' => serialize($battle['characters'][$this->system->userdata['character_id']]['character_data'])));

            if(is_ajax()):
                die(json_encode(array('error' => 39)));
            else:
                show_error('Monster actions not rendering correctly. Pixeltweak has been notified and will get to this as soon as he\'s available!');
            endif;
        endif;

        if(time() < apc_fetch($char_lock_key)):
            die(json_encode(array('error' => 7), JSON_NUMERIC_CHECK)); // Not ready yet!
        else:
            // Waaay to much functionality hidden in here... I should get back in here
            // and start cleaning this up when I get the chance. @see use_item for reference
            $this->battle_engine->use_skill($skill_id, $target_id);

            apc_store('battle_lock_'.$this->system->userdata['character_id'], FALSE, 10); // Unlock the loop key

            if($battle['battle_type'] == 'multiplayer'):
                $this->battle_engine->_push_event($this->battle_engine->response);
            endif;

            $seconds_to_wait = floor($this->battle_engine->response['waiting_time']/1000);

            apc_store($char_lock_key, (time()+$seconds_to_wait), 120);

            echo json_encode($this->battle_engine->response, JSON_NUMERIC_CHECK);
        endif;
    }

    public function use_item()
    {
		if( ! $this->session->userdata('id')) die(json_encode(array('error' => 1)));
        if( ! $this->battle_engine->battle_data_loaded) $battle = $this->battle_engine->get_battle_data();

        // Do I own this item, and the right amount of it to use it?
        foreach ($battle['character']['character_data']['items'] as $key => $item):
            if($item['item_id'] == $this->input->post('item_id') && $item['amount'] > 0):

                // Delete the item from our real inventory
                $this->db->where('item_id', $item['item_id'])
                         ->where('character_id', $this->system->userdata['character_id'])
                         ->where('type', 'usable')
                         ->limit(1)
                         ->delete('character_items');

                $battle['character']['character_data']['items'][$key]['amount'] -= 1;

                // Now from our fake one! :D
                if(($item['amount']-1) > 0):
                    // Update the new value
                    $this->db->where('character_id', $this->system->userdata['character_id'])
                             ->where('battle_id', $this->battle_engine->battle['id'])
                             ->update('battle_characters', array('character_data' => serialize($battle['character']['character_data'])));
                else:
                    // Remove the item from the index entirely
                    unset($battle['character']['character_data']['items'][$key]);
                    $this->db->where('character_id', $this->system->userdata['character_id'])
                             ->where('battle_id', $this->battle_engine->battle['id'])
                             ->update('battle_characters', array('character_data' => serialize($battle['character']['character_data'])));
                endif;

                $this->battle_engine->use_item($item['item_id'], $item);
                apc_store('battle_lock_'.$this->system->userdata['character_id'], FALSE, 10); // Unlock the loop key

                if($battle['battle_type'] == 'multiplayer'):
                    $this->battle_engine->_push_event($this->battle_engine->response);
                endif;

                echo json_encode($this->battle_engine->response, JSON_NUMERIC_CHECK);
            endif;
        endforeach;
    }

    public function monster_turn($monster_id = 0)
    {
		if( ! $this->session->userdata('id')) die(json_encode(array('error' => 1)));
        if( ! is_numeric($monster_id)) show_error('monster_id must be valid');

        $monster_id = ($this->input->post('monster') ? $this->input->post('monster') : $monster_id);
        $reload_time = apc_fetch('reload_at_'.$monster_id);

        // Hot patch for security, this can be removed later.
        if($monster_id < 120000):
            $battle['characters'][$this->system->userdata['character_id']]['character_data']['hp'] -= 10;

            if($battle['characters'][$this->system->userdata['character_id']]['character_data']['hp'] <= 0):
                $this->battle_engine->set_winner('monster');
            endif;
            show_error('Expired monster ID');
        endif;

        if($reload_time > time()) (is_ajax() ? die(json_encode(array('error' => 4), JSON_NUMERIC_CHECK)) : show_error('Not ready yet!'));

        if( ! is_ajax()) show_error('You are not allowed to access this page');

		if(apc_fetch('battle_lock_'.$this->system->userdata['character_id'])):
		    $start_lag_time = time();
            while(apc_fetch('battle_lock_'.$this->system->userdata['character_id']) === TRUE && (time()-$start_lag_time) < 2):
                if(apc_fetch('battle_lock_'.$this->system->userdata['character_id']) !== TRUE) break;
                usleep(10000); // 30 MilliSeconds
            endwhile;
		endif;

		apc_store('battle_lock_'.$this->system->userdata['character_id'], TRUE, 10); // Lock the loop key

        if( ! $this->battle_engine->battle_data_loaded) $battle =& $this->battle_engine->get_battle_data();

        if(isset($battle['id'])){
            if(isset($battle['monsters'][$monster_id])){

                $battle_monster_data = $battle['monsters'][$monster_id];

                // Which skill are we going to use?
                $skill_list = unserialize($battle_monster_data['monster_data']['techniques']);
                $chosen_skill = array_rand($skill_list);

            	// Who is the monster going to attack?
            	$target_character = array_rand($battle['characters']);

                // The lowest ID is 13, which is Pixeltweak's
                if($target_character < 13):
                    $target_character = $this->system->userdata['character_id'];
                endif;

                // We cap the agility at 5 points, we just don't let players know that (trolface)
                $character_data = $this->battle_engine->battle['characters'][$target_character]['character_data'];

                if( ! is_array($character_data)):
                    $this->output->set_content_type('application/json')
                                 ->set_output(json_encode(array('debug' => $battle['characters'], 'target' => $target_character, 'data' => $character_data), JSON_NUMERIC_CHECK));
                endif;

                $character_agility = min($character_data['agility']*5, 25);

                $miss_chance = (mt_rand(0, 100) > (90-$character_agility));
                $critical_chance = (mt_rand(0, 100) > 95);

                $damage = mt_rand($skill_list[$chosen_skill]['min'], $skill_list[$chosen_skill]['max']);
                if($critical_chance) $damage = $skill_list[$chosen_skill]['max'];

                // Check for the defense bonus!
                $damage -= floor($character_data['defense'] * (rand()/getrandmax()*1));
                $damage = max($damage, 1);

                $this->battle_engine->response = array(
                    'caster_type'   => 'monster', // player, monster
                    'response_type' => 'animate', // animate, loss, win, item, escape
                    'caster_id'     => $monster_id,
                    'waiting_time'  => $skill_list[$chosen_skill]['reload'],
                    'animate_data'  => array(
                        'target'        => 'player',
                        'target_id'     => $target_character, // Should also accepts arrays, but not yet...
                        'amount_type'   => 'negative', // negative, positive
                        'amount'        => $damage,
                        'description'   => $battle_monster_data['monster_data']['name'].' used '.$skill_list[$chosen_skill]['skill_name'].'!',
                        'animation_key' => $skill_list[$chosen_skill]['animation_key'],
                        'critical'      => $critical_chance,
                        'miss'          => $miss_chance
                    )
                );

        	    // Update the amount of attacks!
        	    if(apc_fetch('total_attacks_'.$battle['id']) > 0):
                    apc_inc('total_attacks_'.$battle['id']);
                else:
                    apc_store('total_attacks_'.$battle['id'], 1, 3600);
                endif;

            	if( ! $miss_chance):
                    apc_store('reload_at_'.$monster_id, (time()+floor($skill_list[$chosen_skill]['reload'])/1000), 120); // time in miliseconds, /1000 = converts to seconds
                    $this->battle_engine->deplete_hp('player', $target_character, $damage, $monster_id, $critical_chance);
            	endif;

        		apc_store('battle_lock_'.$this->system->userdata['character_id'], FALSE, 2); // Unlock the loop key
            	if($battle['battle_type'] == 'multiplayer'):
                    $this->battle_engine->_push_event($this->battle_engine->response);
                endif;

            	echo json_encode($this->battle_engine->response, JSON_NUMERIC_CHECK);
            } else {
        		apc_store('battle_lock_'.$this->system->userdata['character_id'], FALSE, 2); // Unlock the loop key
            	echo json_encode(array('error' => 3), JSON_NUMERIC_CHECK);
            }
        } else {
    		apc_store('battle_lock_'.$this->system->userdata['character_id'], FALSE, 2); // Unlock the loop key
        	echo json_encode(array('error' => 2), JSON_NUMERIC_CHECK);
        }

    }

    public function level_up_debug()
    {
        $new_exp = $this->system->userdata['exp']+10;
        $new_level = $this->db->limit(1)->order_by('id', 'desc')->get_where('character_templates', array('exp_required <=' => $new_exp))->row_array();
        $next_level = $this->db->limit(1)->get_where('character_templates', array('level' => $new_level['level']+1))->row_array();

        if($new_level['level'] > $this->system->userdata['level']){
            echo "level up from ".$this->system->userdata['level']." to ".$new_level['level'];
        }
    }


    public function move_forward()
    {
        if( ! is_ajax()) die('Permission denied');

        // This should cap rapid-clicking lag issues
        if(apc_fetch('quick_lock_'.$this->system->userdata['character_id']) === TRUE) show_error('Action temporarily locked!');
        apc_store('quick_lock_'.$this->system->userdata['character_id'], TRUE, 2);

        $recent_battle = $this->db->order_by('id', 'desc')
                                  ->limit(1)
                                  ->where('took_jackpot', 0)
                                  ->where('progress_lock', 0)
                                  ->where('battle_finished', 1)
                                  ->where('creator_id', $this->session->userdata('id'))
                                  ->get('battles')
                                  ->row_array();

        // Get their latest battle. Nullify it, and start on this new one.
        $this->db->where('id', $recent_battle['id'])->update('battles', array('took_jackpot' => 0, 'progress_lock' => 1));
        $battle_wave = $this->db->select('*')->where(array('wave_number' => $recent_battle['wave_level']+1))->get('battle_waves');

        if($battle_wave->num_rows() > 0):
        	$battle_wave_data = $battle_wave->row_array();
        else:
            show_error('Battle wave not found...');
        endif;

        $character_data = $this->format_character_data();

        // Let's get this battle started!
        $battles = array(
        	'creator_id' => $this->session->userdata('id'),
        	'battle_finished' => 0,
            'battle_type' => 'singleplayer',
            'wave_level' => $recent_battle['wave_level']+1,
        	'jackpot' => $recent_battle['jackpot'],
        	'battle_started_at' => time()
        );

        $this->db->insert('battles', $battles);
        $battle_id = $this->db->insert_id();

        // Security locks!
        apc_store('total_attacks_'.$battle_id, 0, 3600);
        apc_store('battles_initiated_'.$battle_id, time(), 750);

        // Alright, battle is ready to go, let's dive in to the character(s)
        $battle_characters = array(
        	'character_id' => $this->system->userdata['character_id'],
        	'character_data' => serialize($character_data),
        	'battle_id' => $battle_id,
        	'battle_team' => 'left',
        	'character_username' => $this->system->userdata['username'],
        	'character_user_id' => $this->system->userdata['id'],
        	'active_battle' => 1
        );

        $this->db->insert('battle_characters', $battle_characters);

        // Characters are set. Let's go for the monsters now!
        $load_monsters = unserialize($battle_wave_data['monsters']);

        foreach($load_monsters as $monster_id):
            $monster = $this->db->select('*')->where(array('id' => $monster_id))->get('monsters');
            $total_exp = 0;

            if($monster->num_rows() > 0){
            	$monster_data = $monster->row_array();
                $monster_data['max_hp'] = $monster_data['hp'];
                $total_exp += $monster_data['exp'];

            	$battle_monsters = array(
            		'monster_id' => $monster_id,
            		'monster_data' => serialize($monster_data),
            		'battle_id' => $battle_id,
            		'battle_team' => 'right',
            		'reload_time' => $monster_data['reload_time']
            	);

            	$this->db->insert('battle_monsters', $battle_monsters);

            } else {
            	echo (json_encode(array('error' => "Monster not found:: ".$monster_id), JSON_NUMERIC_CHECK));
            }
        endforeach;

        // Creating the JS array view!
        $battle_data = $this->battle_engine->get_battle_data();
        $js_monsters = array();

        foreach ($battle_data['monsters'] as $monsters):
            $js_monsters[] = array(
                'recoil' => $monsters['reload_time'],
                'monster_id' => $monsters['monster_id'],
                'battle_monster_id' => $monsters['id']
            );
        endforeach;

        $offset = 300;
        foreach ($battle_data['monsters'] as $key => $monster):
            $offset += 50;
            $monster_data = $monster['monster_data'];
            $battle_data['monsters'][$key] = array(
                'id' => $monster['id'],
                'battle_monster_id' => $monster['id'],
                'monster_id' => $monster_data['id'],
                'name' => $monster_data['name'],
                'hp' => $monster_data['hp'],
                'max_hp' => $monster_data['max_hp'],
                'image' => $monster_data['image'],
                'offset' => $offset,
                'animation_frame' => $monster_data['animation_frame'],
                'hp_percent' => percent($monster_data['hp'], $monster_data['max_hp'])
            );
        endforeach;

        // Wrap up new data. Pack it in a json array. And spew it out!
        $view_data = array(
            'wave' => $battle_data['wave_level'],
            'total_monsters' => count($battle_data['monsters']),
            'js_monsters' => $js_monsters,
            'monsters' => $battle_data['monsters']
        );

        if(is_ajax()):
    	    die(json_encode($view_data, JSON_NUMERIC_CHECK));
        else:
            redirect('battle');
        endif;
    }

    public function format_character_data()
    {
        $character = $this->db->get_where('characters', array('user_id' => $this->session->userdata('id')));

        if($character->num_rows() > 0):
            $character_data = $this->battle_engine->format_character_data($character->row_array());
        else:
        	die('Oops, we couldn\'t find your character, report this to a developer so we can get this attended right away!');
        endif;

        return $character_data;
    }

    public function leave_battles()
    {
        if( ! is_ajax()) die('Permission denied');

        // This should cap rapid-clicking lag issues
        if(apc_fetch('quick_lock_'.$this->system->userdata['character_id']) === TRUE) show_error('Action temporarily locked!');
        apc_store('quick_lock_'.$this->system->userdata['character_id'], TRUE, 2);

        $recent_battle = $this->db->order_by('id', 'desc')
                                  ->limit(1)
                                  ->where('took_jackpot', 0)
                                  ->where('progress_lock', 0)
                                  ->where('battle_finished', 1)
                                  ->where('battle_finished_at >', (time()-900))
                                  ->where('winning_side', 'left')
                                  ->where('creator_id', $this->session->userdata('id'))
                                  ->get('battles')
                                  ->row_array();

        $this->db->where('id', $recent_battle['id'])->update('battles', array('took_jackpot' => 1, 'progress_lock' => 1));
        $this->db->where('id', $this->session->userdata('id'))->update('users', array('gold' => ($this->system->userdata['gold']+$recent_battle['jackpot'])));

        // Disable this little feature for now, we'll tinker based on feedback
        $bonus_energy = min(($this->system->userdata['energy']+1), $this->system->userdata['max_energy']);
        $this->db->where('user_id', $this->session->userdata('id'))->update('characters', array('energy' => $bonus_energy));

        echo json_encode(array('jackpot_gold' => $recent_battle['jackpot']), JSON_NUMERIC_CHECK);
    }

    public function setup_monsters()
    {
        $this->db->truncate('monsters');
        $monsters = array(
            array(
            	'name' => 'Forest Rat',
            	'hp' => 8,
            	'level' => 1,
            	'attack' => 1,
            	'defense' => 1,
            	'agility' => 3,
            	'exp' => 5,
            	'image' => 'sewerrat.png',
            	'animation_frame' => 'rat',
            	'techniques' => serialize(array(
                    array(
                        'use_chance' => 100,
                        'skill_name' => 'Bite',
                        'animation_key' => 'bite',
                        'max' => 4,
                        'min' => 1,
                        'reload' => 4500,
                        'target' => 'single',
                    ),
            	))
            ),
            array(
            	'name' => 'Sunlight Bat',
            	'hp' => 6,
            	'level' => 1,
            	'attack' => 1,
            	'defense' => 1,
            	'agility' => 3,
            	'exp' => 8,
            	'image' => 'bat.png',
            	'animation_frame' => 'bat',
            	'techniques' => serialize(array(
                    array(
                        'use_chance' => 100,
                        'skill_name' => 'Bite',
                        'animation_key' => 'bite',
                        'max' => 4,
                        'min' => 1,
                        'reload' => 4000,
                        'target' => 'single',
                    ),
            	))
            ),
            array(
            	'name' => 'Zombie Bat',
            	'hp' => 9,
            	'level' => 2,
            	'attack' => 1,
            	'defense' => 2,
            	'agility' => 5,
            	'exp' => 12,
            	'image' => 'zombiebat.png',
            	'animation_frame' => 'bat',
            	'techniques' => serialize(array(
                    array(
                        'use_chance' => 100,
                        'skill_name' => 'Bite',
                        'animation_key' => 'bite',
                        'max' => 5,
                        'min' => 2,
                        'reload' => 4000,
                        'target' => 'single',
                    ),
            	))
            ),
            array(
            	'name' => 'Water Slime',
            	'hp' => 40,
            	'level' => 2,
            	'attack' => 1,
            	'defense' => 1,
            	'agility' => 2,
            	'exp' => 18,
            	'image' => 'waterslime.png',
            	'animation_frame' => 'slime',
            	'techniques' => serialize(array(
                    array(
                        'use_chance' => 100,
                        'skill_name' => 'Ground snap',
                        'animation_key' => 'bounce',
                        'max' => 40,
                        'min' => 5,
                        'reload' => 6500,
                        'target' => 'single',
                    ),
            	))
            ),
            array(
            	'name' => 'Poison Slime',
            	'hp' => 160,
            	'level' => 2,
            	'attack' => 3,
            	'defense' => 2,
            	'agility' => 2,
            	'exp' => 30,
            	'image' => 'slime.png',
            	'animation_frame' => 'slime',
            	'techniques' => serialize(array(
                    array(
                        'use_chance' => 90,
                        'skill_name' => 'Ground snap',
                        'animation_key' => 'bounce',
                        'max' => 60,
                        'min' => 10,
                        'reload' => 5500,
                        'target' => 'single',
                    )
            	))
            ),
            array(
            	'name' => 'Lava Slime',
            	'hp' => 300,
            	'level' => 4,
            	'attack' => 5,
            	'defense' => 2,
            	'agility' => 2,
            	'exp' => 65,
            	'image' => 'fire_slime.png',
            	'animation_frame' => 'slime',
            	'techniques' => serialize(array(
                    array(
                        'use_chance' => 100,
                        'skill_name' => 'Ground snap',
                        'animation_key' => 'bounce',
                        'max' => 42,
                        'min' => 8,
                        'reload' => 4000,
                        'target' => 'single',
                    ),
            	))
            ),
            array(
            	'name' => 'Dire Rat',
            	'hp' => 19,
            	'level' => 1,
            	'attack' => 2,
            	'defense' => 2,
            	'agility' => 4,
            	'exp' => 14,
            	'image' => 'direrat.png',
            	'animation_frame' => 'rat',
            	'techniques' => serialize(array(
                    array(
                        'use_chance' => 90,
                        'skill_name' => 'Bite',
                        'animation_key' => 'bite',
                        'max' => 6,
                        'min' => 1,
                        'reload' => 4000,
                        'target' => 'single',
                    ),
                    array(
                        'use_chance' => 10,
                        'skill_name' => 'Chomp',
                        'animation_key' => 'bite',
                        'max' => 12,
                        'min' => 4,
                        'reload' => 5000,
                        'target' => 'single',
                    ),
            	))
            ),
            array(
            	'name' => 'Fatal Rat',
            	'hp' => 27,
            	'level' => 2,
            	'attack' => 3,
            	'defense' => 4,
            	'agility' => 5,
            	'exp' => 25,
            	'image' => 'deathrat.png',
            	'animation_frame' => 'rat',
            	'techniques' => serialize(array(
                    array(
                        'use_chance' => 80,
                        'skill_name' => 'Bite',
                        'animation_key' => 'bite',
                        'max' => 6,
                        'min' => 1,
                        'reload' => 3750,
                        'target' => 'single',
                    ),
                    array(
                        'use_chance' => 20,
                        'skill_name' => 'Deadly Chomp',
                        'animation_key' => 'bite',
                        'max' => 18,
                        'min' => 6,
                        'reload' => 7000,
                        'target' => 'single',
                    ),
            	))
            ),
            array(
            	'name' => 'King Bat',
            	'hp' => 40,
            	'level' => 2,
            	'attack' => 3,
            	'defense' => 4,
            	'agility' => 3,
            	'exp' => 80,
            	'image' => 'kingbat.png',
            	'animation_frame' => 'bat',
            	'techniques' => serialize(array(
                    array(
                        'use_chance' => 80,
                        'skill_name' => 'Sonic Boom',
                        'animation_key' => 'sonicboom',
                        'max' => 8,
                        'min' => 2,
                        'reload' => 5000,
                        'target' => 'single',
                    ),
                    array(
                        'use_chance' => 20,
                        'skill_name' => 'Wing Flap',
                        'animation_key' => 'wingflap',
                        'max' => 6,
                        'min' => 2,
                        'reload' => 4000,
                        'target' => 'single',
                    ),
            	))
            ),
        );

        $this->db->insert_batch('monsters', $monsters);



        $this->db->truncate('battle_waves');
        $waves = array(
            array('wave_number' => 1, 'monsters' => serialize(array(1))),
            array('wave_number' => 2, 'monsters' => serialize(array(2, 2))),
            array('wave_number' => 3, 'monsters' => serialize(array(3, 3))),
            array('wave_number' => 4, 'monsters' => serialize(array(1, 3, 1))),
            array('wave_number' => 5, 'monsters' => serialize(array(1, 7, 1))),
            array('wave_number' => 6, 'monsters' => serialize(array(7, 8, 7))),
            array('wave_number' => 7, 'monsters' => serialize(array(4))),
            array('wave_number' => 8, 'monsters' => serialize(array(8, 4))),
            array('wave_number' => 9, 'monsters' => serialize(array(8, 4, 8))),
            array('wave_number' => 10, 'monsters' => serialize(array(8, 8, 8))),
            array('wave_number' => 11, 'monsters' => serialize(array(5))),
            array('wave_number' => 12, 'monsters' => serialize(array(6))),
            array('wave_number' => 13, 'monsters' => serialize(array(5, 6, 5))),
            array('wave_number' => 14, 'monsters' => serialize(array())),
            array('wave_number' => 15, 'monsters' => serialize(array())),
            array('wave_number' => 16, 'monsters' => serialize(array())),
            array('wave_number' => 17, 'monsters' => serialize(array())),
            array('wave_number' => 18, 'monsters' => serialize(array())),
            array('wave_number' => 19, 'monsters' => serialize(array())),
            array('wave_number' => 20, 'monsters' => serialize(array())),
        );

        $this->db->insert_batch('battle_waves', $waves);
    }

}

/* End of file Battle.php */
/* Location: ./system/application/controllers/Battle.php */