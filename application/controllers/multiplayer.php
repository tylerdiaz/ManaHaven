<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Multiplayer Controller
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 **/

class Multiplayer extends CI_Controller
{
    private $allowed_players = 4;

    function __construct(){
        parent::__construct();
        if( ! $this->system->is_staff()) return $this->system->quick_parse('general/development');
    }

    public function party($id = 0)
    {
        if( ! is_numeric($id)) show_error('Invalid party id');

        $battle_party = $this->db->select('*')->where(array('battle_parties.id' => $id))->join('users', 'users.id = battle_parties.author_id')->get('battle_parties');

        if($battle_party->num_rows() > 0):
        	$party = $battle_party->row_array();

        	// Are you invited?
        	$battle_party_member = $this->db->where(array(
        	    'user_id' => $this->system->userdata['id'],
        	    'party_id' => $id
        	))->get('battle_party_members');

        	if($battle_party_member->num_rows() == 0):
        	    if($party['public_party'] == 0):
            	    if(($this->input->get('key') != $party['party_key']) && $party['author_id'] != $this->system->userdata['id']):
                        show_error('This is a private party.');
                    else:
                        // Got the key? Welcome aboard. :D
                        $battle_party_members = array(
                            'user_id'      => $this->system->userdata['id'],
                            'character_id' => $this->system->userdata['character_id'],
                            'username'     => $this->system->userdata['username'],
                            'last_update'  => time(),
                            'party_id'     => $id
                        );

                        $this->db->insert('battle_party_members', $battle_party_members);
            	    endif;
                endif;
        	endif;

        	$event_buffer = apc_fetch('party_event_key_'.$id);

        	// Let's start up the event buffer if one doesn't exist.
        	if( ! $event_buffer):
        	    apc_store('party_event_key_'.$id, 0, 3600); // 30 mins
                $event_buffer = 0;
        	endif;

        	// Load who's in the room.
        	$this->system->quick_parse('world/multiplayer', array(
                'page_title'      => 'Home',
                'page_body'       => 'world',
                'location'        => 'waiting_party',
                'party_id'        => $id,
                'party_key'       => $party['party_key'],
                'party'           => $party,
                'javascript_data' => json_encode(array(
                    'id'         => $id,
                    'last_event' => $event_buffer,
                    'user'       => array(
                        'id'       => $this->system->userdata['id'],
                        'username' => $this->system->userdata['username']
                    ),
        	    ))
        	));

        else:
            show_error('Party not found. Perhaps it was disbanded?');
        	// battle_party not found...
        endif;
    }

    public function join_party($id = 0)
    {
        if( ! is_numeric($id)) show_error('Invalid party id');

        $battle_party = $this->db->select('*')->where(array('id' => $id))->get('battle_parties');

        if($battle_party->num_rows() > 0):
        	$party = $battle_party->row_array();

        	// Are you invited?
        	$battle_party_member = $this->db->where(array(
        	    'user_id' => $this->system->userdata['id'],
        	    'party_id' => $id
        	))->get('battle_party_members');

        	if($battle_party_member->num_rows() == 0):
                show_error('This is a private party.');
        	endif;

            $party_members = apc_fetch('party_members_'.$id);
            $status = "unknown";

            if($party_members):
                if( ! in_array($this->system->userdata['id'], $party_members)):
                    array_push($party_members, $this->system->userdata['id']); // Add me to the member list!
                    apc_store('party_members_'.$id, $party_members, 3600);

                    $ajax_members = $this->db->select('battle_party_members.user_id, battle_party_members.username, characters.level')
                                             ->where('battle_party_members.party_id', $id)
                                             ->where_in('battle_party_members.user_id', $party_members)
                                             ->join('characters', 'characters.user_id = battle_party_members.user_id')
                                             ->order_by('last_update', 'desc')
                                             ->get('battle_party_members')
                                             ->result_array();

                    $event_data = array(
                        'event_js_action' => 'reload_online_list',
                        'event_data' => $ajax_members
                    );

                    $this->_push_event($id, $event_data);
                    $status = "Added to the member list";
                endif;
            else:
                $party_members = array($this->system->userdata['id']);
                apc_store('party_members_'.$id, $party_members, 3600);
                $status = "get members";
            endif;

            // Joined? Now fetch me the users online data!
            $members = $this->db->select('battle_party_members.user_id, battle_party_members.username, characters.level')
                                     ->where('battle_party_members.party_id', $id)
                                     ->where_in('battle_party_members.user_id', $party_members)
                                     ->join('characters', 'characters.user_id = battle_party_members.user_id')
                                     ->order_by('last_update', 'desc')
                                     ->get('battle_party_members')
                                     ->result_array();

//            $members['status'] = $status;

            echo json_encode($members);
        else:
            show_error('Party not found. Perhaps it was disbanded?');
        endif;
    }

