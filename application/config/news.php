<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['google_url_base']   = 'https://news.google.com/news/rss/headlines/section/topic/';
$config['google_url_param']  = '?hl=tr&gl=TR&ceid=TR:tr';
$config['newsapi_url_base']  = 'https://newsapi.org/v2/top-headlines?country=tr&category=';
$config['newsapi_url_param'] = '&pageSize=100&apiKey=';
$config['google_article_url_base']  = 'https://news.google.com/__i/rss/rd/articles/';
$config['google_article_url_param'] = '?oc=5';