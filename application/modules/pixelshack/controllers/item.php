<?php

set_time_limit(3600);

class Item extends PCP_Controller {
    private $image_magick_location = 'sudo convert';
    private $color_data = array(
        '#960000' => array(
            'color_name' => 'Crimson',
            'base_color' => '#960000',
            'shade_color' => '#000000',
            'brightness' => 100,
        ),
        '#e30000' => array(
            'color_name' => 'Red',
            'base_color' => '#e30000',
            'shade_color' => '#150028',
            'brightness' => 100,
        ),
        '#ebcb9e' => array(
            'color_name' => 'Tan',
            'base_color' => '#ebcb9e',
            'shade_color' => '#791500',
            'brightness' => 100,
        ),
        '#ffc7d9' => array(
            'color_name' => 'Pink',
            'base_color' => '#ffc7d9',
            'shade_color' => '#d20065',
            'brightness' => 100,
        ),
        '#ff8100' => array(
            'color_name' => 'Orange',
            'base_color' => '#ff8100',
            'shade_color' => '#9e0011',
            'brightness' => 100,
        ),
        '#ffde00' => array(
            'color_name' => 'Gold',
            'base_color' => '#ffde00',
            'shade_color' => '#b30000',
            'brightness' => 100,
        ),
        '#a53f24' => array(
            'color_name' => 'Brown',
            'base_color' => '#a53f24',
            'shade_color' => '#0a0202',
            'brightness' => 100,
        ),
        '#005e00' => array(
            'color_name' => 'Forest',
            'base_color' => '#005e00',
            'shade_color' => '#000000',
            'brightness' => 100,
        ),
        '#0050eb' => array(
            'color_name' => 'Blue',
            'base_color' => '#0050eb',
            'shade_color' => '#000000',
            'brightness' => 100,
        ),
        '#7900df' => array(
            'color_name' => 'Purple',
            'base_color' => '#7900df',
            'shade_color' => '#000000',
            'brightness' => 100,
        ),
        '#ffebe6' => array(
            'color_name' => 'Rose',
            'base_color' => '#ffebe6',
            'shade_color' => '#e75797',
            'brightness' => 100,
        ),
        '#fff55a' => array(
            'color_name' => 'Yellow',
            'base_color' => '#fff55a',
            'shade_color' => '#dd6300',
            'brightness' => 100,
        ),
        '#a4f0ed' => array(
            'color_name' => 'Azure',
            'base_color' => '#a4f0ed',
            'shade_color' => '#000089',
            'brightness' => 100,
        ),
        '#eee1f9' => array(
            'color_name' => 'Lavender',
            'base_color' => '#eee1f9',
            'shade_color' => '#8600c5',
            'brightness' => 100,
        ),
        '#a1dd56' => array(
            'color_name' => 'Green',
            'base_color' => '#a1dd56',
            'shade_color' => '#001d49',
            'brightness' => 100,
        ),
        '#2e2e2e' => array(
            'color_name' => 'Black',
            'base_color' => '#2e2e2e',
            'shade_color' => '#000000',
            'brightness' => 100,
        ),
        '#dbdbdb' => array(
            'color_name' => 'Silver',
            'base_color' => '#dbdbdb',
            'shade_color' => '#1c1c1c',
            'brightness' => 100,
        ),
        '#ffffff' => array(
            'color_name' => 'White',
            'base_color' => '#ffffff',
            'shade_color' => '#acacac',
            'brightness' => 100,
        ),
    );

	public function index(){
		$this->load->view('item/index');
	}

	public function create(){
		$data = array(
			'gender'=> array(
				'unisex'	=> 'Unisex',
				'male'		=> 'Male Only',
				'female'	=> 'Female Only'
			),
			'class' => array(
				'all'	=> 'All Classes',
				'adventurer'	=> 'Adventurer Only',
				'archer'		=> 'Archer Only',
				'wizard'		=> 'Wizard Only',
			),
			'layers'=> $this->db->select('*')
			                    ->from('avatar_layers')
			                    ->order_by('order', 'asc')
			                    ->get()
			                    ->result_array(),
			'ordered_layers'=> $this->db->select('*')
			                            ->from('avatar_layers')
			                            ->order_by('name', 'asc')
			                            ->get()
			                            ->result_array(),
			'weight'=> array(
				'0'		=> '0 lb',
				'1'		=> '1 lb',
				'2'		=> '2 lbs',
				'3'		=> '3 lbs',
				'4'		=> '4 lbs',
				'5'		=> '5 lbs',
				'10'	=> '10 lbs',
				'20'	=> '20 lbs',
				'30'	=> '30 lbs',
				'40'	=> '40 lbs',
				'50'	=> '50 lbs',
			),
			'colors'=> array_keys($this->color_data),
		);
		$this->load->view('item/new',$data);
	}

