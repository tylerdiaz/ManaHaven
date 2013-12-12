<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User Engine Model
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 **/

/*
 * TODO: Make item attributes::EXP bonus items count!
 * TODO: Make item attributes::Gold bonus items count!
 * TODO: Make item attributes::HP Bonus count!
 * TODO: Allow the person to take the jackpot
*/

/*
 * TEST CASES:
 *  - Multiple monster win
 *  - Multiple monster lose
 *  - Monster win
 *  - Player win
 *  - Monster lose
 *  - Player lose
*/

class Battle_engine extends CI_Model
{
    private $firephp_log = FALSE;
    private $debug_mode = FALSE;
    private $jackpot_cap = 2000;
    public $battle = array();
    public $battle_data_loaded = FALSE;
    public $character = array();
    public $monsters = array();
    public $logs = array();
    public $response = array(
        'caster_type'   => 'player', // player, monster
        'caster_id'     => 0,
        'response_type' => 'animate', // animate, loss, win, item, escape
        'waiting_time'  => 0,
        'animate_data'  => array(
            'target_id'     => 0, // Should also accepts arrays, but not yet...
            'amount_type'   => 'negative', // negative, positive
            'amount'        => 0,
            'description'   => '%s used %s!',
            'animation_key' => '' // the battle animation engine renders this on the switch to see which animation it should run
        )
    );

	function __construct()
	{
        parent::__construct();
        if($this->firephp_log === TRUE):
            require_once('debugger/FirePHP.class.php');
            $this->firephp = FirePHP::getInstance(true);
        endif;
	}


	public function get_battle_id()
	{
		$battle_id = $this->db->select('battle_id')->get_where('battle_characters', array(
            'active_battle'   => 1,
            'character_user_id' => $this->session->userdata('id')
        ))->row()->battle_id;

        return $battle_id;
	}


    public function get_battle_data()
    {
        $battle = $this->db->join('battles', 'battles.id = battle_characters.battle_id')->get_where('battle_characters', array(
            'active_battle'   => 1,
            'character_user_id' => $this->session->userdata('id'),
        ));

        if($battle->num_rows() > 0){
            $battle_data = $battle->row_array();

            $battle_characters = $this->db->where(array(
            	'battle_id' => $battle_data['id'],
            ))->limit(8)->get('battle_characters');

            if($battle_characters->num_rows() > 0):
            	// Yup. We found a match.
            	$battle_characters = $battle_characters->result_array();
	            foreach ($battle_characters as $key => $battle_character):
            		// Looping through value
            		$battle_character['character_data'] = unserialize($battle_character['character_data']);
                	$battle_data['characters'][$battle_character['character_id']] = $battle_character;
            	endforeach;
            else:
            	// No dice.
            endif;

            $battle_character = $this->db->select('*')->where(array('battle_id' => $battle_data['id'], 'character_id' => $this->system->userdata['character_id']))->get('battle_characters');

            if($battle_character->num_rows() > 0){
            	$battle_character_data = $battle_character->row_array();
            	$battle_character_data['character_data'] = unserialize($battle_character_data['character_data']);
            	$battle_data['character'] = $battle_character_data;
            } else {
            	// battle_character not found...
            }

            $battle_monster = $this->db->select('*')->where(array('battle_id' => $battle_data['id'], 'monster_defeated' => 0))->get('battle_monsters');

            if($battle_monster->num_rows() > 0){
                $battle_data_loaded = TRUE;
            	$battle_monster_data = $battle_monster->result_array();

            	foreach($battle_monster_data as $monster){
                	$monster['monster_data'] = unserialize($monster['monster_data']);
                	$battle_data['monsters'][$monster['id']] = $monster;
            	}
            } else {
            	// battle_monster not found...
            }
        } else {
            return false;
        }

        $this->battle = $battle_data;
        $this->logs = unserialize($this->battle['battle_logs']);

        return $battle_data;
    }

