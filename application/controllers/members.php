<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');  

class Members extends REST_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->driver('cache', array('adapter' => 'dummy'));
		$this->load->library('ccb');
		$this->load->config('gcm');
	}

	public function index_get()
	{
		redirect(site_url());
	}

	public function youth_get()
	{
		// a list of accepted URI arguments
		$args = array(
			'trim'         => $this->get('trim')
		);

		$cache_id = md5(serialize(array(
			'controller' => 'members',
			'method'     => 'index',
			'args'       => $args
		)));
		$data = $this->cache->get($cache_id);

		if ( ! $data)
		{
			// look up stuff
			$data = $this->ccb->get('group_participants', array(
				'id' => '16'
			));

			$data = $data->response->groups->group->participants;
			$data = json_decode(json_encode($data));

			$this->cache->save($cache_id, $data);
		}

		die();

		if ($this->response->format == 'html')
		{
			$template_data = array(
				'title'     => 'Members',
				'items'     => $data
			);

			if ( ! $args['trim']) $this->load->view('tpl-header.php', $template_data);
			$this->load->view('members_html', $template_data);
			if ( ! $args['trim']) $this->load->view('tpl-footer.php', $template_data);
		}
		else
		{
			$this->response($data);
		}
	}

}
