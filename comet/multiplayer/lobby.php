<?php

/*
 * Tyler said:
 * By averge means, I would never write another raw PHP file
 * but this is a lot faster than having to run through CodeIgniter
 * all over again when I'm really not using some of it's core
 * components so I guess I can make an exception for the sake
 * of this being faster. Let's keep this as clean as possible!
 *
*/

ob_start();

ob_implicit_flush(true);
set_time_limit(0);

/*
 * SETUP POLL CONFIG FIRST
 * sleep_interval: How long should the script rest for?
 * total_poll_time: How long should the script run for?
*/
$poll_config = array(
    'sleep_interval' => 2,
    'total_poll_time' => 30,
);

/*
 * SECURITY CHECKS: It's a scary world out there.
*/
if( ! isset($_GET['party_id']) || ! isset($_GET['last_event'])):
    // Keep the script busy so it doesn't loop into oblivion
    sleep(5);
    die(json_encode(array('error' => 'Required data not found')));
endif;

if( ! is_numeric($_GET['party_id']) || ! is_numeric($_GET['last_event'])):
    // Keep the script busy so it doesn't loop into oblivion
    sleep(5);
    die(json_encode(array('error' => 'Non numeric format encountered')));
endif;

$party_id = $_GET['party_id'];
$last_event_id = $_GET['last_event'];

// Make sure the request is being done via AJAX
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'):

    $execution_start_time = time();
    $found = false;

	// Let's start up the event buffer if one doesn't exist.
	if( ! apc_fetch('party_event_key_'.$party_id)):
	    apc_store('party_event_key_'.$party_id, 0, 1800); // 30 mins
	endif;

    while($execution_start_time > (time()-($poll_config['total_poll_time']-2))): // Expire call a second earlier to prevent JS timeout
        $load_new_event = apc_fetch('party_event_key_'.$party_id);
        if($load_new_event > $last_event_id):

            $new_keys = 0;
            $return_data = array();

            while($new_keys < ($load_new_event-$last_event_id)):
                $obtain_key = apc_fetch('party_'.$party_id.'_event_'.($load_new_event-$new_keys));
                if($obtain_key):
                    $return_data[] = $obtain_key;
                endif;
                $new_keys++;
            endwhile;

            $found = true;
            die(json_encode(array('last_event_id' => $load_new_event, 'new_events' => $return_data)));
        endif;

        usleep($poll_config['sleep_interval']*100000); // check every x seconds
    endwhile;

    if($found == false):
        header('HTTP/1.1 500 Internal Server Error');
        die(json_encode(array('no_new_posts')));
        exit;
    endif;
else:
    die('Permission denied');
endif;
?>