	function create_base_item()
	{
	    $temporary_avatar_item = array(
	    	'name' => $this->input->post('item_name'),
	    	'description' => $this->input->post('item_description'),
	    	'gender' => ucfirst($this->input->post('gender')),
	    	'class' => $this->input->post('class'),
	    	'thumb' => '',
	    	'layer' => $this->input->post('main_layer'),
	    	'weight' => $this->input->post('weight'),
	    );

	    $this->db->insert('temporary_avatar_items', $temporary_avatar_item);
	    $main_item_id = $this->db->insert_id();
	    mkdir('uploads/'.$main_item_id);

	    $temporary_avatar_item_parts = array(
	    	'item_id' => $main_item_id,
	    	'layer' => 0,
	    	'image_path' => '',
	    	'gender' => 'Unknown'
	    );

	    $this->db->insert('temporary_avatar_item_parts', $temporary_avatar_item_parts);

        echo json_encode(array(
            'success' => true,
            'item_id' => $main_item_id,
            'start_piece_id' => $this->db->insert_id(),
            'data' => $temporary_avatar_item
        ));
	}


	public function debug_combination($item_id = 0)
	{
	    $item_pieces = $this->db->select('taip.*, avatar_layers.order')
                                ->from('temporary_avatar_item_parts taip')
                                ->where('item_id', $item_id)
                                ->join('avatar_layers', 'avatar_layers.id = taip.layer')
                                ->order_by('avatar_layers.order', 'DESC')
                                ->get()
                                ->result_array();

        $item_list = array();
        $item_data = array();
        $items_arranged = array();

        // Group them up!
        foreach ($item_pieces as $item):
            if($item['recolor_group'] > 0): // This item belongs to a group!
                $items_arranged['k'.$item['recolor_group']][$item['id']] = $item;
            else:
                $items_arranged[] = $item;
            endif;
        endforeach;

        foreach ($items_arranged as $key => $item):
            // Is this a group of items? Or is it just one piece?
            if(preg_match('/k/', $key)){
                foreach ($item as $sub_item):
                    $item_data['group_'.substr($key, 1)][$sub_item['id']] = $sub_item;
                endforeach;
                $last_item = end($item);
                $item_list['group_'.substr($key, 1)] = unserialize($last_item['recolor_data']);
            } else {
                $item_data[$item['id']] = $item;

    			if($item['recolor'] == 1):
    				$item_list[$item['id']] = unserialize($item['recolor_data']);
    			else:
    				$item_list[$item['id']] = array('default');
    			endif;
            }
        endforeach;

    	$item_combinations = array();
        $this->combination($item_list, $item_combinations, array_keys($item_list));

        echo "<pre>";

        foreach ($item_combinations as $new_item):

            foreach ($new_item as $sub_item_id => $color):
                if(preg_match('/group/', $sub_item_id)){
                    // Remove the "group_"(6) string
                    foreach ($items_arranged['k'.substr($sub_item_id, 6)] as $sub_group_item_id => $sub_item_color):
                        echo "GR:".print_r(array(
                        	'sub_item' => $item_data[$sub_item_id][$sub_group_item_id],
                        	'item_id' => $item_id,
                        	'color' => $color
                    	));
                    endforeach;
                } else {
                    echo print_r(array(
                    	'sub_item' => $item_data[$sub_item_id],
                    	'item_id' => $item_id,
                    	'color' => $color
                	));
                }
            endforeach;

        endforeach;

        die(print_r($item_combinations));
	}

