<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');  

class Twitter extends REST_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->driver('cache', array('adapter' => 'file'));
		$this->load->config('gcm');
		$this->default_count = $this->config->item('twitter_default_count');
	}

	public function index_get()
	{
		// a list of accepted URI arguments
		$args = array(
			'count'        => $this->get('count'),
			'search'       => $this->get('search'),
			'show_details' => $this->get('show_details'),
			'trim'         => $this->get('trim')
		);

		$cache_id = md5('twitter::index::' . $this->utils->array_implode_associative($args));
		$data = $this->cache->get($cache_id);

		if ( ! $data)
		{
			$data = $this->utils->cURL('api.twitter.com', array(
				'user_id' => 'stevesindelar',
			));

			//$data = $this->_format_events($events, $args['count'], $args['show_details']);
			$this->cache->save($cache_id, $data);
		}

		if ($this->response->format == 'html')
		{
			$template_data = array(
				'title'     => 'Recent Tweets',
				'full_page' => ! $args['trim'],
				'details'   => $args['show_details'],
				'items'     => $data
			);

			var_dump($data);
			//$this->load->view('tweets_html', $template_data);
		}
		else
		{
			$this->response($data);
		}
	}

}