    public function log_data($log_data = '')
    {
        $time_stamp = date("d/n/Y g:i:sA P");
        $this->logs[$time_stamp] = (is_array($log_data) ? serialize($log_data) : $log_data);

        $this->db->where('id', $this->battle['id'])->update('battles', array('battle_logs' => serialize($this->logs)));
    }

    public function set_winner($target = 'monster')
    {
        $battles = array(
        	'battle_finished' => 1,
        	'winning_side' => ($target == 'monster' ? 'right' : 'left'),
        	'battle_finished_at' => time(),
        );

        $this->db->where('character_id', $this->system->userdata['character_id'])
                 ->update('characters', array('hp' => $this->battle['characters'][$this->system->userdata['character_id']]['character_data']['hp']));

        if($target == "monster"):
            $this->db->where('character_id', $this->system->userdata['character_id'])->update('characters', array('hp' => 0));
            $this->db->where('id', $this->battle['id'])->update('battles', array('winning_side' => 'right'));
            $this->response['battle_lost'] = TRUE;
        else:

            if($this->battle['wave_level'] > 2):
                if(($this->battle['battle_started_at']) > (time()-($this->battle['wave_level']*2))):
                    $this->response['jackpot_bonuses']['Quick Victory'] = ($this->battle['wave_level']*2);
                endif;
            endif;

            if($this->battle['used_heal'] == 0 && $this->battle['wave_level'] > 2):
                $this->response['jackpot_bonuses']['No Need to heal'] = 5;
            endif;

            if(isset($this->response['jackpot_bonuses'])):
                $jackpot_bonus = array_sum($this->response['jackpot_bonuses']);

                // I'm not one to use this trick, but it sure is convinient here. ;D
                $this->battle['jackpot'] = $battles['jackpot'] = ($jackpot_bonus+$this->battle['jackpot']);
            endif;

            $this->response['battle_won'] = TRUE;
        endif;

        $battle_characters = array('active_battle' => 0);

        $this->db->where('battle_id', $this->battle['id'])
                 ->where('character_id', $this->system->userdata['character_id'])
                 ->update('battle_characters', array('active_battle' => 0));

        $this->db->where('id', $this->battle['id'])->update('battles', $battles);
    }

