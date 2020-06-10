<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class News_model extends CI_Model
{

    private $redundant_texts = [];

    public function __construct()
    {
        parent::__construct();
        $this->config->load('news');
        $this->load->helper('news');
    }

    public function retrieve_categories()
    {
        $this->db->select('id, gCat');
        $this->db->from('categories');
        return $this->db->get()->result();
    }

    public function retrieve_filters()
    {
        $result = new stdClass();
        $this->db->select('id, gCat');
        $this->db->from('categories');
        $result->categories = $this->db->get()->result();
        return $result;
    }

    public function retrieve_news($param)
    {
        $param = $this->_init_param($param);

        $result = new stdClass();
        $result->appliedFilters = new stdClass();
        
        if ($param['id'] == 0) {
            $result->appliedFilters->h = $param['h'];
            $this->db->select('stories.id');
            $this->db->from('stories');
            $this->db->join('story_categories', 'story_categories.storyId = stories.id');
            $this->db->where('stories.pubDate >=', $param['start_time']);
            $this->db->where('stories.precedingPubDate <', $param['start_time']);
            if ($param['categories']) {
                $this->db->where_in('story_categories.categoryId', $param['categories']);
                $result->appliedFilters->c = $param['c'];
                $result->appliedFilters->categories = $param['categories'];
            }
            $where_clause = $this->db->get_compiled_select();
        }

        $this->db->select('stories.id, stories.mainStoryId, CONCAT(stories.pubDate, " GMT") as pubDate, t.title, e.excerpt, IF(i.imageBaseUrlId>0, CONCAT("https://", u.url, "/", i.urlToImage), null) as image, stories.articleCount');
        $this->db->from('stories');
        $this->db->join('articles as t', 't.id = stories.titleArticleId');
        $this->db->join('articles as e', 'e.id = stories.excerptArticleId', 'left outer');
        $this->db->join('articles as i', 'i.id = stories.imageArticleId', 'left outer');
        $this->db->join('base_urls as u', 'u.id = i.imageBaseUrlId', 'left outer');
        if ($param['id'] > 0) {
            $this->db->where('stories.id', $param['id']);
            $result->appliedFilters->i = $param['id'];
        } else {
            $this->db->where("`stories`.`id` IN ($where_clause)", null, false);
        }
        $this->db->order_by('stories.articleCount', 'DESC');
        $this->db->order_by('stories.totalArticleCount', 'DESC');
        $this->db->order_by('stories.imageArticleId', 'DESC');
        $this->db->order_by('stories.id', 'DESC');
        $this->db->limit($param['l'], $param['o']);
        $stories = $this->db->get()->result();

        foreach ($stories as $key => $story) {
            $this->db->select('categories.id, categories.gCat');
            $this->db->from('story_categories');
            $this->db->join('categories', 'categories.id = story_categories.categoryId');
            $this->db->where('story_categories.storyId', $story->id);
            $this->db->order_by('categories.id', 'ASC');
            $stories[$key]->categories = $this->db->get()->result();
            $this->db->select('articles.id, a.name as source, articles.title, CONCAT(IF(articles.https, "https://", "http://"), u.url, "/", articles.effectiveUrl) as articleUrl');
            $this->db->from('articles');
            $this->db->join('aliases as a', 'a.id = articles.aliasId');
            $this->db->join('base_urls as u', 'u.id = articles.articleBaseUrlId', 'left outer');
            $this->db->where('storyId', $story->id);
            $stories[$key]->articles = $this->db->get()->result();
            $stories[$key]->precedingStories = [];
            if ($story->mainStoryId > 0) {
                $this->db->select('stories.id, CONCAT(stories.pubDate, " GMT") as pubDate, t.title, e.excerpt');
                $this->db->from('stories');
                $this->db->join('related_stories as r', 'r.storyId = stories.id');
                $this->db->join('articles as t', 't.id = stories.titleArticleId');
                $this->db->join('articles as e', 'e.id = stories.excerptArticleId', 'left outer');
                $this->db->where('r.mainStoryId', $story->mainStoryId);
                $this->db->where('r.storyId <', $story->id);
                $stories[$key]->precedingStories = $this->db->get()->result();
            }
            $stories[$key]->succeedingStories = [];
            if ($story->mainStoryId > 0) {
                $this->db->select('stories.id, CONCAT(stories.pubDate, " GMT") as pubDate, t.title, e.excerpt');
                $this->db->from('stories');
                $this->db->join('related_stories as r', 'r.storyId = stories.id');
                $this->db->join('articles as t', 't.id = stories.titleArticleId');
                $this->db->join('articles as e', 'e.id = stories.excerptArticleId', 'left outer');
                $this->db->where('r.mainStoryId', $story->mainStoryId);
                $this->db->where('r.storyId >', $story->id);
                $stories[$key]->succeedingStories = $this->db->get()->result();
            }
        }
        $result->stories = $stories;
        return $result;
    }

    public function retrieve_meta($param)
    {
        if ($param['i'] > 0) {
            $this->db->select('t.title, IF(i.imageBaseUrlId>0, CONCAT("https://", u.url, "/", i.urlToImage), null) as image');
            $this->db->from('stories');
            $this->db->join('articles as t', 't.id = stories.titleArticleId');
            $this->db->join('articles as i', 'i.id = stories.imageArticleId', 'left outer');
            $this->db->join('base_urls as u', 'u.id = i.imageBaseUrlId', 'left outer');
            $this->db->where('stories.id', $param['i']);
            $result = $this->db->get()->row();
            if($result->image == null) {
                $this->load->helper('url');
                $result->image = base_url() . "preview.png";
            }
            return $result;
        }
    }

    public function get_next_category()
    {
        $this->db->select('id, gCat, nCat');
        $this->db->order_by('lastUpdate ASC, sequence ASC, id ASC');
        return $this->db->get('categories')->row();
    }

    public function set_category_updated($categoryId)
    {
        $update_data = array(
            'lastUpdate' => date('Y-m-d H:i:s z'),
        );
        $this->db->where('id', $categoryId);
        $this->db->update('categories', $update_data);
    }

    public function insert_news($categoryId, $rss)
    {
        foreach ($rss as $key => $feed) {
            $rss[$key]['links'] = $this->_find_story_with_google_links($feed['links']);
            $rss[$key] = $this->_save_feed($rss[$key], $categoryId);
            $this->_prepare_report_fields($rss[$key]['stories']);
        }
        return $rss;
    }

    public function update_news($items)
    {
        foreach ($items as $item) {
            $article = $this->_find_article_with_effective_url($item->url);
            if ($article) {
                $story_details = $this->_update_article($article->id, $item->description, $item->urlToImage);
                $this->_update_story_details($article->storyId, $story_details);
                if (array_key_exists('imageArticleId', $story_details)) {
                    $this->_update_related_story_images($article->storyId, $article->id);
                }
            } else {
                log_message('debug', 'URL not found: ' . $item->url);
            }
        }
    }

    public function _init_param($param)
    {
        if (!array_key_exists('i', $param)) {
            $param['id'] = 0;
        } else {
            $param['id'] = (int) $param['i'];
        }
        if (!array_key_exists('h', $param)) {
            $param['h'] = '3';
        }
        if (!array_key_exists('o', $param)) {
            $param['o'] = '0';
        }
        if (!array_key_exists('l', $param)) {
            $param['l'] = '20';
        }
        if (!array_key_exists('c', $param)) {
            $param['categories'] = [];
        } else {
            $param["categories"] = explode("-", $param["c"]);
        }
        $date_time = new DateTime();
        //log_message('debug', 'current time: ' . $date_time->format('Y-m-d H:i:s z'));
        //log_message('debug', 'hours: ' . $param['h']);
        $date_time->modify('-' . $param['h'] . ' hours');
        //log_message('debug', 'calculated time: ' . $date_time->format('Y-m-d H:i:s z'));
        $param['start_time'] = $date_time->format('Y-m-d H:i:s');
        return $param;
    }

    public function _find_story_with_google_links($links)
    {
        foreach ($links as $key => $link) {
            $google_artice_id = $this->_get_google_article_id($link['url']);
            $storyId = $this->_find_story_with_google_article_id($google_artice_id);
            if (!$storyId) {
                $links[$key]['parsed_url'] = $this->_get_parsed_url($link['url']);
                if ($links[$key]['parsed_url']) {
                    $storyId = $this->_find_story_with_parsed_url($links[$key]['parsed_url']);
                }
            }
            $links[$key]['storyId'] = $storyId;
        }
        return $links;
    }

    public function _get_google_article_id($url)
    {
        $result = str_replace($this->config->item('google_article_url_base'), '', $url);
        $result = str_replace($this->config->item('google_article_url_param'), '', $result);
        return $result;
    }

    public function _find_story_with_google_article_id($gCode)
    {
        $this->db->select('storyId');
        $this->db->where('gCode', $gCode);
        $result = $this->db->get('articles');
        if ($result->num_rows() > 0) {
            return intval($result->row()->storyId);
        } else {
            return null;
        }
    }

    public function _find_story_with_parsed_url($parsed_url)
    {
        $this->db->select('storyId');
        $this->db->where('https', $parsed_url["https"]);
        $this->db->where('articleBaseUrlId', $parsed_url["base_url_id"]);
        $this->db->where('effectiveUrl', $parsed_url["remain"]);
        $result = $this->db->get('articles');
        if ($result->num_rows() > 0) {
            return intval($result->row()->storyId);
        } else {
            return null;
        }
    }

    public function _save_feed($feed, $categoryId)
    {
        $feed['stories'] = [];
        $storyId = 0;
        usort($feed['links'], function ($a, $b) {
            return $b['storyId'] - $a['storyId'];
        });
        foreach ($feed['links'] as $key => $link) {
            //log_message('debug', 'Find story for ' . $link['url']);
            $source = $this->_get_source($link['source']);
            $this->_update_source_category($source['sourceId'], $categoryId);
            if ($link['storyId']) {
                //log_message('debug', 'Already has story ' . $link['storyId']);
                $this->_update_story_seen_time($link['storyId']);
                $this->_update_story_category($link['storyId'], $categoryId);
                if (!$storyId) {
                    $storyId = $link['storyId'];
                    $pubDate = $this->_get_story_pubdate($storyId);
                    //log_message('debug', 'pubDate: ' . $pubDate);
                }
            } else if ($storyId && strtotime($feed['pubDate']) <= strtotime($pubDate)) {
                //log_message('debug', 'Add to story ' . $storyId);
                //log_message('debug', 'pubDate: ' . $feed['pubDate']);
                $parsed_url = $feed['links'][$key]['parsed_url'];
                if ($parsed_url) {
                    $this->_insert_article($storyId, $link, $source, $parsed_url, $feed['guid']);
                    $feed['links'][$key]['storyId'] = $storyId;
                }
            } else {
                //log_message('debug', 'No story found. Creating');
                $parsed_url = $feed['links'][$key]['parsed_url'];
                if ($parsed_url) {
                    $pubDate = $feed['pubDate'];
                    $storyId = $this->_create_story($pubDate, $link, $categoryId, $source, $parsed_url, $feed['guid']);
                    $feed['links'][$key]['storyId'] = $storyId;
                    //log_message('debug', 'Created story ' . $storyId);
                    //log_message('debug', 'pubDate: ' . $pubDate);
                }
            }
            if ($feed['links'][$key]['storyId'] && !in_array($feed['links'][$key]['storyId'], $feed['stories'])) {
                array_push($feed['stories'], $feed['links'][$key]['storyId']);
            }
        }
        return $feed;
    }

    public function _get_story_pubdate($storyId)
    {
        $this->db->select('CONCAT(stories.pubDate, " GMT") as pubDate');
        $this->db->where('id', $storyId);
        return $this->db->get('stories')->row()->pubDate;
    }

    public function _update_story_seen_time($storyId)
    {
        $update_data = array(
            'lastSeen' => date('Y-m-d H:i:s z'),
        );
        $this->db->where('id', $storyId);
        $this->db->update('stories', $update_data);
    }

    public function _create_story($pubDate, $link, $categoryId, $source, $parsed_url, $guid)
    {
        $storyId = $this->_insert_story($pubDate);
        $this->_insert_story_category($storyId, $categoryId);
        $this->_insert_article($storyId, $link, $source, $parsed_url, $guid);
        return $storyId;
    }

    public function _insert_story($pubDate)
    {
        $insert_data = array(
            'pubDate' => $pubDate,
            'firstSeen' => date('Y-m-d H:i:s z'),
            'lastSeen' => date('Y-m-d H:i:s z'),
        );
        $this->db->insert('stories', $insert_data);
        return $this->db->insert_id();
    }

    public function _insert_story_category($storyId, $categoryId)
    {
        $insert_data = array(
            'storyId' => $storyId,
            'categoryId' => $categoryId,
        );
        $this->db->insert('story_categories', $insert_data);
        return $this->db->insert_id();
    }

    public function _get_parsed_url($url)
    {
        $effective_url = get_effective_url($url);
        if (substr($effective_url, 0, 4) == 'http') {
            return $this->_parse_url($effective_url);
        } else {
            return false;
        }
    }

    public function _insert_article($storyId, $link, $source, $parsed_url, $guid)
    {
        $insert_data = array(
            'storyId' => $storyId,
            'gCode' => $this->_get_google_article_id($link['url']),
            'https' => $parsed_url['https'],
            'articleBaseUrlId' => $parsed_url['base_url_id'],
            'effectiveUrl' => $parsed_url['remain'],
            'title' => $this->_truncate_title($link['title']),
            'sourceId' => $source['sourceId'],
            'aliasId' => $source['aliasId'],
            'guid' => $guid,
        );
        //log_message('debug', 'storyId ' . $storyId . ' gCode ' . $insert_data['gCode']);
        $this->db->insert('articles', $insert_data);
        return $this->db->insert_id();
    }

    public function _parse_url($url)
    {
        $parsed_url = [];
        if (substr($url, 0, 5) == 'https') {
            $parsed_url['https'] = true;
            $base_url = str_replace('https://', '', $url);
        } else {
            $parsed_url['https'] = false;
            $base_url = str_replace('http://', '', $url);
        }
        $offset = strpos($base_url, '/');
        $parsed_url['remain'] = substr($base_url, $offset + 1);
        $base_url = str_replace('/' . $parsed_url['remain'], '', $base_url);
        $parsed_url['base_url_id'] = $this->_get_base_url_id($base_url);
        return $parsed_url;
    }

    public function _get_base_url_id($url)
    {
        $this->db->select('id');
        $this->db->where('url', $url);
        $result = $this->db->get('base_urls');
        if ($result->num_rows() > 0) {
            return $result->row()->id;
        } else {
            $insert_data = array(
                'url' => $url,
            );
            $this->db->insert('base_urls', $insert_data);
            return $this->db->insert_id();
        }
    }

    public function _truncate_title($title)
    {
        if (empty($this->redundant_texts)) {
            $this->db->select('text');
            $this->db->order_by('CHAR_LENGTH(text) DESC');
            $this->redundant_texts = $this->db->get('redundant')->result();
        }
        $truncated_title = $title;
        foreach ($this->redundant_texts as $redundant) {
            $truncated_title = str_replace($redundant->text, '', $truncated_title);
        }
        return trim($truncated_title);
    }

    public function _get_source($name)
    {
        $source = [];
        $this->db->select('id, sourceId');
        $this->db->where('name', $name);
        $result = $this->db->get('aliases');
        if ($result->num_rows() > 0) {
            $source['sourceId'] = $result->row()->sourceId;
            $source['aliasId'] = $result->row()->id;
        } else {
            $source_data = array(
                'groupId' => 0,
            );
            $this->db->insert('sources', $source_data);
            $source['sourceId'] = $this->db->insert_id();
            $alias_data = array(
                'sourceId' => $source['sourceId'],
                'name' => $name,
            );
            $this->db->insert('aliases', $alias_data);
            $source['aliasId'] = $this->db->insert_id();
        }
        return $source;
    }

    public function _update_story_category($storyId, $categoryId)
    {
        $this->db->select('id');
        $this->db->where('storyId', $storyId);
        $this->db->where('categoryId', $categoryId);
        $result = $this->db->get('story_categories');
        if ($result->num_rows() == 0) {
            $this->_insert_story_category($storyId, $categoryId);
        }
    }

    public function _update_source_category($sourceId, $categoryId)
    {
        $this->db->select('id');
        $this->db->where('sourceId', $sourceId);
        $this->db->where('categoryId', $categoryId);
        $result = $this->db->get('source_categories');
        if ($result->num_rows() == 0) {
            $insert_data = array(
                'sourceId' => $sourceId,
                'categoryId' => $categoryId,
            );
            $this->db->insert('source_categories', $insert_data);
        }
    }

    public function _prepare_report_fields($stories)
    {
        foreach ($stories as $story) {
            $articleCount = $this->_retrieve_article_count($story);
            $details = $this->_retrieve_story_details($story);
            if ($details['imageArticleId']) {
                $update_data = array(
                    'articleCount' => $articleCount,
                    'titleArticleId' => $details['titleArticleId'],
                    'excerptArticleId' => $details['excerptArticleId'],
                    'imageArticleId' => $details['imageArticleId'],
                );
            } else {
                $update_data = array(
                    'articleCount' => $articleCount,
                    'titleArticleId' => $details['titleArticleId'],
                    'excerptArticleId' => $details['excerptArticleId'],
                );
            }
            $this->db->where('id', $story);
            $this->db->update('stories', $update_data);
        }

        $related_stories = $this->_retrieve_related_stories($stories);
        if (count($related_stories) > 1) {
            $this->_update_story_relations($related_stories);
        } else {
            $cumulative_article_count = $this->_retrieve_cumulated_article_count($related_stories);
            foreach ($related_stories as $related_story) {
                $update_data = array(
                    'totalArticleCount' => $cumulative_article_count,
                );
                $this->db->where('id', $related_story);
                $this->db->update('stories', $update_data);
            }
        }
        /*
    $cumulative_article_count = $this->_retrieve_cumulated_article_count($related_stories);
    $precedingPubDate = null;
    foreach ($related_stories as $related_story) {
    $update_data = array(
    'precedingPubDate' => $precedingPubDate,
    'totalArticleCount' => $cumulative_article_count,
    );
    $this->db->where('id', $related_story);
    $this->db->update('stories', $update_data);
    $precedingPubDate = $this->_get_story_pubdate($related_story);
    }
     */
    }

    public function _retrieve_related_stories($stories)
    {
        /*
        $related_stories = $stories;
        $this->db->select('storyId');
        $this->db->where_in('storyId', $stories);
        $query = $this->db->get('related_stories');
        foreach ($query->result() as $row) {
        if (!in_array($row->storyId, $related_stories)) {
        array_push($related_stories, $row->storyId);
        }
        }
        sort($related_stories);
        return $related_stories;
         */

        $related_stories = $stories;

        $this->db->select('mainStoryId');
        $this->db->from('related_stories');
        $this->db->where_in('storyId', $stories);
        $where_clause = $this->db->get_compiled_select();

        $this->db->select('storyId');
        $this->db->where("`mainStoryId` IN ($where_clause)", null, false);
        $query = $this->db->get('related_stories');

        foreach ($query->result() as $row) {
            if (!in_array($row->storyId, $related_stories)) {
                array_push($related_stories, $row->storyId);
            }
        }
        sort($related_stories);
        return $related_stories;
    }

    public function _update_story_relations($story_ids)
    {
/*
$i = 0;
foreach ($stories as $storyId) {
$this->db->delete('related_stories', array('storyId' => $storyId));
$i++;
if ($i == 1) {
$mainStoryId = $storyId;
}
$insert_data = array(
'mainStoryId' => $mainStoryId,
'storyId' => $storyId,
);
$this->db->insert('related_stories', $insert_data);
$update_data = array(
'mainStoryId' => $mainStoryId,
);
$this->db->where('id', $storyId);
$this->db->update('stories', $update_data);
}
 */
        $this->db->where_in('id', $story_ids);
        $this->db->order_by('pubDate', 'ASC');
        $stories = $this->db->get('stories')->result_array();

        $story_categories = [];
        $articles = [];

        $mainStoryId = $story_ids[0];
        $cumulative_article_count = $this->_retrieve_cumulated_article_count($story_ids);
        $i = 0;
        $precedingPubDate = null;
        foreach ($stories as $story) {
            $this->db->delete('related_stories', array('storyId' => $story_ids[$i]));
            if (empty($story_categories)) {
                $this->db->select('id, storyId');
                $this->db->where_in('storyId', $story_ids);
                $story_categories = $this->db->get('story_categories')->result_array();
            }
            if (empty($articles)) {
                $this->db->select('id, storyId');
                $this->db->where_in('storyId', $story_ids);
                $articles = $this->db->get('articles')->result_array();
            }
            $update_data = array(
                'pubDate' => $story['pubDate'],
                'firstSeen' => $story['firstSeen'],
                'lastSeen' => $story['lastSeen'],
                'precedingPubDate' => $precedingPubDate,
                'articleCount' => $story['articleCount'],
                'totalArticleCount' => $cumulative_article_count,
                'mainStoryId' => $mainStoryId,
                'titleArticleId' => $story['titleArticleId'],
                'excerptArticleId' => $story['excerptArticleId'],
                'imageArticleId' => $story['imageArticleId'],
            );
            $this->db->where('id', $story_ids[$i]);
            $this->db->update('stories', $update_data);
            $story_categories_to_update = [];
            foreach ($story_categories as $story_category) {
                if ($story_category['storyId'] == $story['id']) {
                    array_push($story_categories_to_update, $story_category['id']);
                }
            }
            $update_data = array(
                'storyId' => $story_ids[$i],
            );
            $this->db->where_in('id', $story_categories_to_update);
            $this->db->update('story_categories', $update_data);
            $articles_to_update = [];
            foreach ($articles as $article) {
                if ($article['storyId'] == $story['id']) {
                    array_push($articles_to_update, $article['id']);
                }
            }
            $update_data = array(
                'storyId' => $story_ids[$i],
            );
            $this->db->where_in('id', $articles_to_update);
            $this->db->update('articles', $update_data);

            $insert_data = array(
                'mainStoryId' => $mainStoryId,
                'storyId' => $story_ids[$i],
            );
            $this->db->insert('related_stories', $insert_data);
            $precedingPubDate = $story['pubDate'];
            $i++;
        }
    }

    public function _retrieve_cumulated_article_count($stories)
    {
        $this->db->where_in('storyId', $stories);
        return $this->db->count_all_results('articles');
    }

    public function _retrieve_article_count($storyId)
    {
        $this->db->where('storyId', $storyId);
        return $this->db->count_all_results('articles');
    }

    public function _retrieve_story_details($storyId)
    {
        $details = array(
            'titleArticleId' => 0,
            'excerptArticleId' => 0,
            'imageArticleId' => 0,
        );
        $this->db->select('id, title, excerpt, imageBaseUrlId');
        $this->db->where('storyId', $storyId);
        $this->db->order_by('id', 'DESC');
        $articles = $this->db->get('articles')->result_array();
        foreach ($articles as $article) {
            if ($article['imageBaseUrlId']) {
                $details['imageArticleId'] = $article['id'];
                break;
            }
        }
        $external_excerpt = false;
        if (count($articles) == 1) {
            $details['titleArticleId'] = $article['id'];
        } else {
            usort($articles, function ($a, $b) {
                return strlen($b['excerpt']) - strlen($a['excerpt']);
            });
            foreach ($articles as $article) {
                if (strlen($article['excerpt']) > strlen($article['title'])) {
                    $details['excerptArticleId'] = $article['id'];
                    $external_excerpt = true;
                    break;
                }
            }
            usort($articles, function ($a, $b) {
                return strlen($b['title']) - strlen($a['title']);
            });
            //log_message('debug', 'Title search: ' . 'Analysing story ' . $storyId);
            //log_message('debug', 'Title search: ' . 'Is external excerpt? ' . $external_excerpt);
            foreach ($articles as $article) {
                //log_message('debug', 'Title search: ' . 'Article title ' . strlen($article['title']) . ' ' . $article['title']);
                //log_message('debug', 'Title search: ' . 'excerptArticleId ' . $details['excerptArticleId']);
                if ($details['excerptArticleId'] == 0) {
                    $details['excerptArticleId'] = $article['id'];
                    //log_message('debug', 'Title search: ' . 'Set as excerpt');
                }
                if ($external_excerpt) {
                    $details['titleArticleId'] = $article['id'];
                    //log_message('debug', 'Title search: ' . 'External excerpt. Set as title');
                    break;
                } else if ($article['id'] != $details['excerptArticleId']) {
                    $details['titleArticleId'] = $article['id'];
                    //log_message('debug', 'Title search: ' . 'Title excerpt. Set as title');
                    break;
                }
            }
        }
        $this->_update_article_excerpt($articles, $details['excerptArticleId']);
        return $details;
    }

    public function _update_article_excerpt($articles, $excerptArticleId)
    {
        foreach ($articles as $article) {
            if (strlen($article['excerpt']) > 0 && !$article['imageBaseUrlId'] && $article['id'] != $excerptArticleId) {
                $update_data = array(
                    'excerpt' => '',
                );
                $this->db->where('id', $article['id']);
                $this->db->update('articles', $update_data);
            }
            if (strlen($article['excerpt']) == 0 && !$article['imageBaseUrlId'] && $article['id'] == $excerptArticleId) {
                $update_data = array(
                    'excerpt' => $article['title'],
                );
                $this->db->where('id', $article['id']);
                $this->db->update('articles', $update_data);
            }
        }
    }

    public function _find_article_with_effective_url($url)
    {
        if (substr($url, 0, 4) == 'http') {
            $parsed_url = $this->_parse_url($url);
            $this->db->select('id, storyId');
            $this->db->where('https', $parsed_url['https']);
            $this->db->where('articleBaseUrlId', $parsed_url['base_url_id']);
            $this->db->where('effectiveUrl', $parsed_url['remain']);
            $result = $this->db->get('articles');
            if ($result->num_rows() > 0) {
                return $result->row();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function _update_article($articleId, $excerpt, $urlToImage)
    {
        if (substr($urlToImage, 0, 5) == 'https') {
            $parsed_url = $this->_parse_url($urlToImage);
            $update_data = array(
                'excerpt' => $excerpt,
                'imageBaseUrlId' => $parsed_url['base_url_id'],
                'urlToImage' => $parsed_url['remain'],
            );
            $story_details = array(
                'excerptArticleId' => $articleId,
                'imageArticleId' => $articleId,
            );

        } else {
            $update_data = array(
                'excerpt' => $excerpt,
            );
            $story_details = array(
                'excerptArticleId' => $articleId,
            );
        }
        $this->db->where('id', $articleId);
        $this->db->update('articles', $update_data);
        return $story_details;
    }

    public function _update_story_details($storyId, $story_details)
    {
        $this->db->where('id', $storyId);
        $this->db->update('stories', $story_details);
    }

    public function _update_related_story_images($storyId, $articleId)
    {
        $related_stories = $this->_retrieve_related_stories(array($storyId));

        $this->db->select('id, imageArticleId');
        $this->db->where_in('id', $related_stories);
        $stories = $this->db->get('stories')->result_array();
        foreach ($stories as $story) {
            if (!$story['imageArticleId'] > 0) {
                $update_data = array(
                    'imageArticleId' => $articleId,
                );
                $this->db->where('id', $story['id']);
                $this->db->update('stories', $update_data);
            }
        }
    }

}
