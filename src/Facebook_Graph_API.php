<?php
/**
 * 
 */
class Facebook_Graph_API
{
	//protected $access_token;

	function __destruct()
	{
		if (isset($this->access_token)) {
			$this->destroyToken($this->access_token);
		}
	}
	public function getToken($username, $password, $type = 'android')
	{
		$token_type = array('ANDROID', 'IOS');
		if (in_array(strtoupper($type), $token_type)) {
			$type == 'android' ? $data = array('api_key' => '882a8490361da98702bf97a021ddc14d', 'email' => $username, 'format' => 'JSON', 'generate_machine_id' => '1', 'generate_session_cookies' => '1', 'locale' => 'vi_vn', 'method' => 'auth.login', 'password' => $password, 'return_ssl_resources' => '0', 'v' => '1.0') : $data = array('api_key' => '3e7c78e35a76a9299309885393b02d97', 'email' => $username, 'format' => 'JSON', 'generate_machine_id' => '1', 'generate_session_cookies' => '1', 'locale' => 'vi_vn', 'method' => 'auth.login', 'password' => $password, 'return_ssl_resources' => '0', 'v' => '1.0');
			$sig = str_replace('&', '', http_build_query($data));
			$type == 'android' ? $sig .= '62f8ce9f74b12f84c123cc23437a4a32' : $sig .= 'c1e620fa708a1d5696fb991c1bde5662';
			$data['sig'] = md5($sig);

			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, 'https://api.facebook.com/restserver.php');
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			$type == 'android' ? curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 4.4.2; SMART 3.5\'\' Touch+ Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/30.0.0.0 Mobile Safari/537.36') : curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

			$data = curl_exec($ch);
			curl_close($ch);

