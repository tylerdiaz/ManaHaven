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
 * SECURITY CHECKS: It's a scary world out there.
*/
if( ! isset($_GET['topic_id']) || ! isset($_GET['last_post'])):
    // Keep the script busy so it doesn't loop into oblivion
    sleep(5);
    die(json_encode(array('error' => 'Required data not found')));
endif;

if( ! is_numeric($_GET['topic_id']) || ! is_numeric($_GET['last_post'])):
    // Keep the script busy so it doesn't loop into oblivion
    sleep(5);
    die(json_encode(array('error' => 'Non numeric format encountered')));
endif;

$topic_id = $_GET['topic_id'];
$last_post = $_GET['last_post'];
$sleep_time = 1;

if(isset($_GET['slow_load']) && $_GET['slow_load'] == TRUE) $sleep_time = 4;


if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'):

    $execution_start_time = time();
    $found = false;

    if( ! apc_fetch('t'.$topic_id.'_post_key')){
        apc_store('t'.$topic_id.'_post_key', $last_post, 150);
    }

    while($execution_start_time > (time()-28)): // Expire call within 28 seconds
        $load_key = apc_fetch('t'.$topic_id.'_post_key');
        if($load_key > $last_post){
            $new_keys = 0;

            $return_data = array();

            while($new_keys < $load_key-$last_post):
                $obtain_key = apc_fetch('t'.$topic_id.'p'.($load_key-$new_keys));
                if($obtain_key) $return_data[] = $obtain_key;
                $new_keys++;
            endwhile;

            $found = true;
            die(json_encode(array('new_posts' => array_reverse($return_data))));
        }

        sleep($sleep_time); // check every 1 second
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