<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Cache/Archive script
 *
 * @author(s) 	Tyler Diaz
 * @version 	1.0
 **/

class Cache
{
    var $memcache = FALSE;

	function __construct(){
		if(class_exists('Memcache')):
		    $this->memcache = new Memcache();
		    // Using the @ symbol to supress errors, just incase the servers are offline
            if( ! @$this->memcache->connect('127.0.0.1', 11211)) $this->memcache = FALSE;
		endif;
	}

	function set($key = '', $value = '', $expires_in = 300){
	    if($this->memcache != FALSE):
            return $this->memcache->set($key, $value, 0, $expires_in);
        else:
            return FALSE;
	    endif;
	}

	function replace($key = '', $value = '', $expires_in = 300){
	    if($this->memcache != FALSE):
            return $this->memcache->replace($key, $value, 0, $expires_in);
        else:
            return FALSE;
	    endif;
	}

	function get($key = ''){
	    if($this->memcache != FALSE):
	        return $this->memcache->get($key);
        else:
            return FALSE;
	    endif;
	}

	function __destruct(){
		if(class_exists('Memcache')):
              $this->memcache->close();
		endif;
	}

}
?>