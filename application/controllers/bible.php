<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');  

class Bible extends REST_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->config('gcm');
		$this->load->library('biblesorg');
		$this->default_reference = $this->config->item('bible_default_reference');
		$this->load->driver('cache', array('adapter' => $this->config->item('cache_default_driver')));
	}

	public function index_get()
	{
		// a list of accepted URI arguments
		$args = array(
			'reference'   => $this->get('reference'),
		);

		$cache_id = md5('bible::index::' . serialize($args));
		$data = $this->cache->get($cache_id);

		if ( ! $data)
		{
			$args['reference'] || $args['reference'] = $this->default_reference;
			//$events = $this->_get_raw_listing($args['date_start'], $args['date_end'], $args['timeframe']);

			if ($events)
			{
				if ($args['featured'])
				{
					// filter only those events whose names start with an asterisk
					$events = $this->utils->array_filter_items($events, function($event){
						return strpos($event->event_name, '*') !== FALSE;
					});
				}

				if ($args['group'])
				{
					// filter only those events from this group
					$t = $this;
					$events = $this->utils->array_filter_items($events, function($event, $args) use ($t){
						$group = strtolower($t->utils->normalize_string($event->group_name));
						$query = strtolower($args['group']);

						return ($group == $query);
					}, array('group'=>urldecode($args['group'])));
				}

				if ($args['search'])
				{
					// filter search query
					$events = $this->utils->array_filter_items($events, function($event, $args){
						$fields = strtolower(implode((array)$event, ','));
						$query = strtolower($args['query']);

						return (strstr($fields, $query) !== FALSE);
					}, array('query'=>$args['search']));
				}

				$data = $this->_format_events($events, $args['count'], $args['show_details']);
				$this->cache->save($cache_id, $data);
			}
		}

		if (count($data))
		{
			if ($this->response->format == 'html')
			{
				$full_page = ! $args['trim'];

				$template_data = array(
					'title'     => 'Upcoming Events',
					'details'   => $args['show_details'],
					'items'     => $data
				);

				$full_page && $this->load->view('tpl-header', $template_data);
				$this->load->view('events_html', $template_data);
				$full_page && $this->load->view('tpl-footer', $template_data);
			}
			else
			{
				$this->response($data);
			}
		}
		else
		{
			$this->output->set_status_header('204');
			echo "Nothing to display.";
		}
	}
}
