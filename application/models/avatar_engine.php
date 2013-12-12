<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Avatar Engine
 *
 * @author(s) 	Tyler Diaz
 * @version 	1.1
 **/

class Avatar_engine extends CI_Model
{
	var $cache_prefix;
	var $memcached = FALSE;
	var $cache_durations = array(
		'total_registered_users'	=> 300 	// 5 minutes
	);
	var $character_id = 0;

	function __construct()
	{
		parent::__construct();
		// We only really need this stuff if the memcache is enabled
		if($this->memcached === TRUE):
			$this->cache_prefix = strtolower(get_class($this)).'_';
			$this->load->library('cache');
		endif;
	}

	// --------------------------------------------------------------------

	/**
	 * New page
	 *
	 * New page description
	 *
	 * @access	public
	 * @param	none
	 * @return	redirect
	 * @route	n/a
	 */

	function get_user_inventory($character_id = 0)
	{
		if($character_id == 0):
			$character_id = $this->character_id;
		else:
			$this->character_id = $character_id;
		endif;

		$sql = "SELECT cai.name as itemname,
		                al.order as layerorder,
		                al.name as layername,
		                al.id as layer_id,
		                al.main_tab,
		                caui.id as main_id,
		                cai.item_id as item_id,
		                avatar_layer_tabs.tab_name,
		                cai.thumb,
		                (
		                	SELECT count(air.child_id)
		                	FROM avatar_items_relations air
		                	WHERE air.child_id = cai.item_id
		                ) as item_identifier,
		                IF(
		                    (
		                    SELECT count(air.child_id)
		                    FROM avatar_items_relations air
		                    WHERE air.parent_id = cai.item_id
		                    ) > 0, UUID(), cai.item_id
		                ) as item_grouper,
		                count( caui.id ) AS num
		        FROM (`character_items` caui)
		        JOIN `avatar_items` cai ON `cai`.`item_id` = `caui`.`item_id`
		       # JOIN `characters` cu ON `cu`.`id` = `caui`.`user_id`
		        JOIN `avatar_layers` al ON al.id = cai.layer
		        JOIN `avatar_layer_tabs` ON avatar_layer_tabs.tab_id = al.main_tab
		        WHERE `al`.`composite` = '0'
		        AND `caui`.`character_id` = '".$this->system->userdata['character_id']."'
		        #AND (
		        #    CAST( cai.gender AS CHAR ) = CAST( cu.gender AS CHAR )
		        #    OR CAST( cai.gender AS CHAR ) = 'Unisex'
		        #)
		        GROUP BY item_grouper
		        HAVING item_identifier = 0";

		return $this->db->query($sql)->result_array();
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

    function get_item_siblings($parent_id = 0, $item_id = 0, $character_id = 0)
    {
        if($character_id == 0):
			$character_id = $this->character_id;
		else:
			$this->character_id = $character_id;
		endif;

		$query = $this->db->select('character_id, parent_id, equipped, item_id, id')
			 			  ->where('character_id', $this->System->userdata['character_id'])
			 			  ->where('parent_id', $parent_id)
			 			  ->where('item_id !=', $item_id)
			 			  ->get('character_items');

	    return $query;
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

    function get_avatar_inventory($character_id = 0)
    {
        if($character_id == 0):
			$character_id = $this->character_id;
		else:
			$this->character_id = $character_id;
		endif;

        $query = $this->db->query("SELECT *,
		                    al.order as layerorder,
		                    al.name as layername ,
		                    caui.id as main_id,
		                    count( caui.id ) as num
                          FROM (`character_items` caui)
                          JOIN `avatar_items` cai ON `cai`.`item_id` = `caui`.`item_id`
                          JOIN `users` cu ON `cu`.`character_id` = `caui`.`character_id`
                          JOIN `avatar_layers` al ON al.id = cai.layer
                          WHERE `al`.`composite` = '1'
                          AND `caui`.`character_id` = '".$character_id."'
                          AND (
                            CAST( cai.gender AS CHAR ) = CAST( cu.gender AS CHAR )
                            OR CAST( cai.gender AS CHAR ) = 'Unisex'
                          )
		                  GROUP BY `caui`.`item_id`")->result_array();

		return $query;
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

    function get_item_info($item_id = 0, $character_id = 0)
    {
        if($character_id == 0):
			$character_id = $this->character_id;
		else:
			$this->character_id = $character_id;
		endif;

        return $this->db->where('character_id',$character_id)
				        ->where('character_items.id',$item_id)
				        ->get('character_items');
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

    function unequip_all($character_id = 0)
    {
        if($character_id == 0):
			$character_id = $this->character_id;
		else:
			$this->character_id = $character_id;
		endif;

        $this->db->update('character_items', array('equipped' => '0'), array('character_id' => $character_id));
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

    function equip_items($items = array(), $character_id = 0)
    {
        if($character_id == 0):
			$character_id = $this->character_id;
		else:
			$this->character_id = $character_id;
		endif;

        $this->db->where('character_id', $character_id)
                 ->where_in('id', $items)
                 ->update('character_items', array('equipped' => '1'));

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

    function get_user_equipped_items($character_id = 0, $type = 'simple')
    {
        if($character_id == 0):
			$character_id = $this->character_id;
		else:
			$this->character_id = $character_id;
		endif;

        if($type == 'complex'):
            $query = $this->db->select('caip.image_path as path')
					          ->from('avatar_item_parts caip')
					          ->join('avatar_items cai','cai.item_id = caip.item_id')
					          ->join('avatar_layers cal', 'cal.id = caip.layer')
					          ->join('character_items caui', 'caui.item_id = cai.item_id')
					          ->order_by('cal.order','asc')
					          ->order_by('cai.order', 'asc')
					          ->where('caui.character_id',(is_null($character_id) ? $my_id : $character_id))
					          ->where('caui.equipped','1')
					          ->get();
		else:
		    $query = $this->db->select('id as item_id')
								->where('character_id', $character_id)
								->where('equipped', '1')
								->get('character_items');
		endif;
		return $query;
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

    function get_composite_item_data($item_id = 0)
    {
        $query	= $this->db->select('al.name as layername')
								->select('al.composite as layercomposite')
								->select('al.order as layerorder')
								->select('al.id as layerid')
								->from('avatar_items ai')
								->join('avatar_layers al','al.id = ai.layer')
								->join('character_items ui','ui.item_id = ai.item_id')
								->where('ui.id', $item_id)
								->get();
		return $query;
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

    function get_item_thumbnail($item_id = 0)
    {
        $query = $this->db->select('ai.item_id, ai.thumb')
		                    ->where('ui.id', $item_id)
		                    ->join('avatar_items ai','ai.item_id = ui.item_id')
		                    ->limit(1)
		                    ->get('character_items ui');

		return $query;
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

    function get_preview_items($items = array(), $character_id = 0)
    {
        if($character_id == 0):
			$character_id = $this->character_id;
		else:
			$this->character_id = $character_id;
		endif;

        $sql_if = "(IF( CAST(cai.gender AS CHAR) = 'Unisex',
					    IF( CAST(caip.gender AS CHAR) = CAST(u.gender as CHAR)
					        OR CAST(caip.gender AS CHAR) = 'Unisex', true, false),
				        true)
				    )";

        $query = $this->db->select('image_path as path, caip.layer, cal.order')
                          ->from('avatar_item_parts caip')
                          ->join('avatar_items cai', 'cai.item_id=caip.item_id')
                          ->join('avatar_layers cal', 'cal.id=caip.layer')
                          ->join('character_items caui', 'caui.item_id=cai.item_id')
                          ->join('users u', 'u.character_id=caui.character_id')
                          ->where('caui.character_id', $character_id)
                          ->where('caui.id IN ('.implode(',', $items).')')
                          ->where($sql_if)
                          ->group_by('caip.layer')
                          ->order_by('cal.order', 'asc')
                          ->order_by('cai.order', 'asc')
                          ->get();

        return $query;
    }
//---------------new get items after character_items
 function get_user_prew_items($items = array(), $character_id = 0)
    {
        if($character_id == 0):
			$character_id = $this->character_id;
		else:
			$this->character_id = $character_id;
		endif;

        $sql_if = "(IF( CAST(caip.gender AS CHAR) = 'Unisex',
					    IF( CAST(caip.gender AS CHAR) = CAST(u.gender as CHAR)
					        OR CAST(caip.gender AS CHAR) = 'Unisex', true, false),
				        true)
				    )";

        $query = $this->db->select('caip.*')
                          ->from('avatar_items caip')
                          ->join('avatar_layers cal', 'cal.id=caip.layer')
                          ->join('character_items caui', 'caui.item_id=caip.item_id')
                          ->join('users u', 'u.character_id=caui.character_id')
                          ->where('caui.character_id', $character_id)
                          ->where('caui.id IN ('.implode(',', $items).')')
                          //->where($sql_if)
                          ->order_by('cal.order', 'asc')
                          ->get()->result_array();

        if (!empty($query)) return $query; else return false;
    }
    function get_images($item_id=null, $character_id=0){
	 if($character_id == 0):
			$character_id = $this->character_id;
		else:
			$this->character_id = $character_id;
		endif;
	$sql_if = "(IF( CAST(cai.gender AS CHAR) = 'Unisex',
					    IF( CAST(caip.gender AS CHAR) = CAST(u.gender as CHAR)
					        OR CAST(caip.gender AS CHAR) = 'Unisex', true, false),
				        true)
				    )";
	$gender="";
	$users = $this->db->select('*')
                          ->from('users')
                          ->where('character_id',$character_id)
                          ->get()->result_array();
	foreach($users as $user):
		$gender=$user['gender'];
	endforeach;
        $query = $this->db->select('caip.*, cal.order')
                          ->from('avatar_item_parts caip')
                          ->join('avatar_items cai', 'cai.item_id=caip.item_id')
                          ->join('avatar_layers cal', 'cal.id=caip.layer')
                          ->where('caip.item_id ',$item_id)
                          ->where('caip.gender', $gender)
                          ->order_by('cal.order', 'asc')
                          ->get()->result_array();

         if (!empty($query)) return $query; else return false;
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

    function get_item_children($parent_item = 0, $character_id = 0)
    {
        if($character_id == 0):
			$character_id = $this->character_id;
		else:
			$this->character_id = $character_id;
		endif;
        $query = $this->db->where('ui.character_id', $character_id)
                              ->where('air.parent_id', $parent_item['item_id'])
                              ->where('ui.parent_id', $parent_item['main_id'])
                              ->join('avatar_items ai','ai.item_id = air.child_id')
                              ->join('character_items ui','ui.item_id = air.child_id')
                              ->join('avatar_layers al','al.id=ai.layer')
                              ->select('ai.name as itemname')
                              ->select('al.id as layer_id')
                              ->select('ui.id as main_id')
                              ->select('ai.item_id as item_id')
                              ->select('ai.thumb')
                              ->group_by('ai.item_id')
                              ->get('avatar_items_relations air');
        return $query;
    }

	// --------------------------------------------------------------------

	/**
	 * Engine configuration
	 *
	 * Allows the developer to arrange the global class variables
	 *
	 * @access	public
	 * @param	array
	 * @return	n/a
	 */

	function configure($preferences = array())
	{
		foreach($preferences as $config => $value):
			$this->{$config} = strtolower($value);
		endforeach;
	}


	// --------------------------------------------------------------------

	/**
	 * Convert array to an object
	 *
	 * Alright, I'll admit this completely kills one of the main
	 * purposes of using different formats in the first place, *speed*
	 * but on very special cases, it's the simplest solution.
	 *
	 * @access	private
	 * @param	n/a
	 * @return	object
	 */

	function _array_to_object($array = array())
	{
		if (!isset($array[0])) // isset() has proven to be the fastest to me.
		{
			$data = false;
			foreach ($array as $key => $val)
			{
				$data->{$key} = $val;
			}
			return $data;
		}
		return false;
	}

	// --------------------------------------------------------------------

	/**
	 * Purge Cache
	 *
	 * Sometimes cache's go cold, and they need to be purged for their own good
	 *
	 * @access	private
	 * @param	n/a
	 * @return	n/a
	 */

	function _purge_cache()
	{
		if($this->memcached == TRUE):
			foreach($this->cache_durations as $cache => $time):
				$this->cache->remove($this->cache_prefix.$cache);
			endforeach;
		endif;
	}

}

/* End of file forum_engine.php */
/* Location: ./system/application/models/forum_engine.php */