    public function deplete_hp($target = 'monster', $id = 0, $amount = 0, $caster_id = 0, $critical = FALSE)
    {
        if($this->debug_mode === TRUE) $amount = 0;

        if($target == "monster"):
            foreach($this->battle['monsters'] as $monster_key => $monster):
                if($id == $monster['id']):

                    // We got a match!
                    $monster['monster_data']['hp'] -= ($critical === FALSE ? $amount : ceil($amount*1.75));
                    $this->response['animate_data']['amount'] = ($critical === FALSE ? $amount : ceil($amount*1.75));
                    $this->response['animate_data']['critical'] = $critical;

					$jackpot_wave_bonus = max((7-$this->battle['wave_level']), 4);

                    $jackpot_bonus = mt_rand(1, $jackpot_wave_bonus)+(floor($amount/4)+floor($this->battle['wave_level']/4));

                    // Give criticals an intense feel of gold rush when it's hit!
                    if($critical === TRUE) $jackpot_bonus += (5+floor($jackpot_bonus/2.5));

                    // Make sure attacking always gives at least one-to-three gold coin
                    if($jackpot_bonus < 1) $jackpot_bonus = (1+mt_rand(0, 2));

                    // Gold Bump!
                    if($critical === FALSE && $this->battle['characters'][$caster_id]['character_data']['gold_percent_bonus'] > 0):
                        $jackpot_bonus += ceil($jackpot_bonus *($this->battle['characters'][$caster_id]['character_data']['gold_percent_bonus']/100));
                    endif;

                    $jackpot_cap = apc_fetch('jackpot_cap_'.$this->session->userdata('id'));

                    if( ! $jackpot_cap):
                        apc_store('jackpot_cap_'.$this->session->userdata('id'), (int)round($jackpot_bonus), 86400);
                    else:
                        apc_inc('jackpot_cap_'.$this->session->userdata('id'), (int)round($jackpot_bonus));
                        if(($jackpot_bonus+$jackpot_cap) >= $this->jackpot_cap):
                            $jackpot_bonus = 1;
                        endif;
                    endif;


                    $this->response['animate_data']['jackpot_bonus'] = $jackpot_bonus;

                    if(max($monster['monster_data']['hp'], 0) <= 0):
                        // Let's rid ourselves of this data, it's no longer needed
                        unset($this->battle['monsters'][$monster_key]);

                        $this->db->where('id', $monster['id'])->update('battle_monsters', array('monster_defeated' => 1));
                        $this->response['monster_count'] = count($this->battle['monsters']);
                        $this->response['monster_defeated'] = $monster['id'];

                        // Was that the last one, if so, claim the victory!
                        if(count($this->battle['monsters']) < 1) $this->set_winner('player');

                        $this->_increase_exp($caster_id, $monster['monster_data']['exp']);
                    else:
                        $this->db->where('id', $monster['id'])->update('battle_monsters', array('monster_data' => serialize($monster['monster_data'])));
                    endif;

                    $this->db->where('id', $this->battle['id'])->update('battles', array('jackpot' => $this->battle['jackpot']+$jackpot_bonus));
                endif;
            endforeach;
        elseif($target == "player"):
            $amount = ($critical === FALSE ? $amount : ceil($amount*1.5));

            $this->response['animate_data']['amount'] = $amount;
            $this->response['animate_data']['critical'] = $critical;

            $character = $this->battle['characters'][$id]['character_data'];
            $character['hp'] = ($character['hp']-$amount);

            $this->response['real_hp'] = max($character['hp'], 0);

            // Has this character fainted?
            if($character['hp'] <= 0):
                // Is there anyone left alive?
                if(count($this->battle['characters']) > 1):
                    $characters_avaliable = array();
                    foreach ($this->battle['characters'] as $character_id => $character_data):
                        if($character_data['character_defeated'] == 0):
                            array_push($characters_avaliable, $character_id);
                        endif;
                    endforeach;

                    if(count($characters_avaliable) == 0):
                        // All characters_avaliable has fainted!
                        $this->set_winner('monster');
                    endif;
                else:
                    $this->set_winner('monster');
                    $this->response['real_hp'] = max($character['hp'], 0);
                endif;
            endif;

            $this->db->where('id', $this->battle['characters'][$id]['id'])->update('battle_characters', array('character_data' => serialize($character)));
        endif;
    }

