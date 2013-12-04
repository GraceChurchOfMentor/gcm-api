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
			'version'     => $this->get('version'),
			'trim'        => $this->get('trim')
		);

		$cache_id = md5('bible::index::' . serialize($args));
		$data = $this->cache->get($cache_id);

		if ( ! $data)
		{
			$args['reference'] || $args['reference'] = $this->default_reference;
			$args['q[]'] = $args['reference'];
			unset($args['reference']);

			if ( ! $args['version']) { unset($args['version']); }

			$data = $this->biblesorg->get('passages', $args);

			if ($data)
			{
				$data = $data->response->search->result->passages[0];

				$this->cache->save($cache_id, $data);
			}
		}

		if ($data)
		{
			if ($this->response->format == 'html')
			{
				$full_page = ! $args['trim'];

				$template_data = array(
					'reference' => $this->get('reference'),
					'title'     => "$data->display ($data->version_abbreviation)",
					'text'      => $data->text,
					'copyright' => $data->copyright
				);

				$full_page && $this->load->view('tpl-header', $template_data);
				$this->load->view('bible_html', $template_data);
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