    public function quick_leave_party($id = 0)
    {
        if( ! is_numeric($id)) show_error('Invalid party id');

        $battle_party = $this->db->select('*')->where(array('id' => $id))->get('battle_parties');

        if($battle_party->num_rows() > 0):
        	$party = $battle_party->row_array();

        	// Are you invited?
        	$battle_party_member = $this->db->where(array(
        	    'user_id' => $this->system->userdata['id'],
        	    'party_id' => $id
        	))->get('battle_party_members');

        	if($battle_party_member->num_rows() == 0):
                show_error('This is a private party.');
        	endif;

            $party_members = apc_fetch('party_members_'.$id);

            if($party_members):
                if(in_array($this->system->userdata['id'], $party_members)):
                    unset($party_members[array_search($this->system->userdata['id'], $party_members)]); // Remove me from the member list!
                    sort($party_members);

                    apc_store('party_members_'.$id, $party_members, 3600);

                    if(count($party_members) > 0):
                        $ajax_members = $this->db->select('battle_party_members.user_id, battle_party_members.username, characters.level')
                                                 ->where('battle_party_members.party_id', $id)
                                                 ->where_in('battle_party_members.user_id', $party_members)
                                                 ->join('characters', 'characters.user_id = battle_party_members.user_id')
                                                 ->order_by('last_update', 'desc')
                                                 ->get('battle_party_members')
                                                 ->result_array();

                        $event_data = array(
                            'event_js_action' => 'reload_online_list',
                            'event_data' => $ajax_members
                        );

                        $this->_push_event($id, $event_data);
                    endif;
                endif;
            endif;
        else:
            show_error('Party not found. Perhaps it was disbanded?');
        endif;
    }

    public function chat($id = 0)
    {
        if( ! is_numeric($id)) show_error('Invalid party id');

        $battle_party = $this->db->select('*')->where(array('id' => $id))->get('battle_parties');

        if($battle_party->num_rows() > 0):
        	$party = $battle_party->row_array();

        	// Are you invited?
        	$battle_party_member = $this->db->where(array(
        	    'user_id' => $this->system->userdata['id'],
        	    'party_id' => $id
        	))->get('battle_party_members');

        	if($battle_party_member->num_rows() == 0):
                show_error('This is a private party.');
        	endif;

        	// post chat
            $event_data = array(
                'event_js_action' => 'chat_bubble',
                'event_data' => array(
                    'message' => substr(sanitize($this->input->post('text')), 0, 48),
                    'event_user' => $this->system->userdata['username'],
                    'event_user_id' => $this->system->userdata['id'],
                )
            );

        	$this->_push_event($id, $event_data);
        else:
            show_error('Party not found. Perhaps it was disbanded?');
        endif;
    }

