<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ccb
{
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->config->load('ccb');
		$this->CI->load->library('utils');
	}

	public function get($srv, $args=FALSE)
	{
		$args || $args = array();
		$args['srv'] = $srv;

		$response = $this->CI->utils->cURL(
			$this->CI->config->item('ccb_base_url'),
			$args,
			$this->CI->config->item('ccb_username'),
			$this->CI->config->item('ccb_password'),
			$this->CI->config->item('ccb_timeout')
		);

		$object = $this->xml_to_object($response->data);

		return $object;
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