			$data = json_decode($data);
			if (isset($data->error_code)) {
				$data = array('status' => $data->error_code, 'msg' => $data->error_msg);
			}
			else {
				$this->access_token = $data->access_token;
				$this->session_cookies = $data->session_cookies;
				$data = array('status' => 200, 'msg' => 'Login successfull!');
			}
		}
		else $data = array('status' => 100, 'msg' => 'Type doesn\'t exist. It must be one of {ANDROID, IOS}');
		return $data;
	}
	public function destroyToken($access_token)
	{
		$data = array('method' => 'auth.expireSession', 'access_token' => $access_token, 'locale' => 'vi_vn', 'format' => 'JSON');
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, 'https://api.facebook.com/restserver.php');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/78.0.136 Chrome/72.0.3626.136 Safari/537.36');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

		$data = curl_exec($ch);
		curl_close($ch);

		$data = json_decode($data);
		if (isset($data->error_code)) {
			$data = array('status' => $data->error_code, 'msg' => $data->error_msg);
		}
		else {
			$this->access_token = null;
			$data = array('status' => 200, 'msg' => 'Destroy successfull!');
		}

		return $data;
	}
	protected function getAppId($access_token)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/app?access_token='.$access_token);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/68.4.154 Chrome/62.4.3202.154 Safari/537.36');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$data = curl_exec($ch);
		curl_close($ch);

		$data = json_decode($data);
		$app_id = $data->id;

		return $app_id;
	}
	public function getCookie($session_cookies = null, $type = 'text')
	{
		$session_cookies = $this->session_cookies;
		$linklist = 'https://api.facebook.com/method/auth.getSessionforApp?access_token='.$token.'&format=json&new_app_id='.$new_app_id.'&generate_session_cookies=1';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $linklist);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/68.4.154 Chrome/62.4.3202.154 Safari/537.36');

		$data = curl_exec($ch);
		curl_close($ch);

		$data = json_decode($data);
		if ($type == 'json') {
			$cookie = json_encode($data->session_cookies);
		}
		elseif ($type == 'text') {
			$cookie = '';
		 	foreach ($data->session_cookies as $value) {
		 		$cookie .= $value->name. '=' .$value->value. ';';
		 	}
		}
		return $cookie;
	}
	public function getUID($url)
	{
		preg_match('/(?<=profile\.php\?id\=)[0-9]*|(?:(?<=\.com\/)|(?<=\.me\/)|(?<=\.co\/)|(?<=\.us\/))(?:(?!profile\.php)(?!pages\/)(?!groups\/)[\w\.\_]*)/', $url, $url);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/'.$url[0].'?fields=id&access_token='.$this->access_token);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/68.4.154 Chrome/62.4.3202.154 Safari/537.36');
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$data = curl_exec($ch);
		curl_close($ch);

		$data = json_decode($data);
		return $data->id;
	}
	public function reaction($public_scope, $type = 'REACTION', $reaction = null)
	{
		$all_type = array('REACTION', 'UNREACTION', 'GET');
		$type = strtoupper($type);
		if (in_array($type, $all_type)) {
			if ($type == 'REACTION' || $type == 'UNREACTION') {
				$reaction = strtoupper($reaction);
				$reaction_type = array('NONE', 'HAHA', 'SAD', 'LOVE', 'ANGRY', 'LIKE', 'THANKFUL', 'WOW', 'PRIDE');
				if (in_array($reaction, $reaction_type)) {
						$ch = curl_init();

						curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v3.2/'.$public_scope.'/reactions?type='.$reaction.'&access_token='.$this->access_token);
						curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/68.4.154 Chrome/62.4.3202.154 Safari/537.36');
						curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
						$type == 'REACTION' ? curl_setopt($ch, CURLOPT_POST, true) : curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

						$data = curl_exec($ch);
						curl_close($ch);

						$data = json_decode($data);
						isset($data->error) ? $data = array('status' => $data->error->code, 'msg' => $data->error->message) : $data = array('status' => 200, 'msg' => 'Success');
				}
				else $data = array('status' => 100, 'msg' => 'Reaction type doesn\'t exist. It must be one of {NONE, LIKE, LOVE, WOW, HAHA, SAD, ANGRY, THANKFUL, PRIDE}');
			}
			elseif ($type == 'GET') {
				$url = 'https://graph.facebook.com/'.$public_scope.'/reactions?access_token='.$this->access_token;
				$reaction = array('status' => 200, 'data' => array(), 'count' => array());
				while (true) {
					$ch = curl_init();

					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/68.4.154 Chrome/62.4.3202.154 Safari/537.36');
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

					$data = curl_exec($ch);
					curl_close($ch);

					$data = json_decode($data);
					
					if (!isset($data->data) && isset($data->error)) {
						break;
						$reaction = array('status' => $data->error->code, 'msg' => $data->error->message);
					}
					else {
						$reaction['data'] = array_merge($reaction['data'], $data->data);
						if (!empty($data->paging->next)) {
							$url = $data->paging->next;
						}
						else break;
					}
				}
				$reaction['count'] = array_count_values(array_column($reaction['data'], 'type'));
				$data = $reaction;
			}
		}
		else $data = array('status' => 100, 'msg' => 'Type doesn\'t exist. It must be one of {REACTION, UNREACTION, GET}');
		return $data;
	}
}

$a = new Facebook_Graph_API();
$a->getToken('dev.tntd2k2', 'khanhlinhcrush2004');
/*$linklist = 'https://graph.facebook.com/'.$a->getUID('https://www.facebook.com/minhlaheonehihi').'/feed?fields=id&limit=5000&access_token='.$a->access_token;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $linklist);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/68.4.154 Chrome/62.4.3202.154 Safari/537.36');
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$data = curl_exec($ch);
	curl_close($ch);

	$data = json_decode($data);
	$info = array();

	foreach ($data->data as $value) {
		array_push($info, $value->id);
	}
	echo json_encode($info);
	for ($i=0; $i < count($info); $i++) { 
		$a->reaction($info[$i], 'REACTION', 'LOVE');
	}*/