    public function start_battle($id = 0)
    {
        if( ! is_numeric($id)) show_error('Invalid party id');

        $battle_party = $this->db->select('*')->where(array('id' => $id))->get('battle_parties');

        if($battle_party->num_rows() > 0):
        	$party = $battle_party->row_array();

        	// Are you invited?
        	$battle_party_member = $this->db->where(array(
        	    'user_id' => $this->system->userdata['id'],
        	    'party_id' => $id
        	))->get('battle_party_members');

        	if($battle_party_member->num_rows() == 0):
                show_error('This is a private party.');
        	endif;

        	// Start countdown of 10 seconds
            $event_data = array(
                'event_js_action' => 'start_countdown',
                'event_data' => array(
                    'timer' => 10
                )
            );

            $this->_create_battle($party);
            $this->_push_event($id, $event_data);
        else:
            show_error('Party not found. Perhaps it was disbanded?');
        endif;
    }

    private function _create_battle($party_data = array())
    {
        if(count(apc_fetch('party_members_'.$party_data['id'])) < 1) show_error('No players in party!');

        $this->load->model('battle_engine');

        $cached_party_members = array_values(apc_fetch('party_members_'.$party_data['id']));

        $battle_party_members = $this->db->where_in('characters.user_id', $cached_party_members)
                                         ->get('characters')
                                         ->result_array();

        $pre_battle_characters = array();
        $found_party_creator = in_array($party_data['author_id'], $cached_party_members);

        foreach ($battle_party_members as $character):
            if($character['energy'] > 0 && $character['hp'] > 0):
                $pre_battle_characters[$character['character_id']] = $this->battle_engine->format_character_data($character);

                // Reduce and energy when you start a new wave set!
                // $this->db->where_in('character_id', $character['character_id'])->update('characters', array('energy' => ($character['energy']-1)));
            endif;
        endforeach;

        if( ! $found_party_creator) $party_data['author_id'] = $cached_party_members[0];

        $start_wave = 1;
        $battle_wave = $this->db->select('*')->where(array('wave_number' => $start_wave))->get('battle_waves');

        if($battle_wave->num_rows() > 0):
        	$battle_wave_data = $battle_wave->row_array();
        else:
        	// battle_wave not found...
        	show_error('This battle template has not been built yet!');
        endif;

        // Let's get this battle started!
        $packaged_battle_data = array(
        	'creator_id' => $party_data['author_id'],
        	'battle_finished' => 0,
        	'battle_type' => 'multiplayer',
        	'wave_level' => $start_wave,
        	'jackpot' => 0,
        	'battle_started_at' => time()
        );

        $this->db->insert('battles', $packaged_battle_data);
        $battle_id = $this->db->insert_id();

        foreach ($pre_battle_characters as $character_id => $character_data):
            $user_data = $this->db->join('users', 'users.id = characters.user_id')
                                  ->get_where('characters', array('character_id' => $character_id))
                                  ->row_array();

            // Alright, battle is ready to go, let's dive in to the character(s)
            $battle_characters[] = array(
            	'character_id' => $character_id,
            	'character_data' => serialize($pre_battle_characters[$character_id]),
            	'battle_id' => $battle_id,
            	'battle_team' => 'left',
            	'character_username' => $user_data['username'],
            	'character_user_id' => $user_data['id'],
            	'active_battle' => 1
            );

        endforeach;

        $packaged_battle_data['characters'] = $battle_characters;
        $this->db->insert_batch('battle_characters', $battle_characters);

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
            		'monster_id' => $monster_id,
            		'monster_data' => serialize($monster_data),
            		'battle_id' => $battle_id,
            		'battle_team' => 'right',
            		'reload_time' => $monster_data['reload_time']
            	);

                $packaged_battle_data['monsters'][] = $battle_monsters;
            	$this->db->insert('battle_monsters', $battle_monsters);
            else:
            	// monster not found...
            endif;
        endforeach;

        return $packaged_battle_data;
    }

    public function _push_event($party_id = 0, $event_data = array())
    {
        apc_store('party_'.$party_id.'_event_'.apc_inc('party_event_key_'.$party_id), $event_data, 5);
    }

}

/* End of file Multiplayer.php */
/* Location: ./system/application/controllers/Multiplayer.php */