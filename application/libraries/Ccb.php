<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ccb
{
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->config->load('ccb');
	}

	public function get($srv, $args=FALSE)
	{
		$args || $args = array();
		$args['srv'] = $srv;

		$response = $this->cURL(
			$this->CI->config->item('base_url'),
			$args,
			$this->CI->config->item('username'),
			$this->CI->config->item('password'),
			$this->CI->config->item('timeout')
		);

		$object = $this->xml_to_object($response->data);

		return $object;
	}

	public function cURL($url, $args=FALSE, $username=FALSE, $password=FALSE, $timeout=FALSE)
	{
		$args && ($args = '?' . http_build_query($args)) || $args = '';

		$this->CI->load->driver('cache', array('adapter' => 'file'));
		$cache_id = "cURL::$url$args";
		$response = $this->CI->cache->get($cache_id);

		if ( ! $response)
		{
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url . $args);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

			if (($username) && ($password))
			{
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
			}

			if ($timeout)
			{
				curl_setopt($ch, CURLOPT_CONNECTTIEMOUT, $timeout);
			}

			$data = curl_exec($ch);
			$info = curl_getinfo($ch);

			curl_close($ch);

			$response = (object) array(
				'data' => $data,
				'info' => $info
			);
			$this->CI->cache->save($cache_id, $response);
		}

		return $response;
	}

	public function xml_to_object($xml)
	{
		try
		{
			$xml_object = new SimpleXMLElement($xml);

			if ($xml_object == FALSE)
			{
				return FALSE;
			}
		}
		catch (Exception $e)
		{
			return FALSE;
		}

		return $xml_object;
	}
}
