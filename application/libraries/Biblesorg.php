<?php

class Biblesorg
{
	function __construct($params)
	{
		$this->base_url = $params['biblesorg_base_url'];
		$this->username = $params['biblesorg_api_key'];
		$this->timeout  = isset( $params['biblesorg_timeout'] ) ? $params['biblesorg_timeout'] : 120;
	}
}
