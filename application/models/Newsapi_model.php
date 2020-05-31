<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Newsapi_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->config->load('news');
        //$this->load->helper('simple_html_dom');
    }

    public function fetch($category)
    {
        return $this->_fetch_remote_data($category);
    }

    function _fetch_remote_data($category)
    {
        if ($this->config->item('newsapikey')) {
            $link = $this->config->item('newsapi_url_base') . $category . $this->config->item('newsapi_url_param') . $this->config->item('newsapikey');
            $context = stream_context_create([
                "http" => [
                    "ignore_errors" => true,
                ],
            ]);
            $result = json_decode(file_get_contents($link, false, $context));
            if ($result) {
                if (property_exists($result, 'articles')) {
                    return $result->articles;
                }
            }
        }
        return false;
    }

}