	public function create_item_part($piece_id = 0, $item_id = 0)
	{
	    $file_string = explode('.', $_FILES["image_file"]["name"]);

	    $file_name = md5($file_string[0].time()).'.'.$file_string[1];

	    move_uploaded_file($_FILES["image_file"]["tmp_name"], "uploads/".$item_id."/".$file_name);

        $temporary_avatar_item_parts = array(
            'item_id' => $item_id,
        	'layer' => $this->input->post('piece_layer'),
        	'image_path' => $file_name,
        	'gender' => $this->input->post('gender_piece'),
        	'recolor' => ($this->input->post('recolor_item') == TRUE ? 1 : 0),
        	'recolor_group' => $this->input->post('color_group')
        );

        if(is_array($this->input->post('color')) && $this->input->post('recolor_item') == TRUE):
            $temporary_avatar_item_parts['recolor_data'] = serialize($this->input->post('color'));
        else:
            $temporary_avatar_item_parts['recolor_data'] = NULL;
        endif;

        $this->db->where('id', $piece_id)->update('temporary_avatar_item_parts', $temporary_avatar_item_parts);

        echo json_encode(array(
            'success' => true,
            'item_part_id' => $this->db->insert_id(),
            'data' => $temporary_avatar_item_parts
        ));
	}

    public function split_unisex_pieces($item_id = 0)
    {
        $item_pieces = $this->db->select('taip.*, avatar_layers.order')
                                ->from('temporary_avatar_item_parts taip')
                                ->where('item_id', $item_id)
                                ->join('avatar_layers', 'avatar_layers.id = taip.layer')
                                ->order_by('avatar_layers.order', 'DESC')
                                ->get()
                                ->result_array();

        echo print_r($item_pieces);
    }

	public function generate_thumbnail($item_id = 0, $thumbnail_name = '', $location = 'images/avatar_items/thumbnails')
	{
        $item_pieces = $this->db->select('aip.*, avatar_layers.order')
                                ->from('avatar_item_parts aip')
                                ->where('item_id', $item_id)
                                ->where('gender', 'Male')
                                ->join('avatar_layers', 'avatar_layers.id = aip.layer')
                                ->order_by('avatar_layers.order', 'ASC')
                                ->get()
                                ->result_array();

        $image = imagecreatetruecolor(720, 720);

		$transcol	= imagecolorallocatealpha($image, 255, 0, 255, 127);
		$trans		= imagecolortransparent($image,$transcol);

		imagefill($image, 0, 0, $transcol);
		imagesavealpha($image, true);
		imagealphablending($image, true);

        foreach($item_pieces as $item_piece):
            $image = $this->merge_layers($image, 'images/avatar_items/'.$item_piece['image_path']);
        endforeach;

        $transcol = imagecolorallocatealpha($image, 255, 0, 255, 127);
		$trans = imagecolortransparent($image,$transcol);

	    imagegif($image, $location.'/temp_'.$thumbnail_name.'.gif');

	     // Image Magick thumbnail
        passthru($this->image_magick_location.' '.$location.'/temp_'.$thumbnail_name.'.gif'.' -crop 90x90+0+0 -trim +repage '.$location.'/temp_'.$thumbnail_name.'.gif', $response1);

        passthru($this->image_magick_location.' '.$location.'/temp_'.$thumbnail_name.'.gif'.' -background transparent -gravity Center -resize "70x70>" -extent 70x70 -crop 38x38+0+0 -gravity Center PNG32:'.$location.'/'.$thumbnail_name.'.png', $response2);

        // Debugger addition
        // $thumb_debug = imagecreatefrompng($location.'/'.$thumbnail_name.'.png');
        // $transcol    = imagecolorallocatealpha($thumb_debug, 255, 0, 255, 127);
        // $trans       = imagecolortransparent($thumb_debug,$transcol);
        //
        // imagefill($thumb_debug, 0, 0, $transcol);
        // imagesavealpha($thumb_debug, true);
        // imagealphablending($thumb_debug, true);
        //
        // header('content-type: image/png');
        // imagepng($thumb_debug);
	}

	public function merge_layers($layer1, $layer2, $config = array('width' => 720, 'height' => 720))
	{
    	$layer2 	= imagecreatefrompng($layer2);
    	$transcol	= imagecolorallocatealpha($layer2, 255, 0, 255, 127);
    	$trans		= imagecolortransparent($layer2,$transcol);
    	imagealphablending($layer2, true);
    	imagesavealpha($layer2, true);
    	imagecopy($layer1,$layer2,0,0,0,0,$config['width'],$config['height']);
    	$transcol	= imagecolorallocatealpha($layer1, 255, 0, 255, 127);
    	$trans		= imagecolortransparent($layer1,$transcol);

    	return $layer1;
	}


	public function new_item_part($item_id = 0)
	{
	    $temporary_avatar_item_parts = array(
	    	'item_id' => $item_id,
	    	'layer' => 0,
	    	'image_path' => '',
	    	'gender' => 'Unknown'
	    );

	    $this->db->insert('temporary_avatar_item_parts', $temporary_avatar_item_parts);
	    echo json_encode(array('success' => 1, 'item_part_id' => $this->db->insert_id()));
	}

