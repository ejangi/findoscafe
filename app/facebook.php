<?php
class Facebook {
	
	public $graph_url = "https://graph.facebook.com";
	public $access_token = "";
	public $query_me = "/me?fields=about,address,bio,cover,devices,education,email,first_name,favorite_teams,gender,id,inspirational_people,interested_in,last_name,link,location,name,picture,quotes,relationship_status,religion,username,website,work";

	public function __construct() {
		$this->access_token = Config::$facebook_accesstoken;
	}

	public function me() {
		$url = $this->graph_url.$this->query_me.'&access_token='.$this->access_token;
		return $this->curl($url);
	}

	protected function curl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

}