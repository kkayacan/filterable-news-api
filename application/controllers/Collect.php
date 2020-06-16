<?php
defined('BASEPATH') or exit('No direct script access allowed');
use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

class Collect extends RestController
{

    public function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('google_model');
        $this->load->model('newsapi_model');
        $this->load->helper('news');
    }

    public function news_get()
    {
        //tideways_xhprof_enable();
        $date_time = new DateTime();
        $execution_start = $date_time->format('Y-m-d H:i:s');
        //$fileprefix = $date_time->format('YmdHis');
        if ($this->config->item('check_client_ip_in_collector') && $_SERVER['SERVER_ADDR'] != get_client_ip_server()) {
            $this->response('Unauthorized', 401);
        }
        $category = $this->news_model->get_next_category();
        log_message('debug', $category->gCat . ' ' . $execution_start . ' start');

        $semaphore = sem_get(ftok(__FILE__, '0'), 1);
        if (!sem_acquire($semaphore, 1)) {
            log_message('debug', $category->gCat . ' ' . $execution_start . ' another process running');
            die();
        }
        try
        {
            $this->benchmark->mark('google_start');
            $google_news = $this->google_model->fetch($category->gCat);
            $this->benchmark->mark('google_end');
            log_message('debug', $category->gCat . ' ' . $execution_start . ' Google fetch elapsed ' . $this->benchmark->elapsed_time('google_start', 'google_end'));
            $this->benchmark->mark('insert_start');
            $inserted_news = $this->news_model->insert_news($category->id, $google_news);
            $this->benchmark->mark('insert_end');
            log_message('debug', $category->gCat . ' ' . $execution_start . ' news insert elapsed ' . $this->benchmark->elapsed_time('insert_start', 'insert_end'));
            $this->news_model->set_category_updated($category->id);
            $this->benchmark->mark('newsapi_start');
            $newsapi = $this->newsapi_model->fetch($category->nCat);
            $this->benchmark->mark('newsapi_end');
            log_message('debug', $category->gCat . ' ' . $execution_start . ' newsapi fetch elapsed ' . $this->benchmark->elapsed_time('newsapi_start', 'newsapi_end'));
            $this->benchmark->mark('update_start');
            $this->news_model->update_news($newsapi);
            $this->benchmark->mark('update_end');
            log_message('debug', $category->gCat . ' ' . $execution_start . ' news update elapsed ' . $this->benchmark->elapsed_time('update_start', 'update_end'));
            //$this->response($inserted_news, 200);
        } catch (\Exception $e) {
            log_message('debug', $category->gCat . ' ' . $execution_start . ' Catched: ' . $e->getMessage());
        }
        sem_release($semaphore);
        //$profile_data = tideways_xhprof_disable();
        /*
        $this->load->helper('file');
        if (!write_file('../application/logs/' . $fileprefix . '.collector.xhprof', json_encode($profile_data))) {
            log_message('debug', $category->gCat . ' ' . $execution_start . ' Profile data could not be written');
        } else {
            log_message('debug', $category->gCat . ' ' . $execution_start . ' Profile data written');
        }
        */
        $this->response($inserted_news, 200);
    }

}
