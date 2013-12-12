<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Avatar system
 *
 * @author(s) 	Tyler Diaz
 * @version 	1.4
 **/

class Avatar extends CI_Controller
{
	var $avatar_config;
	var $avatar_data = NULL;
    var $main_hat_layer = 31;
    var $unequip_hat_layers = array(46);
    var $use_imagick = FALSE;
    var $hours_to_cache = 0; // 0 to cancel caching
    private $saving = FALSE;

	function __construct(){
		parent::__construct();

        if( ! $this->session->userdata('id')) redirect(site_url());

		$config_query = $this->db->get('avatar_config')->result_array(); // Getting config options...
		foreach($config_query as $row) $this->avatar_config[$row['key']] = $row['value'];
	}

    public function index()
    {
        $data = $this->_get_equipped_data();
        $this->_set_equipped_data($data);

        $this->benchmark->mark('get_items_start');
        $view_data_cache = ($this->hours_to_cache > 0 ? apc_fetch('avatar_cache_'.$this->system->userdata['character_id']) : FALSE);

        if( ! $view_data_cache):
            $inventory = $this->db->select('layers.order as layerorder,
                                            layers.name as layername,
                                            avatar_items.name as name,
                                            avatar_items.thumb,
                                            character_items.id as main_id,
                                            COUNT( character_items.id ) AS num,
                                            layers.appearance,
                                            avatar_items.weight,
                                            avatar_items.max_hp_bonus,
                                            avatar_items.max_energy_bonus,
                                            avatar_items.exp_bonus,
                                            avatar_items.attack_bonus,
                                            avatar_items.defense_bonus,
                                            avatar_items.agility_bonus')
                                  ->from('character_items')
                                  ->join('avatar_items', 'avatar_items.item_id = character_items.item_id')
                                  ->join('characters', 'characters.character_id = character_items.character_id')
                                  ->join('avatar_layers layers', 'layers.id = avatar_items.layer')
                                  ->where('character_items.type', 'equipable')
                                  ->where('character_items.character_id', $this->system->userdata['character_id'])
                                  ->where("(CAST(avatar_items.gender AS CHAR) = CAST(characters.gender AS CHAR) OR CAST(avatar_items.gender AS CHAR) = 'Unisex')")
    							  ->group_by('character_items.item_id')
    							  ->get()
    							  ->result_array();

            $items = array();
            $appearance = array();
            $total_weight = 0;

    		foreach($inventory as $item):
    		    if($item['appearance'] == 1){
        			$appearance[$item['layerorder']][$item['layername']][] = array(
                        'name'     => $item['name'],
                        'id'       => $item['main_id'],
                        'equipped' => (isset($this->avatar_data['items'][$item['main_id']]) ? 'equipped' : ''),
                        'num'      => $item['num'],
                        'thumb'    => $item['thumb']
        			);
    		    } else {
    		        $item_name = trim(preg_replace('/\(.*\)/', '', $item['name']));
        			$items[$item['layerorder']][$item['layername']][] = array(
                        'name'             => $item_name,
                        'id'               => $item['main_id'],
                        'equipped'         => (isset($this->avatar_data['items'][$item['main_id']]) ? 'equipped' : ''),
                        'num'              => $item['num'],
                        'thumb'            => $item['thumb'],
                        'weight'           => $item['weight'],
                        'max_hp_bonus'     => $item['max_hp_bonus'],
                        'max_energy_bonus' => $item['max_energy_bonus'],
                        'exp_bonus'        => $item['exp_bonus'],
                        'attack_bonus'     => $item['attack_bonus'],
                        'defense_bonus'    => $item['defense_bonus'],
                        'agility_bonus'    => $item['agility_bonus']
        			);

                    $item_data[$item['main_id']] = array(
                        'name'   => $item_name,
                        'weight' => $item['weight'],
                        'attr'   => array(
                            'HP'      => $item['max_hp_bonus'],
                            'Energy'  => $item['max_energy_bonus'],
                            'EXP'     => $item['exp_bonus'],
                            'Attack'  => $item['attack_bonus'],
                            'Defense' => $item['defense_bonus'],
                            'Speed'   => $item['agility_bonus']
                        )
                    );

        			if(isset($this->avatar_data['items'][$item['main_id']])) $total_weight += $item['weight'];
    		    }
    		endforeach;

            // Line them up
            ksort($items);

            $this->benchmark->mark('get_items_end');

            $sql_select = 'COUNT(character_items.id) as num, utility_items.*, character_items.*, IF(utility_items.target = "ally", TRUE, FALSE) as usable';

            $usable_items = $this->db->select($sql_select, FALSE)->where(array(
                'character_id' => $this->system->userdata['character_id'],
                'type'         => 'usable',
            ))->join('utility_items', 'utility_items.id = character_items.item_id')->group_by('item_id')->get('character_items');

            if($usable_items->num_rows() > 0):
            	$usable_items = $usable_items->result_array();
            else:
        	    $usable_items = array();
            endif;

            // Double the avatar's image size?
            $double_img_size = TRUE;

            $view_data = array(
                'page_title'      => 'Your avatar',
                'page_body'       => 'avatar',
                'items'           => $items,
                'appearance'      => $appearance,
                'total_weight'    => $total_weight,
                'double_img_size' => $double_img_size,
                'usable_items'    => $usable_items,
                'location'        => 'avatar',
                'item_data'       => $item_data
            );

            if($this->hours_to_cache > 0):
                apc_store('avatar_cache_'.$this->system->userdata['character_id'], $view_data, (60*60)*$this->hours_to_cache);
            endif;
        else:
            $view_data = $view_data_cache;
        endif;

        $this->system->quick_parse('avatar/index', $view_data);
    }


	function equip($item_id = NULL, $ajax = false)
	{
		if( ! is_null($item_id) && is_numeric($item_id)): // Make sure it exists and its a number
		    // Is the item part of the avatar? 'unequippable'
			$composite = $this->_isComposite($item_id);
			$avatar = $this->_get_equipped_data();

            $string[] = $avatar['items'];

            $carrying_weight = $this->_get_weight($avatar['items']);

		    // If your already wearing the item, and it's not compulsive. Unequip it!
			if(isset($avatar['items'][$item_id])):
				if($composite['composite'] === false):
					unset($avatar['items'][$item_id]);
					$this->_set_equipped_data($avatar);
				endif;
			else:
			    // If your not wearing it, check for all the others and replace the layer item!
				foreach($avatar['items'] as $id => $value):
					$comp = $this->_isComposite($id);
					if($comp['layerid'] == $composite['layerid']):
						unset($avatar['items'][$id]);
						$carrying_weight -= $comp['weight'];
					endif;
				endforeach;

				if(($carrying_weight+$composite['weight']) <= $this->system->userdata['max_weight']):
    				$avatar['items'][$item_id] = true;
    				$this->_set_equipped_data($avatar);
    			else:
    			    show_error('You cannot carry this item, it\'s too heavy!');
				endif;
			endif;

            // This wants the array of preview items
            if($this->input->get('json')):
                $sql_if = "(IF( CAST(avatar_items.gender AS CHAR) = 'Unisex',
                			    IF( CAST(avatar_item_parts.gender AS CHAR) = CAST(characters.gender as CHAR)
                			        OR CAST(avatar_item_parts.gender AS CHAR) = 'Unisex', true, false),
                		        true)
                		    )";

