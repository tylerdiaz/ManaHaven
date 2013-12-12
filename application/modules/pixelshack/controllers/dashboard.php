<?php
class Dashboard extends PCP_Controller {
	function index(){
        $this->system->quick_parse('main', array('page_title' => 'Pixel Shack', 'page_body' => 'admin'));
	}
	function info(){
		echo "Welcome...";
	}
}
?>