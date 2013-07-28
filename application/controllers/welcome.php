<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$template_data = array(
			'title' => 'Grace Church of Mentor Public API',
		);
		$this->load->view('tpl-header', $template_data);
		$this->load->view('welcome', $template_data);
		$this->load->view('tpl-footer', $template_data);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