                        $item_query = $this->db->select('image_path as path, avatar_item_parts.layer as layer_piece_id, avatar_layers.order, avatar_items.layer, avatar_items.item_id')
                            ->from('avatar_item_parts')
                            ->join('avatar_items', 'avatar_items.item_id       = avatar_item_parts.item_id')
                            ->join('avatar_layers', 'avatar_layers.id          = avatar_item_parts.layer')
                            ->join('character_items', 'character_items.item_id = avatar_items.item_id')
                            ->join('characters', 'characters.character_id      = character_items.character_id')
                            ->where('character_items.character_id', $this->system->userdata['character_id'])
                            ->where('avatar_item_parts.sprite_display_only', 0)
                            ->where('character_items.id IN ('.implode(',', array_keys($avatar['items'])).')')
                            ->where($sql_if)
                            ->group_by('avatar_item_parts.layer')
                            ->order_by('avatar_layers.order', 'asc')
                            ->order_by('avatar_items.order', 'asc')
                            ->get()
                            ->result_array()
;

                $hat_equipped = 0;
                $item_list = array_reverse($item_query);

                foreach ($item_list as $item):
                    if($item['layer'] == $this->main_hat_layer) $hat_equipped = 1;
                    if($hat_equipped != 1 || ! in_array($item['layer_piece_id'], $this->unequip_hat_layers)) $list_of_items[] = $item;
                endforeach;