    public function use_skill($skill_id = 0, $target_id = 0)
    {
        $skills = $this->battle['characters'][$this->system->userdata['character_id']]['character_data']['skills'];

        foreach ($skills as $skill_key => $skill):
            if($skill['id'] == $skill_id):
                // we found our match, let's go
                $skill_data = $skill;
            endif;
        endforeach;

        if( ! isset($skill_data)) die(json_encode(array('error' => 'Skill was not found!')));

        switch($skill_data['target']):
            case 'character':
                $skill_points = rand(($skill_data['min_damage']+1), $skill_data['max_damage'])+$this->system->userdata['magick'];

                // Am I targetting myself? If so, get my battle character id
                if($target_id == $this->system->userdata['character_id']) $target_id = $this->system->userdata['character_id'];

                // Let's set some useful default data
                $this->battle_engine->render_response(array(
                    'caster_id'     => $this->system->userdata['character_id'],
                    'response_type' => 'animate', // animate, loss, win, item, escape
                    'waiting_time'  => $skill_data['waiting_time'],
                    'animate_data'  => array(
                        'target'        => 'character',
                        'target_id'     => $target_id, // Also accepts arrays
                        'amount_type'   => 'positive', // negative, positive
                        'amount'        => $skill_points,
                        'description'   => $this->system->userdata['username'].' uses '.$skill_data['name'].'!',
                        'animation_key' => $skill_data['animation_key'] // the battle animation engine renders this on the switch to see which animation it should run
                    )
                ));

                // increase your life!
                $this->battle_engine->increase_hp('character', $target_id, $skill_points, $this->system->userdata['character_id']);
            break;
            case 'monster':
                $character_data = $this->battle['characters'][$this->system->userdata['character_id']]['character_data'];
                $agility_bonus = min($character_data['agility']*5, 20);
                $agility_waiting_bonus = min($character_data['agility']*100, 500);

                $miss_chance = (mt_rand(0, 100) > 90);
                $critical_chance = (mt_rand(0, 100) > 95);

                $skill_points = mt_rand(($skill_data['min_damage']+1), $skill_data['max_damage']);

                if($critical_chance) $skill_points = $skill_data['max_damage'];

				// Let's do the attack bonus!
                $skill_points += floor(max(ceil($character_data['attack']/2), 1) * (rand()/getrandmax()*1));

                if($skill_data['skill_id'] == 10) $skill_points += ceil($this->system->userdata['magick']/0.85);

                // Let's set some useful default data
                $this->battle_engine->render_response(array(
                    'caster_id'     => $this->system->userdata['character_id'],
                    'response_type' => 'animate', // animate, loss, win, item, escape
                    'waiting_time'  => ($skill_data['waiting_time']-$agility_waiting_bonus),
                    'animate_data'  => array(
                        'target'        => 'monster',
                        'target_id'     => $target_id, // Also accepts arrays
                        'amount_type'   => 'negative', // negative, positive
                        'amount'        => $skill_points,
                        'description'   => $this->system->userdata['username'].' uses '.$skill_data['name'].'!',
                        'animation_key' => $skill_data['animation_key'], // the battle animation engine renders this on the switch to see which animation it should run
                        'critical'      => $critical_chance,
                        'miss'          => $miss_chance,
                    )
                ));

                // reduce the monsters life, check if you killed it.
                if( ! $miss_chance):
                    if($critical_chance == TRUE):
                        $skill_points = $skill_data['max_damage'];
                    endif;
                    $this->battle_engine->deplete_hp('monster', $target_id, $skill_points, $this->system->userdata['character_id'], $critical_chance);
                endif;
            break;
            case 'all_character':

            break;
            case 'all_monsters':

            break;
        endswitch;
    }

    public function increase_hp($target = 'monster', $id = 0, $amount = 0, $caster_id = 0)
    {
        if($target == 'monster'){
            // Heal the monster!

        } elseif($target == 'character' || $target == "player") {
            $character = $this->battle['characters'][$this->system->userdata['character_id']]['character_data'];
            $character['hp'] = ($character['hp']+$amount);

            // Don't go over the HP limit!
            if($character['hp'] > $character['max_hp']) $character['hp'] = $character['max_hp'];

            if(($this->battle['characters'][$this->system->userdata['character_id']]['character_data']['hp']+$amount) > $character['max_hp']){
                $this->response['animate_data']['amount'] = ($character['max_hp']-$this->battle['characters'][$this->system->userdata['character_id']]['character_data']['hp']);
            }

            // TODO: Heal the character as well!
            $this->db->where('character_id', $this->system->userdata['character_id'])
                     ->where('battle_id', $this->battle['id'])
                     ->update('battle_characters', array('character_data' => serialize($character)));

            $this->db->where('id', $this->battle['id'])->update('battles', array('used_heal' => '1'));
        }
    }

