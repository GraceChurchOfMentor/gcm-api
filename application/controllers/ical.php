<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(E_ALL);

require(APPPATH.'libraries/REST_Controller.php');  

class Ical extends REST_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->config('gcm');
        $this->load->library('ccb');
        $this->load->driver('cache', array('adapter' => $this->config->item('cache_default_driver')));
    }

    public function index_get()
    {
        // a list of accepted URI arguments
        $args = array(
            'url' => $this->get('url'),
        );

        if ( ! $args['url'] ) {
            $this->output->set_status_header('204');
            echo "Nothing to display.";
        }

        $cache_id = md5('ical::index::' . serialize($args));
        $data = $this->cache->get($cache_id);

        if ( ! $data)
        {
            $data = $this->utils->cURL($args['url']);

            $this->cache->save($cache_id, $data, 3600);
        }

        //$this->response->format = 'html';
        $this->response($data);
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
}
