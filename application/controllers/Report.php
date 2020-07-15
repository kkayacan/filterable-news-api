<?php
defined('BASEPATH') or exit('No direct script access allowed');
use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

class Report extends RestController
{

    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
    }

    public function filters_get()
    {
        $filters = $this->news_model->retrieve_filters();
        $this->response($filters, 200);
    }

    public function news_get()
    {
        $news = $this->news_model->retrieve_news($this->uri->uri_to_assoc(3));
        $this->response($news, 200);
    }

    public function meta_get()
    {
        $meta = $this->news_model->retrieve_meta($this->uri->uri_to_assoc(3));
        $this->response($meta, 200);
    }

}
