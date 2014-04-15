<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');  

class Events extends REST_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->config('gcm');
		$this->load->library('ccb');
		$this->default_count = $this->config->item('events_default_count');
		$this->load->driver('cache', array('adapter' => $this->config->item('cache_default_driver')));
	}

	public function index_get()
	{
		// a list of accepted URI arguments
		$args = array(
			'date_start'   => $this->get('date_start'),
			'date_end'     => $this->get('date_end'),
			'timeframe'    => $this->get('timeframe'),
			'featured'     => $this->get('featured'),
			'count'        => $this->get('count'),
			'group'        => $this->get('group'),
			'search'       => $this->get('search'),
			'show_details' => $this->get('show_details'),
			'trim'         => $this->get('trim')
		);

		$cache_id = md5('events::index::' . serialize($args));
		$data = $this->cache->get($cache_id);

		if ( ! $data)
		{
			$args['count'] || $args['count'] = $this->default_count;
			$events = $this->_get_raw_listing($args['date_start'], $args['date_end'], $args['timeframe']);

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
					// filter only those events from the specified group(s)
					$t = $this;
					$events = $this->utils->array_filter_items($events, function($event, $args) use ($t){
						$query = strtolower($args['group']);
                        $groups = explode('|', $query);
						$group = strtolower($t->utils->normalize_string($event->group_name));

						return in_array($group, $groups);
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

	public function cache_get()
	{
		$clean = $this->get('clean');

		if ($clean)
		{
			if ($this->cache->clean()) $response = 'Cache cleaned successfully';
			else $response = 'Cache cleaning failed';

			$this->response($response);
		}
	}

	private function _get_raw_listing($date_start=FALSE, $date_end=FALSE, $timeframe=FALSE)
	{
		$timeframe = $timeframe ? urldecode($timeframe) : $this->config->item('events_default_timeframe');
		$date_start = $this->utils->normalize_time($date_start);
		$date_end = $date_end ? $this->utils->normalize_time($date_end) : $this->utils->normalize_time($timeframe);

		// get the full Public Calendar listing for the next month
		$data = $this->ccb->get('public_calendar_listing', array(
			'date_start' => $date_start,
			'date_end' => $date_end
		));

		$events = array();

		foreach ($data->response->items->item as $item)
		{
			$events[] = $item;
		}
		
		if (count($events))
		{
			return $events;
		}

		return FALSE;
	}

	private function _format_events($events, $count, $show_details)
	{
		// remove any leading asterisks
		$events = array_map(function($event){
			if (substr($event->event_name, 0, 1) == '*')
			{
				$event->event_name = substr($event->event_name, 1);
			}
			return $event;
		}, $events);

		// convert to array
		$events = @json_decode(@json_encode($events), 1);

		// group by date
		$events = $this->utils->array_group_by_index($events, 'date');

		// truncate array
		$events = array_slice($events, 0, $count);

		// remove key names from array
		$events = array_values($events);

		// add pretty-print strings
		$events = array_map(function($date){
			$date = array_map(function($event){
				$new_event = array(
					'date'              => $event['date'],
					'event_name'        => $event['event_name'],
					'start_time_string' => date('g:ia', strtotime($event['start_time'])),
					'end_time_string'   => date('g:ia', strtotime($event['end_time'])),
					'event_description' => is_array($event['event_description']) ? '' : $event['event_description'],
					'location'          => is_array($event['location']) ? '' : $event['location'],
					'leader_name'       => $event['leader_name'],
					'leader_email'      => $event['leader_email'],
					'leader_phone'      => $event['leader_phone'],
				);

				if (($event['start_time'] == '00:00:00') && ($event['end_time'] == '23:59:00'))
				{
					$new_event['time_string'] = 'All Day';
					$new_event['short_time_string'] = '';
				}
				else
				{
					$new_event['time_string'] = $new_event['start_time_string'] . '&#8209;' . $new_event['end_time_string'];
					$new_event['short_time_string'] = $new_event['start_time_string'];
				}

				return $new_event;
			}, $date);

			$date = array(
				'date' => $date[0]['date'],
				'date_string' => date('l, M. jS', strtotime($date[0]['date'])),
				'events' => $date
			);

			return $date;
		}, $events);

		return $events;
	}
}