    public function use_item($item_id = 0, $item = array())
    {
        switch($item['target']):
            case 'ally':
                // For now, let's just be selfish and target ourselves
                if($item['effect'] == 'recover_hp'):

                    // Let's set some useful default data
                    $this->battle_engine->render_response(array(
                        'caster_id'     => $this->system->userdata['character_id'],
                        'response_type' => 'item', // animate, loss, win, item, escape
                        'waiting_time'  => $item['reload_time'],
                        'animate_data'  => array(
                            'target'        => 'character',
                            'target_id'     => $this->system->userdata['character_id'], // Also accepts arrays
                            'amount_type'   => 'positive', // negative, positive
                            'amount'        => $item['points'],
                            'description'   => $this->system->userdata['username'].' uses a '.$item['name'].'!',
                            'animation_key' => $item['animation_key'] // the battle animation engine renders this on the switch to see which animation it should run
                        )
                    ));

                    // increase your life!
                    $this->battle_engine->increase_hp('character', $this->system->userdata['character_id'], $item['points'], $this->system->userdata['character_id']);

                    // Work around the HP cap to show the amount they SHOULD be healing. It's more clear.
                    $this->response['animate_data']['amount'] = $item['points'];
                endif;
            break;
            case 'enemy':

            break;
            case 'all_allies':

            break;
            case 'all_enemies':

            break;
            case 'all':

            break;
        endswitch;

    }

    private function _increase_exp($caster_id = 0, $amount = 0)
    {
        $battle_character = $this->db->where(array('characters.character_id' => $caster_id, 'battle_id' => $this->battle['id']))
                                     ->join('battle_characters', 'characters.character_id = battle_characters.character_id')
                                     ->get('characters');

        if($battle_character->num_rows() > 0){
            $battle_character_data = $battle_character->row_array();

            // EXP Bump!
            if($this->battle['characters'][$caster_id]['character_data']['exp_percent_bonus'] > 0):
                $amount += ceil($amount*($this->battle['characters'][$caster_id]['character_data']['exp_percent_bonus']/100));
            endif;

            if(($battle_character_data['exp']+$amount) >= $battle_character_data['next_level_exp']):
                // How many level ups did we obtain?
                $new_level = $this->db->limit(1)
                                      ->order_by('id', 'desc')
                                      ->get_where('character_templates', array('exp_required <=' => $battle_character_data['exp']+$amount))
                                      ->row_array();

                $next_level = $this->db->limit(1)
                                       ->get_where('character_templates', array('level' => $new_level['level']+1))
                                       ->row_array();

                if($new_level['level'] > $battle_character_data['level']):
                    // Let's do the loop logic over here, and let the Javascript parse the looped array of data
                    $current_level = $battle_character_data['level'];
                    while ($current_level <= $new_level['level']):

                        if($current_level == $new_level['level']): // Last one!
                            $this->response['exp_animation'][] = array(
                                'from_level' => $current_level,
                                'to_level' => $new_level['level'],
                                'exp_bonus' => ($amount-($new_level['exp_required']-$battle_character_data['exp'])),
                                'percent' => percent(($battle_character_data['exp']+$amount), $next_level['exp_required']),
                                'new_exp_required' => $next_level['exp_required'],
                            );
                        else:
                            // TODO: This new_exp_required might be buggy when you level up multiple times *unlikely, but still*
                            $this->response['exp_animation'][] = array(
                                'from_level' => $current_level,
                                'to_level' => $new_level['level'],
                                'exp_bonus' => ($new_level['exp_required']-$battle_character_data['exp']),
                                'percent' => 100,
                                'new_exp_required' => $next_level['exp_required'],
                                'heal_points' => $battle_character_data['max_hp']
                            );
                        endif;
                        $current_level++;
                    endwhile;
                endif;

                $new_character_data = array(
                    'level'          => $new_level['level'],
                    'next_level_exp' => $next_level['exp_required'],
                    'exp'            => ($battle_character_data['exp']+$amount),
                    'skill_points'   => ($battle_character_data['skill_points']+$new_level['points']),
                    'hp'             => $battle_character_data['max_hp']
                );

                // Now let's update the real data
                $this->db->where('character_id', $this->system->userdata['character_id'])->update('characters', $new_character_data);

                $this->battle['characters'][$this->system->userdata['character_id']]['character_data'] = array_merge($this->battle['characters'][$this->system->userdata['character_id']]['character_data'], $new_character_data);

                $this->db->where('id', $this->system->userdata['character_id'])->update('battle_characters', array('character_data' => serialize($this->battle['characters'][$this->system->userdata['character_id']]['character_data'])));
            else:
                $this->response['exp_bonus'] = $amount;
                $this->response['exp_percent'] = percent(($battle_character_data['exp']+$amount), $battle_character_data['next_level_exp']);

                $this->db->where('character_id', $battle_character_data['character_id'])->update('characters', array('exp' => ($battle_character_data['exp']+$amount)));
            endif;
        } else {
            // battle_character not found, exp could not be granted...
        }

    }

