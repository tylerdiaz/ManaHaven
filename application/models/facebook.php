<?

class Facebook extends CI_Model
{
	function get_facebook_cookie()
	{
  		$app_id = 'omitted';
  		$application_secret = 'omitted';

		if(isset($_COOKIE['fbs_'.$app_id])){
  			$args = array();
  			parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
  			ksort($args);
  			$payload = '';
  			foreach ($args as $key => $value)
  			{
    				if ($key != 'sig') {
      				$payload .= $key . '=' . $value;
    				}
  			}
  			if (md5($payload.$application_secret) != $args['sig'])
  			{
    				return null;
  			}
  			return $args;
  		}
  		else
  		{
			return null;
  		}
	}

    function get_file_contents($url = "http://example.com/"){
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents):
            return $contents;
        else:
            return FALSE;
        endif;
    }

	function getUser(){
		$cookie = $this->get_facebook_cookie();
		$user = @json_decode($this->get_file_contents('https://graph.facebook.com/me?access_token='.$cookie['access_token']), true);
		return $user;
	}

	function getFriendIds($include_self = TRUE){
		$cookie = $this->get_facebook_cookie();
		$friends = @json_decode($this->get_file_contents(
    				'https://graph.facebook.com/me/friends?access_token=' .
    				$cookie['access_token']), true);
		$friend_ids = array();
		foreach($friends['data'] as $friend){
			$friend_ids[] = $friend['id'];
		}
		if($include_self == TRUE){
			$friend_ids[] = $cookie['uid'];
		}

		return $friend_ids;
	}

	function getFriends($include_self = TRUE){
		$cookie = $this->get_facebook_cookie();
		$friends = @json_decode($this->get_file_contents(
    				'https://graph.facebook.com/me/friends?access_token=' .
    				$cookie['access_token']), true);

		if($include_self == TRUE){
			$friends['data'][] = array(
				'name'   => 'You',
				'id' => $cookie['uid']
			);
		}

		return $friends['data'];
	}

	function getFriendArray($include_self = TRUE){
		$cookie = $this->get_facebook_cookie();
		$friendlist = @json_decode($this->get_file_contents(
    				'https://graph.facebook.com/me/friends?access_token=' .
    				$cookie['access_token']), true);
		$friends = array();
		foreach($friendlist['data'] as $friend)
		{
			$friends[$friend['id']] = $friend['name'];
		}
		if($include_self == TRUE){
			$friends[$cookie['uid']] = 'You';
		}
		return $friends;
	}
}

?>