    public function install_item($item_id = 0, $bulk_recolor = false)
    {
        if($bulk_recolor == true){
            $bulk_recolor = array_keys($this->color_data);
        } else {
            $bulk_recolor = $this->input->post('bulk_recolor_item');
        }

        $item_pieces = $this->db->select('taip.*, avatar_layers.order')
                                ->from('temporary_avatar_item_parts taip')
                                ->where('item_id', $item_id)
                                ->join('avatar_layers', 'avatar_layers.id = taip.layer')
                                ->order_by('avatar_layers.order', 'DESC')
                                ->get()
                                ->result_array();

        $main_item = $this->db->select('*')
                              ->from('temporary_avatar_items')
                              ->where('item_id', $item_id)
                              ->get()
                              ->row_array();

        $insert_insert_increment = 18;

        // String casted booleans. Hacky, but JS is messing with it, not worth the war
        if($this->input->post('bulk_recolor_item') == "true"){
            foreach($this->input->post('bulk_recolor_data') as $color):

                $thumbnail_file_name = md5($main_item['name'].substr($color, 1).time()).'.png';

                // Install the item
                $temporary_avatar_item = array(
                	'name' => preg_replace('/{color}/', $this->color_data[$color]['color_name'], $main_item['name']),
                	'description' => $main_item['description'],
                	'gender' => $main_item['gender'],
                	'class' => $main_item['class'],
                	'thumb' => $thumbnail_file_name,
                	'order' => $main_item['order'],
                	'layer' => $main_item['layer'],
                	'default' => $main_item['default'],
                	'composite' => $main_item['composite'],
                	'weight' => $main_item['weight'],
                	'item_hash' => md5($main_item['name'].substr($main_item['description'], 0, 5))
                );

                $this->db->insert('avatar_items', $temporary_avatar_item);

                $colored_item_id = $this->db->insert_id();

                foreach ($item_pieces as $item_piece):
                    $this->recolor_item_piece(array(
                        'sub_item' => $item_piece,
                        'item_id' => $item_id,
                        'new_item_id' => $colored_item_id,
                        'color' => $color,
                        'group' => 0
                    ));
                endforeach;

                // Regenerate the thumbnail
                $this->generate_thumbnail($colored_item_id, $thumbnail_file_name);

                if(($insert_insert_increment % 18) == 1){
                    $this->db->insert('character_items', array('character_id' => 18, 'item_id' => $colored_item_id, 'type' => 'equipable'));
                	$this->db->insert('character_items', array('character_id' => 13, 'item_id' => $colored_item_id, 'type' => 'equipable'));
                }
                $insert_insert_increment++;
            endforeach;

            echo json_encode(array('recolor' => 1));
        } else {
            $item_list = array();
            $item_data = array();
            $items_arranged = array();

            // Group them up!
            foreach ($item_pieces as $item):
                if($item['recolor_group'] > 0): // This item belongs to a group!
                    $items_arranged['k'.$item['recolor_group']][$item['id']] = $item;
                else:
                    $items_arranged[] = $item;
                endif;
            endforeach;

            foreach ($items_arranged as $key => $item):
                // Is this a group of items? Or is it just one piece?
                if(preg_match('/k/', $key)){
                    foreach ($item as $sub_item):
                        $item_data['group_'.substr($key, 1)][$sub_item['id']] = $sub_item;
                    endforeach;

                    $last_item = end($item);

                    if($last_item['recolor'] == 1):
                        $item_list['group_'.substr($key, 1)] = unserialize($last_item['recolor_data']);
                    else:
                        $last_item['recolor_data'] = array('default');
                    endif;

                } else {
                    $item_data[$item['id']] = $item;

        			if($item['recolor'] == 1):
        				$item_list[$item['id']] = unserialize($item['recolor_data']);
        			else:
        				$item_list[$item['id']] = array('default');
        			endif;
                }
            endforeach;

        	$item_combinations = array();
            $this->combination($item_list, $item_combinations, array_keys($item_list));

            foreach ($item_combinations as $new_item):
                $thumbnail_file_name = md5($main_item['name'].time()).'.png';

                // $new_name = $main_item['name'];
                // if(isset($this->color_data[$color]['color_name'])){
                //     $new_name = preg_replace('/{color}/', $this->color_data[$color]['color_name'], $main_item['name']);
                // }

                $item_name = $main_item['name'];

                $temporary_avatar_item = array(
                	'name' => $main_item['name'],
                	'description' => $main_item['description'],
                	'gender' => $main_item['gender'],
                	'class' => $main_item['class'],
                	'thumb' => $thumbnail_file_name,
                	'order' => $main_item['order'],
                	'layer' => $main_item['layer'],
                	'default' => $main_item['default'],
                	'composite' => $main_item['composite'],
                	'weight' => $main_item['weight'],
                	'item_hash' => md5($main_item['name'].substr($main_item['description'], 0, 5))
                );

                $this->db->insert('avatar_items', $temporary_avatar_item);
            	$colored_item_id = $this->db->insert_id();

                $color_name_iterator = 1; // This is what I'll use to carve out the {color:1} to the actual color

                foreach ($new_item as $sub_item_id => $color):
                    if(preg_match('/group/', $sub_item_id)){
                        // Remove the "group_"(6) string
                        foreach ($items_arranged['k'.substr($sub_item_id, 6)] as $sub_group_item_id => $sub_item_color):
                            if($color != 'default'):
                                $item_name = preg_replace('/{color:'.$color_name_iterator.'}/', $this->color_data[$color]['color_name'], $item_name);
                                $color_name_iterator++;
                            endif;

                            $this->recolor_item_piece(array(
                            	'sub_item' => $item_data[$sub_item_id][$sub_group_item_id],
                            	'item_id' => $item_id,
                            	'new_item_id' => $colored_item_id,
                            	'color' => $color,
                            	'group' => substr($sub_item_id, 6)
                        	));

                        endforeach;
                    } else {
                        if($color != 'default'):
                            $item_name = preg_replace('/{color:'.$color_name_iterator.'}/', $this->color_data[$color]['color_name'], $item_name);
                            $color_name_iterator++;
                        endif;

                        $this->recolor_item_piece(array(
                        	'sub_item' => $item_data[$sub_item_id],
                        	'item_id' => $item_id,
                        	'new_item_id' => $colored_item_id,
                        	'color' => $color,
                        	'group' => 0
                    	));
                    }
                endforeach;

                // Give this item it's brand new name! :D
                $this->db->where('item_id', $colored_item_id)->update('avatar_items', array('name' => preg_replace('/{color:[0-9]}/', '', $item_name)));

                $this->generate_thumbnail($colored_item_id, $thumbnail_file_name);

                if(($insert_insert_increment % 18) == 1){
                    $this->db->insert('character_items', array('character_id' => 18, 'item_id' => $colored_item_id, 'type' => 'equipable'));
                	$this->db->insert('character_items', array('character_id' => 13, 'item_id' => $colored_item_id, 'type' => 'equipable'));
                }
                $insert_insert_increment++;
            	usleep(7500);
            endforeach;

            echo json_encode(array('recolor' => 1));
        }
    }

