<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->driver('cache', array('adapter' => 'file'));
	}

	function index()
	{
		$cache_id = md5('welcome::index');
		$data = $this->cache->get($cache_id);

		if ( ! $data)
		{
			require_once('application/libraries/php-markdown/Michelf/Markdown.php');

			$readme = \Michelf\Markdown::defaultTransform(file_get_contents('README.md'));
			$readme = preg_replace('@<code>/([^<]*)</code>@', '<code><a href="$1">/$1</a></code>', $readme);

			$data = array(
				'title' => FALSE,
				'readme' => $readme
			);

			$this->cache->save($cache_id, $data);
		}

		$this->load->view('tpl-header', $data);
		$this->load->view('welcome', $data);
		$this->load->view('tpl-footer', $data);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