                $item_query = array_reverse($list_of_items);
                $item_images = array();

                foreach ($item_query as $item) $item_images[] = $item['path'];

                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array_reverse(array_values($item_images)), JSON_NUMERIC_CHECK));
            else:
                $this->output->set_content_type('application/json')
                             ->set_output(json_encode(array($string), JSON_NUMERIC_CHECK));
            endif;
		else:
			trigger_error("Invalid Handler");
		endif;

		if( ! is_ajax()) redirect('avatar');
	}

    public function preview_specific_item($item_preview_id = 0, $items = array())
    {
        $equipped_items = $this->db->select('avatar_items.item_id, avatar_items.layer')
                                   ->from('character_items')
                                   ->where_in('id', $items)
                                   ->join('avatar_items', 'avatar_items.item_id = character_items.item_id')
                                   ->get()
                                   ->result_array();

        $preview_item = $this->db->select('avatar_items.item_id, avatar_items.layer')
                                 ->get_where('avatar_items', array('item_id' => $item_preview_id))
                                 ->row_array();

        $replaced_item = FALSE;
        $items = array();

        foreach($equipped_items as $key => $item):
            if($item['layer'] == $preview_item['layer']):
                $replaced_item = TRUE;
                $item = $preview_item;
            endif;
            $items[$item['item_id']] = TRUE;
        endforeach;

        if($replaced_item == FALSE) $items[$preview_item['item_id']] = TRUE;

        $sql_if = "(IF( CAST(avatar_items.gender AS CHAR) = 'Unisex',
					    IF( CAST(avatar_item_parts.gender AS CHAR) = CAST(characters.gender as CHAR)
					        OR CAST(avatar_item_parts.gender AS CHAR) = 'Unisex', true, false),
				        true)
				    )";

        $item_query = $this->db->select('image_path as path, avatar_item_parts.layer as layer_piece_id, avatar_layers.order, avatar_items.layer, avatar_items.item_id')
                                ->from('avatar_item_parts, characters')
                                ->join('avatar_items', 'avatar_items.item_id = avatar_item_parts.item_id')
                                ->join('avatar_layers', 'avatar_layers.id = avatar_item_parts.layer')
                                ->where('avatar_item_parts.sprite_display_only', 0)
                                ->where('avatar_items.item_id IN ('.implode(',', array_keys($items)).')')
                                ->where($sql_if)
                                ->where('characters.character_id', $this->system->userdata['character_id'])
                                ->group_by('avatar_item_parts.layer')
                                ->order_by('avatar_layers.order', 'asc')
                                ->order_by('avatar_items.order', 'asc')
                                ->get()
                                ->result_array();

        return $item_query;
    }


	function preview($return = FALSE, $sprite_sheet = FALSE, $double_size = FALSE)
	{
		$data = $this->_get_equipped_data();
		$items = array_keys($data['items']);

        if(is_numeric($this->input->get('item_id'))):
            $item_query = $this->preview_specific_item($this->input->get('item_id'), $items);
        else:
            $sql_if = "(IF( CAST(avatar_items.gender AS CHAR) = 'Unisex',
    					    IF( CAST(avatar_item_parts.gender AS CHAR) = CAST(characters.gender as CHAR)
    					        OR CAST(avatar_item_parts.gender AS CHAR) = 'Unisex', true, false),
    				        true)
    				    )";

            $item_query = $this->db->select('image_path as path, avatar_item_parts.layer as layer_piece_id, avatar_layers.order, avatar_items.layer, avatar_items.item_id')
                                   ->from('avatar_item_parts')
                                   ->join('avatar_items', 'avatar_items.item_id = avatar_item_parts.item_id')
                                   ->join('avatar_layers', 'avatar_layers.id = avatar_item_parts.layer')
                                   ->join('character_items', 'character_items.item_id = avatar_items.item_id')
                                   ->join('characters', 'characters.character_id = character_items.character_id')
                                   ->where('character_items.character_id', $this->system->userdata['character_id'])
                                   ->where('avatar_item_parts.sprite_display_only', 0)
                                   ->where('character_items.id IN ('.implode(',', $items).')')
                                   ->where($sql_if)
                                   ->group_by('avatar_item_parts.layer')
                                   ->order_by('avatar_layers.order', 'asc')
                                   ->order_by('avatar_items.order', 'asc')
                                   ->get()
                                   ->result_array();
	    endif;

        $hat_equipped = 0;

        $item_list = array_reverse($item_query);
        foreach ($item_list as $item):
            if($item['layer'] == $this->main_hat_layer) $hat_equipped = 1;

		    if($hat_equipped != 1 || ! in_array($item['layer_piece_id'], $this->unequip_hat_layers)){
		        $list_of_items[] = $item;
		    }
        endforeach;

        $item_query = array_reverse($list_of_items);

        if($this->input->get('json') == TRUE && $this->saving == FALSE):
            $item_images = array();
            foreach ($item_query as $item):
                $item_images[] = $item['path'];
            endforeach;

            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(array_reverse(array_values($item_images)), JSON_NUMERIC_CHECK));
        else:
            if($this->use_imagick === TRUE):
                $base_image = new Imagick();

                if($sprite_sheet == FALSE):
                    $base_image->newImage($this->avatar_config['width'], $this->avatar_config['height'], 'none', 'gif');
                else:
                    $base_image->newImage($this->avatar_config['sheet_width'], $this->avatar_config['sheet_height'], 'none', 'gif');
                endif;

                if($double_size == TRUE) $base_image->newImage(140, 180, 'none', 'gif');

                foreach($item_query as $item):
                    $second = new Imagick(realpath(BASEPATH.$this->avatar_config['items_path'].'/'.($sprite_sheet == FALSE ? 'small_images/' : '').$item['path']));
                    $base_image->setImageColorspace($second->getImageColorspace());
                    $base_image->compositeImage($second, $second->getImageCompose(), 0, 0);
                    usleep(50000);
        		endforeach;

        		if($return == FALSE || ! is_bool($return)):
                    header('Content-type: image/gif');
                    echo $base_image;
        		else:
        			return $base_image;
        		endif;

                $base_image->clear();
                $base_image->destroy();
            else:
                if($sprite_sheet == FALSE && $double_size == FALSE):
                    $avatar_frame = imagecreatetruecolor($this->avatar_config['width'], $this->avatar_config['height']);
                    $localconf['width'] = $this->avatar_config['width'];
                    $localconf['height'] = $this->avatar_config['height'];
                elseif($double_size == TRUE && $sprite_sheet == FALSE):
                    $avatar_frame = imagecreatetruecolor(140, 180);
                    $localconf['width'] = 140;
                    $localconf['height'] = 180;
                else:
                    $avatar_frame = imagecreatetruecolor($this->avatar_config['sheet_width'], $this->avatar_config['sheet_height']);
                    $localconf['width'] = $this->avatar_config['sheet_width'];
                    $localconf['height'] = $this->avatar_config['sheet_height'];
                endif;

                // Transparecy please!
                $avatar_frame = $this->_gd_transparecy($avatar_frame);

            	foreach ($item_query as $item):
                    $new_image = imagecreatefrompng(realpath(BASEPATH.$this->avatar_config['items_path'].'/'.($sprite_sheet == FALSE ? 'small_images/' : '').$item['path']));
                    imagecopy($avatar_frame, $new_image, 0, 0, 0, 0, $localconf['width'], $localconf['height']);
                endforeach;

        		if($return == FALSE || ! is_bool($return)):
                    imagetruecolortopalette($avatar_frame, TRUE, 255256); // PNG-8 trick
                    header('Content-type: image/png');
                    imagepng($avatar_frame);
                    imagedestroy($avatar_frame);
        		else:
        			return $avatar_frame;
        		endif;
            endif;
        endif;
	}

	function save($ajax = false)
	{
	    $my_id = $this->session->userdata('id');
	    $this->saving = TRUE;
		$image_sheet = $this->preview(true, true);

		$path = realpath(BASEPATH.$this->avatar_config['avatar_path']).'/'.$my_id.'.gif';
		$sheet_path = realpath(BASEPATH.$this->avatar_config['avatar_path']).'/sprites/'.$my_id.'.gif';
		$thumb_path = realpath(BASEPATH.$this->avatar_config['avatar_path']).'/thumbnails/'.$my_id.'.gif';
		$flip_path = realpath(BASEPATH.$this->avatar_config['avatar_path']).'/flip/'.$my_id.'.gif';

		if($path):
		    if($this->use_imagick):
		        // Main avatar image
    		    $main_image = $image_sheet->clone();
                $main_image->cropImage(90, 90, 0, 180);
                $main_image->setImagePage(90, 90, 0, 0);

                // Flipped avatar image
    		    $flip_image = $main_image->clone();
                $flip_image->flopImage();

                // Thumbnail image
    		    $thumbnail = $main_image->clone();
                $thumbnail->cropImage(50, 50, 10, 18);
                $thumbnail->setImagePage(50, 50, 0, 0);

                switch($this->input->get('position')):
                    case 'sit':
                        $main_image = $image_sheet->clone();
                        $main_image->cropImage(90, 90, 450, 450);
                        $main_image->setImagePage(90, 90, 0, 0);

                        $flip_image = $main_image->clone();
                        $flip_image->flopImage();
                    break;
                    case 'action':
                        $main_image = $image_sheet->clone();
                        $main_image->cropImage(90, 90, 180, 540);
                        $main_image->setImagePage(90, 90, 0, 0);

                        $flip_image = $main_image->clone();
                        $flip_image->flopImage();
                    break;
                    case 'dance':
                        $main_image = $image_sheet->clone();
                        $main_image->cropImage(90, 90, 630, 0);
                        $main_image->setImagePage(90, 90, 0, 0);

                        $flip_image = $main_image->clone();
                        $flip_image->flopImage();
                    break;
                    default:

                    break;
                endswitch;

                // Save all of the images!
                $image_sheet->writeImage($sheet_path);
                $main_image->writeImage($path);
                $flip_image->writeImage($flip_path);
                $thumbnail->writeImage($thumb_path);

                // Then clear them all from memory. :D
                $thumbnail->clear();
                $thumbnail->destroy();
                $flip_image->clear();
                $flip_image->destroy();
                $main_image->clear();
                $main_image->destroy();
                $image_sheet->clear();
                $image_sheet->destroy();
		    else:
                imagetruecolortopalette($this->_gd_transparecy($image_sheet), TRUE, 256);

                // Create the main sprite
                imagepng($image_sheet, $sheet_path);

                // Main avatar sprite
                $regular_sprite = $this->_gd_transparecy(imagecreatetruecolor($this->avatar_config['width'], $this->avatar_config['height']));
                imagecopy($regular_sprite, $image_sheet, 0, 0, 0, 180, 90, 90);
                imagegif($regular_sprite, $path);
                imagedestroy($image_sheet);

                // Flipped avatar sprite
                $flipped_sprite = $this->_gd_transparecy(imagecreatetruecolor(90, 90));
                for($i = 0; $i < 90; $i++):
                    imagecopy($flipped_sprite, $regular_sprite, ($this->avatar_config['width'] - $i - 1), 0, $i, 0, 1, $this->avatar_config['height']);
                endfor;
                imagegif($flipped_sprite, $flip_path);
                imagedestroy($flipped_sprite);

                // Main avatar thumbnail
                $thumbnail_sprite = $this->_gd_transparecy(imagecreatetruecolor(50, 50));
                imagecopy($thumbnail_sprite, $regular_sprite, 0, 0, 10, 18, 90, 90);
                imagegif($thumbnail_sprite, $thumb_path);
                imagedestroy($thumbnail_sprite);

                imagedestroy($regular_sprite);
		    endif;

            // Unequip all...
            $this->db->where('character_id', $this->system->userdata['character_id'])
                     ->update('character_items', array('equipped'=> 0));

             $items = $this->_get_equipped_data();
             $items = array_keys($items['items']);

            // ...and re-equip the new saved items
            $this->db->where('character_id', $this->system->userdata['character_id'])
                     ->where_in('id', $items)
                     ->update('character_items', array('equipped' => 1));

            $this->_set_equipped_data(array());

            $this->db->where('id', $this->session->userdata('id'))
                     ->update('users', array('last_saved_avatar' => time()));

            if( ! is_ajax()): // If the call is not an AJAX one, sleep a bit and redirect back
                usleep(20000);
                redirect('avatar/');
            endif;
		else:
			trigger_error("An error occurred while attempting to save the avatar.");
		endif;
	}

    public function swap_gender()
    {
        $new_gender = "Male";
        if(strtolower($this->system->userdata['gender']) == "male") $new_gender = "Female";

        $this->db->where('user_id', $this->session->userdata('id'))->update('characters', array('gender' => $new_gender));

        redirect('avatar');
    }

    public function preview_shop_items()
    {
		$data = $this->_get_equipped_data();
		$items = array_keys($data['items']);
        $respond_items = array();

        foreach($this->input->post('items') as $item):
            $list_of_items = array();
            $item_query = $this->preview_specific_item($item, $items);
            $item_images = array();
            $hat_equipped = 0;

            $item_list = array_reverse($item_query);
            foreach ($item_list as $item_piece_data):
                if($item_piece_data['layer'] == $this->main_hat_layer) $hat_equipped = 1;

    		    if( ! ($hat_equipped == 1 && in_array($item_piece_data['layer_piece_id'], $this->unequip_hat_layers))):
  		            $list_of_items[] = $item_piece_data;
    		    endif;
            endforeach;

            foreach (array_reverse($list_of_items) as $item_data):
                $item_images[] = $item_data['path'];
            endforeach;

            $respond_items[$item] = array_values($item_images);
        endforeach;

        $this->output->set_content_type('application/json')
                     ->set_output(json_encode($respond_items, JSON_NUMERIC_CHECK));
    }

	public function use_item($item_id = 0)
	{
		// if( ! is_ajax()) show_error('You are not allowed to access this page');
		if( ! is_numeric($item_id)) show_error('item_id must be valid');

		$utility_item = $this->db->where(array('utility_items.id' => $item_id, 'type' => 'usable', 'character_id' => $this->system->userdata['character_id']))
		                         ->join('character_items', 'item_id = utility_items.id')
		                         ->get('utility_items');

		if($utility_item->num_rows() > 0):
			// utility_item_id found!
			$utility_item_data = $utility_item->row_array();

			if($utility_item_data['target'] == 'ally'):
			    $resoponse = "";

			    $battle = $this->db->where(array(
			        'character_id' => $this->system->userdata['character_id'],
			        'active_battle' => 1
			    ))->get('battle_characters');

			    if($battle->num_rows() > 0):
			    	// battle_id found!
		    	    die(json_encode(array('error' => 'You cannot use items in your avatar page while you\'re in a battle.')));
			    endif;

		        switch($utility_item_data['effect']):
		            case 'recover_hp':
		                $this->db->where('character_id', $this->system->userdata['character_id'])->update('characters', array(
		                    'hp' => min(($utility_item_data['points']+$this->system->userdata['hp']), $this->system->userdata['max_hp'])
		                ));

		        	    $resoponse = json_encode(array('response' => 'You have successfully recovered +'.$utility_item_data['points'].'HP!'), JSON_NUMERIC_CHECK);
		            break;
		        endswitch;

		        // Item used, let's dispose of it!
		        $this->db->where(array(
		            'item_id' => $item_id,
		            'type' => 'usable',
		            'character_id' => $this->system->userdata['character_id'],
		        ))->limit(1)->delete('character_items');

		        echo $resoponse;
			else:
		       die(json_encode(array('error' => 'This item is not usable!')));
			endif;
		else:
			// No dice.
		   die(json_encode(array('error' => 'This item could not be found inside your inventory')));
		endif;
	}

	public function quick_preview()
	{
	    $large_avatar = imagecreatetruecolor(140, 180);
	    $avatar = imagecreatefromgif('images/avatars/'.$this->session->userdata('id').'.gif');
        imagecopyresampled($this->_gd_transparecy($large_avatar), $avatar, 0, 0, 0, 0, 180, 180, 90, 90);
	    header('Content-type: image/gif');
        imagegif($large_avatar);
        imagedestroy($large_avatar);
	}

	public function preview_item($item_id = 0)
	{
	    if( ! is_numeric($item_id)) show_error('item_id must be valid');

	    $eye_combinations = array(356, 353, 374, 411);
	    $mouth_combinations = array(412, 413, 414);
	    $eyebrow_combinations = array(375, 393);

	    $selected = array(
	        'eye' => array_rand($eye_combinations),
	        'mouth' => array_rand($mouth_combinations),
	        'eyebrow' => array_rand($eyebrow_combinations),
	    );

	    $assembly_items = array(333, $eye_combinations[$selected['eye']], $mouth_combinations[$selected['mouth']], $eyebrow_combinations[$selected['eyebrow']]);

	    $preview_avatar = $this->_gd_transparecy(imagecreatetruecolor(140, 180));
	    $avatar_item = $this->db->where(array('item_id' => $item_id))->get('avatar_items');

	    if($avatar_item->num_rows() > 0):
	    	// avatar_item was successfully found!
	    	$avatar_item_data = $avatar_item->row_array();

            array_push($assembly_items, $item_id);

            $item_query = $this->db->select('image_path as path, avatar_item_parts.layer as layer_piece_id, avatar_layers.order, avatar_items.layer, avatar_items.item_id')
                              ->from('avatar_item_parts')
                              ->join('avatar_items', 'avatar_items.item_id = avatar_item_parts.item_id')
                              ->join('avatar_layers', 'avatar_layers.id = avatar_item_parts.layer')
                              ->where('avatar_item_parts.gender', $this->system->userdata['gender'])
                              ->where('avatar_item_parts.sprite_display_only', 0)
                              ->where('avatar_items.item_id IN ('.implode(',', $assembly_items).')')
                              ->group_by('avatar_item_parts.layer')
                              ->order_by('avatar_layers.order', 'asc')
                              ->order_by('avatar_items.order', 'asc')
                              ->get()
                              ->result_array();

            foreach ($item_query as $item):
                $new_image = imagecreatefrompng(realpath(BASEPATH.$this->avatar_config['items_path'].'/small_images/'.$item['path']));
                imagecopy($preview_avatar, $new_image, 0, 0, 0, 0, 140, 180);
            endforeach;

    	    header('Content-type: image/gif');
    	    imagegif($preview_avatar);
    	    imagedestroy($preview_avatar);
	    else:
            show_error('Item not found!');
	    endif;
	}

    public function update_cache_keys(){
        $data = $this->_get_equipped_data();
        $this->_set_equipped_data($data);
    }

	private function _get_equipped_data()
	{
        $avatar = apc_fetch($this->system->userdata['id'].'_avatar');

		if( ! is_null($this->avatar_data)):
			return $this->avatar_data;
		elseif($avatar):
			return $avatar;
		else:
			$query = $this->db->select('id as item_id')
							  ->where('character_id', $this->system->userdata['character_id'])
							  ->where('equipped','1')
							  ->get('character_items');

			$data = array('items' => array());

			foreach($query->result_array() as $row):
				$data['items'][$row['item_id']] = true;
			endforeach;

			$this->_set_equipped_data($data);
			return $data;
		endif;

		trigger_error("An unknown error occurred while trying to load your avatar data.");
	}


	private function _set_equipped_data($new = array(), $redirect = true)
	{
		$this->avatar_data = $new;
		apc_store($this->system->userdata['id'].'_avatar', $new, 300);
	}

	private function _isComposite($item_id = 0){
        $query = $this->db->select('avatar_layers.name as layername, avatar_layers.composite as layercomposite, avatar_layers.order as layerorder, avatar_layers.id as layerid, avatar_items.weight')
                          ->from('character_items')
                          ->join('avatar_items', 'avatar_items.item_id = character_items.item_id')
                          ->join('avatar_layers', 'avatar_layers.id = avatar_items.layer')
                          ->where('character_items.id', $item_id)
 						  ->get()
 						  ->result_array();

	    foreach ($query as $item):
	        if($item['layercomposite'] == 1):
				return array(
				    'composite' => true,
				    'layername' => $item['layername'],
				    'order' => $item['layerorder'],
				    'layerid' => $item['layerid'],
				    'weight' => $item['weight'],
				);
			else:
				$data = array(
				    'composite' => false,
				    'layername' => $item['layername'],
				    'order' => $item['layerorder'],
				    'layerid' => $item['layerid'],
				    'weight' => $item['weight'],
				);
			endif;
	    endforeach;

		return $data;
	}

	private function _gd_transparecy($image_obj = array())
	{
        $transcol = imagecolorallocatealpha($image_obj, 255, 0, 255, 127);
    	$trans = imagecolortransparent($image_obj, $transcol);
        imagefill($image_obj, 0, 0, $transcol);
        return $image_obj;
	}

	private function _get_weight($items)
	{
        $weight = $this->db->select('SUM(weight) as weight')
                           ->from('character_items')
                           ->join('avatar_items', 'character_items.item_id = avatar_items.item_id')
                           ->where('character_id', $this->system->userdata['character_id'])
                           ->where('character_items.id IN ('.implode(',', array_keys($items)).')')
                           ->get()
                           ->row()
                           ->weight;

        return $weight;
	}

}

/* End of file avatar.php */
/* Location: ./system/application/controllers/avatar.php */