    public function render_response($data = array(), $inside = '')
    {
        foreach ($data as $key => $value) $this->response[$key] = $value;
        return $this->response;
    }

    public function format_character_data($character_data = array())
    {
        // First of all, let's get the item bonus
        $item_bonuses = $this->db->select('SUM(avatar_items.defense_bonus) as defense_bonus,
                                             SUM(avatar_items.max_hp_bonus) as hp_bonus,
                                             SUM(avatar_items.max_energy_bonus) as energy_bonus,
                                             SUM(avatar_items.exp_bonus) as exp_bonus,
                                             SUM(avatar_items.gold_bonus) as gold_bonus,
                                             SUM(avatar_items.attack_bonus) as attack_bonus,
                                             SUM(avatar_items.agility_bonus) as agility_bonus')
                                   ->where(array('character_id' => $character_data['character_id'], 'type' => 'equipable', 'equipped' => 1))
                                   ->join('avatar_items', 'character_items.item_id = avatar_items.item_id')
                                   ->from('character_items')
                                   ->get()
                                   ->row_array();

    	$character_data['defense'] += $item_bonuses['defense_bonus'];
    	$character_data['attack'] += $item_bonuses['attack_bonus'];
    	$character_data['agility'] += $item_bonuses['agility_bonus'];
    	$character_data['max_hp'] += $item_bonuses['hp_bonus'];
    	$character_data['max_energy'] += $item_bonuses['energy_bonus'];
    	$character_data['exp_percent_bonus'] = $item_bonuses['exp_bonus'];
    	$character_data['gold_percent_bonus'] = $item_bonuses['gold_bonus'];

    	// Secondly, let's get all the skills
    	$skills = $this->db->select('*')
    	                   ->join('skills', 'character_skills.skill_id = skills.id')
    	                   ->where(array('character_id' => $character_data['character_id']))
    	                   ->get('character_skills');

    	if($skills->num_rows() > 0){
    		$skills_array = $skills->result_array();
    	} else {
    		$character_backpack = $skills->result_array();
    	}

    	$character_data['skills'] = $skills_array;

    	$character_items = $this->db->select('*, COUNT(item_id) as amount')
    	                            ->where(array('character_id' => $character_data['character_id'], 'type' => 'usable'))
    	                            ->join('utility_items', 'utility_items.id = character_items.item_id')
    	                            ->group_by('item_id')
                                    ->get('character_items');

    	if($character_items->num_rows() > 0){
    		$character_backpack = $character_items->result_array();
    	} else {
            $character_backpack = array();
    	}

    	$character_data['items'] = $character_backpack;

    	return $character_data;
    }


    /**
     * Push event (Mainly for multiplayer)
     */
    public function _push_event($data = array())
    {
        $event_id = apc_inc('battle_'.$this->battle['id'].'_event_key');
        apc_store('battle_'.$this->battle['id'].'_event_'.$event_id, $data, 5);
        return $event_id;
    }

}