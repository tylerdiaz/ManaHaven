<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Site URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('site_url'))
{
	function site_url($uri = '')
	{
		$CI =& get_instance();
		return $CI->config->site_url($uri);
	}
}

// ------------------------------------------------------------------------

/**
 * Base URL
 *
 * Returns the "base_url" item from your config file
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('base_url'))
{
	function base_url()
	{
		$CI =& get_instance();
		return $CI->config->slash_item('base_url');
	}
}

// ------------------------------------------------------------------------

/**
 * Header Redirect
 *
 * Header redirect in two flavors
 * For very fine grained control over headers, you could use the Output
 * Library's set_header() function.
 *
 * @access	public
 * @param	string	the URL
 * @param	string	the method: location or redirect
 * @return	string
 */
if ( ! function_exists('redirect'))
{
	function redirect($uri = '', $method = 'location', $http_response_code = 302)
	{
		if ( ! preg_match('#^https?://#i', $uri))
		{
			$uri = site_url($uri);
		}
		
		switch($method)
		{
			case 'refresh'	: header("Refresh:0;url=".$uri);
				break;
			default			: header("Location: ".$uri, TRUE, $http_response_code);
				break;
		}
		exit;
	}
}

// ------------------------------------------------------------------------

/**
 * Grab the percentage of 2 values
 *
 * @return void
 * @author Tyler Diaz
 **/
if ( ! function_exists('percent'))
{
	function percent($num_amount, $num_total, $over_exceed = FALSE){
	    if( ! $over_exceed):
	        $num_amount = (($num_amount > $num_total) ? $num_total : $num_amount);
	    endif;
		return floor(number_format(($num_amount / $num_total) * 100, 1));
	}
}


// ------------------------------------------------------------------------


/**
 * [Jan 18, 2009] becomes [19 days ago]
 *
 * @return void
 * @author Tyler Diaz
 **/
function human_time($date = "")
{
    if(empty($date)) return "No date avalible!";
    $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
    $lengths = array("60","60","24","7","4.35","12","10");
    $now = time();
    $unix_date = strtotime($date);
    if(empty($unix_date)) return "Curropted date";

    if($now > $unix_date) 
    {
        $difference = $now - $unix_date;
        $tense = "ago";
    }
    else 
    {
        $difference = $unix_date - $now;
        $tense = "from now";
    }
    
    for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) $difference /= $lengths[$j];
    $difference = round($difference);
    if($difference != 1) $periods[$j].= "s";
    return "$difference $periods[$j] {$tense}";
}

// ------------------------------------------------------------------------

/**
 * Alternator
 *
 * Allows strings to be alternated.  See docs...
 *
 * @access	public
 * @param	string (as many parameters as needed)
 * @return	string
 */	
if ( ! function_exists('cycle'))
{
	function cycle()
	{
		static $i;	

		if (func_num_args() == 0)
		{
			$i = 0;
			return '';
		}
		$args = func_get_args();
		return $args[($i++ % count($args))];
	}
}


// ------------------------------------------------------------------------

/**
 * Current_url
 *
 * Displays the current URL being accessed
 *
 * @access	public
 * @param	string (as many parameters as needed)
 * @return	string
 */	
if ( ! function_exists('current_url'))
{
	function current_url()
	{
		$CI =& get_instance();
		return $CI->config->site_url($CI->uri->uri_string());
	}
}

// ------------------------------------------------------------------------

/**
 * Language loader
 *
 * Allows strings to be loaded from language files.
 *
 * @access	public
 * @return	string
 */	
if ( ! function_exists('lang'))
{
	function lang($line, $id = '')
	{
		$CI =& get_instance();
		$line = $CI->lang->line($line);

		if ($id != '')
		{
			$line = '<label for="'.$id.'">'.$line."</label>";
		}

		return $line;
	}
}


/* 
*  View helpers, you know,
*  to make view code nicer. :D
*/

if ( ! function_exists('script'))
{
	function script($src = '')
	{
		$CI =& get_instance();

		$link = '<script type="text/javascript" ';

		if (is_array($src))
		{
			foreach ($src as $k=>$v)
			{
				if ($k == 'src' AND strpos($v, '://') === FALSE)
				{
					$link .= 'src="'.$CI->config->slash_item('base_js_url').$v.'"';
				}
				else
				{
					$link .= "$k=\"$v\" ";
				}
			}

			$link .= "></script> \n";
		}
		else
		{
			if ( strpos($src, '://') !== FALSE)
			{
				$link .= 'src="'.$src.'"';
			}
			else
			{
				$link .= 'src="'.$CI->config->slash_item('base_js_url').$src.'"';
			}

			$link .= "></script> \n";
		}


		return $link;
	}
}

if ( ! function_exists('stylesheet'))
{
	function stylesheet($href = '', $rel = 'stylesheet', $type = 'text/css', $title = '', $media = '', $index_page = FALSE)
	{
		$CI =& get_instance();

		$link = '<link';

		if (is_array($href))
		{
			foreach ($href as $k=>$v)
			{
				if ($k == 'href' AND strpos($v, '://') === FALSE)
				{
					$link .= ' href="'.$CI->config->slash_item('base_css_url').$v.'" ';
				}
				else
				{
					$link .= "$k=\"$v\" ";
				}
			}

			$link .= "/> \n";
		}
		else
		{
			if ( strpos($href, '://') !== FALSE)
			{
				$link .= ' href="'.$href.'" ';
			}
			else
			{
				$link .= ' href="'.$CI->config->slash_item('base_css_url').$href.'" ';
			}

			$link .= 'rel="'.$rel.'" type="'.$type.'" ';

			if ($media	!= '')
			{
				$link .= 'media="'.$media.'" ';
			}

			if ($title	!= '')
			{
				$link .= 'title="'.$title.'" ';
			}

			$link .= "/> \n";
		}


		return $link;
	}
}

