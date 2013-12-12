<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Docs Controller
 *
 * @author(s) Tyler Diaz
 * @version 1.0
 **/

class Docs extends CI_Controller
{

	public function terms_of_service()
	{
	   	$view_data = array(
	   		'page_title' => 'Docs &emdash; Terms of Service',
	   		'page_body' => 'docs'
	   	);

	   	$this->system->quick_parse('docs/terms_of_service', $view_data, false);
	}

	public function privacy()
	{
	   	$view_data = array(
	   		'page_title' => 'Docs &emdash; Private Policy',
	   		'page_body' => 'docs'
	   	);

	   	$this->system->quick_parse('docs/privacy', $view_data);
	}

}

/* End of file docs.php */
/* Location: ./system/application/controllers/docs.php */