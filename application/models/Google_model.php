<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Google_model extends CI_Model
{

    private $urls = [];

    public function __construct()
    {
        parent::__construct();
        $this->config->load('news');
        $this->load->helper('simple_html_dom');
    }

    public function fetch($category)
    {
        $rss = $this->_fetch_remote_data($category);
        $formatted_rss = $this->_reformat($rss);
        return $formatted_rss;
    }

    public function _fetch_remote_data($category)
    {
        $link = $this->config->item('google_url_base') . $category . $this->config->item('google_url_param');
        $xml_string = file_get_contents($link);
        $xml = new SimpleXMLElement($xml_string);
        return $xml->channel->item;
    }

    public function _reformat($rss)
    {
        $result = [];
        foreach ($rss as $item) {
            $formatted_item = array(
                'guid' => $item->guid,
                'pubDate' => $this->_format_time($item->pubDate),
                'links' => $this->_build_link_list($item->description),
            );
            if ($formatted_item['links']) {
                array_push($result, $formatted_item);
            }
        }
        return $result;
    }

    public function _format_time($time)
    {
        $date_time_object = DateTime::createFromFormat(DateTimeInterface::RSS, (string) $time);
        return $date_time_object->format('Y-m-d H:i:s'). ' GMT';
    }

    public function _build_link_list($html)
    {
        $dom = str_get_html($html);
        $links = $dom->find('li');
        if ($links) {
            return $this->_parse_link_list($links);
        } else {
            return $this->_parse_single_link($dom);
        }
    }

    public function _parse_link_list($html_links)
    {
        $links = [];
        foreach ($html_links as $html_link) {
            if (!$html_link->find('strong')) {
                if (!in_array($html_link->find('a', 0)->href, $this->urls)) {
                    $link = array(
                        'source' => $html_link->find('font', 0)->innertext,
                        'url' => $html_link->find('a', 0)->href,
                        'title' => $html_link->find('a', 0)->innertext);
                    array_push($links, $link);
                    array_push($this->urls, $html_link->find('a', 0)->href);
                }
            }
        }
        return $links;
    }

    public function _parse_single_link($html_link)
    {
        $links = [];
        if (!in_array($html_link->find('a', 0)->href, $this->urls)) {
            $link = array(
                'source' => $html_link->find('font', 0)->innertext,
                'url' => $html_link->find('a', 0)->href,
                'title' => $html_link->find('a', 0)->innertext);
            array_push($links, $link);
            array_push($this->urls, $html_link->find('a', 0)->href);
            return $links;
        } else {
            return false;
        }
    }

}