    public function recolor_item_piece($main_data = array())
    {
        $return_value = array();

        $old_item_piece_location = 'uploads/'.$main_data['item_id'].'/'.$main_data['sub_item']['image_path'];

        if(strtolower($main_data['color']) == "default"): // Upload the bare item?
            $new_item_piece_location = 'images/avatar_items/'.$main_data['sub_item']['image_path'];
            $new_small_item_piece_location = 'images/avatar_items/small_images/'.$main_data['sub_item']['image_path'];

            copy($old_item_piece_location, $new_item_piece_location);

            if(strtolower($main_data['sub_item']['gender']) == "unisex"){
                $avatar_item_part = array(
                    'item_id'     => $main_data['new_item_id'],
                    'name'        => $main_data['sub_item']['name'],
                    'layer'       => $main_data['sub_item']['layer'],
                    'image_path'  => $main_data['sub_item']['image_path'],
                    'gender'      => 'Female',
                    'base_color'  => 'default',
                    'shade_color' => 'default',
                    'group_color' => $main_data['group'],
                    'recolorable' => 0
                );

                $this->db->insert('avatar_item_parts', $avatar_item_part);

                $avatar_item_part = array(
                    'item_id'     => $main_data['new_item_id'],
                    'name'        => $main_data['sub_item']['name'],
                    'layer'       => $main_data['sub_item']['layer'],
                    'image_path'  => $main_data['sub_item']['image_path'],
                    'gender'      => 'Male',
                    'base_color'  => 'default',
                    'shade_color' => 'default',
                    'group_color' => $main_data['group'],
                    'recolorable' => 0
                );

                $this->db->insert('avatar_item_parts', $avatar_item_part);
            } else {
                $avatar_item_part = array(
                    'item_id'     => $main_data['new_item_id'],
                    'name'        => $main_data['sub_item']['name'],
                    'layer'       => $main_data['sub_item']['layer'],
                    'image_path'  => $main_data['sub_item']['image_path'],
                    'gender'      => $main_data['sub_item']['gender'],
                    'base_color'  => 'default',
                    'shade_color' => 'default',
                    'group_color' => $main_data['group'],
                    'recolorable' => 0
                );

                $this->db->insert('avatar_item_parts', $avatar_item_part);
            }
        else:
            // Color away!
            // For the sake of refactoring:
            $new_item_piece_location = 'images/avatar_items/'.substr($main_data['color'], 1).'_'.$main_data['sub_item']['image_path'];
            $new_small_item_piece_location = 'images/avatar_items/small_images/'.substr($main_data['color'], 1).'_'.$main_data['sub_item']['image_path'];

            $level_colors = '+level-colors "'.$this->color_data[$main_data['color']]['shade_color'].'","'.$this->color_data[$main_data['color']]['base_color'].'"';

            // First we grayscale | convert image.png -fx G PNG32:newimage.png
            exec($this->image_magick_location.' '.$old_item_piece_location.' -fx G PNG32:'.$new_item_piece_location);

            // Then we save & recolor | convert image.png +level-colors "shade","main" PNG32:newimage.png
            exec($this->image_magick_location.' '.$old_item_piece_location.' '.$level_colors.' PNG32:'.$new_item_piece_location, $return_value);

            if(strtolower($main_data['sub_item']['gender']) == "unisex"){
                $avatar_item_part = array(
                	'item_id' => $main_data['new_item_id'],
                	'name' => $main_data['sub_item']['name'],
                	'layer' => $main_data['sub_item']['layer'],
                	'image_path' => substr($main_data['color'], 1).'_'.$main_data['sub_item']['image_path'],
                	'gender' => 'Female',
                	'base_color' => $this->color_data[$main_data['color']]['base_color'],
                	'shade_color' => $this->color_data[$main_data['color']]['shade_color'],
                	'group_color' => $main_data['group'],
                	'recolorable' => 1
                );
                $this->db->insert('avatar_item_parts', $avatar_item_part);

                $avatar_item_part = array(
                	'item_id' => $main_data['new_item_id'],
                	'name' => $main_data['sub_item']['name'],
                	'layer' => $main_data['sub_item']['layer'],
                	'image_path' => substr($main_data['color'], 1).'_'.$main_data['sub_item']['image_path'],
                	'gender' => 'Male',
                	'base_color' => $this->color_data[$main_data['color']]['base_color'],
                	'shade_color' => $this->color_data[$main_data['color']]['shade_color'],
                	'group_color' => $main_data['group'],
                	'recolorable' => 1
                );
                $this->db->insert('avatar_item_parts', $avatar_item_part);

            } else {
                $avatar_item_part = array(
                	'item_id' => $main_data['new_item_id'],
                	'name' => $main_data['sub_item']['name'],
                	'layer' => $main_data['sub_item']['layer'],
                	'image_path' => substr($main_data['color'], 1).'_'.$main_data['sub_item']['image_path'],
                	'gender' => $main_data['sub_item']['gender'],
                	'base_color' => $this->color_data[$main_data['color']]['base_color'],
                	'shade_color' => $this->color_data[$main_data['color']]['shade_color'],
                	'group_color' => $main_data['group'],
                	'recolorable' => 1
                );
                $this->db->insert('avatar_item_parts', $avatar_item_part);
            }
        endif;

        exec($this->image_magick_location.' '.$new_item_piece_location.' -sample 200% -crop 140x180+10+0 +repage  '.$new_small_item_piece_location);
    }


	public function combination($array, array &$results, $keys = array(), $str = '')
	{
	    $current = array_shift($array);

		if(count($array) > 0):
		    foreach($current as $element):
		        $this->combination($array, $results, $keys, $str.'|'.$element);
		    endforeach;
		else:
		    foreach($current as $element):
				$combination = explode('|', substr($str.'|'.$element, 1));
		        $results[] = array_combine($keys, $combination);
		    endforeach;
		endif;
	}


	public function delete_item_part($item_part_id = 0)
	{
	    $this->db->where('id', $item_part_id)->delete('temporary_avatar_item_parts');
	}

}
?>