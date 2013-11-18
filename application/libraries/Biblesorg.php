<?php

class Biblesorg
{
	function __construct($params)
	{
		$this->base_url         = $params['biblesorg_base_url'];
		$this->api_key          = $params['biblesorg_api_key'];
		$this->timeout          = isset( $params['biblesorg_timeout'] ) ? $params['biblesorg_timeout'] : 120;
		$this->default_version  = $params['biblesorg_default_version'];
	}

	public function get($uri, $args=FALSE)
	{
		$args || $args = array();
		isset($args['version']) || $args['version'] = $this->default_version;

		$args && ($args = '?' . http_build_query($args)) || $args = '';

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->base_url . $uri . '.js' . $args);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "$this->api_key:X");
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

		$data = curl_exec($ch);
		$info = curl_getinfo($ch);

		curl_close($ch);

		$object = json_decode($data);

		return $object;
	}
}
