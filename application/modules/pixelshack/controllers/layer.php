<?php

class Layer extends PCP_Controller {
	/**
     * Index
     *
     * Main page for managing Layers for ManaHaven
     *
     * @access  public
     * @param   none
     * @return  none
     * @route   n/a
     */ 
        
    public function index()
    {
        $this->load->view('layer/index', array('layers' => array_reverse($this->db->order_by('order', 'asc')->get('avatar_layers')->result_array())));
    }
    
    // --------------------------------------------------------------------

    /**
     * Create Layer
     *
     * Create a new Layer in given position allowing further updates
     *
     * @access  public
     * @param   none
     * @return  none
     * @route   n/a
     */ 
        
    public function create($after = NULL)
    {
        $name = $this->input->post('name');
        $layers = ($this->db->order_by('order', 'asc')->get('avatar_layers')->result_array());
        
        if($after === NULL):
            $layer_order = 0;
        else:
            $layer_order = $after+1;
        endif;

        $order_i = 0;
        foreach ($layers as $layer):
            if($order_i == $layer_order):
                $this->db->insert('avatar_layers', array('order' => $order_i, 'name' => $name));
            else:
                $this->db->where(array('id' => $layer['id']))->update('avatar_layers', array('order' => $order_i));
            endif;
            $order_i++;
        endforeach;
    }
    
    // --------------------------------------------------------------------

    /**
     * Delete Layer
     *
     * Remove a given layer based on its order id.
     *
     * @access  public
     * @param   int order_id
     * @return  redirect
     * @route   n/a
     */ 
        
    public function delete($order_id = 0)
    {
        $this->db->where('order', $order_id)->delete('avatar_layers');
        $layers = $this->db->order_by('order', 'asc')
                          ->get('avatar_layers')
                          ->result_array();

        $order_i = 0;
        
        foreach ($layers as $layer):
            $this->db->where(array('id' => $layer['id']))->update('avatar_layers', array('order' => $order_i));
            $order_i++;
        endforeach;
        
        redirect('pixelshack/dashboard/');
    }
    
    // --------------------------------------------------------------------

    /**
     * Reorganize Layers
     *
     * Change the order of the layers
     *
     * @access  public
     * @param   none
     * @return  redirect
     * @route   n/a
     */ 
        
    public function reorganize()
    {
        $layer_order = array_reverse($this->input->get('recordsArray'));
        
        $order_i = 0;
        foreach ($layer_order as $layer_id):
            $this->db->where(array('id' => $layer_id))->update('avatar_layers', array('order' => $order_i));
            $order_i++;
        endforeach;
		
		if( ! is_ajax()) redirect('pixelshack/dashboard/');
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
        
    public function rename($order_id)
    {
        $this->db->where('order', $order_id)->update('avatar_layers', array('name' => $this->input->post('name')));
        
        echo json_encode(array('success' => 1));
    }
    
}
?>