if ( ! function_exists('image'))
{
	function image($src = '', $attributes = '')
	{
		if ( ! is_array($src) )
		{
			$src = array('src' => $src);
		}

		$img = '<img';

		foreach ($src as $k=>$v)
		{
			if ($k == 'src' AND strpos($v, '://') === FALSE)
			{
				$CI =& get_instance();
				$img .= ' src="'.$CI->config->slash_item('base_images_url').$v.'" ';
			}
			else
			{
				$img .= " $k=\"$v\" ";
			}
		}

		$img .= $attributes.' />';

		return $img;
	}
}

if ( ! function_exists('anchor'))
{
	function anchor($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;

		if ( ! is_array($uri))
		{
			$site_url = ( ! preg_match('!^\w+://! i', $uri)) ? site_url($uri) : $uri;
		}
		else
		{
			$site_url = site_url($uri);
		}

		if ($title == '')
		{
			$title = $site_url;
		}

		return '<a href="'.$site_url.'" '.$attributes.'>'.$title.'</a>';
	}
	
}

if ( ! function_exists('is_ajax'))
{
    function is_ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }
}

function first_word($string = ''){
    $string = explode(" ", $string);
    return $string[0];
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
    
function sanitize($string = '', $reverse = FALSE)
{
    $characters = array(
        '&',
        '<',
        '>',
        '"',
        '\'',
        '`',
        '!',
        '%',
        '(',
        ')',
        '+',
        '}',
        '{',
        '@'
    );
    
    $replacements = array(
        '&amp;',
        '&lt;',
        '&gt;',
        '&quot;',
        '&#x27;',
        '&#x60;',
        '&#x21;',
        '&#x25;',
        '&#x28;',
        '&#x29;',
        '&#x2B;',
        '&#x7D;',
        '&#x7B;',
        '&#x40;'
    );
    
    if( ! mb_check_encoding($string, 'UTF-8')) $string = utf8_encode($string);
    
    if($reverse == FALSE) return str_replace($characters, $replacements, $string);
    if($reverse == TRUE) return str_replace($replacements, $characters, $string);
    
}

function staff_color($user_id = 0)
{
    if($user_id == 4) return "color:#DA6506; background:#ffd;";
    if($user_id == 108 || $user_id == 15 || $user_id == 158) return "color:#CF359F; background:#FEE4F2;";
}

function simple_bbcode($message = "")
{
    $URLSearchString = " a-zA-Z0-9\:\/\-\?\&\.\=\_\~\#\'";
	$message = preg_replace("(\[url\=([$URLSearchString]*)\](.+?)\[/url\])", '<a href="$1" target="_blank">$2</a>', $message);
	$message = preg_replace("(\[b\](.+?)\[\/b])is",'<strong>$1</strong>',$message);
	$message = preg_replace("(\[i\](.+?)\[\/i])is",'<i>$1</i>',$message);
	$message = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a href=\"\\2\" >\\2</a>'", $message);
	$message = preg_replace("/\[align=(.+?)\](.+?)\[\/align\]/is","<p style=\"margin:0;padding:0;text-align:$1\">$2</p>",$message);
	$message = preg_replace("/\[img\](.+?)\[\/img\]/is", '<img src="$1" style="max-width:400px; max-height:400px;" alt="" />', $message);

	return $message;
}

function replace_foul_words($string = ""){
    
    $search = array(
        'Fuck', 
        'Cunt', 
        'Bitch', 
        'Ass', 
        'Porn', 
        'Sex', 
        'Drugs', 
        'Pussy', 
        'Shit', 
        'Clit', 
        'Skank', 
        'Penis', 
        'Cock', 
        'Prick', 
        'Twat', 
        'Tits', 
        'Niggers', 
        'Sperm', 
        'Jizz', 
        'Horny', 
        'Beaner', 
        'Bancock', 
        'Dildo', 
        'Dyke', 
        'Fag', 
        'Porn', 
        'Wank',
        'c4k3',
    );
    
    $replace = array(
        '****', 
        '****', 
        'B****', 
        'Donkey', 
        '****', 
        'Love', 
        '*****', 
        'Cat', 
        'Snap', 
        '****',
        'Skunk',
        'P****',
        'Ham',
        'Person',
        'Twig',
        'T***',
        '*******',
        '*****',
        '****',
        '*****',
        '*****',
        'B*****',
        '*******',
        '*****',
        '****',
        '****',
        '****',
        'The cake is a lie',
    );

    return str_ireplace($search, $replace, $string);
    
}

/* 50% chance, equal to (bool)mt_rand(0, 1) */
// var_dump(probability(50));
/* 0.1% chance, floats will not work -- "1 out of 1000 cases" */
// var_dump(probability(1, 1000));
function probability($chance, $out_of = 100) {
    $random = mt_rand(1, $out_of);
    return $random <= $chance;
}